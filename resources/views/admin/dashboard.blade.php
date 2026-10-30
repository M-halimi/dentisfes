@extends('layouts.app')

@section('content')
<style>
    .admin-toolbar { display:flex; justify-content:space-between; align-items:center; gap:1rem; margin-bottom:2rem; }
    .admin-toolbar p { margin:0; color:var(--ink-soft); font-size:.78rem; }
    .admin-stat-grid { display:grid; grid-template-columns:repeat(4,1fr); gap:1rem; margin-bottom:1.5rem; }
    .admin-stat { padding:1.3rem 1.4rem; background:var(--surface); border:1px solid var(--line); border-radius:1rem; }
    .admin-stat strong { display:block; font-family:"Playfair Display",Georgia,serif; font-size:2rem; line-height:1; }
    .admin-stat span { display:block; margin-top:.55rem; color:var(--ink-soft); font-size:.7rem; font-weight:800; }
    .admin-panel { overflow:hidden; background:var(--surface); border:1px solid var(--line); border-radius:1.2rem; box-shadow:var(--shadow); }
    .admin-panel-head { display:flex; justify-content:space-between; align-items:center; gap:1rem; padding:1.3rem 1.4rem; border-bottom:1px solid var(--line); }
    .admin-panel-head h2 { margin:0; font-family:"Playfair Display",Georgia,serif; font-size:1.45rem; }
    .admin-table-wrap { overflow-x:auto; }
    .admin-table { width:100%; min-width:760px; border-collapse:collapse; }
    .admin-table th { padding:.85rem 1rem; color:var(--ink-soft); background:#f7faf9; font-size:.65rem; letter-spacing:.05em; text-align:left; text-transform:uppercase; }
    .admin-table td { padding:1rem; border-top:1px solid var(--line); vertical-align:top; font-size:.75rem; }
    .admin-table td strong { display:block; font-size:.8rem; }
    .admin-table td small { display:block; margin-top:.2rem; color:var(--ink-soft); }
    .admin-actions { display:flex; align-items:center; gap:.45rem; }
    .admin-actions select { min-height:2.45rem; padding:.4rem .55rem; color:var(--ink); background:#f8fbfa; border:1px solid #cfddda; border-radius:.55rem; font-size:.7rem; }
    .admin-actions .button { min-height:2.45rem; padding:.4rem .75rem; font-size:.65rem; }
    .admin-delete { padding:.4rem .2rem; color:#a0443c; background:transparent; border:0; cursor:pointer; font-size:.68rem; font-weight:800; }
    .admin-delete:hover { color:#762a24; }
    .status-badge { display:inline-flex; padding:.35rem .55rem; border-radius:999px; font-size:.62rem; font-weight:800; }
    .status-badge.pending { color:#876b45; background:var(--sand-soft); }
    .status-badge.confirmed { color:#166a56; background:#e1f5ec; }
    .status-badge.cancelled { color:#8e3b35; background:#fff0ed; }
    .admin-empty { padding:3rem 1rem; color:var(--ink-soft); text-align:center; }
    .admin-pagination { display:flex; justify-content:center; padding:1rem; border-top:1px solid var(--line); }
    .admin-pagination nav { display:flex; gap:.35rem; }
    .admin-pagination a, .admin-pagination span { display:inline-flex; align-items:center; justify-content:center; min-width:2rem; min-height:2rem; padding:0 .55rem; color:var(--ink-soft); border:1px solid var(--line); border-radius:.45rem; font-size:.7rem; }
    .admin-pagination span[aria-current="page"] { color:white; background:var(--teal); border-color:var(--teal); }
    html[dir="rtl"] .admin-table th { text-align:right; }
    @media (max-width:720px) { .admin-stat-grid { grid-template-columns:repeat(2,1fr); } .admin-toolbar { display:block; } .admin-toolbar .button { margin-top:1rem; } }
</style>
<main id="main">
    @include('partials.page-hero', ['eyebrow' => $rtl ? 'الإدارة' : 'Admin', 'title' => __('admin.dashboard_title'), 'copy' => __('admin.dashboard_description')])
    <section class="section"><div class="container">
        <div class="admin-toolbar"><p>{{ session('admin_email') }}</p><div style="display:flex;gap:.6rem"><a class="button secondary small" href="{{ route('home') }}">{{ __('admin.back_to_site') }}</a><form method="POST" action="{{ route('admin.logout') }}">@csrf<button class="button small" type="submit">{{ __('admin.logout') }}</button></form></div></div>
        @if(session('success'))<div class="form-message visible" role="status">{{ session('success') }}</div>@endif
        <div class="admin-stat-grid"><div class="admin-stat"><strong>{{ $stats['total'] }}</strong><span>{{ __('admin.total') }}</span></div><div class="admin-stat"><strong>{{ $stats['pending'] }}</strong><span>{{ __('admin.pending') }}</span></div><div class="admin-stat"><strong>{{ $stats['confirmed'] }}</strong><span>{{ __('admin.confirmed') }}</span></div><div class="admin-stat"><strong>{{ $stats['cancelled'] }}</strong><span>{{ __('admin.cancelled') }}</span></div></div>
        <div class="admin-panel"><div class="admin-panel-head"><h2>{{ __('admin.dashboard_title') }}</h2><span class="required-note">{{ $appointments->total() }} {{ $rtl ? 'طلب' : 'demande(s)' }}</span></div>@if($appointments->isEmpty())<div class="admin-empty">{{ __('admin.empty') }}</div>@else<div class="admin-table-wrap"><table class="admin-table"><thead><tr><th>{{ __('admin.patient') }}</th><th>{{ __('admin.requested_service') }}</th><th>{{ __('admin.date_time') }}</th><th>{{ __('admin.status') }}</th><th>{{ __('admin.actions') }}</th></tr></thead><tbody>@foreach($appointments as $appointment)<tr><td><strong>{{ $appointment->full_name }}</strong><small>{{ $appointment->phone }}</small>@if($appointment->email)<small>{{ $appointment->email }}</small>@endif @if($appointment->message)<small>{{ __('admin.message') }}: {{ $appointment->message }}</small>@endif</td><td>{{ __('site.services.'.$appointment->service.'.name') }}</td><td>{{ $appointment->preferred_date->format('d/m/Y') }}<small>{{ substr($appointment->preferred_time, 0, 5) }}</small></td><td><span class="status-badge {{ $appointment->status }}">{{ __('admin.statuses.'.$appointment->status) }}</span></td><td><div class="admin-actions"><form method="POST" action="{{ route('admin.appointments.status', $appointment) }}">@csrf @method('PATCH')<select name="status" aria-label="{{ __('admin.status') }}"><option value="pending" @selected($appointment->status === 'pending')>{{ __('admin.statuses.pending') }}</option><option value="confirmed" @selected($appointment->status === 'confirmed')>{{ __('admin.statuses.confirmed') }}</option><option value="cancelled" @selected($appointment->status === 'cancelled')>{{ __('admin.statuses.cancelled') }}</option></select><button class="button small" type="submit">{{ __('admin.update') }}</button></form><form method="POST" action="{{ route('admin.appointments.destroy', $appointment) }}" onsubmit="return confirm('{{ __('admin.confirm_delete') }}')">@csrf @method('DELETE')<button class="admin-delete" type="submit">{{ __('admin.delete') }}</button></form></div></td></tr>@endforeach</tbody></table></div><div class="admin-pagination">{{ $appointments->links() }}</div>@endif</div>
    </div></section>
</main>
@endsection
