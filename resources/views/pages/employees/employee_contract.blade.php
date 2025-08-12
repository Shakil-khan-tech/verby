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
    @include('pages.widgets._widget-profile_aside', ['item_active' => $item_active])
    <!--begin::Content-->
    <div class="flex-row-fluid ml-lg-8">

      <div class="row">
        <!--begin::Card-->
        <div class="card card-custom card-stretch col-12">
          <!--begin::Header-->
          <div class="card-header py-3">
            <div class="card-title align-items-start flex-column">
              <h3 class="card-label font-weight-bolder text-dark">{{ __('Contracts') }}</h3>
              <span class="text-muted font-weight-bold font-size-sm mt-1">{{ __('Manage Employee Contracts') }}</span>
            </div>
            <div class="card-toolbar">
              {{-- <button form="update_User" type="submit" class="btn btn-success mr-2">{{ __('Save Changes') }}</button> --}}
            </div>
          </div>
          <!--end::Header-->

          <!--begin::Body-->
          <div class="card-body">
            <!--begin::Search Form-->
            <div class="row align-items-center">
              <div class="col-lg-10 col-xl-9">
                <div class="row align-items-center">
                  <div class="col-md-4 my-2 my-md-0">
                    <div class="input-icon">
                      <input type="text" class="form-control" placeholder="{{ __('Search...') }}" id="files_datatable_search_query" />
                      <span>
                        <i class="flaticon2-search-1 text-muted"></i>
                      </span>
                    </div>
                  </div>
                  
                </div>
              </div>
              <div class="col-lg-2 col-xl-3 mt-5 mt-lg-0">
                <a href="#" class="btn btn-light-primary px-6 font-weight-bold">{{ __('Search') }}</a>
              </div>
            </div>
            <!--end::Search Form-->

            <!--begin: Datatable-->
            <div class="datatable datatable-bordered datatable-head-custom" id="files_datatable"></div>
            <!--end: Datatable-->
          </div>
          <!--end::Body-->
          
        </div>
        <!--end::Card-->
      </div>

    </div>
    <!--end::Content-->
  </div>
  <!--end::Profile Overview-->
  <!-----Edit File Name Model---->
  <!-- Edit Name Modal -->
 <!-- Edit File Name Modal -->
<div class="modal fade" id="editFileNameModal" tabindex="-1" aria-labelledby="editFileNameModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="editFileNameModalLabel">Edit File Name</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" id="editFileId">
        <div class="mb-3">
          <label for="editFileName" class="form-label">File Name</label>
          <input type="text" class="form-control" id="editFileName">
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" id="saveFileNameBtn" class="btn btn-primary">Save</button>
        <button type="button" class="btn btn-secondary" id="closeFileNameBtn" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>


  <!------Preview File Model---->
  <div class="modal fade" id="previewFileModal" tabindex="-1" role="dialog" aria-labelledby="previewFileModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="previewFileModalLabel">Preview File</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="${Lang.get('script.close')}">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body p-0" style="height: 80vh;">
          <iframe id="filePreviewIframe" src="" width="100%" height="100%" style="border: none;"></iframe>
        </div>
      </div>
    </div>
  </div>

    <div class="modal fade" id="uploadContractModal" tabindex="-1" role="dialog" aria-labelledby="uploadContractLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog" role="document">
            <form id="uploadContractForm" enctype="multipart/form-data">
            <input type="hidden" name="contract_id" id="modal_contract_id">
            <input type="hidden" name="employee_id" id="modal_employee_id" value="{{$employee->id}}">
            <div class="modal-content">
                <div class="modal-header">
                <h5 class="modal-title" id="uploadContractLabel">{{Lang::get('script.upload_sign_contract')}}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                </div>
                <div class="modal-body">
                <input type="file" name="file" class="form-control" required />
                </div>
                <div class="modal-footer">
                <button type="submit" class="btn btn-primary">Upload</button>
                <button type="button" class="btn btn-secondary" id="cancelUploadBtn">Cancel</button>
                </div>
            </div>
            </form>
        </div>
    </div>

@endsection

{{-- Styles Section --}}
@section('styles')

@endsection


{{-- Scripts Section --}}
@section('scripts')
    {{-- vendors --}}

    {{-- page scripts --}}
    <script>
      Dropzone.autoDiscover = false;
      var employee_id = "{{ $employee->id }}";
      var contract_store_url = "{{ route('employees.contract.store', $employee->id) }}";
      var files_get_url = "{{ route('employees.contract.get', $employee->id) }}";
    </script>
    
    <script src="{{ mix('js/pages/_misc/aside.js') }}" type="text/javascript"></script>
    <script src="{{ mix('js/pages/_misc/aside_vacation.js') }}" type="text/javascript"></script>
    <script src="{{ mix('js/pages/_misc/daterangepicker.js') }}" type="text/javascript"></script>
    <script src="{{ mix('js/pages/employees/employee_contract.js') }}" type="text/javascript"></script>
    <script src="{{ mix('js/app.js') }}" type="text/javascript"></script>
@endsection
