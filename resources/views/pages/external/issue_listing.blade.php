{{-- Extends layout --}}
@extends('layout.external')

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

  <!--begin::Entry-->
  <div class="d-flex flex-column-fluid">
    <!--begin::Container-->
    <div class="container">
      <!--begin::Row-->
      <div class="row justify-content-center my-5 px-8 px-lg-10">
        <div class="col-md-8">

          <div class="card card-custom gutter-b example example-compact">
            <div class="card-body">
              <div class="row">

                <div class="col-md-3">
                  <div class="d-flex flex-column text-dark-75">
                    <span class="font-weight-bolder font-size-sm">{{ __('Issue') }}</span>
                    <span class="font-weight-bolder font-size-h5">{{ $listing->issue->name }}</span>
                  </div>
                </div>

                <div class="col-md-3">
                  <div class="d-flex flex-column text-dark-75">
                    <span class="font-weight-bolder font-size-sm">{{ __('Room') }}</span>
                    <span class="font-weight-bolder font-size-h5">{{ $listing->room->name }}</span>
                  </div>
                </div>

                <div class="col-md-3">
                  <div class="d-flex flex-column text-dark-75">
                    <span class="font-weight-bolder font-size-sm">{{ __('Hotel') }}</span>
                    <span class="font-weight-bolder font-size-h5">{{ $listing->room->device->name }}</span>
                  </div>
                </div>

                <div class="col-md-3">
                  <div class="d-flex flex-column text-dark-75">
                    <span class="font-weight-bolder font-size-sm">{{ __('Date') }}</span>
                    <span class="font-weight-bolder font-size-h5">{{ $listing->date_requested }}</span>
                  </div>
                </div>
              </div>

              <div class="border-bottom border-gray-300 border-bottom-dashed my-2"></div>
              
              <div class="row">
                <div class="col-md-12">
                    <span class="font-weight-bolder font-size-sm">{{ __('Comment') }}</span> <br>
                    <span class="font-weight-bolder font-size-h5">{{ $listing->comment_requested }}</span>
                </div>
              </div>
              
            </div>
          </div>

          <!--begin::Card-->
          <div class="card card-custom gutter-b example example-compact">
            <div class="card-header">
              <h3 class="card-title">{{ __('Fix Issue') }}</h3>
            </div>
            <!--begin::Form-->
            <form class="form" action="{{ route('issues.listings_external_fix', $listing->id) }}" method="post" enctype="multipart/form-data">
              
              @csrf
              <input type="hidden" name="listing" value="{{ $listing->id }}">
              <input type="hidden" name="email_fixed" value="{{ $email }}">

              <div class="card-body">

                <div class="form-group row">

                  <div class="col-md-8 col-sm-12">
                    <label for="listing_room" class="d-block">{{ __('Comment') }}</label>
                    <textarea class="form-control" name="comment_fixed" rows="6">{{ $listing->comment_fixed }}</textarea>
                    
                  </div>

                  <div class="col-md-4 col-sm-12">
                    <label class="text-lg-right">{{ __('Photos') }}</label>
                    <br>
                    <input type="file" name="photo[]" multiple accept="image/png, image/gif, image/jpeg" />
                    {{-- <div class="future-dropzone dropzone-default dropzone-success" id="kt_dropzone_external_issues">
                      <div class="dropzone-msg dz-message needsclick">
                        <h3 class="dropzone-msg-title">Drop files here or click to upload.</h3>
                        <span class="dropzone-msg-desc">Only image files are allowed for upload</span>
                      </div>
                    </div> --}}
                  </div>

                </div>

                <div class="form-group row">
                  
                </div>

              </div>
              
              <div class="card-footer">
                <button type="submit" class="btn btn-primary mr-2">{{ __('Submit') }}</button>
              </div>
            </form>
            <!--end::Form-->
          </div>
          <!--end::Card-->

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
    <script>
      Dropzone.autoDiscover = false;
    </script>
    <script src="{{ mix('js/pages/issues/external.js') }}" type="text/javascript"></script>
    <script src="{{ mix('js/app.js') }}" type="text/javascript"></script>
@endsection
