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
          @if ($employee_response === 0)
              <span class="label label-lg label-light-danger label-inline">{{ __('Not accepted') }}</span>
          @elseif ($employee_response === 1)
              <span class="label label-lg label-light-success label-inline">{{ __('Accepted') }}</span>
          @endif
        </h3>
      </div>
      <div class="card-toolbar">
        <a href="#">
            <div class="input-group">
              <div class="input-group-append">
                <span class="input-group-text">
                  <i class="la la-calendar-check-o"></i>
                </span>
              </div>
              <input id="date_picker" class="form-control form-control-lg form-control-solid" type="month" name="date" value="{{ $month }}">
            </div>
        </a>
        <a href="#!" class="btn btn-success font-weight-bold ml-2" onclick="window.print();">
					<i class="flaticon2-print"></i>{{ __('Print') }}
        </a>
        <div class="form-group row -mt-3 mb-0 mx-0">
          <div class="col-12 m-0">
            <label class="col-form-label p-0">{{ __('Hide columns') }}</label>
            <span class="switch switch-outline switch-icon switch-primary justify-center">
              <label>
                <input id="hide_columns" type="checkbox" name="hide_columns">
                <span></span>
              </label>
            </span>
          </div>
        </div>
        <a href="{{ route('records.calendar_email', $employee->id) }}" id="sendEmail" class="btn btn-primary font-weight-bold ml-2" data-month="{{ $month }}">
					<i class="flaticon2-mail-1"></i>{{ __('Email') }}
        </a>

			</div>
    </div>
    <div class="card-body">

      <div class="table-responsive">

        @php
          $grand_total_depa = $grand_total_restant = $grand_total_time = $grand_total_nightshift =  0;
        @endphp
        <table id="tableCalendar" class="table table-vertical-center table-hover table-foot-custom table-xs table-bordered mb-6">
          <thead class="thead-dark">
            <tr class="">
              <th scope="col" class="text-center">{{ __('Date') }}</th>
              <th scope="col">{{ __('Check In') }}</th>
              <th scope="col">{{ __('Pause In') }}</th>
              <th scope="col">{{ __('Pause Out') }}</th>
              <th scope="col">{{ __('Check Out') }}</th>
              <th scope="col" class="to_hide">{{ __('Depa') }}</th>
              <th scope="col" class="to_hide">{{ __('Restant') }}</th>
              <th scope="col">{{ __('Work Time') }}</th>
              <th scope="col">{{ __('Night Shift 10%') }}</th>
              <th scope="col">{{ __('Total') }}</th>
              <th scope="col">{{ __('Break Time') }}</th>
              <th scope="col" class="to_hide">{{ __('Potential Time') }}</th>
              <th scope="col" class="to_hide">{{ __('Difference') }}</th>
              @if ($employee->PartTime == 0)
              {{-- <th scope="col">Daily Total</th> --}}
              @endif
            </tr>
          </thead>
          <tbody>
            @foreach ($matrix as $matrix_key => $day)
              @if ($matrix_key == "24.01.2024" || $matrix_key == "25.01.2024" )
                  
              @endif
              @if ( $day->count() == 0 )
                  <tr>
                    <td class="text-center"><b>{{ $matrix_key }}</b></td>
                    <td colspan="4" class="text-center">
                      @php
                          $symbol = $plans->where('dita_formatted', $matrix_key)->first()?->symbol ?? null;
                      @endphp
                      @if ( isset( Config::get('constants.plans')[ $symbol ] ) )
                      <b>{{ __(Config::get('constants.plans')[ $symbol ]['text']) }}</b>
                      @else
                      <b>{{ $symbol }} ({{ __('No Show') }})</b>
                      @endif
                    </td>
                    {{-- <td></td>
                    <td></td>
                    <td></td> --}}
                    <td class="to_hide"></td>
                    <td class="to_hide"></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td class="to_hide"></td>
                    <td class="to_hide"></td>
                    @if ($employee->PartTime == 0)
                    {{-- <td></td> --}}
                    @endif
                  </tr>
              @endif
              @php
                $total_seconds = $checkin_time = $pausein_time = $pauseout_time = $checkout_time = 0;
                $pausein_arr = $pauseout_arr = collect();
                // $row_records = collect();
              @endphp
              @foreach ($day as $key => $records)
                @php
                  $total_seconds = $checkin_time = $pausein_time = $pauseout_time = $checkout_time = 0;
                  $pausein_arr = $pauseout_arr = collect();
                  $row_records = collect();
                  $seconds_with_nightshift = 0;
                @endphp
                {{-- @foreach ($records as $record_key => $record) --}}
                  @php
                    $device = $records[0]->device;
                  @endphp
                  <tr>
                    {{-- Date	 --}}
                    @if ($key == 0)
                      <td class="text-center" rowspan="{{ $day->count() != 0 ? $day->count() : 1 }}"><b>{{ $matrix_key }}</b></td>
                    @endif
                    {{-- Check In	 --}}
                    <td>
                      @if ( $records->firstWhere('action', 0) )
                        {{ $records->firstWhere('action', 0)->time_formatted }}
                        @php
                          $checkin_time = strtotime( $records->firstWhere('action', 0)->time );
                        @endphp
                      @endif
                    </td>
                    {{-- Pause In	 --}}
                    <td>
                      @php
                      $filtered = $records->where('action', 2);
                      $pausein_arr = $filtered->pluck('time');
                      @endphp
                      
                      @if ( $filtered->count() > 0 )
                        @if ( $filtered->count() > 1 )
                          <b class="unprint" data-html="true" data-toggle="tooltip" title="{{ implode( "<br>", $filtered->pluck('timeFormatted')->all() ) }}">
                            {{ $filtered->count() }}X
                          </b>
                          <span class="print_only">
                            {!! implode( "<br/>", $filtered->pluck('timeFormatted')->all() ) !!}
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
                    {{-- Pause Out	 --}}
                    <td>
                      @php
                      $filtered = $records->where('action', 3);
                      $pauseout_arr = $filtered->pluck('time');
                      @endphp
                      
                      @if ( $filtered->count() > 0 )
                        @if ( $filtered->count() > 1 )
                          <b class="unprint" data-html="true" data-toggle="tooltip" title="{{ implode( "<br>", $filtered->pluck('timeFormatted')->all() ) }}">
                            {{ $filtered->count() }}X
                          </b>
                          <span class="print_only">
                            {!! implode( "<br/>", $filtered->pluck('timeFormatted')->all() ) !!}
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
                    {{-- Check Out	 --}}
                    <td>
                      @if ( $records->firstWhere('action', 1) )
                        {{ $records->firstWhere('action', 1)->time_formatted }}
                        @php
                          $checkout_time = strtotime( $records->firstWhere('action', 1)->time );
                        @endphp
                        @if ( !$records->firstWhere('action', 1)->time->isSameDay( $records->firstWhere('action', 0)->time ) )
                        <b data-toggle="tooltip" title="Night Shift"><i class="text-warning flaticon-stopwatch"></i></b>
                        @endif
                      @endif
                    </td>
                    {{-- Depa --}}
                    <td class="to_hide">
                      @php
                        $depas = 0;
                        // $depas = $records->where('rooms.pivot.clean_type', 0);
                      @endphp
                      {{-- {{ $records->where('rooms.pivot.clean_type', 0) }} --}}
                      @foreach ($records->where('rooms.pivot.clean_type', 0) as $record)
                        @php
                          $depas += $record->rooms->where('pivot.clean_type', 0)->count();
                          $row_records->push( $record->id );
                        @endphp
                      @endforeach
                      <a href="#" class="showRooms btn btn-text-dark-50 btn-icon-primary btn-hover-icon-warning font-weight-bold btn-hover-bg-light inline-flex"
                        data-clean_type="0"
                        data-records="{{ $row_records }}"
                      >
                        {{ Metronic::getSVG("media/svg/icons/Design/Flatten.svg", "svg-icon unprint") }}
                        {{ $depas }}
                        @php
                          $grand_total_depa += $depas;
                        @endphp
                      </a>
                      </a>
                    </td>
                    {{-- Restant --}}
                    <td class="to_hide">
                      @php
                        $restants = 0;
                        // $depas = $records->where('rooms.pivot.clean_type', 0);
                      @endphp
                      @foreach ($records as $record)
                        @foreach ($record->rooms as $room)
                          @if ($room->pivot->clean_type == 1)
                            @php
                              $restants += 1;
                            @endphp
                          @endif
                        @endforeach
                      @endforeach
                      <a href="#" class="showRooms btn btn-text-dark-50 btn-icon-success btn-hover-icon-warning font-weight-bold btn-hover-bg-light inline-flex"
                        data-clean_type="1"
                        data-records="{{ $row_records }}"
                      >
                        {{ Metronic::getSVG("media/svg/icons/Design/Flatten.svg", "svg-icon unprint") }}
                        {{ $restants }}
                        @php
                          $grand_total_restant += $restants;
                        @endphp
                      </a>
                      </a>
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
                          // sprintf('%02d:%02d:%02d', ($total_seconds/3600),($total_seconds/60%60), $total_seconds%60)
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
                            $grand_total_nightshift += $seconds_with_nightshift;
                        @endphp
                        <b data-html="true" data-toggle="tooltip" title="{{ __('Added') }} <b>{{ \Carbon\CarbonInterval::seconds( $seconds_with_nightshift )->cascade()->forHumans(['short' => true, 'options' => 0]) }}</b> <br>{{ __('Night Shift') }}">
                          <i class="text-warning flaticon-stopwatch"></i>
                        </b>
                        @endif
                      @else
                        -
                      @endif
                    </td>
                    {{-- Night Shift 10% --}}
                    <td>
                      @if (isset($seconds_with_nightshift))
                      {{ \Carbon\CarbonInterval::seconds( $seconds_with_nightshift )->cascade()->forHumans(['short' => true, 'options' => 0]) }}
                      @else
                        -
                      @endif
                    </td>
                    {{-- Total --}}
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
                    {{-- Potential Time	 --}}
                    <td class="to_hide">
                      @php
                          $pot_minutes = 0;
                      @endphp
                      @foreach ($records as $record)
                        @foreach ($record->rooms->where('pivot.clean_type', 0) as $room_depa)
                            @php
                                $pot_minutes += $room->depa_minutes;
                            @endphp
                        @endforeach
                        @foreach ($record->rooms->where('pivot.clean_type', 1) as $room_restant)
                            @php
                                $pot_minutes += $room->restant_minutes;
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
                    <td class="to_hide">
                      @if ($checkin_time && $checkout_time)
                        @php
                          $work_time = $total_seconds;
                          $difference = $work_time - ($pot_minutes*60);
                        @endphp
                        @if ($difference > 0)
                          <span>{{ __('Slower for') }}</span>
                          <span class="label font-weight-bold label-lg label-light-danger label-inline">
                            {{ \Carbon\CarbonInterval::seconds($difference)->cascade()->forHumans(['short' => true, 'options' => 0]) }}
                          </span>
                        @else
                          <span>{{ __('Faster for') }}</span>
                          <span class="label font-weight-bold label-lg label-light-success label-inline">
                            {{ \Carbon\CarbonInterval::seconds($difference)->cascade()->forHumans(['short' => true, 'options' => 0]) }}
                          </span>
                        @endif
                      @else
                        -
                      @endif
                    </td>
                    {{-- Daily Total --}}
                    @if ($employee->PartTime == 0)
                    {{-- <td class="text-center">
                      @if ($checkin_time && $checkout_time)
                        @php
                          $work_time = $total_seconds;
                          // $potential_time = ($depas * $device->depa * 60) + ($restants * $device->restant * 60);
                          $difference = 30240 - $work_time;
                        @endphp
                        @if ($difference > 0)
                          <span>-</span>
                          <span class="label font-weight-bold label-lg label-light-danger label-inline">
                            {{ \Carbon\CarbonInterval::seconds($difference)->cascade()->forHumans(['short' => true, 'options' => 0]) }}
                          </span>
                        @else
                          <span>+</span>
                          <span class="label font-weight-bold label-lg label-light-success label-inline">
                            {{ \Carbon\CarbonInterval::seconds($difference)->cascade()->forHumans(['short' => true, 'options' => 0]) }}
                          </span>
                        @endif
                      @else
                        -
                      @endif
                    </td> --}}
                    @endif
                  </tr>
                {{-- @endforeach --}}
              @endforeach

            @endforeach
            {{-- new start ************************************* --}}

            {{-- new end ************************************* --}}
          </tbody>
        </table>


      </div>

      <div class="table-responsive">

        <table class="table table-sm ">
					<thead class="thead-light">
						<tr class="text-center">
              <th Width="150px" class="to_hide" scope="col">{{ __('Total Vacation') }} (F)</th>
							<th Width="200px" class="to_hide" scope="col">{{ __('Total Unexcused') }} (U)</th>
							<th Width="150px" class="to_hide" scope="col">{{ __('Total Sick') }} (K)</th>
							<th colspan="6" scope="col"></th>
							<th Width="100px" class="to_hide" scope="col">{{ __('Total Depa') }}</th>
							<th Width="100px" class="to_hide" scope="col">{{ __('Total Restant') }}</th>
							{{-- <th Width="100px" scope="col">Total Time</th> --}}
							<th Width="200px" scope="col">{{ __('Total Night Shift 10%') }}</th>
							<th Width="150px" scope="col">{{ __('Total Time') }}</th>
              @if ($employee->PartTime == 0)
							{{-- <th Width="100px" scope="col">Monthly Total</th> --}}
							<th Width="100px" scope="col">{{ __('Total +/- hrs.') }}</th>
              @endif
              <th width="150px" scope="col">{{ __('Percentage') }} (182h)</th>
						</tr>
					</thead>
					<tbody>
						<tr class="text-center">
              <td class="to_hide">{{ $plans->where('symbol', 'F')->count() }}</td>
							<td class="to_hide">{{ $plans->where('symbol', 'U')->count() }}</td>
							<td class="to_hide">{{ $plans->where('symbol', 'K')->count() }}</td>
              <td colspan="6" scope="row"></td>
							<td class="to_hide">{{ $grand_total_depa }}</td>
							<td class="to_hide">{{ $grand_total_restant }}</td>
							{{-- <td>
                  {{ sprintf( "%02.2dh : %02.2ds", floor( $grand_total_time / 3600 ), $grand_total_time % 60 ) }}
                  {{
                    \Carbon\CarbonInterval::
                      seconds($grand_total_time)
                      ->cascade()
                      ->forHumans(['short' => true, 'options' => 0])
                  }}
							</td> --}}
              <td>{{ number_format( (float)($grand_total_nightshift / 3600), 2, '.', '' ) }} ha</td>
              <td>{{ number_format( (float)($grand_total_time / 3600), 2, '.', '' ) }} ha</td>
              @if ($employee->PartTime == 0)    
              @php
                $difference = 657900 - $total_seconds;
              @endphp
              {{-- <td>
                @if ($difference > 0)
                  <span>-</span>
                  <span class="label font-weight-bold label-lg label-light-danger label-inline">
                    {{ \Carbon\CarbonInterval::seconds($difference)->cascade()->forHumans(['short' => true, 'options' => 0]) }}
                  </span>
                @else
                  <span>+</span>
                  <span class="label font-weight-bold label-lg label-light-success label-inline">
                    {{ \Carbon\CarbonInterval::seconds($difference)->cascade()->forHumans(['short' => true, 'options' => 0]) }}
                  </span>
                @endif
                
              </td> --}}
              {{-- <td>{{ $difference > 0 ? '-' : '+' }} {{ number_format( (float)($difference / 3600), 2, '.', '' ) }} ha</td> --}}
              <td>{{ number_format( (float)(($grand_total_time - $difference) / 3600), 2, '.', '' ) }} ha</td>
              @endif
              <td>{{ number_format( (float)($grand_total_time / (182 * 3600) * 100), 2, '.', '' ) }} %</td>
						</tr>
					</tbody>
				</table>


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


  <!-- Modal-->
  <div class="modal fade" id="roomModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-scrollable modal-lg" role="document">
          <div class="modal-content">
              <div class="modal-header">
                  <h5 class="modal-title" id="exampleModalLabel">{{ __('Rooms that were performed') }}</h5>
                  <button type="button" class="close" data-dismiss="modal" aria-label="{{ __('Close') }}">
                      <i aria-hidden="true" class="ki ki-close"></i>
                  </button>
              </div>
              <div class="modal-body" style="height: 300px;">
                <div class="rooms"></div>
              </div>
              <div class="modal-footer">
                  <button type="button" class="btn btn-light-primary font-weight-bold" data-dismiss="modal">{{ __('Close') }}</button>
              </div>
          </div>
      </div>
  </div>
