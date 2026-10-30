@extends('layouts.app')

@php
    $clinic = config('clinic');
    $rtl = app()->getLocale() === 'ar';
    $phoneHref = preg_replace('/[^0-9+]/', '', $clinic['phone']);
    $whatsappBase = $clinic['whatsapp_number'] ? 'https://wa.me/'.preg_replace('/[^0-9]/', '', $clinic['whatsapp_number']) : 'https://wa.me/';
    $whatsappUrl = $whatsappBase.'?text='.rawurlencode(__('site.whatsapp_message'));
    $seo = ['title' => '404 — '.$clinic['name'], 'description' => $rtl ? 'الصفحة المطلوبة غير موجودة.' : 'La page demandée est introuvable.', 'canonical' => url()->current()];
@endphp

@section('content')
<main id="main"><section class="page-hero"><div class="container page-hero-inner"><div><span class="eyebrow">404</span><h1>{{ $rtl ? 'هذه الصفحة غير موجودة.' : 'Cette page n’existe pas.' }}</h1></div><p>{{ $rtl ? 'يمكنكم العودة إلى الصفحة الرئيسية أو طلب موعد مباشرة.' : 'Vous pouvez revenir à l’accueil ou demander un rendez-vous directement.' }}</p></div></section><section class="section"><div class="container"><div class="cta-banner"><div class="cta-content"><div><span class="eyebrow">{{ $rtl ? 'العودة إلى العيادة' : 'Retour au cabinet' }}</span><h2 class="section-heading">{{ $rtl ? 'نحن هنا لمساعدتكم.' : 'Nous sommes là pour vous aider.' }}</h2></div><a class="button" href="{{ route('home') }}">{{ $rtl ? 'العودة إلى الرئيسية' : 'Retour à l’accueil' }} @include('partials.icon', ['name' => 'arrow', 'size' => 16])</a></div></div></div></section></main>
@endsection
