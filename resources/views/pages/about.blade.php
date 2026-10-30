@extends('layouts.app')

@php
    $clinic = config('clinic');
    $clinicName = $clinic['name'] ?? 'Anas Kheribech';
    $city = $clinic['city'] ?? 'Fès';
    $address = $clinic['address'] ?? '[Adresse]';
    $hours = $clinic['hours'] ?? 'Lun — Sam · 09:00 — 18:00';
    $rtl = app()->getLocale() === 'ar';
    $seo = [
        'title' => ($rtl ? 'عن العيادة — ' : 'À propos d’Anas Kheribech — ') . $city,
        'description' => $rtl
            ? 'اكتشفوا نهج ' . $clinicName . ' الإنساني والدقيق، عيادتكم في ' . $city . '.'
            : 'Découvrez Anas Kheribech, prothésiste dentaire à ' . $city . ' : précision, écoute et solutions de prothèse dentaire adaptées.',
        'canonical' => url()->current(),
    ];
@endphp

@php
    $profilePhone = $clinic['phone'] ?? '07 71 75 10 87';
    $profilePhoneHref = preg_replace('/[^0-9+]/', '', $profilePhone);
    $profileWhatsapp = preg_replace('/[^0-9]/', '', $profilePhone);
    $profileWhatsapp = str_starts_with($profileWhatsapp, '0') ? '212'.substr($profileWhatsapp, 1) : $profileWhatsapp;
    $profileWhatsappUrl = 'https://wa.me/'.$profileWhatsapp.'?text='.rawurlencode('Bonjour Anas, je souhaite obtenir des informations sur vos prestations.');
    $profileInstagram = ltrim($clinic['instagram'] ?? 'dentiste_anas_kheribech', '@');
    $profileGallery = [
        ['src' => '/images/clinic/clinic-room-wide.jpg', 'alt' => 'Vue d’ensemble de l’espace de soin dentaire à Fès', 'label' => 'Espace de soin'],
        ['src' => '/images/clinic/clinic-room-chair.jpg', 'alt' => 'Fauteuil et équipement de soin du cabinet', 'label' => 'Espace de soin'],
        ['src' => '/images/clinic/clinic-room-detail.jpg', 'alt' => 'Détail de la salle de soin et du fauteuil dentaire', 'label' => 'Détail du cabinet'],
        ['src' => '/images/clinic/clinic-reception-wide.jpg', 'alt' => 'Vue large de l’accueil du cabinet dentaire', 'label' => 'Accueil'],
        ['src' => '/images/clinic/clinic-reception.jpg', 'alt' => 'Comptoir d’accueil et supports d’information du cabinet', 'label' => 'Accueil'],
        ['src' => '/images/clinic/clinic-reception-front.jpg', 'alt' => 'Vue frontale de l’espace d’accueil à Fès', 'label' => 'Accueil'],
    ];
@endphp

