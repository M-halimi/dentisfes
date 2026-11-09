@extends('layouts.app')

@section('content')
<main id="main" class="home-page">
    <section class="home-hero">
        <div class="container home-hero-grid">
            <div class="home-hero-copy" data-reveal>
                <span class="home-location-pill">@include('partials.icon', ['name' => 'pin', 'size' => 13]) {{ __('home.hero_location') }}</span>
                <span class="home-kicker">{{ __('home.hero_kicker') }}</span>
                <h1>{{ __('home.hero_title') }} <span>{{ __('home.hero_title_accent') }}</span></h1>
                <p class="home-hero-description">{{ __('home.hero_description') }}</p>
                <div class="home-hero-actions">
                    <a class="button" href="{{ route('appointment', ['lang' => app()->getLocale()]) }}">{{ __('home.hero_cta') }} @include('partials.icon', ['name' => 'arrow', 'size' => 16])</a>
                    <a class="home-ghost-link" href="#home-services">{{ __('home.hero_secondary') }} @include('partials.icon', ['name' => 'arrow', 'size' => 15])</a>
                </div>
                <div class="home-hero-note">@include('partials.icon', ['name' => 'check', 'size' => 15]) <span>{{ __('home.hero_note') }}</span></div>
            </div>

            <div class="home-hero-art" data-reveal>
                <div class="home-hero-frame">
                    <img src="{{ config('clinic.image.hero') }}" alt="{{ __('home.hero_image_alt') }}" width="896" height="1195" fetchpriority="high">
                    <div class="home-hero-frame-overlay"></div>
                </div>
                <div class="home-hero-float home-hero-float--top">
                    <img src="{{ config('clinic.image.detail') }}" alt="" width="160" height="160">
                    <span>{{ __('home.hero_float_one') }}</span>
                </div>
                <div class="home-hero-float home-hero-float--bottom">
                    <span class="home-float-check">@include('partials.icon', ['name' => 'check', 'size' => 13])</span>
                    <span>{{ __('home.hero_float_two') }}</span>
                </div>
                <div class="home-hero-badge">
                    <span class="home-badge-icon">@include('partials.icon', ['name' => 'shield', 'size' => 18])</span>
                    <span><strong>{{ __('home.hero_badge_title') }}</strong><small>{{ __('home.hero_badge_text') }}</small></span>
                </div>
            </div>
        </div>
    </section>

    <section class="home-proof" aria-label="{{ __('home.hero_kicker') }}">
        <div class="container home-proof-grid">
            @foreach(__('home.proof') as $proof)
                <div class="home-proof-item" data-reveal>
                    <span class="home-proof-icon">@include('partials.icon', ['name' => $proof['icon'], 'size' => 17])</span>
                    <span><strong>{{ $proof['title'] }}</strong><small>{{ $proof['text'] }}</small></span>
                </div>
            @endforeach
        </div>
    </section>

    <section class="home-section home-services" id="home-services">
        <div class="container">
            <div class="home-section-intro" data-reveal>
                <div><span class="home-kicker home-kicker-light">{{ __('home.services_kicker') }}</span><h2>{{ __('home.services_title') }}</h2></div>
                <div><p>{{ __('home.services_intro') }}</p><a class="home-inline-link" href="{{ route('services', ['lang' => app()->getLocale()]) }}">{{ __('home.services_link') }} @include('partials.icon', ['name' => 'arrow', 'size' => 15])</a></div>
            </div>
            <div class="home-service-grid">
                @foreach(__('home.services') as $index => $service)
                    <article class="home-service-card {{ $index === 0 ? 'home-service-card-featured' : '' }}" data-reveal>
                        <div class="home-service-image"><img src="{{ config('clinic.image.'.$service['image']) }}" alt="{{ $service['title'] }}" loading="lazy" width="720" height="520"></div>
                        <div class="home-service-body"><span class="home-service-tag">{{ $service['tag'] }}</span><span class="home-service-number">0{{ $index + 1 }}</span><h3>{{ $service['title'] }}</h3><p>{{ $service['description'] }}</p><a class="home-inline-link" href="{{ route('services', ['lang' => app()->getLocale()]) }}#service-{{ $service['key'] }}">{{ __('site.learn_more') }} @include('partials.icon', ['name' => 'arrow', 'size' => 14])</a></div>
                    </article>
                @endforeach
            </div>
        </div>
    </section>

    <section class="home-tour" aria-labelledby="home-tour-title">
        <div class="container">
            <div class="home-tour-card" data-reveal>
                <div class="home-tour-copy">
                    <span class="home-kicker home-kicker-light">{{ __('home.tour_kicker') }}</span>
                    <h2 id="home-tour-title">{{ __('home.tour_title') }} <span>{{ __('home.tour_title_accent') }}</span></h2>
                    <p>{{ __('home.tour_description') }}</p>
                    <div class="home-tour-details">
                        <span>@include('partials.icon', ['name' => 'pin', 'size' => 16]) {{ __('home.tour_location') }}</span>
                        <span>@include('partials.icon', ['name' => 'heart', 'size' => 16]) {{ __('home.tour_note') }}</span>
                    </div>
                    <a class="home-tour-link" href="{{ route('about', ['lang' => app()->getLocale()]) }}">{{ __('home.tour_link') }} @include('partials.icon', ['name' => 'arrow', 'size' => 17])</a>
                </div>
                <div class="home-tour-media" data-clinic-tour>
                    <video id="home-tour-video" controls playsinline preload="none" tabindex="0" poster="{{ asset('media/about/clinic-tour-poster.webp') }}" aria-label="{{ __('home.tour_video_label') }}" aria-describedby="home-tour-caption">
                        <source src="{{ asset('media/about/clinic-tour-calm.mp4') }}" type="video/mp4">
                        <a href="{{ asset('media/about/clinic-tour-calm.mp4') }}">{{ __('home.tour_play') }}</a>
                    </video>
                    <button class="home-tour-cover" type="button" data-tour-play hidden aria-controls="home-tour-video">
                        <span class="home-tour-play" aria-hidden="true"><svg width="25" height="25" viewBox="0 0 24 24" fill="currentColor"><path d="M8 5.5v13l11-6.5z"/></svg></span>
                        <span>{{ __('home.tour_play') }}</span>
                    </button>
                    <div class="home-tour-media-label" aria-hidden="true"><span></span> {{ __('home.tour_badge') }}</div>
                    <span class="home-tour-caption" id="home-tour-caption">{{ __('home.tour_caption') }}</span>
                    <p class="home-tour-error" data-tour-error hidden role="status">{{ __('home.tour_error') }} <a href="{{ asset('media/about/clinic-tour-calm.mp4') }}">{{ __('home.tour_open') }}</a></p>
                </div>
            </div>
        </div>
    </section>

    <section class="home-section home-why">
        <div class="container home-why-grid">
            <div class="home-why-visual" data-reveal>
                <div class="home-why-image"><img src="{{ config('clinic.image.clinic_card') }}" alt="{{ __('home.why_image_alt') }}" loading="lazy" width="720" height="880"></div>
                <div class="home-why-detail"><img src="{{ config('clinic.image.prosthesis_detail') }}" alt="" loading="lazy" width="320" height="320"></div>
                <div class="home-why-label">@include('partials.icon', ['name' => 'sparkle', 'size' => 15]) {{ __('home.why_image_label') }}</div>
            </div>
            <div class="home-why-content" data-reveal>
                <span class="home-kicker">{{ __('home.why_kicker') }}</span><h2>{{ __('home.why_title') }}</h2><p class="home-section-lead">{{ __('home.why_intro') }}</p>
                <div class="home-feature-grid">
                    @foreach(__('home.why_features') as $feature)
                        <div class="home-feature"><span class="home-feature-icon">@include('partials.icon', ['name' => $feature['icon'], 'size' => 16])</span><span><strong>{{ $feature['title'] }}</strong><small>{{ $feature['text'] }}</small></span></div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    <section class="home-section home-process">
        <div class="container">
            <div class="home-section-intro home-section-intro-centered" data-reveal><div><span class="home-kicker">{{ __('home.process_kicker') }}</span><h2>{{ __('home.process_title') }}</h2></div><p>{{ __('home.process_intro') }}</p></div>
            <div class="home-process-layout">
                <div class="home-process-list">
                    @foreach(__('home.process') as $index => $step)
                        <div class="home-process-step" data-reveal><span class="home-process-number">0{{ $index + 1 }}</span><span><strong>{{ $step['title'] }}</strong><small>{{ $step['text'] }}</small></span></div>
                    @endforeach
                </div>
                <div class="home-process-visual" data-reveal>
                    <img src="{{ config('clinic.image.dental_work') }}" alt="{{ __('home.process_image_alt') }}" loading="lazy" width="1536" height="310">
                    <div class="home-process-gallery" aria-hidden="true">
                        <img src="{{ config('clinic.image.prosthesis') }}" alt="" loading="lazy" width="505" height="335">
                        <img src="{{ config('clinic.image.prosthesis_detail') }}" alt="" loading="lazy" width="503" height="335">
                    </div>
                    <span>@include('partials.icon', ['name' => 'shield', 'size' => 15]) {{ __('home.hero_badge_title') }}</span>
                </div>
            </div>
        </div>
    </section>

    <section class="home-section home-cases">
        <div class="container">
            <div class="home-section-intro" data-reveal><div><span class="home-kicker home-kicker-light">{{ __('home.cases_kicker') }}</span><h2>{{ __('home.cases_title') }}</h2></div><p>{{ __('home.cases_intro') }}</p></div>
            <div class="home-case-gallery" aria-label="{{ __('home.cases_gallery_label') }}" data-reveal>
                <figure class="home-case home-case-large"><img src="{{ config('clinic.image.prosthesis') }}" alt="{{ __('home.cases_gallery_label') }}" loading="lazy" width="900" height="800"><figcaption>{{ __('home.case_note') }}</figcaption></figure>
                <figure class="home-case"><img src="{{ config('clinic.image.prosthesis_detail') }}" alt="" loading="lazy" width="600" height="600"><figcaption>{{ __('home.case_note') }}</figcaption></figure>
                <figure class="home-case"><img src="{{ config('clinic.image.care') }}" alt="" loading="lazy" width="600" height="600"><figcaption>{{ __('home.case_note') }}</figcaption></figure>
            </div>
            <div class="home-testimonials" data-reveal><div><span class="home-kicker home-kicker-light">{{ __('home.testimonials_title') }}</span><p>{{ __('home.testimonials_pending') }}</p></div><div class="home-pending-list"><span>{{ __('home.testimonial_pending_label') }}</span><span>{{ __('home.testimonial_pending_label') }}</span><span>{{ __('home.testimonial_pending_label') }}</span></div></div>
        </div>
    </section>

    <section class="home-section home-cta-section">
        <div class="container">
            <div class="home-cta" data-reveal>
                <div class="home-cta-copy"><span class="home-kicker home-kicker-light">{{ __('home.cta_kicker') }}</span><h2>{{ __('home.cta_title') }}</h2><p>{{ __('home.cta_copy') }}</p><span class="home-cta-note">{{ __('home.cta_contact') }}</span></div>
                <div class="home-cta-actions"><a class="button" href="{{ route('appointment', ['lang' => app()->getLocale()]) }}">{{ __('home.hero_cta') }} @include('partials.icon', ['name' => 'arrow', 'size' => 16])</a><a class="home-cta-contact" href="{{ $phoneHref ? 'tel:'.$phoneHref : '#' }}">@include('partials.icon', ['name' => 'phone', 'size' => 15]) <bdi dir="ltr">{{ $clinic['phone'] }}</bdi></a><a class="home-cta-contact" href="{{ $whatsappUrl }}" target="_blank" rel="noreferrer">@include('partials.icon', ['name' => 'message', 'size' => 15]) WhatsApp</a></div>
                <div class="home-cta-meta"><span>@include('partials.icon', ['name' => 'pin', 'size' => 14]) {{ __('home.location') }}</span><span>@include('partials.icon', ['name' => 'clock', 'size' => 14]) {{ $clinic['hours'] ?: __('home.hours') }}</span></div>
            </div>
        </div>
    </section>
</main>
@endsection
