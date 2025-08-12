@php
  $subheader_buttons = [
    (object)[
      'text' => __('Edit Device'),
      'color' => 'primary',
      'url' => route('devices.edit', $device->id),
    ],
    (object)[
      'text' => __('Authentication'),
      'color' => 'success',
      'url' => route('devices.auth', $device->id),
    ],
    (object)[
      'text' => __('Reports'),
      'color' => 'info',
      'url' => route('devices.report', $device->id),
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

  <!--begin::Entry-->
  <div class="d-flex flex-column-fluid">
    <!--begin::Container-->
    <div class="container-fluid">
      <!--begin::Card-->
      <div class="card card-custom gutter-b">
        <div class="card-body">
          <div class="d-flex">
            <!--begin: Info-->
            <div class="flex-grow-1">
              <!--begin: Title-->
              <div class="d-flex align-items-center justify-content-between flex-wrap">
                <div class="mr-3">
                  <!--begin::Name-->
                  <h2 class="d-flex align-items-center text-dark text-hover-primary font-size-h2 font-weight-bold mr-3">{{ $device->name }}
                    <i class="flaticon2-correct text-success icon-md ml-2"></i>
                  </h2>
                  <!--end::Name-->
                  <!--begin::Contacts-->
                  <div class="d-flex flex-wrap my-2">
                    <span class="text-muted font-weight-bold">{{ __('ID') }}: {{ $device->id }}</span>
                  </div>
                  <!--end::Contacts-->
                </div>
                <div class="my-lg-0 my-1">
                  <div class="d-flex flex-wrap align-items-center py-2">
                    <div class="d-flex align-items-center mr-10">
                      <div class="mr-6">
                        <div class="font-weight-bold mb-2">{{ __('Last involved') }}</div>
                        <span class="btn btn-sm btn-text btn-light-primary text-uppercase font-weight-bold">{{ $last_activity->updated_at ? $last_activity->updated_at : __('no action') }}</span>
                      </div>
                    </div>
                    <div class="flex-grow-1 flex-shrink-0 w-150px w-xl-300px mt-4 mt-sm-0">
                      <span class="font-weight-bold">{{ __('% of total employees') }}</span>
                      <div class="progress progress-xs mt-2 mb-2">
                        <div class="progress-bar bg-success" role="progressbar" style="width: {{ round( $device->employees->count() / $total_employees * 100, 2 ) }}%;" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100"></div>
                      </div>
                      <span class="font-weight-bolder text-dark">{{ round( $device->employees->count() / $total_employees * 100, 2 ) }}%</span>
                    </div>
                  </div>
                </div>
              </div>
              <!--end: Title-->
            </div>
            <!--end: Info-->
          </div>
          <div class="separator separator-solid my-7"></div>
          <!--begin: Items-->
          <div class="d-flex align-items-center flex-wrap">
            <!--begin: Item-->
            <div class="d-flex align-items-center flex-lg-fill mr-5 my-1">
              <span class="mr-4">
                <i class="flaticon-users icon-2x text-muted font-weight-bold"></i>
              </span>
              <div class="d-flex flex-column text-dark-75">
                <span class="font-weight-bolder font-size-sm">{{ __('Total Employees') }}</span>
                <span class="font-weight-bolder font-size-h5">{{ $device->employees->count() }}</span>
              </div>
            </div>
            <!--end: Item-->
            <!--begin: Item-->
            <div class="d-flex align-items-center flex-lg-fill mr-5 my-1">
              <span class="mr-4">
                <i class="flaticon-network icon-2x text-muted font-weight-bold"></i>
              </span>
              <div class="d-flex flex-column text-dark-75">
                <span class="font-weight-bolder font-size-sm">{{ __('Active Employees') }}</span>
                <span class="font-weight-bolder font-size-h5">{{ $active_employees }}</span>
              </div>
            </div>
            <!--end: Item-->
            <!--begin: Item-->
            <div class="d-flex align-items-center flex-lg-fill mr-5 my-1">
              <span class="mr-4">
                <i class="flaticon-mail icon-2x text-muted font-weight-bold"></i>
              </span>
              <div class="d-flex flex-column text-dark-75">
                <span class="font-weight-bolder font-size-sm">{{ __('Hotel email') }}</span>
                <span class="font-weight-bolder font-size-h5">{{ $device->hotel_email }}</span>
              </div>
            </div>
            <!--end: Item-->
            <!--begin: Item-->
            <div class="d-flex align-items-center flex-lg-fill mr-5 my-1">
              <span class="mr-4">
                <i class="flaticon-mail icon-2x text-muted font-weight-bold"></i>
              </span>
              <div class="d-flex flex-column text-dark-75">
                <span class="font-weight-bolder font-size-sm">{{ __('Hotel Technician email') }}</span>
                <span class="font-weight-bolder font-size-h5">{{ $device->hotel_technician_email }}</span>
              </div>
            </div>
            <!--end: Item-->
          </div>
          <!--begin: Items-->
        </div>
      </div>
      <!--end::Card-->

      <!--begin::Row-->
      <div class="row">
        <div class="col-lg-12">
          <!--begin::Advance Table Widget 3-->
          <div class="card card-custom card-stretch gutter-b">
            <!--begin::Header-->
            <div class="card-header border-0 py-5">
              <div class="card-title">
                <h3 class="card-title">
                  <span class="card-label font-weight-bolder text-dark">
                    {{ __('Rooms/Spaces') }} ({{ $device->rooms->count() }})
                  </span>
                </h3>
              </div>
              <div class="card-toolbar">
                @if ( $device->rooms->count() > 0 )
                  @foreach (Config::get('constants.room_categories') as $key => $category)
                    <span class="label label-sm label-rounded label-inline label-{{ Config::get('constants.colors')[$key] }} mr-2">{{ $category }}</span>
                  @endforeach
                @endif
              </div>

            </div>
            <!--end::Header-->
            <!--begin::Body-->
            <div class="card-body pt-0 pb-3">
              <!--begin::Table-->

              <div class="scroll scroll-pull position-relative" data-scroll="true" data-wheel-propagation="true" style="max-height: 500px">
                <div class="rooms grid gap-2 grid-cols-6 md:grid-cols-12 auto-rows-auto">
                  @foreach ($device->rooms as $key => $room)
                    <span
                      class="label label-xl label-light-{{Config::get('constants.colors')[$room->category]}} label-pill label-inline"
                      data-toggle="tooltip" title="{{__('Depa')}}: {{$room->depa_minutes}} <br>{{__('Restant')}}: {{$room->restant_minutes}}" data-html="true">
                      {{ $room->name }}
                    </span>
                  @endforeach
                </div>
              </div>
              <!--end::Table-->
            </div>
            <!--end::Body-->
          </div>
          <!--end::Advance Table Widget 3-->
        </div>
      </div>
      <!--end::Row-->
      <!--begin::Row-->
      <div class="row">
        <div class="col-lg-6">
          <!--begin::Advance Table Widget 3-->
          <div class="card card-custom card-stretch gutter-b">
            <!--begin::Header-->
            <div class="card-header border-0 py-5">
              <h3 class="card-title align-items-start flex-column">
                <span class="card-label font-weight-bolder text-dark">{{ __('Managers') }} ({{ $device->employees->where('function', 0)->count() }})</span>
                <span class="text-muted mt-3 font-weight-bold font-size-sm">{{ Config::get('constants.functions')[0] }}</span>
              </h3>
            </div>
            <!--end::Header-->
            <!--begin::Body-->
            <div class="card-body pt-0 pb-3" style="height: 1000px">
              <!--begin::Table-->
              <div class="scroll scroll-pull position-relative" data-scroll="true" data-wheel-propagation="true" style="height: 1000px">
                <div class="table-responsive">
                  <table class="table table-head-custom table-head-bg table-borderless table-vertical-center">
                    <thead>
                      <tr class="text-uppercase">
                        <th style="min-width: 250px" class="pl-7">
                          <span class="text-dark-75">{{ __('Employee') }}</span>
                        </th>
                        <th style="min-width: 100px">{{ __('Canton') }}</th>
                        <th style="min-width: 100px">{{ __('Function') }}</th>
                        <th style="min-width: 120px" class="text-right">{{ __('Action') }}</th>
                      </tr>
                    </thead>
                    <tbody>
                      @foreach ($device->employees->where('function', 0) as $manager)
                        <tr>
                          <td class="pl-0 py-1">
                            <div class="d-flex align-items-center">
                              <div class="symbol symbol-40 symbol-success flex-shrink-0">
                                <div class="symbol-label">{{ $manager->id }}</div>
                              </div>
                              <div class="ml-2">
                                <a href="{{ route('employees.show', $manager->id) }}">
                                  <div class="text-dark-75 font-weight-bold line-height-sm">{{ $manager->fullname }}</div>
                                  <span class="font-size-sm text-dark-50 text-hover-primary">{{ Config::get('constants.functions')[ $manager->function ] }}<span>
                                </span></span></a>
                              </div>
                            </div>
                          </td>
                          <td>
                            <span class="text-dark-75 font-weight-bolder d-block font-size-lg">{{ $manager->ORT }}</span>
                            <span class="text-muted font-weight-bold">{{ $manager->ORT1 }}</span>
                          </td>
                          <td>
                            <span class="label label-lg label-light-primary label-inline">{{ Config::get('constants.functions')[ $manager->function ] }}</span>
                          </td>
                          <td class="text-right pr-0">
                            <a href="{{ route('employees.show', $manager->id) }}" class="btn btn-icon btn-light btn-hover-primary btn-sm">
                              <span class="svg-icon svg-icon-md svg-icon-primary">
                                <!--begin::Svg Icon | path:assets/media/svg/icons/Navigation/Arrow-right.svg-->
                                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                  <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                    <polygon points="0 0 24 0 24 24 0 24" />
                                    <rect fill="#000000" opacity="0.3" transform="translate(12.000000, 12.000000) rotate(-90.000000) translate(-12.000000, -12.000000)" x="11" y="5" width="2" height="14" rx="1" />
                                    <path d="M9.70710318,15.7071045 C9.31657888,16.0976288 8.68341391,16.0976288 8.29288961,15.7071045 C7.90236532,15.3165802 7.90236532,14.6834152 8.29288961,14.2928909 L14.2928896,8.29289093 C14.6714686,7.914312 15.281055,7.90106637 15.675721,8.26284357 L21.675721,13.7628436 C22.08284,14.136036 22.1103429,14.7686034 21.7371505,15.1757223 C21.3639581,15.5828413 20.7313908,15.6103443 20.3242718,15.2371519 L15.0300721,10.3841355 L9.70710318,15.7071045 Z" fill="#000000" fill-rule="nonzero" transform="translate(14.999999, 11.999997) scale(1, -1) rotate(90.000000) translate(-14.999999, -11.999997)" />
                                  </g>
                                </svg>
                                <!--end::Svg Icon-->
                              </span>
                            </a>
                          </td>
                        </tr>
                      @endforeach
                    </tbody>
                  </table>
                </div>
              </div>
              <!--end::Table-->
            </div>
            <!--end::Body-->
          </div>
          <!--end::Advance Table Widget 3-->
        </div>

        <div class="col-lg-6">
          <!--begin::Advance Table Widget 3-->
          <div class="card card-custom card-stretch gutter-b">
            <!--begin::Header-->
            <div class="card-header border-0 py-5">
              <h3 class="card-title align-items-start flex-column">
                <span class="card-label font-weight-bolder text-dark">{{ __('Employees') }} ({{ $device->employees->whereNotIn('function', 0)->count() }})</span>
                <span class="text-muted mt-3 font-weight-bold font-size-sm">{{ implode(', ', array_slice( Config::get('constants.functions'), 1) ) }}</span>
              </h3>
            </div>
            <!--end::Header-->
            <!--begin::Body-->
            <div class="card-body pt-0 pb-3">
              <!--begin::Table-->
              <div class="scroll scroll-pull position-relative" data-scroll="true" data-wheel-propagation="true" style="height: 1000px">
                <div class="table-responsive">
                <table class="table table-head-custom table-head-bg table-borderless table-vertical-center">
                    <thead>
                      <tr class="text-uppercase">
                        <th style="min-width: 250px" class="pl-7">
                          <span class="text-dark-75">{{ __('Employee') }}</span>
                        </th>
                        <th style="min-width: 100px">{{ __('Canton') }}</th>
                        <th style="min-width: 100px">{{ __('Function') }}</th>
                        <th style="min-width: 120px" class="text-right">{{ __('Action') }}</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php 
                      //echo "<pre>";print_r($device->employees->whereNotIn('function', [0])->toArray());
                        //exit;
                        ?>
                      @foreach ($device->employees->whereNotIn('function', [0, 6]) as $employee)
                      
                        <tr>
                          <td class="pl-0 py-1">
                            <div class="d-flex align-items-center">
                              <div class="symbol symbol-40 symbol-success flex-shrink-0">
                                <div class="symbol-label">{{ $employee->id }}</div>
                              </div>
                              <div class="ml-2">
                                <a href="{{ route('employees.show', $employee->id) }}">
                                  <div class="text-dark-75 font-weight-bold line-height-sm">{{ $employee->fullname }}</div>
                                  <span class="font-size-sm text-dark-50 text-hover-primary">{{ Config::get('constants.functions')[ $employee->function ] }}<span>
                                </span></span></a>
                              </div>
                            </div>                            
                          </td>
                          <td>
                            <span class="text-dark-75 font-weight-bolder d-block font-size-lg">{{ $employee->ORT }}</span>
                            <span class="text-muted font-weight-bold">{{ $employee->ORT1 }}</span>
                          </td>
                          <td>
                            <span class="label label-lg label-light-{{ Config::get('constants.funktion_colors')[$employee->function] }} label-inline">{{ Config::get('constants.functions')[ $employee->function ] }}</span>
                          </td>
                          <td class="text-right pr-0">
                            <a href="{{ route('employees.show', $employee->id) }}" class="btn btn-icon btn-light btn-hover-primary btn-sm">
                              <span class="svg-icon svg-icon-md svg-icon-primary">
                                <!--begin::Svg Icon | path:assets/media/svg/icons/Navigation/Arrow-right.svg-->
                                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                  <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                    <polygon points="0 0 24 0 24 24 0 24" />
                                    <rect fill="#000000" opacity="0.3" transform="translate(12.000000, 12.000000) rotate(-90.000000) translate(-12.000000, -12.000000)" x="11" y="5" width="2" height="14" rx="1" />
                                    <path d="M9.70710318,15.7071045 C9.31657888,16.0976288 8.68341391,16.0976288 8.29288961,15.7071045 C7.90236532,15.3165802 7.90236532,14.6834152 8.29288961,14.2928909 L14.2928896,8.29289093 C14.6714686,7.914312 15.281055,7.90106637 15.675721,8.26284357 L21.675721,13.7628436 C22.08284,14.136036 22.1103429,14.7686034 21.7371505,15.1757223 C21.3639581,15.5828413 20.7313908,15.6103443 20.3242718,15.2371519 L15.0300721,10.3841355 L9.70710318,15.7071045 Z" fill="#000000" fill-rule="nonzero" transform="translate(14.999999, 11.999997) scale(1, -1) rotate(90.000000) translate(-14.999999, -11.999997)" />
                                  </g>
                                </svg>
                                <!--end::Svg Icon-->
                              </span>
                            </a>
                          </td>
                        </tr>
                      @endforeach
                    </tbody>
                  </table>
              </div>
              </div>
              <!--end::Table-->
            </div>
            <!--end::Body-->
          </div>
          <!--end::Advance Table Widget 3-->
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
