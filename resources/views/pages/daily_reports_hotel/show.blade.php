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
  <div class="card card-custom gutter-b">
    <div class="card-header flex-wrap border-0 pt-6 pb-0">
      <div class="card-title">
        <h3 class="card-label">{{ $page_title }}
        <span class="d-block text-muted pt-2 font-size-sm">{{ $page_description }}</span></h3>
      </div>
      <div class="card-toolbar">

        <a href="#">
          <div class="input-group date" id="date_picker" data-target-input="nearest">
            <input type="text" class="form-control datetimepicker-input" data-target="#date_picker" data-toggle="datetimepicker" value="{{ $day }}" />
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
              <th scope="col">{{ __('Check In') }}</th>
              <th scope="col">{{ __('Pause In') }}</th>
              <th scope="col">{{ __('Pause Out') }}</th>
              <th scope="col">{{ __('Check Out') }}</th>
              <th scope="col">{{ __('Depa') }}</th>
              <th scope="col">{{ __('Restant') }}</th>
              <th scope="col">{{ __('Work Time') }}</th>
              <th scope="col">{{ __('with Nightshift') }}</th>
              <th scope="col">{{ __('Break Time') }}</th>
              <th scope="col">{{ __('Potential Time') }}</th>
              <th scope="col">{{ __('Difference') }}</th>
            </tr>
          </thead>
          <tbody>
            {{-- new start ************************************* --}}
            @php
              $counter = 1;
            @endphp
            @foreach ($employees_func as $key => $employees)
              <tr class="table-secondary">
                <th scope="row" colspan="13" >{{ Config::get('constants.functions')[$key] }}</th>
              </tr>
              @foreach ($employees as $index => $employee)
                @foreach ($employee->matrix as $key => $matrixes)
                  @php
                    $total_seconds = $checkin_time = $pausein_time = $pauseout_time = $checkout_time = 0;
                    $pausein_arr = $pauseout_arr = collect();
                    $row_records = collect();
                  @endphp
                  <tr>
                    <td>{{ $counter++ }}</td>
                    @if ($loop->iteration == 1)
                      <td class="text-center" rowspan="{{ $employee->matrix->count() }}"><b data-toggle="tooltip" title="{{ $employee->fullname }}">{{ __('Employee #') }}{{ $employee->id }}</b></td>
                    @endif

                    {{-- Check In --}}
                    <td>
                      @foreach ($matrixes as $key => $matrix)
                        @if ($matrix->action == 0)
                          {{ $matrix->time_formatted }}
                          @php
                            $checkin_time = strtotime($matrix->time);
                          @endphp
                        @endif
                      @endforeach
                    </td>
                    {{-- Pause In --}}
                    <td>
                      @php
                      $filtered = $matrixes->where('action', 2);
                      $pausein_arr = $filtered->pluck('time');
                      @endphp

                      @if ( $filtered->count() > 0 )
                        @if ( $filtered->count() > 1 )
                          <b class="unprint" data-html="true" data-toggle="tooltip" title="{{ implode( "<br>", $filtered->pluck('time_formatted')->all() ) }}">
                            {{ $filtered->count() }}X
                          </b>
                          <span class="print_only">
                            {!! implode( "<br/>", $filtered->pluck('time_formatted')->all() ) !!}
                          </span>
                          @foreach ($filtered as $pin_record)
                            @php $pausein_time += strtotime( $pin_record->time ); @endphp
                          @endforeach
                        @else
                          {{ $filtered->first()->time_formatted }}
                          @php $pausein_time = strtotime( $filtered->first()->time ); @endphp
                        @endif
                      @endif
                    </td>
                    {{-- Pause Out --}}
                    <td>
                      @php
                      $filtered = $matrixes->where('action', 3);
                      $pauseout_arr = $filtered->pluck('time');
                      @endphp

                      @if ( $filtered->count() > 0 )
                        @if ( $filtered->count() > 1 )
                          <b class="unprint" data-html="true" data-toggle="tooltip" title="{{ implode( "<br>", $filtered->pluck('time_formatted')->all() ) }}">
                            {{ $filtered->count() }}X
                          </b>
                          <span class="print_only">
                            {!! implode( "<br/>", $filtered->pluck('time_formatted')->all() ) !!}
                          </span>
                          @foreach ($filtered as $pout_record)
                            @php $pauseout_time += strtotime( $pout_record->time ); @endphp
                          @endforeach
                        @else
                          {{ $filtered->first()->time_formatted }}
                          @php $pauseout_time = strtotime( $filtered->first()->time ); @endphp
                        @endif
                      @endif
                    </td>
                    {{-- Check Out --}}
                    <td>
                      @if ( $matrixes->firstWhere('action', 1) )
                        {{ $matrixes->firstWhere('action', 1)->time_formatted }}
                        @php
                          $checkout_time = strtotime( $matrixes->firstWhere('action', 1)->time );
                        @endphp
                        @if ( $matrixes->firstWhere('action', 0))
                          @if ( !$matrixes->firstWhere('action', 1)->time->isSameDay( $matrixes->firstWhere('action', 0)->time ) )
                          <b data-toggle="tooltip" title="Night Shift"><i class="text-warning flaticon-stopwatch"></i></b>
                          @endif
                        @endif
                      @endif 
                    </td>
                    {{-- Depa --}}
                    <td>
                      @php $depas = 0; @endphp
                      <table class="table">
                        <thead>
                          <tr>
                            <th scope="col">{{ __('Room') }}</th>
                            <th scope="col">{{ __('Category') }}</th>
                            <th scope="col">{{ __('Extra') }}</th>
                            <th scope="col">{{ __('Status') }}</th>
                            <th scope="col">{{ __('Volunteer') }}</th>
                          </tr>
                        </thead>
                        <tbody>
                      @foreach ($matrixes as $key => $matrix)
                        @foreach ($matrix->rooms->where('pivot.clean_type', 0) as $room)
                          <tr>
                            <td>{{ $room->name }}</td>
                            <td>{{ Config::get('constants.room_categories')[$room->category] }}</td>
                            <td>{{ Config::get('constants.calendar_room_extra')[$room->pivot->extra] }}</td>
                            <td>{{ Config::get('constants.room_status')[$room->pivot->status] }}</td>
                            @if ($room->pivot->volunteer)
                            <td><b data-toggle="tooltip" title="{{ $room->pivot->volunteer_name }}">{{ __('Yes') }}</b></td>
                            @else
                            <td>{{ __('No') }}</td>
                            @endif
                          </tr>
                        @endforeach
                        @php
                          $depas += $matrix->rooms->where('pivot.clean_type', 0)->count();
                        @endphp
                      @endforeach
                          <tr>
                            <th>{{ __('Total') }}</th>
                            <td colspan="4" scope="row" class="text-center">{{ $depas }}</td>
                          </tr>
                        </tbody>
                      </table>
                      @php
                        $grand_total_depa += $depas;
                      @endphp
                    </td>
                    {{-- Restant --}}
                    <td>
                      @php $restants = 0; @endphp
                      <table class="table">
                        <thead>
                          <tr>
                            <th scope="col">{{ __('Room') }}</th>
                            <th scope="col">{{ __('Category') }}</th>
                            <th scope="col">{{ __('Extra') }}</th>
                            <th scope="col">{{ __('Status') }}</th>
                            <th scope="col">{{ __('Volunteer') }}</th>
                          </tr>
                        </thead>
                        <tbody>
                      @foreach ($matrixes as $key => $matrix)
                        @foreach ($matrix->rooms->where('pivot.clean_type', 1) as $room)
                          <tr>
                            <td>{{ $room->name }}</td>
                            <td>{{ Config::get('constants.room_categories')[$room->category] }}</td>
                            <td>{{ Config::get('constants.calendar_room_extra')[$room->pivot->extra] }}</td>
                            <td>{{ Config::get('constants.room_status')[$room->pivot->status] }}</td>
                            @if ($room->pivot->volunteer)
                            <td><b data-toggle="tooltip" title="{{ $room->pivot->volunteer_name }}">{{ __('Yes') }}</b></td>
                            @else
                            <td>{{ __('No') }}</td>
                            @endif
                          </tr>
                        @endforeach
                        @php
                          $restants += $matrix->rooms->where('pivot.clean_type', 1)->count();
                        @endphp
                      @endforeach
                          <tr>
                            <th>{{ __('Total') }}</th>
                            <td colspan="4" scope="row" class="text-center">{{ $restants }}</td>
                          </tr>
                        </tbody>
                      </table>
                      @php
                        $grand_total_restant += $restants;
                      @endphp
                    </td>
                    {{-- Work Time --}}
                    <td>
                      @if ($checkin_time && $checkout_time)
                        @if ($pausein_time && $pauseout_time)
                          @php $total_seconds = $checkout_time - $checkin_time - ($pauseout_time - $pausein_time); @endphp
                        @else
                          @php $total_seconds = $checkout_time - $checkin_time; @endphp
                        @endif
                        {{
                          \Carbon\CarbonInterval::
                            seconds($total_seconds)
                            ->cascade()
                            ->forHumans(['short' => true, 'options' => 0])
                        }}

                        @php
                          $has_night_shift = $record_helper->hasNightShift($checkin_time, $checkout_time);
                        @endphp
                        @if ( $has_night_shift )
                        @php
                          $seconds_with_nightshift = $record_helper->nightShiftHours($checkin_time, $pausein_arr, $pauseout_arr, $checkout_time);
                        @endphp
                        <b data-html="true" data-toggle="tooltip" title="Added <b>{{ \Carbon\CarbonInterval::seconds( $seconds_with_nightshift )->cascade()->forHumans(['short' => true, 'options' => 0]) }}</b> <br>{{ __('Night Shift') }}">
                          <i class="text-warning flaticon-stopwatch"></i>
                        </b>
                        @endif
                      @else
                        -
                      @endif
                    </td>
                    {{-- with Nightshift --}}
                    <td>
                      @php
                          $total_seconds_with_nightshift = $record_helper->nightShiftHours($checkin_time, $pausein_arr, $pauseout_arr, $checkout_time, $total_seconds);
                          $grand_total_time += $total_seconds_with_nightshift;
                      @endphp
                      {{ \Carbon\CarbonInterval::seconds( $total_seconds_with_nightshift )->cascade()->forHumans(['short' => true, 'options' => 0]) }}
                    </td>
                    {{-- Break Time --}}
                    <td>
                      @if ($pausein_time && $pauseout_time)
                        {{
                          \Carbon\CarbonInterval::
                            seconds($pauseout_time - $pausein_time)
                            ->cascade()
                            ->forHumans(['short' => true, 'options' => 0])
                        }}
                      @else
                        -
                      @endif
                    </td>
                    {{-- Potential Time --}}
                    <td>
                      @php
                          $pot_minutes = 0;
                      @endphp
                      @foreach ($matrixes as $key => $matrix)
                        @foreach ($matrix->rooms as $room)
                        @php
                            $pot_minutes +=  $room->pivot->clean_type == 0 ? $room->depa_minutes : $room->restant_minutes;
                        @endphp
                        @endforeach    
                      @endforeach

                      {{
                        \Carbon\CarbonInterval::minutes( $pot_minutes )
                        ->cascade()
                        ->forHumans(['short' => true, 'options' => 0])
                      }}
                    </td>
                    {{-- Difference --}}
                    <td>
                      @if ($checkin_time && $checkout_time)
                        @php
                          $work_time = $total_seconds;
                          $difference = $work_time - ($pot_minutes*60);
                        @endphp
                        @if ($difference > 0)
                        {{ __('Slower for') }}
                        @else
                        {{ __('Faster for') }}
                        @endif
                        <b class="print_only">{{ \Carbon\CarbonInterval::seconds($difference)->cascade()->forHumans(['short' => true, 'options' => 0]) }}</b>
                        <span class="label font-weight-bold label-lg label-light-{{ $difference > 0 ? 'danger' : 'success' }} label-inline unprint">
                          {{ \Carbon\CarbonInterval::seconds($difference)->cascade()->forHumans(['short' => true, 'options' => 0]) }}
                        </span>
                      @else
                        -
                      @endif
                    </td>
                  </tr>
                @endforeach
              @endforeach
            @endforeach
            {{-- new end ************************************* --}}
          </tbody>
        </table>


      </div>

      <div class="table-responsive">

        <table class="table table-sm ">
					<thead class="thead-light">
						<tr>
							<th colspan="9" scope="col"></th>
							<th Width="100px" scope="col">{{ __('Total Depa') }}</th>
							<th Width="100px" scope="col">{{ __('Total Restant') }}</th>
							<th Width="100px" scope="col">{{ __('Total Time') }}</th>
              <th Width="200px" scope="col">{{ __('Total Time in Decimal') }}</th>
						</tr>
					</thead>
					<tbody>
						<tr>
              <th colspan="9" scope="row"></th>
							<td>{{ $grand_total_depa }}</td>
							<td>{{ $grand_total_restant }}</td>
							<td>
                  {{
                    \Carbon\CarbonInterval::
                      seconds($grand_total_time)
                      ->cascade()
                      ->forHumans(['short' => true, 'options' => 0])
                  }}
							</td>
              <td>{{ number_format( (float)($grand_total_time / 3600), 2, '.', '' ) }} ha</td>
						</tr>
					</tbody>
				</table>


      </div>
    </div>
  </div>
  <!--end::Card-->

  {{-- <footer class="print_only"> --}}
  {{-- <footer class="absolute bottom-0"> --}}
  <footer class=" pb-20 px-30 print_only">
    <div class="d-flex justify-content-between flex-column flex-sm-row text-center text-sm-left mt-30">
      @foreach ($managers as $key => $manager)
        <div class="font-weight-bolder text-center">
          {{ $manager->fullname }} <br><br> _________________________
        </div>
      @endforeach
  	</div>
  </footer>
  <!--begin::Report footer-->

  <!--end::Report footer-->

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
    var daily_employees = {!! $daily_employees !!};
    console.log(daily_employees);
  </script>
    <!--begin::Global Config(global config for global JS scripts)-->
    <script>var KTAppSettings = { "breakpoints": { "sm": 576, "md": 768, "lg": 992, "xl": 1200, "xxl": 1400 }, "colors": { "theme": { "base": { "white": "#ffffff", "primary": "#3699FF", "secondary": "#E5EAEE", "success": "#1BC5BD", "info": "#8950FC", "warning": "#FFA800", "danger": "#F64E60", "light": "#E4E6EF", "dark": "#181C32" }, "light": { "white": "#ffffff", "primary": "#E1F0FF", "secondary": "#EBEDF3", "success": "#C9F7F5", "info": "#EEE5FF", "warning": "#FFF4DE", "danger": "#FFE2E5", "light": "#F3F6F9", "dark": "#D6D6E0" }, "inverse": { "white": "#ffffff", "primary": "#ffffff", "secondary": "#3F4254", "success": "#ffffff", "info": "#ffffff", "warning": "#ffffff", "danger": "#ffffff", "light": "#464E5F", "dark": "#ffffff" } }, "gray": { "gray-100": "#F3F6F9", "gray-200": "#EBEDF3", "gray-300": "#E4E6EF", "gray-400": "#D1D3E0", "gray-500": "#B5B5C3", "gray-600": "#7E8299", "gray-700": "#5E6278", "gray-800": "#3F4254", "gray-900": "#181C32" } }, "font-family": "Poppins" };</script>
    <!--end::Global Config-->

    {{-- vendors --}}

    {{-- page scripts --}}
    <script src="{{ mix('js/pages/daily_reports_hotel/show.js') }}" type="text/javascript"></script>
    <script src="{{ mix('js/app.js') }}" type="text/javascript"></script>
@endsection
