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

  <!--begin::Profile Overview-->
  <div class="d-flex flex-row">
    <!--begin::Container-->
    <div class="container">
      <!--begin::Content-->
      <!--begin::Row-->
      <div class="row justify-content-center my-10 px-8 my-lg-15 px-lg-10">
        <div class="col-md-8">
          <!--begin::Card-->
          <div class="card card-custom card-stretch">
          <!--begin::Header-->
          <div class="card-header py-3">
            <div class="card-title align-items-start flex-column">
              <h3 class="card-label font-weight-bolder text-dark">{{ __('Personal Information') }}</h3>
              <span class="text-muted font-weight-bold font-size-sm mt-1">{{ __('Add users\'s personal information') }}</span>
            </div>
            <div class="card-toolbar">
              <button type="submit" form="create_User" class="btn btn-success mr-2">{{ __('Add User') }}</button>
              <button type="reset" form="create_User" class="btn btn-secondary">{{ __('Cancel') }}</button>
            </div>
          </div>
          <!--end::Header-->
          <!--begin::Form-->
          <form id="create_User" action="{{ route('users.store') }}" method="post" class="form" enctype="multipart/form-data">
            @csrf
            <!--begin::Body-->
            <div class="card-body">
              <div class="row">
                <label class="col-xl-3"></label>
                <div class="col-lg-9 col-xl-6">
                  <h5 class="font-weight-bold mb-6">{{ __('Contact Info') }}</h5>
                </div>
              </div>
              <div class="form-group row">
                <label class="col-xl-3 col-lg-3 col-form-label">{{ __('Avatar') }}</label>
                <div class="col-lg-9 col-xl-6">
                  <div class="image-input image-input-outline" id="kt_profile_avatar" style="background-image: url({{ asset('media/users/blank.png') }})">
                    <div class="image-input-wrapper"></div>
                    <label class="btn btn-xs btn-icon btn-circle btn-white btn-hover-text-primary btn-shadow" data-action="change" data-toggle="tooltip" title="" data-original-title="{{ __('Change avatar') }}">
                      <i class="fa fa-pen icon-sm text-muted"></i>
                      <input type="file" name="avatar" accept=".png, .jpg, .jpeg" />
                      <input type="hidden" name="profile_avatar_remove" />
                    </label>
                    <span class="btn btn-xs btn-icon btn-circle btn-white btn-hover-text-primary btn-shadow" data-action="cancel" data-toggle="tooltip" title="{{ __('Cancel avatar') }}">
                      <i class="ki ki-bold-close icon-xs text-muted"></i>
                    </span>
                    <span class="btn btn-xs btn-icon btn-circle btn-white btn-hover-text-primary btn-shadow" data-action="remove" data-toggle="tooltip" title="{{ __('Remove avatar') }}">
                      <i class="ki ki-bold-close icon-xs text-muted"></i>
                    </span>
                  </div>
                  <span class="form-text text-muted">{{ __('Allowed file types:') }} png, jpg, jpeg.</span>
                </div>
              </div>
              <div class="form-group row">
                <label class="col-xl-3 col-lg-3 col-form-label">{{ __('Name') }}</label>
                <div class="col-lg-9 col-xl-6">
                  <input class="form-control form-control-lg form-control-solid" type="text" name="name" value="{{ old('name') }}" placeholder="{{ __('Full Name') }}" required />
                </div>
              </div>
              <div class="form-group row">
                <label class="col-xl-3 col-lg-3 col-form-label">{{ __('Email Address') }}</label>
                <div class="col-lg-9 col-xl-6">
                  <div class="input-group input-group-lg input-group-solid">
                    <div class="input-group-prepend">
                      <span class="input-group-text">
                        <i class="la la-at"></i>
                      </span>
                    </div>
                    <input type="text" class="form-control form-control-lg form-control-solid" name="email" value="{{ old('email') }}" placeholder="{{ __('Email') }}" required />
                  </div>
                </div>
              </div>
              <div class="form-group row">
                <label class="col-xl-3 col-lg-3 col-form-label text-alert">{{ __('New Password') }}</label>
                <div class="col-lg-9 col-xl-6">
                  <input type="password" class="form-control form-control-lg form-control-solid" name="password" value="" placeholder="{{ __('New password') }}" required />
                  <span class="form-text text-muted">{{ __('Type and confirm a password with at least 6 characters.') }}</span>
                </div>
              </div>
              <div class="form-group row">
                <label class="col-xl-3 col-lg-3 col-form-label text-alert">{{ __('Verify Password') }}</label>
                <div class="col-lg-9 col-xl-6">
                  <input type="password" class="form-control form-control-lg form-control-solid" name="password_confirmation" value="" placeholder="{{ __('Verify password') }}" required />
                </div>
              </div>
            </div>
            <!--end::Body-->
          </form>
          <!--end::Form-->
        </div>
        </div>
      </div>
      <!--end::Row-->
    </div>
    <!--end::Container-->
  </div>
  <!--end::Profile Overview-->

@endsection

{{-- Styles Section --}}
@section('styles')
    
@endsection


{{-- Scripts Section --}}
@section('scripts')
    {{-- vendors --}}

    {{-- page scripts --}}
    <script src="{{ mix('js/pages/users/show.js') }}" type="text/javascript"></script>
    <script src="{{ mix('js/app.js') }}" type="text/javascript"></script>
@endsection
