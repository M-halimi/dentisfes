<footer class="site-footer">
    <div class="container">
        <div class="footer-grid">
            <div class="footer-brand">
                <a class="brand" href="{{ route('home', ['lang' => $locale]) }}" aria-label="DENTAFES">@include('partials.brand-logo')</a>
                <p class="footer-intro">{{ __('site.footer.text') }}</p>
            </div>
            <div><h3 class="footer-heading">{{ __('site.footer.explore') }}</h3><div class="footer-links"><a href="{{ route('home', ['lang' => app()->getLocale()]) }}">{{ __('site.nav.home') }}</a><a href="{{ route('about', ['lang' => app()->getLocale()]) }}">{{ __('site.nav.about') }}</a><a href="{{ route('services', ['lang' => app()->getLocale()]) }}">{{ __('site.nav.services') }}</a></div></div>
            <div><h3 class="footer-heading">{{ __('site.footer.find_us') }}</h3><div class="footer-links"><a href="{{ route('appointment', ['lang' => app()->getLocale()]) }}">{{ __('site.appointment') }}</a><a href="{{ route('contact', ['lang' => app()->getLocale()]) }}">{{ __('site.nav.contact') }}</a><a href="{{ $whatsappUrl }}" target="_blank" rel="noreferrer">WhatsApp</a></div></div>
            <div class="footer-contact"><h3 class="footer-heading">{{ __('site.footer.hours') }}</h3><a href="{{ $phoneHref ? 'tel:'.$phoneHref : '#' }}">@include('partials.icon', ['name' => 'phone', 'size' => 14]) <bdi dir="ltr">{{ $clinic['phone'] }}</bdi></a><span>@include('partials.icon', ['name' => 'pin', 'size' => 14]) {{ $clinic['address'] }}, {{ $clinic['city'] }}</span><span>@include('partials.icon', ['name' => 'clock', 'size' => 14]) <bdi>{{ $clinic['hours'] }}</bdi></span></div>
        </div>
        <div class="footer-bottom"><span>© {{ now()->year }} DENTAFES</span><span><a href="{{ route('legal', ['lang' => app()->getLocale()]) }}">{{ __('site.footer.legal') }}</a> · <a href="{{ route('privacy', ['lang' => app()->getLocale()]) }}">{{ __('site.footer.privacy') }}</a></span></div>
    </div>
</footer>
