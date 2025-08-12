@component('mail::message')
# {{ __('Email from') }} {{ config('app.name') }}

<h3>{{ __('Issue is requested') }}</h3><br>
{{ __('Issue') }}: <strong>{{ $listing->issue->name }}</strong><br>
{{ __('Hotel') }}: <strong>{{ $listing->room->device->name }}</strong><br>
{{ __('Room') }}: <strong>{{ $listing->room->name }}</strong><br>
{{ __('Date Requested') }}: <strong>{{ $listing->date_requested }}</strong><br>
{{ __('Comment') }}: <strong>{{ $listing->comment_requested }}</strong><br>

@component('mail::button', ['url' => $url])
{{ __('View issue') }}
@endcomponent

<br>
{{ __('Best regards') }},<br>
{{ config('app.name') }}
@endcomponent