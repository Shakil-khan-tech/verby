@php
  $subheader_buttons = [
    (object)[
      'text' => __('View all'),
      'url' => route('devices.index'),
    ],
    (object)[
      'text' => __('Authentication'),
      'color' => 'success',
      'url' => route('devices.auth', $device->id),
    ],
    (object)[
      'text' => __('Reports'),
      'color' => 'info',
      'url' => route('devices.report', $device->id),
    ],
  ];
  $subheader_button_forms = [
    (object)[
      'text' => __('Delete Device'),
      'color' => 'danger',
      'confirm' => __('Deleting the device will delete all employees in it! Are you sure?'),
      'action' => route('devices.destroy', $device->id),
      'method' => 'POST',
      'method_field' => 'DELETE',
    ],
  ];
@endphp
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
    <div class="container px-0">
      <!--begin::Card-->
      <div class="card card-custom gutter-b">
        <div class="card-body px-5">
          <div class="d-flex">
            <!--begin: Info-->
            <div class="flex-grow-1">
              <!--begin: Title-->
              <div class="d-flex align-items-center justify-content-between flex-wrap">
                <div class="mr-3">
                  <!--begin::Name-->
                  <h2 class="d-flex align-items-center text-dark text-hover-primary font-size-h2 font-weight-bold mr-3">{{ $device->name }}
                    <i class="flaticon2-correct text-success icon-md ml-2"></i>
                  </h2>
                  <!--end::Name-->
                  <!--begin::Contacts-->
                  <div class="d-flex flex-wrap my-2">
                    <span class="text-muted font-weight-bold">{{ __('ID') }}: {{ $device->id }}</span>
                  </div>
                  <!--end::Contacts-->
                </div>
                <div class="my-lg-0 my-1">
                  <div class="d-flex flex-wrap align-items-center py-2">
                    <div class="d-flex align-items-center mr-10">
                      <div class="mr-6">
                        <div class="font-weight-bold mb-2">{{ __('Last involved') }}</div>
                        <span class="btn btn-sm btn-text btn-light-primary text-uppercase font-weight-bold">{{ $last_activity->updated_at ? $last_activity->updated_at : __('no action') }}</span>
                      </div>
                    </div>
                    <div class="flex-grow-1 flex-shrink-0 w-150px w-xl-300px mt-4 mt-sm-0">
                      <span class="font-weight-bold">{{ __('% of total employees') }}</span>
                      <div class="progress progress-xs mt-2 mb-2">
                        <div class="progress-bar bg-success" role="progressbar" style="width: {{ round( $device->employees->count() / $total_employees * 100, 2 ) }}%;" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100"></div>
                      </div>
                      <span class="font-weight-bolder text-dark">{{ round( $device->employees->count() / $total_employees * 100, 2 ) }}%</span>
                    </div>
                  </div>
                </div>
              </div>
              <!--end: Title-->
            </div>
            <!--end: Info-->
          </div>
          <div class="separator separator-solid my-7"></div>
          <!--begin: Items-->
          <div class="d-flex align-items-center flex-wrap">
            <!--begin: Item-->
            <div class="d-flex align-items-center flex-lg-fill mr-5 my-1">
              <span class="mr-4">
                <i class="flaticon-time icon-2x text-muted font-weight-bold"></i>
              </span>
              <div class="d-flex flex-column text-dark-75">
                <span class="font-weight-bolder font-size-sm">{{ __('Depa') }}</span>
                <span class="font-weight-bolder font-size-h5">{{ $device->depa }} <span class="text-dark-50 font-weight-bold">{{ __('min') }}</span></span>
              </div>
            </div>
            <!--end: Item-->
            <!--begin: Item-->
            <div class="d-flex align-items-center flex-lg-fill mr-5 my-1">
              <span class="mr-4">
                <i class="flaticon-time icon-2x text-muted font-weight-bold"></i>
              </span>
              <div class="d-flex flex-column text-dark-75">
                <span class="font-weight-bolder font-size-sm">{{ __('Restant') }}</span>
                <span class="font-weight-bolder font-size-h5">{{ $device->depa }} <span class="text-dark-50 font-weight-bold">{{ __('min') }}</span></span>
              </div>
            </div>
            <!--end: Item-->
            <!--begin: Item-->
            <div class="d-flex align-items-center flex-lg-fill mr-5 my-1">
              <span class="mr-4">
                <i class="flaticon-users icon-2x text-muted font-weight-bold"></i>
              </span>
              <div class="d-flex flex-column text-dark-75">
                <span class="font-weight-bolder font-size-sm">{{ __('Employees') }}</span>
                <span class="font-weight-bolder font-size-h5">{{ $device->employees->count() }}</span>
              </div>
            </div>
            <!--end: Item-->
            <!--begin: Item-->
            {{-- <div class="d-flex align-items-center flex-lg-fill mr-5 my-1">
              <span class="mr-4">
                <i class="flaticon-network icon-2x text-muted font-weight-bold"></i>
              </span>
              <div class="d-flex flex-column text-dark-75">
                <span class="font-weight-bolder font-size-sm">Active Employees</span>
                <span class="font-weight-bolder font-size-h5">?</span>
              </div>
            </div> --}}
            <!--end: Item-->
            <!--begin::Item-->
            <div class="hidden sm:flex flex-column flex-lg-fill float-left mb-7">
              <span class="font-weight-bolder mb-4">{{ __('Managers') }}</span>
              @if ( $device->employees->where('function', 0)->count() <= 0)
              {{ __('No managers') }}
              @endif
              <div class="symbol-group symbol-hover">
                @foreach ($device->employees->where('function', 0)->take(5) as $manager)
                  <div class="symbol symbol-30 symbol-circle" data-toggle="tooltip" title="{{ $manager->fullname }}">
                    <img alt="Pic" src="{{ asset('media/users/default.jpg') }}" />
                  </div>
                @endforeach
                @if ( $device->employees->where('function', 0)->count() > 5 )
                  <div class="symbol symbol-30 symbol-circle symbol-light">
                    <span class="symbol-label font-weight-bold">{{ $device->employees->where('function', 0)->count() - 5 }}+</span>
                  </div>
                @endif
              </div>
            </div>
            <!--end::Item-->
            <!--begin::Item-->
            <div class="hidden sm:flex flex-column flex-lg-fill float-left mb-7">
              <span class="font-weight-bolder mb-4">{{ __('Employees') }}</span>
              @if ( $device->employees->whereNotIn('function', 0)->count() <= 0)
              {{ __('No Employees') }}
              @endif
              <div class="symbol-group symbol-hover">
                @foreach ($device->employees->whereNotIn('function', 0)->take(5) as $employee)
                  <div class="symbol symbol-30 symbol-circle" data-toggle="tooltip" title="{{ $employee->fullname }}">
                    <img alt="Pic" src="{{ asset('media/users/default.jpg') }}" />
                  </div>
                @endforeach
                @if ( $device->employees->whereNotIn('function', 0)->count() > 5 )
                  <div class="symbol symbol-30 symbol-circle symbol-light">
                    <span class="symbol-label font-weight-bold">{{ $device->employees->whereNotIn('function', 0)->count() - 5 }}+</span>
                  </div>
                @endif
              </div>
            </div>
            <!--end::Item-->
          </div>
          <!--begin: Items-->
        </div>
      </div>
      <!--end::Card-->
      <!--begin::Row-->
      <div class="row">
        <div class="col-md-12">
          <!--begin::Card-->
          <div class="card card-custom gutter-b example example-compact">
            <div class="card-header">
              <h3 class="card-title">{{ __('Add device') }}</h3>
            </div>
            <!--begin::Form-->
            <form class="form" action="{{ route('devices.update', $device->id) }}" method="post">
              @csrf
              {{ method_field('PUT') }}
              <div class="card-body">
                <div class="form-group row">
                  <div class="col-lg-4">
                    <label>{{ __('Device name') }}</label>
                    <div class="input-group">
                      <div class="input-group-prepend">
                        <span class="input-group-text">
                          <i class="flaticon-information icon-2x text-muted font-weight-bold"></i>
                        </span>
                      </div>
                      <input type="text" name="name" class="form-control" placeholder="{{ __('Name') }}" value="{{ $device->name }}" />
                    </div>
                    <span class="form-text text-muted">{{ __('Name the device (ex. based on the location where it will be placed)') }}</span>
                  </div>
                  <div class="col-lg-4">
                    <label>{{ __('Hotel email') }}</label>
                    <div class="input-group">
                      <div class="input-group-prepend">
                        <span class="input-group-text">
                          <i class="flaticon-mail icon-2x text-muted font-weight-bold"></i>
                        </span>
                      </div>
                      <input type="email" name="hotel_email" class="form-control" placeholder="{{ __('Email') }}" value="{{ $device->hotel_email }}" />
                    </div>
                    <span class="form-text text-muted">{{ __('Hotel email (Hotel Management will receive emails regarding Items and Inventory status)') }}</span>
                  </div>
                  <div class="col-lg-4">
                    <label>{{ __('Hotel Technician email') }}</label>
                    <div class="input-group">
                      <div class="input-group-prepend">
                        <span class="input-group-text">
                          <i class="flaticon-mail icon-2x text-muted font-weight-bold"></i>
                        </span>
                      </div>
                      <input type="email" name="hotel_technician_email" class="form-control" placeholder="{{ __('Email') }}" value="{{ $device->hotel_technician_email }}" />
                    </div>
                    <span class="form-text text-muted">{{ __('Hotel Technician (this will notify the technician regarding Items to be fixed, for this Hotel)') }}</span>
                  </div>
                  {{-- <div class="col-lg-4">
                    <label>{{ __('Depa') }}</label>
                    <div class="input-group">
                      <div class="input-group-prepend">
                        <span class="input-group-text">
                          <i class="flaticon-time icon-2x text-muted font-weight-bold"></i>
                        </span>
                      </div>
                      <input type="number" name="depa" class="form-control" placeholder="{{ __('Depa') }}" value="{{ $device->depa }}" />
                    </div>
                    <span class="form-text text-muted">{{ __('Enter a number of minutes for Depa') }}</span>
                  </div>
                  <div class="col-lg-4">
                    <label>{{ __('Restant') }}</label>
                    <div class="input-group">
                      <div class="input-group-prepend">
                        <span class="input-group-text">
                          <i class="flaticon-time icon-2x text-muted font-weight-bold"></i>
                        </span>
                      </div>
                      <input type="number" name="restant" class="form-control" placeholder="{{ __('Restant') }}" value="{{ $device->restant }}" />
                    </div>
                    <span class="form-text text-muted">{{ __('Enter a number of minutes for Restant') }}</span>
                  </div> --}}
                </div>
                <div class="separator separator-dashed my-8"></div>
                <p class="font-size-h3">{{ __('Rooms') }}</p>
                {{-- Automatic generation start --}}
                <div class="form-group">
									<label>{{ __('Automatic generation') }}</label>
									<div class="input-group auto_add_rooms">
										<div class="input-group-prepend"><span class="input-group-text">{{ __('prefix') }}:</span></div>
										<input type="text" class="form-control" name="autoPrefix" aria-label="Prefix" placeholder="A">
                    <div class="input-group-prepend"><span class="input-group-text">{{ __('pad') }}:</span></div>
										<input type="number" class="form-control" name="autoPad" aria-label="Left Number Padding" placeholder="2">
                    <div class="input-group-prepend"><span class="input-group-text">{{ __('from') }}:</span></div>
										<input type="number" class="form-control" name="autoFrom" aria-label="From" placeholder="1">
                    <div class="input-group-prepend"><span class="input-group-text">{{ __('to') }}:</span></div>
										<input type="number" class="form-control" name="autoTo" aria-label="To" placeholder="99">
                    <div class="input-group-prepend"><span class="input-group-text">{{ __('sufix') }}:</span></div>
										<input type="text" class="form-control" name="autoSufix" aria-label="Sufix" placeholder="B">
                    <div class="input-group-prepend"><span class="input-group-text">{{ __('category') }}:</span></div>
                    <select class="form-control selectpicker min-w-120px" name="autoCategory">
                      @foreach (Config::get('constants.room_categories') as $key => $category)
                        <option value="{{ $key }}">{{ $category }}</option>
                      @endforeach
                    </select>
                    <div class="input-group-prepend"><span class="input-group-text">{{ __('depa min.') }}:</span></div>
										<input type="number" class="form-control" name="autoDepaMin" aria-label="Depa" placeholder="20 min">
                    <div class="input-group-prepend"><span class="input-group-text">{{ __('restant min.') }}:</span></div>
										<input type="number" class="form-control" name="autoRestantMin" aria-label="Restant" placeholder="10 min">
                    <div class="input-group-append auto_add">
											<button id="btnAutoAdd" class="btn btn-primary" type="button">{{ __('Generate') }}</button>
										</div>
									</div>
								</div>
                {{-- Automatic generation end --}}
                {{-- Manual generation start --}}
                <div class="form-group row">
                  <div class="col-xl-2 col-lg-4">
                    <label>{{ __('Manual generation') }}</label>
                    <div class="input-group">
                      <div class="input-group-prepend">
                        <span class="input-group-text">{{ __('name') }}:</span>
                      </div>
                      <input type="text" name="manualName" class="form-control" placeholder="{{ __('Name') }}" />
                    </div>
                    <span class="form-text text-muted">{{ __('Name the room/space') }}</span>
                  </div>
                  <div class="col-xl-2 col-lg-4">
                    <label>&nbsp;</label>
                    <div class="input-group">
                      <div class="input-group-prepend">
                        <span class="input-group-text">{{ __('category') }}:</span>
                      </div>
                      <select class="form-control selectpicker" name="manualCategory">
                        @foreach (Config::get('constants.room_categories') as $key => $category)
                          <option value="{{ $key }}">{{ $category }}</option>
                        @endforeach
                      </select>
                    </div>
                    <span class="form-text text-muted">{{ __('Select the category of room/space') }}</span>
                  </div>
                  <div class="col-xl-2 col-lg-4">
                    <label>&nbsp;</label>
                    <div class="input-group">
                      <div class="input-group-prepend">
                        <span class="input-group-text">{{ __('depa min.') }}:</span>
                      </div>
                      <input type="number" name="manualDepaMin" class="form-control" placeholder="20 min">
                    </div>
                    <span class="form-text text-muted">{{ __('Set the Depa minutes for the current room') }}</span>
                  </div>
                  <div class="col-xl-2 col-lg-4">
                    <label>&nbsp;</label>
                    <div class="input-group">
                      <div class="input-group-prepend">
                        <span class="input-group-text">{{ __('restant min.') }}:</span>
                      </div>
                      <input type="number" name="manualRestantMin" class="form-control" placeholder="20 min">
                    </div>
                    <span class="form-text text-muted">{{ __('Set the Restant minutes for the current room') }}</span>
                  </div>


                  <div class="col-xl-2 col-lg-4">
                    <label>&nbsp</label>
                    <div class="input-group">
                      <div class="input-group-prepend">
                        <span class="input-group-text">{{ __('add') }}:</span>
                      </div>
                      <div class="input-group-append">
												<button id="btnManualAdd" class="btn btn-primary" type="button">{{ __('Add room/space') }}</button>
											</div>
                    </div>
                  </div>
                </div>
                {{-- Manual generation end --}}
                <div class="separator separator-dashed my-8"></div>
                {{-- Generated Rooms start --}}
                <div class="form-group rooms_generated">
                  <label>{{ __('Generated Rooms') }}</label>
                  @foreach (Config::get('constants.room_categories') as $key => $category)
                    <span class="label label-sm label-rounded label-inline label-{{ Config::get('constants.colors')[$key] }}">{{ $category }}</span>
                  @endforeach
                  <input class="form-control" id="generatedRooms" name="rooms">
                  <div class="mt-3">
                    <a href="javascript:;" id="generatedRooms_remove" class="btn btn-sm btn-light-primary font-weight-bold">{{ __('Remove all rooms') }}</a>
                  </div>
                  <div class="mt-3 text-muted">{{ __('Here is a list of generated rooms/spaces. After adding, you can remove them individually or all at once with the button above.') }}</div>
                </div>
                {{-- Generated Rooms end --}}
              </div>
              <div class="card-footer">
                <button type="submit" class="btn btn-primary mr-2">{{ __('Update') }}</button>
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
    <script type="text/javascript">
      var currentRooms = {!! $device->rooms !!};
    </script>
    <script src="{{ mix('js/pages/devices/edit.js') }}" type="text/javascript"></script>
    <script src="{{ mix('js/app.js') }}" type="text/javascript"></script>
@endsection
