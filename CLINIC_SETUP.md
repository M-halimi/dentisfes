# Cabinet Dentaire Prisme — Laravel setup

## Run locally

```bash
composer install
npm install
php artisan migrate
npm run build
php artisan serve
```

The application is available at `http://127.0.0.1:8000`.

## Clinic configuration

Set these values in `.env` before production:

```dotenv
CLINIC_NAME="[Nom du cabinet]"
CLINIC_CITY="[Ville]"
CLINIC_ADDRESS="[Adresse du cabinet]"
CLINIC_PHONE="[Numéro de téléphone]"
CLINIC_EMAIL="[email@cabinet.ma]"
CLINIC_HOURS="Lun — Ven · 09:00 — 18:00"
WHATSAPP_NUMBER=""
CLINIC_LAT=""
CLINIC_LNG=""
CLINIC_MAP_EMBED_URL=""
```

The central configuration is in `config/clinic.php`. Service names and copy are in `lang/fr/site.php` and `lang/ar/site.php`.

## Backend behavior

- `POST /rendez-vous` validates and stores appointment requests in `appointments` with `pending` status.
- The appointment endpoint is limited to 5 requests per minute per client.
- Forms use Laravel CSRF protection and escaped Blade output.
- `/langue/fr` and `/langue/ar` switch the session locale and render the French or RTL Arabic interface.
- Google Maps is shown as a configurable embed when `CLINIC_MAP_EMBED_URL` is set; otherwise the page provides a safe placeholder and directions link.
