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
    @if (session()->has('error'))
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
    @if (session()->has('success'))
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
                            <h3 class="card-label font-weight-bolder text-dark">{{ __('Deductions Information') }}</h3>
                            <span
                                class="text-muted font-weight-bold font-size-sm mt-1">{{ __('Update deduction information') }}</span>
                        </div>
                        <div class="card-toolbar">
                            <button form="update_User" type="submit"
                                class="btn btn-success mr-2">{{ __('Save Changes') }}</button>
                        </div>
                    </div>
                    <!--end::Header-->
                    <!--begin::Form-->
                    <form id="update_User" action="{{ route('employees.update', $employee->id) }}" method="post"
                        class="form">
                        @method('PATCH')
                        @csrf
                        <input type="hidden" name="deduction" value="1">
                        <!--begin::Body-->
                        <div class="card-body">
                            <div class="row">
                                <label class="col-xl-3"></label>
                                <div class="col-xl-12">
                                    <h5 class="text-center font-weight-bold mt-10 mb-6">{{ __('Deduction') }}</h5>
                                </div>
                            </div>
                            <div class="row">
                                @if ($employee->PartTime == 1)
                                    <div class="form-group col-xl-6">
                                        <label class="col-form-label">{{ __('Hourly rate') }}</label>
                                        <input class="form-control form-control-lg form-control-solid" type="number"
                                            step="0.01" name="EhChf" value="{{ $employee->EhChf }}"
                                            placeholder="CHF" />
                                    </div>
                                @else
                                    <div class="form-group col-xl-6">
                                        <label class="col-form-label">{{ __('Salary') }}</label>
                                        <input class="form-control form-control-lg form-control-solid" type="number"
                                            step="0.01" name="rroga" value="{{ $employee->rroga }}"
                                            placeholder="CHF" />
                                    </div>
                                @endif

                                <!-- Apply for Child Allowance -->
                                <div class="form-group col-xl-6">
                                    <label class="col-form-label">{{ __('Apply for Child Allowance') }}</label>
                                    <select class="form-control form-control-solid form-control-lg" id="child_allowance"
                                        name="child_allowance">
                                        <option value=""></option>
                                        <option value="yes"
                                            {{ old('child_allowance', $employee->child_allowance) == 'yes' ? 'selected' : '' }}>
                                            {{ __('Yes') }}</option>
                                        <option value="no"
                                            {{ old('child_allowance', $employee->child_allowance) == 'no' ? 'selected' : '' }}>
                                            {{ __('No') }}</option>
                                        <option value="not_yet"
                                            {{ old('child_allowance', $employee->child_allowance) == 'not_yet' ? 'selected' : '' }}>
                                            {{ __('Not Yet') }}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row" id="allowance_fields"
                                style="display: {{ old('child_allowance', $employee->child_allowance) == 'yes' ? 'flex' : 'none' }};">
                                <div class="form-group col-xl-6">
                                    <label class="col-form-label">{{ __('Education allowance 250') }}</label>
                                    <input class="form-control form-control-lg form-control-solid" type="number"
                                        id="decki250" step="0.01" name="decki250"
                                        value="{{ old('decki250', $employee->decki250) }}" />
                                </div>
                                <div class="form-group col-xl-6">
                                    <label class="col-form-label">{{ __('Child allowance 200') }}</label>
                                    <input class="form-control form-control-lg form-control-solid" type="number"
                                        id="decki200" step="0.01" name="decki200"
                                        value="{{ old('decki200', $employee->decki200) }}" />
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-xl-6">
                                    <label class="col-form-label">{{ __('BVG') }}</label>
                                    <input class="form-control form-control-lg form-control-solid" type="number"
                                        step="0.01" name="BVG" value="{{ $employee->BVG }}" />
                                </div>
                                <div class="form-group col-xl-6">
                                    <label class="col-form-label">{{ __('Holiday Compensation 1') }}</label>
                                    <input class="form-control form-control-lg form-control-solid" type="number"
                                        step="0.01" name="Perqind1" value="{{ $employee->Perqind1 }}"
                                        placeholder="%" />
                                </div>
                                <div class="form-group col-xl-6">
                                    <label class="col-form-label">{{ __('Holiday Compensation 2') }}</label>
                                    <input class="form-control form-control-lg form-control-solid" type="number"
                                        step="0.01" name="Perqind2" value="{{ $employee->Perqind2 }}"
                                        placeholder="%" />
                                </div>
                                <div class="form-group col-xl-6">
                                    <label class="col-form-label">{{ __('13th monthly salary') }}</label>
                                    <input class="form-control form-control-lg form-control-solid" type="number"
                                        step="0.01" name="Perqind3" value="{{ $employee->Perqind3 }}"
                                        placeholder="%" />
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-xl-4">
                                    <label class="col-form-label">{{ __('Old holiday balance') }}</label>
                                    <input class="form-control form-control-lg form-control-solid" type="number"
                                        name="oldSaldoF" value="{{ $employee->oldSaldoF }}" />
                                </div>
                                <div class="form-group col-xl-4">
                                    <label class="col-form-label">{{ __('Old balance 13') }}</label>
                                    <input class="form-control form-control-lg form-control-solid" type="number"
                                        name="oldSaldo13" value="{{ $employee->oldSaldo13 }}" />
                                </div>
                                <div class="form-group col-xl-4">
                                    <label class="col-form-label">{{ __('Work Percentage') }}</label>
                                    <input class="form-control form-control-lg form-control-solid" type="number"
                                        step="0.01" name="work_percetage" value="{{ $employee->work_percetage }}"
                                        placeholder="%" />
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
    <script type="text/javascript">
        document.addEventListener("DOMContentLoaded", function() {
        const childAllowanceSelect = document.getElementById("child_allowance");
        const allowanceFields = document.getElementById("allowance_fields");
        const decki250Input = document.getElementById("decki250");
        const decki200Input = document.getElementById("decki200");

        // Function to toggle fields visibility and clear values
        function toggleAllowanceFields() {
            if (childAllowanceSelect.value === "yes") {
                allowanceFields.style.display = "flex";
            } else {
                // Clear the input values when hiding
                decki250Input.value = "";
                decki200Input.value = "";
                allowanceFields.style.display = "none";
            }
        }

        // Initial check (not needed anymore since we set it in HTML)
        // toggleAllowanceFields();

        // Add event listener
        childAllowanceSelect.addEventListener("change", toggleAllowanceFields);
    });
    </script>
@endsection
