@php
    $locale = app()->getLocale();
    $rtl = $locale === 'ar';
    $clinic = \App\Models\ClinicSetting::apply(config('clinic'));
    $phoneHref = preg_replace('/[^0-9+]/', '', $clinic['phone']);
    $whatsappUrl = \App\Models\ClinicSetting::whatsappUrl($clinic);
    $brandName = config('app.name');
    $baseUrl = rtrim(config('seo.url'), '/');
    $isIndexable = request()->routeIs(...config('seo.pages'));
    $isLocalized = request()->routeIs(...config('seo.localized_pages'));
    $seoLocale = $isLocalized ? $locale : 'fr';
    $pageTitle = $seo['title'];
    if (!str_contains($pageTitle, $brandName)) {
        $pageTitle .= ' | '.$brandName;
    }
    $canonical = $isIndexable ? $baseUrl.route(request()->route()->getName(), ['lang' => $seoLocale], false) : null;
    $alternateUrls = [];
    $canonicalAlternates = [];
    if ($isIndexable) {
        foreach ($isLocalized ? config('seo.locales') : ['fr'] as $code) {
            $alternateUrls[$code] = route(request()->route()->getName(), ['lang' => $code]);
            $canonicalAlternates[$code] = $baseUrl.route(request()->route()->getName(), ['lang' => $code], false);
        }
    }
    $socialImage = $baseUrl.'/images/brand/dentafes-social.png';
    $business = [
        '@type' => 'Dentist', '@id' => $baseUrl.'/#clinic', 'name' => $brandName,
        'url' => $baseUrl.'/', 'logo' => $baseUrl.'/images/brand/dentafes-logo.png',
        'image' => $socialImage, 'telephone' => $clinic['phone'], 'email' => $clinic['email'],
        'address' => ['@type' => 'PostalAddress', 'streetAddress' => $clinic['address'], 'addressLocality' => $clinic['city'], 'addressCountry' => 'MA'],
        'areaServed' => ['@type' => 'City', 'name' => $clinic['city']],
    ];
    if (is_numeric($clinic['lat']) && is_numeric($clinic['lng'])) {
        $business['geo'] = ['@type' => 'GeoCoordinates', 'latitude' => (float) $clinic['lat'], 'longitude' => (float) $clinic['lng']];
    }
    $graph = [
        $business,
        ['@type' => 'WebSite', '@id' => $baseUrl.'/#website', 'url' => $baseUrl.'/', 'name' => $brandName, 'inLanguage' => config('seo.locales'), 'publisher' => ['@id' => $baseUrl.'/#clinic']],
        ['@type' => request()->routeIs('about') ? 'AboutPage' : (request()->routeIs('contact') ? 'ContactPage' : 'WebPage'), '@id' => $canonical.'#webpage', 'url' => $canonical, 'name' => $pageTitle, 'description' => $seo['description'], 'inLanguage' => $seoLocale, 'isPartOf' => ['@id' => $baseUrl.'/#website'], 'about' => ['@id' => $baseUrl.'/#clinic']],
    ];
    if ($isIndexable && !request()->routeIs('home')) {
        $graph[] = ['@type' => 'BreadcrumbList', 'itemListElement' => [
            ['@type' => 'ListItem', 'position' => 1, 'name' => __('site.nav.home'), 'item' => $baseUrl.route('home', ['lang' => $seoLocale], false)],
            ['@type' => 'ListItem', 'position' => 2, 'name' => $pageTitle, 'item' => $canonical],
        ]];
    }
    $structuredData = ['@context' => 'https://schema.org', '@graph' => $graph];
@endphp
<!doctype html>
<html lang="{{ $locale }}" dir="{{ $rtl ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#0d7c82">
    <meta name="application-name" content="{{ $brandName }}">
    <meta name="apple-mobile-web-app-title" content="{{ $brandName }}">
    <link rel="icon" href="{{ asset('favicon.ico') }}" sizes="any">
    <link rel="icon" type="image/png" sizes="96x96" href="{{ asset('images/brand/favicon-96.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}">
    <link rel="manifest" href="{{ asset('site.webmanifest') }}">
    <meta name="description" content="{{ $seo['description'] }}">
    <meta name="robots" content="{{ $isIndexable ? 'index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1' : 'noindex, nofollow' }}">
    @if($isIndexable)
    <link rel="canonical" href="{{ $canonical }}">
    @foreach($canonicalAlternates as $code => $alternate)
    <link rel="alternate" hreflang="{{ $code }}" href="{{ $alternate }}">
    @endforeach
    <link rel="alternate" hreflang="x-default" href="{{ $canonicalAlternates['fr'] }}">
    @endif
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="{{ $brandName }}">
    <meta property="og:title" content="{{ $pageTitle }}">
    <meta property="og:description" content="{{ $seo['description'] }}">
    @if($isIndexable)
    <meta property="og:url" content="{{ $canonical }}">
    <script type="application/ld+json">{!! json_encode($structuredData, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) !!}</script>
    @endif
    <title>{{ $pageTitle }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <meta property="og:locale" content="{{ $locale === 'ar' ? 'ar_MA' : ($locale === 'en' ? 'en_MA' : 'fr_MA') }}">
    <meta property="og:image" content="{{ $socialImage }}">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:image:type" content="image/png">
    <meta property="og:image:alt" content="DENTAFES — dentafes.com">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $pageTitle }}">
    <meta name="twitter:description" content="{{ $seo['description'] ?? '' }}">
    <meta name="twitter:image" content="{{ $socialImage }}">
    <meta name="twitter:image:alt" content="DENTAFES — dentafes.com">
</head>
@php($isAdminShell = request()->routeIs('admin.*') && !request()->routeIs('admin.login', 'admin.login.alias', 'admin.login.store'))
<body class="{{ $isAdminShell ? 'admin-dashboard-body' : '' }}">
    @if($isAdminShell)
        @yield('content')
    @else
    <a class="skip-link" href="#main">{{ $rtl ? 'الانتقال إلى المحتوى' : 'Aller au contenu' }}</a>
    @include('partials.header')
    @yield('content')
    @include('partials.footer')
    <a class="whatsapp-float" href="{{ $whatsappUrl }}" target="_blank" rel="noopener noreferrer" aria-label="{{ __('contact.whatsapp_action') }}">@include('partials.icon', ['name' => 'whatsapp', 'size' => 24])</a>
    <div class="mobile-appointment"><span>{{ __('site.appointment') }}</span><a class="button small" href="{{ route('appointment', ['lang' => app()->getLocale()]) }}">{{ __('site.appointment') }} @include('partials.icon', ['name' => 'arrow', 'size' => 14])</a></div>
    @include('partials.mobile-bottom-nav')
    @endif
</body>
</html>
