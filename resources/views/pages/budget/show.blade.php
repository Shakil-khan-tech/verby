@inject('record_helper', 'App\Classes\Helpers\Record')

{{-- {{ Metronic::addClass('body', 'print-content-only') }} --}}
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
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
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
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true"><i class="ki ki-close"></i></span>
            </button>
        </div>
    </div>
  @endif

  <!--begin::Card-->
  <div class="card card-custom gutter-b">
    <div class="card-header flex-wrap border-0 pt-6 pb-0">
      <div class="card-title">
        <h3 class="card-label">{{ $page_title }}
        <span class="d-block text-muted pt-2 font-size-sm">{{ $page_description }}</span></h3>
      </div>
      <div class="card-toolbar">
        <a href="#">
          <div class="input-group date" id="date_picker" data-target-input="nearest">
            <input type="text" class="form-control datetimepicker-input" data-target="#date_picker" data-toggle="datetimepicker" value="{{ $month }}" />
            <div class="input-group-append" data-target="#date_picker" data-toggle="datetimepicker">
              <span class="input-group-text">
                <i class="ki ki-calendar"></i>
              </span>
            </div>
          </div>
        </a>
        @php
            $currentDate = request()->query('date', \Carbon\Carbon::now()->toDateString()); // get date from URL or default today
        @endphp

        <a href="{{ route('download.excel', ['device' => $device->id]) }}?date={{ $currentDate }}" class="btn btn-success font-weight-bold ml-2">
            <i class="flaticon2-cube"></i> {{ __('Export Excel') }}
        </a>
      </div>
    </div>
    <div class="card-body">
      <div class="table-responsive">
        <div class="table-responsive">
            <table id="dateWiseTable" class="table table-bordered table-sm" style="width:100%; font-size: 12px;">
                <thead style="text-align: center; background-color: #f2f2f2;">
                    <tr>
                        <th rowspan="2" style="width: 120px;">{{ __('Employees')}}</th>
                        <th rowspan="2">Datum</th>
                        @foreach($period as $day)
                            <th>{{ $day->format('d') }}</th>
                        @endforeach
                        <th>{{ __('Total')}}</th>
                        <th>%</th> <!-- Added percentage column -->
                    </tr>
                </thead>
                <tbody>
                    @php
                        $functions = config('constants.functions');
                    @endphp
                    @foreach ($functions as $fun => $functionName)
                        @php
                            $employeesByFunction = collect($matrix)->where('function', $fun);
                            $totalColumns = count($period) + 4; // Days + Employees + Datum + Total + %
                        @endphp

                        @if($employeesByFunction->count())
                            <!-- Department Header Row -->
                            <tr style="background-color: #e6e6e6; font-weight: bold;">
                                <td colspan="{{ $totalColumns }}">{{ $functionName }}</td>
                            </tr>

                            @foreach ($employeesByFunction as $employee)
                                @php
                                    // Calculations
                                    $zeitTime = $employee['work_seconds'] / 3600;
                                    $zeitTimeTotalTime = number_format($zeitTime, 2, '.', '');
                                    $requiredTotalTime = ($employee['function'] == 0)
                                        ? round((($employee['depas'] + $employee['restants']) * 3) / 60, 2)
                                        : round((($employee['depas'] * 20 + $employee['restants'] * 10) / 60), 2);

                                    $difference = $requiredTotalTime - $zeitTimeTotalTime;
                                    $differenceClass = $difference < 0 ? 'text-danger' : 'text-success';

                                    $formattedPercentage = '0%';
                                    $percentageClass = '';
                                    if ($zeitTime > 0) {
                                        $percentage = ($difference / $zeitTime) * 100;
                                        $sign = $percentage >= 0 ? '+' : '';
                                        $formattedPercentage = $sign . number_format($percentage, 2) . '%';
                                        $percentageClass = $percentage < 0 ? 'text-danger' : 'text-success';
                                    }
                                @endphp

                                <!-- Depa Row -->
                                <tr>
                                    <td>{{ $employee['fullname'] }}</td>
                                    <td>Depa</td>
                                    @foreach($period as $day)
                                        @php
                                            $dayKey = $day->format('d.m.Y');
                                            $daily = $employee['daily_data'][$dayKey] ?? null;
                                        @endphp
                                        <td style="text-align: center;">{{ $daily['depas'] ?? '' }}</td>
                                    @endforeach
                                    <td>{{ $employee['depas'] }}</td>
                                    <td></td> <!-- Empty cell for percentage column -->
                                </tr>

                                <!-- Restant Row -->
                                <tr>
                                    <td></td>
                                    <td>Restant</td>
                                    @foreach($period as $day)
                                        @php
                                            $dayKey = $day->format('d.m.Y');
                                            $daily = $employee['daily_data'][$dayKey] ?? null;
                                        @endphp
                                        <td style="text-align: center;">{{ $daily['restants'] ?? '' }}</td>
                                    @endforeach
                                    <td>{{ $employee['restants'] }}</td>
                                    <td></td> <!-- Empty cell for percentage column -->
                                </tr>

                                <!-- Time Row -->
                                <tr>
                                    <td></td>
                                    <td>Time.</td>
                                    @foreach($period as $day)
                                        @php
                                            $dayKey = $day->format('d.m.Y');
                                            $daily = $employee['daily_data'][$dayKey] ?? null;
                                        @endphp
                                        <td style="text-align: center;">
                                            @if($daily)
                                                {{ number_format(($daily['work_seconds'] / 3600), 2) }}
                                            @endif
                                        </td>
                                    @endforeach
                                    <td>{{ $zeitTimeTotalTime }}</td>
                                    <td></td> <!-- Empty cell for percentage column -->
                                </tr>

                                <!-- Budget Row -->
                                <tr>
                                    <td></td>
                                    <td>Budget</td>
                                    @foreach($period as $day)
                                        @php
                                            $dayKey = $day->format('d.m.Y');
                                            $daily = $employee['daily_data'][$dayKey] ?? null;
                                            $dailyDifference = 0;
                                            if($daily) {
                                                $totalDailyTime = ($daily['work_seconds'] / 3600);
                                                $totalTime = ($employee['function'] == 0)
                                                    ? round((($daily['depas'] + $daily['restants']) * 3) / 60, 2)
                                                    : round((($daily['depas'] * 20 + $daily['restants'] * 10) / 60), 2);
                                                $dailyDifference = $totalTime - $totalDailyTime;
                                                $differenceClassDaily = $dailyDifference < 0 ? 'text-danger' : 'text-success';
                                            }
                                        @endphp
                                        <td style="text-align: center;" class="{{ $differenceClassDaily ?? '' }}">
                                            @if($daily)
                                                {{ number_format($dailyDifference, 2) }}
                                            @endif
                                        </td>
                                    @endforeach
                                    <td class="{{ $differenceClass }}">
                                        {{ number_format($difference, 2) }}
                                    </td>
                                    <td class="{{ $percentageClass }}">
                                        {{ $formattedPercentage }}
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                    @endforeach
                </tbody>
            </table>
        </div>
      </div>
    </div>
  </div>
  <!--end::Card-->

