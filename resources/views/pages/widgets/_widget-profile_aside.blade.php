@php
  use Carbon\Carbon;
@endphp
<!--begin::Aside-->
<div class="flex-row-auto offcanvas-mobile w-300px w-xl-350px" id="kt_profile_aside">
  <!--begin::Profile Card-->
  <div class="card card-custom card-stretch">
    <!--begin::Body-->
    <div class="card-body pt-4">

      <!--begin::User-->
      <div class="pb-5">
      {{-- <div class="d-flex align-items-center pb-5"> --}}
        {{-- <div class="symbol symbol-60 symbol-xxl-100 mr-5 align-self-start align-self-xxl-center">
          <div class="symbol-label" style="background-image:url('/media/users/default.jpg')"></div>
        </div> --}}
        <div>
          <a href="#" class="font-weight-bolder font-size-h5 text-dark-75 text-hover-primary">{{ $employee->fullname }}</a>
          <div class="text-muted">
            <td>
            @switch( $employee->roli )
                @case(0)
                    Gouvernante
                    @break
                @case(1)
                    Raumpflegerinnen
                    @break
                @case(2)
                    Unterhalt
                    @break
                @case(3)
                    Stewarding
                    @break
                @default
                    Not Set
            @endswitch
            </td>
          </div>

          <!--begin::Contact-->
          <div class="d-flex align-items-center justify-content-between">
            <span class="font-weight-bold mr-2">ID:</span>
            <span class="text-muted">{{ $employee->id }}</span>
          </div>
          <div class="d-flex align-items-center justify-content-between">
            <span class="font-weight-bold mr-2">{{ __('Email') }}:</span>
            <a href="mailto:{{ $employee->email }}" class="text-muted text-hover-primary break-any">{{ $employee->email }}</a>
          </div>
          <div class="d-flex align-items-center justify-content-between">
            <span class="font-weight-bold mr-2">{{ __('Phone') }}:</span>
            <span class="text-muted">{{ $employee->phone }}</span>
          </div>
          <div class="d-flex align-items-center justify-content-between">
            <span class="font-weight-bold mr-2">{{ __('Location') }}:</span>
            <span class="text-muted">{{ $employee->ORT }}</span>
          </div>
          <div class="d-flex align-items-center justify-content-between">
            <span class="font-weight-bold mr-2">{{ __('Sage ID') }}:</span>
            <span class="text-muted">{{ $employee->sage_number }}</span>
          </div>
          <div class="d-flex align-items-center justify-content-between">
            <span class="font-weight-bold mr-2">{{ __('Work Percentage') }}:</span>
            <span class="text-muted">{{ $employee->work_percetage }}</span>
          </div>
          <!--end::Contact-->
        </div>
      </div>
      <!--end::User-->


      <!--begin::Nav-->
      <div class="navi navi-bold navi-hover navi-active navi-link-rounded">
        <div class="navi-item mb-2">
          <a href="{{ route('employees.show', $employee->id) }}" class="navi-link py-4 {{ $item_active == 'personal' ? 'active' : '' }}">
            <span class="navi-icon mr-2">
              <span class="svg-icon">
                <!--begin::Svg Icon | path:assets/media/svg/icons/General/User.svg-->
                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                  <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                    <polygon points="0 0 24 0 24 24 0 24" />
                    <path d="M12,11 C9.790861,11 8,9.209139 8,7 C8,4.790861 9.790861,3 12,3 C14.209139,3 16,4.790861 16,7 C16,9.209139 14.209139,11 12,11 Z" fill="#000000" fill-rule="nonzero" opacity="0.3" />
                    <path d="M3.00065168,20.1992055 C3.38825852,15.4265159 7.26191235,13 11.9833413,13 C16.7712164,13 20.7048837,15.2931929 20.9979143,20.2 C21.0095879,20.3954741 20.9979143,21 20.2466999,21 C16.541124,21 11.0347247,21 3.72750223,21 C3.47671215,21 2.97953825,20.45918 3.00065168,20.1992055 Z" fill="#000000" fill-rule="nonzero" />
                  </g>
                </svg>
                <!--end::Svg Icon-->
              </span>
            </span>
            <span class="navi-text font-size-lg">{{ __('Personal Information') }}</span>
          </a>
        </div>
        <div class="navi-item mb-2">
          <a href="{{ route('employees.overview', $employee->id) }}" class="navi-link py-4 {{ $item_active == 'overview' ? 'active' : '' }}">
            <span class="navi-icon mr-2">
              <span class="svg-icon">
                <!--begin::Svg Icon | path:assets/media/svg/icons/Design/Layers.svg-->
                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                  <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                    <polygon points="0 0 24 0 24 24 0 24" />
                    <path d="M12.9336061,16.072447 L19.36,10.9564761 L19.5181585,10.8312381 C20.1676248,10.3169571 20.2772143,9.3735535 19.7629333,8.72408713 C19.6917232,8.63415859 19.6104327,8.55269514 19.5206557,8.48129411 L12.9336854,3.24257445 C12.3871201,2.80788259 11.6128799,2.80788259 11.0663146,3.24257445 L4.47482784,8.48488609 C3.82645598,9.00054628 3.71887192,9.94418071 4.23453211,10.5925526 C4.30500305,10.6811601 4.38527899,10.7615046 4.47382636,10.8320511 L4.63,10.9564761 L11.0659024,16.0730648 C11.6126744,16.5077525 12.3871218,16.5074963 12.9336061,16.072447 Z" fill="#000000" fill-rule="nonzero" />
                    <path d="M11.0563554,18.6706981 L5.33593024,14.122919 C4.94553994,13.8125559 4.37746707,13.8774308 4.06710397,14.2678211 C4.06471678,14.2708238 4.06234874,14.2738418 4.06,14.2768747 L4.06,14.2768747 C3.75257288,14.6738539 3.82516916,15.244888 4.22214834,15.5523151 C4.22358765,15.5534297 4.2250303,15.55454 4.22647627,15.555646 L11.0872776,20.8031356 C11.6250734,21.2144692 12.371757,21.2145375 12.909628,20.8033023 L19.7677785,15.559828 C20.1693192,15.2528257 20.2459576,14.6784381 19.9389553,14.2768974 C19.9376429,14.2751809 19.9363245,14.2734691 19.935,14.2717619 L19.935,14.2717619 C19.6266937,13.8743807 19.0546209,13.8021712 18.6572397,14.1104775 C18.654352,14.112718 18.6514778,14.1149757 18.6486172,14.1172508 L12.9235044,18.6705218 C12.377022,19.1051477 11.6029199,19.1052208 11.0563554,18.6706981 Z" fill="#000000" opacity="0.3" />
                  </g>
                </svg>
                <!--end::Svg Icon-->
              </span>
            </span>
            <span class="navi-text font-size-lg">{{ __('Status Overview') }}</span>
          </a>
        </div>
        <div class="navi-item mb-2">
          <a href="{{ route('employees.deduction', $employee->id) }}" class="navi-link py-4 {{ $item_active == 'deduction' ? 'active' : '' }}">
            <span class="navi-icon mr-2">
              <span class="svg-icon">
                <!--begin::Svg Icon | path:assets/media/svg/icons/Communication/Shield-user.svg-->
                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                  <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                    <rect x="0" y="0" width="24" height="24" />
                    <path d="M4,4 L11.6314229,2.5691082 C11.8750185,2.52343403 12.1249815,2.52343403 12.3685771,2.5691082 L20,4 L20,13.2830094 C20,16.2173861 18.4883464,18.9447835 16,20.5 L12.5299989,22.6687507 C12.2057287,22.8714196 11.7942713,22.8714196 11.4700011,22.6687507 L8,20.5 C5.51165358,18.9447835 4,16.2173861 4,13.2830094 L4,4 Z" fill="#000000" opacity="0.3" />
                    <path d="M12,11 C10.8954305,11 10,10.1045695 10,9 C10,7.8954305 10.8954305,7 12,7 C13.1045695,7 14,7.8954305 14,9 C14,10.1045695 13.1045695,11 12,11 Z" fill="#000000" opacity="0.3" />
                    <path d="M7.00036205,16.4995035 C7.21569918,13.5165724 9.36772908,12 11.9907452,12 C14.6506758,12 16.8360465,13.4332455 16.9988413,16.5 C17.0053266,16.6221713 16.9988413,17 16.5815,17 C14.5228466,17 11.463736,17 7.4041679,17 C7.26484009,17 6.98863236,16.6619875 7.00036205,16.4995035 Z" fill="#000000" opacity="0.3" />
                  </g>
                </svg>
                <!--end::Svg Icon-->
              </span>
            </span>
            <span class="navi-text font-size-lg">{{ __('Deductions') }}</span>
          </a>
        </div>
        <div class="navi-item mb-2">
          <a href="{{ route('employees.insurance', $employee->id) }}" class="navi-link py-4 {{ $item_active == 'insurance' ? 'active' : '' }}">
            <span class="navi-icon mr-2">
              <span class="svg-icon">
                <!--begin::Svg Icon | path:assets/media/svg/icons/Shopping/Chart-line1.svg-->
                <svg width="24px" height="24px" viewBox="0 0 24 24" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                  <g id="Stockholm-icons-/-Shopping-/-Chart-line1" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                      <rect id="bound" x="0" y="0" width="24" height="24"></rect>
                      <path d="M5,19 L20,19 C20.5522847,19 21,19.4477153 21,20 C21,20.5522847 20.5522847,21 20,21 L4,21 C3.44771525,21 3,20.5522847 3,20 L3,4 C3,3.44771525 3.44771525,3 4,3 C4.55228475,3 5,3.44771525 5,4 L5,19 Z" id="Path-95" fill="#000000" fill-rule="nonzero"></path>
                      <path d="M8.7295372,14.6839411 C8.35180695,15.0868534 7.71897114,15.1072675 7.31605887,14.7295372 C6.9131466,14.3518069 6.89273254,13.7189711 7.2704628,13.3160589 L11.0204628,9.31605887 C11.3857725,8.92639521 11.9928179,8.89260288 12.3991193,9.23931335 L15.358855,11.7649545 L19.2151172,6.88035571 C19.5573373,6.44687693 20.1861655,6.37289714 20.6196443,6.71511723 C21.0531231,7.05733733 21.1271029,7.68616551 20.7848828,8.11964429 L16.2848828,13.8196443 C15.9333973,14.2648593 15.2823707,14.3288915 14.8508807,13.9606866 L11.8268294,11.3801628 L8.7295372,14.6839411 Z" id="Path-97" fill="#000000" fill-rule="nonzero" opacity="0.3"></path>
                  </g>
                </svg>
                <!--end::Svg Icon-->
              </span>
            </span>
            <span class="navi-text font-size-lg">{{ __('Interim Payment') }}</span>
          </a>
        </div>
        <div class="navi-item mb-2">
          <a href="{{ route('employees.files', $employee->id) }}" class="navi-link py-4 {{ $item_active == 'files' ? 'active' : '' }}">
            <span class="navi-icon mr-2">
              <span class="svg-icon">
                <!--begin::Svg Icon | path:assets/media/svg/icons/General/Folder.svg-->
                <svg width="24px" height="24px" viewBox="0 0 24 24" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                  <g id="Stockholm-icons-/-General-/-Folder" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                    <rect id="bound" x="0" y="0" width="24" height="24"></rect>
                    <path d="M3.5,20 L20.5,20 C21.3284271,20 22,19.3284271 22,18.5 L22,8.5 C22,7.67157288 21.3284271,7 20.5,7 L11,7 L8.43933983,4.43933983 C8.15803526,4.15803526 7.77650439,4 7.37867966,4 L3.5,4 C2.67157288,4 2,4.67157288 2,5.5 L2,18.5 C2,19.3284271 2.67157288,20 3.5,20 Z" id="Path-5" fill="#000000"></path>
                  </g>
                </svg>
                <!--end::Svg Icon-->
              </span>
            </span>
            <span class="navi-text font-size-lg">{{ __('File Manager') }}</span>
          </a>
        </div>
        <div class="navi-item mb-2">
          <a href="{{ route('employees.contracts', $employee->id) }}" class="navi-link py-4 {{ $item_active == 'contracts' ? 'active' : '' }}">
            <span class="navi-icon mr-2">
              <span class="svg-icon">
                <!--begin::Svg Icon | path:assets/media/svg/icons/General/Folder.svg-->
                <svg width="24px" height="24px" viewBox="0 0 24 24" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                  <g id="Stockholm-icons-/-General-/-Folder" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                    <rect id="bound" x="0" y="0" width="24" height="24"></rect>
                    <path d="M3.5,20 L20.5,20 C21.3284271,20 22,19.3284271 22,18.5 L22,8.5 C22,7.67157288 21.3284271,7 20.5,7 L11,7 L8.43933983,4.43933983 C8.15803526,4.15803526 7.77650439,4 7.37867966,4 L3.5,4 C2.67157288,4 2,4.67157288 2,5.5 L2,18.5 C2,19.3284271 2.67157288,20 3.5,20 Z" id="Path-5" fill="#000000"></path>
                  </g>
                </svg>
                <!--end::Svg Icon-->
              </span>
            </span>
            <span class="navi-text font-size-lg">{{ __('Contracts') }}</span>
          </a>
        </div>

        {{-- Separator --}}
    		<div class="separator separator-dashed mt-8 mb-5"></div>

        <div class="d-flex align-items-center pt-5">
          <div class="text-muted">{{ __('Select Payroll month') }}</div>
        </div>
        {{-- Begin buttons --}}
        <div class="pb-5">
          <div class="mt-2">
            <input class="form-control form-control-lg form-control-solid lohnMonthYear" type="month" name="date" value="">
            <div class="col-md-6">
            </div>
          </div>
        </div>
        {{-- End buttons --}}

        {{-- Separator --}}
    		<div class="separator separator-dashed mt-8 mb-5"></div>

        <div class="d-flex align-items-center pt-5">
          <div class="text-muted">{{ __('Set holiday') }}</div>
        </div>

        <div class="navi-item mb-2">
          <div class="form-group">
						<div class="input-group" id="kt_daterangepicker">
              <div class="input-group-append">
                <span class="input-group-text">
                  <i class="la la-calendar-check-o"></i>
                </span>
              </div>
              <input type="text" class="form-control vacation" name="vacation" readonly="readonly" placeholder="Select date range">
							<div class="input-group-append">
								<button class="btn btn-primary" id="btnVacation" type="button">{{ __('Go!') }}</button>
							</div>
						</div>
					</div>
        </div>

        <div class="employeeVacationData d-flex align-items-center pt-0">
          <div class="scroll scroll-pull position-relative" data-scroll="true" data-wheel-propagation="true" style="height: 250px">
            <table class="table userVacationTable">
                <thead>
                    <tr>
                        <th scope="col">{{ __('Days') }}</th>
                        <th scope="col">{{ __('Begin') }}</th>
                        <th scope="col">{{ __('End') }}</th>
                        <th scope="col">{{ __('Opt.') }}</th>
                    </tr>
                </thead>
                <tbody>
                  @foreach ($pushimi as $key => $value)
                    <tr>
                        <th scope="row">{{ $value->days }}</th>
                        <td>{{ $value->fillimi }}</td>
                        <td>{{ $value->mbarimi }}</td>
                        <td>
                          <form class="formDeleteVacation" action="{{ route('employees.vacation', $employee->id) }}" onsubmit="event.preventDefault();" method="post">
                            @csrf
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="date_start" value="{{ $value->fillimi }}">
                            <input type="hidden" name="date_end" value="{{ $value->mbarimi }}">
                            <input type="hidden" name="_method" value="DELETE">
                            <button type="submit" class="btnDeleteVacation btn btn-sm btn-clean btn-icon">
                              <span class="svg-icon svg-icon-md">
                                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                  <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                    <rect x="0" y="0" width="24" height="24"></rect>
                                    <path d="M6,8 L6,20.5 C6,21.3284271 6.67157288,22 7.5,22 L16.5,22 C17.3284271,22 18,21.3284271 18,20.5 L18,8 L6,8 Z" fill="#000000" fill-rule="nonzero"></path>
                                    <path d="M14,4.5 L14,4 C14,3.44771525 13.5522847,3 13,3 L11,3 C10.4477153,3 10,3.44771525 10,4 L10,4.5 L5.5,4.5 C5.22385763,4.5 5,4.72385763 5,5 L5,5.5 C5,5.77614237 5.22385763,6 5.5,6 L18.5,6 C18.7761424,6 19,5.77614237 19,5.5 L19,5 C19,4.72385763 18.7761424,4.5 18.5,4.5 L14,4.5 Z" fill="#000000" opacity="0.3"></path>
                                  </g>
                                </svg>
                              </span>
                            </button>
                          </form>
                        </td>
                    </tr>
                  @endforeach
                </tbody>
            </table>
          </div>
        </div>

      </div>
      <!--end::Nav-->
    </div>
    <!--end::Body-->
  </div>
  <!--end::Profile Card-->
</div>
<!--end::Aside-->