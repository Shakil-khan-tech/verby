@php
//   $subheader_buttons = [
//     (object)[
//       'text' => __('Add Contract'),
//       'url' => route('contracts.create'),
//     ],
//   ];
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

  <div class="d-flex flex-row">
    <!--begin::Content-->
    <div class="flex-row-fluid ml-lg-8">

      <div class="row">
        <!--begin::Card-->
        <div class="card card-custom card-stretch col-12">
          <!--begin::Header-->
          <div class="card-header py-3">
            <div class="card-title align-items-start flex-column">
              <h3 class="card-label font-weight-bolder text-dark">{{ __('Reminders') }}</h3>
              <span class="text-muted font-weight-bold font-size-sm mt-1">{{ __('Manage Employee Reminders') }}</span>
            </div>
            <div class="card-toolbar">
              {{-- <button form="update_User" type="submit" class="btn btn-success mr-2">{{ __('Save Changes') }}</button> --}}
            </div>
          </div>
          <!--end::Header-->

          <!--begin::Body-->
          <div class="card-body">
            <!--begin::Search Form-->
            <div class="row align-items-center">
              <div class="col-lg-10 col-xl-9">
                <div class="row align-items-center">
                  <div class="col-md-4 my-2 my-md-0">
                    <div class="input-icon">
                      <input type="text" class="form-control" placeholder="{{ __('Search...') }}" id="files_datatable_search_query" />
                      <span>
                        <i class="flaticon2-search-1 text-muted"></i>
                      </span>
                    </div>
                  </div>
                  <div class="col-md-8 my-2 my-md-0">
                    <div class="">
                      <div class="form-group">
                        <label class="mr-3 mb-0 d-none d-md-block">{{ __('Date') }}:</label>
                        <div class="input-group">
                          <div class="input-group-append">
                            <span class="input-group-text">
                              <i class="la la-calendar-check-o"></i>
                            </span>
                          </div>
                          <input type="text" id="requestedDaterangepicker" class="form-control" name="vacation" readonly="readonly" placeholder="{{ __('Select date range') }}">
                          <div class="input-group-append show">
                            <button type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">{{ __('Action') }}</button>
                            <div class="dropdown-menu">
                              <a class="dropdown-item" id="clearRequestedDaterangepicker" href="#!">{{ __('Clear Date') }}</a>
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

            <!--begin: Datatable-->
            <div class="datatable datatable-bordered datatable-head-custom" id="files_datatable"></div>
            <!--end: Datatable-->
          </div>
          <!--end::Body-->
          
        </div>
        <!--end::Card-->
      </div>

    </div>
    <!--end::Content-->
  </div>

@endsection

{{-- Styles Section --}}
@section('styles')

@endsection


{{-- Scripts Section --}}
@section('scripts')
    <!--begin::Global Config(global config for global JS scripts)-->
    <script>
      var KTAppSettings = { "breakpoints": { "sm": 576, "md": 768, "lg": 992, "xl": 1200, "xxl": 1400 }, "colors": { "theme": { "base": { "white": "#ffffff", "primary": "#3699FF", "secondary": "#E5EAEE", "success": "#1BC5BD", "info": "#8950FC", "warning": "#FFA800", "danger": "#F64E60", "light": "#E4E6EF", "dark": "#181C32" }, "light": { "white": "#ffffff", "primary": "#E1F0FF", "secondary": "#EBEDF3", "success": "#C9F7F5", "info": "#EEE5FF", "warning": "#FFF4DE", "danger": "#FFE2E5", "light": "#F3F6F9", "dark": "#D6D6E0" }, "inverse": { "white": "#ffffff", "primary": "#ffffff", "secondary": "#3F4254", "success": "#ffffff", "info": "#ffffff", "warning": "#ffffff", "danger": "#ffffff", "light": "#464E5F", "dark": "#ffffff" } }, "gray": { "gray-100": "#F3F6F9", "gray-200": "#EBEDF3", "gray-300": "#E4E6EF", "gray-400": "#D1D3E0", "gray-500": "#B5B5C3", "gray-600": "#7E8299", "gray-700": "#5E6278", "gray-800": "#3F4254", "gray-900": "#181C32" } }, "font-family": "Poppins" };
    </script>
    <script>
      Dropzone.autoDiscover = false;
      var reminder_get_url = "{{ route('reminders.employees.get') }}";
    </script>
    <script src="{{ mix('js/pages/reminders/reminder.js') }}" type="text/javascript"></script>
    <script src="{{ mix('js/app.js') }}" type="text/javascript"></script>
@endsection
