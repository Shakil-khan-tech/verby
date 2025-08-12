{{-- Extends layout --}}
@extends('layout.default')

{{-- Content --}}
@section('content')

  @if ($errors->any())
    <div class="alert alert-custom alert-outline-danger fade show mb-5 py-0" role="alert">
        <div class="alert-icon"><i class="flaticon-warning"></i></div>
        <div class="alert-text">
          {{ session()->get('error') }}
          @foreach ($errors->all() as $key => $error)
            <p class="mb-0">{{ $key }}: {{ $error }}</p>
          @endforeach

        </div>
        <div class="alert-close">
            <button type="button" class="close" data-dismiss="alert" aria-label="{{ __('Close') }}">
                <span aria-hidden="true"><i class="ki ki-close"></i></span>
            </button>
        </div>
    </div>
  @endif

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

  <div class="row">
    <div class="col-lg-3">
      <!--begin::Card-->
      <div class="card card-custom card-stretch">
        <div class="card-header">
          <div class="card-title">
            <h3 class="card-label">{{ __('Draggable Records') }}</h3>
          </div>
          <div class="card-toolbar">
            <a href="#!" id="btnCalendarRecordPrint" class="btn btn-success font-weight-bold ml-2">
              <i class="flaticon2-cube"></i>{{ __('Print') }}
            </a>
          </div>
        </div>
        <div class="card-body px-2">
          <div id="kt_calendar_external_events" class="fc-unthemed">
            <div class="btn sm:block text-left font-weight-bold btn-light-primary fc-draggable-handle sm:mb-5 cursor-move"
              data-color="fc-event-primary"
              data-hour="08:00"
              data-action="0"
              data-device="1"
              data-perform="0"
              data-identity="3"
            >{{ __('Check In') }}</div>
            <div class="btn sm:block text-left font-weight-bold btn-light-success fc-draggable-handle sm:mb-5 cursor-move"
              data-color="fc-event-success"
              data-hour="12:00"
              data-action="2"
              data-device="1"
              data-perform="0"
              data-identity="3"
            >{{ __('Pause In') }}</div>
            <div class="btn sm:block text-left font-weight-bold btn-light-warning fc-draggable-handle sm:mb-5 cursor-move"
              data-color="fc-event-warning"
              data-hour="13:00"
              data-action="3"
              data-device="1"
              data-perform="0"
              data-identity="3"
            >{{ __('Pause Out') }}</div>
            <div class="btn sm:block text-left font-weight-bold btn-light-danger fc-draggable-handle sm:mb-5 cursor-move"
              data-color="fc-event-danger"
              data-hour="16:00"
              data-action="1"
              data-device="1"
              data-perform="0"
              data-identity="3"
            >{{ __('Check Out') }}</div>
          </div>
        </div>
      </div>
      <!--end::Card-->
    </div>
    <div class="col-lg-9">
      <!--begin::Card-->
      <div class="card card-custom card-stretch">
        <div class="card-body px-0 px-md-9 px-sm-0">
          <div id="kt_calendar"></div>
        </div>
        <!-- Modal Rooms-->
        <div class="modal fade" id="eventModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="eventTitle" aria-hidden="true">
          <div class="modal-dialog modal-xl modal-dialog-scrollable" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="eventTitle"></h5>
                <button id="btnColseModal" type="button" class="close" data-dismiss="modal" aria-label="{{ __('Close') }}">
                  <i aria-hidden="true" class="ki ki-close"></i>
                </button>
              </div>
              <div class="modal-body p-0" style="min-height:450px">
                <!--begin::Form-->
                <form id="formRecordCalendar" class="form" action="{{ route('records.calendar_store_update', $employee->id) }}" method="post">
                  @csrf
                  <input type="hidden" id="record_id" name="record_id">
                  <div class="card-body">
                    {{-- <div class="form-group row">
                      <label class="col-form-label text-right col-lg-3 col-sm-12">Default Time</label>
                      <div class="col-lg-4 col-md-9 col-sm-12">
                        <div class="input-group timepicker">
                          <div class="input-group-prepend">
                            <span class="input-group-text">
                              <i class="la la-clock-o"></i>
                            </span>
                          </div>
                          <input class="form-control" id="record_datetime" readonly="readonly" placeholder="Select time" type="text" />
                        </div>
                      </div>
                    </div> --}}
                    <div class="form-group row">
                      <div class="col-lg-6">
                        <label>{{ __('Date & Time') }}</label>
                        <div class="input-group timepicker">
                          <div class="input-group-prepend">
                            <span class="input-group-text">
                              <i class="la la-clock-o"></i>
                            </span>
                          </div>
                          <input name="time" class="form-control" id="record_datetime" placeholder="{{ __('Select time') }}" type="text" />
                        </div>
                        <span class="form-text text-muted">{{ __('Select the datetime for the new record.') }}</span>
                      </div>
                      <div class="col-lg-6">
                        <label>{{ __('Device') }}</label>
                        <div class="input-group">
                          <div class="input-group-prepend">
                            <span class="input-group-text">
                              <i class="flaticon-time icon-2x text-muted font-weight-bold"></i>
                            </span>
                          </div>
                          <select class="form-control" id="device_input" name="device">
                            @foreach ($devices as $key => $device)
                              <option value="{{ $device->id }}">{{ $device->name }}</option>
                            @endforeach
                          </select>
                        </div>
                        <span class="form-text text-muted">{{ __('Select the device for the new record.') }}</span>
                      </div>
                    </div>
                    <div class="separator separator-dashed my-8"></div>
                    <div class="form-group row">
                      <div class="col-lg-6">
                        <label>{{ __('Action') }}</label>
                        <div class="input-group">
                          <div class="input-group-prepend">
                            <span class="input-group-text">
                              <i class="flaticon-information icon-2x text-muted font-weight-bold"></i>
                            </span>
                          </div>
                          <select class="form-control" id="action_input" name="action">
                            @foreach (Config::get('constants.actions') as $key => $action)
                              <option value="{{ $key }}">{{ $action }}</option>
                            @endforeach
                          </select>
                        </div>
                        <span class="form-text text-muted">{{ __('Select the action for the new record.') }}</span>
                      </div>
                      <div class="col-lg-6">
                        <label>{{ __('Perform') }}</label>
                        <div class="input-group">
                          <div class="input-group-prepend">
                            <span class="input-group-text">
                              <i class="flaticon-time icon-2x text-muted font-weight-bold"></i>
                            </span>
                          </div>
                          <select class="form-control" id="perform_input" name="perform">
                            @foreach (Config::get('constants.performs') as $key => $perform)
                              <option value="{{ $key }}">{{ $perform }}</option>
                            @endforeach
                          </select>
                        </div>
                        <span class="form-text text-muted">{{ __('Select what the employee did perform for the new record.') }}</span>
                      </div>
                    </div>
                    <div class="separator separator-dashed my-8"></div>
                    <div class="row row_rooms">

                      <div class="col-lg-6">
                        <label>{{ __('Depa') }}</label>
                        <div class="form-group row gap-1">
                          <div class="min-w-120px">
                            <select class="form-control selectpicker" name="recordDepaRooms" id="recordDepaRooms" data-size="7" data-live-search="true">

                            </select>
                            <span class="form-text text-muted">{{ __('Select Room') }}</span>
                          </div>
                          <div class="min-w-110px">
                            <select class="form-control selectpicker" name="depaExtra" id="depaExtra">
                              @foreach (Config::get('constants.calendar_room_extra') as $key => $extra)
                                <option value="{{ $key }}">{{ $extra }}</option>
                              @endforeach
                            </select>
                            <span class="form-text text-muted">{{ __('Extra attribute') }}</span>
                          </div>
                          <div class="">
                            <select class="form-control selectpicker" name="depaStatus" id="depaStatus">
                              @foreach (Config::get('constants.room_status') as $key => $status)
                                <option value="{{ $key }}">{{ $status }}</option>
                              @endforeach
                            </select>
                            <span class="form-text text-muted">{{ __('Room status') }}</span>
                          </div>
                          <div class="min-w-180px">
                            <select class="form-control select2" id="volunteers_depa_input" name="volunteer_depa">
    
                            </select>
                            <span class="form-text text-muted">{{ __('Volunteer if any') }}</span>
                          </div>
                          <div class="">
                            <button id="btnDepaAdd" class="btn btn-primary" type="button">{{ __('Add') }}</button>
                          </div>
                        </div>
                        <div class="separator separator-dashed my-8"></div>
                        <div class="form-group row">
                          <div class="col-lg-12">
                            <div class="form-group depa_rooms_selected">

                              <div class="d-inline-block">
                                <label class="d-block">{{ __('Extra attribute') }}</label>
                                @foreach (Config::get('constants.calendar_room_extra') as $key => $extra)
                                <span class="label label-sm label-rounded label-inline label-{{ Config::get('constants.colors')[$key] }}">{{ $extra }}</span>
                                @endforeach
                              </div>
                              |
                              <div class="d-inline-block mb-4">
                                <label class="d-block">{{ __('Room Status') }}</label>
                                @foreach (Config::get('constants.room_status') as $key => $status)
                                  <span class="label label-sm label-rounded label-inline label-outline-dark"><b>{{ mb_substr($status, 0, 1) }}</b>: {{ $status }}</span>
                                @endforeach
                              </div>

                              <input class="form-control" id="depaRooms" name="depa">
                              <div class="mt-3">
                                <a href="javascript:;" id="depaRooms_remove" class="btn btn-sm btn-light-primary font-weight-bold">{{ __('Remove all rooms') }}</a>
                              </div>
                              <div class="mt-3 text-muted">{{ __('Here is a list of generated rooms/spaces. After adding, you can remove them individually or all at once with the button above.') }} </div>
                            </div>
                          </div>

                        </div>
                      </div>

                      <div class="col-lg-6">
                        <label>{{ __('Restant') }}</label>
                        <div class="form-group row gap-1">
                          <div class="min-w-120px">
                            <select class="form-control selectpicker" name="recordRestantRooms" id="recordRestantRooms" data-size="7" data-live-search="true">

                            </select>
                            <span class="form-text text-muted">{{ __('Select Room') }}</span>
                          </div>
                          <div class="min-w-110px">
                            <select class="form-control selectpicker" name="restantExtra" id="restantExtra">
                              @foreach (Config::get('constants.calendar_room_extra') as $key => $extra)
                                <option value="{{ $key }}">{{ $extra }}</option>
                              @endforeach
                            </select>
                            <span class="form-text text-muted">{{ __('Extra attribute') }}</span>
                          </div>
                          <div class="">
                            <select class="form-control selectpicker" name="restantStatus" id="restantStatus">
                              @foreach (Config::get('constants.room_status') as $key => $status)
                                <option value="{{ $key }}">{{ $status }}</option>
                              @endforeach
                            </select>
                            <span class="form-text text-muted">{{ __('Room status') }}</span>
                          </div>
                          <div class="min-w-180px">
                            <select class="form-control select2" id="volunteers_restant_input" name="volunteer_restant">
    
                            </select>
                            <span class="form-text text-muted">{{ __('Volunteer if any') }}</span>
                          </div>
                          <div class="">
                            <button id="btnRestantAdd" class="btn btn-primary" type="button">{{ __('Add') }}</button>
                          </div>
                        </div>
                        <div class="separator separator-dashed my-8"></div>
                        <div class="form-group row">
                          <div class="col-lg-12">
                            <div class="form-group restant_rooms_selected">

                              <div class="d-inline-block">
                                <label class="d-block">{{ __('Extra attribute') }}</label>
                                @foreach (Config::get('constants.calendar_room_extra') as $key => $extra)
                                <span class="label label-sm label-rounded label-inline label-{{ Config::get('constants.colors')[$key] }}">{{ $extra }}</span>
                                @endforeach
                              </div>
                              |
                              <div class="d-inline-block mb-4">
                                <label class="d-block">{{ __('Room Status') }}</label>
                                @foreach (Config::get('constants.room_status') as $key => $status)
                                  <span class="label label-sm label-rounded label-inline label-outline-dark"><b>{{ mb_substr($status, 0, 1) }}</b>: {{ $status }}</span>
                                @endforeach
                              </div>

                              <input class="form-control" id="restantRooms" name="restant">
                              <div class="mt-3">
                                <a href="javascript:;" id="restantRooms_remove" class="btn btn-sm btn-light-primary font-weight-bold">{{ __('Remove all rooms') }}</a>
                              </div>
                              <div class="mt-3 text-muted">{{ __('Here is a list of generated rooms/spaces. After adding, you can remove them individually or all at once with the button above.') }}</div>
                            </div>
                          </div>

                        </div>
                      </div>


                    </div>
                  </div>
                </form>
                <!--end::Form-->
              </div>
              <div class="modal-footer d-flex justify-content-between">
                <button form="formRecordCalendar" id="btnRecordDelete" type="submit" class="btn btn-danger mr-2">{{ __('Delete') }}</button>
                <button form="formRecordCalendar" id="btnRecordCalendar" type="submit" class="btn btn-primary mr-2">{{ __('Submit') }}</button>
              </div>
            </div>
          </div>
        </div>
      </div>
      <!--end::Card-->
    </div>
  </div>

