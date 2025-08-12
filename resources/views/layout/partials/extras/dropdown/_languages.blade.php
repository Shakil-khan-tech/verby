{{-- Nav --}}
<ul class="navi navi-hover py-4">
    {{-- Item --}}
    <li class="navi-item">
        <a href="{{ LaravelLocalization::getLocalizedURL('en') }}" class="navi-link">
            <span class="symbol symbol-20 mr-3">
                <img src="{{ asset('media/svg/flags/en.svg') }}" alt=""/>
            </span>
            <span class="navi-text">English</span>
        </a>
    </li>

    {{-- Item --}}
    <li class="navi-item">
        <a href="{{ LaravelLocalization::getLocalizedURL('de') }}" class="navi-link">
            <span class="symbol symbol-20 mr-3">
                <img src="{{ asset('media/svg/flags/de.svg') }}" alt=""/>
            </span>
            <span class="navi-text">Deutsch</span>
        </a>
    </li>
</ul>
