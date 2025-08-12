<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('Lisitngs') }}</title>

    <link href="http://fonts.googleapis.com/css2?family=Nunito&display=swap" rel="stylesheet">
    @include('pages.pdfs.calendar-style')

  </head>
  <body>
    <h3 class="text-center">{{ __('Active Inventory Requests') }}</h3>
    <table class="bg-white">
      <thead>
        <tr class="">
          <th scope="col" class="text-left p-20" style="width: 33%;">{{ __('Devices') }}: {{ $devices }}</th>
          <th scope="col" class="text-center p-20" style="width: 33%;">{{ __('Period') }}: {{ $period }}</th>
          <th scope="col" class="text-right p-20" style="width: 33%;">{{ __('Query') }}: {{ $query }}</th>
        </tr>
      </thead>
    </table>


    <table id="suppliesTable" class="table bg-white">
      <thead>
        <tr class="text-center">
          <th scope="col">{{ __('Inventory') }}</th>
          <th scope="col">{{ __('Device') }}</th>
          <th scope="col">{{ __('Date requested') }}</th>
          <th scope="col">{{ __('User Requested') }}</th>
          <th scope="col">{{ __('Comment') }}</th>
        </tr>
      </thead>
      <tbody>
        @forelse ($listings as $listing)
        <tr class="table-secondary text-center">
          <td scope="row">{{ $listing->supply->name }}</td>
          <td scope="row">{{ $listing->device->name }}</td>
          <td scope="row">{{ $listing->date_requested }}</td>
          <td scope="row">{{ $listing->userRequested->name }}</td>
          <td scope="row">{{ $listing->comment }}</td>
        </tr>
        @empty
        <tr class="table-secondary text-center">
          <td scope="row" colspan="6">{{ __('No Data for this filter') }}</td>
        </tr>
        @endforelse
      </tbody>
    </table>

  </body>
</html>
