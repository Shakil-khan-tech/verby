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

        <div id="statistics_export" class="d-none"></div>
      </div>
      <div class="card-toolbar">

        {{-- <a href="#" class="btn btn-light-primary" data-kt-ecommerce-export="pdf">
          <span class="navi-icon"><i class="la la-file-pdf-o"></i></span>
          <span class="navi-text">PDF</span>
        </a> --}}

      </div>
    </div>
    <div class="card-body">
      <!--begin: Search Form-->
      <div class="mb-7">
        <div class="row align-items-center">
          
          <div class="col-lg-12">
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
                <div class="d-flex align-items-center">
                  <label class="mr-3 mb-0 d-md-block">{{ __('Status') }}:</label>
                  <select class="form-control" id="employee_datatable_search_status">
                    <option value="">{{ __('All') }}</option>
                    <option value="0">{{ __('Shift Manager') }}</option>
                    <option value="1">{{ __('Cleaners') }}</option>
                    <option value="2">{{ __('Maintenance') }}</option>
                    <option value="3">{{ __('Stewarding') }}</option>
                    <option value="4">{{ __('Not Set') }}</option>
                  </select>
                </div>
              </div>
              <div class="col-md-2 my-2 my-md-0">
                <div class="d-flex align-items-center">
                  <label class="mr-3 mb-0 d-md-block">{{ __('Active') }}:</label>
                  <select class="form-control" id="employee_datatable_search_active">
                    <option value="">{{ __('All') }}</option>
                    <option value="1" selected>{{ __('Yes') }}</option>
                    <option value="0">{{ __('No') }}</option>
                  </select>
                </div>
              </div>
              <div class="col-md-4 my-2 my-md-0">

                <div class="dropdown dropdown-inline">
                    <a href="#" id="dropdown-statistics-toggle" class="btn btn-sm font-weight-bolder dropdown-toggle px-5" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                      {{ now()->format('Y-m') }} {{ __('to') }} {{ now()->format('Y-m') }}
                    </a>
                    <div id="dropdown-statistics" class="dropdown-menu dropdown-menu-lg dropdown-menu-right">

                      {{-- <form> --}}
                        <ul class="navi navi-hover">
                          <li class="navi-item">
                              <a href="#" class="navi-link statDefinedMonths bg-gray-200 active" data-months="1">
                                  <i class="navi-icon flaticon2-calendar-4"></i>
                                  <span class="navi-text">1 {{ __('month') }}</span>
                              </a>
                          </li>
                          <li class="navi-item">
                              <a href="#" class="navi-link statDefinedMonths" data-months="3">
                                  <i class="navi-icon flaticon2-calendar-4"></i>
                                  <span class="navi-text">3 {{ __('months') }}</span>
                              </a>
                          </li>
                          <li class="navi-item">
                              <a href="#" class="navi-link statDefinedMonths" data-months="6">
                                  <i class="navi-icon flaticon2-calendar-4"></i>
                                  <span class="navi-text">6 {{ __('months') }}</span>
                              </a>
                          </li>
                          {{-- <div class="dropdown-divider"></div>
                          <li class="navi-item">
                              <a href="#" class="navi-link" data-months="custom">
                                  <i class="navi-icon flaticon2-calendar-4"></i>
                                  <span class="navi-text">{{ __('custom') }}</span>
                              </a>
                          </li>
                          <li class="navi-item -mt-2">
                              <div class="d-flex navi-link">
                                <div class="input-group date" id="date_picker_start" data-target-input="nearest">
                                  <input type="text" class="form-control datetimepicker-input" data-target="#date_picker_start" data-toggle="datetimepicker" />
                                  <div class="input-group-append" data-target="#date_picker_start" data-toggle="datetimepicker">
                                    <span class="input-group-text">
                                      <i class="ki ki-calendar"></i>
                                    </span>
                                  </div>
                                </div>

                                <span> - </span>

                                <div class="input-group date" id="date_picker_end" data-target-input="nearest">
                                  <input type="text" class="form-control datetimepicker-input" data-target="#date_picker_end" data-toggle="datetimepicker" />
                                  <div class="input-group-append" data-target="#date_picker_end" data-toggle="datetimepicker">
                                    <span class="input-group-text">
                                      <i class="ki ki-calendar"></i>
                                    </span>
                                  </div>
                                </div>
                              </div>
                          </li> --}}
                        </ul>

                        {{-- <div class="d-flex justify-content-between px-5 py-2">
                          <a href="#!" class="btn btn-light-primary font-weight-bold" data-dropdown="cancel">{{ __('Cancel') }}</a>
                          <a href="#!" class="btn btn-primary font-weight-bold" data-dropdown="apply">{{ __('Apply') }}</a>
                        </div> --}}
                      {{-- </form> --}}

                    </div>
                </div>


              </div>
            </div>
          </div>

        </div>
      </div>
      <!--end: Search Form-->

      <!--begin: Selected Rows Group Action Form-->
      <div class="mt-10 mb-5 collapse" id="datatable_group_action_form">
        <div class="d-flex align-items-center">
          <div class="font-weight-bold text-danger mr-3">{{ __('Selected') }} 
          <span id="datatable_selected_records">0</span> {{ __('records') }}:</div>
          <button class="btn btn-sm btn-success" type="button" id="datatable_send_email_bulk"><i class="flaticon2-email"></i>{{ __('Send Email') }}</button>
        </div>
      </div>
      <!--end: Selected Rows Group Action Form-->

      <!--begin: Datatable-->
      <div class="datatable datatable-bordered datatable-head-custom" id="employee_datatable"></div>
      <!--end: Datatable-->

      {{-- <table class="table table-striped table-row-bordered align-middle gy-2" id="employee_datatable">
        <thead class="border-bottom border-gray-200 fs-7 fw-bold">
            <tr class="text-start text-muted text-uppercase gs-0">
                <th>{{ __('Name') }}</th>
                <th>{{ __('Canton') }}</th>
                <th>{{ __('Active') }}</th>
                <th>{{ __('Working Hours') }}</th>
                <th>{{ __('Percentage') }} (182h)</th>
                <th>{{ __('Assigned Work Percentage') }}</th>
                <th>{{ __('Function') }}</th>
                <th>{{ __('Actions') }}</th>
            </tr>
        </thead>
        <tbody class="fs-6 fw-semibold text-gray-600"></tbody>    
    </table> --}}
      
    </div>
  </div>


