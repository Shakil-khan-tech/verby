@php
  $subheader_buttons = [
    (object)[
      'text' => __('Add Record'),
      'url' => route('records.create'),
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

  <div class="card card-custom">
    <div class="card-header flex-wrap border-0 pt-6 pb-0">
      <div class="card-title">
        <h3 class="card-label">{{ $page_title }}
        <span class="d-block text-muted pt-2 font-size-sm">{{ $page_description }}</span></h3>
      </div>
      <div class="card-toolbar">

        <a href="#" class="btn btn-primary font-weight-bold ml-2" data-toggle="modal" data-target="#selectEmployee">
					<i class="flaticon2-calendar-9"></i>{{ __('Calendar View') }}
        </a>

			</div>
    </div>
    <div class="card-body">
      <!--begin: Search Form-->
      <!--begin::Search Form-->
      <div class="mb-7">
        <div class="row align-items-center">
          <div class="col-lg-10 col-xl-9">
            <div class="row align-items-center">
              <div class="col-md-3 my-2 my-md-0">
                <div class="input-icon">
                  <input type="text" class="form-control" placeholder="{{ __('Search...') }}" id="records_datatable_search_query" />
                  <span>
                    <i class="flaticon2-search-1 text-muted"></i>
                  </span>
                </div>
              </div>
              <div class="col-md-3 my-2 my-md-0">
                <div class="">
                  <div class="form-group">
                    <label class="mr-3 mb-0 d-none d-md-block">{{ __('Device') }}:</label>
        						<select class="form-control" id="records_datatable_search_device">
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
                    <label class="mr-3 mb-0 d-none d-md-block">{{ __('Date') }}:</label>
        						<div class="input-group">
                      <div class="input-group-append">
                        <span class="input-group-text">
                          <i class="la la-calendar-check-o"></i>
                        </span>
                      </div>
                      <input type="text" id="recordsDaterangepicker" class="form-control" name="vacation" readonly="readonly" placeholder="{{ __('Select date range') }}">
        							<div class="input-group-append show">
                        <button type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">{{ __('Action') }}</button>
                        <div class="dropdown-menu">
                          <a class="dropdown-item" id="clearRecordsDaterangepicker" href="#!">{{ __('Clear Date') }}</a>
                        </div>
        							</div>
        						</div>
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
      <!--end: Search Form-->
      <!--begin: Datatable-->
      <div class="datatable datatable-bordered datatable-head-custom" id="records_datatable"></div>
      <!--end: Datatable-->

      
      <!-- Modal Employees-->
      <div class="modal fade" id="selectEmployee" tabindex="-1" role="dialog" aria-labelledby="employeesTitle" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="employeesTitle">{{ __('Employees') }}
                <span class="d-block text-muted font-size-sm">{{ __('Select Employee to view record calendar') }}</span>
              </h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="{{ __('Close') }}">
                  <i aria-hidden="true" class="ki ki-close"></i>
              </button>
            </div>
            <div class="modal-body p-0">
              <!--begin::Card-->
              <div class="card card-custom gutter-b card-stretch">
                <!--begin::Body-->
                <div class="card-body">
                  <!--begin::Search Form-->
                  <div class="mb-7">
                    <div class="row align-items-center">
                      <div class="col-lg-9 col-xl-9">
                        <div class="row align-items-center">
                          <div class="col-md-12 my-2 my-md-0">
                            <div class="input-icon">
                              <input type="text" class="form-control" placeholder="{{ __('Search...') }}" id="record_employee_datatable_search_query" />
                              <span>
                                <i class="flaticon2-search-1 text-muted"></i>
                              </span>
                            </div>
                          </div>
                        </div>
                      </div>
                      <div class="col-lg-3 col-xl-3 mt-5 mt-lg-0">
                        <a href="#" class="btn btn-light-primary w-full px-6 font-weight-bold">{{ __('Search') }}</a>
                      </div>
                    </div>
                  </div>
                  <!--end::Search Form-->
                  <!--begin: Datatable-->
                  <table class="datatable datatable-bordered datatable-head-custom" id="record_employee_datatable">
                    <thead>
                      <tr>
                        <th title="Employee">{{ __('Employee') }}</th>
                        <th title="View">{{ __('View') }}</th>
                      </tr>
                    </thead>
                    <tbody>
                      @foreach ($employees as $employee)
                      <tr>
                        <td width="200">
                          <div class="d-flex align-items-center">
                            <div class="symbol symbol-40 symbol-{{ Config::get('constants.colors')[$employee->function] }} flex-shrink-0">
                              <div class="symbol-label">{{ $employee->id }}</div>
                            </div>
                            <div class="ml-2">
                              <a class="block" href="{{ route('records.calendar_show', [$employee->id]) }}">
                                <div class="text-dark-75 font-weight-bold line-height-sm">{{ $employee->fullname }}</div>
                                <span class="font-size-sm text-dark-50 text-hover-primary">{{ Config::get('constants.functions')[$employee->function] }}<span>
                              </span></span></a>
                            </div>
                          </div>
                        </td>
                        <td class="text-left">
                          <a href="{{ route('records.calendar_show', [$employee->id]) }}" class="btn btn-icon btn-light btn-hover-primary btn-sm">
                            {{ Metronic::getSVG("media/svg/icons/Navigation/Arrow-right.svg", "svg-icon-md svg-icon-primary") }}
                          </a>
                        </td>
                      </tr>
                      @endforeach
                    </tbody>
                  </table>
                  <!--end: Datatable-->
                </div>
                <!--end::Body-->
              </div>
              <!--end::Card-->
            </div>
          </div>
        </div>
      </div>
      <!-- Modal Rooms-->
      <div class="modal fade" id="roomModal" tabindex="-1" role="dialog" aria-labelledby="roomsTitle" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title align-items-center d-flex gap-2 modal-title" id="roomsTitle">{{ __('Rooms that were performed') }}
                <span class="font-bold label label-inline label-lg py-8"></span>
              </h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
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

    </div>
  </div>

@endsection

{{-- Styles Section --}}
@section('styles')

@endsection


{{-- Scripts Section --}}
@section('scripts')
    <script>
      var records_json_url = "{{ route('records.ajax') }}";
      var employee_json_url = "{{ route('employees.getAll') }}";
    </script>
    <!--begin::Global Config(global config for global JS scripts)-->
    <script>
      var KTAppSettings = { "breakpoints": { "sm": 576, "md": 768, "lg": 992, "xl": 1200, "xxl": 1400 }, "colors": { "theme": { "base": { "white": "#ffffff", "primary": "#3699FF", "secondary": "#E5EAEE", "success": "#1BC5BD", "info": "#8950FC", "warning": "#FFA800", "danger": "#F64E60", "light": "#E4E6EF", "dark": "#181C32" }, "light": { "white": "#ffffff", "primary": "#E1F0FF", "secondary": "#EBEDF3", "success": "#C9F7F5", "info": "#EEE5FF", "warning": "#FFF4DE", "danger": "#FFE2E5", "light": "#F3F6F9", "dark": "#D6D6E0" }, "inverse": { "white": "#ffffff", "primary": "#ffffff", "secondary": "#3F4254", "success": "#ffffff", "info": "#ffffff", "warning": "#ffffff", "danger": "#ffffff", "light": "#464E5F", "dark": "#ffffff" } }, "gray": { "gray-100": "#F3F6F9", "gray-200": "#EBEDF3", "gray-300": "#E4E6EF", "gray-400": "#D1D3E0", "gray-500": "#B5B5C3", "gray-600": "#7E8299", "gray-700": "#5E6278", "gray-800": "#3F4254", "gray-900": "#181C32" } }, "font-family": "Poppins" };
    </script>
    <!--end::Global Config-->

    {{-- vendors --}}

    {{-- page scripts --}}
    <script src="{{ mix('js/pages/records/index.js') }}" type="text/javascript"></script>
    <script src="{{ mix('js/app.js') }}" type="text/javascript"></script>
@endsection
