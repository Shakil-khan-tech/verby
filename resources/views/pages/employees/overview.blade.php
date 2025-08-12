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
              <h3 class="card-label font-weight-bolder text-dark">{{ __('Status Overview') }}</h3>
              <span class="text-muted font-weight-bold font-size-sm mt-1">{{ __('Update overview information') }}</span>
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
            <input type="hidden" name="overview" value="1">
            <!--begin::Body-->
            <div class="card-body">
              <div class="row">
                <label class="col-xl-3"></label>
                <div class="col-xl-12">
                  <h5 class="text-center font-weight-bold mb-6">{{ __('Overview') }}</h5>
                </div>
              </div>
              <div class="form-group row">
                <label class="col-xl-3 col-lg-3 col-form-label">{{ __('Sage ID') }}</label>
                <div class="col-lg-9 col-xl-6">
                  <input class="form-control form-control-lg form-control-solid" type="number" name="sage_number" value="{{ $employee->sage_number }}" placeholder="{{ __('Sage ID') }}" />
                </div>
              </div>
              <div class="form-group row">
                <label class="col-xl-3 col-lg-3 col-form-label">{{ __('Api monitoring') }}</label>
                <div class="col-lg-9 col-xl-6 d-flex">
                  <span class="switch switch-sm">
                    <label>
                      <input type="checkbox" name="api_monitoring" {{ $employee->api_monitoring ? 'checked' : '' }} />
                      <span></span>
                    </label>
                  </span>
                </div>
              </div>
              <div class="form-group row">
                <label class="col-xl-3 col-lg-3 col-form-label">{{ __('Function') }}</label>
                <div class="col-lg-9 col-xl-6">
                  <select class="form-control form-control-lg form-control-solid" name="function">
                    @foreach (Config::get('constants.functions') as $key => $function)
                      <option value="{{ $key }}" {{ $employee->function == $key ? 'selected' : '' }}>{{ $function }}</option>
                    @endforeach
                  </select>
                </div>
              </div>
              <div class="form-group row">
                <label class="col-xl-3 col-lg-3 col-form-label">{{ __('Payment method') }}</label>
                <div class="col-lg-9 col-xl-6">
                  <select class="form-control form-control-lg form-control-solid" name="PartTime">
                    @foreach (Config::get('constants.part_time') as $key => $part_time)
                      <option value="{{ $key }}" {{ $employee->PartTime == $key ? 'selected' : '' }}>{{ $part_time }}</option>
                    @endforeach
                  </select>
                </div>
              </div>
              <div class="form-group row">
                <label class="col-xl-3 col-lg-3 col-form-label">{{ __('Nightshift') }}</label>
                <div class="col-lg-9 col-xl-6 d-flex">
                  <div class="checkbox-inline">
                    <label class="checkbox">
                      <input type="checkbox" name="noqnaSmena" {{ $employee->noqnaSmena ? 'checked' : '' }} />
                      <span></span>{{ __('Nightshift') }}
                    </label>
                  </div>
                </div>
              </div>
              <div class="form-group row">
                <label class="col-xl-3 col-lg-3 col-form-label">{{ __('Location') }}</label>
                <div class="col-lg-9 col-xl-6">
                  <div class="checkbox-inline">
                    @foreach ($devices as $key => $device)
                      <label class="checkbox">
                        <input type="checkbox" name="locations[]" {{ $employee->devices->contains('id', $device->id) ? 'checked' : '' }} value="{{$device->id}}" />
                        <span></span>{{ $device->name }}
                      </label>
                    @endforeach
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="form-group col-xl-6">
                  <label class="col-form-label">{{ __('Entry date') }}</label>
                  <input class="form-control form-control-lg form-control-solid" type="date" name="start" value="{{ $employee->start }}" />
                </div>
                <div class="form-group col-xl-6">
                  <label class="col-form-label">{{ __('Exit date') }}</label>
                  <input class="form-control form-control-lg form-control-solid" type="date" name="end" value="{{ $employee->end }}" />
                </div>
              </div>
              
              <div class="row">
                <div class="col-12">
                  <h2>{{ __('Entry History') }}</h2>
                  
                  <table class="table table-xs text-center">
                    <thead>
                        <tr>
                            <th scope="col">{{ __('Entry') }}</th>
                            <th scope="col">{{ __('Exit') }}</th>
                            <th scope="col">{{ __('Action') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                      @foreach ($employee->entries as $entry)
                      <tr>
                        <td>{{ $entry->start->format('d M Y') }}</td>
                        <td>{{ $entry->end->format(' d M Y') }}</td>
                        <td>
                          <a 
                            data-entry="delete"
                            data-key="{{$entry->id}}"
                            data-start="{{$entry->start}}"
                            data-end="{{$entry->end}}"
                            href="#!" class="btn btn-hover-bg-danger btn-text-danger btn-hover-text-white border-0 font-weight-bold">{{ __('Delete') }}
                          </a>
                        </td>
                      </tr>        
                      @endforeach
                    </tbody>
                  </table>

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
    <script src="{{ mix('js/pages/employees/overview.js') }}" type="text/javascript"></script>
    <script src="{{ mix('js/app.js') }}" type="text/javascript"></script>
@endsection
