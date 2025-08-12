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
    @php $first = 0; @endphp
    @foreach ($period as $key => $date)

      @if ( count($calendars->where('date', $date->format('Y-m-d'))) == 0 )
        @php $first++; @endphp
        @continue
      @endif

      @if ( $key < count($period) && $key > $first )
        <div class="page_break"></div>
      @endif


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
            <th scope="row" class="text-left"><h4 class="font-size-h4 my-2">{{ $device->name }}</h4></th>
            <th scope="row" class="text-center"><h4 class="font-size-h4 my-2">{{ $employee->fullname }}</h4></th>
            <th scope="row" class="text-right"><h4 class="font-size-h4 my-2">{{ $date->format('Y-m-d') }}</h4></th>
          </tr>
        </tbody>
      </table>
      <!--end::Header-->

      <!--begin::DepaRestant-->
      <table class="table table-bordered bg-white table-striped">
        <thead>
          <tr>
            <th colspan="3"><h4 class="font-size-h4 my-2">{{ __('Rooms') }}</h4></th>
          </tr>
          <tr>
            <th scope="col" class="text-center">{{ __('Room Name') }}</th>
            <th scope="col" class="text-center">{{ __('Depa/Restant') }}</th>
            <th scope="col" class="text-center">{{ __('Room Type') }}</th>
            <th scope="col" class="text-center">{{ __('Extra') }}</th>
          </tr>
        </thead>
        <tbody>
          @foreach ($calendars->where('date', $date->format('Y-m-d')) as $calendar)
            @if ( $calendar->rooms->isEmpty() )
              <tr>
                <td colspan="4" class="text-center">{{ __('No rooms/spaces for depa') }}</td>
              </tr>
            @else
              {{-- @foreach ($calendar->rooms->where('pivot.clean_type', 0) as $room) {{-- depa only --}} --}}
              @foreach ($calendar->rooms as $room) {{-- depa only --}}
                <tr>
                  <th scope="row" class="text-center">{{ $room->name }}</th>
                  <th scope="row" class="text-center">{!! ($room->pivot->clean_type == 0) ? "<span class='text-primary'>{{ __('Depa') }}<span>" : "{{ __('Restant') }}" !!}</th>
                  <td class="text-center">{{ Config::get('constants.room_categories')[$room->category] }}</td>
                  <td class="text-center">{{ Config::get('constants.calendar_room_extra')[$room->pivot->extra] }}</td>
                </tr>
              @endforeach
            @endif
          @endforeach
        </tbody>
      </table>
      <!--end::Depa/Restant-->

    @endforeach

  </body>
</html>
