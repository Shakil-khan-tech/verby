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

  <!--begin::Entry-->
  <div class="d-flex flex-column-fluid">
    <!--begin::Container-->
    <div class="container">
      <!--begin::Row-->
      <div class="row">
        <div class="col-xl-12">
          <!--begin::Card-->
          <div class="card card-custom gutter-b card-stretch">
            <!--begin::Body-->
            <div class="card-body">
              <!--begin::Search Form-->
              <div class="mb-7">
                <div class="row align-items-center">
                  <div class="col-lg-10 col-xl-9">
                    <div class="row align-items-center">
                      <div class="col-md-3 my-2 my-md-0">
                        <div class="input-icon">
                          <input type="text" class="form-control" placeholder="{{ __('Search...') }}" id="employee_datatable_search_query" />
                          <span>
                            <i class="flaticon2-search-1 text-muted"></i>
                          </span>
                        </div>
                      </div>
                      <div class="col-md-3 my-2 my-md-0">
                        <div class="">
                          <div class="form-group">
                            <label class="mr-3 mb-0 d-none d-md-block">{{ __('Device') }}:</label>
                            <select class="form-control" id="employee_datatable_search_device">
                              <option value="">{{ __('All') }}</option>
                              @foreach ($devices as $key => $device)
                                <option value="{{ $device->id }}">{{ $device->name }}</option>
                              @endforeach
                            </select>
                          </div>
                        </div>
                      </div>
                      <div class="col-md-6 my-2 my-md-0">
                        <div class="">
                          <div class="form-group">
                            <label class="mr-3 mb-0 d-none d-md-block">{{ __('Function') }}:</label>
                            <select class="form-control" id="employee_datatable_search_status">
                              <option value="">{{ __('All') }}</option>
                              @foreach (Config::get('constants.functions') as $key => $function)
                                <option value="{{ $key }}">{{ $function }}</option>
                              @endforeach
                            </select>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="col-lg-2 col-xl-3 mt-5 mt-lg-0">
                    <a href="#" class="btn btn-light-primary px-6 font-weight-bold">{{ __('Search') }}</a>
                  </div>
                </div>
              </div>
              <!--end::Search Form-->
              <!--begin: Datatable-->
              <div class="datatable datatable-bordered datatable-head-custom" id="employee_datatable"></div>
              <!--end: Datatable-->
            </div>
            <!--end::Body-->
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
    <script>
      var employee_json_url = "{{ route('employees.getAll') }}";
    </script>
    <!--begin::Global Config(global config for global JS scripts)-->
    <script>var KTAppSettings = { "breakpoints": { "sm": 576, "md": 768, "lg": 992, "xl": 1200, "xxl": 1400 }, "colors": { "theme": { "base": { "white": "#ffffff", "primary": "#3699FF", "secondary": "#E5EAEE", "success": "#1BC5BD", "info": "#8950FC", "warning": "#FFA800", "danger": "#F64E60", "light": "#E4E6EF", "dark": "#181C32" }, "light": { "white": "#ffffff", "primary": "#E1F0FF", "secondary": "#EBEDF3", "success": "#C9F7F5", "info": "#EEE5FF", "warning": "#FFF4DE", "danger": "#FFE2E5", "light": "#F3F6F9", "dark": "#D6D6E0" }, "inverse": { "white": "#ffffff", "primary": "#ffffff", "secondary": "#3F4254", "success": "#ffffff", "info": "#ffffff", "warning": "#ffffff", "danger": "#ffffff", "light": "#464E5F", "dark": "#ffffff" } }, "gray": { "gray-100": "#F3F6F9", "gray-200": "#EBEDF3", "gray-300": "#E4E6EF", "gray-400": "#D1D3E0", "gray-500": "#B5B5C3", "gray-600": "#7E8299", "gray-700": "#5E6278", "gray-800": "#3F4254", "gray-900": "#181C32" } }, "font-family": "Poppins" };</script>
    <!--end::Global Config-->

    {{-- vendors --}}

    {{-- page scripts --}}
    <script src="{{ mix('js/pages/records/calendar_report.js') }}" type="text/javascript"></script>
    <script src="{{ mix('js/app.js') }}" type="text/javascript"></script>
@endsection
