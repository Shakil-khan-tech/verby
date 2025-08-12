@php
  $subheader_buttons = [
    (object)[
      'text' => __('Add Access Management'),
      'url' => route('roles.create'),
    ],
  ];
  $subheader_button_forms = [
    (object)[
      'text' => __('Delete Access Management'),
      'color' => 'danger',
      'confirm' => __('Are you sure?'),
      'action' => route('roles.destroy', $role->id),
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

  <!--begin::Profile Overview-->
  <div class="d-flex flex-row">
    @include('pages.widgets._widget-role_aside', ['item_active' => $item_active])
    <!--begin::Content-->
    <div class="flex-row-fluid ml-lg-8">
      <!--begin::Card-->
      <div class="card card-custom card-stretch">
        <!--begin::Header-->
        <div class="card-header py-3">
          <div class="card-title align-items-start flex-column">
            <h3 class="card-label font-weight-bolder text-dark">{{ __('Permissions') }}</h3>
            <span class="text-muted font-weight-bold font-size-sm mt-1">{{ __('Update access management\'s permissions') }}</span>
          </div>
          <div class="card-toolbar">
            <button type="submit" form="permissions_Role" class="btn btn-success mr-2">{{ __('Save Changes') }}</button>
            <a href="{{ route('roles.index') }}" class="btn btn-secondary">{{ __('Cancel') }}</a>
          </div>
        </div>
        <!--end::Header-->
        <!--begin::Form-->
        <form id="permissions_Role" action="{{ route('roles.update', $role->id) }}" method="post" class="form">
          @method('PATCH')
          @csrf
          <!--begin::Body-->
          <div class="card-body">
            <div class="row justify-content-md-center">
              <div class="col-lg-4">
                <div class="form-group">
                  <label>{{ __('Access Management Name') }} <span class="text-danger">*</span></label>
                  <input type="text" class="form-control" name="name"  placeholder="{{ __('Access Management Name') }}" value="{{ $role->name }}" required />
                  <span class="form-text text-muted">{{ __('Identify access management with a name.') }}</span>
                 </div>
              </div>
            </div>

            <div class="separator separator-dashed mt-5 mb-10"></div>

            <div class="row">
              <div class="col-lg-6">

                <div class="row">
                  <label class="col-xl-3"></label>
                  <div class="col-lg-9 col-xl-6">
                    <h5 class="font-weight-bold mb-6">{{ __('Users Permissions') }}:</h5>
                  </div>
                </div>
                <div class="form-group row align-items-center">
                  <label class="col-3 col-form-label font-weight-bold text-left text-right">{{ __('View') }}</label>
                  <div class="col-3 col-lg-3">
                    <span class="switch switch-sm">
                      <label>
                        <input type="checkbox" name="permissions[]" value="view_users" data-model="user" data-action="view"
                        {{ $role->permissions->where('name', 'view_users')->count() ? 'checked="checked"' : '' }}
                        />
                        <span></span>
                      </label>
                    </span>
                  </div>
                  <label class="col-3 col-lg-3 col-form-label font-weight-bold text-left text-right">{{ __('Edit') }}</label>
                  <div class="col-3 col-lg-2">
                    <span class="switch switch-sm">
                      <label>
                        <input type="checkbox" name="permissions[]" value="manage_users" data-model="user" data-action="manage"
                        {{ $role->permissions->where('name', 'manage_users')->count() ? 'checked="checked"' : '' }}
                        />
                        <span></span>
                      </label>
                    </span>
                  </div>
                </div>

                <div class="separator separator-dashed my-10"></div>

                <div class="row">
                  <label class="col-xl-3"></label>
                  <div class="col-lg-9 col-xl-6">
                    <h5 class="font-weight-bold mb-6">{{ __('Devices Permissions') }}:</h5>
                  </div>
                </div>
                <div class="form-group row align-items-center">
                  <label class="col-3 col-form-label font-weight-bold text-left text-right">{{ __('View') }}</label>
                  <div class="col-3 col-lg-3">
                    <span class="switch switch-sm">
                      <label>
                        <input type="checkbox" name="permissions[]" value="view_devices" data-model="device" data-action="view"
                        {{ $role->permissions->where('name', 'view_devices')->count() ? 'checked="checked"' : '' }}
                        />
                        <span></span>
                      </label>
                    </span>
                  </div>
                  <label class="col-3 col-lg-3 col-form-label font-weight-bold text-left text-right">{{ __('Edit') }}</label>
                  <div class="col-3 col-lg-2">
                    <span class="switch switch-sm">
                      <label>
                        <input type="checkbox" name="permissions[]" value="manage_devices" data-model="device" data-action="manage"
                        {{ $role->permissions->where('name', 'manage_devices')->count() ? 'checked="checked"' : '' }}
                        />
                        <span></span>
                      </label>
                    </span>
                  </div>
                </div>

                <div class="separator separator-dashed my-10"></div>

                <div class="row">
                  <label class="col-xl-3"></label>
                  <div class="col-lg-9 col-xl-6">
                    <h5 class="font-weight-bold mb-6">{{ __('Employees Permissions') }}:</h5>
                  </div>
                </div>
                <div class="form-group row align-items-center">
                  <label class="col-3 col-form-label font-weight-bold text-left text-right">{{ __('View') }}</label>
                  <div class="col-3 col-lg-3">
                    <span class="switch switch-sm">
                      <label>
                        <input type="checkbox" name="permissions[]" value="view_employees" data-model="employee" data-action="view"
                        {{ $role->permissions->where('name', 'view_employees')->count() ? 'checked="checked"' : '' }}
                        />
                        <span></span>
                      </label>
                    </span>
                  </div>
                  <label class="col-3 col-lg-3 col-form-label font-weight-bold text-left text-right">{{ __('Edit') }}</label>
                  <div class="col-3 col-lg-2">
                    <span class="switch switch-sm">
                      <label>
                        <input type="checkbox" name="permissions[]" value="manage_employees" data-model="employee" data-action="manage"
                        {{ $role->permissions->where('name', 'manage_employees')->count() ? 'checked="checked"' : '' }}
                        />
                        <span></span>
                      </label>
                    </span>
                  </div>
                </div>

                <div class="separator separator-dashed my-10"></div>

                <div class="row">
                  <label class="col-xl-3"></label>
                  <div class="col-lg-9 col-xl-6">
                    <h5 class="font-weight-bold mb-6">{{ __('Vacations Permissions') }}:</h5>
                  </div>
                </div>
                <div class="form-group row align-items-center">
                  <label class="col-3 col-form-label font-weight-bold text-left text-right">{{ __('View') }}</label>
                  <div class="col-3 col-lg-3">
                    <span class="switch switch-sm">
                      <label>
                        <input type="checkbox" name="permissions[]" value="view_vacations" data-model="vacation" data-action="view"
                        {{ $role->permissions->where('name', 'view_vacations')->count() ? 'checked="checked"' : '' }}
                        />
                        <span></span>
                      </label>
                    </span>
                  </div>
                  <label class="col-3 col-lg-3 col-form-label font-weight-bold text-left text-right">{{ __('Edit') }}</label>
                  <div class="col-3 col-lg-2">
                    <span class="switch switch-sm">
                      <label>
                        <input type="checkbox" name="permissions[]" value="manage_vacations" data-model="vacation" data-action="manage"
                        {{ $role->permissions->where('name', 'manage_vacations')->count() ? 'checked="checked"' : '' }}
                        />
                        <span></span>
                      </label>
                    </span>
                  </div>
                </div>

                <div class="separator separator-dashed my-10"></div>

                <div class="row">
                  <label class="col-xl-3"></label>
                  <div class="col-lg-9 col-xl-6">
                    <h5 class="font-weight-bold mb-6">{{ __('Records Permissions') }}:</h5>
                  </div>
                </div>
                <div class="form-group row align-items-center">
                  <label class="col-3 col-form-label font-weight-bold text-left text-right">{{ __('View') }}</label>
                  <div class="col-3 col-lg-3">
                    <span class="switch switch-sm">
                      <label>
                        <input type="checkbox" name="permissions[]" value="view_records" data-model="record" data-action="view"
                        {{ $role->permissions->where('name', 'view_records')->count() ? 'checked="checked"' : '' }}
                        />
                        <span></span>
                      </label>
                    </span>
                  </div>
                  <label class="col-3 col-lg-3 col-form-label font-weight-bold text-left text-right">{{ __('Edit') }}</label>
                  <div class="col-3 col-lg-2">
                    <span class="switch switch-sm">
                      <label>
                        <input type="checkbox" name="permissions[]" value="manage_records" data-model="record" data-action="manage"
                        {{ $role->permissions->where('name', 'manage_records')->count() ? 'checked="checked"' : '' }}
                        />
                        <span></span>
                      </label>
                    </span>
                  </div>
                </div>

                
                <div class="separator separator-dashed my-10"></div>
                <div class="row">
                  <label class="col-xl-3"></label>
                  <div class="col-lg-9 col-xl-6">
                    <h5 class="font-weight-bold mb-6">{{ __('Contract') }}:</h5>
                  </div>
                </div>
                <div class="form-group row align-items-center">
                  <label class="col-3 col-lg-3 col-form-label font-weight-bold text-left text-right">{{ __('Manage') }}</label>
                  <div class="col-3 col-lg-2">
                    <span class="switch switch-sm">
                      <label>
                        <input type="checkbox" name="permissions[]" value="contract" data-model="employee" data-action="manage"
                        {{ $role->permissions->where('name', 'contract')->count() ? 'checked="checked"' : '' }}
                        />
                        <span></span>
                      </label>
                    </span>
                  </div>
                </div>

                <div class="separator separator-dashed my-10"></div>
                <div class="row">
                  <label class="col-xl-3"></label>
                  <div class="col-lg-9 col-xl-6">
                    <h5 class="font-weight-bold mb-6">{{ __('Employee Contract') }}:</h5>
                  </div>
                </div>
                <div class="form-group row align-items-center">
                  <label class="col-3 col-form-label font-weight-bold text-left text-right">{{ __('View') }}</label>
                  <div class="col-3 col-lg-3">
                    <span class="switch switch-sm">
                      <label>
                        <input type="checkbox" name="permissions[]" value="view_contracts" data-model="contract" data-action="view"
                        {{ $role->permissions->where('name', 'view_contracts')->count() ? 'checked="checked"' : '' }}
                        />
                        <span></span>
                      </label>
                    </span>
                  </div>
                  <label class="col-3 col-lg-3 col-form-label font-weight-bold text-left text-right">{{ __('Manage') }}</label>
                  <div class="col-3 col-lg-2">
                    <span class="switch switch-sm">
                      <label>
                        <input type="checkbox" name="permissions[]" value="manage_contracts" data-model="record" data-action="manage"
                        {{ $role->permissions->where('name', 'manage_contracts')->count() ? 'checked="checked"' : '' }}
                        />
                        <span></span>
                      </label>
                    </span>
                  </div>
                </div>
              </div>
              <div class="border-left-lg col-lg-6">

                <div class="row">
                  <label class="col-xl-3"></label>
                  <div class="col-lg-9 col-xl-6">
                    <h5 class="font-weight-bold mb-6">{{ __('Holidays Permissions') }}:</h5>
                  </div>
                </div>
                <div class="form-group row align-items-center">
                  <label class="col-3 col-form-label font-weight-bold text-left text-right">{{ __('View') }}</label>
                  <div class="col-3 col-lg-3">
                    <span class="switch switch-sm">
                      <label>
                        <input type="checkbox" name="permissions[]" value="view_holidays" data-model="holiday" data-action="view"
                        {{ $role->permissions->where('name', 'view_holidays')->count() ? 'checked="checked"' : '' }}
                        />
                        <span></span>
                      </label>
                    </span>
                  </div>
                  <label class="col-3 col-lg-3 col-form-label font-weight-bold text-left text-right">{{ __('Edit') }}</label>
                  <div class="col-3 col-lg-2">
                    <span class="switch switch-sm">
                      <label>
                        <input type="checkbox" name="permissions[]" value="manage_holidays" data-model="holiday" data-action="manage"
                        {{ $role->permissions->where('name', 'manage_holidays')->count() ? 'checked="checked"' : '' }}
                        />
                        <span></span>
                      </label>
                    </span>
                  </div>
                </div>

                <div class="separator separator-dashed my-10"></div>

                <div class="row">
                  <label class="col-xl-3"></label>
                  <div class="col-lg-9 col-xl-6">
                    <h5 class="font-weight-bold mb-6">{{ __('Plans Permissions') }}:</h5>
                  </div>
                </div>
                <div class="form-group row align-items-center">
                  <label class="col-3 col-form-label font-weight-bold text-left text-right">{{ __('Manage') }}</label>
                  <div class="col-3 col-lg-3">
                    <span class="switch switch-sm dependable" data-by="device">
                      <label>
                        <input type="checkbox" name="permissions[]" value="manage_plans" data-model="plan" data-action="manage"
                        {{ $role->permissions->where('name', 'manage_plans')->count() ? 'checked="checked"' : '' }}
                        />
                        <span></span>
                      </label>
                    </span>
                  </div>
                </div>

                <div class="separator separator-dashed my-10"></div>

                <div class="row">
                  <label class="col-xl-3"></label>
                  <div class="col-lg-9 col-xl-6">
                    <h5 class="font-weight-bold mb-6">{{ __('Calendars Permissions') }}:</h5>
                  </div>
                </div>
                <div class="form-group row align-items-center">
                  <label class="col-3 col-form-label font-weight-bold text-left text-right">{{ __('Manage') }}</label>
                  <div class="col-3 col-lg-3">
                    <span class="switch switch-sm dependable" data-by="device">
                      <label>
                        <input type="checkbox" name="permissions[]" value="manage_calendars" data-model="calendar" data-action="manage"
                        {{ $role->permissions->where('name', 'manage_calendars')->count() ? 'checked="checked"' : '' }}
                        />
                        <span></span>
                      </label>
                    </span>
                  </div>
                </div>

                <div class="separator separator-dashed my-10"></div>

                <div class="row">
                  <label class="col-xl-3"></label>
                  <div class="col-lg-9 col-xl-6">
                    <h5 class="font-weight-bold mb-6">{{ __('Payroll Permissions') }}:</h5>
                  </div>
                </div>
                <div class="form-group row align-items-center">
                  <label class="col-3 col-form-label font-weight-bold text-left text-right">{{ __('Manage') }}</label>
                  <div class="col-3 col-lg-3">
                    <span class="switch switch-sm dependable" data-by="employee">
                      <label>
                        <input type="checkbox" name="permissions[]" value="manage_payrolls" data-model="payroll" data-action="manage"
                        {{ $role->permissions->where('name', 'manage_payrolls')->count() ? 'checked="checked"' : '' }}
                        />
                        <span></span>
                      </label>
                    </span>
                  </div>
                </div>

                <div class="separator separator-dashed my-10"></div>

                <div class="row">
                  <label class="col-xl-3"></label>
                  <div class="col-lg-9 col-xl-6">
                    <h5 class="font-weight-bold mb-6">{{ __('Daily Reports Permissions') }}:</h5>
                  </div>
                </div>
                <div class="form-group row align-items-center">
                  <label class="col-3 col-form-label font-weight-bold text-left text-right">{{ __('Manage') }}</label>
                  <div class="col-3 col-lg-3">
                    <span class="switch switch-sm dependable" data-by="device, employee">
                      <label>
                        <input type="checkbox" name="permissions[]" value="manage_daily_reports" data-model="daily_reports" data-action="manage"
                        {{ $role->permissions->where('name', 'manage_daily_reports')->count() ? 'checked="checked"' : '' }}
                        />
                        <span></span>
                      </label>
                    </span>
                  </div>
                </div>
                 {{-- <div class="separator separator-dashed my-10"></div>
                <div class="row">
                  <label class="col-xl-3"></label>
                  <div class="col-lg-9 col-xl-6">
                    <h5 class="font-weight-bold mb-6">{{ __('Employee Tax Mode') }}:</h5>
                  </div>
                </div> --}}
                {{-- <div class="form-group row align-items-center">
                  <label class="col-3 col-form-label font-weight-bold text-left text-right">{{ __('Manage') }}</label>
                  <div class="col-3 col-lg-3">
                    <span class="switch switch-sm">
                      <label>
                        <input type="checkbox" name="permissions[]" value="manage_tax_mode" data-model="employee" data-action="manage" 
                        {{ $role->permissions->where('name', 'manage_tax_mode')->count() ? 'checked="checked"' : '' }}
                        />
                        <span></span>
                      </label>
                    </span>
                  </div>
                </div> --}}

                <div class="separator separator-dashed my-10"></div>
                <div class="row">
                  <label class="col-xl-3"></label>
                  <div class="col-lg-9 col-xl-6">
                    <h5 class="font-weight-bold mb-6">{{ __('Employee Reminder Contract') }}:</h5>
                  </div>
                </div>
                <div class="form-group row align-items-center">
                  <label class="col-3 col-lg-3 col-form-label font-weight-bold text-left text-right">{{ __('Manage') }}</label>
                  <div class="col-3 col-lg-2">
                    <span class="switch switch-sm">
                      <label>
                        <input type="checkbox" name="permissions[]" value="manage_reminder" data-model="employee" data-action="manage"
                        {{ $role->permissions->where('name', 'manage_reminder')->count() ? 'checked="checked"' : '' }}
                        />
                        <span></span>
                      </label>
                    </span>
                  </div>
                </div>
              </div>
            </div>


            <div class="separator separator-dashed my-10"></div>

          </div>
          <!--end::Body-->
        </form>
        <!--end::Form-->
      </div>
    </div>
    <!--end::Content-->
  </div>
  <!--end::Profile Overview-->

@endsection

{{-- Scripts Section --}}
@section('scripts')
    {{-- page scripts --}}
    <script src="{{ mix('js/pages/roles/edit.js') }}" type="text/javascript"></script>
    <script src="{{ mix('js/app.js') }}" type="text/javascript"></script>
@endsection
