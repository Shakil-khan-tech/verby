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
        <div class="card card-custom card-stretch col-12">
          <!--begin::Header-->
          <div class="card-header py-3">
            <div class="card-title align-items-start flex-column">
              <h3 class="card-label font-weight-bolder text-dark">{{ __('Interim Payment') }}</h3>
              <span class="text-muted font-weight-bold font-size-sm mt-1">{{ __('Update Interim Payment') }}</span>
            </div>
            <div class="card-toolbar">
              <button form="update_User" type="submit" class="btn btn-success mr-2">{{ __('Save Changes') }}</button>
            </div>
          </div>
          <!--end::Header-->
          <!--begin::Form-->
          <form id="update_User" action="{{ route('employees.update', $employee->id) }}" method="post" class="form">
            @method('PATCH')
            @csrf
            <input type="hidden" name="insurance" value="1">
            <!--begin::Body-->
            <div class="card-body">
              {{-- Insurance 6 --}}
              <div class="row">
                <label class="col-xl-3"></label>
                <div class="col-xl-12">
                  <h5 class="text-center font-weight-bold mt-10 mb-6">
                    <span class="font-size-h5">6.</span>
                    {{ __('Has the insured person been offered more working hours in the certified month?') }}
                  </h5>
                </div>
              </div>
              <div class="row">
                <div class="form-group col-xl-3">
                  <label class="col-form-label">{{ __('Yes/No') }}</label>
                  <div class="radio-inline">
                    <label class="radio">
                      <input type="radio" name="insurance_6" value="yes" {{ $employee->insurance_6_1 ? 'checked' : '' }}>
                      <span></span>{{ __('Yes') }}
                    </label>
                    <label class="radio">
                      <input type="radio" name="insurance_6" value="no" {{ $employee->insurance_6_5 ? 'checked' : '' }}>
                      <span></span>{{ __('No') }}
                    </label>
                  </div>
                </div>
                <div class="form-group col-xl-3">
                  <label class="col-form-label">{{ __('Hours per day') }}</label>
                  <input class="form-control form-control-lg form-control-solid" type="number" step="0.01" name="insurance_6_2" value="{{ $employee->insurance_6_2 }}" />
                </div>
                <div class="form-group col-xl-3">
                  <label class="col-form-label">{{ __('Hours per week') }}</label>
                  <input class="form-control form-control-lg form-control-solid" type="number" step="0.01" name="insurance_6_3" value="{{ $employee->insurance_6_3 }}" />
                </div>
                <div class="form-group col-xl-3">
                  <label class="col-form-label">{{ __('Hours per month') }}</label>
                  <input class="form-control form-control-lg form-control-solid" type="number" step="0.01" name="insurance_6_4" value="{{ $employee->insurance_6_4 }}" />
                </div>
              </div>
  
              {{-- Insurance 7 --}}
              <div class="row">
                <label class="col-xl-3"></label>
                <div class="col-xl-12">
                  <h5 class="text-center font-weight-bold mt-10 mb-6">
                    <span class="font-size-h5">7.</span>
                    {{ __('On what grounds did the insured person reject your job offer?') }}
                  </h5>
                </div>
              </div>
              <div class="row">
                <div class="form-group col-xl-12">
                  <textarea class="form-control" name="insurance_7_1" rows="2">{{ $employee->insurance_7_1 }}</textarea>
                </div>
              </div>
  
              {{-- Insurance 15 --}}
              <div class="row">
                <label class="col-xl-3"></label>
                <div class="col-xl-12">
                  <h5 class="text-center font-weight-bold mt-10 mb-6">
                    <span class="font-size-h5">15.</span>
                    {{ __('Will the insured person continue to be employed?') }}
                  </h5>
                </div>
              </div>
  
              <div class="row">
                <div class="form-group col-xl-6">
                  {{-- <label class="col-form-label">{{ __('Yes/No') }}</label> --}}
                  <div class="checkbox-list">
                    <label class="checkbox">
                      <input type="checkbox" name="insurance_15_1" {{ $employee->insurance_15_1 ? 'checked' : '' }}>
                      <span></span>{{ __('yes, indefinitely') }}
                    </label>
                  </div>
                </div>
                <div class="form-group col-xl-6">
  
                  <div class="input-group">
                    <div class="input-group-prepend">
                      <span class="input-group-text">
                        <label class="checkbox checkbox-inline">
                          <input type="checkbox" name="insurance_15_2" {{ $employee->insurance_15_2 ? 'checked' : '' }}>
                          <span></span>
                        </label>
                      </span>
                      <span class="input-group-text">{{ __('yes, probably until') }}</span>
                    </div>
                    <input class="form-control" type="text" name="insurance_15_3" value="{{ $employee->insurance_15_3 }}" />
                  </div>
  
                </div>
              </div>
  
              <div class="row">
                <div class="form-group col-xl-6">
                  {{-- <label class="col-form-label">{{ __('Yes/No') }}</label> --}}
                  <div class="checkbox-list">
                    <label class="checkbox">
                      <input type="checkbox" name="insurance_15_4" {{ $employee->insurance_15_4 ? 'checked' : '' }}>
                      <span></span>{{ __('no, who gave notice?') }}
                    </label>
                  </div>
                </div>
                <div class="form-group col-xl-6">
                  <input class="form-control form-control-lg form-control-solid" type="text" name="insurance_15_5" value="{{ $employee->insurance_15_5 }}" />
                </div>
              </div>
  
              <div class="row">
                <div class="form-group col-xl-4">
                  <label class="col-form-label">{{ __('When?') }}</label>
                  <input class="form-control form-control-lg form-control-solid" type="text" name="insurance_15_6" value="{{ $employee->insurance_15_6 }}" />
                </div>
                <div class="form-group col-xl-8">
                  <label class="col-form-label">{{ __('At what point in time?') }}</label>
                  <input class="form-control form-control-lg form-control-solid" type="text" name="insurance_15_7" value="{{ $employee->insurance_15_7 }}" />
                </div>
              </div>
  
              {{-- Insurance 16 --}}
              <div class="row">
                <label class="col-xl-3"></label>
                <div class="col-xl-12">
                  <h5 class="text-center font-weight-bold mt-10 mb-6">
                    <span class="font-size-h5">16.</span>
                    {{ __('Reason for termination of contract') }}
                  </h5>
                </div>
              </div>
              <div class="row">
                <div class="form-group col-xl-12">
                  <textarea class="form-control" name="insurance_16_1" rows="2">{{ $employee->insurance_16_1 }}</textarea>
                </div>
              </div>
            </div>
            <!--end::Body-->
          </form>
          <!--end::Form-->
        </div>
      </div>

    </div>
    <!--end::Content-->
  </div>
  <!--end::Profile Overview-->

@endsection

{{-- Styles Section --}}
@section('styles')

@endsection


{{-- Scripts Section --}}
@section('scripts')
    {{-- vendors --}}

    {{-- page scripts --}}
    <script src="{{ mix('js/pages/_misc/aside.js') }}" type="text/javascript"></script>
    <script src="{{ mix('js/pages/_misc/aside_vacation.js') }}" type="text/javascript"></script>
    <script src="{{ mix('js/pages/_misc/daterangepicker.js') }}" type="text/javascript"></script>
    <script src="{{ mix('js/app.js') }}" type="text/javascript"></script>
@endsection
