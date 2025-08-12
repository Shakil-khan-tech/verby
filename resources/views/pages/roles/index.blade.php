@php
  $subheader_buttons = [
    (object)[
      'text' => __('Add Access Management'),
      'url' => route('roles.create'),
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


  <div class="row my-10 px-8 my-lg-15 px-lg-10">
    @foreach ($roles as $role)
        @continue($role->name == 'super_admin' && !$authenticated_user->hasRole('super_admin'))
  	<!--begin::Col-->
  	<div class="col-xl-4 col-lg-6 col-md-6 col-sm-6">
  		<!--begin::Card-->
  		<div class="card card-custom gutter-b card-stretch">
  			<!--begin::Body-->
  			<div class="card-body pt-4">
  				<!--begin::Toolbar-->
  				<div class="d-flex align-items-center">
  					<!--begin::Info-->
  					<div class="d-flex flex-column mr-auto">
  						<!--begin: Title-->
  						<a href="#" class="card-title text-hover-primary font-weight-bolder font-size-h5 text-dark mb-1">{{ $role->name }}</a>

  						<!--end::Title-->
  					</div>
  					<!--end::Info-->
            @if ($role->name != 'super_admin')
  					<!--begin::Toolbar-->
  					<div class="card-toolbar mb-auto">
  						<div class="dropdown dropdown-inline" data-toggle="tooltip" title="" data-placement="left" data-original-title="{{ __('Quick actions') }}">
  							<a href="#" class="btn btn-clean btn-hover-light-primary btn-sm btn-icon" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
  								<i class="ki ki-bold-more-hor"></i>
  							</a>
  							<div class="dropdown-menu dropdown-menu-sm dropdown-menu-right" style="">
  								<!--begin::Navigation-->
                  <ul class="navi navi-hover">
                    <li class="navi-header pb-1">
                      <span class="text-primary text-uppercase font-weight-bold font-size-sm">{{ __('Options') }}:</span>
                    </li>
                    <li class="navi-item">
                      <a href="{{ route('roles.edit', $role->id) }}" class="navi-link">
                        <span class="navi-icon">
                          <i class="flaticon-edit-1"></i>
                        </span>
                        <span class="navi-text">{{ __('Edit') }}</span>
                      </a>
                    </li>
                    @if ($role->name != 'admin')

                    <li class="navi-item">
                      <form method="POST" action="{{ route('roles.destroy', $role->id) }}">
                          {{ csrf_field() }}
                          {{ method_field('DELETE') }}
                          <div class="navi-link">
                            <span class="navi-icon">
                              <i class="flaticon-delete"></i>
                            </span>
                            <span class="navi-text text-danger"><input type="submit" class="bg-transparent" onclick="return confirm('{{ __('Are you sure?') }}')" value="{{ __('Delete') }}"></span>
                          </div>
                      </form>
                    </li>
                    @endif
                  </ul>
  								<!--end::Navigation-->
  							</div>
  						</div>
  					</div>
  					<!--end::Toolbar-->
            @endif
  				</div>
  				<!--end::Toolbar-->
  				<!--begin::User-->
  				<div class="mb-7">
						<div class="symbol-group symbol-hover">
              @foreach ($role->users->take(5) as $user)
                <div class="symbol symbol-30 symbol-circle" data-toggle="tooltip" title="{{ $user->name }}">
                  <img alt="Pic" src="{{ asset($user->avatar_path) }}" />
                </div>
              @endforeach
              @if ( $role->users->count() > 5 )
                <div class="symbol symbol-30 symbol-circle symbol-light">
                  <span class="symbol-label font-weight-bold">{{ $role->users->count() - 5 }}+</span>
                </div>
              @endif
						</div>
					</div>
  				<!--end::User-->
  				<!--begin::Desc-->

  				<!--end::Desc-->
  				<!--begin::Info-->
  				<div class="mb-7">
            @foreach ($permissions as $permission)
              <div class="d-flex justify-content-between align-items-center border-bottom">
                <span class="text-dark-75 font-weight-bolder mr-2">{{ __($permission->nice_name) }}:</span>
                @if ( $role->hasPermissionTo($permission->name) )
                  <span class="label label-dot label-success"></span>
                @else
                  <span class="label label-dot label-danger"></span>
                @endif
              </div>
            @endforeach
  				</div>
  				<!--end::Info-->
  				<a href="{{ route('roles.edit', $role->id) }}" class="btn btn-block btn-sm btn-light-success font-weight-bolder text-uppercase py-4">{{ __('Edit Access Management') }}</a>
  			</div>
  			<!--end::Body-->
  		</div>
  		<!--end:: Card-->
  	</div>
  	<!--end::Col-->
    @endforeach
  </div>

@endsection

{{-- Styles Section --}}
@section('styles')

@endsection


{{-- Scripts Section --}}
@section('scripts')
    <script>

    </script>
    <!--begin::Global Config(global config for global JS scripts)-->
    <script>
      var KTAppSettings = { "breakpoints": { "sm": 576, "md": 768, "lg": 992, "xl": 1200, "xxl": 1400 }, "colors": { "theme": { "base": { "white": "#ffffff", "primary": "#3699FF", "secondary": "#E5EAEE", "success": "#1BC5BD", "info": "#8950FC", "warning": "#FFA800", "danger": "#F64E60", "light": "#E4E6EF", "dark": "#181C32" }, "light": { "white": "#ffffff", "primary": "#E1F0FF", "secondary": "#EBEDF3", "success": "#C9F7F5", "info": "#EEE5FF", "warning": "#FFF4DE", "danger": "#FFE2E5", "light": "#F3F6F9", "dark": "#D6D6E0" }, "inverse": { "white": "#ffffff", "primary": "#ffffff", "secondary": "#3F4254", "success": "#ffffff", "info": "#ffffff", "warning": "#ffffff", "danger": "#ffffff", "light": "#464E5F", "dark": "#ffffff" } }, "gray": { "gray-100": "#F3F6F9", "gray-200": "#EBEDF3", "gray-300": "#E4E6EF", "gray-400": "#D1D3E0", "gray-500": "#B5B5C3", "gray-600": "#7E8299", "gray-700": "#5E6278", "gray-800": "#3F4254", "gray-900": "#181C32" } }, "font-family": "Poppins" };
    </script>
    <!--end::Global Config-->

    {{-- vendors --}}

    {{-- page scripts --}}
    <script src="{{ mix('js/app.js') }}" type="text/javascript"></script>
@endsection
