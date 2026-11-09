@extends('admin.patients.layout')

@section('patient-content')
<a class="patient-breadcrumb" href="{{ route('admin.patients.index') }}">{{ __('patients.patient_list') }} / {{ $patient->full_name }}</a>
<div class="patient-heading"><div><h1>{{ __('patients.title') }}</h1><p>{{ __('patients.subtitle') }}</p></div><div class="patient-actions"><a class="patient-button secondary" href="{{ route('admin.patients.pdf',$patient) }}">@include('partials.icon',['name'=>'download']){{ __('patients.export_pdf') }}</a><button class="patient-button" type="button" data-patient-dialog="new-appointment">@include('partials.icon',['name'=>'plus-circle']){{ __('patients.new_appointment') }}</button></div></div>
<section class="patient-summary" aria-label="{{ __('patients.title') }}">
    <div class="patient-identity"><div class="patient-avatar" aria-hidden="true">{{ mb_strtoupper(mb_substr($patient->full_name, 0, 1)) }}</div><div><h2>{{ $patient->full_name }}</h2><span class="patient-reference"><bdi>{{ $patient->reference }}</bdi></span><p><bdi>{{ $patient->phone }}</bdi></p>@if($patient->email)<p><bdi>{{ $patient->email }}</bdi></p>@endif<button class="patient-text-link" type="button" data-patient-dialog="edit-contact">{{ __('patients.edit_contact') }}</button></div></div>
    @foreach([['total','tooth',$total],['collected','currency',$paid],['balance','currency',$total-$paid]] as [$key,$icon,$value])<article class="patient-stat {{ $key }}">@include('partials.icon',['name'=>$icon,'size'=>28])<span>{{ __('patients.'.$key) }}</span><strong><bdi>{{ \App\Money::format($value) }}</bdi></strong></article>@endforeach
</section>
<nav class="patient-tabs" aria-label="{{ __('patients.title') }}">@foreach(['overview'=>'dashboard','appointments'=>'calendar','payments'=>'currency','documents'=>'download'] as $key=>$icon)<a href="{{ route('admin.patients.show', [$patient,'tab'=>$key]) }}" @if($tab===$key) aria-current="page" @endif>@include('partials.icon',['name'=>$icon]){{ __('patients.'.$key) }}</a>@endforeach</nav>
@if($tab === 'overview')
<div class="patient-grid">
    <div class="patient-stack">
        @include('admin.patients.appointments')
        @can('patient-notes')
        <section class="patient-panel"><div class="patient-panel-head"><h2>@include('partials.icon',['name'=>'message','size'=>24]){{ __('patients.notes') }}</h2></div><p class="patient-hint">{{ __('patients.restricted') }}</p>
            @forelse($notes as $note)<div class="patient-note">{{ $note->body }}<small>{{ $note->created_at->locale($locale)->isoFormat('D MMM YYYY · HH:mm') }} · {{ $note->author }}</small></div>@empty<div class="patient-empty">{{ __('patients.no_notes') }}</div>@endforelse
            <div class="patient-pagination">{{ $notes->links() }}</div>
            <details><summary class="patient-text-link">{{ __('patients.new_note') }}</summary><form class="patient-form" method="POST" action="{{ route('admin.patients.notes.store',$patient) }}">@csrf<label class="patient-field"><span>{{ __('patients.note_body') }}</span><textarea name="body" maxlength="5000" required>{{ old('body') }}</textarea></label><button class="patient-button" type="submit">{{ __('patients.save') }}</button></form></details>
        </section>
        @endcan
        @include('admin.patients.documents')
    </div>
    <div class="patient-stack">
        <section class="patient-panel"><div class="patient-panel-head"><h2>@include('partials.icon',['name'=>'currency','size'=>24]){{ __('patients.payments') }}</h2></div>
            @forelse($payments->take(3) as $payment)<div class="patient-list-item"><div class="patient-payment-line"><span>{{ $payment->paid_on->locale($locale)->isoFormat('D MMM YYYY') }}<br><small>{{ __('patients.methods.'.$payment->method) }}</small></span><strong><bdi>{{ \App\Money::format($payment->amount_cents) }}</bdi></strong></div>@if($payment->cancelled_at)<span class="patient-badge cancelled">{{ __('patients.cancelled') }}</span>@else<a class="patient-text-link" href="{{ route('admin.patients.payments.receipt',[$patient,$payment]) }}">{{ __('patients.receipt') }}</a>@endif</div>@empty<div class="patient-empty">{{ __('patients.no_payments') }}</div>@endforelse
            <div class="patient-stack" style="gap:10px;margin-top:15px"><button class="patient-button full" data-patient-dialog="new-payment" type="button" @disabled(!$services->contains(fn($service)=>$service->price_cents > (int)$service->paid_cents))>@include('partials.icon',['name'=>'plus-circle']){{ __('patients.new_payment') }}</button><button class="patient-button secondary full" type="button" data-patient-dialog="new-service">{{ __('patients.new_service') }}</button><a class="patient-text-link" href="{{ route('admin.patients.show',[$patient,'tab'=>'payments']) }}">{{ __('patients.payments') }} →</a></div>
        </section>
        <section class="patient-panel"><div class="patient-panel-head"><h2>@include('partials.icon',['name'=>'calendar','size'=>24]){{ __('patients.next_appointment') }}</h2></div>
            @if($nextAppointment)<div class="patient-next"><strong>{{ $nextAppointment->preferred_date->locale($locale)->isoFormat('D MMMM YYYY') }} · {{ substr($nextAppointment->preferred_time,0,5) }}</strong>{{ __('site.services.'.$nextAppointment->service.'.name') }}<p><span class="patient-badge {{ $nextAppointment->status }}">{{ __('patients.statuses.'.$nextAppointment->status) }}</span></p></div><button class="patient-button secondary full" type="button" data-patient-dialog="edit-appointment">{{ __('patients.edit_appointment') }}</button>@else<div class="patient-empty">{{ __('patients.no_next') }}</div>@endif
        </section>
    </div>
