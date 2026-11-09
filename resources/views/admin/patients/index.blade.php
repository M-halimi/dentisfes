@extends('admin.patients.layout')

@section('patient-content')
<div class="patient-heading"><div><h1>{{ __('patients.patient_list') }}</h1><p>{{ __('patients.subtitle') }}</p></div><button class="patient-button" type="button" data-patient-dialog="new-patient">@include('partials.icon',['name'=>'plus-circle']){{ __('patients.new_patient') }}</button></div>
<section class="patient-panel">
    <form class="patient-filter" method="GET" action="{{ route('admin.patients.index') }}"><label class="patient-field"><span>{{ __('patients.search') }}</span><input name="q" value="{{ $search }}" maxlength="120" type="search"></label><button class="patient-button" type="submit">{{ __('patients.search_button') }}</button></form>
    @if($patients->isEmpty())<div class="patient-empty">{{ __('patients.no_patients') }}</div>@else
    <div class="patient-table-wrap"><table class="patient-table"><thead><tr><th>{{ __('patients.full_name') }}</th><th>{{ __('patients.reference') }}</th><th>{{ __('patients.phone') }}</th><th>{{ __('patients.appointments') }}</th><th>{{ __('patients.actions') }}</th></tr></thead><tbody>
    @foreach($patients as $patient)<tr><td><strong>{{ $patient->full_name }}</strong><small>{{ $patient->email }}</small></td><td><bdi>{{ $patient->reference }}</bdi></td><td><bdi>{{ $patient->phone }}</bdi></td><td>{{ $patient->appointments_count }}</td><td><a class="patient-text-link" href="{{ route('admin.patients.show', $patient) }}">{{ __('patients.open') }} →</a></td></tr>@endforeach
    </tbody></table></div><div class="patient-pagination">{{ $patients->links() }}</div>@endif
</section>
<dialog class="patient-dialog" id="new-patient" aria-labelledby="new-patient-title"><div class="patient-dialog-head"><h2 id="new-patient-title">{{ __('patients.new_patient') }}</h2><button type="button" class="patient-dialog-close" data-close-dialog aria-label="{{ __('patients.close') }}">×</button></div><form class="patient-form" method="POST" action="{{ route('admin.patients.store') }}">@csrf<input type="hidden" name="_form" value="new-patient">@include('admin.patients.contact-fields', ['patient'=>null])<button class="patient-button" type="submit">{{ __('patients.save') }}</button></form></dialog>
@endsection
