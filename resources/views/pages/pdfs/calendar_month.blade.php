<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Calendar</title>

    {{-- @include('pages.pdfs.calendar-style') --}}

  </head>
  <body>
    <table id="tableCalendar" class="table bg-white">
      <thead>
        <tr class="text-center">
          <th scope="col">{{ __('Employee') }}</th>
          @foreach ($period as $date)
            <th colspan="2" class="{{ Config::get('constants.plan_dayofweek')[$date->dayOfWeek] }}" scope="col">
              {{ $date->format('d') }} <br>
              <span class="text-muted font-size-xs">{{ $date->translatedFormat('D') }}</span><br>
              <span class="font-size-xs">D | R</span><br>
            </th>
          @endforeach
        </tr>
      </thead>
      <tbody>
        @php
          $function = -999;
        @endphp
        @foreach ($data as $emp_key => $employee)

          @if ($employee->function != $function)
            @php
              $function = $employee->function;
            @endphp
            <tr class="table-secondary">
              <th scope="row" colspan="{{ count($period) * 2 + 1 }}" >{{ Config::get('constants.functions')[$employee->function] }}</th>
            </tr>
          @endif

          <tr>
            <td>{{ $employee->fullname }}</td>
          @foreach ($period as $period_key => $date)

              <td class="plan_data border-1 hover:bg-indigo-200 {{ Config::get('constants.plan_dayofweek')[$date->dayOfWeek] }}" data-type="0" data-day="{{$date->format('d')}}" data-date="{{$date->format('Y-m-d')}}" data-employee="{{$employee->id}}" data-device="{{$device->id}}">
                <a href="#!" class="block text-center" data-toggle="modal" data-target="#calendarDay">0</a>
              </td>
              <td class="plan_data border-1 hover:bg-pink-200 {{ Config::get('constants.plan_dayofweek')[$date->dayOfWeek] }}" data-type="1" data-day="{{$date->format('d')}}" data-date="{{$date->format('Y-m-d')}}" data-employee="{{$employee->id}}" data-device="{{$device->id}}">
                <a href="#!" class="block text-center" data-toggle="modal" data-target="#calendarDay">0</a>
              </td>

          @endforeach
        </tr>
        @endforeach
      </tbody>
    </table>

  </body>
</html>
