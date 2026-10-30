<footer class="site-footer">
    <div class="container">
        <div class="footer-grid">
            <div class="footer-brand">
                <a class="brand" href="{{ route('home') }}"><span class="brand-mark"><span>P</span></span><span class="brand-copy"><span class="brand-name">{{ $clinic['name'] }}</span><span class="brand-caption">{{ app()->getLocale() === 'ar' ? 'عناية · دقة · ثقة' : (app()->getLocale() === 'en' ? 'Care · Precision · Trust' : 'Soin · Précision · Confiance') }}</span></span></a>
                <p class="footer-intro">{{ __('site.footer.text') }}</p>
            </div>
            <div><h3 class="footer-heading">{{ __('site.footer.explore') }}</h3><div class="footer-links"><a href="{{ route('home') }}">{{ __('site.nav.home') }}</a><a href="{{ route('about') }}">{{ __('site.nav.about') }}</a><a href="{{ route('services') }}">{{ __('site.nav.services') }}</a></div></div>
            <div><h3 class="footer-heading">{{ __('site.footer.find_us') }}</h3><div class="footer-links"><a href="{{ route('appointment') }}">{{ __('site.appointment') }}</a><a href="{{ route('contact') }}">{{ __('site.nav.contact') }}</a><a href="{{ $whatsappUrl }}" target="_blank" rel="noreferrer">WhatsApp</a></div></div>
            <div class="footer-contact"><h3 class="footer-heading">{{ __('site.footer.hours') }}</h3><a href="{{ $phoneHref ? 'tel:'.$phoneHref : '#' }}">@include('partials.icon', ['name' => 'phone', 'size' => 14]) {{ $clinic['phone'] }}</a><span>@include('partials.icon', ['name' => 'pin', 'size' => 14]) {{ $clinic['address'] }}, {{ $clinic['city'] }}</span><span>@include('partials.icon', ['name' => 'clock', 'size' => 14]) {{ $clinic['hours'] }}</span></div>
        </div>
        <div class="footer-bottom"><span>© {{ now()->year }} {{ $clinic['name'] }}</span><span><a href="{{ route('legal') }}">{{ __('site.footer.legal') }}</a> · <a href="{{ route('privacy') }}">{{ __('site.footer.privacy') }}</a></span></div>
    </div>
</footer>
