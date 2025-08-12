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
        <!--begin::Content-->
        <div class="flex-row-fluid ml-lg-8">
            <!--begin::Card-->
            <div class="card card-custom">
                <div class="card-body p-0">
                    <!--begin: Wizard-->
                    <div class="wizard wizard-2" id="kt_wizard" data-wizard-state="step-first"
                        data-wizard-clickable="false">
                        <!--begin: Wizard Nav-->
                        <div class="wizard-nav border-right py-8 px-8 py-lg-20 px-lg-10">
                            <!--begin::Wizard Step 1 Nav-->
                            <div class="wizard-steps">
                                <div class="wizard-step" data-wizard-type="step">
                                    <div class="wizard-wrapper">
                                        <div class="wizard-icon">
                                            <span class="svg-icon svg-icon-2x">
                                                <!--begin::Svg Icon | path:assets/media/svg/icons/Map/Compass.svg-->
                                                <svg xmlns="http://www.w3.org/2000/svg"
                                                    xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px"
                                                    viewBox="0 0 24 24" version="1.1">
                                                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                        <polygon points="0 0 24 0 24 24 0 24" />
                                                        <path
                                                            d="M12,11 C9.790861,11 8,9.209139 8,7 C8,4.790861 9.790861,3 12,3 C14.209139,3 16,4.790861 16,7 C16,9.209139 14.209139,11 12,11 Z"
                                                            fill="#000000" fill-rule="nonzero" opacity="0.3" />
                                                        <path
                                                            d="M3.00065168,20.1992055 C3.38825852,15.4265159 7.26191235,13 11.9833413,13 C16.7712164,13 20.7048837,15.2931929 20.9979143,20.2 C21.0095879,20.3954741 20.9979143,21 20.2466999,21 C16.541124,21 11.0347247,21 3.72750223,21 C3.47671215,21 2.97953825,20.45918 3.00065168,20.1992055 Z"
                                                            fill="#000000" fill-rule="nonzero" />
                                                    </g>
                                                </svg>
                                                <!--end::Svg Icon-->
                                            </span>
                                        </div>
                                        <div class="wizard-label">
                                            <h3 class="wizard-title">{{ __('Personal Information') }}</h3>
                                            <div class="wizard-desc">{{ __('Update your personal information') }}</div>
                                        </div>
                                    </div>
                                </div>
                                <!--end::Wizard Step 1 Nav-->
                                <!--begin::Wizard Step 2 Nav-->
                                <div class="wizard-step" data-wizard-type="step" data-wizard-state="current">
                                    <div class="wizard-wrapper">
                                        <div class="wizard-icon">
                                            <span class="svg-icon svg-icon-2x">
                                                <!--begin::Svg Icon | path:assets/media/svg/icons/General/User.svg-->
                                                <svg xmlns="http://www.w3.org/2000/svg"
                                                    xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px"
                                                    viewBox="0 0 24 24" version="1.1">
                                                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                        <polygon points="0 0 24 0 24 24 0 24" />
                                                        <path
                                                            d="M12.9336061,16.072447 L19.36,10.9564761 L19.5181585,10.8312381 C20.1676248,10.3169571 20.2772143,9.3735535 19.7629333,8.72408713 C19.6917232,8.63415859 19.6104327,8.55269514 19.5206557,8.48129411 L12.9336854,3.24257445 C12.3871201,2.80788259 11.6128799,2.80788259 11.0663146,3.24257445 L4.47482784,8.48488609 C3.82645598,9.00054628 3.71887192,9.94418071 4.23453211,10.5925526 C4.30500305,10.6811601 4.38527899,10.7615046 4.47382636,10.8320511 L4.63,10.9564761 L11.0659024,16.0730648 C11.6126744,16.5077525 12.3871218,16.5074963 12.9336061,16.072447 Z"
                                                            fill="#000000" fill-rule="nonzero" />
                                                        <path
                                                            d="M11.0563554,18.6706981 L5.33593024,14.122919 C4.94553994,13.8125559 4.37746707,13.8774308 4.06710397,14.2678211 C4.06471678,14.2708238 4.06234874,14.2738418 4.06,14.2768747 L4.06,14.2768747 C3.75257288,14.6738539 3.82516916,15.244888 4.22214834,15.5523151 C4.22358765,15.5534297 4.2250303,15.55454 4.22647627,15.555646 L11.0872776,20.8031356 C11.6250734,21.2144692 12.371757,21.2145375 12.909628,20.8033023 L19.7677785,15.559828 C20.1693192,15.2528257 20.2459576,14.6784381 19.9389553,14.2768974 C19.9376429,14.2751809 19.9363245,14.2734691 19.935,14.2717619 L19.935,14.2717619 C19.6266937,13.8743807 19.0546209,13.8021712 18.6572397,14.1104775 C18.654352,14.112718 18.6514778,14.1149757 18.6486172,14.1172508 L12.9235044,18.6705218 C12.377022,19.1051477 11.6029199,19.1052208 11.0563554,18.6706981 Z"
                                                            fill="#000000" opacity="0.3" />
                                                    </g>
                                                </svg>
                                                <!--end::Svg Icon-->
                                            </span>
                                        </div>
                                        <div class="wizard-label">
                                            <h3 class="wizard-title">{{ __('Status Overview') }}</h3>
                                            <div class="wizard-desc">{{ __('Update overview information') }}</div>
                                        </div>
                                    </div>
                                </div>
                                <!--end::Wizard Step 2 Nav-->
                                <!--begin::Wizard Step 3 Nav-->
                                <div class="wizard-step" data-wizard-type="step">
                                    <div class="wizard-wrapper">
                                        <div class="wizard-icon">
                                            <span class="svg-icon svg-icon-2x">
                                                <!--begin::Svg Icon | path:assets/media/svg/icons/General/Thunder-move.svg-->
                                                <svg xmlns="http://www.w3.org/2000/svg"
                                                    xmlns:xlink="http://www.w3.org/1999/xlink" width="24px"
                                                    height="24px" viewBox="0 0 24 24" version="1.1">
                                                    <g stroke="none" stroke-width="1" fill="none"
                                                        fill-rule="evenodd">
                                                        <rect x="0" y="0" width="24" height="24" />
                                                        <path
                                                            d="M16.3740377,19.9389434 L22.2226499,11.1660251 C22.4524142,10.8213786 22.3592838,10.3557266 22.0146373,10.1259623 C21.8914367,10.0438285 21.7466809,10 21.5986122,10 L17,10 L17,4.47708173 C17,4.06286817 16.6642136,3.72708173 16.25,3.72708173 C15.9992351,3.72708173 15.7650616,3.85240758 15.6259623,4.06105658 L9.7773501,12.8339749 C9.54758575,13.1786214 9.64071616,13.6442734 9.98536267,13.8740377 C10.1085633,13.9561715 10.2533191,14 10.4013878,14 L15,14 L15,19.5229183 C15,19.9371318 15.3357864,20.2729183 15.75,20.2729183 C16.0007649,20.2729183 16.2349384,20.1475924 16.3740377,19.9389434 Z"
                                                            fill="#000000" />
                                                        <path
                                                            d="M4.5,5 L9.5,5 C10.3284271,5 11,5.67157288 11,6.5 C11,7.32842712 10.3284271,8 9.5,8 L4.5,8 C3.67157288,8 3,7.32842712 3,6.5 C3,5.67157288 3.67157288,5 4.5,5 Z M4.5,17 L9.5,17 C10.3284271,17 11,17.6715729 11,18.5 C11,19.3284271 10.3284271,20 9.5,20 L4.5,20 C3.67157288,20 3,19.3284271 3,18.5 C3,17.6715729 3.67157288,17 4.5,17 Z M2.5,11 L6.5,11 C7.32842712,11 8,11.6715729 8,12.5 C8,13.3284271 7.32842712,14 6.5,14 L2.5,14 C1.67157288,14 1,13.3284271 1,12.5 C1,11.6715729 1.67157288,11 2.5,11 Z"
                                                            fill="#000000" opacity="0.3" />
                                                    </g>
                                                </svg>
                                                <!--end::Svg Icon-->
                                            </span>
                                        </div>
                                        <div class="wizard-label">
                                            <h3 class="wizard-title">{{ __('Deductions') }}</h3>
                                            <div class="wizard-desc">{{ __('Update deduction information') }}</div>
                                        </div>
                                    </div>
                                </div>
                                <!--end::Wizard Step 3 Nav-->
                                <!--begin::Wizard Step 4 Nav-->
                                <div class="wizard-step" data-wizard-type="step">
                                    <div class="wizard-wrapper">
                                        <div class="wizard-icon">
                                            <span class="svg-icon svg-icon-2x">
                                                <!--begin::Svg Icon | path:assets/media/svg/icons/General/Like.svg-->
                                                <svg xmlns="http://www.w3.org/2000/svg"
                                                    xmlns:xlink="http://www.w3.org/1999/xlink" width="24px"
                                                    height="24px" viewBox="0 0 24 24" version="1.1">
                                                    <g stroke="none" stroke-width="1" fill="none"
                                                        fill-rule="evenodd">
                                                        <rect x="0" y="0" width="24" height="24" />
                                                        <path
                                                            d="M9,10 L9,19 L10.1525987,19.3841996 C11.3761964,19.7920655 12.6575468,20 13.9473319,20 L17.5405883,20 C18.9706314,20 20.2018758,18.990621 20.4823303,17.5883484 L21.231529,13.8423552 C21.5564648,12.217676 20.5028146,10.6372006 18.8781353,10.3122648 C18.6189212,10.260422 18.353992,10.2430672 18.0902299,10.2606513 L14.5,10.5 L14.8641964,6.49383981 C14.9326895,5.74041495 14.3774427,5.07411874 13.6240179,5.00562558 C13.5827848,5.00187712 13.5414031,5 13.5,5 L13.5,5 C12.5694044,5 11.7070439,5.48826024 11.2282564,6.28623939 L9,10 Z"
                                                            fill="#000000" />
                                                        <rect fill="#000000" opacity="0.3" x="2" y="9" width="5"
                                                            height="11" rx="1" />
                                                    </g>
                                                </svg>
                                                <!--end::Svg Icon-->
                                            </span>
                                        </div>
                                        <div class="wizard-label">
                                            <h3 class="wizard-title">{{ __('Completed!') }}</h3>
                                            <div class="wizard-desc">{{ __('Review and Submit') }}</div>
                                        </div>
                                    </div>
                                </div>
                                <!--end::Wizard Step 4 Nav-->
                            </div>
                        </div>
                        <!--end: Wizard Nav-->
                        <!--begin: Wizard Body-->
                        <div class="wizard-body py-8 px-8 py-lg-20 px-lg-10">
                            <!--begin: Wizard Form-->
                            <div class="row">
                                <div class="col-xxl-12">
                                    <form class="form" id="createUser" action="{{ route('employees.store') }}"
                                        method="post">
                                        @csrf
                                        <!--begin: Wizard Step 1-->
                                        <div class="pb-5" data-wizard-type="step-content" data-wizard-state="current">
                                            <h4 class="mb-10 font-weight-bold text-dark">
                                                {{ __('Enter Personal Information') }}</h4>

                                            <!-- First Name & Last Name -->
                                            <div class="row">
                                                <div class="col-xl-6">
                                                    <div class="form-group">
                                                        <label>{{ __('First Name') }}</label>
                                                        <input class="form-control form-control-lg form-control-solid"
                                                            type="text" name="name"
                                                            placeholder="{{ __('First Name') }}"
                                                            value="{{ old('name') }}" />
                                                    </div>
                                                </div>
                                                <div class="col-xl-6">
                                                    <div class="form-group">
                                                        <label>{{ __('Last Name') }}</label>
                                                        <input class="form-control form-control-lg form-control-solid"
                                                            type="text" name="surname"
                                                            placeholder="{{ __('Last Name') }}"
                                                            value="{{ old('surname') }}" />
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Nationality & Phone -->
                                            <div class="row">
                                                <div class="col-xl-6">
                                                    <div class="form-group">
                                                        <label>{{ __('Nationality') }}</label>
                                                        <select
                                                            class="form-control select2-nationality form-control-lg form-control-solid"
                                                            name="nationality">
                                                            <option value=""></option>
                                                            @foreach ($countries as $country)
                                                                <option value="{{ $country->id }}"
                                                                    {{ old('nationality') == $country->id ? 'selected' : '' }}>
                                                                    {{ session('locale') == 'en' ? $country->name_en : $country->name_de }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-xl-6">
                                                    <div class="form-group">
                                                        <label>{{ __('Phone') }}</label>
                                                        <input type="tel"
                                                            class="form-control form-control-solid form-control-lg"
                                                            name="phone" placeholder="{{ __('Phone') }}"
                                                            value="{{ old('phone') }}" />
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Email & DOB -->
                                            <div class="row">
                                                <div class="col-xl-6">
                                                    <div class="form-group">
                                                        <label>{{ __('Email') }}</label>
                                                        <input type="email"
                                                            class="form-control form-control-solid form-control-lg"
                                                            name="email" placeholder="{{ __('Email') }}"
                                                            value="{{ old('email') }}" />
                                                    </div>
                                                </div>
                                                <div class="col-xl-6">
                                                    <div class="form-group">
                                                        <label>{{ __('DOB') }}</label>
                                                        <input type="date"
                                                            class="form-control form-control-lg form-control-solid"
                                                            name="DOB" value="{{ old('DOB') }}" />
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Gender & Married -->
                                            <div class="row">
                                                <div class="col-xl-6">
                                                    <div class="form-group">
                                                        <label>{{ __('Gender') }}</label>
                                                        <select class="form-control form-control-lg form-control-solid"
                                                            name="gender">
                                                            <option value=""></option>
                                                            <option value="0"
                                                                {{ old('gender') == '0' ? 'selected' : '' }}>
                                                                {{ __('Male') }}</option>
                                                            <option value="1"
                                                                {{ old('gender') == '1' ? 'selected' : '' }}>
                                                                {{ __('Female') }}</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-xl-6">
                                                    <div class="form-group">
                                                        <label>{{ __('Married') }}</label>
                                                        <select class="form-control form-control-lg form-control-solid"
                                                            name="maried">
                                                            <option value=""></option>
                                                            <option value="1"
                                                                {{ old('maried') == '1' ? 'selected' : '' }}>
                                                                {{ __('Yes') }}</option>
                                                            <option value="0"
                                                                {{ old('maried') == '0' ? 'selected' : '' }}>
                                                                {{ __('No') }}</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Street & PLZ -->
                                            <div class="row">
                                                <div class="col-xl-6">
                                                    <div class="form-group">
                                                        <label>{{ __('Street') }}</label>
                                                        <input type="text"
                                                            class="form-control form-control-solid form-control-lg"
                                                            name="strasse" placeholder="{{ __('Street') }}"
                                                            value="{{ old('strasse') }}" />
                                                    </div>
                                                </div>
                                                <div class="col-xl-6">
                                                    <div class="form-group">
                                                        <label>{{ __('PLZ') }}</label>
                                                        <input type="text"
                                                            class="form-control form-control-solid form-control-lg"
                                                            name="PLZ" placeholder="{{ __('PLZ') }}"
                                                            value="{{ old('PLZ') }}" />
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- ORT1 & Canton -->
                                            <div class="row">
                                                <div class="col-xl-6">
                                                    <div class="form-group">
                                                        <label>{{ __('ORT') }}</label>
                                                        <input type="text"
                                                            class="form-control form-control-solid form-control-lg"
                                                            name="ORT1" placeholder="{{ __('ORT') }}"
                                                            value="{{ old('ORT1') }}" />
                                                    </div>
                                                </div>
                                                <div class="col-xl-6">
                                                    <div class="form-group">
                                                        <label>{{ __('Canton') }}</label>
                                                        <select class="form-control form-control-lg form-control-solid"
                                                            name="ORT">
                                                            <option value=""></option>
                                                            @foreach (Config::get('constants.kantons') as $key => $canton)
                                                                <option value="{{ $key }}"
                                                                    {{ old('ORT') == $key ? 'selected' : '' }}>
                                                                    {{ $canton }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- AHV & TAX -->
                                            <div class="row">
                                                <div class="col-xl-6">
                                                    <div class="form-group">
                                                        <label>{{ __('AHV') }}</label>
                                                        <input type="text"
                                                            class="form-control form-control-solid form-control-lg"
                                                            name="AHV" placeholder="{{ __('AHV') }}"
                                                            value="{{ old('AHV') }}" />
                                                    </div>
                                                </div>
                                                <div class="col-xl-6">
                                                    <div class="form-group">
                                                        <label>{{ __('TAX') }}</label>
                                                        <select class="form-control form-control-solid form-control-lg"
                                                            name="TAX_TOGGLE" id="taxToggle">
                                                            <option value=""></option>
                                                            <option value="no"
                                                                {{ old('TAX_TOGGLE', empty(old('TAX')) ? 'no' : 'yes') == 'no' ? 'selected' : '' }}>
                                                                {{ __('No') }}</option>
                                                            <option value="yes"
                                                                {{ old('TAX_TOGGLE', empty(old('TAX')) ? 'no' : 'yes') == 'yes' ? 'selected' : '' }}>
                                                                {{ __('Yes') }}</option>
                                                        </select>
                                                        <div id="taxInputContainer" class="mt-2"
                                                            style="{{ old('TAX_TOGGLE', empty(old('TAX')) ? 'no' : 'yes') == 'yes' ? '' : 'display:none;' }}">
                                                            <input type="text"
                                                                class="form-control form-control-solid form-control-lg"
                                                                name="TAX" placeholder="{{ __('TAX Code') }}"
                                                                value="{{ old('TAX') }}" />
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Bank & IBAN -->
                                            <div class="row">
                                                <div class="col-xl-6">
                                                    <div class="form-group">
                                                        <label>{{ __('Bank') }}</label>
                                                        <input type="text"
                                                            class="form-control form-control-solid form-control-lg"
                                                            name="bankname" placeholder="{{ __('Bank') }}"
                                                            value="{{ old('bankname') }}" />
                                                    </div>
                                                </div>
                                                <div class="col-xl-6">
                                                    <div class="form-group">
                                                        <label>{{ __('IBAN') }}</label>
                                                        <input type="text"
                                                            class="form-control form-control-solid form-control-lg"
                                                            name="IBAN" placeholder="{{ __('IBAN') }}"
                                                            value="{{ old('IBAN') }}" />
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- PIN & Additional Income -->
                                            <div class="row">
                                                <div class="col-xl-6">
                                                    <div class="form-group">
                                                        <label>{{ __('PIN') }}</label>
                                                        <input type="number" min="1000" max="9999"
                                                            class="form-control form-control-solid form-control-lg"
                                                            name="pin" placeholder="{{ __('PIN') }}"
                                                            value="{{ old('pin') }}" />
                                                    </div>
                                                </div>
                                                <div class="col-xl-6">
                                                    <div class="form-group">
                                                        <label>{{ __('Additional Income (RAV)') }}</label>
                                                        <select class="form-control form-control-solid form-control-lg"
                                                            name="additional_income_toggle" id="additionalIncomeToggle">
                                                            <option value=""></option>
                                                            <option value="no"
                                                                {{ old('additional_income_toggle') == 'no' ? 'selected' : '' }}>
                                                                {{ __('No') }}</option>
                                                            <option value="yes"
                                                                {{ old('additional_income_toggle') == 'yes' ? 'selected' : '' }}>
                                                                {{ __('Yes') }}</option>
                                                        </select>
                                                    </div>
                                                    <div id="additionalIncomeAmountContainer"
                                                        style="{{ old('additional_income_toggle') == 'yes' ? '' : 'display:none;' }}">
                                                        <div class="form-group">
                                                            <label>{{ __('Additional Income Amount') }}</label>
                                                            <input type="number" min="0" step="0.01"
                                                                class="form-control form-control-solid form-control-lg"
                                                                name="additional_income"
                                                                placeholder="{{ __('Enter Amount') }}"
                                                                value="{{ old('additional_income') }}" />
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Married Since & Religion -->
                                            <div class="row">
                                                <div class="col-xl-6">
                                                    <div class="form-group">
                                                        <label>{{ __('Married Since') }}</label>
                                                        <input type="date"
                                                            class="form-control form-control-solid form-control-lg"
                                                            name="married_since" value="{{ old('married_since') }}" />
                                                    </div>
                                                </div>
                                                <div class="col-xl-6">
                                                    <div class="form-group">
                                                        <label>{{ __('Religious Affiliation') }}</label>
                                                        <input type="text"
                                                            class="form-control form-control-solid form-control-lg"
                                                            name="religion"
                                                            placeholder="{{ __('Religious Affiliation') }}"
                                                            value="{{ old('religion') }}" />
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Children & Work Permit -->
                                            <div class="row">
                                                <div class="col-xl-6">
                                                    <div class="form-group">
                                                        <label>{{ __('Children') }}</label>
                                                        <input type="number" min="0"
                                                            class="form-control form-control-solid form-control-lg"
                                                            name="children" placeholder="{{ __('Number of Children') }}"
                                                            value="{{ old('children') }}" />
                                                    </div>
                                                </div>
                                                <div class="col-xl-6">
                                                    <div class="form-group">
                                                        <label>{{ __('Work Permit') }}</label>
                                                        <select class="form-control form-control-solid form-control-lg"
                                                            name="work_permit">
                                                            <option value=""></option>
                                                            <option value="L"
                                                                {{ old('work_permit') == 'L' ? 'selected' : '' }}>L
                                                            </option>
                                                            <option value="B"
                                                                {{ old('work_permit') == 'B' ? 'selected' : '' }}>B
                                                            </option>
                                                            <option value="C"
                                                                {{ old('work_permit') == 'C' ? 'selected' : '' }}>C
                                                            </option>
                                                            <option value="S"
                                                                {{ old('work_permit') == 'S' ? 'selected' : '' }}>S
                                                            </option>
                                                            <option value="CH"
                                                                {{ old('work_permit') == 'CH' ? 'selected' : '' }}>CH
                                                            </option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Work Permit Expiry (alone in last row, full-width or add another field if needed) -->
                                            <div class="row">
                                                <div class="col-xl-6">
                                                    <div class="form-group">
                                                        <label>{{ __('Work Permit Expiration Date') }}</label>
                                                        <input type="date"
                                                            class="form-control form-control-solid form-control-lg"
                                                            name="work_permit_expiry"
                                                            value="{{ old('work_permit_expiry') }}" />
                                                    </div>
                                                </div>
                                                <div class="col-xl-6"></div> <!-- Empty to complete the row layout -->
                                            </div>
                                        </div>


                                        <!--end: Wizard Step 1-->
                                        <!--begin: Wizard Step 2-->
                                        <div class="pb-5" data-wizard-type="step-content">
                                            <h4 class="mb-10 font-weight-bold text-dark">
                                                {{ __('Enter Overview Information') }}</h4>
                                            <div class="row">
                                                <div class="col-xl-6">
                                                    <div class="form-group">
                                                        <label>{{ __('Sage ID') }}</label>
                                                        <input type="number"
                                                            class="form-control form-control-solid form-control-lg"
                                                            name="sage_number" placeholder="{{ __('Sage ID') }}"
                                                            value="{{ old('sage_number') }}" />
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <label
                                                    class="col-xl-3 col-lg-3 col-form-label">{{ __('Api monitoring') }}</label>
                                                <div class="col-lg-9 col-xl-6 d-flex">
                                                    <span class="switch switch-sm">
                                                        <label>
                                                            <input type="checkbox" name="api_monitoring"
                                                                {{ old('api_monitoring') ? 'checked' : '' }} />
                                                            <span></span>
                                                        </label>
                                                    </span>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-xl-6">
                                                    <div class="form-group">
                                                        <label>{{ __('Function') }}</label>
                                                        <select class="form-control form-control-lg form-control-solid"
                                                            name="function">
                                                            @foreach (Config::get('constants.functions') as $key => $function)
                                                                <option value="{{ $key }}"
                                                                    {{ old('function') == $key ? 'selected' : '' }}>
                                                                    {{ $function }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-xl-6">
                                                    <div class="form-group">
                                                        <label>{{ __('Access Management') }}</label>
                                                        <select class="form-control form-control-lg form-control-solid"
                                                            name="role">
                                                            @foreach (Config::get('constants.functions') as $key => $function)
                                                                <option value="{{ $key }}"
                                                                    {{ old('function') == $key ? 'selected' : '' }}>
                                                                    {{ $function }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-xl-6">
                                                    <div class="form-group">
                                                        <label>{{ __('Payment method') }}</label>
                                                        <select class="form-control form-control-lg form-control-solid"
                                                            name="PartTime">
                                                            @foreach (Config::get('constants.part_time') as $key => $part_time)
                                                                <option value="{{ $key }}"
                                                                    {{ old('PartTime') == $key ? 'selected' : '' }}>
                                                                    {{ $part_time }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-xl-6">
                                                    <div class="form-group">
                                                        <label>{{ __('Nightshift') }}</label>
                                                        <div class="checkbox-inline py-4">
                                                            <label class="checkbox">
                                                                <input type="checkbox" name="noqnaSmena"
                                                                    {{ old('noqnaSmena') == 'on' ? 'checked' : '' }} />
                                                                <span></span>{{ __('Nightshift') }}
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-xl-12">
                                                    <div class="form-group">
                                                        <label>Hotels</label>
                                                        <div class="checkbox-inline">
                                                            @foreach ($devices as $device)
                                                                <label class="checkbox">
                                                                    <input type="checkbox" name="locations[]"
                                                                        {{ is_array(old('locations')) && in_array($device->id, old('locations')) ? 'checked' : '' }}
                                                                        value="{{ $device->id }}" />
                                                                    <span></span><b>{{ $device->name }}</b>
                                                                </label>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!--end: Wizard Step 2-->
                                        <!--begin: Wizard Step 3-->
                                        <div class="pb-5" data-wizard-type="step-content">
                                            <h4 class="mb-10 font-weight-bold text-dark">
                                                {{ __('Enter Deduction Information') }}</h4>

                                            <div class="row">
                                                <div class="col-xl-6">
                                                    <div class="form-group hidden">
                                                        <label>{{ __('Hourly rate') }}</label>
                                                        <input class="form-control form-control-lg form-control-solid"
                                                            type="number" name="EhChf" placeholder="CHF"
                                                            value="{{ old('EhChf') }}" />
                                                    </div>
                                                    <div class="form-group">
                                                        <label>{{ __('Monthly wage') }}</label>
                                                        <input class="form-control form-control-lg form-control-solid"
                                                            type="number" name="rroga" placeholder="CHF"
                                                            value="{{ old('rroga') }}" />
                                                    </div>
                                                </div>
                                                <div class="col-xl-6">
                                                    <div class="form-group">
                                                        <label>{{ __('Apply for Child Allowance') }}</label>
                                                        <select class="form-control form-control-solid form-control-lg"
                                                            name="child_allowance" id="child_allowance">
                                                            <option value=""></option>
                                                            <option value="yes"
                                                                {{ old('child_allowance') == 'yes' ? 'selected' : '' }}>
                                                                {{ __('Yes') }}</option>
                                                            <option value="no"
                                                                {{ old('child_allowance') == 'no' ? 'selected' : '' }}>
                                                                {{ __('No') }}</option>
                                                            <option value="not_yet"
                                                                {{ old('child_allowance') == 'not_yet' ? 'selected' : '' }}>
                                                                {{ __('Not Yet') }}</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row" id="allowance_fields" style="display: none;">
                                                <div class="col-xl-6">
                                                    <div class="form-group">
                                                        <label>{{ __('Education allowance 250') }}</label>
                                                        <input class="form-control form-control-lg form-control-solid"
                                                            type="number" name="decki250" id="decki250"
                                                            placeholder="{{ __('Education allowance 250') }}"
                                                            value="{{ old('decki250') }}" />
                                                    </div>
                                                </div>
                                                <div class="col-xl-6">
                                                    <div class="form-group">
                                                        <label>{{ __('Child allowance 200') }}</label>
                                                        <input class="form-control form-control-lg form-control-solid"
                                                            type="number" name="decki200" id="decki200"
                                                            placeholder="{{ __('Child allowance 200') }}"
                                                            value="{{ old('decki200') }}" />
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-xl-6">
                                                    <div class="form-group">
                                                        <label>{{ __('BVG') }}</label>
                                                        <input class="form-control form-control-lg form-control-solid"
                                                            type="number" name="BVG"
                                                            placeholder="{{ __('BVG') }}"
                                                            value="{{ old('BVG') }}" />
                                                    </div>
                                                </div>
                                                <div class="col-xl-6">
                                                    <div class="form-group">
                                                        <label>{{ __('Holiday Compensation 1') }}</label>
                                                        <input class="form-control form-control-lg form-control-solid"
                                                            type="number" name="Perqind1" placeholder="%"
                                                            value="{{ old('Perqind1') }}" />
                                                    </div>
                                                </div>

                                            </div>
                                            <div class="row">
                                                <div class="col-xl-6">
                                                    <div class="form-group">
                                                        <label>{{ __('Holiday Compensation 2') }}</label>
                                                        <input class="form-control form-control-lg form-control-solid"
                                                            type="number" name="Perqind2" placeholder="%"
                                                            value="{{ old('Perqind2') }}" />
                                                    </div>
                                                </div>
                                                <div class="col-xl-6">
                                                    <div class="form-group">
                                                        <label>{{ __('13th monthly salary') }}</label>
                                                        <input class="form-control form-control-lg form-control-solid"
                                                            type="number" name="Perqind3" placeholder="%"
                                                            value="{{ old('Perqind3') }}" />
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-xl-6">
                                                    <div class="form-group">
                                                        <label>{{ __('Entry date') }}</label>
                                                        <input class="form-control form-control-lg form-control-solid"
                                                            type="date" name="start"
                                                            value="{{ old('start') }}" />
                                                    </div>
                                                </div>
                                                <div class="col-xl-6">
                                                    <div class="form-group">
                                                        <label>{{ __('Exit date') }}</label>
                                                        <input class="form-control form-control-lg form-control-solid"
                                                            type="date" name="end"
                                                            value="{{ old('end') }}" />
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-xl-4">
                                                    <div class="form-group">
                                                        <label>{{ __('Old holiday balance') }}</label>
                                                        <input class="form-control form-control-lg form-control-solid"
                                                            type="number" name="oldSaldoF"
                                                            placeholder="{{ __('Old holiday balance') }}"
                                                            value="{{ old('oldSaldoF') }}" />
                                                    </div>
                                                </div>
                                                <div class="col-xl-4">
                                                    <div class="form-group">
                                                        <label>{{ __('Old balance 13') }}</label>
                                                        <input class="form-control form-control-lg form-control-solid"
                                                            type="number" name="oldSaldo13"
                                                            placeholder="{{ __('Old balance 13') }}"
                                                            value="{{ old('oldSaldo13') }}" />
                                                    </div>
                                                </div>
                                                <div class="col-xl-4">
                                                    <div class="form-group">
                                                        <label>{{ __('Work Percentage') }}</label>
                                                        <input class="form-control form-control-lg form-control-solid"
                                                            type="number" step="0.01" name="work_percetage"
                                                            placeholder="{{ __('Work Percentage') }}"
                                                            value="{{ old('work_percetage') }}" />
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!--end: Wizard Step 3-->
                                        <!--begin: Wizard Step 4-->
                                        <div class="pb-5" data-wizard-type="step-content">
                                            <!--begin::Section-->
                                            <h4 class="mb-10 font-weight-bold text-dark">
                                                {{ __('Review your Details and Submit') }}</h4>
                                            <h6 class="font-weight-bolder mb-3">{{ __('Personal Information') }}:</h6>
                                            <div id="review-personal" class="text-dark-50 line-height-lg">
                                                <div>&nbsp;</div>
                                            </div>
                                            <div class="separator separator-dashed my-5"></div>
                                            <!--end::Section-->
                                            <!--begin::Section-->
                                            <h6 class="font-weight-bolder mb-3">{{ __('Status Overview') }}:</h6>
                                            <div id="review-status" class="text-dark-50 line-height-lg">
                                                <div>&nbsp;</div>
                                            </div>
                                            <div class="separator separator-dashed my-5"></div>
                                            <!--end::Section-->
                                            <!--begin::Section-->
                                            <h6 class="font-weight-bolder mb-3">{{ __('Deductions') }}:</h6>
                                            <div id="review-deduction" class="text-dark-50 line-height-lg">
                                                <div>&nbsp;</div>
                                            </div>
                                            <div class="separator separator-dashed my-5"></div>
                                            <!--end::Section-->
                                        </div>
                                        <!--end: Wizard Step 4-->
                                        <!--begin: Wizard Actions-->
                                        <div class="d-flex justify-content-between border-top mt-5 pt-10">
                                            <div class="mr-2">
                                                <button type="button"
                                                    class="btn btn-light-primary font-weight-bolder text-uppercase px-9 py-4"
                                                    data-wizard-type="action-prev">{{ __('Previous') }}</button>
                                            </div>
                                            <div>
                                                {{-- <button type="submit" class="btn btn-success font-weight-bolder text-uppercase px-9 py-4">Submit</button> --}}
                                                <button type="button" id="finalSubmit"
                                                    class="btn btn-success font-weight-bolder text-uppercase px-9 py-4"
                                                    data-wizard-type="action-submit">{{ __('Submit') }}</button>
                                                <button type="button"
                                                    class="btn btn-primary font-weight-bolder text-uppercase px-9 py-4"
                                                    data-wizard-type="action-next">{{ __('Next') }}</button>
                                            </div>
                                        </div>
                                        <!--end: Wizard Actions-->
                                    </form>
                                </div>
                                <!--end: Wizard-->
                            </div>
                            <!--end: Wizard Form-->
                        </div>
                        <!--end: Wizard Body-->
                    </div>
                    <!--end: Wizard-->
                </div>
            </div>
        </div>
        <!--end::Content-->
    </div>
    <!--end::Profile Overview-->

@endsection

{{-- Styles Section --}}
@section('styles')
    <link href="{{ asset('css/pages/wizard/wizard-2.css') }}" rel="stylesheet" type="text/css" />
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
    {{-- page scripts --}}
    <script src="{{ mix('js/pages/employees/create.js') }}" type="text/javascript"></script>
    <script src="{{ mix('js/app.js') }}" type="text/javascript"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
@endsection
