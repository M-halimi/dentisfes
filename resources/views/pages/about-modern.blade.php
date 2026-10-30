@extends('layouts.app')

@php
    $about = __('site.about_page');
    $city = $clinic['city'];
    $replaceCity = static fn (string $value): string => str_replace(':city', $city, $value);
@endphp

@section('content')
<main id="main" class="about-locale-page">
    <section class="about-locale-hero">
        <div class="container about-locale-hero-grid">
            <div class="about-locale-hero-copy" data-reveal>
                <span class="about-locale-kicker">{{ $replaceCity($about['hero_kicker']) }}</span>
                <h1>{{ $replaceCity($about['hero_title']) }}</h1>
                <p>{{ $about['hero_copy'] }}</p>
                <div class="about-locale-actions">
                    <a class="button button-light" href="{{ route('appointment') }}">{{ $about['appointment_link'] }} @include('partials.icon', ['name' => 'arrow', 'size' => 16])</a>
                    <a class="about-locale-link" href="#about-values">{{ $about['services_link'] }} @include('partials.icon', ['name' => 'arrow', 'size' => 15])</a>
                </div>
            </div>
            <div class="about-locale-hero-art" data-reveal>
                <div class="about-locale-hero-photo"><img src="{{ asset('images/clinic/anas-kheribech-office.jpg') }}" alt="{{ $clinic['name'] }} — {{ $about['specialty_title'] }}" fetchpriority="high"></div>
                <div class="about-locale-hero-inset"><img src="{{ asset('images/clinic/clinic-reception-wide.jpg') }}" alt="{{ $about['image_alt'] }}" loading="lazy"></div>
                <span class="about-locale-hero-label"><strong>{{ $clinic['name'] }}</strong><small>{{ $about['specialty_title'] }}</small></span>
            </div>
        </div>
        <div class="container about-locale-stats">
            <div><strong>7+</strong><span>{{ $about['stat_disciplines'] }}</span></div>
            <div><strong>100%</strong><span>{{ $about['stat_follow_up'] }}</span></div>
            <div><strong>Fès</strong><span>{{ $about['stat_clinic'] }}</span></div>
        </div>
    </section>

    <section class="about-locale-section about-locale-section-white">
        <div class="container about-locale-clinical-grid">
            <div class="about-locale-image-card about-locale-owner-card" data-reveal><img src="{{ asset('images/clinic/anas-kheribech-office.jpg') }}" alt="{{ $clinic['name'] }} — {{ $about['specialty_title'] }}" loading="lazy"><span>{{ $clinic['name'] }} · {{ $about['specialty_title'] }}</span></div>
            <div class="about-locale-copy" data-reveal>
                <span class="about-locale-eyebrow">{{ $about['clinical_eyebrow'] }}</span>
                <h2>{{ $about['clinical_title'] }}</h2>
                <p>{{ $about['clinical_copy'] }}</p>
                <div class="about-locale-media-note">@include('partials.icon', ['name' => 'shield', 'size' => 16]) <span>{{ $about['video_note'] }}</span></div>
            </div>
        </div>
    </section>

    <section class="about-locale-section about-locale-section-tint">
        <div class="container about-locale-specialty-grid">
            <div class="about-locale-copy" data-reveal>
                <span class="about-locale-eyebrow">{{ $about['specialty_eyebrow'] }}</span>
                <h2>{{ $about['specialty_title'] }}</h2>
                <p>{{ $about['specialty_copy'] }}</p>
                <span class="about-locale-solutions-label">{{ $about['solutions_label'] }}</span>
                <div class="about-locale-chips">@foreach($about['chips'] as $chip)<span>{{ $chip }}</span>@endforeach</div>
                <a class="button" href="{{ route('services') }}">{{ $about['services_link'] }} @include('partials.icon', ['name' => 'arrow', 'size' => 15])</a>
            </div>
            <div class="about-locale-specialty-art" data-reveal><img src="{{ config('clinic.image.dentist') }}" alt="{{ $about['specialty_title'] }}" loading="lazy"><div>@include('partials.icon', ['name' => 'sparkle', 'size' => 17]) {{ $about['video_caption'] }}</div></div>
        </div>
    </section>

    <section id="about-values" class="about-locale-section about-locale-section-white">
        <div class="container">
            <div class="about-locale-section-head" data-reveal><span class="about-locale-eyebrow">{{ $about['values_eyebrow'] }}</span><h2>{{ $about['values_title'] }}</h2><p>{{ $about['values_copy'] }}</p></div>
            <div class="about-locale-values-grid">@foreach($about['values'] as $index => $value)<article data-reveal><span>0{{ $index + 1 }}</span><h3>{{ $value['title'] }}</h3><p>{{ $value['text'] }}</p></article>@endforeach</div>
        </div>
    </section>

    <section class="about-locale-process">
        <div class="container about-locale-process-grid">
            <div data-reveal><span class="about-locale-eyebrow">{{ $about['process_eyebrow'] }}</span><h2>{{ $about['process_title'] }}</h2></div>
            <div class="about-locale-process-list">@foreach($about['process'] as $index => $step)<article data-reveal><span>0{{ $index + 1 }}</span><div><h3>{{ $step['title'] }}</h3><p>{{ $step['text'] }}</p></div>@include('partials.icon', ['name' => 'arrow', 'size' => 15])</article>@endforeach</div>
        </div>
    </section>

    <section class="about-locale-section about-locale-section-white">
        <div class="container about-locale-location" data-reveal><div><span class="about-locale-eyebrow">{{ $about['location_eyebrow'] }}</span><h2>{{ $clinic['name'] }}</h2><p>{{ $clinic['address'] }} · {{ $city }}, Maroc</p></div><div><span>{{ $about['address'] }}</span><strong>{{ $clinic['address'] }}</strong></div><div><span>{{ $about['hours'] }}</span><strong>{{ $clinic['hours'] }}</strong></div><a class="button" href="{{ route('contact') }}">{{ $about['access_link'] }} @include('partials.icon', ['name' => 'arrow', 'size' => 15])</a></div>
    </section>

    <section class="about-locale-final"><div class="container about-locale-final-inner" data-reveal><div><span class="about-locale-eyebrow">{{ $about['cta_eyebrow'] }}</span><h2>{{ $about['cta_title'] }}</h2></div><a class="button button-light" href="{{ route('appointment') }}">{{ $about['appointment_link'] }} @include('partials.icon', ['name' => 'arrow', 'size' => 16])</a></div></section>
</main>
@endsection
