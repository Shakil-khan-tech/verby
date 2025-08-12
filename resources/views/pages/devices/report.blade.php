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
    @include('pages.widgets._widget-device_aside', ['item_active' => $item_active])
    <!--begin::Content-->
    <div class="flex-row-fluid ml-lg-8">
      <!--begin::Card-->
      <div class="card card-custom card-stretch">
        <!--begin::Header-->
        <div class="card-header py-3">
          <div class="card-title align-items-start flex-column">
            <div class="card-label">
              <h3>{{ __('Report for') }} <b>{{ $device->name }}</b> {{ __('for period') }}:
                <b id="date_report_time_from">{{ date('Y-m-d') }}</b>
                {{ __('to') }}
                <b id="date_report_time_to">{{ date('Y-m-d') }}</b>
              </h3>
              <div class="d-flex flex-wrap my-2">
                <span class="text-muted font-weight-bold mr-2">{{ __('Total in this page') }}: </span>
                <span id="total_report_time" class="text-muted font-weight-bold spinner"></span>
              </div>
            </div>
          </div>
        </div>
        <!--end::Header-->
        <!--begin::Body-->
        <div class="card-body">
          <!--begin: Search Form-->
          <!--begin::Search Form-->
          <div class="mb-7">
            <div class="row align-items-center">
              <div class="col-lg-9 col-xl-10">
                <div class="row align-items-center">
                  <div class="col-md-4 my-2 my-md-0">
                    <div class="input-icon">
                      <input type="text" class="form-control" placeholder="{{ __('Search') }}..." id="device_report_datatable_search_query" />
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
                          <input type="text" id="recordsDaterangepicker" class="form-control" name="vacation" readonly="readonly" placeholder="{{ __('Select date range') }}">
                          <div class="input-group-append show">
                            <button type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">{{ __('Action') }}</button>
                            <div class="dropdown-menu">
                              <a class="dropdown-item" id="clearRecordsDaterangepicker" href="#!">{{ __('Clear Date') }}</a>
                            </div>
                          </div>
                        </div>
                      </div>
    
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-lg-3 col-xl-2 mt-5 mt-lg-0">
                <a href="#" class="btn btn-light-primary px-6 font-weight-bold">{{ __('Search') }}</a>
              </div>
            </div>
          </div>
          <!--end: Search Form-->
          <!--begin: Datatable-->
          <div class="datatable datatable-bordered datatable-head-custom" id="device_report_datatable"></div>
          <!--end: Datatable-->    
        </div>
        <!--end::Body-->
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
    <script>
      var device_report_json_url = "{{ route('devices.report_ajax', $device->id) }}";
    </script>
    {{-- vendors --}}

    {{-- page scripts --}}
    <script src="{{ mix('js/pages/devices/report.js') }}" type="text/javascript"></script>
    <script src="{{ mix('js/app.js') }}" type="text/javascript"></script>
@endsection
