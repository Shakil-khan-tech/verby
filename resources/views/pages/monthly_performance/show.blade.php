@inject('record_helper', 'App\Classes\Helpers\Record')

{{-- {{ Metronic::addClass('body', 'print-content-only') }} --}}
{{ Metronic::addClass('body', 'printable') }}

{{-- Extends layout --}}
@extends('layout.default')

{{-- Content --}}
@section('content')

  @if( session()->has('error') )
    <div class="alert alert-custom alert-outline-danger fade show mb-5 py-0" role="alert">
        <div class="alert-icon"><i class="flaticon-warning"></i></div>
        <div class="alert-text">
          {{ session()->get('error') }}
          @foreach (session()->get('message') as $key => $message)
            <p class="mb-0">{{ $key }}: {{ $message }}</p>
          @endforeach

        </div>
        <div class="alert-close">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true"><i class="ki ki-close"></i></span>
            </button>
        </div>
    </div>
  @endif
  @if(session()->has('success'))
    <div class="alert alert-custom alert-outline-success fade show mb-5 py-0" role="alert">
        <div class="alert-icon"><i class="flaticon-bell"></i></div>
        <div class="alert-text">{{ session()->get('success') }}</div>
        <div class="alert-close">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true"><i class="ki ki-close"></i></span>
            </button>
        </div>
    </div>
  @endif

  <!--begin::Card-->
  <div class="card card-custom gutter-b">
    <div class="card-header flex-wrap border-0 pt-6 pb-0">
      <div class="card-title">
        <h3 class="card-label">{{ $page_title }}
        <span class="d-block text-muted pt-2 font-size-sm">{{ $page_description }}</span></h3>
      </div>
      <div class="card-toolbar">

        <a href="#">
          <div class="input-group date" id="date_picker" data-target-input="nearest">
            <input type="text" class="form-control datetimepicker-input" data-target="#date_picker" data-toggle="datetimepicker" value="{{ $month }}" />
            <div class="input-group-append" data-target="#date_picker" data-toggle="datetimepicker">
              <span class="input-group-text">
                <i class="ki ki-calendar"></i>
              </span>
            </div>
          </div>
        </a>
        <a href="#!" class="btn btn-success font-weight-bold ml-2" onclick="window.print();">
					<i class="flaticon2-cube"></i>{{ __('Print') }}
        </a>

			</div>
    </div>
    <div class="card-body">

      <div class="table-responsive">

        @php
          $grand_total_depa = $grand_total_restant = $grand_total_time = 0;
        @endphp
        <table id="tableCalendar" class="table table-vertical-center table-hover table-foot-custom table-xs table-bordered mb-6">
          <thead class="thead-dark">
            <tr class="">
              <th scope="col">#</th>
              <th scope="col">{{ __('Employee') }}</th>
              <th scope="col">{{ __('Depa') }}</th>
              <th scope="col">{{ __('Restant') }}</th>
              <th scope="col">{{ __('Work Time') }}</th>
              <th scope="col">{{ __('Potential Time') }}</th>
              <th scope="col">{{ __('Difference') }}</th>
              <th scope="col">{{ __('Percentage') }}</th>
            </tr>
          </thead>
          <tbody>
              @php
              $functions = config('constants.functions');
              $grouped = collect($matrix)->groupBy('function');
              $totalDifference = 0;
              $overAlltotalPotentialTime = 0;
            @endphp

            @foreach ($grouped as $functionId => $employees)
              {{-- Function Group Header --}}
              <tr>
                <td colspan="7" class="bg-light font-weight-bold text-dark">
                  {{ $functions[$functionId] ?? 'Unknown Function' }}
                </td>
              </tr>

            @foreach ($employees as $key => $emp)
              @php
                  $totalTime = ($emp['function'] == 0)
                      ? round((($emp['depas'] + $emp['restants']) * 3) / 60, 2)
                      : round((($emp['depas'] * 20 + $emp['restants'] * 10) / 60), 2);
                  $overAlltotalPotentialTime+=$totalTime;    
              @endphp
              <tr>
                <td>{{ $key + 1 }}</td>
                <td class="text-center"><b>{{ $emp['fullname'] }}</b></td>
                <td>{{ $emp['depas'] }}</td>
                <td>{{ $emp['restants'] }}</td>
                <td>{{ number_format((float)($emp['work_seconds'] / 3600), 2, '.', '') }} ha</td>
                {{-- <td>{{ number_format((float)($emp['potential_seconds'] / 3600), 2, '.', '') }} ha</td> --}}
                <td>
                  {{ $totalTime }} ha
                </td>
                <td>
                  @php

                    $workSeconds = number_format((float)($emp['work_seconds'] / 3600), 2, '.', '');
                    $difference = $totalTime - $workSeconds;
                    $totalDifference += $difference;
                  @endphp

                      {{ $difference > 0 ? __('Faster for') : __('Slower for') }}
                      <b class="print_only">{{ number_format((float)abs($difference), 2, '.', '') }} ha</b>
                      <span class="label font-weight-bold label-lg label-light-{{ $difference > 0 ? 'success' : 'danger' }} label-inline unprint">
                        {{ number_format((float)abs($difference), 2, '.', '') }} ha
                      </span>
                    </td>
                    <td>
                      @php 
                        $percentage = ($workSeconds > 0) 
                            ? number_format(($totalTime / $workSeconds * 100), 2, '.', '') 
                            : '0.00';
                      @endphp
                      {{$percentage}} %
                    </td>
                  </tr>
                @endforeach
              @endforeach

          </tbody>
        </table>
      </div>
      <div class="table-responsive">
        <table class="table table-sm">
          <thead class="thead-light">
            <tr>
              <th colspan="9" scope="col"></th>
              <th Width="100px" scope="col">{{ __('Total Depa') }}</th>
              <th Width="100px" scope="col">{{ __('Total Restant') }}</th>
              <th Width="100px" scope="col">{{ __('Total Work') }}</th>
              <th Width="100px" scope="col">{{ __('Total Potential') }}</th>
              <th Width="100px" scope="col">{{ __('Total Difference') }}</th>
              <th Width="100px" scope="col">{{ __('Total Percentage') }}</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <!-- Insert your left-side label text here -->
              <th colspan="9" scope="row" class="text-left align-middle">
                <small>
                  <strong>Depa</strong> = 20min, 
                  <strong>Restant</strong> = 10min, 
                  <strong>Gouvernante</strong> = 3min/room
                </small>
              </th>

              <td>{{ $matrix->sum('depas') }}</td>
              <td>{{ $matrix->sum('restants') }}</td>
              <td>{{ number_format((float)($matrix->sum('work_seconds') / 3600), 2, '.', '') }} ha</td>
              <td>{{ number_format((float)($overAlltotalPotentialTime), 2, '.', '') }} ha</td>

              @php
                $total_difference = $totalDifference;
              @endphp
              <td>
                <span class="label font-weight-bold label-lg label-light-{{ $total_difference > 0 ? 'success' : 'danger' }} label-inline">
                  {{ number_format((float)abs($total_difference), 2, '.', '') }} ha
                </span>
              </td>
              <td>
                 @php 
                        $totalWorkSeconds=number_format((float)($matrix->sum('work_seconds') / 3600), 2, '.', ''); 
                        $totalPercentage = ($totalWorkSeconds > 0) 
                            ? number_format(($overAlltotalPotentialTime / $totalWorkSeconds * 100), 2, '.', '') 
                            : '0.00';
                      @endphp
                      {{$totalPercentage}} %
              </td>
            </tr>
          </tbody>
        </table>



      </div>
    </div>
  </div>
  <!--end::Card-->

