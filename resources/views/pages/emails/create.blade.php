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
    <div class="container-fluid">

      <!--begin::Row-->
      <div class="row">
        <div class="col-lg-12">
          <!--begin::Card-->
          <div class="card card-custom card-stretch">
            <!--begin::Header-->
            <div class="card-header py-3">
              <div class="card-title align-items-start flex-column">
                <h3 class="card-label font-weight-bolder text-dark">{{ __('Email Information') }}</h3>
                <span class="text-muted font-weight-bold font-size-sm mt-1">{{ __('Create domain email') }}</span>
              </div>
              <div class="card-toolbar">
                <button type="submit" form="domain_email" class="btn btn-success mr-2">{{ __('Save Changes') }}</button>
              </div>
            </div>
            <!--end::Header-->
            <!--begin::Form-->
            <pre>
              @php
                  // print_r($data);
              @endphp
            </pre>
            <form id="domain_email" action="{{ route('emails.store') }}" method="post" class="form">
              @csrf
              <!--begin::Body-->
              <div class="card-body">
                <div class="form-group row">
                  <label class="col-xl-3 col-lg-3 col-form-label">{{ __('Email Address') }}</label>
                  <div class="col-lg-9 col-xl-6">
                    <div class="input-group input-group-lg input-group-solid">
                      <div class="input-group-prepend">
                        <span class="input-group-text">
                          <i class="la la-at"></i>
                        </span>
                      </div>
                      <input type="text" class="form-control form-control-lg form-control-solid" name="email" value="{{ old('email') }}" placeholder="{{ __('Email') }}" />
                    </div>
                  </div>
                </div>
                <div class="form-group row">
                  <label class="col-xl-3 col-lg-3 col-form-label">{{ __('Quota') }}</label>
                  <div class="col-lg-9 col-xl-6">
                    <div class="input-group input-group-lg input-group-solid">
                      <div class="input-group-prepend">
                        <span class="input-group-text">
                          <i class="la">MB</i>
                        </span>
                      </div>
                      <input type="number" min="1" max="20480"
                        class="form-control form-control-lg form-control-solid"
                        name="quota" value="{{ old('quota') != '' ? old('quota') : 128 }}" placeholder="{{ __('Quota') }}" />
                    </div>
                  </div>
                </div>
                <div class="form-group row">
                  <label class="col-xl-3 col-lg-3 col-form-label text-alert">{{ __('New Password') }}</label>
                  <div class="col-lg-9 col-xl-6">
                    <input type="password" class="form-control form-control-lg form-control-solid" name="password" value="" placeholder="{{ __('New password') }}" />
                    <span class="form-text text-muted">{{ __('Type and confirm a password with at least 6 characters.') }}</span>
                  </div>
                </div>
                <div class="form-group row">
                  <label class="col-xl-3 col-lg-3 col-form-label text-alert">{{ __('Verify Password') }}</label>
                  <div class="col-lg-9 col-xl-6">
                    <input type="password" class="form-control form-control-lg form-control-solid" name="password_confirmation" value="" placeholder="{{ __('Verify password') }}" />
                  </div>
                </div>
              </div>
              <!--end::Body-->
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
    <script src="{{ mix('js/app.js') }}" type="text/javascript"></script>
@endsection