</div>
@elseif($tab === 'appointments')
    @include('admin.patients.appointments')
@elseif($tab === 'documents')
    @include('admin.patients.documents')
@else
<div class="patient-stack">
    <section class="patient-panel"><div class="patient-panel-head"><h2>{{ __('patients.services') }}</h2><button class="patient-button secondary" type="button" data-patient-dialog="new-service">{{ __('patients.new_service') }}</button></div>
        @if($services->isEmpty())<div class="patient-empty">{{ __('patients.no_services') }}</div>@else<div class="patient-table-wrap"><table class="patient-table"><thead><tr><th>{{ __('patients.label') }}</th><th>{{ __('patients.date') }}</th><th>{{ __('patients.price') }}</th><th>{{ __('patients.collected') }}</th><th>{{ __('patients.balance') }}</th></tr></thead><tbody>@foreach($services as $service)<tr><td>{{ $service->label }}</td><td>{{ $service->performed_on->format('d/m/Y') }}</td><td><bdi>{{ \App\Money::format($service->price_cents) }}</bdi></td><td><bdi>{{ \App\Money::format((int)$service->paid_cents) }}</bdi></td><td><bdi>{{ \App\Money::format($service->price_cents-(int)$service->paid_cents) }}</bdi></td></tr>@endforeach</tbody></table></div>@endif
    </section>
    <section class="patient-panel"><div class="patient-panel-head"><h2>{{ __('patients.payments') }}</h2><button class="patient-button" type="button" data-patient-dialog="new-payment" @disabled(!$services->contains(fn($service)=>$service->price_cents > (int)$service->paid_cents))>{{ __('patients.new_payment') }}</button></div><p class="patient-hint">{{ __('patients.payments_hint') }}</p>
        @forelse($payments as $payment)<article class="patient-list-item"><div class="patient-payment-line"><div><strong>{{ $payment->patientService->label }}</strong><p>{{ $payment->paid_on->locale($locale)->isoFormat('D MMM YYYY') }} · {{ __('patients.methods.'.$payment->method) }}</p><small><bdi>{{ $payment->receipt_number }}</bdi>@if($payment->reference) · {{ $payment->reference }}@endif</small></div><div><strong><bdi>{{ \App\Money::format($payment->amount_cents) }}</bdi></strong><br><span class="patient-badge {{ $payment->cancelled_at ? 'cancelled' : '' }}">{{ __('patients.'.($payment->cancelled_at ? 'cancelled' : 'recorded')) }}</span></div></div>
            <div class="patient-payment-actions"><a class="patient-text-link" href="{{ route('admin.patients.payments.receipt',[$patient,$payment]) }}">@include('partials.icon',['name'=>'download','size'=>15]) {{ __('patients.receipt') }}</a></div>
            @if(!$payment->cancelled_at)<details class="patient-correction"><summary>{{ __('patients.cancel_payment') }}</summary><p class="patient-hint">{{ __('patients.cancel_help') }}</p><form class="patient-form" method="POST" action="{{ route('admin.patients.payments.cancel',[$patient,$payment]) }}" data-confirm-payment="{{ __('patients.cancel_confirm') }}">@csrf<label class="patient-field"><span>{{ __('patients.reason') }}</span><textarea name="reason" minlength="3" maxlength="2000" required></textarea></label><button class="patient-button danger" type="submit">{{ __('patients.cancel_payment') }}</button></form></details>@endif
            <details class="patient-correction"><summary>{{ __('patients.audit') }}</summary>@foreach($payment->audits as $audit)<p class="patient-hint">{{ $audit->created_at->format('d/m/Y H:i') }} · {{ __('patients.'.($audit->action === 'cancelled' ? 'cancelled' : 'recorded')) }} · {{ $audit->actor }}@if($audit->reason)<br>{{ $audit->reason }}@endif</p>@endforeach</details>
        </article>@empty<div class="patient-empty">{{ __('patients.no_payments') }}</div>@endforelse
        <div class="patient-pagination">{{ $payments->links() }}</div>
    </section>
</div>
@endif
@include('admin.patients.dialogs')
@endsection
