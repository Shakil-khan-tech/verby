@component('mail::message')
# {{ __('Email from') }} {{ config('app.name') }}

<h3>{{ __('Issue is fixed') }}</h3><br>
{{ __('Issue') }}: <strong>{{ $listing->issue->name }}</strong><br>
{{ __('Hotel') }}: <strong>{{ $listing->room->device->name }}</strong><br>
{{ __('Room') }}: <strong>{{ $listing->room->name }}</strong><br>
{{ __('User Requested') }}: <strong>{{ $listing->userRequested->name }}</strong><br>
{{ __('Email Fixed') }}: <strong>{{ $listing->email_fixed }}</strong><br>
{{ __('Date Requested') }}: <strong>{{ $listing->date_requested }}</strong><br>
{{ __('Date Fixed') }}: <strong>{{ $listing->date_fixed }}</strong><br>
{{ __('Comment Requested') }}: <strong>{{ $listing->comment_requested }}</strong><br>
{{ __('Comment Fixed') }}: <strong>{{ $listing->comment_fixed }}</strong><br>

<br>
{{ __('Best regards') }},<br>
{{ config('app.name') }}
@endcomponent
