@php
    // setlocale(LC_TIME, LaravelLocalization::getCurrentLocaleName());
    // setlocale(LC_TIME, 'de_DE');
@endphp
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

        <a href="#" class="btn btn-primary font-weight-bold ml-2" data-toggle="modal" data-target="#dateModal">
					<i class="flaticon2-cube"></i>{{ __('Date') }}
        </a>
        <a href="#!" id="btnCalendarPrintAll" class="btn btn-success font-weight-bold ml-2" onclick="window.print();">
					<i class="flaticon2-cube"></i>{{ __('Print Month') }}
        </a>

			</div>
    </div>
    <div class="card-body">

      <div class="table-responsive">

        <table id="tableCalendar" class="table table-vertical-center table-hover table-xs mb-6">
          <thead>
            <tr class="text-center unprint">
              <th scope="col">&nbsp;</th>
              @foreach ($period as $date)
                @if ( $today == $date->format('Y-m-d') )
                  <th colspan="2" scope="col"><span class="navi-text block" style="font-size: 0.7rem;">{{ __('Today') }}</span></th>
                  @elseif ( $holidays->contains('month_day', $date->format('m-d')) )
                  <th colspan="2" scope="col"><span class="navi-text block font-size-xs max-w-40px rotate-90 m-auto"> {{ $holidays->firstWhere('month_day', $date->format('m-d'))->name }} </span></th>
                @else
                  <th colspan="2" scope="col"><span class="navi-text block rotate-90">&nbsp;</span></th>
                @endif
              @endforeach
              <th scope="col">&nbsp;</th>
            </tr>
            <tr class="text-center table-bordered">
              <th scope="col">{{ __('Employee') }}</th>
              @foreach ($period as $date)
                @php
                if ( $today == $date->format('Y-m-d') ) {
                    $day_class = 'bg-info-o-40';
                } elseif ( $holidays->contains('month_day', $date->format('m-d')) ) {
                    $day_class = 'hover:bg-indigo-200 bg-success-o-100';
                } elseif ( $date->dayOfWeek == \Carbon\Carbon::SATURDAY ) {
                    $day_class = 'bg-yellow-50';
                } elseif ( $date->dayOfWeek == \Carbon\Carbon::SUNDAY ) {
                    $day_class = 'bg-red-50';
                } else {
                    $day_class = "";
                }
                @endphp
                <th colspan="2" class="{{ $day_class }} min-w-35px" scope="col">
                  {{ $date->format('d') }} <br>
                  <span class="text-muted font-size-xs">{{ $date->translatedFormat('D') }}</span><br>
                  <span class="font-size-xs">D | R</span><br>
                </th>
              @endforeach
              <th class="unprint" scope="col"><span class="navi-text block rotate-90">{{ __('Print') }}</span></th>
            </tr>
            <tr class="table-bordered unprint">
              <td></td>
              @foreach ($period as $period_key => $date)
                @php
                  if ( $today == $date->format('Y-m-d') ) {
                    $day_class = 'bg-info-o-40';
                  } elseif ( $holidays->contains('month_day', $date->format('m-d')) ) {
                    $day_class = 'hover:bg-indigo-200 bg-success-o-100';
                  } else {
                    $day_class = "";
                  }
                @endphp
                <td class="text-center {{ $day_class }}" colspan="2">
                  <a href="{{ route('pdf.calendar_vertical', ['device' => $device->id, 'date' => $date->format('Y-m-d')]) }}" target="_blank" class="navi-link">
                    <span class="navi-icon">
                        <i class="la la-print"></i>
                    </span>
                  </a>
                </td>
              @endforeach
            </tr>
          </thead>
          <tbody>
            @php
              $function = -999;
            @endphp
            @foreach ($data as $emp_key => $employee)

              @if ($employee->function != $function)
                @php
                  $function = $employee->function;
                @endphp
                <tr class="table-secondary">
                  <th scope="row" colspan="{{ count($period) * 2 + 1 }}" >{{ Config::get('constants.functions')[$employee->function] }}</th>
                </tr>
              @endif

              <tr>
                <td><span data-toggle="tooltip" title="ID: {{ $employee->id }}">{{ $employee->fullname }}</span></td>
              @foreach ($period as $period_key => $date)
                @php
                if ( $today == $date->format('Y-m-d') ) {
                  $day_class = 'bg-info-o-40';
                } elseif ( $holidays->contains('month_day', $date->format('m-d')) ) {
                  $day_class = 'hover:bg-indigo-200 bg-success-o-100';
                } else {
                  $day_class = Config::get('constants.plan_dayofweek')[$date->dayOfWeek];
                }
                $out_of_entry = true;
                @endphp

                @foreach ($employee->combined_entries as $entry)
                  @if ( $entry['start'] <= $date && ($entry['end'] >= $date || $entry['end'] == null ) )
                      @php
                          $out_of_entry = false;
                      @endphp
                      @break
                  @endif
                @endforeach

                <td class="plan_data border-1 hover:bg-indigo-200 {{ $day_class }} {{$out_of_entry ? 'out_of_entry bg-danger-o-95' : ''}}" data-type="0" data-day="{{$date->format('d')}}" data-date="{{$date->format('Y-m-d')}}" data-employee="{{$employee->id}}" data-device="{{$device->id}}">
                  @if ($out_of_entry)
                  <a href="#!" class="block text-center pointer-events-none">0</a>
                  @else
                  <a href="#!" class="block text-center empty" data-toggle="modal" data-target="#calendarDay">0</a>
                  @endif
                </td>
                <td class="plan_data border-1 hover:bg-pink-200 {{ $day_class }} {{$out_of_entry ? 'out_of_entry bg-danger-o-95' : ''}}" data-type="1" data-day="{{$date->format('d')}}" data-date="{{$date->format('Y-m-d')}}" data-employee="{{$employee->id}}" data-device="{{$device->id}}">
                  @if ($out_of_entry)
                  <span class="block text-center pointer-events-none">0</span>
                  @else
                  <a href="#!" class="block text-center empty" data-toggle="modal" data-target="#calendarDay">0</a>
                  @endif
                </td>
              @endforeach
              <td class="text-center unprint">
                <a href="{{ route('pdf.calendar_horizontal', ['device' => $device->id, 'employee' => $employee->id, 'date' => $date->format('Y-m-d')]) }}" target="_blank" class="navi-link">
                  <span class="navi-icon">
                      <i class="la la-print"></i>
                  </span>
                </a>
              </td>
            </tr>
            @endforeach
          </tbody>
        </table>

      </div>
    </div>
  </div>
  <!--end::Card-->

  <!-- Modal-->
  <div class="modal fade" id="dateModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="staticBackdrop" aria-hidden="true">
      <div class="modal-dialog" role="document">
          <div class="modal-content">
            <form action="{{ route('calendars.show', $device) }}" method="post">
              @csrf
              <div class="modal-header">
                  <h5 class="modal-title">{{ __('Select date to view plan!') }}</h5>
                  <button type="button" class="close" data-dismiss="modal" aria-label="{{ __('Close') }}">
                      <i aria-hidden="true" class="ki ki-close"></i>
                  </button>
              </div>
              <div class="modal-body">
                <div class="form-group">
                  <div class="input-group">
                    <div class="input-group-append">
                      <span class="input-group-text">
                        <i class="la la-calendar-check-o"></i>
                      </span>
                    </div>
                    <input id="datePlan" class="form-control form-control-lg form-control-solid" type="month" name="date" value="{{ $date->format('Y-m') }}">
                  </div>
                </div>
              </div>
              <div class="modal-footer">
                  <button type="button" class="btn btn-light-primary font-weight-bold" data-dismiss="modal">{{ __('Close') }}</button>
                  <button type="submit" class="btn btn-primary font-weight-bold">Go!</button>
              </div>
            </form>
          </div>
      </div>
  </div>

  <!-- Modal-->
  <div class="modal fade" id="calendarDay" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="staticBackdrop" aria-hidden="true">
      <div class="modal-dialog modal-xl" role="document">
          <div class="modal-content">
            <form id="modalForm" action="{{ route('calendars.update', $device) }}" method="post">
              @csrf
              <div class="modal-header">
                  <h5 class="modal-title text-center">
                    <span id="modalEmployee">{{ __('loading...') }}</span>
                    <small><span id="modalDate">{{ __('loading...') }}</span></small>
                  </h5>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                      <i aria-hidden="true" class="ki ki-close"></i>
                  </button>
              </div>
              <div class="modal-body">
                <ul class="nav nav-primary nav-bold nav-pills">
                  <li class="nav-item w-50 border-1 m-0">
                    <a id="navi_depa" class="nav-link active" data-toggle="tab" href="#rooms_holder_depa">
                      <span class="nav-text">{{ __('Depa') }} </span>
                      <span class="ml-2"><small>{{ $device->depa }} min.</small></span>
                    </a>
                  </li>
                  <li class="nav-item w-50 border-1 m-0">
                    <a id="navi_restant" class="nav-link" data-toggle="tab" href="#rooms_holder_restant">
                      <span class="nav-text">{{ __('Restant') }}</span>
                      <span class="ml-2"><small>{{ $device->restant }} min.</small></span>
                    </a>
                  </li>
                </ul>
                <div class="tab-content rooms_holder">
                  {{-- Room Holder Depa START --}}
                  <div id="rooms_holder_depa" class="tab-pane fade show active" role="tabpanel" aria-labelledby="rooms_holder_depa">
                    <div class="form-group row">

                      <div class="col-lg-12 my-5">
                        <div class="input-group input-group-sm input-group-solid max-w-250px">
                          <input type="text" class="form-control" id="calendar_room_depa_search" placeholder="{{ __('Search...') }}">
                          <div class="input-group-append">
                            <span class="input-group-text">
                              {{ Metronic::getSVG("media/svg/icons/General/Search.svg", "") }}
                            </span>
                          </div>
                        </div>
                      </div>

                      <div class="col-lg-12">
                        <div class="form-group">
                          <div id="modalRoomsDepa" class="scrollit scroll-300">
                          </div>
                          <span class="form-text text-muted">{{ __('Select Depa Rooms') }}</span>
                        </div>
                      </div>
                      <div class="col-lg-6">
                        <select class="form-control selectpicker" name="modalExtraDepa">
                          @foreach (Config::get('constants.calendar_room_extra') as $key => $extra)
                            <option value="{{ $key }}">{{ $extra }}</option>
                          @endforeach
                        </select>
                        <span class="form-text text-muted">{{ __('Select extra attribute') }}</span>
                      </div>
                      <div class="col-lg-6">
                        <button id="btnModalAddDepa" class="btn btn-primary w-full" type="button">{{ __('Add Depa room') }}</button>
                      </div>
                    </div>
                    <div class="separator separator-dashed my-8"></div>
                    <div class="form-group rooms_generated">
                      <label>{{ __('Generated Depa Rooms') }}</label>
                      @foreach (Config::get('constants.calendar_room_extra') as $key => $extra)
                        <span class="label label-sm label-rounded label-inline label-{{ Config::get('constants.colors')[$key] }}">{{ $extra }}</span>
                      @endforeach
                      <input class="form-control" id="generatedRoomsDepa" name="rooms_depaX">
                      <div class="mt-3">
                        <a href="javascript:;" id="generatedRoomsDepa_remove" class="btn btn-sm btn-light-primary font-weight-bold">{{ __('Remove all Depa rooms') }}</a>
                      </div>
                      <div class="mt-3 text-muted">{{ __('Here is a list of generated rooms/spaces. After adding, you can remove them individually or all at once with the button above.') }}</div>
                    </div>
                  </div>
                  {{-- Room Holder Depa END --}}
                  {{-- Room Holder Restant START --}}
                  <div id="rooms_holder_restant" class="tab-pane fade" role="tabpanel" aria-labelledby="rooms_holder_restant">
                    <div class="form-group row">

                      <div class="col-lg-12 my-5">
                        <div class="input-group input-group-sm input-group-solid max-w-250px">
                          <input type="text" class="form-control" id="calendar_room_restant_search" placeholder="{{ __('Search...') }}">
                          <div class="input-group-append">
                            <span class="input-group-text">
                              {{ Metronic::getSVG("media/svg/icons/General/Search.svg", "") }}
                            </span>
                          </div>
                        </div>
                      </div>

                      <div class="col-lg-12">
                        <div class="form-group">
                          <div id="modalRoomsRestant" class="scrollit scroll-300">
                          </div>
                          <span class="form-text text-muted">{{ __('Select Restant Rooms') }}</span>
                        </div>
                      </div>
                      <div class="col-lg-6">
                        <select class="form-control selectpicker" name="modalExtraRestant">
                          @foreach (Config::get('constants.calendar_room_extra') as $key => $extra)
                            <option value="{{ $key }}">{{ $extra }}</option>
                          @endforeach
                        </select>
                        <span class="form-text text-muted">{{ __('Select extra attribute') }}</span>
                      </div>
                      <div class="col-lg-6">
                        <button id="btnModalAddRestant" class="btn btn-primary w-full" type="button">{{ __('Add Restant room') }}</button>
                      </div>
                    </div>
                    <div class="separator separator-dashed my-8"></div>
                    <div class="form-group rooms_generated">
                      <label>{{ __('Generated Restant Rooms') }}</label>
                      @foreach (Config::get('constants.calendar_room_extra') as $key => $extra)
                        <span class="label label-sm label-rounded label-inline label-{{ Config::get('constants.colors')[$key] }}">{{ $extra }}</span>
                      @endforeach
                      <input class="form-control" id="generatedRoomsRestant" name="rooms_restantX">
                      <div class="mt-3">
                        <a href="javascript:;" id="generatedRoomsRestant_remove" class="btn btn-sm btn-light-primary font-weight-bold">{{ __('Remove all Restant rooms') }}</a>
                      </div>
                      <div class="mt-3 text-muted">{{ __('Here is a list of generated rooms/spaces. After adding, you can remove them individually or all at once with the button above.') }}</div>
                    </div>
                  </div>
                  {{-- Room Holder Restant END --}}
                </div>

              </div>
              <div class="modal-footer">
                  <button type="button" class="btn btn-light-primary font-weight-bold" data-dismiss="modal">{{ __('Close') }}</button>
                  <button type="submit" id="btnModalGo" class="btn btn-primary font-weight-bold">{{ __('Go!') }}</button>
              </div>
            </form>
          </div>
      </div>
  </div>

