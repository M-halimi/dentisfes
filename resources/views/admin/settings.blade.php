@extends('layouts.app')

@section('content')
<style>
    .admin-settings-app{min-height:100vh;background:#f7fafb;color:#15383d;padding:28px clamp(18px,4vw,64px) 56px}
    .admin-settings-top{max-width:1240px;margin:0 auto 26px;display:flex;align-items:flex-start;justify-content:space-between;gap:20px}
    .admin-settings-back{display:inline-flex;align-items:center;gap:8px;color:#0d7c82;font-weight:700;text-decoration:none;margin-bottom:12px}
    .admin-settings-top h1{margin:0;font-size:clamp(1.8rem,3vw,2.8rem);letter-spacing:-.04em}
    .admin-settings-top p{margin:8px 0 0;color:#6b8589}
    .admin-settings-layout{max-width:1240px;margin:0 auto;display:grid;grid-template-columns:minmax(0,1.5fr) minmax(260px,.75fr);gap:22px;align-items:start}
    .admin-settings-card,.admin-settings-preview{background:#fff;border:1px solid #e1ecee;border-radius:24px;box-shadow:0 15px 45px rgba(30,76,83,.06)}
    .admin-settings-card{padding:clamp(20px,3vw,34px)}
    .admin-settings-section{padding:0 0 26px;margin:0 0 26px;border-bottom:1px solid #e5eff0}
    .admin-settings-section:last-of-type{margin-bottom:20px}
    .admin-settings-section h2{margin:0 0 5px;font-size:1.05rem}
    .admin-settings-section p{margin:0 0 20px;color:#789095;font-size:.9rem}
    .admin-settings-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:17px}
    .admin-settings-field{display:flex;flex-direction:column;gap:7px}
    .admin-settings-field.full{grid-column:1/-1}
    .admin-settings-field label{font-size:.82rem;font-weight:800;color:#47676c}
    .admin-settings-field input,.admin-settings-field select{width:100%;box-sizing:border-box;border:1px solid #d7e6e8;border-radius:12px;padding:12px 13px;background:#fbfdfd;color:#173d42;font:inherit;outline:none;transition:border-color .2s,box-shadow .2s}
    .admin-settings-field input:focus,.admin-settings-field select:focus{border-color:#0d9ba0;box-shadow:0 0 0 4px rgba(13,155,160,.1)}
    .admin-settings-error{color:#c34d4d;font-size:.78rem}
    .admin-settings-alert{border-radius:12px;padding:12px 14px;margin-bottom:22px;font-weight:700;font-size:.9rem}
    .admin-settings-alert.success{background:#e9f8f1;color:#23734d}
    .admin-settings-alert.error{background:#fff0f0;color:#a94444}
    .admin-settings-check{display:flex;align-items:center;gap:11px;padding:13px 15px;border:1px solid #dcebed;border-radius:13px;background:#fbfdfd;grid-column:1/-1}
    .admin-settings-check input{width:18px;height:18px;accent-color:#0d8d93}
    .admin-settings-check label{font-weight:700;color:#3e6065}
    .admin-settings-actions{display:flex;align-items:center;justify-content:flex-end;gap:14px}
    .admin-settings-submit{border:0;border-radius:12px;background:#0d7c82;color:#fff;padding:13px 20px;font:inherit;font-weight:800;cursor:pointer;box-shadow:0 8px 18px rgba(13,124,130,.22)}
    .admin-settings-submit:hover{background:#09666b}
    .admin-settings-preview{padding:24px;position:sticky;top:24px}
    .admin-settings-preview-kicker{color:#0d8d93;text-transform:uppercase;letter-spacing:.12em;font-size:.7rem;font-weight:900}
    .admin-settings-preview h2{font-size:1.35rem;margin:10px 0 4px;letter-spacing:-.03em}
    .admin-settings-preview p{color:#728b8f;margin:0 0 22px}
    .admin-settings-preview-line{display:flex;gap:12px;align-items:flex-start;padding:13px 0;border-top:1px solid #e8f0f1;color:#49676b;font-size:.9rem}
    .admin-settings-preview-line svg{color:#0d8d93;flex:none}
    .admin-settings-signout{border:0;background:transparent;color:#658084;font:inherit;cursor:pointer;padding:10px 0}
    @media (max-width: 850px){.admin-settings-layout{grid-template-columns:1fr}.admin-settings-preview{position:static}.admin-settings-top{display:block}.admin-settings-signout{padding-top:18px}}
    @media (max-width: 560px){.admin-settings-grid{grid-template-columns:1fr}.admin-settings-field.full,.admin-settings-check{grid-column:auto}.admin-settings-actions{justify-content:stretch}.admin-settings-submit{width:100%}}
</style>

<main id="main" class="admin-settings-app">
    <header class="admin-settings-top">
        <div>
            <a class="admin-settings-back" href="{{ route('admin.dashboard') }}">@include('partials.icon', ['name' => 'arrow', 'size' => 16]) {{ __('admin.dashboard_title') }}</a>
            <h1>Paramètres du cabinet</h1>
            <p>Modifiez les informations affichées sur votre site et les préférences de votre espace admin.</p>
        </div>
        <form method="POST" action="{{ route('admin.logout') }}">
            @csrf
            <button class="admin-settings-signout" type="submit">@include('partials.icon', ['name' => 'logout', 'size' => 16]) {{ __('admin.logout') }}</button>
        </form>
    </header>

    <div class="admin-settings-layout">
        <form class="admin-settings-card" method="POST" action="{{ route('admin.settings.update') }}">
            @csrf
            @method('PUT')

            @if(session('success'))
                <div class="admin-settings-alert success">{{ session('success') }}</div>
            @endif
            @if($errors->any())
                <div class="admin-settings-alert error">{{ __('Veuillez corriger les champs indiqués.') }}</div>
            @endif

            <section class="admin-settings-section">
                <h2>Identité du cabinet</h2>
                <p>Ces informations sont utilisées dans l’en-tête, le pied de page et les pages publiques.</p>
                <div class="admin-settings-grid">
                    <div class="admin-settings-field">
                        <label for="name">Nom du cabinet</label>
                        <input id="name" name="name" value="{{ old('name', $settings['name']) }}" required>
                        @error('name')<span class="admin-settings-error">{{ $message }}</span>@enderror
                    </div>
                    <div class="admin-settings-field">
                        <label for="profession">Profession</label>
                        <input id="profession" name="profession" value="{{ old('profession', $settings['profession']) }}" required>
                        @error('profession')<span class="admin-settings-error">{{ $message }}</span>@enderror
                    </div>
                    <div class="admin-settings-field full">
                        <label for="practitioner_name">{{ __('patients.practitioner_name') }}</label>
                        <input id="practitioner_name" name="practitioner_name" maxlength="120" value="{{ old('practitioner_name', $settings['practitioner_name']) }}">
                        @error('practitioner_name')<span class="admin-settings-error">{{ $message }}</span>@enderror
                    </div>
                    <div class="admin-settings-field">
                        <label for="city">Ville</label>
                        <input id="city" name="city" value="{{ old('city', $settings['city']) }}" required>
                        @error('city')<span class="admin-settings-error">{{ $message }}</span>@enderror
                    </div>
                    <div class="admin-settings-field">
                        <label for="address">Adresse</label>
                        <input id="address" name="address" value="{{ old('address', $settings['address']) }}" required>
                        @error('address')<span class="admin-settings-error">{{ $message }}</span>@enderror
                    </div>
                </div>
            </section>

            <section class="admin-settings-section">
                <h2>Contact et horaires</h2>
                <p>Les coordonnées servent aussi aux boutons d’appel et WhatsApp.</p>
                <div class="admin-settings-grid">
                    <div class="admin-settings-field">
                        <label for="phone">Téléphone</label>
                        <input id="phone" name="phone" value="{{ old('phone', $settings['phone']) }}" required>
                        @error('phone')<span class="admin-settings-error">{{ $message }}</span>@enderror
                    </div>
                    <div class="admin-settings-field">
                        <label for="email">E-mail</label>
                        <input id="email" type="email" name="email" value="{{ old('email', $settings['email']) }}" required>
                        @error('email')<span class="admin-settings-error">{{ $message }}</span>@enderror
                    </div>
                    <div class="admin-settings-field">
                        <label for="whatsapp_number">Numéro WhatsApp</label>
                        <input id="whatsapp_number" name="whatsapp_number" value="{{ old('whatsapp_number', $settings['whatsapp_number']) }}">
                        @error('whatsapp_number')<span class="admin-settings-error">{{ $message }}</span>@enderror
                    </div>
                    <div class="admin-settings-field">
                        <label for="instagram">Instagram</label>
                        <input id="instagram" name="instagram" value="{{ old('instagram', $settings['instagram']) }}">
                        @error('instagram')<span class="admin-settings-error">{{ $message }}</span>@enderror
                    </div>
                    <div class="admin-settings-field full">
                        <label for="hours">Horaires</label>
                        <input id="hours" name="hours" value="{{ old('hours', $settings['hours']) }}" required>
                        @error('hours')<span class="admin-settings-error">{{ $message }}</span>@enderror
                    </div>
                </div>
            </section>

            <section class="admin-settings-section">
                <h2>Préférences de l’espace admin</h2>
                <p>Configurez l’affichage et les rappels de votre tableau de bord.</p>
                <div class="admin-settings-grid">
                    <div class="admin-settings-field">
                        <label for="default_duration">Durée par défaut d’un rendez-vous</label>
                        <select id="default_duration" name="default_duration">
                            @foreach(['30' => '30 minutes', '45' => '45 minutes', '60' => '1 heure', '90' => '1 h 30'] as $value => $label)
                                <option value="{{ $value }}" @selected(old('default_duration', $settings['default_duration']) === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('default_duration')<span class="admin-settings-error">{{ $message }}</span>@enderror
                    </div>
                    <div class="admin-settings-field">
                        <label for="admin_locale">Langue de l’espace admin</label>
                        <select id="admin_locale" name="admin_locale">
                            @foreach(['fr' => 'Français', 'en' => 'English', 'ar' => 'العربية'] as $value => $label)
                                <option value="{{ $value }}" @selected(old('admin_locale', $settings['admin_locale']) === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('admin_locale')<span class="admin-settings-error">{{ $message }}</span>@enderror
                    </div>
                    <div class="admin-settings-check">
                        <input id="notifications_enabled" type="checkbox" name="notifications_enabled" value="1" @checked(old('notifications_enabled', $settings['notifications_enabled']))>
                        <label for="notifications_enabled">Activer les notifications de nouveaux rendez-vous</label>
                    </div>
                </div>
            </section>

            <div class="admin-settings-actions">
                <button class="admin-settings-submit" type="submit">Enregistrer les paramètres</button>
            </div>
        </form>

        <aside class="admin-settings-preview">
            <span class="admin-settings-preview-kicker">Aperçu</span>
            <h2>{{ old('name', $settings['name']) }}</h2>
            <p>{{ old('profession', $settings['profession']) }}</p>
            <div class="admin-settings-preview-line">@include('partials.icon', ['name' => 'pin', 'size' => 17]) <span>{{ old('address', $settings['address']) }}, {{ old('city', $settings['city']) }}</span></div>
            <div class="admin-settings-preview-line">@include('partials.icon', ['name' => 'phone', 'size' => 17]) <span>{{ old('phone', $settings['phone']) }}</span></div>
            <div class="admin-settings-preview-line">@include('partials.icon', ['name' => 'mail', 'size' => 17]) <span>{{ old('email', $settings['email']) }}</span></div>
            <div class="admin-settings-preview-line">@include('partials.icon', ['name' => 'clock', 'size' => 17]) <span>{{ old('hours', $settings['hours']) }}</span></div>
        </aside>
    </div>
</main>
@endsection
