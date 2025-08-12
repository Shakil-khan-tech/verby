{{-- Extends layout --}}
@extends('layout.default')

{{-- Content --}}
@section('content')

    {{-- Dashboard 1 --}}

    <div class="row">
        <div class="col-lg-12 col-xxl-12">
            @include('pages.widgets._widget-dashboard', ['class' => 'card-stretch gutter-b'])
        </div>

        {{-- <div class="col-lg-6 col-xxl-4">
            @include('pages.widgets._widget-activities', ['class' => 'card-stretch gutter-b'])
        </div> --}}
    </div>

@endsection

{{-- Scripts Section --}}
@section('scripts')
    <script src="{{ mix('js/pages/dashboard/index.js') }}" type="text/javascript"></script>
@endsection
