@php
  $subheader_buttons = [
    (object)[
      'text' => __('Configuration'),
      'url' => route('supplies.index'),
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

  <div id="requestedCard" class="card card-custom gutter-b">
    <div class="card-header flex-wrap border-0 pt-6 pb-0">
      <div class="card-title">
        <h3 class="card-label">{{ __('Inventory Requests') }}
        <span class="d-block text-muted pt-2 font-size-sm">{{ __('Total active Requests') }}:
          <span class="current_requested_listings">{{ $open_supply_listings }}</span>
        </span></h3>
      </div>
      <div class="card-toolbar">
        <a href="#" id="generate_pdf" class="btn btn-warning font-weight-bold ml-2">
					<i class="flaticon2-document"></i>{{ __('Generate PDF') }}
        </a>
        <a href="#" class="btn btn-primary font-weight-bold ml-2" data-toggle="modal" data-target="#addSupplyListingModal">
					<i class="flaticon2-clip-symbol"></i>{{ __('New Request') }}
        </a>
			</div>
    </div>
    <div class="card-body">
      <!--begin: Search Form-->
      <!--begin::Search Form-->
      <div class="mb-7">
        <div class="row align-items-center">
          <div class="col-lg-10 col-xl-9">
            <div class="row align-items-center">
              <div class="col-md-3 my-2 my-md-0">
                <div class="input-icon">
                  <input type="text" class="form-control" placeholder="{{ __('Search...') }}" id="requested_supply_listing_datatable_search_query" />
                  <span>
                    <i class="flaticon2-search-1 text-muted"></i>
                  </span>
                </div>
              </div>
              <div class="col-md-3 my-2 my-md-0">
                <div class="">
                  <div class="form-group">
                    <label class="mr-3 mb-0 d-none d-md-block">{{ __('Device') }}:</label>
        						<select class="form-control" id="requested_supply_listing_datatable_search_device">
                      <option value="">{{ __('All') }}</option>
                      @foreach ($devices as $key => $device)
                        <option value="{{ $device->id }}">{{ $device->name }}</option>
                      @endforeach
                    </select>
        					</div>
                </div>
              </div>
              <div class="col-md-6 my-2 my-md-0">
                <div class="">
                  <div class="form-group">
                    <label class="mr-3 mb-0 d-none d-md-block">{{ __('Date') }}:</label>
        						<div class="input-group">
                      <div class="input-group-append">
                        <span class="input-group-text">
                          <i class="la la-calendar-check-o"></i>
                        </span>
                      </div>
                      <input type="text" id="requestedDaterangepicker" class="form-control" name="vacation" readonly="readonly" placeholder="{{ __('Select date range') }}">
        							<div class="input-group-append show">
                        <button type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">{{ __('Action') }}</button>
                        <div class="dropdown-menu">
                          <a class="dropdown-supply" id="clearRequestedDaterangepicker" href="#!">{{ __('Clear Date') }}</a>
                        </div>
        							</div>
        						</div>
        					</div>
                </div>
              </div>
            </div>
          </div>
          <div class="col-lg-2 col-xl-3 mt-5 mt-lg-0">
            <a href="#" class="btn btn-light-primary px-6 font-weight-bold">{{ __('Search') }}</a>
          </div>
        </div>
      </div>
      <!--end::Search Form-->
      <!--end: Search Form-->
      <!--begin: Datatable-->
      <div class="datatable datatable-bordered datatable-head-custom" id="requested_supply_listing_datatable"></div>
      <!--end: Datatable-->

    </div>
  </div>

  <div id="fixedCard" class="card card-custom gutter-b">
    <div class="card-header flex-wrap border-0 pt-6 pb-0">
      <div class="card-title">
        <h3 class="card-label">{{ __('Fulfilled Requests') }}
        <span class="d-block text-muted pt-2 font-size-sm">{{ __('Fixed') }}:
          <span class="current_fixed_listings">{{ $closed_supply_listings }}</span>
        </span></h3>
      </div>
      <div class="card-toolbar">

			</div>
    </div>
    <div class="card-body">
      <!--begin: Search Form-->
      <!--begin::Search Form-->
      <div class="mb-7">
        <div class="row align-items-center">
          <div class="col-lg-10 col-xl-9">
            <div class="row align-items-center">
              <div class="col-md-3 my-2 my-md-0">
                <div class="input-icon">
                  <input type="text" class="form-control" placeholder="{{ __('Search...') }}" id="fixed_supply_listing_datatable_search_query" />
                  <span>
                    <i class="flaticon2-search-1 text-muted"></i>
                  </span>
                </div>
              </div>
              <div class="col-md-3 my-2 my-md-0">
                <div class="">
                  <div class="form-group">
                    <label class="mr-3 mb-0 d-none d-md-block">{{ __('Device') }}:</label>
        						<select class="form-control" id="fixed_supply_listing_datatable_search_device">
                      <option value="">{{ __('All') }}</option>
                      @foreach ($devices as $key => $device)
                        <option value="{{ $device->id }}">{{ $device->name }}</option>
                      @endforeach
                    </select>
        					</div>
                </div>
              </div>
              <div class="col-md-6 my-2 my-md-0">
                <div class="">
                  <div class="form-group">
                    <label class="mr-3 mb-0 d-none d-md-block">{{ __('Date') }}:</label>
        						<div class="input-group">
                      <div class="input-group-append">
                        <span class="input-group-text">
                          <i class="la la-calendar-check-o"></i>
                        </span>
                      </div>
                      <input type="text" id="fixedDaterangepicker" class="form-control" name="vacation" readonly="readonly" placeholder="{{ __('Select date range') }}">
        							<div class="input-group-append show">
                        <button type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">{{ __('Action') }}</button>
                        <div class="dropdown-menu">
                          <a class="dropdown-supply" id="clearFixedDaterangepicker" href="#!">{{ __('Clear Date') }}</a>
                        </div>
        							</div>
        						</div>
        					</div>
                </div>
              </div>
            </div>
          </div>
          <div class="col-lg-2 col-xl-3 mt-5 mt-lg-0">
            <a href="#" class="btn btn-light-primary px-6 font-weight-bold">{{ __('Search') }}</a>
          </div>
        </div>
      </div>
      <!--end::Search Form-->
      <!--end: Search Form-->
      <!--begin: Datatable-->
      <div class="datatable datatable-bordered datatable-head-custom" id="fixed_supply_listing_datatable"></div>
      <!--end: Datatable-->

    </div>
  </div>

  <!-- Modal Add Listing-->
  <div class="modal fade" id="addSupplyListingModal" tabindex="-1" role="dialog" aria-labelledby="listingTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="listingTitle">{{ __('New Request') }}</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <i aria-hidden="true" class="ki ki-close"></i>
          </button>
        </div>
        <div class="modal-body">
          <form id="addSupplyListingForm" class="form" action="{{ route('supplies.store_listings') }}" method="post">
            @csrf
            <div class="form-group row">
              <div class="col-lg-6">
                <label for="listing_device" class="d-block">{{ __('Device') }}</label>
                <select data-placeholder="{{ __('Select a device') }}" class="form-control" id="listing_device" name="device">
                  <option></option>
                  @foreach ($devices as $device)
                  <option value="{{ $device->id }}">{{ $device->name }}</option>
                  @endforeach
                </select>
              </div>
              <div class="col-lg-6">
                <label for="listing_supply" class="d-block">{{ __('Inventory') }}</label>
                <select data-placeholder="{{ __('Select an Inventory') }}" class="form-control" id="listing_supply" name="supply">
                  @foreach ($supplies as $supply)
                    <option value="{{ $supply->id }}">{{ $supply->name }}</option>
                  @endforeach
                </select>
              </div>
            </div>

            <div class="form-group row">
              <div class="col-lg-12">
                <label for="listing_comment" class="d-block">{{ __('Comment') }}</label>
                <textarea id="listing_comment" class="form-control" name="comment" rows="4"></textarea>
              </div>
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-light-primary font-weight-bold" data-dismiss="modal">{{ __('Close') }}</button>
          <button type="submit" form="addSupplyListingForm" id="btnModalGo" class="btn btn-primary font-weight-bold">Go!</button>
      </div>
      </div>
    </div>
  </div>

@endsection

{{-- Styles Section --}}
@section('styles')

@endsection


{{-- Scripts Section --}}
@section('scripts')
    <script>
      console.log('asa');
      var listings_json_url = "{{ route('supplies.listings.ajax') }}";
      // var employee_json_url = "{{ route('employees.getAll') }}";
    </script>
    <!--begin::Global Config(global config for global JS scripts)-->
    <script>
      var KTAppSettings = { "breakpoints": { "sm": 576, "md": 768, "lg": 992, "xl": 1200, "xxl": 1400 }, "colors": { "theme": { "base": { "white": "#ffffff", "primary": "#3699FF", "secondary": "#E5EAEE", "success": "#1BC5BD", "info": "#8950FC", "warning": "#FFA800", "danger": "#F64E60", "light": "#E4E6EF", "dark": "#181C32" }, "light": { "white": "#ffffff", "primary": "#E1F0FF", "secondary": "#EBEDF3", "success": "#C9F7F5", "info": "#EEE5FF", "warning": "#FFF4DE", "danger": "#FFE2E5", "light": "#F3F6F9", "dark": "#D6D6E0" }, "inverse": { "white": "#ffffff", "primary": "#ffffff", "secondary": "#3F4254", "success": "#ffffff", "info": "#ffffff", "warning": "#ffffff", "danger": "#ffffff", "light": "#464E5F", "dark": "#ffffff" } }, "gray": { "gray-100": "#F3F6F9", "gray-200": "#EBEDF3", "gray-300": "#E4E6EF", "gray-400": "#D1D3E0", "gray-500": "#B5B5C3", "gray-600": "#7E8299", "gray-700": "#5E6278", "gray-800": "#3F4254", "gray-900": "#181C32" } }, "font-family": "Poppins" };
    </script>
    <!--end::Global Config-->

    {{-- vendors --}}

    {{-- page scripts --}}
    <script src="{{ mix('js/pages/supplies/listings.js') }}" type="text/javascript"></script>
    <script src="{{ mix('js/app.js') }}" type="text/javascript"></script>
@endsection
