@php($locale = app()->getLocale())
<div class="topbar">
    <div class="container topbar-inner">
        <span>{{ $clinic['city'] }} · {{ $locale === 'ar' ? 'عيادة أسنان خاصة' : ($locale === 'en' ? 'Private dental clinic' : 'Cabinet dentaire privé') }}</span>
        <div class="topbar-meta">
            <span>@include('partials.icon', ['name' => 'clock', 'size' => 13]) {{ $clinic['hours'] }}</span>
            <a href="{{ $phoneHref ? 'tel:'.$phoneHref : '#' }}">@include('partials.icon', ['name' => 'phone', 'size' => 13]) {{ $clinic['phone'] }}</a>
        </div>
    </div>
</div>
<header class="site-header">
    <div class="container header-inner">
        <a class="brand" href="{{ route('home') }}" aria-label="{{ $clinic['name'] }}">
            <span class="brand-mark"><span>P</span></span>
            <span class="brand-copy"><span class="brand-name">{{ $clinic['name'] }}</span><span class="brand-caption">{{ $locale === 'ar' ? 'عناية · دقة · ثقة' : ($locale === 'en' ? 'Care · Precision · Trust' : 'Soin · Précision · Confiance') }}</span></span>
        </a>
        <nav id="primary-navigation" class="nav" data-mobile-nav aria-label="{{ $rtl ? 'التنقل الرئيسي' : 'Navigation principale' }}">
            <a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">{{ __('site.nav.home') }}</a>
            <a class="nav-link {{ request()->routeIs('about') ? 'active' : '' }}" href="{{ route('about') }}">{{ __('site.nav.about') }}</a>
            <a class="nav-link {{ request()->routeIs('services') ? 'active' : '' }}" href="{{ route('services') }}">{{ __('site.nav.services') }}</a>
            <a class="nav-link {{ request()->routeIs('appointment*') ? 'active' : '' }}" href="{{ route('appointment') }}">{{ __('site.nav.appointment') }}</a>
            <a class="nav-link {{ request()->routeIs('contact') ? 'active' : '' }}" href="{{ route('contact') }}">{{ __('site.nav.contact') }}</a>
        </nav>
        <div class="header-actions">
            <div class="language-switcher" aria-label="{{ $locale === 'ar' ? 'اختيار اللغة' : ($locale === 'en' ? 'Choose language' : 'Choisir la langue') }}">
                @foreach(['fr' => 'FR', 'en' => 'EN', 'ar' => 'عربي'] as $code => $label)
                    <a class="language-button {{ $locale === $code ? 'active' : '' }}" href="{{ route('language.switch', ['locale' => $code]) }}" @if($locale === $code) aria-current="true" @endif>{{ $label }}</a>
                @endforeach
            </div>
            <a class="button small" href="{{ route('appointment') }}">{{ __('site.appointment') }} @include('partials.icon', ['name' => 'arrow', 'size' => 15])</a>
            <button class="menu-toggle" data-menu-toggle aria-controls="primary-navigation" aria-expanded="false" aria-label="{{ $rtl ? 'القائمة' : 'Menu' }}">@include('partials.icon', ['name' => 'menu', 'size' => 18])</button>
        </div>
    </div>
</header>