@endsection

{{-- Styles Section --}}
@section('styles')
  @php
      $plan_styles = '';
      foreach (Config::get('constants.plans') as $key => $plan) {
        $plan_styles .= sprintf(".record_calendar_plan.%s::before { content: '%s'; } ", $key, __($plan['text']));
      }
  @endphp
  <link href="{{ asset('plugins/custom/fullcalendar/fullcalendar.bundle.css') }}" rel="stylesheet" type="text/css"/>
  <style>
    {!! $plan_styles !!}
    /* :lang(en) .record_calendar_plan.F::before { content: "aa"; } */
    /* :lang(en) .record_calendar_plan.F::before { content: &#039;Holiday&#039;; content: 'a'; } */
    .record_calendar_plan::before {
      color: #000000;
    }

  </style>
@endsection


{{-- Scripts Section --}}
@section('scripts')
    <script>
      var current_employee = {!! $employee !!};
      var calendar_time = '{!! $calendar_time !!}';
    </script>
    <!--begin::Global Config(global config for global JS scripts)-->
    <script>
      var KTAppSettings = { "breakpoints": { "sm": 576, "md": 768, "lg": 992, "xl": 1200, "xxl": 1400 }, "colors": { "theme": { "base": { "white": "#ffffff", "primary": "#3699FF", "secondary": "#E5EAEE", "success": "#1BC5BD", "info": "#8950FC", "warning": "#FFA800", "danger": "#F64E60", "light": "#E4E6EF", "dark": "#181C32" }, "light": { "white": "#ffffff", "primary": "#E1F0FF", "secondary": "#EBEDF3", "success": "#C9F7F5", "info": "#EEE5FF", "warning": "#FFF4DE", "danger": "#FFE2E5", "light": "#F3F6F9", "dark": "#D6D6E0" }, "inverse": { "white": "#ffffff", "primary": "#ffffff", "secondary": "#3F4254", "success": "#ffffff", "info": "#ffffff", "warning": "#ffffff", "danger": "#ffffff", "light": "#464E5F", "dark": "#ffffff" } }, "gray": { "gray-100": "#F3F6F9", "gray-200": "#EBEDF3", "gray-300": "#E4E6EF", "gray-400": "#D1D3E0", "gray-500": "#B5B5C3", "gray-600": "#7E8299", "gray-700": "#5E6278", "gray-800": "#3F4254", "gray-900": "#181C32" } }, "font-family": "Poppins" };
    </script>
    <!--end::Global Config-->

    {{-- vendors --}}
    <!-- <script src="{{ asset('plugins/custom/fullcalendar/fullcalendar.bundle.js') }}" type="text/javascript"></script> -->
    <script src="{{ asset('js/fix/fullcalendar.bundle.js') }}" type="text/javascript"></script>

    {{-- page scripts --}}
    <script src="{{ mix('js/pages/records/calendar_show.js') }}" type="text/javascript"></script>
    <script src="{{ mix('js/app.js') }}" type="text/javascript"></script>
@endsection