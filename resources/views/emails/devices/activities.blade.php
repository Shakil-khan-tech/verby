@component('mail::message')
# {{ __('Email from') }} {{ config('app.name') }}

{{ __('Devices with no activities in last 24h') }}:<br>

<table style="width:100%">
    <tr>
        <th>{{ __('Device') }}</th>
        <th>{{ __('Last activity') }}</th>
    </tr>
    @foreach ($devices as $device)
    <tr style="text-align: center">
        <td>{{ $device['name'] }}</td>
        <td>
            @if ( $device['updated_at'] )
            {{ date_format( $device['updated_at'],'H:i:s' ) }} &nbsp;
            {{ date_format( $device['updated_at'],'d.m.Y' ) }}
            @else
                {{ __('No activity') }}
            @endif
        </td>
    </tr>
    @endforeach
</table>

{{ __('Best regards') }},<br>
{{ config('app.name') }}
@endcomponent
