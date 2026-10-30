@extends('layouts.app')

@section('content')
<main id="main">
    @include('partials.page-hero', ['eyebrow' => $rtl ? 'الإدارة' : ($locale === 'en' ? 'Admin' : 'Administration'), 'title' => __('admin.login_title'), 'copy' => __('admin.login_description')])
    <section class="section"><div class="container" style="max-width: 34rem"><form class="booking-form" method="POST" action="{{ route('admin.login.store') }}"><div class="form-head"><h3>{{ __('admin.login_title') }}</h3><span class="required-note">{{ $clinic['name'] }}</span></div>@csrf @if(session('error'))<div class="form-message visible error" role="alert">{{ session('error') }}</div>@endif @if($errors->any())<div class="form-message visible error" role="alert">{{ $errors->first() }}</div>@endif<div class="form-grid" style="grid-template-columns: 1fr"><label class="form-field"><span class="form-label">{{ __('admin.email') }}</span><input class="form-control" name="email" type="email" value="{{ old('email') }}" required autocomplete="username"></label><label class="form-field"><span class="form-label">{{ __('admin.password') }}</span><input class="form-control" name="password" type="password" required autocomplete="current-password"></label></div><div class="form-footer"><span class="form-privacy">{{ __('admin.login_note') }}</span><button class="button" type="submit">{{ __('admin.login') }} @include('partials.icon', ['name' => 'arrow', 'size' => 15])</button></div></form></div></section>
</main>
@endsection
