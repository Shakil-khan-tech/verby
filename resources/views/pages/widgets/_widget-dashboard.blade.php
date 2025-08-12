{{-- Mixed Widget 1 --}}

<div class="card card-custom bg-gray-100 {{ @$class }}">
    {{-- Header --}}
    <div class="card-header border-0 bg-mili py-5">
        <h3 class="card-title font-weight-bolder text-white">{{ __('Dashboard') }}</h3>
        <div class="card-toolbar">
            <div class="dropdown dropdown-inline">
                <a href="#" class="btn btn-transparent-white btn-sm font-weight-bolder dropdown-toggle px-5" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                  {{ __('Show') }}
                </a>
                <div class="dropdown-menu dropdown-menu-sm dropdown-menu-right">
                    {{-- Navigation --}}
                    <ul class="navi navi-hover">
                        <li class="navi-item">
                            <a href="#" class="navi-link chartMonths" data-months="3">
                                <i class="navi-icon flaticon2-calendar-4"></i>
                                <span class="navi-text">3 {{ __('months') }}</span>
                            </a>
                        </li>
                        <li class="navi-item">
                            <a href="#" class="navi-link chartMonths bg-gray-200 active" data-months="6">
                                <i class="navi-icon flaticon2-calendar-4"></i>
                                <span class="navi-text">6 {{ __('months') }}</span>
                            </a>
                        </li>
                        <li class="navi-item">
                            <a href="#" class="navi-link chartMonths" data-months="9">
                                <i class="navi-icon flaticon2-calendar-4"></i>
                                <span class="navi-text">9 {{ __('months') }}</span>
                            </a>
                        </li>
                        <li class="navi-item">
                            <a href="#" class="navi-link chartMonths" data-months="12">
                                <i class="navi-icon flaticon2-calendar-4"></i>
                                <span class="navi-text">12 {{ __('months') }}</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    {{-- Body --}}
    <div class="card-body p-0 position-relative overflow-hidden">
        {{-- Chart --}}
        <div id="widget_dashboard_chart" class="card-rounded-bottom bg-mili" style="height: 200px"></div>

        {{-- Stats --}}
        <div class="card-spacer mt-n12">
            {{-- Row --}}
            <div class="row m-0">
                <a href="{{ route('users.index') }}" class="col bg-light-primary px-6 py-8 rounded-xl mr-7 mb-7">
                  {{ Metronic::getSVG("media/svg/icons/Communication/Shield-user.svg", "svg-icon-3x svg-icon-primary d-block my-2") }}
                  <span class="text-primary font-weight-bold font-size-h6">{{ __('Users') }}</span>
                </a>
                <a href="{{ route('roles.index') }}" class="col bg-light-success px-6 py-8 rounded-xl mr-7 mb-7">
                  {{ Metronic::getSVG("media/svg/icons/Home/Key.svg", "svg-icon-3x svg-icon-success d-block my-2") }}
                  <span class="text-success font-weight-bold font-size-h6">{{ __('Access Management') }}</span>
                </a>
                <a href="{{ route('devices.index') }}" class="col bg-light-info px-6 py-8 rounded-xl mr-7 mb-7">
                    {{ Metronic::getSVG("media/svg/icons/Devices/Tablet.svg", "svg-icon-3x svg-icon-info d-block my-2") }}
                    <span class="text-info font-weight-bold font-size-h6">{{ __('Devices') }}</span>
                </a>
                <a href="{{ route('plans.index') }}" class="col bg-light-warning px-6 py-8 rounded-xl mr-7 mb-7">
                  {{ Metronic::getSVG("media/svg/icons/Layout/Layout-grid.svg", "svg-icon-3x svg-icon-warning d-block my-2") }}
                  <span class="text-warning font-weight-bold font-size-h6">{{ __('Plan') }}</span>
                </a>
                <a href="{{ route('calendars.index') }}" class="col bg-light-danger px-6 py-8 rounded-xl mr-7 mb-7">
                  {{ Metronic::getSVG("media/svg/icons/Communication/Clipboard-check.svg", "svg-icon-3x svg-icon-danger d-block my-2") }}
                  <span class="text-danger font-weight-bold font-size-h6">{{ __('Calendar') }}</span>
                </a>
                <a href="{{ route('employees.index') }}" class="col bg-light-secondary px-6 py-8 rounded-xl mr-7 mb-7">
                  {{ Metronic::getSVG("media/svg/icons/Communication/Group.svg", "svg-icon-3x svg-icon-secondary d-block my-2") }}
                  <span class="text-secondary font-weight-bold font-size-h6">{{ __('Employees') }}</span>
                </a>
            </div>
            {{-- Row --}}
            <div class="row m-0">
                <a href="{{ route('lohn.index') }}" class="col bg-light-dark px-6 py-8 rounded-xl mr-7 mb-7">
                  {{ Metronic::getSVG("media/svg/icons/General/Clipboard.svg", "svg-icon-3x svg-icon-dark d-block my-2") }}
                  <span class="text-dark font-weight-bold font-size-h6">{{ __('Payroll') }}</span>
                </a>
                <a href="{{ route('vacations.index') }}" class="col bg-light-primary px-6 py-8 rounded-xl mr-7 mb-7">
                  {{ Metronic::getSVG("media/svg/icons/Food/Coffee1.svg", "svg-icon-3x svg-icon-info d-block my-2") }}
                  <span class="text-info font-weight-bold font-size-h6">{{ __('Vacations') }}</span>
                </a>
                <a href="{{ route('records.index') }}" class="col bg-light-success px-6 py-8 rounded-xl mr-7 mb-7">
                  {{ Metronic::getSVG("media/svg/icons/Files/Cloud-download.svg", "svg-icon-3x svg-icon-dark d-block my-2") }}
                  <span class="text-dark font-weight-bold font-size-h6">{{ __('Records - List View') }}</span>
                </a>
                <a href="{{ route('records.calendar_index') }}" class="col bg-light-info px-6 py-8 rounded-xl mr-7 mb-7">
                  {{ Metronic::getSVG("media/svg/icons/Files/Cloud-download.svg", "svg-icon-3x svg-icon-info d-block my-2") }}
                  <span class="text-info font-weight-bold font-size-h6">{{ __('Records - Calendar View') }}</span>
                </a>
                <a href="{{ route('daily_reports.index') }}" class="col bg-light-warning px-6 py-8 rounded-xl mr-7 mb-7">
                  {{ Metronic::getSVG("media/svg/icons/Home/Clock.svg", "svg-icon-3x svg-icon-warning d-block my-2") }}
                  <span class="text-warning font-weight-bold font-size-h6">{{ __('Daily Reports') }}</span>
                </a>
                {{-- <a href="{{ route('daily_reports_hotel.index') }}" class="col bg-light-danger px-6 py-8 rounded-xl mr-7 mb-7">
                  {{ Metronic::getSVG("media/svg/icons/Home/Clock.svg", "svg-icon-3x svg-icon-danger d-block my-2") }}
                  <span class="text-danger font-weight-bold font-size-h6">{{ __('Daily Reports - Hotel') }}</span>
                </a> --}}
                <a href="{{ route('records.calendar_report') }}" class="col bg-light-danger px-6 py-8 rounded-xl mr-7 mb-7">
                  {{ Metronic::getSVG("media/svg/icons/Communication/Clipboard-check.svg", "svg-icon-3x svg-icon-danger d-block my-2") }}
                  <span class="text-danger font-weight-bold font-size-h6">{{ __('Individual Monthly Performance') }}</span>
                </a>
            </div>
        </div>
    </div>
</div>
