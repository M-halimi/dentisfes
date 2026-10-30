@extends('layouts.app')

@section('content')
<main id="main">
    @include('partials.page-hero', ['eyebrow' => 'Cabinet dentaire', 'title' => 'Politique de confidentialité', 'copy' => 'Nous utilisons vos informations uniquement pour traiter votre demande de rendez-vous et vous répondre.'])
    <section class="section"><div class="container" style="max-width: 52rem"><article class="booking-form"><h2>Données collectées</h2><p>Les informations saisies dans le formulaire — identité, téléphone, email et message — sont utilisées pour organiser votre prise de contact avec le cabinet.</p><h2>Conservation et contact</h2><p>Vos données ne sont pas vendues. Pour toute question ou demande de suppression, contactez {{ $clinic['email'] }} ou appelez le {{ $clinic['phone'] }}.</p></article></div></section>
</main>
@endsection
