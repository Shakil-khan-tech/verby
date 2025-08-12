@php
  $subheader_buttons = [
    (object)[
      'text' => __('Configuration'),
      'url' => route('issues.index'),
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
        <h3 class="card-label">{{ __('Issue Requests') }}
        <span class="d-block text-muted pt-2 font-size-sm">{{ __('Total active Requests') }}:
          <span class="current_requested_listings">{{ $open_issue_listings }}</span>
        </span></h3>
      </div>
      <div class="card-toolbar">
        <a href="#" id="generate_pdf" class="btn btn-warning font-weight-bold ml-2">
					<i class="flaticon2-document"></i>{{ __('Generate PDF') }}
        </a>
        <a href="#" class="btn btn-primary font-weight-bold ml-2" data-toggle="modal" data-target="#addIssueListingModal">
					<i class="flaticon2-clip-symbol"></i>{{ __('New Issue') }}
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
                  <input type="text" class="form-control" placeholder="{{ __('Search...') }}" id="requested_issue_listing_datatable_search_query" />
                  <span>
                    <i class="flaticon2-search-1 text-muted"></i>
                  </span>
                </div>
              </div>
              <div class="col-md-3 my-2 my-md-0">
                <div class="">
                  <div class="form-group">
                    <label class="mr-3 mb-0 d-none d-md-block">{{ __('Device') }}:</label>
        						<select class="form-control" id="requested_issue_listing_datatable_search_device">
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
                      <input type="text" id="requestedDaterangepicker" class="form-control" name="issue" readonly="readonly" placeholder="{{ __('Select date range') }}">
        							<div class="input-group-append show">
                        <button type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">{{ __('Action') }}</button>
                        <div class="dropdown-menu">
                          <a class="dropdown-item" id="clearRequestedDaterangepicker" href="#!">{{ __('Clear Date') }}</a>
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
      <div class="datatable datatable-bordered datatable-head-custom" id="requested_issue_listing_datatable"></div>
      <!--end: Datatable-->

    </div>
  </div>

  <div id="fixedCard" class="card card-custom gutter-b">
    <div class="card-header flex-wrap border-0 pt-6 pb-0">
      <div class="card-title">
        <h3 class="card-label">{{ __('Resolved Issues') }}
        <span class="d-block text-muted pt-2 font-size-sm">{{ __('Total Fixed') }}:
          <span class="current_fixed_listings">{{ $closed_issue_listings }}</span>
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
                  <input type="text" class="form-control" placeholder="{{ __('Search...') }}" id="fixed_issue_listing_datatable_search_query" />
                  <span>
                    <i class="flaticon2-search-1 text-muted"></i>
                  </span>
                </div>
              </div>
              <div class="col-md-3 my-2 my-md-0">
                <div class="">
                  <div class="form-group">
                    <label class="mr-3 mb-0 d-none d-md-block">{{ __('Device') }}:</label>
        						<select class="form-control" id="fixed_issue_listing_datatable_search_device">
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
                      <input type="text" id="fixedDaterangepicker" class="form-control" name="issue" readonly="readonly" placeholder="{{ __('Select date range') }}">
        							<div class="input-group-append show">
                        <button type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">{{ __('Action') }}</button>
                        <div class="dropdown-menu">
                          <a class="dropdown-item" id="clearFixedDaterangepicker" href="#!">{{ __('Clear Date') }}</a>
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
      <div class="datatable datatable-bordered datatable-head-custom" id="fixed_issue_listing_datatable"></div>
      <!--end: Datatable-->

    </div>
  </div>

  <!-- Modal Add Listing-->
  <div class="modal fade" id="addIssueListingModal" tabindex="-1" role="dialog" aria-labelledby="listingTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="listingTitle">{{ __('Add Issue') }}</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <i aria-hidden="true" class="ki ki-close"></i>
          </button>
        </div>
        <div class="modal-body">
          <form id="addIssueListingForm" class="form" action="{{ route('issues.store_listings') }}" method="post">
            @csrf
            <div class="form-group row">

              {{-- <div class="col-lg-4">
                <label for="listing_room" class="d-block">{{ __('Room') }}</label>
                <select data-placeholder="{{ __('Select a room') }}" class="form-control" id="listing_room" name="room">
                  @foreach ($hotel_rooms as $hotel)
                  <optgroup label="{{ $hotel->name }}">
                    @foreach ($hotel->rooms as $room)
                    <option value="{{ $room->id }}">{{ $room->name }}</option>
                    @endforeach
                  </optgroup>
                  @endforeach
                </select>
              </div> --}}

              <div class="col-lg-3">
                <label for="hotel" class="d-block">{{ __('Hotel') }}</label>
                <select data-placeholder="{{ __('Select a hotel') }}" class="form-control" id="hotel" name="hotel">
                  <option></option>
                  @foreach ($hotel_rooms as $hotel)
                  <option value="{{ $hotel->id }}">{{ $hotel->name }}</option>
                  @endforeach
                </select>
              </div>

              <div class="col-lg-3">
                <label for="listing_room" class="d-block">{{ __('Room') }}</label>
                <select data-placeholder="{{ __('Select a room') }}" class="form-control" id="listing_room" name="room">
                  {{-- @foreach ($hotel_rooms->first()->rooms as $room)
                  <option value="{{ $room->id }}">{{ $room->name }}</option>
                  @endforeach --}}
                </select>
              </div>

              <div class="col-lg-3">
                <label for="listing_issue" class="d-block">{{ __('Issue') }}</label>
                <select data-placeholder="{{ __('Select an issue') }}" class="form-control" id="listing_issue" name="issue">
                  @foreach ($issues as $issue)
                    <option value="{{ $issue->id }}">{{ $issue->name }}</option>
                  @endforeach
                </select>
              </div>

              <div class="col-lg-3">
                <label for="priority" class="d-block">{{ __('Priority') }}</label>
                <select data-placeholder="{{ __('Select a priority') }}" class="form-control" id="priority" name="priority">
                  @foreach ( Config::get('constants.priorities') as $key => $priority)
                    <option value="{{ $key }}">{{ $priority }}</option>
                  @endforeach
                </select>
              </div>
              
            </div>

            <div class="form-group row">
              <div class="col-lg-12">
                <label for="listing_room" class="d-block">{{ __('Comment') }}</label>
                <textarea id="listing_comment" class="form-control" name="comment" rows="4"></textarea>
              </div>
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-light-primary font-weight-bold" data-dismiss="modal">{{ __('Close') }}</button>
          <button type="submit" form="addIssueListingForm" id="btnModalGo" class="btn btn-primary font-weight-bold">Go!</button>
      </div>
      </div>
    </div>
  </div>

  <!-- Modal View Listing-->
  <div class="modal fade" id="viewIssueListingModal" tabindex="-1" role="dialog" aria-labelledby="listingTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="listingTitle">{{ __('View Issues') }}</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <i aria-hidden="true" class="ki ki-close"></i>
          </button>
        </div>
        <div class="modal-body">

          <div class="border-1 border-r-8 border-red-300 p-4 rounded-md">
            <div class="row">
              <div class="col-md-12">
                <h2 class="font-size-h4 font-weight-bold mb-6">{{ __('Requested Info') }}</h2>
              </div>
            </div>
            
            <div class="row">
              <div class="col-md-4">
                <div class="border border-gray-300 border-dashed rounded py-3 px-2 mb-3">
                  <div class="fs-5 fw-bold text-gray-700">
                    <span data-trace-listing="issue">-</span>
                  </div>
                  <div class="fw-semibold text-muted fs-9">{{ __('Issue') }}</div>
                </div>
              </div>
            
              <div class="col-md-4">
                <div class="border border-gray-300 border-dashed rounded py-3 px-2 mb-3">
                  <div class="fs-5 fw-bold text-gray-700">
                    <span data-trace-listing="room">-</span>
                  </div>
                  <div class="fw-semibold text-muted fs-9">{{ __('Room') }}</div>
                </div>
              </div>
            
              <div class="col-md-4">
                <div class="border border-gray-300 border-dashed rounded py-3 px-2 mb-3">
                  <div class="fs-5 fw-bold text-gray-700">
                    <span data-trace-listing="hotel">-</span>
                  </div>
                  <div class="fw-semibold text-muted fs-9">{{ __('Hotel') }}</div>
                </div>
              </div>
            
              <div class="col-md-4">
                <div class="border border-gray-300 border-dashed rounded py-3 px-2 mb-3">
                  <div class="fs-5 fw-bold text-gray-700">
                    <span data-trace-listing="requested_date">-</span>
                  </div>
                  <div class="fw-semibold text-muted fs-9">{{ __('Date') }}</div>
                </div>
              </div>
            
              <div class="col-md-4">
                <div class="border border-gray-300 border-dashed rounded py-3 px-2 mb-3">
                  <div class="fs-5 fw-bold text-gray-700">
                    <span data-trace-listing="priority">-</span>
                  </div>
                  <div class="fw-semibold text-muted fs-9">{{ __('Priority') }}</div>
                </div>
              </div>
            </div>
            
            <div class="row">
              <div class="col-md-12">
                  <div class="border border-gray-300 border-dashed rounded py-3 px-2 mb-3">
                    <div class="fs-5 fw-bold text-gray-700">
                      <span data-trace-listing="requested_comment">-</span>
                    </div>
                    <div class="fw-semibold text-muted fs-9">{{ __('Comment') }}</div>
                  </div>
              </div>
            </div>
          </div>


          <div class="border-1 border-green-300 border-r-8 mt-6 p-4 pt-0 rounded-md">
            <div class="row">
              <div class="col-md-12">
                <h2 class="font-size-h4 font-weight-bold mb-6">{{ __('Fixed Info') }}</h2>
              </div>
            </div>
            
            <div class="row">
              <div class="col-md-3">
                <div class="border border-gray-300 border-dashed rounded py-3 px-2 mb-3">
                  <div class="fs-5 fw-bold text-gray-700">
                    <span data-trace-listing="fixed_email">-</span>
                  </div>
                  <div class="fw-semibold text-muted fs-9">{{ __('Email') }}</div>
                </div>
              </div>
            
              <div class="col-md-3">
                <div class="border border-gray-300 border-dashed rounded py-3 px-2 mb-3">
                  <div class="fs-5 fw-bold text-gray-700">
                    <span data-trace-listing="fixed_date">-</span>
                  </div>
                  <div class="fw-semibold text-muted fs-9">{{ __('Date') }}</div>
                </div>
              </div>
            </div>
  
            <div class="row">
              <div class="col-md-12">
                <div class="border border-gray-300 border-dashed rounded py-3 px-2 mb-3">
                  <div class="fs-5 fw-bold text-gray-700">
                    <span data-trace-listing="fixed_comment">-</span>
                  </div>
                  <div class="fw-semibold text-muted fs-9">{{ __('Comment') }}</div>
                </div>
              </div>
            </div>
  
            <div class="row">
              <div class="col-md-12">
                <div class="border border-gray-300 border-dashed rounded py-3 px-2 mb-3">
                  <div class="fs-5 fw-bold text-gray-700">
                    <span data-trace-listing="images">-</span>
                  </div>
                  <div class="fw-semibold text-muted fs-9">{{ __('Images') }}</div>
                </div>
              </div>
            </div>
          </div>
          
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-light-primary font-weight-bold" data-dismiss="modal">{{ __('Close') }}</button>
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
      var listings_json_url = "{{ route('issues.listings.ajax') }}";
      // var employee_json_url = "{{ route('employees.getAll') }}";
    </script>
    <!--begin::Global Config(global config for global JS scripts)-->
    <script>
      var KTAppSettings = { "breakpoints": { "sm": 576, "md": 768, "lg": 992, "xl": 1200, "xxl": 1400 }, "colors": { "theme": { "base": { "white": "#ffffff", "primary": "#3699FF", "secondary": "#E5EAEE", "success": "#1BC5BD", "info": "#8950FC", "warning": "#FFA800", "danger": "#F64E60", "light": "#E4E6EF", "dark": "#181C32" }, "light": { "white": "#ffffff", "primary": "#E1F0FF", "secondary": "#EBEDF3", "success": "#C9F7F5", "info": "#EEE5FF", "warning": "#FFF4DE", "danger": "#FFE2E5", "light": "#F3F6F9", "dark": "#D6D6E0" }, "inverse": { "white": "#ffffff", "primary": "#ffffff", "secondary": "#3F4254", "success": "#ffffff", "info": "#ffffff", "warning": "#ffffff", "danger": "#ffffff", "light": "#464E5F", "dark": "#ffffff" } }, "gray": { "gray-100": "#F3F6F9", "gray-200": "#EBEDF3", "gray-300": "#E4E6EF", "gray-400": "#D1D3E0", "gray-500": "#B5B5C3", "gray-600": "#7E8299", "gray-700": "#5E6278", "gray-800": "#3F4254", "gray-900": "#181C32" } }, "font-family": "Poppins" };
    </script>
    <!--end::Global Config-->

    {{-- vendors --}}

    {{-- page scripts --}}
    <script src="{{ mix('js/pages/issues/listings.js') }}" type="text/javascript"></script>
    <script src="{{ mix('js/app.js') }}" type="text/javascript"></script>
    <script src="{{ mix('plugins/custom/fslightbox/fslightbox.bundle.js') }}" type="text/javascript"></script>
@endsection