@endsection

{{-- Styles Section --}}
@section('styles')
<style media="screen">
@media print { @page {size: auto !important} }
</style>
@endsection


{{-- Scripts Section --}}
@section('scripts')
    <script type="text/javascript">
      var deviceId = {!! $device->id !!};
    </script>
    <!--begin::Global Config(global config for global JS scripts)-->
    <script>var KTAppSettings = { "breakpoints": { "sm": 576, "md": 768, "lg": 992, "xl": 1200, "xxl": 1400 }, "colors": { "theme": { "base": { "white": "#ffffff", "primary": "#3699FF", "secondary": "#E5EAEE", "success": "#1BC5BD", "info": "#8950FC", "warning": "#FFA800", "danger": "#F64E60", "light": "#E4E6EF", "dark": "#181C32" }, "light": { "white": "#ffffff", "primary": "#E1F0FF", "secondary": "#EBEDF3", "success": "#C9F7F5", "info": "#EEE5FF", "warning": "#FFF4DE", "danger": "#FFE2E5", "light": "#F3F6F9", "dark": "#D6D6E0" }, "inverse": { "white": "#ffffff", "primary": "#ffffff", "secondary": "#3F4254", "success": "#ffffff", "info": "#ffffff", "warning": "#ffffff", "danger": "#ffffff", "light": "#464E5F", "dark": "#ffffff" } }, "gray": { "gray-100": "#F3F6F9", "gray-200": "#EBEDF3", "gray-300": "#E4E6EF", "gray-400": "#D1D3E0", "gray-500": "#B5B5C3", "gray-600": "#7E8299", "gray-700": "#5E6278", "gray-800": "#3F4254", "gray-900": "#181C32" } }, "font-family": "Poppins" };</script>
    <!--end::Global Config-->

    {{-- vendors --}}

    {{-- page scripts --}}
    <script src="{{ mix('js/pages/monthly_performance/show.js') }}" type="text/javascript"></script>
    <script src="{{ mix('js/app.js') }}" type="text/javascript"></script>
@endsection
