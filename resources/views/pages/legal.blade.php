@extends('layouts.app')

@section('content')
<main id="main">
    @include('partials.page-hero', ['eyebrow' => __('legal.eyebrow'), 'title' => __('legal.title'), 'copy' => __('legal.intro')])
    <section class="section">
        <div class="container" style="max-width: 52rem">
            <article class="booking-form">
                <h2>{{ __('legal.publisher') }}</h2>
                <p><bdi>{{ $clinic['name'] }}</bdi> — <bdi>{{ $clinic['profession'] }}</bdi>, <bdi>{{ $clinic['city'] }}</bdi>.</p>
                <p>{{ __('contact.address') }} : <bdi>{{ $clinic['address'] }}</bdi><br>{{ __('contact.phone') }} : <bdi dir="ltr">{{ $clinic['phone'] }}</bdi><br>{{ __('contact.email') }} : <bdi dir="ltr">{{ $clinic['email'] }}</bdi></p>
                <h2>{{ __('legal.usage_title') }}</h2>
                <p>{{ __('legal.usage_copy') }}</p>
            </article>
        </div>
    </section>
</main>
@endsection