@endsection

{{-- Styles Section --}}
@section('styles')

@endsection


{{-- Scripts Section --}}
@section('scripts')
    <script>
      var statistics_json_url = "{{ route('employees.statistics_ajax') }}";
    </script>
    <!--begin::Global Config(global config for global JS scripts)-->
    <script>
      var KTAppSettings = { "breakpoints": { "sm": 576, "md": 768, "lg": 992, "xl": 1200, "xxl": 1400 }, "colors": { "theme": { "base": { "white": "#ffffff", "primary": "#3699FF", "secondary": "#E5EAEE", "success": "#1BC5BD", "info": "#8950FC", "warning": "#FFA800", "danger": "#F64E60", "light": "#E4E6EF", "dark": "#181C32" }, "light": { "white": "#ffffff", "primary": "#E1F0FF", "secondary": "#EBEDF3", "success": "#C9F7F5", "info": "#EEE5FF", "warning": "#FFF4DE", "danger": "#FFE2E5", "light": "#F3F6F9", "dark": "#D6D6E0" }, "inverse": { "white": "#ffffff", "primary": "#ffffff", "secondary": "#3F4254", "success": "#ffffff", "info": "#ffffff", "warning": "#ffffff", "danger": "#ffffff", "light": "#464E5F", "dark": "#ffffff" } }, "gray": { "gray-100": "#F3F6F9", "gray-200": "#EBEDF3", "gray-300": "#E4E6EF", "gray-400": "#D1D3E0", "gray-500": "#B5B5C3", "gray-600": "#7E8299", "gray-700": "#5E6278", "gray-800": "#3F4254", "gray-900": "#181C32" } }, "font-family": "Poppins" };
    </script>
    <!--end::Global Config-->

    {{-- vendors --}}

    {{-- page scripts --}}
    <script src="{{ mix('plugins/custom/datatables/datatables.bundle.js') }}" type="text/javascript"></script>
    <script src="{{ mix('js/pages/employees/statistics.js') }}" type="text/javascript"></script>
    <script src="{{ mix('js/app.js') }}" type="text/javascript"></script>
@endsection
