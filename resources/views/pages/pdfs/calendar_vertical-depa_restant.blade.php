<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Calendar</title>

    @include('pages.pdfs.calendar-style')

  </head>
  <body>
    @foreach ($employees as $key => $employee)
      <div class="page">
        <!--begin::Header-->
        <table class="table bg-white">
          <thead>
            <tr>
              <th scope="col" class="text-left">{{ __('Hotel') }}</th>
              <th scope="col" class="text-center">{{ __('Employee') }}</th>
              <th scope="col" class="text-right">{{ __('Date') }}</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <th scope="row" class="text-left"><h4 class="font-size-h4 my-2">{{ $employee->device->name }}</h4></th>
              <th scope="row" class="text-center"><h4 class="font-size-h4 my-2">{{ $employee->fullname }}</h4></th>
              <th scope="row" class="text-right"><h4 class="font-size-h4 my-2">{{ $date->format('Y-m-d') }}</h4></th>
            </tr>
          </tbody>
        </table>
        <!--end::Header-->

        <!--begin::Depa-->
        <table class="table table-bordered bg-white">
          <thead>
            <tr>
              <th colspan="3"><h4 class="font-size-h4 my-2">{{ __('Depa') }}</h4></th>
            </tr>
            <tr>
              <th scope="col">{{ __('Room Name') }}</th>
              <th scope="col">{{ __('Room Type') }}</th>
              <th scope="col">{{ __('Extra') }}</th>
            </tr>
          </thead>
          <tbody>
            @foreach ($employee->calendars as $calendar)
              @if ( $calendar->rooms->where('pivot.clean_type', 0)->isEmpty() )
                <tr>
                  <td colspan="3" class="text-center">{{ __('No rooms/spaces for depa') }}</td>
                </tr>
              @else
                @foreach ($calendar->rooms->where('pivot.clean_type', 0) as $room) {{-- depa only --}}
                  <tr>
                    <th scope="row">{{ $room->name }}</th>
                    <td>{{ Config::get('constants.room_categories')[$room->category] }}</td>
                    <td>{{ Config::get('constants.calendar_room_extra')[$room->pivot->extra] }}</td>
                  </tr>
                @endforeach
              @endif
            @endforeach
          </tbody>
        </table>
        <!--end::Depa-->

        <!--begin::Restant-->
        <table class="table table-bordered bg-white">
          <thead>
            <tr>
              <th colspan="3"><h4 class="font-size-h4 my-2">{{ __('Restant') }}</h4></th>
            </tr>
            <tr>
              <th scope="col">{{ __('Room Name') }}</th>
              <th scope="col">{{ __('Room Type') }}</th>
              <th scope="col">{{ __('Extra') }}</th>
            </tr>
          </thead>
          <tbody>
            @foreach ($employee->calendars as $calendar)
              @if ( $calendar->rooms->where('pivot.clean_type', 1)->isEmpty() )
                <tr>
                  <td colspan="3" class="text-center">{{ __('No rooms/spaces for restant') }}</td>
                </tr>
              @else
                @foreach ($calendar->rooms->where('pivot.clean_type', 1) as $room) {{-- restant only --}}
                  <tr>
                    <th scope="row">{{ $room->name }}</th>
                    <td>{{ Config::get('constants.room_categories')[$room->category] }}</td>
                    <td>{{ Config::get('constants.calendar_room_extra')[$room->pivot->extra] }}</td>
                  </tr>
                @endforeach
              @endif
            @endforeach
          </tbody>
        </table>
        <!--end::Restant-->
        @if ( $key+1 < $employees->count() )
          <div class="page_break"></div>
        @endif
      </div>
    @endforeach






  </body>
</html>
