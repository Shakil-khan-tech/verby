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

  <!--begin::Entry-->
  <div class="d-flex flex-column-fluid">
    <!--begin::Container-->
    <div class="container">
      <!--begin::Row-->
      <div class="row justify-content-center my-10 px-8 my-lg-15 px-lg-10">
        <div class="col-md-12">
          <!--begin::Card-->
          <div class="card card-custom gutter-b example example-compact">
            <div class="card-header">
              <h3 class="card-title">{{ __('Add a record') }}</h3>
            </div>
            <!--begin::Form-->
            <form class="form" action="{{ route('records.store') }}" method="post">
              @csrf
              <div class="card-body">
                <div class="form-group">
                  <div class="alert alert-custom alert-default" role="alert">
                    <div class="alert-icon">
                      <span class="svg-icon svg-icon-primary svg-icon-xl">
                        <!--begin::Svg Icon | path:assets/media/svg/icons/Tools/Compass.svg-->
                        <!--begin::Svg Icon | path:assets/media/svg/icons/Devices/Tablet.svg-->
                        <svg width="24px" height="24px" viewBox="0 0 24 24" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                            <!-- Generator: Sketch 50.2 (55047) - http://www.bohemiancoding.com/sketch -->
                            <title>Stockholm-icons / Devices / Tablet</title>
                            <desc>Created with Sketch.</desc>
                            <defs></defs>
                            <g id="Stockholm-icons-/-Devices-/-Tablet" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                <rect id="bound" x="0" y="0" width="24" height="24"></rect>
                                <path d="M6.5,4 L6.5,20 L17.5,20 L17.5,4 L6.5,4 Z M7,2 L17,2 C18.1045695,2 19,2.8954305 19,4 L19,20 C19,21.1045695 18.1045695,22 17,22 L7,22 C5.8954305,22 5,21.1045695 5,20 L5,4 C5,2.8954305 5.8954305,2 7,2 Z" id="Combined-Shape" fill="#000000" fill-rule="nonzero"></path>
                                <polygon id="Combined-Shape" fill="#000000" opacity="0.3" points="6.5 4 6.5 20 17.5 20 17.5 4"></polygon>
                            </g>
                        </svg>
                        <!--end::Svg Icon-->
                      </span>
                    </div>
                    <div class="alert-text">{{ __('You can manually add a record here, which will then be synchronized with the device.') }}</div>
                  </div>
                </div>
                <div class="form-group row">
                  <div class="col-lg-6">
                    <label>{{ __('Employee') }}</label>
                    <div class="input-group">
                      <div class="input-group-prepend">
                        <span class="input-group-text">
                          <i class="flaticon-information icon-2x text-muted font-weight-bold"></i>
                        </span>
                      </div>
                      <select class="form-control select2" id="employee_input" name="employee" value="{{ old('employee') }}">
                        <option label="Label"></option>
                      </select>
                    </div>
                    <span class="form-text text-muted">{{ __('Select the employee for the new record.') }}</span>
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
                    <label>Action</label>
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
                    <label>Perform</label>
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
                <div class="form-group row">
                  <div class="col-lg-6">
                    <label>Identity</label>
                    <div class="input-group">
                      <div class="input-group-prepend">
                        <span class="input-group-text">
                          <i class="flaticon-information icon-2x text-muted font-weight-bold"></i>
                        </span>
                      </div>
                      <select class="form-control" id="identity_input" name="identity" disabled="disabled">
                        <option value="3">{{ __('PC') }}</option>
                      </select>
                    </div>
                    <span class="form-text text-muted">{{ __('Select how the employee got authenticated into the device for the new record.') }}</span>
                  </div>
                  <div class="col-lg-6">
                    <label>Date & Time</label>
                    <div class="input-group date" id="record_datetime" data-target-input="nearest">
                      <div class="input-group-prepend" data-target="#record_datetime" data-toggle="datetimepicker">
                        <span class="input-group-text">
                          <i class="flaticon-time icon-2x text-muted font-weight-bold"></i>
                        </span>
                      </div>
                      <input type="text"  name="time" class="form-control datetimepicker-input" placeholder="{{ __('Select date & time') }}"  data-toggle="datetimepicker" data-target="#record_datetime" autocomplete="off" />
                    </div>
                    <span class="form-text text-muted">{{ __('Select the datetime for the new record.') }}</span>
                  </div>
                </div>
                <div class="separator separator-dashed my-8"></div>
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
                        <button id="btnDepaAdd" class="btn btn-primary" type="button">{{ __('Add room') }}</button>
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
                          <div class="mt-3 text-muted">{{ __('Here is a list of generated rooms/spaces. After adding, you can remove them individually or all at once with the button above.') }}</div>
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
                        <span class="form-text text-muted">{{ __('Select extra attribute') }}</span>
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
                        <button id="btnRestantAdd" class="btn btn-primary" type="button">{{ __('Add room') }}</button>
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
                          <div class="mt-3 text-muted">{{ __('Here is a list of generated rooms/spaces. After adding, you can remove them individually or all at once with the button above.') }} </div>
                        </div>
                      </div>

                    </div>
                  </div>


                </div>
              </div>
              <div class="card-footer">
                <button type="submit" class="btn btn-primary mr-2">{{ __('Submit') }}</button>
                <button type="reset" class="btn btn-secondary">{{ __('Cancel') }}</button>
              </div>
            </form>
            <!--end::Form-->
          </div>
          <!--end::Card-->
        </div>
      </div>
      <!--end::Row-->
    </div>
    <!--end::Container-->
  </div>
  <!--end::Entry-->

@endsection

{{-- Styles Section --}}
@section('styles')

@endsection


{{-- Scripts Section --}}
@section('scripts')
    <!--begin::Global Config(global config for global JS scripts)-->
    <script>var KTAppSettings = { "breakpoints": { "sm": 576, "md": 768, "lg": 992, "xl": 1200, "xxl": 1400 }, "colors": { "theme": { "base": { "white": "#ffffff", "primary": "#3699FF", "secondary": "#E5EAEE", "success": "#1BC5BD", "info": "#8950FC", "warning": "#FFA800", "danger": "#F64E60", "light": "#E4E6EF", "dark": "#181C32" }, "light": { "white": "#ffffff", "primary": "#E1F0FF", "secondary": "#EBEDF3", "success": "#C9F7F5", "info": "#EEE5FF", "warning": "#FFF4DE", "danger": "#FFE2E5", "light": "#F3F6F9", "dark": "#D6D6E0" }, "inverse": { "white": "#ffffff", "primary": "#ffffff", "secondary": "#3F4254", "success": "#ffffff", "info": "#ffffff", "warning": "#ffffff", "danger": "#ffffff", "light": "#464E5F", "dark": "#ffffff" } }, "gray": { "gray-100": "#F3F6F9", "gray-200": "#EBEDF3", "gray-300": "#E4E6EF", "gray-400": "#D1D3E0", "gray-500": "#B5B5C3", "gray-600": "#7E8299", "gray-700": "#5E6278", "gray-800": "#3F4254", "gray-900": "#181C32" } }, "font-family": "Poppins" };</script>
    <!--end::Global Config-->

    {{-- vendors --}}

    {{-- page scripts --}}
    <script src="{{ mix('js/pages/records/create.js') }}" type="text/javascript"></script>
    <script src="{{ mix('js/app.js') }}" type="text/javascript"></script>
@endsection
