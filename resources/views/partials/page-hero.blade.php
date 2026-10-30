<section class="page-hero">
    <div class="container page-hero-inner">
        <div>
            <div class="breadcrumbs"><a href="{{ route('home') }}">{{ __('site.nav.home') }}</a><span>·</span><span>{{ $title }}</span></div>
            <span class="eyebrow">{{ $eyebrow }}</span>
            <h1>{{ $title }}</h1>
        </div>
        <p>{{ $copy }}</p>
    </div>
</section>
