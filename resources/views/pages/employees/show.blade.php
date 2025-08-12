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
                            <h3 class="card-label font-weight-bolder text-dark">{{ __('Personal Information') }}</h3>
                            <span
                                class="text-muted font-weight-bold font-size-sm mt-1">{{ __('Update your personal information') }}</span>
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
                        <input type="hidden" name="show" value="1">
                        <!--begin::Body-->
                        <div class="card-body">
                            <div class="row">
                                <div class="col-xl-12">
                                    <h5 class="text-center font-weight-bold mb-6">{{ __('The data') }}</h5>
                                </div>
                            </div>

                            {{-- First & Last Name --}}
                            <div class="row">
                                <div class="form-group col-xl-6">
                                    <label>{{ __('First Name') }}</label>
                                    <input type="text" name="name" value="{{ $employee->name }}"
                                        class="form-control form-control-lg form-control-solid">
                                </div>
                                <div class="form-group col-xl-6">
                                    <label>{{ __('Last Name') }}</label>
                                    <input type="text" name="surname" value="{{ $employee->surname }}"
                                        class="form-control form-control-lg form-control-solid">
                                </div>
                            </div>

                            {{-- Nationality & Phone --}}
                            <div class="row">
                                <div class="form-group col-xl-6">
                                    <label>{{ __('Nationality') }}</label>
                                    <select name="nationality"
                                        class="form-control select2-nationality form-control-lg form-control-solid">
                                        <option value=""></option>
                                        @foreach ($countries as $country)
                                            <option value="{{ $country->id }}"
                                                {{ $employee->country_id == $country->id ? 'selected' : '' }}>
                                                {{ session('locale') == 'en' ? $country->name_en : $country->name_de }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group col-xl-6">
                                    <label>{{ __('Phone') }}</label>
                                    <div class="input-group input-group-lg input-group-solid">
                                        <div class="input-group-prepend"><span class="input-group-text"><i
                                                    class="la la-phone"></i></span></div>
                                        <input type="text" name="phone" value="{{ $employee->phone }}"
                                            class="form-control form-control-lg form-control-solid"
                                            placeholder="{{ __('Phone') }}">
                                    </div>
                                </div>
                            </div>

                            {{-- Email & DOB --}}
                            <div class="row">
                                <div class="form-group col-xl-6">
                                    <label>{{ __('Email') }}</label>
                                    <div class="input-group input-group-lg input-group-solid">
                                        <div class="input-group-prepend"><span class="input-group-text"><i
                                                    class="la la-at"></i></span></div>
                                        <input type="text" name="email" value="{{ $employee->email }}"
                                            class="form-control form-control-lg form-control-solid"
                                            placeholder="{{ __('Email') }}">
                                    </div>
                                </div>
                                <div class="form-group col-xl-6">
                                    <label>{{ __('DOB') }}</label>
                                    <input type="date" name="DOB" value="{{ $employee->DOB }}"
                                        class="form-control form-control-lg form-control-solid">
                                </div>
                            </div>

                            {{-- Gender & Married --}}
                            <div class="row">
                                <div class="form-group col-xl-6">
                                    <label>{{ __('Gender') }}</label>
                                    <select name="gender" class="form-control form-control-lg form-control-solid">
                                        <option value=""></option>
                                        <option value="0" {{ $employee->gender == 0 ? 'selected' : '' }}>
                                            {{ __('Male') }}</option>
                                        <option value="1" {{ $employee->gender == 1 ? 'selected' : '' }}>
                                            {{ __('Female') }}</option>
                                    </select>
                                </div>
                                <div class="form-group col-xl-6">
                                    <label>{{ __('Married') }}</label>
                                    <select name="maried" class="form-control form-control-lg form-control-solid">
                                        <option value=""></option>
                                        <option value="1" {{ $employee->maried == 1 ? 'selected' : '' }}>
                                            {{ __('Yes') }}</option>
                                        <option value="0" {{ $employee->maried == 0 ? 'selected' : '' }}>
                                            {{ __('No') }}</option>
                                    </select>
                                </div>
                            </div>

                            {{-- Street & PLZ --}}
                            <div class="row">
                                <div class="form-group col-xl-6">
                                    <label>{{ __('Street') }}</label>
                                    <input type="text" name="strasse" value="{{ $employee->strasse }}"
                                        class="form-control form-control-lg form-control-solid">
                                </div>
                                <div class="form-group col-xl-6">
                                    <label>{{ __('PLZ') }}</label>
                                    <input type="text" name="PLZ" value="{{ $employee->PLZ }}"
                                        class="form-control form-control-lg form-control-solid">
                                </div>
                            </div>

                            {{-- ORT + Canton --}}
                            <div class="row">
                                <div class="form-group col-xl-6">
                                    <label>{{ __('ORT') }}</label>
                                    <input type="text" name="ORT1" value="{{ $employee->ORT1 }}"
                                        class="form-control form-control-lg form-control-solid">
                                </div>
                                <div class="form-group col-xl-6">
                                    <label>{{ __('Canton') }}</label>
                                    <select name="ORT" class="form-control form-control-lg form-control-solid">
                                        <option value=""></option>
                                        @foreach (Config::get('constants.kantons') as $key => $canton)
                                            <option value="{{ $key }}"
                                                {{ $key == $employee->ORT ? 'selected' : '' }}>{{ $canton }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            {{-- AHV & TAX --}}
                            <div class="row">
                                <div class="form-group col-xl-6">
                                    <label>{{ __('AHV') }}</label>
                                    <input type="text" name="AHV" value="{{ $employee->AHV }}"
                                        class="form-control form-control-lg form-control-solid">
                                </div>
                                <div class="form-group col-xl-6">
                                    <label>{{ __('TAX') }}</label>
                                    <select name="TAX_TOGGLE" id="taxToggle"
                                        class="form-control form-control-lg form-control-solid">
                                        <option value="no" {{ empty($employee->TAX) ? 'selected' : '' }}>No</option>
                                        <option value="yes" {{ !empty($employee->TAX) ? 'selected' : '' }}>Yes</option>
                                    </select>
                                    <div id="taxInputContainer" class="mt-2"
                                        style="{{ !empty($employee->TAX) ? '' : 'display:none;' }}">
                                        <input type="text" name="TAX" value="{{ $employee->TAX }}"
                                            class="form-control form-control-lg form-control-solid"
                                            placeholder="{{ __('TAX Amount') }}">
                                    </div>
                                </div>
                            </div>

                            {{-- Bank + IBAN --}}
                            <div class="row">
                                <div class="form-group col-xl-6">
                                    <label>{{ __('Bank') }}</label>
                                    <input type="text" name="bankname" value="{{ $employee->bankname }}"
                                        class="form-control form-control-lg form-control-solid">
                                </div>
                                <div class="form-group col-xl-6">
                                    <label>{{ __('IBAN') }}</label>
                                    <input type="text" name="IBAN" value="{{ $employee->IBAN }}"
                                        class="form-control form-control-lg form-control-solid">
                                </div>
                            </div>

                            {{-- PIN + CARD --}}
                            <div class="row">
                                <div class="form-group col-xl-6">
                                    <label>{{ __('PIN') }}</label>
                                    <input type="number" min="1000" max="9999" name="pin"
                                        value="{{ $employee->pin }}"
                                        class="form-control form-control-lg form-control-solid">
                                </div>
                                <div class="form-group col-xl-6">
                                    <label>{{ __('CARD') }}</label>
                                    <input type="text" name="card" value="{{ $employee->card }}"
                                        class="form-control form-control-lg form-control-solid">
                                </div>
                            </div>

                            {{-- Married Since + Religion --}}
                            <div class="row">
                                <div class="form-group col-xl-6">
                                    <label>{{ __('Married Since') }}</label>
                                    <input type="date" name="married_since" value="{{ $employee->married_since }}"
                                        class="form-control form-control-lg form-control-solid">
                                </div>
                                <div class="form-group col-xl-6">
                                    <label>{{ __('Religious Affiliation') }}</label>
                                    <input type="text" name="religion" value="{{ $employee->religion }}"
                                        class="form-control form-control-lg form-control-solid">
                                </div>
                            </div>

                            {{-- Children + Work Permit --}}
                            <div class="row">
                                <div class="form-group col-xl-6">
                                    <label>{{ __('Children') }}</label>
                                    <input type="number" min="0" name="children"
                                        value="{{ $employee->children }}"
                                        class="form-control form-control-lg form-control-solid">
                                </div>
                                <div class="form-group col-xl-6">
                                    <label>{{ __('Work Permit') }}</label>
                                    <select name="work_permit" class="form-control form-control-lg form-control-solid">
                                        <option value=""></option>
                                        @foreach (['L', 'B', 'C', 'S', 'CH'] as $wp)
                                            <option value="{{ $wp }}"
                                                {{ $employee->work_permit == $wp ? 'selected' : '' }}>{{ $wp }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            {{-- Work Permit Expiry + Additional Income --}}
                            <div class="row">
                                <div class="form-group col-xl-6">
                                    <label>{{ __('Work Permit Expiration Date') }}</label>
                                    <input type="date" name="work_permit_expiry"
                                        value="{{ $employee->work_permit_expiry }}"
                                        class="form-control form-control-lg form-control-solid">
                                </div>
                                <div class="form-group col-xl-6">
                                    <label>{{ __('Additional Income (RAV)') }}</label>
                                    <select name="additional_income_toggle" id="additionalIncomeToggle"
                                        class="form-control form-control-lg form-control-solid">
                                        <option value=""></option>
                                        <option value="no"
                                            {{ empty($employee->additional_income) ? 'selected' : '' }}>
                                            {{ __('No') }}</option>
                                        <option value="yes"
                                            {{ !empty($employee->additional_income) ? 'selected' : '' }}>
                                            {{ __('Yes') }}</option>
                                    </select>
                                    <div id="additionalIncomeAmountContainer" class="mt-2"
                                        style="{{ !empty($employee->additional_income) ? '' : 'display:none;' }}">
                                        <input type="number" min="0" step="0.01" name="additional_income"
                                            value="{{ old('additional_income', $employee->additional_income) }}"
                                            class="form-control form-control-lg form-control-solid"
                                            placeholder="{{ __('Enter Amount') }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--end::Body-->
                    </form>
                    <!--end::Form-->
                </div>
            </div>

            <div class="row mt-4">
                <div class="card card-custom card-stretch col-12">
                    <div class="card-header">
                        <div class="card-title"></div>
                        <div class="card-toolbar">
                            <form action="{{ route('employees.destroy', $employee->id) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <input type="submit" class="btn btn-light-danger font-weight-bold ml-2 px-2"
                                    onclick="return confirm('{{ __('Are you sure?') }}')"
                                    value="{{ __('Delete Employee') }}">
                            </form>
                        </div>
                    </div>
                </div>
            </div>

        </div>
        <!--end::Content-->
    </div>
    <!--end::Profile Overview-->

@endsection

{{-- Styles Section --}}
@section('styles')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        /* Select2 styling to match your form controls */
        .select2-container .select2-selection--single {
            height: 46px !important;
            border: 1px solid #E4E6EF !important;
            border-radius: 0.475rem !important;
            padding: 0.65rem 1rem;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 1.5 !important;
            padding-left: 0 !important;
            color: #3F4254 !important;
            padding-right: 0 !important;
            /* Added to compensate for removed arrow */
        }

        /* Hide dropdown arrow */
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            display: none !important;
        }

        /* Hide clear button */
        .select2-container--default .select2-selection--single .select2-selection__clear {
            display: none !important;
        }

        /* Dropdown styling */
        .select2-container--default .select2-results__option--highlighted[aria-selected] {
            background-color: #F3F6F9 !important;
            color: #3F4254 !important;
        }

        .select2-container--default .select2-results__option[aria-selected=true] {
            background-color: #F3F6F9 !important;
            color: #3F4254 !important;
        }

        .select2-container--open .select2-dropdown--below {
            border-color: #E4E6EF !important;
            border-radius: 0 0 0.475rem 0.475rem !important;
        }

        /* Make the select field fully clickable */
        .select2-container--default .select2-selection--single {
            cursor: pointer !important;
        }
    </style>
@endsection


{{-- Scripts Section --}}
@section('scripts')
    {{-- vendors --}}

    {{-- page scripts --}}
    <script src="{{ mix('js/pages/_misc/aside.js') }}" type="text/javascript"></script>
    <script src="{{ mix('js/pages/_misc/aside_vacation.js') }}" type="text/javascript"></script>
    <script src="{{ mix('js/pages/_misc/daterangepicker.js') }}" type="text/javascript"></script>
    <script src="{{ mix('js/app.js') }}" type="text/javascript"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            // Initialize Select2 for nationality dropdown
            $('.select2-nationality').select2({
                allowClear: true,
                templateResult: formatCountry,
                templateSelection: formatCountrySelection,
                width: '100%'
            });

            // Format the displayed option
            function formatCountry(country) {
                if (!country.id) {
                    return country.text;
                }
                var $country = $(
                    '<span>' + country.text + '</span>'
                );
                return $country;
            }

            // Format the selected option
            function formatCountrySelection(country) {
                return country.text;
            }
        });
        document.addEventListener('DOMContentLoaded', function() {
            const taxToggle = document.getElementById('taxToggle');

            if (taxToggle) {
                // Handle change event
                taxToggle.addEventListener('change', function() {
                    const container = document.getElementById('taxInputContainer');
                    const taxInput = container.querySelector('input[name="TAX"]');

                    container.style.display = this.value === 'yes' ? 'block' : 'none';
                    if (this.value === 'no') {
                        taxInput.value = '';
                    }
                });

                // Initialize based on current value
                taxToggle.dispatchEvent(new Event('change'));
            }
        });

        document.addEventListener("DOMContentLoaded", function() {
            const incomeToggle = document.getElementById("additionalIncomeToggle");
            const incomeAmountContainer = document.getElementById("additionalIncomeAmountContainer");
            const incomeInput = incomeAmountContainer?.querySelector('input[name="additional_income"]');

            if (incomeToggle && incomeAmountContainer) {
                incomeToggle.addEventListener("change", function() {
                    if (this.value === "yes") {
                        incomeAmountContainer.style.display = "block";
                    } else {
                        incomeAmountContainer.style.display = "none";
                        if (incomeInput) incomeInput.value = '';
                    }
                });

                // Trigger change on load to ensure correct visibility
                incomeToggle.dispatchEvent(new Event("change"));
            }
        });
    </script>
@endsection
