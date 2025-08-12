@component('mail::message')
# {{ __('Email from') }} {{ config('app.name') }}

<h3>{{ __('Item Inventory is requested') }}</h3><br>
{{ __('Inventory') }}: <strong>{{ $listing->supply->name }}</strong><br>
{{ __('Hotel') }}: <strong>{{ $listing->device->name }}</strong><br>
{{ __('User Requested') }}: <strong>{{ $listing->userRequested->name }}</strong><br>
{{ __('Date Requested') }}: <strong>{{ $listing->date_requested }}</strong><br>
{{ __('Comment') }}: <strong>{{ $listing->comment }}</strong><br>

<br>
{{ __('Best regards') }},<br>
{{ config('app.name') }}
@endcomponent