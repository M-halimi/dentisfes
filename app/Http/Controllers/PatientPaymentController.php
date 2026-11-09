<?php

namespace App\Http\Controllers;

use App\Models\ClinicSetting;
use App\Models\Patient;
use App\Models\Payment;
use App\Money;
use App\PatientPdf;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class PatientPaymentController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'from' => ['nullable', 'date_format:Y-m-d'],
            'to' => ['nullable', 'date_format:Y-m-d', Rule::when($request->filled('from'), ['after_or_equal:from'])],
        ], ['*' => __('patients.invalid_data')]);
        $query = Payment::query()->when($filters['from'] ?? null, fn ($query, $date) => $query->whereDate('paid_on', '>=', $date))
            ->when($filters['to'] ?? null, fn ($query, $date) => $query->whereDate('paid_on', '<=', $date));
        $collected = (int) (clone $query)->whereNull('cancelled_at')->sum('amount_cents');
        $payments = $query->with('patientService.patient')->latest('paid_on')->latest('id')->paginate(20)->withQueryString();

        return view('admin.patients.payments', compact('payments', 'collected', 'filters') + ['locale' => app()->getLocale(), 'clinic' => ClinicSetting::apply(config('clinic')),
            'seo' => ['title' => __('patients.payments'), 'description' => __('patients.subtitle')]]);
    }

    public function store(Request $request, Patient $patient): RedirectResponse
    {
        $data = $request->validate([
            'patient_service_id' => ['required', 'integer'],
            'amount' => ['required', 'string', 'regex:/^\d{1,8}(?:[.,]\d{1,2})?$/D'],
            'paid_on' => ['required', 'date_format:Y-m-d', 'before_or_equal:today'],
            'method' => ['required', Rule::in(['cash', 'card', 'transfer', 'cheque'])],
            'reference' => ['nullable', 'string', 'max:120'],
            'submission_key' => ['required', 'uuid'],
        ], ['*' => __('patients.invalid_data')]);
        $amount = Money::cents($data['amount']);
        if ($amount < 1) {
            throw ValidationException::withMessages(['amount' => __('patients.invalid_amount')]);
        }
        DB::transaction(function () use ($patient, $data, $amount): void {
            $service = $patient->services()->whereKey($data['patient_service_id'])->lockForUpdate()->firstOrFail();
            $existing = Payment::query()->where('submission_key', $data['submission_key'])->first();
            if ($existing) {
                if ($existing->patient_service_id === $service->id && $existing->amount_cents === $amount
                    && $existing->paid_on->toDateString() === $data['paid_on'] && $existing->method === $data['method']
                    && $existing->reference === ($data['reference'] ?? null)) {
                    return;
                }
                throw ValidationException::withMessages(['amount' => __('patients.duplicate_payment')]);
            }
            $paid = (int) $service->payments()->whereNull('cancelled_at')->sum('amount_cents');
            if ($amount > $service->price_cents - $paid) {
                throw ValidationException::withMessages(['amount' => __('patients.overpayment')]);
            }
            $clinic = Arr::only(ClinicSetting::apply(config('clinic')), ['name', 'practitioner_name', 'profession', 'address', 'city', 'phone', 'email']);
            $clinic['website'] = config('seo.url');
            $payment = $service->payments()->create(Arr::only($data, ['paid_on', 'method', 'reference', 'submission_key']) + [
                'amount_cents' => $amount, 'receipt_number' => 'REC-'.Str::ulid(), 'recorded_by' => session('admin_email'),
                'receipt_snapshot' => ['clinic' => $clinic, 'patient' => $patient->only(['reference', 'full_name', 'phone', 'email']), 'service' => $service->only(['label', 'price_cents'])],
            ]);
            $payment->update(['receipt_number' => 'REC-'.now()->format('Y').'-'.str_pad((string) $payment->id, 8, '0', STR_PAD_LEFT)]);
            $payment->audits()->create(['action' => 'recorded', 'actor' => session('admin_email'), 'snapshot' => $payment->only(['amount_cents', 'paid_on', 'method', 'reference', 'receipt_number'])]);
        }, 3);

        return to_route('admin.patients.show', [$patient, 'tab' => 'payments'])->with('success', __('patients.payment_saved'));
    }

    public function cancel(Request $request, Patient $patient, Payment $payment): RedirectResponse
    {
        abort_unless($patient->payments()->whereKey($payment->id)->exists(), 404);
        $data = $request->validate(['reason' => ['required', 'string', 'min:3', 'max:2000']], ['*' => __('patients.reason_required')]);
        DB::transaction(function () use ($patient, $payment, $data): void {
            $patient->services()->whereKey($payment->patient_service_id)->lockForUpdate()->firstOrFail();
            $locked = Payment::query()->whereKey($payment->id)->lockForUpdate()->firstOrFail();
            if ($locked->cancelled_at !== null) {
                throw ValidationException::withMessages(['reason' => __('patients.already_cancelled')]);
            }
            $locked->audits()->create(['action' => 'cancelled', 'actor' => session('admin_email'), 'reason' => $data['reason'], 'snapshot' => $locked->only(['amount_cents', 'paid_on', 'method', 'reference', 'receipt_number'])]);
            $locked->update(['cancelled_at' => now()]);
        }, 3);

        return back()->with('success', __('patients.payment_cancelled'));
    }

    public function receipt(Patient $patient, Payment $payment, PatientPdf $pdf): Response
    {
        abort_unless($patient->payments()->whereKey($payment->id)->exists(), 404);

        return $pdf->download('admin.patients.receipt', compact('payment') + $pdf->branding($payment->receipt_snapshot['clinic']), $payment->receipt_number, $payment->receipt_number.'.pdf');
    }
}
