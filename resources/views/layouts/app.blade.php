@php
    $locale = app()->getLocale();
    $rtl = $locale === 'ar';
    $clinic = \App\Models\ClinicSetting::apply(config('clinic'));
    $phoneHref = preg_replace('/[^0-9+]/', '', $clinic['phone']);
    $whatsappBase = $clinic['whatsapp_number'] ? 'https://wa.me/'.preg_replace('/[^0-9]/', '', $clinic['whatsapp_number']) : 'https://wa.me/';
    $whatsappUrl = $whatsappBase.'?text='.rawurlencode(__('site.whatsapp_message'));
    $structuredData = ['@context' => 'https://schema.org', '@type' => 'Dentist', 'name' => $clinic['name'], 'url' => url('/'), 'telephone' => $clinic['phone'], 'email' => $clinic['email'], 'address' => ['@type' => 'PostalAddress', 'streetAddress' => $clinic['address'], 'addressLocality' => $clinic['city'], 'addressCountry' => 'MA']];
    if ($clinic['lat'] && $clinic['lng']) {
        $structuredData['geo'] = ['@type' => 'GeoCoordinates', 'latitude' => $clinic['lat'], 'longitude' => $clinic['lng']];
    }
@endphp
<!doctype html>
<html lang="{{ $locale }}" dir="{{ $rtl ? 'rtl' : 'ltr' }}">
<head>
    @php($socialImage = url(config('clinic.image.hero', '/images/image-dents/02_aligneur_transparent.png')))
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#0d7c82">
    <meta name="description" content="{{ $seo['description'] }}">
    <link rel="canonical" href="{{ $seo['canonical'] }}">
    <meta property="og:type" content="website">
    <meta property="og:title" content="{{ $seo['title'] }}">
    <meta property="og:description" content="{{ $seo['description'] }}">
    <meta property="og:url" content="{{ $seo['canonical'] }}">
    <title>{{ $seo['title'] }}</title>
    <script type="application/ld+json">{!! json_encode($structuredData, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}</script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <meta name="robots" content="index,follow">
    <meta property="og:locale" content="{{ $locale === 'ar' ? 'ar_MA' : ($locale === 'en' ? 'en_MA' : 'fr_MA') }}">
    <meta property="og:image" content="{{ $socialImage }}">
    <meta property="og:image:alt" content="{{ $seo['title'] ?? config('app.name') }}">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $seo['title'] ?? config('app.name') }}">
    <meta name="twitter:description" content="{{ $seo['description'] ?? '' }}">
    <meta name="twitter:image" content="{{ $socialImage }}">
</head>
@php($isAdminShell = request()->routeIs('admin.dashboard', 'admin.settings'))
<body class="{{ $isAdminShell ? 'admin-dashboard-body' : '' }}">
    @if($isAdminShell)
        @yield('content')
    @else
    <a class="skip-link" href="#main">{{ $rtl ? 'الانتقال إلى المحتوى' : 'Aller au contenu' }}</a>
    @include('partials.header')
    @yield('content')
    @include('partials.footer')
    <a class="whatsapp-float" href="{{ $whatsappUrl }}" target="_blank" rel="noreferrer" aria-label="WhatsApp">@include('partials.icon', ['name' => 'message', 'size' => 21])</a>
    <div class="mobile-appointment"><span>{{ __('site.appointment') }}</span><a class="button small" href="{{ route('appointment') }}">{{ __('site.appointment') }} @include('partials.icon', ['name' => 'arrow', 'size' => 14])</a></div>
    @include('partials.mobile-bottom-nav')
    @endif
</body>
</html>
