<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\ClinicSetting;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AdminController extends Controller
{
    public function showLogin(): View
    {
        return view('admin.login', $this->sharedViewData(__('admin.login_title'), __('admin.login_description')));
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email', 'max:255'],
            'password' => ['required', 'string', 'max:255'],
        ]);

        $configuredEmail = strtolower(trim((string) config('clinic.admin_email')));
        $configuredPassword = (string) config('clinic.admin_password');
        $submittedEmail = strtolower(trim($credentials['email']));

        if ($configuredEmail !== '' && $configuredPassword !== ''
            && hash_equals($configuredEmail, $submittedEmail)
            && hash_equals($configuredPassword, $credentials['password'])) {
            $request->session()->regenerate();
            $request->session()->put('admin_authenticated', true);
            $request->session()->put('admin_email', $configuredEmail);

            return redirect()->intended(route('admin.dashboard'));
        }

        return back()->withErrors(['email' => __('admin.invalid_credentials')])->onlyInput('email');
    }

    public function settings(): View
    {
        $clinic = ClinicSetting::apply(config('clinic'));

        return view('admin.settings', $this->sharedViewData(
            'Paramètres — '.$clinic['name'],
            'Gérez les informations de votre cabinet et les préférences du tableau de bord.'
        ) + [
            'settings' => [
                'name' => $clinic['name'],
                'profession' => $clinic['profession'],
                'city' => $clinic['city'],
                'address' => $clinic['address'],
                'phone' => $clinic['phone'],
                'email' => $clinic['email'],
                'instagram' => $clinic['instagram'],
                'whatsapp_number' => $clinic['whatsapp_number'],
                'hours' => $clinic['hours'],
                'default_duration' => ClinicSetting::value('default_duration', '45'),
                'admin_locale' => ClinicSetting::value('admin_locale', app()->getLocale()),
                'notifications_enabled' => ClinicSetting::value('notifications_enabled', '1') === '1',
            ],
        ]);
    }

    public function updateSettings(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'profession' => ['required', 'string', 'max:120'],
            'city' => ['required', 'string', 'max:80'],
            'address' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:30'],
            'email' => ['required', 'email', 'max:255'],
            'instagram' => ['nullable', 'string', 'max:120'],
            'whatsapp_number' => ['nullable', 'string', 'max:30'],
            'hours' => ['required', 'string', 'max:120'],
            'default_duration' => ['required', Rule::in(['30', '45', '60', '90'])],
            'admin_locale' => ['required', Rule::in(['fr', 'en', 'ar'])],
        ]);

        $validated['notifications_enabled'] = $request->boolean('notifications_enabled') ? '1' : '0';

        foreach ([
            'name',
            'profession',
            'city',
            'address',
            'phone',
            'email',
            'instagram',
            'whatsapp_number',
            'hours',
            'default_duration',
            'admin_locale',
            'notifications_enabled',
        ] as $key) {
            ClinicSetting::updateOrCreate(
                ['key' => $key],
                ['value' => (string) ($validated[$key] ?? '')]
            );
        }

        $request->session()->put('locale', $validated['admin_locale']);

        return to_route('admin.settings')->with('success', 'Paramètres enregistrés avec succès.');
    }

    public function dashboard(Request $request): View
    {
        $today = now()->startOfDay();
        try {
            $selectedDate = $request->date ? Carbon::parse($request->date)->startOfDay() : $today->copy();
        } catch (\Throwable) {
            $selectedDate = $today->copy();
        }

        $appointments = Appointment::query()
            ->latest('preferred_date')
            ->latest('preferred_time')
            ->paginate(12)
            ->withQueryString();

        $allAppointments = Appointment::query()->get();
        $prices = [
            'consultation' => 250,
            'scaling' => 350,
            'care' => 300,
            'aesthetic' => 1800,
            'prosthetics' => 2200,
            'implants' => 4500,
        ];
        $revenue = static function ($items) use ($prices): int {
            return (int) $items->sum(fn (Appointment $appointment): int => $prices[$appointment->service] ?? 500);
        };

        $periods = [
            'day' => $allAppointments->filter(fn (Appointment $appointment): bool => $appointment->preferred_date->isSameDay($today)),
            'month' => $allAppointments->filter(fn (Appointment $appointment): bool => $appointment->preferred_date->isSameMonth($today)),
            'year' => $allAppointments->filter(fn (Appointment $appointment): bool => $appointment->preferred_date->isSameYear($today)),
        ];
        $periodStats = collect($periods)->map(fn ($items): array => [
            'appointments' => $items->count(),
            'confirmed' => $items->where('status', 'confirmed')->count(),
            'revenue' => $revenue($items),
        ])->all();

        $calendarDays = collect(range(1, $selectedDate->daysInMonth))->map(function (int $day) use ($selectedDate, $allAppointments): array {
            $date = $selectedDate->copy()->day($day);
            $items = $allAppointments->filter(fn (Appointment $appointment): bool => $appointment->preferred_date->isSameDay($date));

            return [
                'date' => $date->toDateString(),
                'day' => $day,
                'weekday' => $date->isoFormat('ddd'),
                'isToday' => $date->isToday(),
                'isSelected' => $date->isSameDay($selectedDate),
                'count' => $items->count(),
                'urgent' => $items->where('status', 'pending')->count(),
            ];
        })->all();
        $calendarOffset = $selectedDate->copy()->startOfMonth()->dayOfWeekIso - 1;

        $weekDays = collect(range(-2, 3))->map(function (int $offset) use ($selectedDate, $allAppointments): array {
            $date = $selectedDate->copy()->addDays($offset);
            $items = $allAppointments->filter(fn (Appointment $appointment): bool => $appointment->preferred_date->isSameDay($date));

            return [
                'date' => $date->toDateString(),
                'day' => $date->day,
                'weekday' => $date->isoFormat('ddd'),
                'count' => $items->count(),
                'isToday' => $date->isToday(),
                'isSelected' => $date->isSameDay($selectedDate),
            ];
        })->all();

        $selectedAppointments = $allAppointments
            ->filter(fn (Appointment $appointment): bool => $appointment->preferred_date->isSameDay($selectedDate))
            ->sortBy('preferred_time')
            ->values();

        $chart = collect(range(5, 0))->map(function (int $monthsAgo) use ($today, $allAppointments, $revenue): array {
            $month = $today->copy()->subMonths($monthsAgo)->startOfMonth();
            $items = $allAppointments->filter(fn (Appointment $appointment): bool => $appointment->preferred_date->isSameMonth($month));

            return [
                'label' => $month->isoFormat('MMM'),
                'appointments' => $items->count(),
                'revenue' => $revenue($items),
            ];
        })->values()->all();

        $serviceStats = collect($prices)->map(function (int $price, string $service) use ($allAppointments, $revenue): array {
            $items = $allAppointments->where('service', $service);

            return [
                'key' => $service,
                'label' => __('site.services.'.$service.'.name'),
                'count' => $items->count(),
                'revenue' => $revenue($items),
            ];
        })->filter(fn (array $service): bool => $service['count'] > 0)->sortByDesc('count')->values()->take(4)->all();

        return view('admin.dashboard-localized', $this->sharedViewData(__('admin.dashboard_title'), __('admin.dashboard_description')) + [
            'appointments' => $appointments,
            'selectedDate' => $selectedDate,
            'calendarDays' => $calendarDays,
            'calendarOffset' => $calendarOffset,
            'weekDays' => $weekDays,
            'selectedAppointments' => $selectedAppointments,
            'periodStats' => $periodStats,
            'chart' => $chart,
            'serviceStats' => $serviceStats,
            'prices' => $prices,
            'stats' => [
                'total' => Appointment::query()->count(),
                'pending' => Appointment::query()->where('status', 'pending')->count(),
                'confirmed' => Appointment::query()->where('status', 'confirmed')->count(),
                'cancelled' => Appointment::query()->where('status', 'cancelled')->count(),
            ],
        ]);
    }

    public function updateStatus(Request $request, Appointment $appointment): RedirectResponse
    {
        $validated = $request->validate(['status' => ['required', Rule::in(['pending', 'confirmed', 'cancelled'])]]);
        $appointment->update(['status' => $validated['status']]);

        return back()->with('success', __('admin.status_updated'));
    }

    public function destroy(Appointment $appointment): RedirectResponse
    {
        $appointment->delete();

        return back()->with('success', __('admin.deleted'));
    }

    public function exportAppointments(): StreamedResponse
    {
        return response()->streamDownload(function (): void {
            $handle = fopen('php://output', 'wb');
            fwrite($handle, "\xEF\xBB\xBF");
            fputcsv($handle, ['Patient', 'Téléphone', 'Email', 'Service', 'Date', 'Heure', 'Statut', 'Message'], ';');

            Appointment::query()
                ->latest('preferred_date')
                ->latest('preferred_time')
                ->cursor()
                ->each(function (Appointment $appointment) use ($handle): void {
                    fputcsv($handle, [
                        $appointment->full_name,
                        $appointment->phone,
                        $appointment->email,
                        __('site.services.'.$appointment->service.'.name'),
                        $appointment->preferred_date->format('d/m/Y'),
                        substr($appointment->preferred_time, 0, 5),
                        $appointment->status,
                        $appointment->message,
                    ], ';');
                });

            fclose($handle);
        }, 'rendez-vous-'.now()->format('Y-m-d').'.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    public function logout(Request $request): RedirectResponse
    {
        $request->session()->forget(['admin_authenticated', 'admin_email']);
        $request->session()->regenerateToken();

        return to_route('admin.login');
    }

    private function sharedViewData(string $title, string $description): array
    {
        $clinic = ClinicSetting::apply(config('clinic'));
        $phoneHref = preg_replace('/[^0-9+]/', '', $clinic['phone']);
        $whatsappBase = $clinic['whatsapp_number'] ? 'https://wa.me/'.preg_replace('/[^0-9]/', '', $clinic['whatsapp_number']) : 'https://wa.me/';

        return [
            'clinic' => $clinic,
            'locale' => app()->getLocale(),
            'rtl' => app()->getLocale() === 'ar',
            'phoneHref' => $phoneHref,
            'whatsappUrl' => $whatsappBase.'?text='.rawurlencode(__('site.whatsapp_message')),
            'seo' => ['title' => $title.' — '.$clinic['name'], 'description' => $description, 'canonical' => url()->current()],
        ];
    }
}