@endsection

{{-- Styles Section --}}
@section('styles')
<style media="screen">
@media print { @page {size: auto !important} }
#dateWiseTable th {
    font-size: 12px;
    padding: 5px;
}
#dateWiseTable td {
    font-size: 12px;
    padding: 5px;
}
.table-responsive {
    overflow-x: auto;
}
</style>
@endsection


{{-- Scripts Section --}}
@section('scripts')
    <script type="text/javascript">
      var deviceId = {!! $device->id !!};
    </script>
    <!--begin::Global Config(global config for global JS scripts)-->
    <script>var KTAppSettings = { "breakpoints": { "sm": 576, "md": 768, "lg": 992, "xl": 1200, "xxl": 1400 }, "colors": { "theme": { "base": { "white": "#ffffff", "primary": "#3699FF", "secondary": "#E5EAEE", "success": "#1BC5BD", "info": "#8950FC", "warning": "#FFA800", "danger": "#F64E60", "light": "#E4E6EF", "dark": "#181C32" }, "light": { "white": "#ffffff", "primary": "#E1F0FF", "secondary": "#EBEDF3", "success": "#C9F7F5", "info": "#EEE5FF", "warning": "#FFF4DE", "danger": "#FFE2E5", "light": "#F3F6F9", "dark": "#D6D6E0" }, "inverse": { "white": "#ffffff", "primary": "#ffffff", "secondary": "#3F4254", "success": "#ffffff", "info": "#ffffff", "warning": "#ffffff", "danger": "#ffffff", "light": "#464E5F", "dark": "#ffffff" } }, "gray": { "gray-100": "#F3F6F9", "gray-200": "#EBEDF3", "gray-300": "#E4E6EF", "gray-400": "#D1D3E0", "gray-500": "#B5B5C3", "gray-600": "#7E8299", "gray-700": "#5E6278", "gray-800": "#3F4254", "gray-900": "#181C32" } }, "font-family": "Poppins" };</script>
    <!--end::Global Config-->

    {{-- vendors --}}

    {{-- page scripts --}}
    <script src="{{ mix('js/pages/budget/show.js') }}" type="text/javascript"></script>
    <script src="{{ mix('js/app.js') }}" type="text/javascript"></script>
@endsection