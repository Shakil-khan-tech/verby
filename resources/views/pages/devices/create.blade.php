@php
  $subheader_buttons = [
    (object)[
      'text' => __('Add Device'),
      'url' => route('devices.create'),
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
    <div class="container">
      <!--begin::Row-->
      <div class="row justify-content-center my-10 px-8 my-lg-15 px-lg-10">
        <div class="col-md-12">
          <!--begin::Card-->
          <div class="card card-custom gutter-b example example-compact">
            <div class="card-header">
              <h3 class="card-title">{{ __('Add device') }}</h3>
            </div>
            <!--begin::Form-->
            {{-- <form class="form" action="{{ route('devices.store') }}" method="post">
              @csrf --}}
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
                    <div class="alert-text">{{ __('Add a device here first, then proceed with the configuration on the device.') }}</div>
                  </div>
                </div>
                <div class="form-group row">
                  <div class="col-lg-4">
                    <label>{{ __('Device name') }}</label>
                    <div class="input-group">
                      <div class="input-group-prepend">
                        <span class="input-group-text">
                          <i class="flaticon-information icon-2x text-muted font-weight-bold"></i>
                        </span>
                      </div>
                      <input form="formCreateDevice" type="text" name="name" class="form-control" placeholder="{{ __('Name') }}" value="{{ old('name') }}" required />
                    </div>
                    <span class="form-text text-muted">{{ __('Name the device (ex. based on the location where it will be placed)') }}</span>
                  </div>
                  {{-- <div class="col-lg-4">
                    <label>{{ __('Depa') }}</label>
                    <div class="input-group">
                      <div class="input-group-prepend">
                        <span class="input-group-text">
                          <i class="flaticon-time icon-2x text-muted font-weight-bold"></i>
                        </span>
                      </div>
                      <input form="formCreateDevice" type="number" name="depa" class="form-control" placeholder="{{ __('Depa') }}" value="{{ old('depa') }}" required />
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
                      <input form="formCreateDevice" type="number" name="restant" class="form-control" placeholder="{{ __('Restant') }}" value="{{ old('restant') }}" required />
                    </div>
                    <span class="form-text text-muted">{{ __('Enter a number of minutes for Restant') }}</span>
                  </div> --}}
                </div>
                <div class="separator separator-dashed my-8"></div>
                <p class="font-size-h3">{{ __('Rooms') }}</p>
                <div class="form-group">

									<label>{{ __('Automatic generation') }}</label>
									<div class="input-group">
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
                    <select class="form-control selectpicker" name="autoCategory">
                      @foreach (Config::get('constants.room_categories') as $key => $category)
                        <option value="{{ $key }}">{{ $category }}</option>
                      @endforeach
                    </select>
                    <div class="input-group-prepend"><span class="input-group-text">{{ __('depa min.') }}:</span></div>
										<input type="number" class="form-control" name="autoDepaMin" aria-label="Depa" placeholder="20 min">
                    <div class="input-group-prepend"><span class="input-group-text">{{ __('restant min.') }}:</span></div>
										<input type="number" class="form-control" name="autoRestantMin" aria-label="Restant" placeholder="10 min">
                    <div class="input-group-append">
											<button id="btnAutoAdd" class="btn btn-primary" type="button">{{ __('Generate') }}</button>
										</div>
									</div>

								</div>
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
                      <input type="number" name="manualDepaMin" class="form-control" min="0" placeholder="20 min">
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
                <div class="separator separator-dashed my-8"></div>
                <div class="form-group rooms_generated">
                  <label>{{ __('Generated Rooms') }}</label>
                  @foreach (Config::get('constants.room_categories') as $key => $category)
                    <span class="label label-sm label-rounded label-inline label-{{ Config::get('constants.colors')[$key] }}">{{ $category }}</span>
                  @endforeach
                  <input form="formCreateDevice" class="form-control" id="generatedRooms" name="rooms">
                  <div class="mt-3">
                    <a href="javascript:;" id="generatedRooms_remove" class="btn btn-sm btn-light-primary font-weight-bold">{{ __('Remove all rooms') }}</a>
                  </div>
                  <div class="mt-3 text-muted">{{ __('Here is a list of generated rooms/spaces. After adding, you can remove them individually or all at once with the button above.') }}</div>
                </div>
              </div>
              <div class="card-footer">
                <form class="form" id="formCreateDevice" action="{{ route('devices.store') }}" method="post">
                  @csrf
                  <button type="submit" class="btn btn-primary mr-2">{{ __('Submit') }}</button>
                  <button type="reset" class="btn btn-secondary">{{ __('Cancel') }}</button>
                </form>
              </div>
            {{-- </form> --}}
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
    <script src="{{ mix('js/pages/devices/create.js') }}" type="text/javascript"></script>
    <script src="{{ mix('js/app.js') }}" type="text/javascript"></script>
@endsection
