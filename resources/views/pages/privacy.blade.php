@extends('layouts.app')

@section('content')
<main id="main">
    @include('partials.page-hero', ['eyebrow' => __('legal.eyebrow'), 'title' => __('legal.privacy_title'), 'copy' => __('legal.privacy_intro')])
    <section class="section">
        <div class="container" style="max-width: 52rem">
            <article class="booking-form">
                <h2>{{ __('legal.data_title') }}</h2>
                <p>{{ __('legal.data_copy') }}</p>
                <h2>{{ __('legal.contact_title') }}</h2>
                <p>{{ __('legal.contact_copy') }} <bdi dir="ltr">{{ $clinic['email'] }}</bdi> {{ __('legal.or_call') }} <bdi dir="ltr">{{ $clinic['phone'] }}</bdi>.</p>
            </article>
        </div>
    </section>
</main>
@endsection
