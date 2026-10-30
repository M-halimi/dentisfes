@php($locale = app()->getLocale())
<nav class="mobile-bottom-nav" aria-label="{{ $locale === 'ar' ? 'التنقل عبر الهاتف' : ($locale === 'en' ? 'Mobile navigation' : 'Navigation mobile') }}">
    <a class="mobile-nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">
        @include('partials.icon', ['name' => 'heart', 'size' => 17])
        <span>{{ $locale === 'ar' ? 'الرئيسية' : ($locale === 'en' ? 'Home' : 'Accueil') }}</span>
    </a>
    <a class="mobile-nav-link {{ request()->routeIs('services') ? 'active' : '' }}" href="{{ route('services') }}">
        @include('partials.icon', ['name' => 'sparkle', 'size' => 17])
        <span>{{ $locale === 'ar' ? 'العلاجات' : ($locale === 'en' ? 'Care' : 'Soins') }}</span>
    </a>
    <a class="mobile-nav-link {{ request()->routeIs('appointment*') ? 'active' : '' }}" href="{{ route('appointment') }}">
        @include('partials.icon', ['name' => 'calendar', 'size' => 17])
        <span>{{ $locale === 'ar' ? 'موعد' : ($locale === 'en' ? 'Book' : 'R.-vous') }}</span>
    </a>
    <a class="mobile-nav-link {{ request()->routeIs('about') ? 'active' : '' }}" href="{{ route('about') }}">
        @include('partials.icon', ['name' => 'tooth', 'size' => 17])
        <span>{{ $locale === 'ar' ? 'العيادة' : ($locale === 'en' ? 'Clinic' : 'Cabinet') }}</span>
    </a>
    <a class="mobile-nav-link {{ request()->routeIs('contact') ? 'active' : '' }}" href="{{ route('contact') }}">
        @include('partials.icon', ['name' => 'pin', 'size' => 17])
        <span>{{ $locale === 'ar' ? 'الوصول' : ($locale === 'en' ? 'Visit' : 'Accès') }}</span>
    </a>
</nav>
