@extends('layouts.app')

@section('content')
<main id="main">
    @include('partials.page-hero', ['eyebrow' => 'Cabinet dentaire', 'title' => 'Mentions légales', 'copy' => 'Les informations essentielles concernant le cabinet et l’utilisation de ce site.'])
    <section class="section"><div class="container" style="max-width: 52rem"><article class="booking-form"><h2>Éditeur du site</h2><p>{{ $clinic['name'] }} — {{ $clinic['profession'] }}, {{ $clinic['city'] }}.</p><p>Adresse : {{ $clinic['address'] }}<br>Téléphone : {{ $clinic['phone'] }}<br>Email : {{ $clinic['email'] }}</p><h2>Utilisation du site</h2><p>Les contenus sont fournis à titre informatif. Les demandes de rendez-vous sont soumises à confirmation par le cabinet.</p></article></div></section>
</main>
@endsection
