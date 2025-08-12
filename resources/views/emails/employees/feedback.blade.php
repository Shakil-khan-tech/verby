@component('mail::message')
# {{ __('Email from') }} {{ config('app.name') }}

{{ __('Dear Management') }},

{{ __('The user has declined the submitted timesheet report') }} <strong>{{ $period }}</strong>.

{{ __('We recommend a prompt review and necessary action to address any concerns.') }} {{ __('Your attention to this matter is appreciated.') }}

@if ($comment)
{{ __('Reason') }}:
<strong>{{ $comment }}</strong>
@endif

@component('mail::button', ['url' => $url])
{{ __('View report') }}
@endcomponent

{{ __('Best regards') }},<br>
{{ config('app.name') }}
@endcomponent
