<article class="service-large">
    <div class="service-image"><img src="{{ config('clinic.image.'.$service['image']) }}" alt="{{ __('site.services.'.$service['key'].'.name') }}" loading="lazy"></div>
    <div>
        <span class="service-tag">{{ __('site.services.'.$service['key'].'.category') }}</span>
        <h3>{{ __('site.services.'.$service['key'].'.name') }}</h3>
        <p>{{ __('site.services.'.$service['key'].'.description') }}</p>
        <div class="service-info"><a class="text-link" href="{{ route('appointment', ['service' => $service['key']]) }}">{{ __('site.appointment') }} @include('partials.icon', ['name' => 'arrow', 'size' => 14])</a><span class="service-number">0{{ $index + 1 }}</span></div>
    </div>
</article>
