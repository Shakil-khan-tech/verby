@php
  $subheader_buttons = [
    (object)[
      'text' => __('Add Email'),
      'color' => 'primary',
      'url' => route('emails.create'),
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

  <!--begin::Card-->
  <div class="card card-custom gutter-b">
    <div class="card-header flex-wrap border-0 pt-6 pb-0">
      <div class="card-title">
        <h3 class="card-label">{{ $page_title }}
        <span class="d-block text-muted pt-2 font-size-sm">{{ $page_description }}</span></h3>
      </div>
    </div>
    <div class="card-body">
      <!--begin: Search Form-->
      <!--begin::Search Form-->
      <div class="mb-7">
        <div class="row align-items-center">
          <div class="col-lg-4 col-xl-4">
            <div class="row align-items-center">
              <div class="col-md-12 my-2 my-md-0">
                <div class="input-icon">
                  <input type="text" class="form-control" placeholder="{{ __('Search') }}..." id="kt_datatable_search_query" />
                  <span>
                    <i class="flaticon2-search-1 text-muted"></i>
                  </span>
                </div>
              </div>
            </div>
          </div>
          <div class="col-lg-3 col-xl-3 mt-5 mt-lg-0">
            <a href="#" class="btn btn-light-primary px-6 font-weight-bold">{{ __('Search') }}</a>
          </div>
        </div>
      </div>
      <!--end::Search Form-->
      <!--end: Search Form-->
      <!--begin: Selected Rows Group Action Form-->
      <div class="mt-10 mb-5 collapse" id="kt_datatable_group_action_form">
        <div class="d-flex align-items-center">
          <div class="font-weight-bold text-danger mr-3">{{ __('Selected') }}
          <span id="kt_datatable_selected_records">0</span> {{ __('records') }}:</div>
          <button class="btn btn-sm btn-danger mr-2" type="button" id="kt_datatable_delete_all">{{ __('Delete Selected') }}</button>
        </div>
      </div>
      <!--end: Selected Rows Group Action Form-->
      <!--begin: Datatable-->
      <div class="datatable datatable-bordered datatable-head-custom" id="kt_datatable"></div>
      <!--end: Datatable-->
    </div>
  </div>
  <!--end::Card-->

@endsection

{{-- Styles Section --}}
@section('styles')

@endsection


{{-- Scripts Section --}}
@section('scripts')
    <script>
      var emails_json_url = "{{ route('emails.getAll') }}";
    </script>
    <!--begin::Global Config(global config for global JS scripts)-->
    <script>var KTAppSettings = { "breakpoints": { "sm": 576, "md": 768, "lg": 992, "xl": 1200, "xxl": 1400 }, "colors": { "theme": { "base": { "white": "#ffffff", "primary": "#3699FF", "secondary": "#E5EAEE", "success": "#1BC5BD", "info": "#8950FC", "warning": "#FFA800", "danger": "#F64E60", "light": "#E4E6EF", "dark": "#181C32" }, "light": { "white": "#ffffff", "primary": "#E1F0FF", "secondary": "#EBEDF3", "success": "#C9F7F5", "info": "#EEE5FF", "warning": "#FFF4DE", "danger": "#FFE2E5", "light": "#F3F6F9", "dark": "#D6D6E0" }, "inverse": { "white": "#ffffff", "primary": "#ffffff", "secondary": "#3F4254", "success": "#ffffff", "info": "#ffffff", "warning": "#ffffff", "danger": "#ffffff", "light": "#464E5F", "dark": "#ffffff" } }, "gray": { "gray-100": "#F3F6F9", "gray-200": "#EBEDF3", "gray-300": "#E4E6EF", "gray-400": "#D1D3E0", "gray-500": "#B5B5C3", "gray-600": "#7E8299", "gray-700": "#5E6278", "gray-800": "#3F4254", "gray-900": "#181C32" } }, "font-family": "Poppins" };</script>
    <!--end::Global Config-->

    {{-- vendors --}}
    <script src="{{ mix('js/pages/emails/index.js') }}" type="text/javascript"></script>

    {{-- page scripts --}}
    <script src="{{ mix('js/app.js') }}" type="text/javascript"></script>
@endsection