@endsection

{{-- Styles Section --}}
@section('styles')
<style media="screen">
@media print { @page {size: auto !important} }
</style>
@if ($employee->function == 3)
<style>
  .to_hide { display: none !important; }
</style>
@endif
@endsection


{{-- Scripts Section --}}
@section('scripts')
  <script type="text/javascript">
    var current_employee = {!! $employee !!};
    var employee_records = {!! $employee_records !!};
  </script>
    <!--begin::Global Config(global config for global JS scripts)-->
    <script>var KTAppSettings = { "breakpoints": { "sm": 576, "md": 768, "lg": 992, "xl": 1200, "xxl": 1400 }, "colors": { "theme": { "base": { "white": "#ffffff", "primary": "#3699FF", "secondary": "#E5EAEE", "success": "#1BC5BD", "info": "#8950FC", "warning": "#FFA800", "danger": "#F64E60", "light": "#E4E6EF", "dark": "#181C32" }, "light": { "white": "#ffffff", "primary": "#E1F0FF", "secondary": "#EBEDF3", "success": "#C9F7F5", "info": "#EEE5FF", "warning": "#FFF4DE", "danger": "#FFE2E5", "light": "#F3F6F9", "dark": "#D6D6E0" }, "inverse": { "white": "#ffffff", "primary": "#ffffff", "secondary": "#3F4254", "success": "#ffffff", "info": "#ffffff", "warning": "#ffffff", "danger": "#ffffff", "light": "#464E5F", "dark": "#ffffff" } }, "gray": { "gray-100": "#F3F6F9", "gray-200": "#EBEDF3", "gray-300": "#E4E6EF", "gray-400": "#D1D3E0", "gray-500": "#B5B5C3", "gray-600": "#7E8299", "gray-700": "#5E6278", "gray-800": "#3F4254", "gray-900": "#181C32" } }, "font-family": "Poppins" };</script>
    <!--end::Global Config-->

    {{-- vendors --}}

    {{-- page scripts --}}
    <script src="{{ mix('js/pages/records/calendar_print.js') }}" type="text/javascript"></script>
    <script src="{{ mix('js/app.js') }}" type="text/javascript"></script>
@endsection
