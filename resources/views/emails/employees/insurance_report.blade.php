@component('mail::message')
# {{ __('Email from') }} {{ config('app.name') }}

{{ __('Attached you can find the Interim earnings for the following period:') }}
<strong>{{ $period }}</strong>

{{ __('Best regards') }},<br>
{{ config('app.name') }}
@endcomponent
