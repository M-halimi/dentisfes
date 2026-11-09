@extends('admin.patients.pdf-layout')

@section('pdf-styles')
.document-heading{margin-bottom:5mm}.panel{padding:4mm;margin-bottom:4mm}.section-title{margin-top:4mm;margin-bottom:2mm}.summary{margin-bottom:4mm}.notice{margin-top:4mm;padding:3mm;font-size:7.5pt}
@endsection

@section('pdf-content')
<div class="document-heading"><div class="eyebrow">{{ $pdfClinic['name'] }} / {{ __('patients.pdf_patient_copy') }}</div><div class="document-title">{{ __('patients.title') }}</div><div class="muted">{{ __('patients.pdf_generated') }} {{ $generatedAt->format('d/m/Y · H:i') }}</div></div>
<div class="panel"><div class="person-name">{{ $patient->full_name }}</div><div class="ref">{{ $patient->reference }}</div><div class="contact">{{ __('patients.phone') }} : <span dir="ltr">{{ $patient->phone }}</span>@if($patient->email)<br>{{ __('patients.pdf_email') }} : <span dir="ltr">{{ $patient->email }}</span>@endif</div></div>
<table class="summary"><tr><td><div class="summary-label">{{ __('patients.total') }}</div><div class="summary-amount"><span dir="ltr">{{ \App\Money::format($total) }}</span></div></td><td><div class="summary-label">{{ __('patients.collected') }}</div><div class="summary-amount"><span dir="ltr">{{ \App\Money::format($paid) }}</span></div></td><td class="outstanding"><div class="summary-label">{{ __('patients.balance') }}</div><div class="summary-amount" style="color:#b96a17"><span dir="ltr">{{ \App\Money::format($total-$paid) }}</span></div></td></tr></table>
<div class="section-title">{{ __('patients.services') }}</div>
@if($services->isEmpty())<div class="empty">{{ __('patients.no_services') }}</div>@else
<table class="data-table"><thead><tr><th width="16%">{{ __('patients.date') }}</th><th width="36%">{{ __('patients.label') }}</th><th width="24%">{{ __('patients.price') }}</th><th width="24%">{{ __('patients.balance') }}</th></tr></thead><tbody>@foreach($services as $service)<tr class="{{ $loop->even ? 'stripe' : '' }}"><td>{{ $service->performed_on->format('d/m/Y') }}</td><td>{{ $service->label }}</td><td class="money"><span dir="ltr">{{ \App\Money::format($service->price_cents) }}</span></td><td class="money amber"><span dir="ltr">{{ \App\Money::format($service->price_cents-(int)$service->paid_cents) }}</span></td></tr>@endforeach</tbody></table>
@endif
<div class="section-title">{{ __('patients.payments') }}</div>
@if($payments->isEmpty())<div class="empty">{{ __('patients.no_payments') }}</div>@else
<table class="data-table"><thead><tr><th width="17%">{{ __('patients.date') }}</th><th width="37%">{{ __('patients.service') }} / {{ __('patients.receipt_number') }}</th><th width="23%">{{ __('patients.method') }}</th><th width="23%">{{ __('patients.amount') }}</th></tr></thead><tbody>@foreach($payments as $payment)<tr class="{{ $loop->even ? 'stripe' : '' }}"><td>{{ $payment->paid_on->format('d/m/Y') }}</td><td>{{ $payment->patientService->label }}<br><span class="muted">{{ $payment->receipt_number }}</span>@if($payment->cancelled_at)<br><span class="cancelled-text">{{ __('patients.cancelled') }}</span>@endif</td><td>{{ __('patients.methods.'.$payment->method) }}</td><td class="money {{ $payment->cancelled_at ? 'cancelled-text' : 'teal' }}"><span dir="ltr">{{ \App\Money::format($payment->amount_cents) }}</span></td></tr>@endforeach</tbody></table>
@endif
<div class="section-title">{{ __('patients.appointments') }}</div>
@if($appointments->isEmpty())<div class="empty">{{ __('patients.no_appointments') }}</div>@else
<table class="data-table"><thead><tr><th width="31%">{{ __('patients.date') }} / {{ __('patients.time') }}</th><th width="44%">{{ __('patients.service') }}</th><th width="25%">{{ __('patients.status') }}</th></tr></thead><tbody>@foreach($appointments as $appointment)<tr class="{{ $loop->even ? 'stripe' : '' }}"><td>{{ $appointment->preferred_date->format('d/m/Y') }} · {{ substr($appointment->preferred_time,0,5) }}</td><td>{{ __('site.services.'.$appointment->service.'.name') }}</td><td>{{ __('patients.statuses.'.$appointment->status) }}</td></tr>@endforeach</tbody></table>
@endif
<div class="notice">{{ __('patients.pdf_summary_notice') }}</div>
@endsection
