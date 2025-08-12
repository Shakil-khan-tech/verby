@php
  $subheader_buttons = [
    (object)[
      'text' => __('Add Device'),
      'url' => route('devices.create'),
    ],
  ];
@endphp
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

  <!--begin::Entry-->
  <div class="d-flex flex-column-fluid">
    <!--begin::Container-->
    <div class="container">
      <!--begin::Row-->
      <div class="row">
        @foreach ($devices as $key => $device)
        <div class="col-xl-4">
            <div class="card card-custom gutter-b card-stretch">
                <div class="card-body pb-0">
                    <!-- Device Info -->
                    <div class="d-flex align-items-center">
                        <div class="d-flex flex-column mr-auto">
                            <h2 class="d-flex align-items-center text-dark text-hover-primary font-size-h2 font-weight-bold mr-3">
                                {{ $device->name }}
                                <i class="flaticon2-correct text-success icon-md ml-2"></i>
                            </h2>
                            <span class="text-muted font-weight-bold">ID: {{ $device->id }}</span>
                        </div>
                        <div class="card-toolbar mb-auto">
                            <div class="dropdown dropdown-inline" data-toggle="tooltip" title="{{ __('Quick actions') }}">
                                <a href="#" class="btn btn-clean btn-hover-light-primary btn-sm btn-icon" data-toggle="dropdown">
                                    <i class="ki ki-bold-more-hor"></i>
                                </a>
                                <div class="dropdown-menu dropdown-menu-sm dropdown-menu-right">
                                    <ul class="navi navi-hover">
                                        <li class="navi-header pb-1">
                                            <span class="text-primary text-uppercase font-weight-bold font-size-sm">{{ __('Options') }}:</span>
                                        </li>
                                        <li class="navi-item">
                                            <a href="{{ route('devices.show', $device->id) }}" class="navi-link">
                                                <span class="navi-icon"><i class="flaticon-eye"></i></span>
                                                <span class="navi-text">{{ __('View') }}</span>
                                            </a>
                                        </li>
                                        <li class="navi-item">
                                            <a href="{{ route('devices.edit', $device->id) }}" class="navi-link">
                                                <span class="navi-icon"><i class="flaticon-edit-1"></i></span>
                                                <span class="navi-text">{{ __('Edit') }}</span>
                                            </a>
                                        </li>
                                        <li class="navi-item">
                                            <form method="POST" action="{{ route('devices.destroy', $device->id) }}">
                                                @csrf
                                                @method('DELETE')
                                                <div class="navi-link">
                                                    <span class="navi-icon"><i class="flaticon-delete"></i></span>
                                                    <span class="navi-text text-danger">
                                                        <input type="submit" class="bg-transparent border-0 text-danger"
                                                              onclick="return confirm('{{ __('Deleting the device will delete all employees in it! Are you sure?') }}')"
                                                              value="{{ __('Delete') }}">
                                                    </span>
                                                </div>
                                            </form>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Last Activity + Progress -->
                    <div class="d-flex mt-4">
                        <div class="mr-12 d-flex flex-column mb-7">
                            <span class="d-block font-weight-bold mb-4">{{ __('Last involved') }}</span>
                            <span class="btn btn-light-primary btn-sm font-weight-bold btn-upper btn-text">
                                {{ optional($last_activities->where('id', $device->id)->first())->updated_at ?? __('no action') }}
                            </span>
                        </div>
                        <div class="flex-row-fluid mb-7">
                            <span class="d-block font-weight-bold mb-4">{{ __('% of total') }}</span>
                            <div class="d-flex align-items-center pt-2">
                                <div class="progress progress-xs mt-2 mb-2 w-100">
                                    <div class="progress-bar bg-warning" role="progressbar"
                                        style="width: {{ round($device->validEmployees->count() / max($total_employees, 1) * 100, 2) }}%;">
                                    </div>
                                </div>
                                <span class="ml-3 font-weight-bolder">
                                    {{ round($device->validEmployees->count() / max($total_employees, 1) * 100, 2) }}%
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Device Stats -->
                    <div class="d-flex flex-wrap gap-2">
                        <div class="d-flex flex-column flex-fill mb-7">
                            <span class="font-weight-bolder mb-4">{{ __('Depa') }}</span>
                            <span class="font-weight-bolder font-size-h5 pt-1">{{ $device->depa }} <span class="font-weight-bold text-dark-50">min</span></span>
                        </div>
                        <div class="d-flex flex-column flex-fill mb-7">
                            <span class="font-weight-bolder mb-4">{{ __('Restant') }}</span>
                            <span class="font-weight-bolder font-size-h5 pt-1">{{ $device->restant }} <span class="font-weight-bold text-dark-50">min</span></span>
                        </div>
                        <div class="d-flex flex-column flex-fill mb-7">
                            <span class="font-weight-bolder mb-4">{{ __('Total Employees') }}</span>
                            <span class="font-weight-bolder label label-xl label-light-primary label-inline px-3 py-5 min-w-45px">
                                {{ $device->validEmployees->count() }}
                            </span>
                        </div>
                    </div>

                    <!-- Employee Avatars -->
                    <div class="hidden flex-wrap sm:flex">
                        <div class="d-flex flex-column flex-lg-fill float-left mb-7">
                            <span class="font-weight-bolder mb-4">{{ __('Managers') }}</span>
                            <div class="symbol-group symbol-hover">
                                @foreach ($device->validEmployees->where('function', 0)->take(5) as $manager)
                                    <div class="symbol symbol-30 symbol-circle" data-toggle="tooltip" title="{{ $manager->fullname }}">
                                        <img src="{{ asset('media/users/default.jpg') }}" alt="Manager">
                                    </div>
                                @endforeach
                                @if ($device->validEmployees->where('function', 0)->count() > 5)
                                    <div class="symbol symbol-30 symbol-circle symbol-light">
                                        <span class="symbol-label font-weight-bold">
                                            {{ $device->validEmployees->where('function', 0)->count() - 5 }}+
                                        </span>
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="d-flex flex-column flex-lg-fill float-left mb-7">
                            <span class="font-weight-bolder mb-4">{{ __('Employees') }}</span>
                            <div class="symbol-group symbol-hover">
                                @foreach ($device->validEmployees->whereNotIn('function', [0,6])->take(5) as $employee)
                                    <div class="symbol symbol-30 symbol-circle" data-toggle="tooltip" title="{{ $employee->fullname }}">
                                        <img src="{{ asset('media/users/default.jpg') }}" alt="Employee">
                                    </div>
                                @endforeach
                                @if ($device->validEmployees->whereNotIn('function', [0,6])->count() > 5)
                                    <div class="symbol symbol-30 symbol-circle symbol-light">
                                        <span class="symbol-label font-weight-bold">
                                            {{ $device->validEmployees->whereNotIn('function', [0,6])->count() - 5 }}+
                                        </span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card Footer -->
                <div class="card-footer d-flex align-items-center py-5">
                    <a href="{{ route('calendars.show', $device->id) }}" class="btn btn-block btn-sm btn-light-success font-weight-bolder text-uppercase py-4">
                        {{ __('View Calendar') }}
                    </a>
                </div>
            </div>
        </div>
        @endforeach
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
