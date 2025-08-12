<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Employee Report</title>
    <style>
        @page {
            margin: 5mm;
            size: A4 landscape;
        }
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 5px; /* Further reduced font size */
            margin: 0;
            padding: 0;
            width: 100%;
        }
        .report-container {
            width: 100%;
            overflow: visible;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }
        .table th, .table td {
            border: 1px solid #ddd;
            padding: 1px; /* Minimal padding */
            text-align: center;
            word-wrap: break-word;
            height: 10px;
        }
        .table th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        .employee-name {
            width: 80px; /* Fixed width for employee names */
            position: sticky;
            left: 0;
            background-color: white;
            z-index: 2;
        }
        .day-header {
            width: 12px; /* Very narrow columns for days */
        }
        .data-cell {
            width: 12px; /* Very narrow columns for data */
        }
        .total-cell {
            width: 15px; /* Slightly wider for totals */
        }
        .bg-gray-200 {
            background-color: #e5e7eb;
        }
        .font-bold {
            font-weight: bold;
        }
        .text-danger {
            color: #dc3545;
        }
        .text-success {
            color: #28a745;
        }
        .rotate-text {
            writing-mode: vertical-rl;
            transform: rotate(180deg);
            white-space: nowrap;
            display: inline-block;
            width: 1em;
            line-height: 1em;
            padding: 0.5em 0;
        }
    </style>
</head>
<body>
    <div class="report-container">
        <table class="table">
            <thead>
                <tr>
                    <th rowspan="2" class="employee-name">{{ __('Employee') }}</th>
                    @foreach($period as $day)
                        <th colspan="4" class="day-header">
                            <span class="rotate-text">{{ $day->format('d M') }}</span>
                        </th>
                    @endforeach
                    <th colspan="5" class="total-cell">Total</th>
                </tr>
                <tr>
                    @foreach($period as $day)
                        <th class="data-cell">D</th>
                        <th class="data-cell">R</th>
                        <th class="data-cell">Z</th>
                        <th class="data-cell">B</th>
                    @endforeach
                    <th class="total-cell">{{ __('Depa') }}</th>
                    <th class="total-cell">{{ __('Restant') }}</th>
                    <th class="total-cell">Zeit</th>
                    <th class="total-cell">Budget</th>
                    <th class="total-cell">%</th>
                </tr>
            </thead>
            <tbody>
                @php
                $functions = config('constants.functions');
                @endphp
                @foreach ($functions as $fun=>$functionName)
                    @php
                        $employeesByFunction = collect($matrix)->where('function', $fun);
                    @endphp

                    @if($employeesByFunction->count())
                        <tr>
                            <td colspan="{{ 1 + $period->count() * 4 + 5 }}" class="bg-gray-200 font-bold">
                                {{ $functionName }}
                            </td>
                        </tr>

                        @foreach ($employeesByFunction as $employee)
                            @php
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

                            <tr>
                                <td class="employee-name">{{ $employee['fullname'] }}</td>
                                @foreach($period as $day)
                                    @php
                                        $dayKey = $day->format('d.m.Y');
                                        $daily = $employee['daily_data'][$dayKey] ?? null;
                                    @endphp
                                    @if($daily)
                                        @php
                                            $totalDailyTime = number_format(($daily['work_seconds'] / 3600), 2, '.', '');
                                            $potentialDailyTime = number_format(($daily['potential_seconds'] / 3600), 2, '.', '');
                                            $totalTime = ($employee['function'] == 0)
                                                ? round((($daily['depas'] + $daily['restants']) * 3) / 60, 2)
                                                : round((($daily['depas'] * 20 + $daily['restants'] * 10) / 60), 2);
                                            $dailyDifference = $totalTime - $totalDailyTime;
                                            $differenceClassDaily = $dailyDifference < 0 ? 'text-danger' : 'text-success';
                                        @endphp
                                        <td class="data-cell">{{ $daily['depas'] }}</td>
                                        <td class="data-cell">{{ $daily['restants'] }}</td>
                                        <td class="data-cell">{{ $totalDailyTime }}</td>
                                        <td class="data-cell {{ $differenceClassDaily }}">{{ $dailyDifference }}</td>
                                    @else
                                        <td class="data-cell">0</td>
                                        <td class="data-cell">0</td>
                                        <td class="data-cell">0.00</td>
                                        <td class="data-cell">0.00</td>
                                    @endif
                                @endforeach
                                <td class="total-cell">{{ $employee['depas'] }}</td>
                                <td class="total-cell">{{ $employee['restants'] }}</td>
                                <td class="total-cell">{{ $zeitTimeTotalTime }}</td>
                                <td class="total-cell {{ $differenceClass }}">{{ number_format($difference, 2) }}</td>
                                <td class="total-cell {{ $percentageClass }}">{{ $formattedPercentage }}</td>
                            </tr>
                        @endforeach
                    @endif
                @endforeach
            </tbody>
        </table>
    </div>
</body>
</html>