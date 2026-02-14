<header class="site-header">
    <div class="container d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center">
            <a href="javascript:history.back()" class="text-white me-3 fs-5">
                <i class="fas fa-arrow-left"></i>
            </a>
            <a class="logo" href="https://rvrising.com/">
                @if($logo = App\Models\SiteSetting::getLogo())
                <img src="{{ $logo }}" alt="RV Rising">
                @else
                <!-- <i class="fas fa-chart-line"></i> -->
                @endif
                RV Rising
            </a>
        </div>

        <div class="d-flex align-items-center gap-3">
            <a href="https://pricing.rvrising.com/" class="nav-link-custom">Home</a>
            <a href="https://rvrising.com/pr-services/" class="nav-link-custom">PR Services</a>
            <a href="https://rvrising.com/about-us/" class="nav-link-custom d-none d-md-inline">About</a>

            @if(isset($languages) && $languages->count() > 1)
            <div class="dropdown">
                <button class="lang-btn dropdown-toggle" type="button" data-bs-toggle="dropdown">
                    <i class="fas fa-globe me-1"></i>
                    @php
                    $langCode = request('lang', isset($currentLang) ? $currentLang : (isset($defaultLanguage) ? $defaultLanguage->code : 'en'));
                    @endphp
                    {{ $languages->firstWhere('code', $langCode)?->name ?? 'EN' }}
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    @foreach($languages as $lang)
                    <li><a class="dropdown-item {{ $langCode == $lang->code ? 'active' : '' }}" href="?lang={{ $lang->code }}">{{ $lang->name }}</a></li>
                    @endforeach
                </ul>
            </div>
            @endif
        </div>
    </div>
</header>