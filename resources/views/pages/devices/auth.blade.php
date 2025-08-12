@php
  $subheader_buttons = [
    (object)[
      'text' => __('Edit Device'),
      'color' => 'primary',
      'url' => route('devices.edit', $device->id),
    ],
  ];
  $subheader_button_forms = [
    (object)[
      'text' => __('Delete Device'),
      'color' => 'danger',
      'confirm' => __('Deleting the device will delete all employees in it! Are you sure?'),
      'action' => route('devices.destroy', $device->id),
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
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
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
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
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
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true"><i class="ki ki-close"></i></span>
            </button>
        </div>
    </div>
  @endif

  <!--begin::Profile Role-->
  <div class="d-flex flex-row">
    @include('pages.widgets._widget-device_aside', ['item_active' => $item_active])
    <!--begin::Content-->
    <div class="flex-row-fluid ml-lg-8">
      <!--begin::Card-->
      <div class="card card-custom card-stretch">
        <!--begin::Header-->
        <div class="card-header py-3">
          <div class="card-title align-items-start flex-column">
            <h3 class="card-label font-weight-bolder text-dark">{{ __('Auth Information') }}</h3>
            <span class="text-muted font-weight-bold font-size-sm mt-1">{{ __('Update device\'s authentication') }}</span>
          </div>
          <div class="card-toolbar">
            <button type="submit" form="device_auth" class="btn btn-success mr-2" {{ !$can_change_pass ? 'disabled' : '' }}>{{ __('Save Changes') }}</button>
          </div>
        </div>
        <!--end::Header-->
        <!--begin::Form-->
        <form id="device_auth" action="{{ route('devices.auth_update', $device->id) }}" method="post" class="form">
          @method('PATCH')
          @csrf
          <!--begin::Body-->
          <div class="card-body">
            <div class="alert alert-custom alert-light-danger fade show mb-5" role="alert">
              <div class="alert-icon">
                <i class="flaticon-information"></i>
              </div>
              <div class="alert-text font-weight-bold">
                {{ __('When changing the device password here, you will need to proceed with the authentication on the device with the new password') }}
                <br>{{ __('or the device might inadvertently get locked out of the system!') }}
              </div>
              <div class="alert-close">
                <button type="button" class="close" data-dismiss="alert" aria-label="{{ __('Close') }}">
                  <span aria-hidden="true">
                    <i class="ki ki-close"></i>
                  </span>
                </button>
              </div>
            </div>

            <div class="alert alert-custom alert-outline-secondary fade show mb-10 py-1" role="alert">
              <div class="alert-icon">
                <i class="flaticon-information"></i>
              </div>
              <div class="alert-text text-muted">
                {{ __('Password was changed on') }}: <span class="">{{ $device->user->updated_at->format('d.m.Y H:i:s') }}</span>
              </div>
              {{-- <div class="alert-close">
                <button type="button" class="close" data-dismiss="alert" aria-label="{{ __('Close') }}">
                  <span aria-hidden="true">
                    <i class="ki ki-close"></i>
                  </span>
                </button>
              </div> --}}
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
                  <input type="text" class="form-control form-control-lg form-control-solid" name="email" value="{{ $device->user->email }}" placeholder="{{ __('Email') }}" readonly />
                </div>
              </div>
            </div>
            @if ( $can_change_pass )
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
            @else
            <div class="alert alert-custom alert-notice alert-secondary fade show mb-5" role="alert">
              <div class="alert-icon">
                <i class="flaticon-warning"></i>
              </div>
              <div class="alert-text">{{ __('Only Super admins and admins can change device password!') }}</div>
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
  <!--end::Profile Role-->

@endsection

{{-- Styles Section --}}
@section('styles')

@endsection


{{-- Scripts Section --}}
@section('scripts')
    {{-- vendors --}}

    {{-- page scripts --}}
    <script src="{{ mix('js/app.js') }}" type="text/javascript"></script>
@endsection
