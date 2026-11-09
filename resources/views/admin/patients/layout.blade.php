@extends('layouts.app')

@section('content')
<main id="main" class="patient-app">
    <div class="patient-shell">
        <aside class="patient-sidebar" aria-label="{{ __('admin.navigation') }}">
            <a class="patient-brand" href="{{ route('admin.dashboard') }}">@include('partials.brand-logo')</a>
            <nav>
                @foreach([
                    ['admin.dashboard', 'dashboard', __('admin.overview')],
                    ['admin.dashboard', 'calendar', __('admin.agenda')],
                    ['admin.patients.index', 'users', __('patients.patient_list')],
                    ['admin.payments.index', 'currency', __('patients.payments')],
                    ['admin.settings', 'settings', __('admin.options')],
                ] as [$routeName, $icon, $label])
                <a href="{{ route($routeName) }}{{ $icon === 'calendar' ? '#agenda' : '' }}" @if(request()->routeIs($routeName === 'admin.patients.index' ? 'admin.patients.*' : $routeName)) aria-current="page" @endif>@include('partials.icon', ['name' => $icon, 'size' => 21]) {{ $label }}</a>
                @endforeach
            </nav>
            <div class="patient-sidebar-foot">{{ session('admin_email') }}<form method="POST" action="{{ route('admin.logout') }}">@csrf<button type="submit" class="patient-logout">@include('partials.icon', ['name'=>'logout']) {{ __('admin.logout') }}</button></form></div>
        </aside>
        <div class="patient-main">
            <header class="patient-topbar">
                <div><strong>{{ $clinic['name'] }}</strong><p>{{ $clinic['profession'] }}</p></div>
                <nav class="patient-languages" aria-label="{{ __('admin.language') }}">@foreach(['fr'=>'FR','en'=>'EN','ar'=>'عربي'] as $code => $label)<a href="{{ route('language.switch', ['locale'=>$code]) }}" @if($locale === $code) aria-current="true" @endif>{{ $label }}</a>@endforeach</nav>
            </header>
            <div class="patient-content">
                @if(session('success'))<div class="patient-alert" role="status">{{ session('success') }}</div>@endif
                @if($errors->any())<div class="patient-alert error" role="alert" data-reopen-dialog="{{ old('_form', '') }}"><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
                @yield('patient-content')
            </div>
        </div>
    </div>
    <nav class="patient-mobile-nav" aria-label="{{ __('admin.navigation') }}">
        <a href="{{ route('admin.dashboard') }}">@include('partials.icon',['name'=>'dashboard']){{ __('admin.overview') }}</a>
        <a href="{{ route('admin.dashboard') }}#agenda">@include('partials.icon',['name'=>'calendar']){{ __('admin.agenda') }}</a>
        <a href="{{ route('admin.patients.index') }}" @if(request()->routeIs('admin.patients.*')) aria-current="page" @endif>@include('partials.icon',['name'=>'users']){{ __('patients.patient_list') }}</a>
        <a href="{{ route('admin.payments.index') }}" @if(request()->routeIs('admin.payments.*')) aria-current="page" @endif>@include('partials.icon',['name'=>'currency']){{ __('patients.payments') }}</a>
        <a href="{{ route('admin.settings') }}">@include('partials.icon',['name'=>'settings']){{ __('admin.options') }}</a>
    </nav>
</main>
@endsection
