@component('mail::message')
# {{ __('Email from') }} {{ config('app.name') }}

{{ __('Dear') }} {{ $employee->name }},

{{ __('Here\'s your timesheet report for') }}  <strong>{{ $period }}</strong>.

{{-- $expiration variable not used in this template --}}
@component('mail::panel')
{{ __('Please control your hours and send us your request within 15 days after receiving this email.') }} <br><br>
{{ __('Click in the report on either accept or decline. If you do nothing, it will be automatically accepted after 15 days.') }}
@endcomponent

@component('mail::button', ['url' => $url])
{{ __('View report') }}
@endcomponent

{{ __('Best regards') }},<br>
{{ config('app.name') }}
@endcomponent
