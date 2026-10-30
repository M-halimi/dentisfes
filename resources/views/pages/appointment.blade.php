@extends('layouts.app')

@section('content')
@php($requestedService = request('service'))
@if(false)
<main id="main">
    @include('partials.page-hero', ['eyebrow' => __('site.booking.eyebrow'), 'title' => __('site.booking.title'), 'copy' => __('site.booking.intro')])
    <section class="section"><div class="container booking-layout"><div class="booking-intro"><span class="eyebrow">{{ __('site.booking.eyebrow') }}</span><h2>{{ __('site.booking.title') }}</h2><p>{{ __('site.booking.intro') }}</p><div class="booking-details"><a class="booking-detail" href="{{ $phoneHref ? 'tel:'.$phoneHref : '#' }}">@include('partials.icon', ['name' => 'phone', 'size' => 17])<span><strong>{{ $rtl ? 'الهاتف' : 'Téléphone' }}</strong><br>{{ $clinic['phone'] }}</span></a><span class="booking-detail">@include('partials.icon', ['name' => 'clock', 'size' => 17])<span><strong>{{ $rtl ? 'ساعات العمل' : 'Horaires d’ouverture' }}</strong><br>{{ $clinic['hours'] }}</span></span><a class="booking-detail" href="{{ $whatsappUrl }}" target="_blank" rel="noreferrer">@include('partials.icon', ['name' => 'message', 'size' => 17])<span><strong>WhatsApp</strong><br>{{ $rtl ? 'اكتبوا على واتساب' : 'Écrire sur WhatsApp' }}</span></a></div></div>
        <form class="booking-form" method="POST" action="{{ route('appointments.store') }}"><div class="form-head"><h3>{{ __('site.form.title') }}</h3><span class="required-note">{{ __('site.form.required') }}</span></div>@csrf
            @if(session('success'))<div class="form-message visible" role="status">{{ session('success') }}</div>@endif
            @if($errors->any())<div class="form-message visible error" role="alert">{{ $rtl ? 'يرجى التحقق من الحقول المطلوبة.' : 'Merci de vérifier les champs du formulaire.' }}</div>@endif
            <div class="form-grid">
                <label class="form-field"><span class="form-label">{{ __('site.form.full_name') }} <span>*</span></span><input class="form-control" name="full_name" value="{{ old('full_name') }}" required autocomplete="name"></label>
                <label class="form-field"><span class="form-label">{{ __('site.form.phone') }} <span>*</span></span><input class="form-control" name="phone" value="{{ old('phone') }}" type="tel" required autocomplete="tel"></label>
                <label class="form-field"><span class="form-label">{{ __('site.form.email') }}</span><input class="form-control" name="email" value="{{ old('email') }}" type="email" autocomplete="email"></label>
                <label class="form-field"><span class="form-label">{{ __('site.form.service') }} <span>*</span></span><select class="form-control" name="service" required><option value="">{{ __('site.form.choose_service') }}</option>@foreach(config('clinic.services') as $service)<option value="{{ $service['key'] }}" @selected(old('service', $requestedService) === $service['key'])>{{ __('site.services.'.$service['key'].'.name') }}</option>@endforeach</select></label>
                <label class="form-field"><span class="form-label">{{ __('site.form.date') }} <span>*</span></span><input class="form-control" name="preferred_date" value="{{ old('preferred_date') }}" type="date" min="{{ now()->toDateString() }}" required></label>
                <label class="form-field"><span class="form-label">{{ __('site.form.time') }} <span>*</span></span><select class="form-control" name="preferred_time" required><option value="">—</option>@foreach(['09:00','10:30','14:00','15:30','17:00'] as $time)<option value="{{ $time }}" @selected(old('preferred_time') === $time)>{{ $time }}</option>@endforeach</select></label>
                <label class="form-field full-span"><span class="form-label">{{ __('site.form.message') }}</span><textarea class="form-control" name="message" placeholder="{{ __('site.form.message_placeholder') }}">{{ old('message') }}</textarea></label>
            </div><div class="form-footer"><span class="form-privacy">{{ __('site.form.privacy') }}</span><button class="button" type="submit">{{ __('site.form.submit') }} @include('partials.icon', ['name' => 'arrow', 'size' => 15])</button></div>
        </form>
    </div></section>
