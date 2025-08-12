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
            <button type="button" class="close" data-dismiss="alert" aria-label="{{ __('Close') }}">
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
            <button type="button" class="close" data-dismiss="alert" aria-label="{{ __('Close') }}">
                <span aria-hidden="true"><i class="ki ki-close"></i></span>
            </button>
        </div>
    </div>
  @endif

  <!--begin::Card-->
  <div id="cardPrint" class="card card-custom gutter-b">
    <div class="card-header flex-wrap border-0 pt-6 pb-0">
      <div class="card-title">
        <h3 class="card-label">
          <span class="d-block">{{ $page_title }}</span>
          <span class="text-muted pt-2 font-size-sm">{{ $page_description }}</span>
        </h3>
      </div>
      <div class="card-toolbar">
        {{-- <a href="#">
            <div class="input-group">
              <div class="input-group-append">
                <span class="input-group-text">
                  <i class="la la-calendar-check-o"></i>
                </span>
              </div>
              <input id="date_picker" class="form-control form-control-lg form-control-solid" type="month" name="date" value="{{ $month }}">
              <input id="dateReport" class="form-control form-control-lg form-control-solid" type="month" name="date" value="{{ $month }}">
            </div>
        </a> --}}
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
        <a href="#!" class="btn btn-info font-weight-bold ml-2" onclick="window.print();">
					<i class="flaticon2-cube"></i>{{ __('Print') }}
        </a>
        <a href="#!" id="reportFormSubmitAjax" class="btn btn-success font-weight-bold ml-2">
          <i class="flaticon2-cube"></i>{{ __('Save') }}
      </a>
			</div>
    </div>
    <div class="card-body">

      <div class="table-responsive">
        <form id="MonthlyReportForm" action="{{ route('monthly_reports.update', $device->id) }}" method="post">
        
          <table id="tableMonthlyReport" class="table table-striped table-hover table-xs table-bordered">
            <thead class="thead-dark">
              <tr class="text-center">
                <th class="bg-white text-dark p-1"></th>
                <th colspan="10" class="bg-white text-dark p-1">{{ __('Rooms') }}</th>
                <th colspan="7" class="bg-white text-dark p-1">{{ __('Hours') }}</th>
              </tr>
              <tr class="text-center">
                <th scope="col" class=""><span>{{ __('Date') }}</span></th>
                @foreach (config('constants.room_categories') as $category)
                <th scope="col"><span>{{ $category }}</span></th>
                @endforeach
                <th scope="col"><span>{{ __('Sum') }}</span></th>
                <th scope="col"><span>{{ __('Red Card') }}</span></th>
                <th scope="col" class="bg-gray-300 text-dark"><span>{{ __('Total') }}</span></th>

                <th scope="col" class="w-100px"><span>{{ Config::get('constants.performs')[0] }}:</span></th>
                <th scope="col"><span>{{ __('Hours') }}</span></th>
                <th scope="col" class="w-100px"><span>{{ Config::get('constants.performs')[1] }}:</span></th>
                <th scope="col"><span>{{ __('Hours') }}</span></th>
                <th scope="col" class="w-100px"><span>{{ Config::get('constants.performs')[2] }}:</span></th>
                <th scope="col"><span>{{ __('Hours') }}</span></th>
                
                <th scope="col" class="w-150px"><span>{{ __('Directing hours') }}</span></th>
              </tr>
            </thead>
            <tbody>
              @php
                $rooms_total = $reg_total = $rote_total = 0;
              @endphp
              @foreach ($matrix['days'] as $day => $records)
                @if ( count($records) == 0 )
                    <tr class="text-center">
                      <td><b><span>{{ $day }}</span></b></td>
                      @foreach (config('constants.room_categories') as $category)
                        <td><span></span></td>
                      @endforeach
                      <td><span></span></td>
                      <td><span></span></td>
                      <td><span></span></td>
                      <td><span></span></td>
                      <td><span></span></td>
                      <td><span></span></td>
                      <td><span></span></td>
                      <td><span></span></td>
                      <td><span></span></td>
                      <td><span></span></td>
                    </tr>
                @endif
                <tr class="text-center">
                  @php
                      $rooms_total += $records['total'];
                      $reg_total += $records['reg'];
                      $rote_total += $records['rote'];
                  @endphp
                  <td><span>{{ $day }}</span></td>
                  @foreach ($records['rooms'] as $key => $room)
                    <td><span>{{ $room['sum'] }}</span></td>
                  @endforeach
                  <td><span>{{ $records['total'] }}</span></td>
                  <td><span>{{ $records['rote'] }}</span></td>
                  <td class="bg-gray-300 text-dark"><span>{{ $records['total'] - $records['rote'] }}</span></td>

                  <td><span>{{ $records['performs'][0]['employees'] }}</span></td>
                  <td><span>{{ $records['performs'][0]['hours'] }}</span></td>
                  <td><span>{{ $records['performs'][1]['employees'] }}</span></td>
                  <td><span>{{ $records['performs'][1]['hours'] }}</span></td>
                  <td><span>{{ $records['performs'][2]['employees'] }}</span></td>
                  <td><span>{{ $records['performs'][2]['hours'] }}</span></td>                  
                  <td><span><input class="form-control form-control-sm h-6 form-control-solid text-center" type="number" name="report[{{ $day }}][reg]" value="{{ $records['reg'] }}"/></span></td>
                </tr>
              @endforeach
              {{-- show thead again on the bottom --}}
              <tr class="text-center">
                <th scope="col" class=""><span>{{ __('Date') }}</span></th>
                @foreach (config('constants.room_categories') as $category)
                <th scope="col"><span>{{ $category }}</span></th>
                @endforeach
                <th scope="col"><span>{{ __('Sum') }}</span></th>
                <th scope="col"><span>{{ __('Red Card') }}</span></th>
                <th scope="col"><span>{{ __('Total') }}</span></th>

                <th scope="col" class="w-100px"><span>{{ Config::get('constants.performs')[0] }}:</span></th>
                <th scope="col"><span>{{ __('Hours') }}</span></th>
                <th scope="col" class="w-100px"><span>{{ Config::get('constants.performs')[1] }}:</span></th>
                <th scope="col"><span>{{ __('Hours') }}</span></th>
                <th scope="col" class="w-100px"><span>{{ Config::get('constants.performs')[2] }}:</span></th>
                <th scope="col"><span>{{ __('Hours') }}</span></th>
                <th scope="col" class="w-150px"><span>{{ __('Directing hours') }}</span></th>
              </tr>
            </tbody>
            <tfoot class="table-dark">
              <tr class="text-center">
                <td scope="col"><span>{{ __('Total') }}</span></td>
                @foreach ($matrix['grandtotal'] as $room)
                <td><span>{{ $room }}</span></td>
                @endforeach
                <td><span>{{ $rooms_total }}</span></td>
                <td><span>{{ $rote_total }}</span></td>
                <td><span>{{ $rooms_total - $rote_total }}</span></td>

                <td><span>{{ $matrix['grandtotal_performs'][ Config::get('constants.performs')[0] ] }}</span></td> {{-- Stewarding --}}
                <td><span>{{ $matrix['grandtotal_performs_hours'][ Config::get('constants.performs')[0] ] }}</span></td> {{-- Stewarding --}}
                <td><span>{{ $matrix['grandtotal_performs'][ Config::get('constants.performs')[1] ] }}</span></td> {{-- Unterhalt --}}
                <td><span>{{ $matrix['grandtotal_performs_hours'][ Config::get('constants.performs')[1] ] }}</span></td> {{-- Unterhalt --}}
                <td><span>{{ $matrix['grandtotal_performs'][ Config::get('constants.performs')[2] ] }}</span></td> {{-- Gouvernante --}}
                <td><span>{{ $matrix['grandtotal_performs_hours'][ Config::get('constants.performs')[2] ] }}</span></td> {{-- Gouvernante --}}
                <td><span>{{ $reg_total }}</span></td>
              </tr>
            </tfoot>
          </table>

        </form>
      </div>
    </div>
  </div>
  <!--end::Card-->

  {{-- <footer class="print_only"> --}}
  {{-- <footer class="absolute bottom-0"> --}}
  {{-- <footer class="fixed-bottom pb-20 px-30 print_only">
    <div class="d-flex justify-content-between flex-column flex-sm-row text-center text-sm-left mt-30">
      @foreach ($managers as $key => $manager)
        <div class="font-weight-bolder text-center">
          {{ $manager->fullname }} <br><br> _________________________
        </div>
      @endforeach
  	</div>
  </footer> --}}
  <!--begin::Report footer-->

  <!--end::Report footer-->

@endsection

{{-- Styles Section --}}
@section('styles')
<style media="print">
/* @media print { */
  body {
    -webkit-print-color-adjust: exact !important;
  }
  @page {
    size: auto !important;
  }
  .table td, .table th {
    background-color: unset !important;
  }
  .table-striped tbody tr:nth-of-type(odd) {
      background-color: #ebedf3 !important;
  }
  .table-dark {
      color: #000 !important;
      background-color: #bfc4d1 !important;
  }
  .table-dark td, .table-dark th, .table-dark thead th {
      border-color: #bfc4d1 !important;
  }
/* } */
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
    <script src="{{ mix('js/pages/monthly_reports/show.js') }}" type="text/javascript"></script>
    <script src="{{ mix('js/app.js') }}" type="text/javascript"></script>
@endsection
