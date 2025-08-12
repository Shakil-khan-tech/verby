@component('mail::message')
# {{ __('Email from') }} {{ config('app.name') }}

{{ __('There is a Suspicious checkin for') }}:<br>

{{ __('Employee') }}: <strong>{{ $employee->name }}</strong><br>
{{ __('Checkout') }}: <strong>{{ date_format( $checkout,'d.m.Y H:i:s' ) }}</strong><br>
{{ __('Checkin') }}: <strong>{{ date_format( $checkin,'d.m.Y H:i:s' ) }}</strong><br>
{{ __('Difference') }}: <strong>{{ $checkin->diffInMinutes($checkout); }}</strong> {{ __('minutes') }}<br>


{{ __('Best regards') }},<br>
{{ config('app.name') }}
@endcomponent
