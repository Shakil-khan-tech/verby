@php
  $subheader_button_forms = [
    (object)[
      'text' => __('Delete User'),
      'color' => 'danger',
      'confirm' => __('Are you sure?'),
      'action' => route('users.destroy', $user->id),
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

  <!--begin::Profile Overview-->
  <div class="d-flex flex-row">
    @include('pages.widgets._widget-user_personal_aside', ['item_active' => $item_active])
    <!--begin::Content-->
    <div class="flex-row-fluid ml-lg-8">
      <!--begin::Card-->
      <div class="card card-custom card-stretch">
        <!--begin::Header-->
        <div class="card-header py-3">
          <div class="card-title align-items-start flex-column">
            <h3 class="card-label font-weight-bolder text-dark">{{ __('Personal Information') }}</h3>
            <span class="text-muted font-weight-bold font-size-sm mt-1">{{ __('Update users\'s personal information') }}</span>
          </div>
          <div class="card-toolbar">
            <button type="submit" form="update_User" class="btn btn-success mr-2">{{ __('Save Changes') }}</button>
            @if ( $authenticated_user->hasRole('super_admin') )
            <form class="d-inline-block mr-2" action="{{ route('users.change_active', $user->id) }}" method="post">
                @csrf
                <input type="hidden" name="active" value="{{ $user->active ? 0 : 1 }}">
                @if (!$user->active)
                <button type="submit" class="btn btn-primary d-flex me-2">
                    {{ Metronic::getSVG("media/svg/icons/Code/Done-circle.svg", "svg-icon-md") }}{{ __('Activate') }}
                </button>
                @else
                <button type="submit" class="btn btn-warning d-flex me-2">
                  {{ Metronic::getSVG("media/svg/icons/Code/Error-circle.svg", "svg-icon-md") }}{{ __('Disable') }}
                </button>
                @endif
            </form>
            @endif
            <a href="{{ route('users.index') }}" class="btn btn-secondary">{{ __('Cancel') }}</a>
          </div>
        </div>
        <!--end::Header-->
        <!--begin::Form-->
        <form id="update_User" action="{{ route('users.update', $user->id) }}" method="post" class="form" enctype="multipart/form-data">
          @method('PATCH')
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
                  <div class="image-input-wrapper" style="background-image: url({{ asset($user->avatar_path) }})"></div>
                  <label class="btn btn-xs btn-icon btn-circle btn-white btn-hover-text-primary btn-shadow" data-action="change" data-toggle="tooltip" title="" data-original-title="Change avatar">
                    <i class="fa fa-pen icon-sm text-muted"></i>
                    <input type="file" name="avatar" accept=".png, .jpg, .jpeg" />
                    <input type="hidden" name="profile_avatar_remove" />
                  </label>
                  <span class="btn btn-xs btn-icon btn-circle btn-white btn-hover-text-primary btn-shadow" data-action="cancel" data-toggle="tooltip" title="Cancel avatar">
                    <i class="ki ki-bold-close icon-xs text-muted"></i>
                  </span>
                  <span class="btn btn-xs btn-icon btn-circle btn-white btn-hover-text-primary btn-shadow" data-action="remove" data-toggle="tooltip" title="Remove avatar">
                    <i class="ki ki-bold-close icon-xs text-muted"></i>
                  </span>
                </div>
                <span class="form-text text-muted">{{ __('Allowed file types:') }} png, jpg, jpeg.</span>
              </div>
            </div>
            <div class="form-group row">
              <label class="col-xl-3 col-lg-3 col-form-label">{{ __('Name') }}</label>
              <div class="col-lg-9 col-xl-6">
                <input class="form-control form-control-lg form-control-solid" type="text" name="name" value="{{ $user->name }}" placeholder="{{ __('Full Name') }}" />
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
                  <input type="text" class="form-control form-control-lg form-control-solid" name="email" value="{{ $user->email }}" placeholder="{{ __('Email') }}" />
                </div>
              </div>
            </div>
            @if (  $can_change_pass )
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
            @endif

          </div>
          <!--end::Body-->
        </form>
        <!--end::Form-->
      </div>
    </div>
    <!--end::Content-->
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
    <script src="{{ mix('js/pages/_misc/aside.js') }}" type="text/javascript"></script>
    <script src="{{ mix('js/pages/users/show.js') }}" type="text/javascript"></script>
    <script src="{{ mix('js/app.js') }}" type="text/javascript"></script>
@endsection