@endsection

{{-- Styles Section --}}
@section('styles')
<style media="screen">
@media print { @page {size: auto !important} }
</style>
@endsection


{{-- Scripts Section --}}
@section('scripts')
    <!--begin::Global Config(global config for global JS scripts)-->
    <script>var KTAppSettings = { "breakpoints": { "sm": 576, "md": 768, "lg": 992, "xl": 1200, "xxl": 1400 }, "colors": { "theme": { "base": { "white": "#ffffff", "primary": "#3699FF", "secondary": "#E5EAEE", "success": "#1BC5BD", "info": "#8950FC", "warning": "#FFA800", "danger": "#F64E60", "light": "#E4E6EF", "dark": "#181C32" }, "light": { "white": "#ffffff", "primary": "#E1F0FF", "secondary": "#EBEDF3", "success": "#C9F7F5", "info": "#EEE5FF", "warning": "#FFF4DE", "danger": "#FFE2E5", "light": "#F3F6F9", "dark": "#D6D6E0" }, "inverse": { "white": "#ffffff", "primary": "#ffffff", "secondary": "#3F4254", "success": "#ffffff", "info": "#ffffff", "warning": "#ffffff", "danger": "#ffffff", "light": "#464E5F", "dark": "#ffffff" } }, "gray": { "gray-100": "#F3F6F9", "gray-200": "#EBEDF3", "gray-300": "#E4E6EF", "gray-400": "#D1D3E0", "gray-500": "#B5B5C3", "gray-600": "#7E8299", "gray-700": "#5E6278", "gray-800": "#3F4254", "gray-900": "#181C32" } }, "font-family": "Poppins" };</script>
    <!--end::Global Config-->

    {{-- vendors --}}

    {{-- page scripts --}}
    <script type="text/javascript">
      var deviceId = {!! $device->id !!};
      var calendarDate = "{!! $date !!}";
    </script>
    <script src="{{ mix('js/pages/calendars/show.js') }}" type="text/javascript"></script>
    <script src="{{ mix('js/app.js') }}" type="text/javascript"></script>
@endsection