@if(false)
@section('content')
<style>
    .about-page{background:var(--surface,#f4faff);color:var(--ink,#111d23)}
    .about-hero{padding:clamp(2rem,5vw,4.75rem) 0 2rem;background:var(--surface-low,#eef7fb)}
    .about-hero-card{display:grid;grid-template-columns:minmax(0,1.2fr) minmax(230px,.8fr);gap:2rem;align-items:end;padding:clamp(1.5rem,4vw,4rem);border-radius:1.5rem;background:var(--primary,#001b2e);color:#fff;overflow:hidden}
    .about-kicker{display:inline-flex;margin-bottom:1rem;color:#9ed5d0;font-size:.72rem;font-weight:800;letter-spacing:.13em;text-transform:uppercase}
    .about-hero h1{max-width:760px;margin:0;font-size:clamp(2.2rem,5vw,5rem);line-height:.98;letter-spacing:-.055em;color:#fff}
    .about-hero-content>p{max-width:630px;margin:1.35rem 0 0;color:#c8d9e1;font-size:clamp(1rem,1.5vw,1.2rem);line-height:1.75}
    .about-hero-actions{display:flex;flex-wrap:wrap;gap:1rem;align-items:center;margin-top:2rem}
    .button-light{background:#fff!important;color:var(--primary,#001b2e)!important}
    .about-text-link,.about-cta-phone{color:#d7e6ea;font-weight:800;text-decoration:none}
    .about-text-link span,.about-cta-phone span{color:#c9ad80}
    .about-stat-grid{display:grid;grid-template-columns:1fr;gap:.7rem}
    .about-stat{padding:1rem 0;border-top:1px solid rgba(255,255,255,.24)}
    .about-stat strong{display:block;color:#fff;font-size:clamp(1.35rem,2.2vw,2rem);line-height:1.1}
    .about-stat span{display:block;margin-top:.25rem;color:#a8c0ca;font-size:.8rem;line-height:1.45}
    .about-section-heading{max-width:760px;margin-bottom:2rem}
    .about-section-heading h2{max-width:760px;margin:.55rem 0 0;font-size:clamp(2rem,4vw,3.5rem);line-height:1.05;letter-spacing:-.045em}
    .about-section-heading p{max-width:680px;margin:1rem 0 0;color:var(--muted,#5d6b72);font-size:1.02rem;line-height:1.75}
    .about-section-heading-centered{margin-right:auto;margin-left:auto;text-align:center}
    .about-section-heading-centered p{margin-right:auto;margin-left:auto}
    .about-clinical-grid{display:grid;grid-template-columns:minmax(0,1.35fr) minmax(0,.65fr);gap:1.25rem;align-items:stretch}
    .about-media-frame,.about-media-inset{position:relative;margin:0;overflow:hidden;border-radius:1.25rem;background:#dfeaf0}
    .about-media-frame img,.about-media-frame video,.about-media-inset img{display:block;width:100%;height:100%;object-fit:cover}
    .about-media-large{min-height:480px}
    .about-video-frame{min-height:480px;background:#001b2e}
    .about-video-frame video{object-fit:cover}
    .about-media-frame figcaption{position:absolute;right:1rem;bottom:1rem;left:1rem;padding:.7rem .85rem;border-radius:.7rem;background:rgba(0,27,46,.78);color:#fff;font-size:.75rem;line-height:1.4}
    .about-media-frame figcaption span{color:#b9d0d8}
    .about-specialty-grid{display:grid;grid-template-columns:minmax(0,.85fr) minmax(0,1.15fr);gap:clamp(2rem,6vw,6rem);align-items:center}
    .about-specialty-copy h2{max-width:600px;margin:.55rem 0 1rem;font-size:clamp(2rem,4vw,3.6rem);line-height:1.05;letter-spacing:-.045em}
    .about-specialty-copy p{max-width:560px;color:var(--muted,#5d6b72);line-height:1.75}
    .about-chip-grid{display:flex;flex-wrap:wrap;gap:.65rem;margin:1.5rem 0 2rem}
    .about-chip{padding:.6rem .75rem;border:1px solid rgba(29,105,102,.22);border-radius:999px;color:var(--primary,#001b2e);font-size:.78rem;font-weight:800;background:#fff}
    .button-dark{background:var(--primary,#001b2e)!important;color:#fff!important}
    .about-specialty-media{position:relative;min-height:520px}
    .about-card-photo{height:520px}
    .about-card-photo img{object-position:center 42%}
    .about-media-inset{position:absolute;right:-2rem;bottom:-2rem;width:42%;height:42%;border:.7rem solid var(--surface-low,#eef7fb);box-shadow:0 18px 45px rgba(0,27,46,.18)}
    .about-media-inset img{object-position:center}
    .about-values-grid{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:1rem}
    .about-value-card{min-height:220px;padding:1.35rem;border:1px solid rgba(0,27,46,.09);border-radius:1rem;background:#fff;box-shadow:0 10px 30px rgba(0,27,46,.05)}
    .about-value-card>span{color:var(--teal,#1d6966);font-size:.78rem;font-weight:900;letter-spacing:.12em}
    .about-value-card h3{margin:2.4rem 0 .65rem;font-size:1.12rem;line-height:1.2}
    .about-value-card p{margin:0;color:var(--muted,#5d6b72);font-size:.9rem;line-height:1.6}
    .about-process-list{display:grid;gap:.75rem;max-width:900px}
    .about-process-step{display:grid;grid-template-columns:3.2rem 1fr 2rem;gap:1rem;align-items:center;padding:1.2rem 1.3rem;border:1px solid rgba(0,27,46,.1);border-radius:1rem;background:#fff}
    .about-process-step>span:first-child{color:var(--teal,#1d6966);font-weight:900}
    .about-process-step h3{margin:0;font-size:1.05rem}
    .about-process-step p{margin:.3rem 0 0;color:var(--muted,#5d6b72);font-size:.9rem;line-height:1.5}
    .about-step-arrow{color:var(--gold,#c9ad80);font-size:1.3rem;text-align:right}
    .about-location-card{display:grid;grid-template-columns:1.25fr 1fr 1fr auto;gap:1.5rem;align-items:end;padding:clamp(1.4rem,3vw,2.4rem);border:1px solid rgba(0,27,46,.1);border-radius:1.25rem;background:#fff}
    .about-location-card h2{margin:.55rem 0 .2rem;font-size:clamp(1.5rem,3vw,2.6rem)}
    .about-location-city{margin:0;color:var(--teal,#1d6966);font-weight:800}
    .about-location-detail{display:grid;gap:.35rem}
    .about-location-detail span{color:var(--muted,#5d6b72);font-size:.72rem;font-weight:800;letter-spacing:.12em;text-transform:uppercase}
    .about-location-detail strong{font-size:.9rem;line-height:1.45;overflow-wrap:anywhere}
    .about-final-cta{padding-top:0}
    .about-cta-actions{display:flex;flex-wrap:wrap;gap:1rem;align-items:center}
    @media (max-width:1024px){.about-hero-card,.about-specialty-grid{grid-template-columns:1fr}.about-stat-grid{grid-template-columns:repeat(3,1fr)}.about-specialty-media{min-height:440px}.about-card-photo{height:440px}.about-values-grid{grid-template-columns:repeat(2,minmax(0,1fr))}.about-location-card{grid-template-columns:repeat(2,minmax(0,1fr))}}
    @media (max-width:700px){.about-hero{padding-top:1rem}.about-hero-card{gap:2.2rem;padding:1.35rem;border-radius:1.1rem}.about-hero h1{font-size:clamp(2rem,11vw,3.2rem)}.about-hero-actions{align-items:stretch;flex-direction:column}.about-hero-actions .button,.about-text-link{width:100%;justify-content:center;text-align:center}.about-stat-grid{gap:.55rem}.about-stat{padding-top:.75rem}.about-stat strong{font-size:1.2rem}.about-stat span{font-size:.68rem}.about-clinical-grid{grid-template-columns:1fr}.about-media-large,.about-video-frame{min-height:330px}.about-specialty-media{min-height:330px}.about-card-photo{height:330px}.about-media-inset{right:-.75rem;bottom:-1rem;width:45%;height:45%;border-width:.45rem}.about-values-grid{grid-template-columns:1fr}.about-value-card{min-height:0}.about-value-card h3{margin-top:1.5rem}.about-location-card{grid-template-columns:1fr;gap:1rem}.about-location-card .button{width:100%;justify-content:center}.about-cta-actions{align-items:stretch;flex-direction:column}.about-cta-actions .button,.about-cta-phone{text-align:center}.about-process-step{grid-template-columns:2.3rem 1fr 1.25rem;padding:.95rem}.about-process-step h3{font-size:.96rem}}
    html[dir="rtl"] .about-media-inset{right:auto;left:-2rem}
    html[dir="rtl"] .about-step-arrow{text-align:left}
    @media (max-width:700px){html[dir="rtl"] .about-media-inset{right:auto;left:-.75rem}}
    @media (prefers-reduced-motion:reduce){.about-page *,.about-page *::before,.about-page *::after{scroll-behavior:auto!important;transition-duration:.01ms!important;animation-duration:.01ms!important}}
</style>
<main id="main" class="about-page">
    <section class="about-hero" aria-labelledby="about-title">
        <div class="container">
            <div class="about-hero-card">
                <div class="about-hero-content">
                    <span class="about-kicker">{{ __('site.about_page.hero_kicker', ['city' => $city]) }}</span>
                    <h1 id="about-title">{{ __('site.about_page.hero_title', ['city' => $city]) }}</h1>
                    <p>{{ __('site.about_page.hero_copy') }}</p>
                    <div class="about-hero-actions">
                        <a class="button button-light" href="{{ route('appointment') }}">{{ __('site.about_page.appointment_link') }} @include('partials.icon', ['name' => 'arrow', 'size' => 16])</a>
                        <a class="about-text-link" href="{{ route('contact') }}">{{ __('site.contact_us') }} <span aria-hidden="true">{{ $rtl ? '↖' : '↗' }}</span></a>
                    </div>
                </div>
                <div class="about-stat-grid" aria-label="{{ $rtl ? 'معلومات عن العيادة' : 'Repères du cabinet' }}">
                    <div class="about-stat"><strong>6</strong><span>{{ __('site.about_page.stat_disciplines') }}</span></div>
                    <div class="about-stat"><strong>1</strong><span>{{ __('site.about_page.stat_follow_up') }}</span></div>
                    <div class="about-stat"><strong>{{ $city }}</strong><span>{{ __('site.about_page.stat_clinic') }}</span></div>
                </div>
            </div>
        </div>
    </section>

    <section class="section about-section" aria-labelledby="clinical-title">
        <div class="container">
            <div class="about-section-heading">
                <span class="eyebrow">{{ __('site.about_page.clinical_eyebrow') }}</span>
                <h2 id="clinical-title">{{ __('site.about_page.clinical_title') }}</h2>
                <p>{{ __('site.about_page.clinical_copy') }}</p>
            </div>
            <div class="about-clinical-grid">
                <figure class="about-media-frame about-media-large">
                    <img src="{{ $clinic['image']['about_hero'] ?? ($clinic['image']['interior'] ?? '/images/image-dents/01_instrument_dentaire.png') }}" alt="{{ __('site.about_page.image_alt') }}" width="1600" height="1200" loading="lazy" decoding="async">
                    <figcaption>{{ __('site.about_page.image_caption') }}</figcaption>
                </figure>
                <figure class="about-media-frame about-video-frame">
                    <video controls preload="metadata" poster="{{ $clinic['image']['about_hero'] ?? ($clinic['image']['interior'] ?? '/images/image-dents/01_instrument_dentaire.png') }}" playsinline>
                        <source src="{{ $clinic['image']['video'] ?? '' }}" type="video/quicktime">
                        {{ __('site.about_page.video_unavailable') }}
                    </video>
                    <figcaption>{{ __('site.about_page.video_caption') }} <span>· {{ __('site.about_page.video_note') }}</span></figcaption>
                </figure>
            </div>
        </div>
    </section>

    <section class="section section-tint about-specialty-section" aria-labelledby="specialty-title">
        <div class="container about-specialty-grid">
            <div class="about-specialty-copy">
                <span class="eyebrow">{{ __('site.about_page.specialty_eyebrow') }}</span>
                <h2 id="specialty-title">{{ __('site.about_page.specialty_title') }}</h2>
                <p>{{ __('site.about_page.specialty_copy') }}</p>
                <div class="about-chip-grid" aria-label="{{ __('site.about_page.solutions_label') }}">
                    @foreach(__('site.about_page.chips') as $chip)<span class="about-chip">{{ $chip }}</span>@endforeach
                </div>
                <a class="button button-dark" href="{{ route('services') }}">{{ __('site.about_page.services_link') }} <span aria-hidden="true">{{ $rtl ? '↖' : '↗' }}</span></a>
            </div>
            <div class="about-specialty-media">
                <figure class="about-media-frame about-card-photo">
                    <img src="{{ $clinic['image']['clinic_card'] ?? ($clinic['image']['prosthesis'] ?? '/images/image-dents/03_radiographie_dentaire.png') }}" alt="{{ $rtl ? 'صورة تعريفية بالعيادة' : 'Carte de présentation du cabinet dentaire' }}" width="1600" height="1200" loading="lazy" decoding="async">
                </figure>
                <figure class="about-media-inset">
                    <img src="{{ $clinic['image']['prosthesis_detail'] ?? ($clinic['image']['prosthesis'] ?? '/images/image-dents/06_prothese_complete_angle.png') }}" alt="{{ $rtl ? 'نموذج أسنان لشرح حل تركيبي' : 'Modèle dentaire utilisé pour expliquer une solution prothétique' }}" width="1600" height="1200" loading="lazy" decoding="async">
                </figure>
            </div>
        </div>
    </section>

    <section class="section about-values-section" aria-labelledby="values-title">
        <div class="container">
            <div class="about-section-heading about-section-heading-centered">
                <span class="eyebrow">{{ __('site.about_page.values_eyebrow') }}</span>
                <h2 id="values-title">{{ __('site.about_page.values_title') }}</h2>
                <p>{{ __('site.about_page.values_copy') }}</p>
            </div>
            <div class="about-values-grid">
                @foreach(__('site.about_page.values') as $index => $value)
                    <article class="about-value-card"><span>{{ str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) }}</span><h3>{{ $value['title'] }}</h3><p>{{ $value['text'] }}</p></article>
                @endforeach
            </div>
        </div>
    </section>

    <section class="section section-tint about-process-section" aria-labelledby="process-title">
        <div class="container">
            <div class="about-section-heading">
                <span class="eyebrow">{{ __('site.about_page.process_eyebrow') }}</span>
                <h2 id="process-title">{{ __('site.about_page.process_title') }}</h2>
            </div>
            <div class="about-process-list">
                @foreach(__('site.about_page.process') as $index => $step)
                    <article class="about-process-step"><span>{{ str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) }}</span><div><h3>{{ $step['title'] }}</h3><p>{{ $step['text'] }}</p></div><span class="about-step-arrow" aria-hidden="true">{{ $rtl ? '↖' : '↗' }}</span></article>
                @endforeach
            </div>
        </div>
    </section>

    <section class="section about-location-section" aria-labelledby="location-title">
        <div class="container">
            <div class="about-location-card">
                <div><span class="eyebrow">{{ __('site.about_page.location_eyebrow') }}</span><h2 id="location-title">{{ $clinicName }}</h2><p class="about-location-city">{{ $rtl ? $city.'، المغرب' : $city.', Maroc' }}</p></div>
                <div class="about-location-detail"><span>{{ __('site.about_page.address') }}</span><strong>{{ $address }}</strong></div>
                <div class="about-location-detail"><span>{{ __('site.about_page.hours') }}</span><strong>{{ $hours }}</strong></div>
                <a class="button button-dark" href="{{ route('contact') }}">{{ __('site.about_page.access_link') }} <span aria-hidden="true">{{ $rtl ? '↖' : '↗' }}</span></a>
            </div>
        </div>
    </section>

    <section class="section about-final-cta" aria-labelledby="about-cta-title">
        <div class="container">
            <div class="cta-banner">
                <div class="cta-content">
                    <div><span class="eyebrow">{{ __('site.about_page.cta_eyebrow') }}</span><h2 id="about-cta-title" class="section-heading">{{ __('site.about_page.cta_title') }}</h2></div>
                    <div class="about-cta-actions">
                        <a class="button button-light" href="{{ route('appointment') }}">{{ __('site.about_page.appointment_link') }} @include('partials.icon', ['name' => 'arrow', 'size' => 16])</a>
                        <a class="about-cta-phone" href="tel:{{ preg_replace('/[^0-9+]/', '', $clinic['phone'] ?? '') }}">{{ __('site.about_page.call_link') }} <span aria-hidden="true">{{ $rtl ? '↖' : '↗' }}</span></a>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>
@endsection

@endif

@section('content')
<style>
    .anas-about{--anas-navy:#001b2e;--anas-teal:#73b8b5;--anas-gold:#c9ad80;--anas-ink:#111d23;--anas-muted:#5d6b72;overflow:hidden;background:#f4faff;color:var(--anas-ink)}
    .anas-about .container{width:min(calc(100% - 2rem),1280px)}
    .anas-about__hero{padding:1.1rem 0 3rem;background:#f4faff}.anas-about__hero-grid{display:grid;grid-template-columns:minmax(0,1.05fr) minmax(300px,.95fr);gap:clamp(2rem,6vw,6rem);align-items:center;min-height:590px;padding:clamp(1.5rem,5vw,4.5rem);border-radius:1.5rem;background:var(--anas-navy);color:#fff}.anas-about__kicker,.anas-about__eyebrow{display:inline-flex;align-items:center;gap:.55rem;color:var(--anas-teal);font-size:.72rem;font-weight:800;letter-spacing:.14em;line-height:1.4;text-transform:uppercase}.anas-about__kicker::before,.anas-about__eyebrow::before{width:1.8rem;height:1px;background:var(--anas-gold);content:""}.anas-about__hero h1{max-width:680px;margin:1.25rem 0;color:#fff;font-size:clamp(2.8rem,6vw,5.8rem);font-weight:600;letter-spacing:-.065em;line-height:.96}.anas-about__hero-copy{max-width:600px;color:#c8d9e1;font-size:clamp(1rem,1.45vw,1.15rem);line-height:1.75}.anas-about__actions{display:flex;flex-wrap:wrap;gap:.8rem;margin-top:2rem}.anas-about__hero .button{background:#fff;color:var(--anas-navy)}.anas-about__hero .button:hover{background:var(--anas-teal);color:var(--anas-navy)}.anas-about__outline-link{display:inline-flex;align-items:center;gap:.45rem;min-height:3.15rem;padding:.75rem 1.1rem;color:#dbe8ed;font-size:.82rem;font-weight:800}.anas-about__outline-link:hover{color:var(--anas-teal)}.anas-about__hero-note{display:flex;align-items:center;gap:.55rem;margin-top:1.4rem;color:#9db5bf;font-size:.8rem}.anas-about__hero-note svg{color:var(--anas-teal)}.anas-about__hero-art{position:relative;min-height:490px}.anas-about__hero-photo{position:absolute;inset:0 0 1.5rem 2rem;overflow:hidden;border:7px solid rgba(255,255,255,.92);border-radius:12rem 1.5rem 1.5rem 1.5rem;box-shadow:0 1.8rem 4rem rgba(0,0,0,.24);transform:rotate(2deg)}.anas-about__hero-photo img{display:block;width:100%;height:100%;object-fit:cover;object-position:center 52%}.anas-about__hero-label{position:absolute;right:-.5rem;bottom:0;display:flex;align-items:center;gap:.65rem;max-width:15rem;padding:.85rem 1rem;border:1px solid rgba(255,255,255,.16);border-radius:.85rem;background:rgba(255,255,255,.1);backdrop-filter:blur(10px);color:#fff;font-size:.78rem;font-weight:800;line-height:1.4}.anas-about__hero-label span{display:block;color:#9edbd8;font-size:.68rem;font-weight:600}.anas-about__section{padding:clamp(4.5rem,8vw,7.5rem) 0}.anas-about__section--tint{background:#e9f6fd}.anas-about__section--white{background:#fff}.anas-about__section-head{max-width:720px;margin-bottom:2.25rem}.anas-about__section-head h2,.anas-about__profile-copy h2,.anas-about__philosophy h2{max-width:720px;margin:.7rem 0 1rem;color:var(--anas-navy);font-size:clamp(2.15rem,4.5vw,4.2rem);letter-spacing:-.06em;line-height:1.02}.anas-about__section-head p,.anas-about__profile-copy p{max-width:660px;margin:0;color:var(--anas-muted);line-height:1.8}.anas-about__profile{display:grid;grid-template-columns:minmax(280px,.9fr) minmax(0,1.1fr);gap:clamp(2rem,7vw,7rem);align-items:center}.anas-about__profile-photo{position:relative;min-height:560px;overflow:hidden;border-radius:1.25rem;background:#dbe9ee;box-shadow:0 1.5rem 3rem rgba(0,27,46,.12)}.anas-about__profile-photo::after{position:absolute;inset:auto 0 0;height:35%;background:linear-gradient(transparent,rgba(0,27,46,.6));content:""}.anas-about__profile-photo img{display:block;width:100%;height:100%;object-fit:cover;object-position:center 53%}.anas-about__profile-caption{position:absolute;z-index:1;right:1.2rem;bottom:1.1rem;left:1.2rem;color:#fff;font-size:.8rem;font-weight:700}.anas-about__profile-copy h2{font-size:clamp(2.15rem,4vw,3.7rem)}.anas-about__profile-copy p+p{margin-top:1rem}.anas-about__profile-facts{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:.75rem;margin:2rem 0}.anas-about__fact{padding:1rem;border:1px solid rgba(0,27,46,.1);border-radius:.85rem;background:#fff}.anas-about__fact strong,.anas-about__fact span{display:block}.anas-about__fact strong{color:var(--anas-navy);font-size:.9rem}.anas-about__fact span{margin-top:.2rem;color:var(--anas-muted);font-size:.75rem}.anas-about__commitments{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:1rem}.anas-about__commitment{min-height:220px;padding:1.35rem;border-top:2px solid var(--anas-gold);border-radius:1rem;background:#fff;box-shadow:0 10px 30px rgba(0,27,46,.05);transition:transform .2s ease,box-shadow .2s ease}.anas-about__commitment:hover{box-shadow:0 1rem 2.2rem rgba(0,27,46,.1);transform:translateY(-.25rem)}.anas-about__commitment-number{color:#1d6966;font-size:.75rem;font-weight:900;letter-spacing:.12em}.anas-about__commitment h3{margin:2.5rem 0 .6rem;color:var(--anas-navy);font-size:1.08rem;line-height:1.2}.anas-about__commitment p{margin:0;color:var(--anas-muted);font-size:.88rem;line-height:1.65}.anas-about__gallery{display:grid;grid-template-columns:1.25fr .75fr .75fr;grid-auto-rows:230px;gap:1rem}.anas-about__gallery-item{position:relative;overflow:hidden;min-height:0;padding:0;border:0;border-radius:1rem;background:#dbe9ee;cursor:zoom-in}.anas-about__gallery-item:first-child{grid-row:span 2}.anas-about__gallery-item img{display:block;width:100%;height:100%;object-fit:cover;transition:transform .35s ease}.anas-about__gallery-item:hover img{transform:scale(1.04)}.anas-about__gallery-item-caption{position:absolute;right:.75rem;bottom:.75rem;left:.75rem;padding:.55rem .65rem;border-radius:.55rem;background:rgba(0,27,46,.76);color:#fff;font-size:.68rem;font-weight:700;text-align:left}.anas-about__philosophy{display:grid;grid-template-columns:minmax(0,.8fr) minmax(0,1.2fr);gap:clamp(2rem,7vw,8rem);align-items:center;padding:clamp(2rem,6vw,5rem);border-radius:1.5rem;background:var(--anas-navy);color:#fff}.anas-about__philosophy h2{color:#fff}.anas-about__philosophy p{max-width:620px;margin:0;color:#c8d9e1;font-size:1.05rem;line-height:1.85}.anas-about__philosophy-mark{display:grid;place-items:center;width:10rem;height:10rem;border:1px solid rgba(201,173,128,.6);border-radius:50%;color:var(--anas-gold);font-size:4rem;font-family:Georgia,serif}.anas-about__contact{display:grid;grid-template-columns:minmax(0,1fr) auto;gap:2rem;align-items:center;padding:clamp(1.5rem,4vw,3rem);border:1px solid rgba(0,27,46,.1);border-radius:1.25rem;background:#fff;box-shadow:0 1.5rem 4rem rgba(0,27,46,.06)}.anas-about__contact h2{margin:.6rem 0 .35rem;color:var(--anas-navy);font-size:clamp(1.8rem,3.5vw,3rem);letter-spacing:-.045em;line-height:1.05}.anas-about__contact p{margin:0;color:var(--anas-muted)}.anas-about__contact-links{display:flex;flex-wrap:wrap;gap:1rem;margin-top:1.25rem;color:var(--anas-navy);font-size:.8rem;font-weight:800}.anas-about__contact-links a:hover{color:#1d6966}.anas-about__contact-actions{display:flex;flex-wrap:wrap;gap:.75rem;justify-content:flex-end}.anas-about__contact-actions .button{white-space:nowrap}.anas-about__lightbox{position:fixed;z-index:110;inset:0;display:grid;place-items:center;padding:1.25rem;background:rgba(0,17,29,.88);opacity:0;pointer-events:none;transition:opacity .2s ease}.anas-about__lightbox.is-open{opacity:1;pointer-events:auto}.anas-about__lightbox img{max-width:min(94vw,1100px);max-height:84vh;border:4px solid #fff;border-radius:.75rem;box-shadow:0 2rem 5rem rgba(0,0,0,.35)}.anas-about__lightbox button{position:absolute;top:1.25rem;right:1.25rem;display:grid;place-items:center;width:2.75rem;height:2.75rem;border:1px solid rgba(255,255,255,.45);border-radius:50%;background:rgba(255,255,255,.1);color:#fff;cursor:pointer;font-size:1.4rem}.anas-about__lightbox p{position:absolute;right:1.25rem;bottom:1.25rem;left:1.25rem;margin:0;text-align:center;color:#fff;font-size:.85rem}
    @media (max-width:1024px){.anas-about__hero-grid,.anas-about__profile{grid-template-columns:1fr}.anas-about__hero-grid{gap:2rem}.anas-about__hero-art{min-height:420px;max-width:550px;width:100%;margin:0 auto}.anas-about__profile-photo{min-height:460px;max-width:620px}.anas-about__commitments{grid-template-columns:repeat(2,minmax(0,1fr))}.anas-about__philosophy{grid-template-columns:1fr}.anas-about__philosophy-mark{width:7rem;height:7rem;font-size:2.8rem}}
    @media (max-width:700px){.anas-about__hero{padding-top:0}.anas-about__hero-grid{padding:1.35rem;border-radius:1rem}.anas-about__hero h1{font-size:clamp(2.6rem,13vw,4rem)}.anas-about__actions{align-items:stretch;flex-direction:column}.anas-about__actions .button,.anas-about__outline-link{justify-content:center;width:100%}.anas-about__hero-art{min-height:350px}.anas-about__hero-photo{inset:1rem .8rem 1.8rem .8rem;border-width:4px;border-radius:7rem 1rem 1rem 1rem}.anas-about__hero-label{right:0;max-width:12rem;font-size:.72rem}.anas-about__section{padding:4.5rem 0}.anas-about__profile-photo{min-height:430px}.anas-about__profile-facts{grid-template-columns:1fr}.anas-about__commitments{grid-template-columns:1fr}.anas-about__commitment{min-height:0}.anas-about__commitment h3{margin-top:1.5rem}.anas-about__gallery{grid-template-columns:1fr 1fr;grid-auto-rows:170px}.anas-about__gallery-item:first-child{grid-row:span 2;grid-column:span 2}.anas-about__gallery-item-caption{font-size:.6rem}.anas-about__philosophy{padding:1.4rem;border-radius:1rem}.anas-about__philosophy-mark{width:5rem;height:5rem;font-size:2rem}.anas-about__contact{grid-template-columns:1fr}.anas-about__contact-actions{justify-content:flex-start}.anas-about__contact-actions .button{flex:1}.anas-about__contact-links{flex-direction:column;gap:.55rem}}
    @media (prefers-reduced-motion:reduce){.anas-about *,.anas-about *::before,.anas-about *::after{transition-duration:.01ms!important}}
</style>

<main id="main" class="anas-about">
    <section class="anas-about__hero" aria-labelledby="anas-about-title"><div class="container"><div class="anas-about__hero-grid"><div><span class="anas-about__kicker">Prothèse dentaire · {{ $city }}, Maroc</span><h1 id="anas-about-title">À propos d’Anas Kheribech</h1><p class="anas-about__hero-copy">Un savoir-faire au service de votre sourire. La précision, l’écoute et l’attention aux détails au cœur de chaque réalisation.</p><div class="anas-about__actions"><a class="button" href="{{ route('contact') }}">Nous contacter @include('partials.icon', ['name' => 'arrow', 'size' => 16])</a><a class="anas-about__outline-link" href="#engagements">Découvrir notre approche @include('partials.icon', ['name' => 'arrow', 'size' => 15])</a></div><div class="anas-about__hero-note">@include('partials.icon', ['name' => 'check', 'size' => 15]) <span>Un accompagnement personnalisé à Fès.</span></div></div><div class="anas-about__hero-art"><figure class="anas-about__hero-photo"><img src="{{ asset('/images/clinic/anas-kheribech-office.jpg') }}" alt="Anas Kheribech dans son espace professionnel à Fès" width="1200" height="1600" fetchpriority="high"></figure><div class="anas-about__hero-label"><span><strong>Anas Kheribech</strong><span>Prothésiste dentaire à Fès</span></span></div></div></div></div></section>

    <section class="anas-about__section anas-about__section--white" aria-labelledby="profile-title"><div class="container anas-about__profile"><figure class="anas-about__profile-photo"><img src="{{ asset('/images/clinic/anas-kheribech-office.jpg') }}" alt="Portrait d’Anas Kheribech, prothésiste dentaire à Fès" width="1200" height="1600" loading="lazy"><figcaption class="anas-about__profile-caption">Anas Kheribech, prothésiste dentaire à Fès.</figcaption></figure><div class="anas-about__profile-copy"><span class="anas-about__eyebrow">Découvrez Anas Kheribech</span><h2 id="profile-title">Un savoir-faire au service de votre sourire</h2><p>Bienvenue dans l’univers d’Anas Kheribech, prothésiste dentaire basé à Fès, au Maroc.</p><p>Nous mettons l’accent sur la précision, le savoir-faire et l’attention portée aux détails pour proposer des solutions de prothèse dentaire adaptées aux besoins de chaque patient.</p><p>Chaque réalisation est abordée avec sérieux et minutie, en recherchant un équilibre entre esthétique, confort et fonctionnalité.</p><div class="anas-about__profile-facts" aria-label="Informations professionnelles"><div class="anas-about__fact"><strong>Fès, Maroc</strong><span>Espace professionnel</span></div><div class="anas-about__fact"><strong>Prothèse dentaire</strong><span>Savoir-faire et précision</span></div></div><a class="button" href="{{ route('appointment') }}">Prendre rendez-vous @include('partials.icon', ['name' => 'arrow', 'size' => 16])</a></div></div></section>

    <section id="engagements" class="anas-about__section anas-about__section--tint" aria-labelledby="engagements-title"><div class="container"><div class="anas-about__section-head"><span class="anas-about__eyebrow">Nos engagements</span><h2 id="engagements-title">Une attention particulière à chaque réalisation.</h2><p>Parce que chaque sourire est unique, nous privilégions une approche attentive et des solutions adaptées à chaque situation.</p></div><div class="anas-about__commitments"><article class="anas-about__commitment"><span class="anas-about__commitment-number">01</span><h3>Précision et minutie</h3><p>Une attention particulière portée aux détails de chaque réalisation.</p></article><article class="anas-about__commitment"><span class="anas-about__commitment-number">02</span><h3>Esthétique naturelle</h3><p>La recherche de résultats harmonieux, adaptés au sourire de chacun.</p></article><article class="anas-about__commitment"><span class="anas-about__commitment-number">03</span><h3>Écoute et accompagnement</h3><p>Une approche personnalisée pour mieux comprendre les besoins et les attentes.</p></article><article class="anas-about__commitment"><span class="anas-about__commitment-number">04</span><h3>Professionnalisme</h3><p>Le sérieux, le respect et la qualité du travail au cœur de notre démarche.</p></article></div></div></section>

    <section class="anas-about__section anas-about__section--white" aria-labelledby="gallery-title"><div class="container"><div class="anas-about__section-head"><span class="anas-about__eyebrow">Notre espace professionnel</span><h2 id="gallery-title">Un environnement dédié à la précision et au confort.</h2><p>Découvrez notre espace de travail situé à Fès, aménagé pour accueillir les visiteurs dans un cadre professionnel, propre et organisé.</p></div><div class="anas-about__gallery" aria-label="Galerie de l’espace professionnel">@foreach($profileGallery as $photo)<button class="anas-about__gallery-item" type="button" data-lightbox="{{ asset($photo['src']) }}" data-caption="{{ $photo['label'] }} — Fès"><img src="{{ asset($photo['src']) }}" alt="{{ $photo['alt'] }}" width="1200" height="1600" loading="lazy" decoding="async"><span class="anas-about__gallery-item-caption">{{ $photo['label'] }}</span></button>@endforeach</div></div></section>

    <section class="anas-about__section anas-about__section--tint" aria-labelledby="philosophy-title"><div class="container"><div class="anas-about__philosophy"><div class="anas-about__philosophy-mark" aria-hidden="true">✦</div><div><span class="anas-about__eyebrow">Notre philosophie</span><h2 id="philosophy-title">Chaque sourire mérite une attention particulière.</h2><p>Nous croyons qu’un beau sourire va au-delà de l’apparence. Il participe au confort, à la confiance en soi et au bien-être au quotidien. C’est pourquoi nous mettons notre savoir-faire et notre attention au service de chaque réalisation.</p></div></div></div></section>

    <section class="anas-about__section anas-about__section--white" aria-labelledby="contact-title"><div class="container"><div class="anas-about__contact"><div><span class="anas-about__eyebrow">Prenez contact avec nous</span><h2 id="contact-title">Parlons de votre projet de sourire.</h2><p>{{ $clinicName }} — {{ $clinic['profession'] ?? 'Prothésiste dentaire' }} · {{ $city }}, Maroc</p><div class="anas-about__contact-links"><a href="tel:{{ $profilePhoneHref }}">{{ $profilePhone }}</a><a href="mailto:{{ $clinic['email'] }}">{{ $clinic['email'] }}</a><a href="https://instagram.com/{{ $profileInstagram }}" target="_blank" rel="noreferrer">@{{ $profileInstagram }}</a></div></div><div class="anas-about__contact-actions"><a class="button" href="{{ route('contact') }}">Nous contacter @include('partials.icon', ['name' => 'arrow', 'size' => 16])</a><a class="button secondary" href="{{ $profileWhatsappUrl }}" target="_blank" rel="noreferrer">WhatsApp</a></div></div></div></section>

    <div class="anas-about__lightbox" data-lightbox-modal role="dialog" aria-modal="true" aria-label="Aperçu de la photo" aria-hidden="true"><button type="button" data-lightbox-close aria-label="Fermer">×</button><img src="" alt=""><p data-lightbox-caption></p></div>
</main>
@endsection

