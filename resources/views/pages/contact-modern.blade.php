@extends('layouts.app')

@php
    $contact = __('contact');
    $directionsUrl = 'https://www.google.com/maps/search/?api=1&query='.rawurlencode($clinic['address'].' '.$clinic['city']);
@endphp

@section('content')
<main id="main" class="contact-locale-page">
    <section class="contact-locale-hero">
        <div class="container contact-locale-hero-grid">
            <div data-reveal>
                <span class="contact-locale-kicker">{{ $contact['eyebrow'] }}</span>
                <h1>{{ $contact['title'] }}</h1>
                <p>{{ $contact['intro'] }}</p>
            </div>
            <div class="contact-locale-hero-art" data-reveal>
                <img src="{{ asset('images/clinic/clinic-reception-wide.jpg') }}" alt="{{ $contact['title'] }}" fetchpriority="high">
                <span><bdi>{{ $clinic['city'] }}</bdi> · {{ $contact['country'] }}</span>
            </div>
        </div>
    </section>
    <section class="contact-locale-section">
        <div class="container contact-locale-grid">
            <div class="contact-locale-details" data-reveal>
                <span class="contact-locale-kicker">{{ $contact['eyebrow'] }}</span>
                <h2>{{ $contact['title'] }}</h2>
                <div class="contact-locale-cards">
                    <a class="contact-whatsapp" href="{{ $whatsappUrl }}" target="_blank" rel="noopener noreferrer">
                        <span>@include('partials.icon', ['name' => 'whatsapp', 'size' => 24])</span>
                        <small>{{ $contact['whatsapp'] }}</small>
                        <strong>{{ $contact['whatsapp_action'] }}</strong>
                    </a>
                    <a href="{{ $phoneHref ? 'tel:'.$phoneHref : '#' }}">
                        <span>@include('partials.icon', ['name' => 'phone', 'size' => 22])</span>
                        <small>{{ $contact['phone'] }}</small>
                        <strong><bdi dir="ltr">{{ $clinic['phone'] }}</bdi></strong>
                    </a>
                    <a href="{{ $directionsUrl }}" target="_blank" rel="noopener noreferrer">
                        <span>@include('partials.icon', ['name' => 'pin', 'size' => 22])</span>
                        <small>{{ $contact['address'] }}</small>
                        <strong><bdi>{{ $clinic['address'] }}</bdi> · <bdi>{{ $clinic['city'] }}</bdi></strong>
                    </a>
                    <a href="mailto:{{ $clinic['email'] }}">
                        <span>@include('partials.icon', ['name' => 'mail', 'size' => 22])</span>
                        <small>{{ $contact['email'] }}</small>
                        <strong><bdi dir="ltr">{{ $clinic['email'] }}</bdi></strong>
                    </a>
                    <div>
                        <span>@include('partials.icon', ['name' => 'clock', 'size' => 22])</span>
                        <small>{{ $contact['hours'] }}</small>
                        <strong><bdi>{{ $clinic['hours'] }}</bdi></strong>
                    </div>
                </div>
                <a class="button" href="{{ $directionsUrl }}" target="_blank" rel="noopener noreferrer">
                    @include('partials.icon', ['name' => 'pin', 'size' => 18])
                    {{ $contact['map_link'] }}
                    @include('partials.icon', ['name' => 'arrow', 'size' => 15])
                </a>
            </div>
            <div class="contact-locale-map" data-reveal>
                @if($clinic['map_embed_url'])
                    <iframe src="{{ $clinic['map_embed_url'] }}" title="{{ $contact['map_title'] }}" loading="lazy" style="border:0"></iframe>
                @else
                    <div class="contact-locale-map-placeholder">
                        <div class="map-grid"></div>
                        <span class="map-pin">@include('partials.icon', ['name' => 'pin', 'size' => 22])</span>
                        <strong>{{ $contact['map_pending'] }}</strong>
                        <small><bdi>{{ $clinic['address'] }}</bdi> · <bdi>{{ $clinic['city'] }}</bdi></small>
                    </div>
                @endif
            </div>
        </div>
    </section>
    <section class="contact-locale-final">
        <div class="container contact-locale-final-inner" data-reveal>
            <div>
                <span class="contact-locale-kicker">{{ $contact['cta_kicker'] }}</span>
                <h2>{{ $contact['cta_title'] }}</h2>
                <p>{{ $contact['cta_copy'] }}</p>
            </div>
            <a class="button button-light" href="{{ route('appointment', ['lang' => app()->getLocale()]) }}">
                {{ $contact['cta_action'] }} @include('partials.icon', ['name' => 'arrow', 'size' => 16])
            </a>
        </div>
    </section>
</main>
@endsection
