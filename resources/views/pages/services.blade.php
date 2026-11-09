@extends('layouts.app')

@section('content')
<main id="main" class="services-detail-page">
    <section class="services-detail-hero">
        <div class="container">
            <div class="services-detail-hero-grid" data-reveal>
                <div>
                    <div class="breadcrumbs"><a href="{{ route('home', ['lang' => app()->getLocale()]) }}">{{ __('site.nav.home') }}</a><span>·</span><span>{{ __('site.nav.services') }}</span></div>
                    <span class="services-detail-kicker">{{ __('services.hero_kicker') }}</span>
                    <h1>{{ __('services.hero_title') }}</h1>
                    <p>{{ __('services.hero_intro') }}</p>
                    <span class="services-detail-note">@include('partials.icon', ['name' => 'shield', 'size' => 15]) {{ __('services.hero_note') }}</span>
                </div>
                <div class="services-detail-hero-art">
                    <div class="services-detail-hero-image"><img src="{{ config('clinic.image.dentist') }}" alt="{{ __('services.items.prosthetics.image_alt') }}" width="900" height="760"></div>
                    <div class="services-detail-hero-badge">@include('partials.icon', ['name' => 'sparkle', 'size' => 16]) <span>{{ __('services.contents_label') }}</span></div>
                </div>
            </div>
            <nav class="services-detail-index" aria-label="{{ __('services.contents_label') }}" data-reveal>
                @foreach(__('services.items') as $key => $service)
                    <a href="#service-{{ $key }}"><span>{{ $service['number'] }}</span><strong>{{ $service['title'] }}</strong><small>{{ __('services.contents_link') }} @include('partials.icon', ['name' => 'arrow', 'size' => 12])</small></a>
                @endforeach
            </nav>
        </div>
    </section>

    <section class="services-detail-catalog">
        <div class="container">
            @foreach(__('services.items') as $index => $service)
                <article id="service-{{ $index }}" class="service-detail-card {{ $loop->odd ? 'service-detail-card-reverse' : '' }}" data-reveal>
                    <div class="service-detail-media">
                        <img src="{{ config('clinic.image.'.$service['image']) }}" alt="{{ $service['image_alt'] }}" loading="lazy" width="900" height="720">
                        <span class="service-detail-media-label">{{ $service['category'] }}</span>
                        <span class="service-detail-media-number">{{ $service['number'] }}</span>
                    </div>
                    <div class="service-detail-content">
                        <span class="service-detail-category">{{ $service['category'] }}</span>
                        <h2>{{ $service['title'] }}</h2>
                        <p class="service-detail-intro">{{ $service['intro'] }}</p>
                        <div class="service-detail-highlights">
                            @foreach($service['highlights'] as $highlight)
                                <span>@include('partials.icon', ['name' => 'check', 'size' => 13]) {{ $highlight }}</span>
                            @endforeach
                        </div>
                        <div class="service-detail-copy">
                            <div><h3>{{ $service['detail_title'] }}</h3><p>{{ $service['detail_text'] }}</p></div>
                            <ul>
                                @foreach($service['benefits'] as $benefit)
                                    <li>@include('partials.icon', ['name' => 'sparkle', 'size' => 13]) {{ $benefit }}</li>
                                @endforeach
                            </ul>
                        </div>
                        <div class="service-detail-footer"><span class="service-detail-disclaimer">{{ $service['note'] }}</span><a class="button small" href="{{ route('appointment', ['lang' => app()->getLocale()]) }}">{{ $service['cta'] }} @include('partials.icon', ['name' => 'arrow', 'size' => 14])</a></div>
                    </div>
                </article>
            @endforeach
        </div>
    </section>

    <section class="services-detail-support">
        <div class="container">
            <div class="services-detail-support-head" data-reveal><div><span class="services-detail-kicker">{{ __('services.why_kicker') }}</span><h2>{{ __('services.why_title') }}</h2></div></div>
            <div class="services-detail-support-grid">
                @foreach(__('services.why_items') as $item)
                    <article class="services-detail-support-card" data-reveal><span class="services-detail-support-icon">@include('partials.icon', ['name' => $item['icon'], 'size' => 16])</span><h3>{{ $item['title'] }}</h3><p>{{ $item['text'] }}</p></article>
                @endforeach
            </div>
        </div>
    </section>

    <section class="services-detail-process">
        <div class="container">
            <div class="services-detail-process-head" data-reveal><span class="services-detail-kicker">{{ __('services.process_kicker') }}</span><h2>{{ __('services.process_title') }}</h2></div>
            <div class="services-detail-process-grid">
                @foreach(__('services.process') as $step)
                    <div class="services-detail-process-step" data-reveal><span>{{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}</span><h3>{{ $step['title'] }}</h3><p>{{ $step['text'] }}</p></div>
                @endforeach
            </div>
        </div>
    </section>

    <section class="services-detail-cta-section">
        <div class="container">
            <div class="services-detail-cta" data-reveal>
                <div><span class="services-detail-kicker">{{ __('services.cta_kicker') }}</span><h2>{{ __('services.cta_title') }}</h2><p>{{ __('services.cta_copy') }}</p></div>
                <div class="services-detail-cta-actions"><a class="button" href="{{ route('appointment', ['lang' => app()->getLocale()]) }}">{{ __('services.cta_button') }} @include('partials.icon', ['name' => 'arrow', 'size' => 15])</a><a class="services-detail-contact-link" href="{{ route('contact', ['lang' => app()->getLocale()]) }}">{{ __('services.cta_contact') }} @include('partials.icon', ['name' => 'arrow', 'size' => 14])</a></div>
            </div>
        </div>
    </section>
</main>
@endsection
