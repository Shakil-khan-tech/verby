<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('Calendar') }}</title>

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

      <div class="btn btn-icon btn-clean btn-lg btn-dropdown mr-1">
        {{ Metronic::getSVG("media/svg/icons/General/Search.svg", "svg-icon-xl svg-icon-primary") }}
     </div>
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
            <th colspan="3"><h4 class="font-size-h4 my-2">{{ __('Room List') }}</h4></th>
          </tr>
          <tr class="text-center">
            <th scope="col">{{ __('Room Number') }}</th>
            <th scope="col" class="text-center">{{ __('Depa/Restant') }}</th>
            <th scope="col">{{ __('Room Type') }}</th>
            <th scope="col">{{ __('Finished') }}</th>
            <th scope="col">{{ __('Extra') }}</th>
          </tr>
        </thead>
        <tbody>
          @foreach ($calendars->where('date', $date->format('Y-m-d')) as $calendar)
            @if ( $calendar->rooms->isEmpty() )
              <tr>
                <td colspan="4" class="text-center">{{ __('No rooms/spaces for depa') }}</td>
              </tr>
            @else
              @foreach ($calendar->rooms as $room) {{-- depa only --}}
                <tr>
                  <th scope="row" class="text-center">{{ $room->name }}</th>
                  @if ( $room->pivot->clean_type == 0 )
                    <th scope="row" class="text-center">
                      <span class="label font-weight-bold label-lg  label-light-danger label-inline" style="display:block; width:60px; height:8px; text-align:center; margin: 0 auto; line-height:0.8">
                        {{ __('Depa') }}
                      </span>
                    </th>
                  @else
                    <th scope="row" class="text-center">
                      <span class="label font-weight-bold label-lg label-light-success label-inline " style="display:block; width:60px; height:8px; text-align:center; margin: 0 auto; line-height:0.8">
                        {{ __('Restant') }}
                      </span>
                    </th>
                  @endif
                  <td class="text-center">{{ Config::get('constants.room_categories')[$room->category] }}</td>
                  <td class="text-center">
                    {{-- {{ $room }} --}}
                    {{-- @if ( $date->gte( \Carbon\Carbon::now()->startOfDay() ) )
                          
                    @else --}}
                      @if ( $room->pivot->volunteer )
                        <span class="text-muted"> {{ __('Volunteered by') }}:</span> <br> {{ $room->pivot->volunteer_name }}
                      @elseif ($room->pivot->status == 0)
                        <img src="data:image/svg+xml;base64,{{ base64_encode(file_get_contents(asset("media/svg/icons/Code/Question-circle.svg"))) }}" width="20" height="" />
                      @elseif ($room->pivot->status == 1 || $room->pivot->status == 3)
                        <img src="data:image/svg+xml;base64,{{ base64_encode(file_get_contents(asset("media/svg/icons/Code/Done-circle-color.svg"))) }}" width="20" height="" />
                      @else
                        <img src="data:image/svg+xml;base64,{{ base64_encode(file_get_contents(asset("media/svg/icons/Code/Error-circle-color.svg"))) }}" width="20" height="" />
                      @endif
                    {{-- @endif --}}
                  </td>
                  <td class="text-center">{{ Config::get('constants.calendar_room_extra')[$room->pivot->extra] }}</td>
                </tr>
              @endforeach
            @endif
          @endforeach
        </tbody>
      </table>
      <br><br>
      <table>
        <thead>
          <tr>
            <th class="text-right pr-40">
              <img src="data:image/svg+xml;base64,{{ base64_encode(file_get_contents(asset("media/svg/icons/Code/Question-circle.svg"))) }}" width="20" height="" class="inline" />
              <span class="inline lh-2 mr-2">= {{ __('No status') }}</span>
              <img src="data:image/svg+xml;base64,{{ base64_encode(file_get_contents(asset("media/svg/icons/Code/Error-circle-color.svg"))) }}" width="20" height="" class="inline" />
              <span class="inline lh-2">= {{ __('Red Card') }}</span>
            </th>
          </tr>
        </thead>
      </table>
      <!--end::Depa/Restant-->
      <footer>
        <table class="text-center">
          <tr>
            @if ( $footer->has($date->format('d.m.Y')) )
            <td>
              @if ( $footer[$date->format('d.m.Y')]->isNotEmpty() )
                @foreach ($footer[$date->format('d.m.Y')] as $row)
                  From: <u>{{ $row['from'] }}</u>
                  To: <u>{{ $row['to'] }}</u>
                  Pause: <u>{{ $row['pause'] }}</u> <br>
                @endforeach
              @else
                From: <u>---</u>
                To: <u>---</u>
                Pause: <u>---</u> <br>
              @endif
            </td>
            <td>__________________________ <br> Unterschrift Mitarbeiter</td>
            @endif
          </tr>
        </table>        
      </footer>

    @endforeach
    
  </body>
</html>
