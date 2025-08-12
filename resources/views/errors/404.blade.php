<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" {{ Metronic::printAttrs('html') }} {{ Metronic::printClasses('html') }}>
    <head>
        <meta charset="utf-8"/>

        {{-- Title Section --}}
        <title>{{ config('app.name') }} | Not Found</title>

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
        <link rel="stylesheet" href="{{ asset('css/pages/error/error-3.css') }}">
        <link rel="stylesheet" href="{{ mix('css/app.css') }}">
    </head>

    <body {{ Metronic::printAttrs('body') }} {{ Metronic::printClasses('body') }}>
      <!--begin::Main-->
      <div class="d-flex flex-column flex-root">
        <!--begin::Error-->
  			<div class="error error-3 d-flex flex-row-fluid bgi-size-cover bgi-position-center" style="background-image: url({{ asset('media/error/bg3.jpg') }});">
  				<!--begin::Content-->
  				<div class="px-10 px-md-30 py-10 py-md-0 d-flex flex-column justify-content-md-center">
  					<h1 class="error-title text-stroke text-transparent">{{ $exception->getStatusCode() }}</h1>
  					<p class="display-4 font-weight-boldest text-white mb-12">{{ __('How did you get here.') }} <a href="{{route('home')}}">{{ __('Go home') }}!</a> </p>
  					<p class="font-size-h1 font-weight-boldest text-dark-75">{{ __('Sorry we cannot seem to find the page you are looking for.') }}</p>
  					<p class="font-size-h4 line-height-md">{{ __('There may be a misspelling in the URL entered or the page you are looking for may no longer exist.') }}</p>
  				</div>
  				<!--end::Content-->
  			</div>
  			<!--end::Error-->
      </div>
      <!--end::Main-->



        {{-- Global Config (global config for global JS scripts) --}}
        <script>
            var KTAppSettings = {!! json_encode(config('layout.js'), JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES) !!};
        </script>

        {{-- Global Theme JS Bundle (used by all pages)  --}}
        @foreach(config('layout.resources.js') as $script)
            <script src="{{ asset($script) }}" type="text/javascript"></script>
        @endforeach

    </body>
</html>
