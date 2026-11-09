<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\ClinicSetting;
use App\Models\Patient;
use App\Money;
use App\PatientPdf;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class PatientController extends Controller
{
    public function index(Request $request): View
    {
        $validated = $request->validate(['q' => ['nullable', 'string', 'max:120']]);
        $search = $validated['q'] ?? '';
        $patients = Patient::query()->when($search !== '', function ($query) use ($search): void {
            $query->where(function ($query) use ($search): void {
                $query->where('full_name', 'like', '%'.$search.'%')->orWhere('phone', 'like', '%'.$search.'%')->orWhere('reference', 'like', '%'.$search.'%');
            });
        })->withCount('appointments')->latest('id')->paginate(15)->withQueryString();

        return view('admin.patients.index', compact('patients', 'search') + $this->viewData());
    }

    public function store(Request $request): RedirectResponse
    {
        $patient = Patient::fromContact($this->contact($request));

        return to_route('admin.patients.show', $patient)->with('success', __('patients.saved'));
    }

    public function show(Request $request, Patient $patient): View
    {
        $tab = $request->validate(['tab' => ['nullable', Rule::in(['overview', 'appointments', 'payments', 'documents'])]])['tab'] ?? 'overview';
        $services = $patient->services()->withSum(['payments as paid_cents' => fn ($query) => $query->whereNull('cancelled_at')], 'amount_cents')->latest('performed_on')->latest('id')->get();
        $total = (int) $services->sum('price_cents');
        $paid = (int) $services->sum('paid_cents');
        $appointments = $patient->appointments()->latest('preferred_date')->latest('preferred_time')->latest('id')->paginate(10, ['*'], 'appointments_page')->withQueryString();
        $payments = $patient->payments()->with(['patientService', 'audits'])->latest('paid_on')->latest('id')->paginate(10, ['payments.*'], 'payments_page')->withQueryString();
        $documents = $patient->documents()->latest('id')->paginate(10, ['*'], 'documents_page')->withQueryString();
        $notes = Gate::allows('patient-notes') ? $patient->notes()->latest('id')->paginate(10, ['*'], 'notes_page')->withQueryString() : collect();
        $nextAppointment = $patient->appointments()->whereIn('status', ['pending', 'confirmed'])
            ->where(function ($query): void {
                $query->whereDate('preferred_date', '>', today()->toDateString())->orWhere(function ($query): void {
                    $query->whereDate('preferred_date', today()->toDateString())->where('preferred_time', '>=', now()->format('H:i:s'));
                });
            })->oldest('preferred_date')->oldest('preferred_time')->first();

        return view('admin.patients.show', compact('patient', 'tab', 'services', 'total', 'paid', 'appointments', 'payments', 'documents', 'notes', 'nextAppointment') + $this->viewData());
    }

    public function update(Request $request, Patient $patient): RedirectResponse
    {
        $data = $this->contact($request);
        if (Patient::query()->where('identity_key', Patient::identityKey($data['full_name'], $data['phone']))->whereKeyNot($patient->id)->exists()) {
            throw ValidationException::withMessages(['phone' => __('patients.duplicate')]);
        }
        $patient->update($data);

        return back()->with('success', __('patients.saved'));
    }

    public function export(Patient $patient, PatientPdf $pdf): Response
    {
        $services = $patient->services()->withSum(['payments as paid_cents' => fn ($query) => $query->whereNull('cancelled_at')], 'amount_cents')->latest('performed_on')->latest('id')->get();
        $total = (int) $services->sum('price_cents');
        $paid = (int) $services->sum('paid_cents');
        $payments = $patient->payments()->with('patientService')->latest('paid_on')->latest('id')->get();
        $appointments = $patient->appointments()->latest('preferred_date')->latest('preferred_time')->latest('id')->get();
        $generatedAt = now();

        return $pdf->download('admin.patients.record-pdf', compact('patient', 'services', 'total', 'paid', 'payments', 'appointments', 'generatedAt')
            + $pdf->branding(ClinicSetting::apply(config('clinic'))), __('patients.title').' '.$patient->reference, 'Dossier-'.$patient->reference.'.pdf');
    }

    public function storeAppointment(Request $request, Patient $patient): RedirectResponse
    {
        $data = $this->appointmentData($request);
        $patient->appointments()->create($data + $patient->only(['full_name', 'phone', 'email']) + ['status' => 'confirmed']);

        return to_route('admin.patients.show', [$patient, 'tab' => 'appointments'])->with('success', __('patients.saved'));
    }

    public function updateAppointment(Request $request, Patient $patient, Appointment $appointment): RedirectResponse
    {
        abort_unless($appointment->patient_id === $patient->id, 404);
        $appointment->update($this->appointmentData($request));

        return back()->with('success', __('patients.saved'));
    }

    public function storeNote(Request $request, Patient $patient): RedirectResponse
    {
        $data = $request->validate(['body' => ['required', 'string', 'max:5000']], ['*' => __('patients.invalid_data')]);
        $patient->notes()->create($data + ['author' => session('admin_email')]);

        return back()->with('success', __('patients.saved'));
    }

    public function storeService(Request $request, Patient $patient): RedirectResponse
    {
        $data = $request->validate([
            'label' => ['required', 'string', 'max:160'],
            'performed_on' => ['required', 'date_format:Y-m-d'],
            'price' => ['required', 'string', 'regex:/^\d{1,8}(?:[.,]\d{1,2})?$/D'],
        ], ['*' => __('patients.invalid_data')]);
        $price = Money::cents($data['price']);
        if ($price < 1) {
            throw ValidationException::withMessages(['price' => __('patients.invalid_amount')]);
        }
        $patient->services()->create(['label' => $data['label'], 'performed_on' => $data['performed_on'], 'price_cents' => $price]);

        return to_route('admin.patients.show', [$patient, 'tab' => 'payments'])->with('success', __('patients.saved'));
    }

    private function contact(Request $request): array
    {
        return $request->validate([
            'full_name' => ['required', 'string', 'max:120'],
            'phone' => ['required', 'string', 'max:30', 'regex:/^[0-9 +().-]{8,30}$/'],
            'email' => ['nullable', 'email', 'max:255'],
        ], ['*' => __('patients.invalid_data')]);
    }

    private function appointmentData(Request $request): array
    {
        return $request->validate([
            'service' => ['required', Rule::in(collect(config('clinic.services'))->pluck('key')->all())],
            'preferred_date' => ['required', 'date_format:Y-m-d', 'after_or_equal:today'],
            'preferred_time' => ['required', 'date_format:H:i'],
            'message' => ['nullable', 'string', 'max:2000'],
        ], ['*' => __('patients.invalid_data')]);
    }

    private function viewData(): array
    {
        return ['locale' => app()->getLocale(), 'clinic' => ClinicSetting::apply(config('clinic')),
            'seo' => ['title' => __('patients.title'), 'description' => __('patients.subtitle')]];
    }
}