</main>
@endif

<main id="main" class="appointment-modern-page">
    <section class="appointment-modern-hero">
        <div class="container appointment-modern-hero-grid">
            <div class="appointment-modern-hero-copy" data-reveal>
                <div class="breadcrumbs"><a href="{{ route('home') }}">{{ __('site.nav.home') }}</a><span>/</span><span>{{ __('site.nav.appointment') }}</span></div>
                <span class="appointment-kicker">{{ __('appointment.hero_kicker') }}</span>
                <h1>{{ __('appointment.hero_title') }}</h1>
                <p>{{ __('appointment.hero_intro') }}</p>
                <span class="appointment-hero-note">@include('partials.icon', ['name' => 'shield']) {{ __('appointment.hero_note') }}</span>
                <div class="appointment-hero-badge">@include('partials.icon', ['name' => 'calendar']) <span>{{ __('appointment.hero_badge') }}</span></div>
            </div>
            <div class="appointment-modern-hero-art" data-reveal>
                <div class="appointment-hero-main-image"><img src="{{ asset('images/clinic/clinic-reception-wide.jpg') }}" alt="{{ __('appointment.hero_title') }}" fetchpriority="high"></div>
                <div class="appointment-hero-secondary-image"><img src="{{ asset('media/about/clinic-room.webp') }}" alt="{{ __('appointment.contact_title') }}" loading="lazy"></div>
                <div class="appointment-hero-detail-image"><img src="{{ config('clinic.image.hero') }}" alt="{{ __('home.hero_image_alt') }}" loading="lazy"></div>
            </div>
        </div>
        <div class="container appointment-trust-grid">
            @foreach (__('appointment.trust') as $item)
                <div class="appointment-trust-item" data-reveal><span class="appointment-trust-icon">@include('partials.icon', ['name' => $item['icon']])</span><span><strong>{{ $item['title'] }}</strong><small>{{ $item['text'] }}</small></span></div>
            @endforeach
        </div>
    </section>

    <section class="appointment-modern-section">
        <div class="container appointment-modern-layout">
            <aside class="appointment-modern-aside" data-reveal>
                <span class="appointment-kicker">{{ __('appointment.contact_kicker') }}</span>
                <h2>{{ __('appointment.contact_title') }}</h2>
                <div class="appointment-contact-list">
                    <a href="{{ $phoneHref ? 'tel:'.$phoneHref : '#' }}" class="appointment-contact-line"><span class="appointment-contact-icon">@include('partials.icon', ['name' => 'phone'])</span><span><small>{{ __('appointment.phone_label') }}</small><strong>{{ $clinic['phone'] }}</strong></span></a>
                    <div class="appointment-contact-line"><span class="appointment-contact-icon">@include('partials.icon', ['name' => 'clock'])</span><span><small>{{ __('appointment.hours_label') }}</small><strong>{{ $clinic['hours'] }}</strong></span></div>
                    <a href="{{ $whatsappUrl }}" class="appointment-contact-line" target="_blank" rel="noreferrer"><span class="appointment-contact-icon">@include('partials.icon', ['name' => 'message'])</span><span><small>{{ __('appointment.whatsapp_label') }}</small><strong>{{ __('appointment.whatsapp_action') }}</strong></span></a>
                </div>
                <div class="appointment-aside-card"><img src="{{ asset('media/about/clinic-room.webp') }}" alt="{{ __('appointment.contact_title') }}" loading="lazy"><span>@include('partials.icon', ['name' => 'sparkles']) {{ __('appointment.hero_note') }}</span></div>
            </aside>

            <section class="appointment-form-card" data-reveal>
                <div class="appointment-form-head"><div><span class="appointment-kicker">{{ __('appointment.form_kicker') }}</span><h2>{{ __('appointment.form_title') }}</h2><p>{{ __('appointment.form_intro') }}</p></div><span class="appointment-form-step">01 <small>/ 01</small></span></div>
                <form class="appointment-form" method="POST" action="{{ route('appointments.store') }}">
                    @csrf
                    @if (session('success'))<div class="form-message success" role="status">{{ session('success') }}</div>@endif
                    @if ($errors->any())<div class="form-message error" role="alert">{{ __('appointment.error') }}</div>@endif
                    <div class="form-grid">
                        <label class="form-field"><span class="form-label">{{ __('appointment.full_name') }} <span>*</span></span><input id="full_name" class="form-control" name="full_name" value="{{ old('full_name') }}" required autocomplete="name">@error('full_name') <span class="field-error">{{ $message }}</span> @enderror</label>
                        <label class="form-field"><span class="form-label">{{ __('appointment.phone') }} <span>*</span></span><input id="phone" class="form-control" name="phone" value="{{ old('phone') }}" type="tel" required autocomplete="tel">@error('phone') <span class="field-error">{{ $message }}</span> @enderror</label>
                        <label class="form-field"><span class="form-label">{{ __('appointment.email') }}</span><input id="email" class="form-control" name="email" value="{{ old('email') }}" type="email" autocomplete="email">@error('email') <span class="field-error">{{ $message }}</span> @enderror</label>
                        <label class="form-field"><span class="form-label">{{ __('appointment.service') }} <span>*</span></span><select id="service" class="form-control" name="service" required><option value="">{{ __('appointment.choose_service') }}</option>@foreach(config('clinic.services') as $service)<option value="{{ $service['key'] }}" @selected(old('service', $requestedService) === $service['key'])>{{ __('site.services.'.$service['key'].'.name') }}</option>@endforeach</select>@error('service') <span class="field-error">{{ $message }}</span> @enderror</label>
                        <label class="form-field"><span class="form-label">{{ __('appointment.date') }} <span>*</span></span><input id="preferred_date" class="form-control" name="preferred_date" value="{{ old('preferred_date') }}" type="date" min="{{ now()->toDateString() }}" required>@error('preferred_date') <span class="field-error">{{ $message }}</span> @enderror</label>
                        <label class="form-field"><span class="form-label">{{ __('appointment.time') }} <span>*</span></span><select id="preferred_time" class="form-control" name="preferred_time" required><option value="">{{ __('appointment.choose_time') }}</option>@foreach(['09:00','10:30','14:00','15:30','17:00'] as $time)<option value="{{ $time }}" @selected(old('preferred_time') === $time)>{{ $time }}</option>@endforeach</select>@error('preferred_time') <span class="field-error">{{ $message }}</span> @enderror</label>
                        <label class="form-field full-span"><span class="form-label">{{ __('appointment.message') }}</span><textarea id="message" class="form-control" name="message" placeholder="{{ __('appointment.message_placeholder') }}">{{ old('message') }}</textarea>@error('message') <span class="field-error">{{ $message }}</span> @enderror</label>
                    </div>
                    <div class="form-footer"><span class="form-privacy">{{ __('appointment.required') }} · {{ __('appointment.privacy') }}</span><button class="button button-primary" type="submit">{{ __('appointment.submit') }} @include('partials.icon', ['name' => 'arrow-up-right'])</button></div>
                </form>
            </section>
        </div>
    </section>

    <section class="appointment-steps-section"><div class="container"><div class="appointment-section-heading" data-reveal><span class="appointment-kicker">{{ __('appointment.steps_kicker') }}</span><h2>{{ __('appointment.steps_title') }}</h2></div><div class="appointment-steps-grid">@foreach(__('appointment.steps') as $index => $step)<article class="appointment-step-card" data-reveal><span class="appointment-step-number">0{{ $index + 1 }}</span><h3>{{ $step['title'] }}</h3><p>{{ $step['text'] }}</p></article>@endforeach</div></div></section>

    <section class="appointment-final-section"><div class="container appointment-final-inner" data-reveal><div><span class="appointment-kicker">{{ __('appointment.final_kicker') }}</span><h2>{{ __('appointment.final_title') }}</h2><p>{{ __('appointment.final_copy') }}</p></div><a class="button button-light" href="{{ $phoneHref ? 'tel:'.$phoneHref : '#' }}">{{ __('appointment.final_phone') }} @include('partials.icon', ['name' => 'phone'])</a></div></section>
</main>
@endsection
