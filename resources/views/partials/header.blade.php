@php($locale = app()->getLocale())
<header class="site-header">
    <div class="container header-inner">
        <a class="brand" href="{{ route('home', ['lang' => $locale]) }}" aria-label="DENTAFES">
            @include('partials.brand-logo')
        </a>
        <nav id="primary-navigation" class="nav" data-mobile-nav aria-label="{{ $rtl ? 'التنقل الرئيسي' : 'Navigation principale' }}">
            <a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home', ['lang' => app()->getLocale()]) }}">{{ __('site.nav.home') }}</a>
            <a class="nav-link {{ request()->routeIs('about') ? 'active' : '' }}" href="{{ route('about', ['lang' => app()->getLocale()]) }}">{{ __('site.nav.about') }}</a>
            <a class="nav-link {{ request()->routeIs('services') ? 'active' : '' }}" href="{{ route('services', ['lang' => app()->getLocale()]) }}">{{ __('site.nav.services') }}</a>
            <a class="nav-link {{ request()->routeIs('appointment*') ? 'active' : '' }}" href="{{ route('appointment', ['lang' => app()->getLocale()]) }}">{{ __('site.nav.appointment') }}</a>
            <a class="nav-link {{ request()->routeIs('contact') ? 'active' : '' }}" href="{{ route('contact', ['lang' => app()->getLocale()]) }}">{{ __('site.nav.contact') }}</a>
        </nav>
        <div class="header-actions">
            <div class="language-switcher" aria-label="{{ $locale === 'ar' ? 'اختيار اللغة' : ($locale === 'en' ? 'Choose language' : 'Choisir la langue') }}">
                @foreach(['fr' => 'FR', 'en' => 'EN', 'ar' => 'عربي'] as $code => $label)
                    <a class="language-button {{ $locale === $code ? 'active' : '' }}" href="{{ $alternateUrls[$code] ?? route('home', ['lang' => $code]) }}" lang="{{ $code }}" hreflang="{{ $code }}" @if($locale === $code) aria-current="true" @endif>{{ $label }}</a>
                @endforeach
            </div>
            <a class="button small" href="{{ route('appointment', ['lang' => app()->getLocale()]) }}">{{ __('site.appointment') }} @include('partials.icon', ['name' => 'arrow', 'size' => 15])</a>
            <button class="menu-toggle" data-menu-toggle aria-controls="primary-navigation" aria-expanded="false" aria-label="{{ $rtl ? 'القائمة' : 'Menu' }}">@include('partials.icon', ['name' => 'menu', 'size' => 18])</button>
        </div>
    </div>
</header>
