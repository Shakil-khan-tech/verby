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

  <!--begin::Profile Role-->
  <div class="d-flex flex-row">
    @include('pages.widgets._widget-user_personal_aside', ['item_active' => $item_active])
    <!--begin::Content-->
    <div class="flex-row-fluid ml-lg-8">
      <!--begin::Card-->
      <div class="card card-custom card-stretch">
        <!--begin::Header-->
        <div class="card-header py-3">
          <div class="card-title align-items-start flex-column">
            <h3 class="card-label font-weight-bolder text-dark">{{ __('Access Management Informatio') }}</h3>
            <span class="text-muted font-weight-bold font-size-sm mt-1">{{ __('Update users\' access management') }}</span>
          </div>
          <div class="card-toolbar">
            <button type="submit" form="update_User" class="btn btn-success mr-2">{{ __('Save Changes') }}</button>
            <button type="reset" form="update_User" class="btn btn-secondary">{{ __('Cancel') }}</button>
          </div>
        </div>
        <!--end::Header-->
        <!--begin::Form-->
        <form id="update_User" action="{{ route('users.role_update', $user->id) }}" method="post" class="form">
          @method('PATCH')
          @csrf
          <!--begin::Body-->
          <div class="card-body">
            <div class="form-group row">
              <label class="col-xl-3 col-lg-3 col-form-label">{{ __('Select Access Management') }}</label>
              <div class="col-lg-9 col-xl-6">
                <select class="form-control form-control-lg form-control-solid" name="role">
                  @foreach ($all_roles as $role)
                    @if ( $user_roles->contains( $role->name ) )

                      <option value="{{ $role->id }}" selected="selected">{{ $role->name }}</option>
                    @else
                      <option value="{{ $role->id }}">{{ $role->name }}</option>

                    @endif
                  @endforeach
                </select>
              </div>
            </div>
            <div class="form-group row">
              <label class="col-xl-3 col-lg-3 col-form-label">{{ __('Device permission') }}</label>
              <div class="col-lg-9 col-xl-6">
                <div class="checkbox-inline">
                  @foreach ($devices as $key => $device)
                    <label class="checkbox">
                      <input type="checkbox" name="device_permissions[]" {{ $user->devices->contains('id', $device->id) ? 'checked' : '' }} value="{{$device->id}}" />
                      <span></span>{{ $device->name }}
                    </label>
                  @endforeach
                </div>
              </div>
            </div>
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
    <script src="{{ mix('js/pages/_misc/aside.js') }}" type="text/javascript"></script>
    <script src="{{ mix('js/pages/users/show.js') }}" type="text/javascript"></script>
    <script src="{{ mix('js/app.js') }}" type="text/javascript"></script>
@endsection
