<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" {{ Metronic::printAttrs('html') }} {{ Metronic::printClasses('html') }}>
    <head>
        <meta charset="utf-8"/>

        {{-- Title Section --}}
        <title>{{ config('app.name') }} | @yield('title', $page_title ?? '')</title>

        {{-- Meta Data --}}
        <meta name="description" content="@yield('page_description', $page_description ?? '')"/>
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no"/>

        <!-- CSRF Token -->
        <meta name="csrf-token" content="{{ csrf_token() }}">

        {{-- Favicon --}}
        <link rel="shortcut icon" href="{{ asset('media/logos/favicon.ico') }}" />

        {{-- Global Theme Styles (used by all pages) --}}
        @foreach(config('layout.resources.css') as $style)
            <link href="{{ config('layout.self.rtl') ? asset(Metronic::rtlCssPath($style)) : asset($style) }}" rel="stylesheet" type="text/css"/>
        @endforeach

        {{-- Includable CSS --}}
        @yield('styles')
        <link rel="stylesheet" href="{{ mix('css/app.css') }}">
        <script src="{{ asset('messages.js') }}?ver={{ filemtime(public_path('messages.js')) }}" type="text/javascript"></script>
    </head>

    <body {{ Metronic::printAttrs('body') }} {{ Metronic::printClasses('body') }}>

        <div class="d-flex flex-column flex-root">
          @yield('content')
        </div>

        {{-- Global Theme JS Bundle (used by all pages)  --}}
        @foreach(config('layout.resources.js') as $script)
            <script src="{{ asset($script) }}" type="text/javascript"></script>
        @endforeach

        {{-- Includable JS --}}
        @yield('scripts')
        <script type="text/javascript">
          var constants = {
            colors: {!! json_encode( Config::get('constants.colors') ) !!},
            plan_colors: {!! json_encode( Config::get('constants.plans') ) !!},
            actions: {!! json_encode( Config::get('constants.actions') ) !!},
            performs: {!! json_encode( Config::get('constants.performs') ) !!},
            identities: {!! json_encode( Config::get('constants.identities') ) !!},
            room_categories: {!! json_encode( Config::get('constants.room_categories') ) !!},
            clean_types: {!! json_encode( Config::get('constants.clean_types') ) !!},
            calendar_room_extra: {!! json_encode( Config::get('constants.calendar_room_extra') ) !!},
            room_statuses: {!! json_encode( Config::get('constants.room_status') ) !!},
          };
        </script>

    </body>
</html>
