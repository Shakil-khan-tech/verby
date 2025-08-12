{{ Metronic::addClass('body', 'printable') }}

{{-- Extends layout --}}
@extends('layout.default')

{{-- Content --}}
@section('content')

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

  <!--begin::Card-->
    <div id="planCard" class="card card-custom card-sticky gutter-b">
        <div class="card-header flex-wrap border-0 pt-6 pb-0 px-1 px-md-10 unprint">
            <div class="card-title">
                <h3 class="card-label">{{ $page_title }}
                    <span class="d-block text-muted pt-2 font-size-sm">{{ $page_description }}</span>
                </h3>
                {{-- <div class="d-flex flex-wrap align-items-center p-4">
                    @foreach (Config::get('constants.plans') as $symbol => $plan)
                    <div class="footerSymbol btn btn-icon w-auto btn-clean flex-column align-items-center btn-lg px-2" data-symbol="{{$symbol}}">
                        <span class="symbol symbol-35 symbol-{{ $plan['class'] }}">
                            <span class="symbol-label font-size-h5 font-weight-bold {{ $plan['symbol_class'] }}">{{$symbol}}</span>
                        </span>
                        <span class="text-muted font-weight-bold font-size-base mr-1">{{ $plan['text'] }}</span>
                    </div>
                    @endforeach
                </div> --}}
            </div>
            <div class="card-title">
                <h1>{{ $device->name }}</h1>
            </div>
            <div class="card-toolbar">

                <a href="#" class="btn btn-icon btn-light-primary pulse pulse-primary" data-toggle="modal" data-target="#launchHelp">
                    <i class="icon-2x flaticon-questions-circular-button"></i>
                    <span class="pulse-ring"></span>
                </a>

                @if ($inactive_employees)
                <a href="/plans/{{$device->id}}?date={{$date}}&inactive=false" class="btn btn-success font-weight-bold ml-2">
                    <i class="flaticon2-cube"></i>{{ __('Hide Inactive') }}
                </a>
                @else
                <a href="/plans/{{$device->id}}?date={{$date}}&inactive=true" class="btn btn-danger font-weight-bold ml-2">
                    <i class="flaticon2-cube"></i>{{ __('Show Inactive') }}
                @endif


                <a href="#" class="btn btn-primary font-weight-bold ml-2" data-toggle="modal" data-target="#dateModal">
                    <i class="flaticon2-cube"></i>{{ __('Date') }}
                </a>
                <a href="#!" id="btnCalendarPrintAll" class="btn btn-info font-weight-bold ml-2" onclick="window.print();">
                    <i class="flaticon2-cube"></i>{{ __('Print') }}
                </a>
                <a href="#!" id="planFormSubmitAjax" class="btn btn-success font-weight-bold ml-2">
                    <i class="flaticon2-cube"></i>{{ __('Save') }}
                </a>

                

            </div>
        </div>
        <div class="plan-card card-body flex">
            <div class="table-responsive">
                <form id="planForm" action="{{ route('plans.update', $device->id) }}" method="post">
                    {{-- @method('PATCH')
                    @csrf --}}
                    <table id="tablePlans" class="table table-sticky table-vertical-center table-hover table-sm mb-6">
                        <thead>
                            <tr class="print_only print_only_as_table">
                                <th class="text-left" scope="row" colspan="5">
                                    <h3 class="card-label">{{ $page_title }}
                                    <span class="d-block text-muted pt-2 font-size-sm">{{ $page_description }}</span>
                                    </h3>
                                </th>
                                <th class="text-center" scope="row" colspan="{{ count($period) - 5 - 5 + 2 }}"><h1 class="fa-2x">{{ $device->name }}</h1></th>
                                <th class="text-right" scope="row" colspan="5"><h1>&nbsp;</h1></th>
                            </tr>
                            <tr>
                                <td colspan="{{ $period->count() + 3 }}">
                                    <div class="alert alert-custom alert-outline-danger fade show mb-2 py-1 m-auto" role="alert">
                                        <div class="alert-icon">
                                          <i class="flaticon-information"></i>
                                        </div>
                                        <div class="alert-text text-dark text-wrap">
                                            <span class="font-size-sm">Die Gouvernante ist verantwortlich für die Erstellung des Arbeitsplans und ist gemäss den Arbeitsgesetzen dazu verpflichtet sicherzustellen, dass nach fünf Arbeitstagen zwei freie Tage gewährt werden. Jeder Mitarbeiter hat Anspruch auf zwei freie Wochenenden pro Monat, wie gesetzlich festgelegt.</span>
                                        </div>
                                      </div>
                                </td>
                            </tr>
                            <tr class="text-center unprint">
                                <th scope="col" colspan="2">&nbsp;</th>
                                @foreach ($period as $date)
                                  @if ( $today == $date->format('Y-m-d') )
                                    <th scope="col"><span class="navi-text block" style="font-size: 0.7rem;">{{ __('Today') }}</span></th>
                                    @elseif ( $holidays->contains('month_day', $date->format('m-d')) )
                                    <th scope="col"><span class="navi-text block font-size-xs max-w-25px rotate-90 m-auto"> {{ $holidays->firstWhere('month_day', $date->format('m-d'))->name }} </span></th>
                                  @else
                                    <th scope="col"><span class="navi-text block rotate-90">&nbsp;</span></th>
                                  @endif
                                @endforeach
                                <th scope="col">&nbsp;</th>
                            </tr>
                            <tr class="text-center">
                                <th scope="col" class="unprint">{{ __('ID') }}</th>
                                <th scope="col">{{ __('Employee') }}</th>
                                @foreach ($period as $date)
                                @php
                                if ( $today == $date->format('Y-m-d') ) {
                                    $day_class = 'bg-info-o-40';
                                } elseif ( $holidays->contains('month_day', $date->format('m-d')) ) {
                                    $day_class = 'hover:bg-indigo-200 bg-success-o-100';
                                } elseif ( $date->dayOfWeek == \Carbon\Carbon::SATURDAY ) {
                                    $day_class = 'bg-yellow-50';
                                } elseif ( $date->dayOfWeek == \Carbon\Carbon::SUNDAY ) {
                                    $day_class = 'bg-red-50';
                                } else {
                                    $day_class = "";
                                }
                                @endphp
                                <th class="min-w-25px" scope="col">
                                    <div class="{{ $day_class }}">
                                        {{ $date->format('d') }} <br> <span class="text-muted font-size-xs">{{ $date->translatedFormat('D') }}</span>
                                    </div>
                                </th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $function = -999;
                            @endphp
                            @foreach ($data as $emp_key => $employee)
                                @if ($employee->function != $function && $employee->function!=6)
                                @php
                                    $function = $employee->function;
                                @endphp
                                {{-- <tr class="table-{{ Config::get('constants.funktion_colors')[$employee->roli] }}"> --}}  
                                <tr class="table-secondary">
                                    <th scope="row" colspan="{{ count($period) + 2 }}">{{ Config::get('constants.functions')[$employee->function] }}</td>
                                </tr>
                                @endif
                            @if($employee->function!=6)
                            <tr>
                                <td class="unprint">{{ $employee->id }}</td>
                                <td class="text-break">{{ $employee->fullname }}</td>
                                @foreach ($period as $period_key => $date)

                                    @php
                                    $has_vacation = false;
                                    $out_of_entry = true;
                                    $has_plan = false;
                                    $the_key = null;

                                    if ( $today == $date->format('Y-m-d') ) {
                                    $day_class = 'bg-info-o-40';
                                    } elseif ( $holidays->contains('month_day', $date->format('m-d')) ) {
                                    $day_class = 'hover:bg-indigo-200 bg-success-o-100';
                                    } else {
                                    $day_class = Config::get('constants.plan_dayofweek')[$date->dayOfWeek];
                                    }
                                    @endphp

                                    @foreach ($employee->plans as $plan_key => $plan)
                                    @if ($plan->dita == $date->format('Y-m-d'))
                                        @php
                                        $has_plan = true;
                                        $the_key = $plan_key;
                                        @endphp
                                    @endif
                                    @endforeach

                                    @foreach ($employee->vacations as $vacation_key => $vacation)
                                    @if ($vacation->data == $date->format('Y-m-d'))
                                        @php
                                        $has_vacation = true;
                                        @endphp
                                    @endif
                                    @endforeach

                                    @foreach ($employee->combined_entries as $entry)
                                        @if ( $entry['start'] <= $date && ($entry['end'] >= $date || $entry['end'] == null ) )
                                            @php
                                                $out_of_entry = false;
                                            @endphp
                                            @break
                                        @endif
                                    @endforeach


                                    @if ( $has_plan )
                                    <td>
                                        <div class="
                                        {{ $day_class }}
                                        @php
                                            if ( isset( Config::get('constants.plans')[$employee->plans[$the_key]->symbol]['color'] ) ) {
                                            $bg_color = Config::get('constants.plans')[$employee->plans[$the_key]->symbol]['color'];
                                            } else {
                                            $bg_color = '';
                                            }
                                        @endphp
                                        {{ $has_vacation ? 'bg-green-200' : $bg_color }}
                                        ">
                                            <input type="hidden" name="plan[{{$emp_key}}][{{$period_key}}][dita]" value="{{$date->format('Y-m-d')}}">
                                            <input type="hidden" name="plan[{{$emp_key}}][{{$period_key}}][employee_id]" value="{{$employee->id}}">
                                            <input type="hidden" name="plan[{{$emp_key}}][{{$period_key}}][device_id]" value="{{$device->id}}">
                                            <input type="text" class="inputSymbol form-control form-control-sm border-gray-300 p-0 text-center bg-transparent {{$out_of_entry ? 'out_of_entry bg-danger-o-95' : ''}}" name="plan[{{$emp_key}}][{{$period_key}}][symbol]"
                                                value="{{ $has_vacation ? 'F' : $employee->plans[$the_key]->symbol }}" maxlength="11" data-initial_value="{{ $has_vacation ? 'F' : $employee->plans[$the_key]->symbol }}"
                                                {{ $has_vacation ? 'readonly' : '' }}
                                                {{ $out_of_entry ? 'readonly' : '' }}>
                                        </div>
                                    </td>
                                    @else
                                    <td>
                                        <div class="
                                        {{ $day_class }}
                                        {{ $has_vacation ? 'bg-green-200' : '' }}
                                        ">
                                            <input type="hidden" name="plan[{{$emp_key}}][{{$period_key}}][dita]" value="{{$date->format('Y-m-d')}}">
                                            <input type="hidden" name="plan[{{$emp_key}}][{{$period_key}}][employee_id]" value="{{$employee->id}}">
                                            <input type="hidden" name="plan[{{$emp_key}}][{{$period_key}}][device_id]" value="{{$device->id}}">
                                            <input type="text" class="inputSymbol form-control form-control-sm border-gray-300 p-0 text-center bg-transparent {{$out_of_entry ? 'out_of_entry bg-danger-o-95' : ''}}" name="plan[{{$emp_key}}][{{$period_key}}][symbol]" value="{{ $has_vacation ? 'F' : '' }}" maxlength="11"
                                                {{ $has_vacation ? 'readonly' : '' }}
                                                {{ $out_of_entry ? 'readonly' : '' }}>
                                        </div>
                                    </td>
                                    @endif

                                @endforeach
                            </tr>
                            @endif
                            @endforeach

                        </tbody>
                        <tfoot>
                            <th scope="col" class="unprint">{{ __('ID') }}</th>
                            <th scope="col">{{ __('Employee') }}</th>
                            @foreach ($period as $date)
                                @php
                                if ( $today == $date->format('Y-m-d') ) {
                                    $day_class = 'bg-info-o-40';
                                } elseif ( $holidays->contains('month_day', $date->format('m-d')) ) {
                                    $day_class = 'hover:bg-indigo-200 bg-success-o-100';
                                } elseif ( $date->dayOfWeek == \Carbon\Carbon::SATURDAY ) {
                                    $day_class = 'bg-yellow-50';
                                } elseif ( $date->dayOfWeek == \Carbon\Carbon::SUNDAY ) {
                                    $day_class = 'bg-red-50';
                                } else {
                                    $day_class = "";
                                }
                                @endphp
                                <th class="min-w-25px" scope="col">
                                    <div class="{{ $day_class }}">
                                        {{ $date->format('d') }} <br> <span class="text-muted font-size-xs">{{ $date->translatedFormat('D') }}</span>
                                    </div>
                                </th>
                            @endforeach
                        </tfoot>
                    </table>
                </form>
            </div>
            {{-- <div class="hidden md:fixed relative right-0 bg-white md:flex flex-wrap flex-column align-items-center pt-2 unprint">
                @foreach (Config::get('constants.plans') as $symbol => $plan)
                <div class="footerSymbol btn btn-icon w-auto btn-clean flex-column align-items-center btn-lg px-2" data-symbol="{{$symbol}}">
                    <span class="symbol symbol-25 symbol-{{ $plan['class'] }}">
                        <span class="symbol-label font-weight-bold font-size-xs {{ $plan['symbol_class'] }}">{{$symbol}}</span>
                    </span>
                    <span class="text-muted font-weight-bold font-size-sm">{{ $plan['text'] }}</span>
                </div>
                @endforeach
            </div> --}}
        </div>

        <div class="card-footer d-flex justify-content-between justify-content-around">
            {{-- <div class="d-flex flex-wrap align-items-center p-4">
                @foreach (Config::get('constants.plans') as $symbol => $plan)
                <div class="footerSymbol btn btn-icon w-auto btn-clean flex-column align-items-center btn-lg px-2" data-symbol="{{$symbol}}">
                    <span class="symbol symbol-25 symbol-{{ $plan['class'] }}">
                        <span class="symbol-label font-size-h5 font-weight-bold {{ $plan['symbol_class'] }}">{{$symbol}}</span>
                    </span>
                    <span class="text-muted font-weight-bold font-size-base mr-1">{{ $plan['text'] }}</span>
                </div>
                @endforeach
            </div> --}}
        </div>
    </div>
  <!--end::Card-->

  <!--begin::Sticky Toolbar-->
  <ul class="sticky-toolbar nav flex-column pl-2 pr-2 pt-3 pb-3 mt-4">
    <!--begin::Item-->
    <li class="nav-item mb-2" id="kt_demo_panel_toggle" data-toggle="tooltip" data-placement="right">
        <a class="btn btn-sm btn-icon btn-bg-light btn-icon-success btn-hover-success" href="#">
            <i class="flaticon2-drop"></i>
        </a>
    </li>
    <!--end::Item-->
  </ul>
  <ul class="sticky-toolbar nav flex-column pl-2 pr-2 pt-3 pb-3 mt-4">
    @foreach (Config::get('constants.plans') as $symbol => $plan)
    <li class="footerSymbol nav-item mb-2" data-toggle="tooltip" title="{{ __($plan['text']) }}" data-placement="left" data-symbol="{{$symbol}}">
        <span class="symbol symbol-25 symbol-{{ $plan['class'] }} cursor-pointer">
            <span class="symbol-label font-weight-bold font-size-xs {{ $plan['symbol_class'] }}">{{$symbol}}</span>
        </span>
    </li>
    @endforeach

  </ul>
  <!--end::Sticky Toolbar-->

  <!-- Modal Date-->
  <div class="modal fade" id="dateModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="staticBackdrop" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <form action="{{ route('plans.show', $device) }}" method="post">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('Select date to view plan!') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <i aria-hidden="true" class="ki ki-close"></i>
                    </button>
                </div>

                <div class="modal-body">
                    <div class="form-group">
                        <div class="input-group">
                            <div class="input-group-append">
                                <span class="input-group-text">
                                    <i class="la la-calendar-check-o"></i>
                                </span>
                            </div>
                            <input id="datePlan" class="form-control form-control-lg form-control-solid" type="month" name="date" value="{{ $date->format('Y-m') }}">
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-light-primary font-weight-bold" data-dismiss="modal">{{ __('Close') }}</button>
                    <button type="submit" class="btn btn-primary font-weight-bold">{{ __('Go!') }}</button>
                </div>

            </form>
        </div>
    </div>
    </div>

    <!-- Modal Help-->
    <div class="modal fade" id="launchHelp" tabindex="-1" role="dialog" aria-labelledby="helpTitle" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="helpTitle">{{ __('How to use Plan') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <i aria-hidden="true" class="ki ki-close"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <!--begin::Iconbox-->
                    <div class="card card-custom wave wave-animate-slow mb-8 mb-lg-0">
                        <div class="card-body p-0">
                            <div class="d-flex align-items-center p-5">
                                <div class="mr-6">
                                    <span class="symbol symbol-2by3 symbol-50 symbol-light-success">
                                        <span class="symbol-label font-size-h5 font-weight-bold">F</span>
                                    </span>
                                </div>
                                <div class="d-flex flex-column">
                                    <a href="#" class="text-dark text-hover-primary font-weight-bold font-size-h4 mb-3">{{ __('Character') }}</a>
                                    <div class="text-dark-75">{{ __('Type or click one of the characters from the legend on the right side of the table.') }}</div>
                                </div>
                            </div>
                            <div class="d-flex align-items-center p-5">
                                <div class="mr-6">
                                    <span class="symbol symbol-2by3 symbol-50 symbol-light-primary">
                                        <span class="symbol-label font-size-h5 font-weight-bold">8</span> <br>
                                        <span class="symbol-label font-size-h5 font-weight-bold">8:20</span>
                                    </span>
                                </div>
                                <div class="d-flex flex-column">
                                    <a href="#" class="text-dark text-hover-primary font-weight-bold font-size-h4 mb-3">{{ __('Single Shift') }}</a>
                                    <div class="text-dark-75">{{ __('Type just the hour when the employee starts. ex:') }} 8</div>
                                </div>
                            </div>
                            <div class="d-flex align-items-center p-5">
                                <div class="mr-6">
                                    <span class="symbol symbol-2by3 symbol-50 symbol-light-danger">
                                        <span class="symbol-label font-size-h5 font-weight-bold">8-15</span> <br>
                                        <span class="symbol-label font-size-sm font-weight-bold">8:30-15:30</span>
                                    </span>
                                </div>
                                <div class="d-flex flex-column">
                                    <a href="#" class="text-dark text-hover-primary font-weight-bold font-size-h4 mb-3">{{ __('Double Shift') }}</a>
                                    <div class="text-dark-75">{{ __('If there are two shifts for the employee, then type the two hours separating them with a dash') }} (<b>-</b>). {{ __('ex:') }} 8-15</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--end::Iconbox-->
                </div>
            </div>
        </div>
    </div>

@endsection

{{-- Styles Section --}}
@section('styles')
<style>
@media print {
    @page {size: auto !important}
    #planForm td {
        padding: 0 !important;
    }
    input.inputSymbol {
        font-size: 0.7rem !important;
    }
    .table,
    .table thead th,
    .table thead td {
        font-size: 0.7rem !important;
    }
    .card-sticky-on .card.card-custom.card-sticky > .card-header {
        position: revert !important;
    }
    ::-webkit-scrollbar {
        display: none;
    }
    body {
        -webkit-print-color-adjust:exact;
        color-adjust:exact
    }
    .table-responsive th, .table-responsive td {
        white-space: unset !important;
    }
    
}
</style>
@endsection


{{-- Scripts Section --}}
@section('scripts')
    <!--begin::Global Config(global config for global JS scripts)-->
    <script>var KTAppSettings = { "breakpoints": { "sm": 576, "md": 768, "lg": 992, "xl": 1200, "xxl": 1400 }, "colors": { "theme": { "base": { "white": "#ffffff", "primary": "#3699FF", "secondary": "#E5EAEE", "success": "#1BC5BD", "info": "#8950FC", "warning": "#FFA800", "danger": "#F64E60", "light": "#E4E6EF", "dark": "#181C32" }, "light": { "white": "#ffffff", "primary": "#E1F0FF", "secondary": "#EBEDF3", "success": "#C9F7F5", "info": "#EEE5FF", "warning": "#FFF4DE", "danger": "#FFE2E5", "light": "#F3F6F9", "dark": "#D6D6E0" }, "inverse": { "white": "#ffffff", "primary": "#ffffff", "secondary": "#3F4254", "success": "#ffffff", "info": "#ffffff", "warning": "#ffffff", "danger": "#ffffff", "light": "#464E5F", "dark": "#ffffff" } }, "gray": { "gray-100": "#F3F6F9", "gray-200": "#EBEDF3", "gray-300": "#E4E6EF", "gray-400": "#D1D3E0", "gray-500": "#B5B5C3", "gray-600": "#7E8299", "gray-700": "#5E6278", "gray-800": "#3F4254", "gray-900": "#181C32" } }, "font-family": "Poppins" };</script>
    <!--end::Global Config-->

    {{-- vendors --}}

    {{-- page scripts --}}
    <script type="text/javascript">
      var deviceId = {!! $device->id !!};
      var calendarDate = "{!! $date !!}";
      var inactiveEmployees = "{{ $inactive_employees }}";
    </script>
    <script src="{{ mix('js/pages/plans/show.js') }}" type="text/javascript"></script>
    <script src="{{ mix('js/app.js') }}" type="text/javascript"></script>
@endsection
