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

  <div class="card card-custom">
    <div class="card-header flex-wrap border-0 pt-6 pb-0">
      <div class="card-title">
        <h3 class="card-label">{{ $page_title }}
        <span class="d-block text-muted pt-2 font-size-sm">{{ $page_description }}</span></h3>
      </div>
      <div class="card-toolbar">
        <!--begin::Button-->
        <a href="{{ route('employees.create') }}" class="btn btn-primary font-weight-bolder d-flex">
          {{ Metronic::getSVG("media/svg/icons/Communication/Add-user.svg", "svg-icon svg-icon-md inline") }}
          {{ __('New Employee') }}
        </a>
        <!--end::Button-->
      </div>
    </div>
    <div class="card-body">
      <!--begin: Search Form-->
      <!--begin::Search Form-->
      <div class="mb-7">
        <div class="row align-items-center">
          {{-- <div class="col-lg-12">
            {{ __('Total Males') }}: <span id="totalMales" class="mr-2">-</span>
            {{ __('Total Females') }}: <span id="totalFemales" >-</span>
          </div> --}}
          <div class="col-12">
            <div class="row align-items-center">
              
              {{-- style="margin-top: 24px !important" --}}
              <div class="col-md-2 my-2 my-md-0">
                <div class="input-icon">
                  <input type="text" class="form-control" placeholder="{{ __('Search...') }}" id="employee_datatable_search_query" />
                  <span>
                    <i class="flaticon2-search-1 text-muted"></i>
                  </span>
                </div>
              </div>

              <div class="col-md-2 my-2 my-md-0">
                <div class="d-flex align-items-center">
                  <label class="mr-3 mb-0 d-md-block">{{ __('Gender') }}:</label>
                  <select class="form-control" id="employee_datatable_search_gender">
                    <option value="">{{ __('All') }}</option>
                    <option value="0">{{ __('Male') }}</option>
                    <option value="1">{{ __('Female') }}</option>
                  </select>
                </div>
              </div>

              <div class="col-md-2 my-2 my-md-0">
                <div class="d-flex align-items-center">
                  <label class="mr-3 mb-0 d-md-block">{{ __('Active') }}:</label>
                  <select class="form-control" id="employee_datatable_search_active">
                    <option value="">{{ __('All') }}</option>
                    <option value="1">{{ __('Yes') }}</option>
                    <option value="0">{{ __('No') }}</option>
                    <option value="2">{{ __('Active Again') }}</option>
                  </select>
                </div>
              </div>

              <div class="col-md-3 my-2 my-md-0">
                <div class="d-flex align-items-center">
                  <label class="mr-3 mb-0 d-md-block">{{ __('Status') }}:</label>
                  <select class="form-control" id="employee_datatable_search_status">
                    <option value="">{{ __('All') }}</option>
                    <option value="0">{{ __('Shift Manager') }}</option>
                    <option value="1">{{ __('Cleaners') }}</option>
                    <option value="2">{{ __('Maintenance') }}</option>
                    <option value="3">{{ __('Stewarding') }}</option>
                    <option value="4">{{ __('Not Set') }}</option>
                    <option value="6">{{ __('Canceled') }}</option>
                  </select>
                </div>
              </div>

              <div class="col-md-3 my-2 my-md-0">
                <div class="d-flex align-items-center">
                  <label class="mr-3 mb-0 d-md-block">{{ __('Hotel') }}:</label>
                  <select class="form-control" id="employee_datatable_search_hotel">
                    <option value="">{{ __('Select Hotel') }}</option>
                    @foreach($devices as $device)
                    <option value="{{$device['id']}}">{{ucfirst($device['name'])}}</option>
                    @endforeach;
                  </select>
                </div>
              </div>
            </div>
          </div>
          {{-- <div class="col-lg-2 mt-lg-0" style="margin-bottom: 55px !important">
            <a href="#" class="btn btn-light-primary px-6 font-weight-bold">{{ __('Search') }}</a>
          </div> --}}
        </div>
      </div>
      <!--end::Search Form-->
      <!--end: Search Form-->
      <!--begin: Datatable-->
      <div class="datatable datatable-bordered datatable-head-custom horizontale" id="employee_datatable"></div>
      <!--end: Datatable-->
    </div>
  </div>

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
    <script>
      var KTAppSettings = { "breakpoints": { "sm": 576, "md": 768, "lg": 992, "xl": 1200, "xxl": 1400 }, "colors": { "theme": { "base": { "white": "#ffffff", "primary": "#3699FF", "secondary": "#E5EAEE", "success": "#1BC5BD", "info": "#8950FC", "warning": "#FFA800", "danger": "#F64E60", "light": "#E4E6EF", "dark": "#181C32" }, "light": { "white": "#ffffff", "primary": "#E1F0FF", "secondary": "#EBEDF3", "success": "#C9F7F5", "info": "#EEE5FF", "warning": "#FFF4DE", "danger": "#FFE2E5", "light": "#F3F6F9", "dark": "#D6D6E0" }, "inverse": { "white": "#ffffff", "primary": "#ffffff", "secondary": "#3F4254", "success": "#ffffff", "info": "#ffffff", "warning": "#ffffff", "danger": "#ffffff", "light": "#464E5F", "dark": "#ffffff" } }, "gray": { "gray-100": "#F3F6F9", "gray-200": "#EBEDF3", "gray-300": "#E4E6EF", "gray-400": "#D1D3E0", "gray-500": "#B5B5C3", "gray-600": "#7E8299", "gray-700": "#5E6278", "gray-800": "#3F4254", "gray-900": "#181C32" } }, "font-family": "Poppins" };
    </script>
    <!--end::Global Config-->

    {{-- vendors --}}

    {{-- page scripts --}}
    <script src="{{ mix('js/pages/employees/index.js') }}" type="text/javascript"></script>
    <script src="{{ mix('js/app.js') }}" type="text/javascript"></script>
@endsection
