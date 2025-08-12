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

      {{-- Fonts --}}
      {{ Metronic::getGoogleFontsInclude() }}

      {{-- Global Theme Styles (used by all pages) --}}
      @foreach(config('layout.resources.css') as $style)
          <link href="{{ config('layout.self.rtl') ? asset(Metronic::rtlCssPath($style)) : asset($style) }}" rel="stylesheet" type="text/css"/>
      @endforeach

      {{-- Layout Themes (used by all pages) --}}
      @foreach (Metronic::initThemes() as $theme)
          <link href="{{ config('layout.self.rtl') ? asset(Metronic::rtlCssPath($theme)) : asset($theme) }}" rel="stylesheet" type="text/css"/>
      @endforeach

      {{-- Includable CSS --}}
      @yield('styles')
      <link rel="stylesheet" href="{{ asset('css/pages/error/error-6.css') }}">
      <link rel="stylesheet" href="{{ mix('css/app.css') }}">
    </head>

    <body {{ Metronic::printAttrs('body') }} {{ Metronic::printClasses('body') }}>
      @include('layout.base._header')
      <!--begin::Main-->
      <div class="d-flex flex-column flex-root">
        <!--begin::Error-->
        <div class="error error-6 d-flex flex-row-fluid bgi-size-cover bgi-position-center" style="background-image: url({{ asset('media/error/bg6.jpg') }});">
          <!--begin::Content-->
          <div class="d-flex flex-column flex-row-fluid text-center">
            <h1 class="error-title font-weight-boldest text-white mb-12" style="margin-top: 12rem;">{{ $exception->getStatusCode() }}</h1>
            <p class="display-3 font-weight-boldest text-white mb-12">Oops...</p>
            <p class="display-4 font-weight-bold text-white">{{ $exception->getMessage() }}</p>
          </div>
          <!--end::Content-->
        </div>
        <!--end::Error-->
      </div>
      <!--end::Main-->

      @if (config('layout.extras.user.layout') == 'offcanvas')
          @include('layout.partials.extras.offcanvas._quick-user')
      @endif

      {{-- Global Config (global config for global JS scripts) --}}
      <script>
          var KTAppSettings = {!! json_encode(config('layout.js'), JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES) !!};
      </script>

      {{-- Global Theme JS Bundle (used by all pages)  --}}
      @foreach(config('layout.resources.js') as $script)
          <script src="{{ asset($script) }}" type="text/javascript"></script>
      @endforeach
      <script src="{{ asset('js/custom.js') }}" type="text/javascript"></script>

    </body>
</html>
