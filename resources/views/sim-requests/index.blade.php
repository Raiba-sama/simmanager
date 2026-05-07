@extends('layouts.bootstrap')

@section('title', 'Demandes de SIM')
@section('page-title', 'Demandes de SIM')

@section('page-actions')
<a href="{{ route('sim-requests.create') }}" class="btn-create">
    <i class="bi bi-plus-lg"></i> Nouvelle demande
</a>
@endsection

@push('styles')
<style>
/* ── Create button ───────────────────────────────────── */
.btn-create {
    display: inline-flex; align-items: center; gap: 7px;
    padding: 9px 18px; border-radius: 10px;
    background: var(--primary); color: white;
    font-size: 13.5px; font-weight: 600; text-decoration: none;
    border: none; cursor: pointer;
    transition: background 0.18s, box-shadow 0.18s;
    box-shadow: 0 2px 8px rgba(0,87,74,0.20);
}
.btn-create:hover { background: #003d34; color: white; box-shadow: 0 4px 14px rgba(0,87,74,0.28); }

/* ── Filter panel ────────────────────────────────────── */
.filter-panel {
    background: white; border: 1px solid #e2e8f0;
    border-radius: 16px; padding: 20px 22px;
    margin-bottom: 14px; box-shadow: 0 1px 3px rgba(0,0,0,0.05);
}
.filter-panel-title {
    font-size: 11.5px; font-weight: 700; color: #64748b;
    text-transform: uppercase; letter-spacing: 0.6px;
    margin-bottom: 14px; display: flex; align-items: center; gap: 6px;
}
.filter-input {
    height: 38px; font-size: 13px; border: 1.5px solid #e2e8f0;
    border-radius: 9px; padding: 0 12px;
    font-family: 'Poppins', sans-serif; color: #1e293b;
    transition: border-color 0.18s, box-shadow 0.18s;
    width: 100%; background: #f8fafc; appearance: none;
}
.filter-input:focus {
    outline: none; border-color: #00574A;
    box-shadow: 0 0 0 3px rgba(0,87,74,0.10);
    background: white;
}
select.filter-input { background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%2394a3b8' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='m2 5 6 6 6-6'/%3e%3c/svg%3e"); background-repeat: no-repeat; background-position: right 10px center; background-size: 14px; padding-right: 32px; }
.fi-wrap { position: relative; }
.fi-wrap .fi-icon { position: absolute; left: 10px; top: 50%; transform: translateY(-50%); color: #94a3b8; font-size: 13px; pointer-events: none; }
.fi-wrap .filter-input { padding-left: 30px; }
.fi-switch-label { font-size: 11.5px; color: #64748b; font-weight: 500; display: block; margin-bottom: 5px; }

.btn-filter {
    height: 38px; padding: 0 16px; border-radius: 9px; font-size: 13px;
    font-weight: 600; display: inline-flex; align-items: center; gap: 6px;
    cursor: pointer; border: 1.5px solid transparent;
    transition: all 0.18s; font-family: 'Poppins', sans-serif; white-space: nowrap;
    text-decoration: none;
}
.btn-filter-primary  { background: #00574A; color: white; border-color: #00574A; }
.btn-filter-primary:hover  { background: #003d34; border-color: #003d34; color: white; }
.btn-filter-reset    { background: #f1f5f9; color: #475569; border-color: #e2e8f0; }
.btn-filter-reset:hover    { background: #e2e8f0; color: #1e293b; }
.btn-filter-excel    { background: #f0fdf4; color: #16a34a; border-color: #bbf7d0; }
.btn-filter-excel:hover    { background: #dcfce7; color: #15803d; }
.btn-filter-pdf      { background: #fff1f2; color: #dc2626; border-color: #fecaca; }
.btn-filter-pdf:hover      { background: #ffe4e6; color: #b91c1c; }
.btn-filter-danger   { background: #ef4444; color: white; border-color: #ef4444; }
.btn-filter-danger:hover   { background: #dc2626; border-color: #dc2626; }

/* ── Toolbar ─────────────────────────────────────────── */
.list-toolbar {
    display: flex; align-items: center; justify-content: space-between;
    margin-bottom: 10px; flex-wrap: wrap; gap: 8px;
}
.list-count { font-size: 12.5px; color: #64748b; font-weight: 500; }
.btn-toggle-cols {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 6px 13px; border-radius: 8px; font-size: 12.5px;
    font-weight: 500; background: #f8fafc; color: #475569;
    border: 1.5px solid #e2e8f0; cursor: pointer;
    transition: all 0.16s; font-family: 'Poppins', sans-serif;
}
.btn-toggle-cols:hover { background: #f1f5f9; color: #1e293b; }

/* ── Bulk bar ─────────────────────────────────────────── */
.bulk-bar {
    display: none; align-items: center; justify-content: space-between;
    padding: 12px 18px; background: #eef9f7; border-radius: 12px;
    border: 1.5px solid #6ee7b7; margin-bottom: 12px;
    flex-wrap: wrap; gap: 10px;
}
.bulk-bar.visible { display: flex; }
.bulk-bar-info { font-size: 13px; color: #1e293b; }
.bulk-bar-info strong { color: #00574A; font-size: 15px; font-weight: 700; }
.bulk-bar-actions { display: flex; gap: 6px; flex-wrap: wrap; align-items: center; }
.btn-bulk {
    display: inline-flex; align-items: center; gap: 5px;
    padding: 6px 12px; border-radius: 7px; font-size: 12px; font-weight: 600;
    cursor: pointer; border: none; transition: all 0.16s;
    font-family: 'Poppins', sans-serif;
}
.bb-approve  { background: #10b981; color: white; }
.bb-approve:hover  { background: #059669; }
.bb-reject   { background: #ef4444; color: white; }
.bb-reject:hover   { background: #dc2626; }
.bb-pending  { background: #06b6d4; color: white; }
.bb-pending:hover  { background: #0891b2; }
.bb-accepted { background: #10b981; color: white; }
.bb-accepted:hover { background: #059669; }
.bb-refused  { background: #ef4444; color: white; }
.bb-refused:hover  { background: #dc2626; }
.bb-export   { background: #f8fafc; color: #475569; border: 1.5px solid #e2e8f0; }
.bb-export:hover   { background: #f1f5f9; color: #1e293b; }
.bb-delete   { background: #fff1f2; color: #dc2626; border: 1.5px solid #fecada; }
.bb-delete:hover   { background: #ffe4e6; }
.bb-cancel   { background: white; color: #64748b; border: 1.5px solid #e2e8f0; }
.bb-cancel:hover   { background: #f1f5f9; color: #475569; }

/* ── Table card ──────────────────────────────────────── */
.list-card {
    background: white; border: 1px solid #e2e8f0;
    border-radius: 16px; overflow: hidden;
    box-shadow: 0 1px 3px rgba(0,0,0,0.05);
}
.dtable { width: 100%; border-collapse: collapse; }
.dtable thead tr { background: #f8fafc; border-bottom: 2px solid #e2e8f0; }
.dtable thead th {
    padding: 13px 14px; font-size: 11.5px; font-weight: 700;
    color: #64748b; text-transform: uppercase; letter-spacing: 0.55px;
    white-space: nowrap;
}
.dtable tbody tr { border-bottom: 1px solid #f1f5f9; transition: background 0.14s; }
.dtable tbody tr:last-child { border-bottom: none; }
.dtable tbody tr:hover { background: #f8fafc !important; }
.dtable td { padding: 13px 14px; font-size: 13px; color: #374151; vertical-align: middle; }

/* Urgency row variants */
.row-urgent { border-left: 3px solid #ef4444 !important; background: #fef2f2 !important; }
.row-high   { border-left: 3px solid #f59e0b !important; background: #fffbeb !important; }
.row-normal { border-left: 3px solid #3b82f6 !important; background: #eff6ff !important; }
.dtable tbody tr.row-urgent:hover { background: #fee2e2 !important; }
.dtable tbody tr.row-high:hover   { background: #fef3c7 !important; }
.dtable tbody tr.row-normal:hover { background: #dbeafe !important; }

/* Sticky columns */
.sticky-left  { position: sticky; left: 0;    z-index: 2; background: white; }
.sticky-left2 { position: sticky; left: 46px; z-index: 2; background: white; }
.sticky-right { position: sticky; right: 0;   z-index: 2; background: white; }
/* When secondary columns are hidden, collapse table width so Actions doesn't float right */
.list-card.details-hidden .dtable { width: auto; }
.list-card.details-hidden .sticky-right { position: static !important; }
/* Propagate urgency backgrounds to sticky cols */
tr.row-urgent .sticky-left, tr.row-urgent .sticky-left2, tr.row-urgent .sticky-right { background: #fef2f2; }
tr.row-high   .sticky-left, tr.row-high   .sticky-left2, tr.row-high   .sticky-right { background: #fffbeb; }
tr.row-normal .sticky-left, tr.row-normal .sticky-left2, tr.row-normal .sticky-right { background: #eff6ff; }
tr:hover .sticky-left, tr:hover .sticky-left2, tr:hover .sticky-right { background: #f8fafc; }
tr.row-urgent:hover .sticky-left, tr.row-urgent:hover .sticky-left2, tr.row-urgent:hover .sticky-right { background: #fee2e2; }
tr.row-high:hover   .sticky-left, tr.row-high:hover   .sticky-left2, tr.row-high:hover   .sticky-right { background: #fef3c7; }
tr.row-normal:hover .sticky-left, tr.row-normal:hover .sticky-left2, tr.row-normal:hover .sticky-right { background: #dbeafe; }

/* ── Badges ──────────────────────────────────────────── */
.sbadge {
    display: inline-flex; align-items: center; gap: 5px;
    padding: 3px 9px; border-radius: 20px;
    font-size: 11.5px; font-weight: 600; white-space: nowrap;
}
.sdot { width: 6px; height: 6px; border-radius: 50%; display: inline-block; flex-shrink: 0; }
.sb-amber   { background: #fef3c7; color: #d97706; } .sb-amber   .sdot { background: #f59e0b; }
.sb-emerald { background: #d1fae5; color: #059669; } .sb-emerald .sdot { background: #10b981; }
.sb-blue    { background: #dbeafe; color: #2563eb; } .sb-blue    .sdot { background: #3b82f6; }
.sb-red     { background: #fee2e2; color: #dc2626; } .sb-red     .sdot { background: #ef4444; }
.sb-purple  { background: #ede9fe; color: #7c3aed; } .sb-purple  .sdot { background: #8b5cf6; }
.sb-cyan    { background: #cffafe; color: #0e7490; } .sb-cyan    .sdot { background: #06b6d4; }
.sb-green   { background: #dcfce7; color: #16a34a; } .sb-green   .sdot { background: #22c55e; }
.sb-slate   { background: #f1f5f9; color: #475569; } .sb-slate   .sdot { background: #94a3b8; }
/* Solid priority badges */
.sbadge-solid { color: white; }
.sbadge-solid.sb-red    { background: #ef4444; }
.sbadge-solid.sb-amber  { background: #f59e0b; }
.sbadge-solid.sb-cyan   { background: #06b6d4; }
.sbadge-solid.sb-slate  { background: #94a3b8; }

/* ── Age / group / fix badges ────────────────────────── */
.age-badge { display: inline-flex; align-items: center; padding: 1px 6px; border-radius: 20px; font-size: 10.5px; font-weight: 700; margin-left: 4px; }
.age-urgent { background: #fee2e2; color: #dc2626; }
.age-high   { background: #fef3c7; color: #d97706; }
.age-normal { background: #dbeafe; color: #2563eb; }
.group-badge { background: #f1f5f9; color: #475569; font-size: 10px; font-weight: 600; padding: 1px 7px; border-radius: 20px; margin-left: 4px; }
.fix-badge   { background: #fee2e2; color: #dc2626; font-size: 10px; font-weight: 700; padding: 1px 7px; border-radius: 20px; margin-left: 4px; }

/* ── Requester chip ──────────────────────────────────── */
.req-chip { display: flex; align-items: center; gap: 8px; }
.req-avatar { width: 30px; height: 30px; border-radius: 50%; object-fit: cover; flex-shrink: 0; border: 1.5px solid #e2e8f0; }
.req-name { font-size: 13px; font-weight: 500; color: #1e293b; line-height: 1.3; }
.req-mat  { font-size: 11px; color: #94a3b8; }

/* ── Delivered ───────────────────────────────────────── */
.delivered-ok { display: inline-flex; align-items: center; gap: 4px; font-size: 12px; font-weight: 600; color: #059669; }

/* ── Row action buttons ──────────────────────────────── */
.row-actions { display: flex; align-items: center; gap: 4px; }
.ra-btn {
    display: inline-flex; align-items: center; justify-content: center;
    width: 30px; height: 30px; border-radius: 7px; font-size: 14px;
    border: 1.5px solid #e2e8f0; background: #f8fafc;
    color: #64748b; cursor: pointer; transition: all 0.16s;
    text-decoration: none; padding: 0;
}
.ra-btn:hover            { border-color: #cbd5e1; background: #f1f5f9; color: #1e293b; }
.ra-btn.ra-view:hover    { border-color: #93c5fd; background: #eff6ff; color: #2563eb; }
.ra-btn.ra-fav:hover     { border-color: #fcd34d; background: #fffbeb; color: #d97706; }
.ra-btn.ra-fav.active    { border-color: #f59e0b; background: #fef3c7; color: #d97706; }
.ra-btn.ra-approve:hover { border-color: #6ee7b7; background: #ecfdf5; color: #059669; }
.ra-btn.ra-reject:hover  { border-color: #fca5a5; background: #fef2f2; color: #dc2626; }
.ra-btn.ra-pending:hover { border-color: #67e8f9; background: #ecfeff; color: #0e7490; }
.ra-btn.ra-accepted:hover{ border-color: #6ee7b7; background: #ecfdf5; color: #059669; }
.ra-btn.ra-refused:hover { border-color: #fca5a5; background: #fef2f2; color: #dc2626; }
.ra-btn.status-active    { color: white !important; border-color: transparent !important; }
.ra-btn.ra-pending.status-active  { background: #06b6d4; }
.ra-btn.ra-accepted.status-active { background: #10b981; }
.ra-btn.ra-refused.status-active  { background: #ef4444; }
/* Mark-as-delivered pill button */
.btn-deliver {
    display: inline-flex; align-items: center; gap: 5px;
    height: 28px; padding: 0 10px; border-radius: 7px; font-size: 11.5px;
    font-weight: 600; background: #ecfdf5; color: #059669;
    border: 1.5px solid #6ee7b7; cursor: pointer; transition: all 0.16s;
    font-family: 'Poppins', sans-serif;
}
.btn-deliver:hover { background: #d1fae5; border-color: #34d399; }
.btn-undeliver {
    display: inline-flex; align-items: center;
    height: 20px; padding: 0 7px; border-radius: 5px; font-size: 10.5px;
    font-weight: 600; background: #f1f5f9; color: #64748b;
    border: 1.5px solid #e2e8f0; cursor: pointer; transition: all 0.16s;
    font-family: 'Poppins', sans-serif; margin-left: 5px;
}
.btn-undeliver:hover { background: #fee2e2; color: #dc2626; border-color: #fca5a5; }

/* ── Empty state ─────────────────────────────────────── */
.empty-state { padding: 60px 20px; text-align: center; color: #94a3b8; }
.empty-state i { font-size: 42px; display: block; margin-bottom: 10px; opacity: 0.5; }
.empty-state p { font-size: 14px; margin: 0; }

/* ── Pagination wrapper ──────────────────────────────── */
.pag-wrap { padding: 14px 20px; border-top: 1px solid #f1f5f9; }

/* ── Spin ────────────────────────────────────────────── */
@keyframes spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }
.spin { animation: spin 1s linear infinite; display: inline-block; }

/* ── Mobile responsive ───────────────────────────────── */
.filter-panel-head {
    display: flex; align-items: center; justify-content: space-between;
}
.filter-toggle-btn {
    display: none; align-items: center; justify-content: center;
    width: 32px; height: 32px; border-radius: 8px;
    background: #f1f5f9; border: 1.5px solid #e2e8f0;
    color: #64748b; cursor: pointer; transition: all 0.18s;
    flex-shrink: 0;
}
.filter-toggle-btn:hover { background: #e2e8f0; color: #1e293b; }
.filter-toggle-btn i { font-size: 14px; transition: transform 0.2s; }
.filter-toggle-btn.open i { transform: rotate(180deg); }

@media (max-width: 767px) {
    .filter-toggle-btn { display: flex; }
    .filter-body { display: none; margin-top: 12px; }
    .filter-body.open { display: block; }
    .filter-panel-title { margin-bottom: 0; }

    .list-toolbar { margin-bottom: 8px; }
    .btn-toggle-cols { font-size: 11.5px; padding: 5px 10px; }

    .bulk-bar { padding: 10px 14px; }
    .bulk-bar-actions { gap: 5px; }
    .btn-bulk { padding: 5px 9px; font-size: 11.5px; }

    .dtable thead th { padding: 10px 10px; font-size: 10.5px; }
    .dtable td { padding: 10px 10px; }
    .ra-btn { width: 28px; height: 28px; font-size: 13px; }

    .pag-wrap { padding: 10px 14px; }

    .empty-state { padding: 40px 16px; }
}

@media (max-width: 479px) {
    .filter-panel { padding: 14px; }
    .list-card { border-radius: 12px; }
    .btn-create { padding: 8px 14px; font-size: 12.5px; }
}
</style>
@endpush

@section('content')
@php
    $typeBadge = [
        'recuperation'  => 'sb-amber',
        'creation'      => 'sb-emerald',
        'suspension'    => 'sb-blue',
        'desactivation' => 'sb-red',
        'ajustement'    => 'sb-purple',
    ];
    $typeLabels = [
        'recuperation'  => 'Récupération',
        'creation'      => 'Création',
        'suspension'    => 'Suspension',
        'desactivation' => 'Désactivation',
        'ajustement'    => 'Ajustement',
    ];
    $statusBadge = [
        'en_attente'      => 'sb-amber',
        'validee'         => 'sb-emerald',
        'rejetee'         => 'sb-red',
        'demande_envoyee' => 'sb-cyan',
        'pending'         => 'sb-cyan',
        'accepted'        => 'sb-green',
        'refused'         => 'sb-red',
    ];
    $statusLabels = [
        'en_attente'      => 'En attente',
        'validee'         => 'Validée',
        'rejetee'         => 'Rejetée',
        'demande_envoyee' => 'Demande envoyée',
        'pending'         => 'Pending',
        'accepted'        => 'Acceptée',
        'refused'         => 'Refusée',
    ];
    $priorityBadge = ['urgent' => 'sb-red', 'high' => 'sb-amber', 'normal' => 'sb-cyan'];
    $hasFilters = request()->hasAny(['request_type','status','delivered','grouped','favorites','collaborator','agence','phone_number','iccid']);
@endphp

{{-- ── Filter panel ────────────────────────────────── --}}
<div class="filter-panel">
    <div class="filter-panel-head">
        <div class="filter-panel-title"><i class="bi bi-funnel"></i> Filtres</div>
        <button type="button" class="filter-toggle-btn" id="filter-toggle-btn" onclick="toggleFilterPanel()" aria-label="Afficher/masquer les filtres">
            <i class="bi bi-chevron-down"></i>
        </button>
    </div>
    <form method="GET" action="{{ route('sim-requests.index') }}" id="filters-form">
    <div class="filter-body" id="filter-body">
        <div class="row g-2 mb-2">
            <div class="col-md-2">
                <select name="request_type" id="filter-request-type" class="filter-input">
                    <option value="">Tous les types</option>
                    <option value="recuperation"  {{ request('request_type') === 'recuperation'  ? 'selected' : '' }}>Récupération</option>
                    <option value="creation"      {{ request('request_type') === 'creation'      ? 'selected' : '' }}>Création</option>
                    <option value="suspension"    {{ request('request_type') === 'suspension'    ? 'selected' : '' }}>Suspension</option>
                    <option value="desactivation" {{ request('request_type') === 'desactivation' ? 'selected' : '' }}>Désactivation</option>
                    <option value="ajustement"    {{ request('request_type') === 'ajustement'    ? 'selected' : '' }}>Ajustement</option>
                </select>
            </div>
            <div class="col-md-2">
                <select name="status" id="filter-status" class="filter-input">
                    <option value="">Tous les statuts</option>
                    <option value="en_attente"      {{ request('status') === 'en_attente'      ? 'selected' : '' }}>En attente</option>
                    <option value="validee"         {{ request('status') === 'validee'         ? 'selected' : '' }}>Validée</option>
                    <option value="rejetee"         {{ request('status') === 'rejetee'         ? 'selected' : '' }}>Rejetée</option>
                    <option value="demande_envoyee" {{ request('status') === 'demande_envoyee' ? 'selected' : '' }}>Demande envoyée</option>
                    <option value="pending"         {{ request('status') === 'pending'         ? 'selected' : '' }}>Pending (opérateur)</option>
                    <option value="accepted"        {{ request('status') === 'accepted'        ? 'selected' : '' }}>Acceptée (opérateur)</option>
                    <option value="refused"         {{ request('status') === 'refused'         ? 'selected' : '' }}>Refusée (opérateur)</option>
                </select>
            </div>
            <div class="col-md-2">
                <select name="delivered" id="filter-delivered" class="filter-input">
                    <option value="">Livré : tous</option>
                    <option value="1" {{ request('delivered') === '1' ? 'selected' : '' }}>Livrées</option>
                    <option value="0" {{ request('delivered') === '0' ? 'selected' : '' }}>Non livrées</option>
                </select>
            </div>
            <div class="col-md-2">
                <div class="fi-wrap">
                    <i class="bi bi-person fi-icon"></i>
                    <input type="text" name="collaborator" class="filter-input" value="{{ request('collaborator') }}" placeholder="Collaborateur">
                </div>
            </div>
            <div class="col-md-2">
                <div class="fi-wrap">
                    <i class="bi bi-building fi-icon"></i>
                    <input type="text" name="agence" class="filter-input" value="{{ request('agence') }}" placeholder="Agence">
                </div>
            </div>
            <div class="col-md-2">
                <div class="fi-wrap">
                    <i class="bi bi-telephone fi-icon"></i>
                    <input type="text" name="phone_number" class="filter-input" value="{{ request('phone_number') }}" placeholder="N° de ligne">
                </div>
            </div>
        </div>
        <div class="row g-2 align-items-center">
            <div class="col-md-2">
                <div class="fi-wrap">
                    <i class="bi bi-upc-scan fi-icon"></i>
                    <input type="text" name="iccid" class="filter-input" value="{{ request('iccid') }}" placeholder="ICCID">
                </div>
            </div>
            <div class="col-auto">
                <span class="fi-switch-label">Favoris</span>
                <div class="form-check form-switch mb-0">
                    <input class="form-check-input" type="checkbox" name="favorites" id="filter-favorites" value="1" {{ request('favorites') === '1' ? 'checked' : '' }}>
                </div>
            </div>
            <div class="col-auto">
                <span class="fi-switch-label">Groupées</span>
                <div class="form-check form-switch mb-0">
                    <input class="form-check-input" type="checkbox" name="grouped" id="filter-grouped" value="1" {{ request('grouped') === '1' ? 'checked' : '' }}>
                </div>
            </div>
            <div class="col-auto ms-auto d-flex gap-2 flex-wrap align-items-center">
                @if($hasFilters)
                <a href="{{ route('sim-requests.index') }}" class="btn-filter btn-filter-reset">
                    <i class="bi bi-x-circle"></i> Réinitialiser
                </a>
                @endif
                @php $ep = request()->query(); $ep['format'] = 'excel'; @endphp
                <a href="{{ route('sim-requests.export', $ep) }}" class="btn-filter btn-filter-excel" id="export-excel">
                    <i class="bi bi-file-earmark-excel"></i> Excel
                </a>
                @php $ep['format'] = 'pdf'; @endphp
                <a href="{{ route('sim-requests.export', $ep) }}" class="btn-filter btn-filter-pdf" id="export-pdf">
                    <i class="bi bi-file-earmark-pdf"></i> PDF
                </a>
                <button type="submit" class="btn-filter btn-filter-primary">
                    <i class="bi bi-funnel"></i> Filtrer
                </button>
            </div>
        </div>
    </div>{{-- /.filter-body --}}
    </form>
</div>

{{-- ── Toolbar ──────────────────────────────────────── --}}
<div class="list-toolbar">
    <span class="list-count">{{ $requests->total() }} demande(s)</span>
</div>

{{-- ── Bulk bar ─────────────────────────────────────── --}}
<div class="bulk-bar" id="bulk-actions-bar">
    <div class="bulk-bar-info">
        <strong id="selected-count">0</strong> élément(s) sélectionné(s)
    </div>
    <div class="bulk-bar-actions">
        @if(auth()->user()->isValidator())
        <button type="button" class="btn-bulk bb-approve" onclick="bulkAction('approve')"><i class="bi bi-check-circle"></i> Valider</button>
        <button type="button" class="btn-bulk bb-reject"  onclick="bulkAction('reject')"><i class="bi bi-x-circle"></i> Rejeter</button>
        @endif
        @if(auth()->user()->isAdmin())
        <button type="button" class="btn-bulk bb-pending"  onclick="bulkAction('status','pending')"><i class="bi bi-clock"></i> Pending</button>
        <button type="button" class="btn-bulk bb-accepted" onclick="bulkAction('status','accepted')"><i class="bi bi-check"></i> Accepted</button>
        <button type="button" class="btn-bulk bb-refused"  onclick="bulkAction('status','refused')"><i class="bi bi-x"></i> Refused</button>
        @endif
        <button type="button" class="btn-bulk bb-export"  onclick="bulkAction('export')"><i class="bi bi-download"></i> Exporter</button>
        @if(auth()->user()->canValidateRequests())
        <button type="button" class="btn-bulk bb-delete"  onclick="bulkAction('delete')"><i class="bi bi-trash"></i> Supprimer</button>
        @endif
        <button type="button" class="btn-bulk bb-cancel"  onclick="clearSelection()"><i class="bi bi-x"></i> Annuler</button>
    </div>
</div>

{{-- ── Table card ───────────────────────────────────── --}}
<div class="list-card">
    <div class="table-responsive">
        <table class="dtable">
            <thead>
                <tr>
                    <th class="sticky-left" style="width:46px;">
                        <input type="checkbox" id="select-all" onchange="toggleSelectAll(this)" style="cursor:pointer;">
                    </th>
                    <th class="sticky-left2">N° Demande</th>
                    <th>Type</th>
                    <th class="col-secondary">Demandeur</th>
                    <th class="col-secondary">Collaborateur</th>
                    <th class="col-secondary">Ligne</th>
                    <th>Statut</th>
                    <th class="col-secondary">Livré</th>
                    <th class="col-secondary">Priorité</th>
                    <th class="col-secondary">Date</th>
                    <th class="sticky-right">Actions</th>
                </tr>
            </thead>
            <tbody id="table-body">
                @forelse($requests as $req)
                    @php
                        $daysPending = $req->status === 'en_attente' ? now()->diffInDays($req->created_at) : 0;
                        $urg = $daysPending >= 7 ? 'urgent' : ($daysPending >= 5 ? 'high' : ($daysPending >= 3 ? 'normal' : ''));

                        $requester = $req->creator ?? $req->user;

                        if ($req->isCreation()) {
                            $collabName = trim(($req->beneficiary_name ?? '') . ' ' . ($req->beneficiary_first_name ?? '')) ?: null;
                            $collabMat  = $req->beneficiary_matricule ?? null;
                        } else {
                            $collabName = trim(($req->collaborator_name ?? '') . ' ' . ($req->collaborator_first_name ?? '')) ?: null;
                            $collabMat  = $req->collaborator_matricule ?? null;
                            if (!$collabName && !$collabMat) {
                                $collabName = $req->user->full_name ?? null;
                                $collabMat  = $req->user->matricule ?? null;
                            }
                        }
                        $lineNumber = $req->phone_number ?? ($req->sim ? $req->sim->phone_number : null);

                        $needsAttention = $req->isCreation()
                            ? empty($req->beneficiary_name)
                            : (empty($req->collaborator_matricule) || empty($lineNumber));

                        $typeClass   = $typeBadge[$req->request_type] ?? 'sb-slate';
                        $statusClass = $statusBadge[$req->status]     ?? 'sb-slate';
                        $isFav       = !empty($req->is_favorite);
                    @endphp
                    <tr class="{{ $urg ? 'row-'.$urg : '' }}">
                        <td class="sticky-left">
                            <input type="checkbox" class="request-checkbox" value="{{ $req->id }}" onchange="updateBulkActions()" style="cursor:pointer;">
                        </td>
                        <td class="sticky-left2" style="font-weight:600; color:#1a1a1a;">
                            <a href="{{ route('sim-requests.show', $req) }}" class="text-decoration-none" style="color:inherit;">{{ $req->request_number }}</a>
                            @if($req->group_id)<span class="group-badge">Groupe</span>@endif
                            @if($urg)<span class="age-badge age-{{ $urg }}">{{ $daysPending }}j</span>@endif
                        </td>
                        <td>
                            <span class="sbadge {{ $typeClass }}">
                                <span class="sdot"></span>
                                {{ $typeLabels[$req->request_type] ?? ucfirst($req->request_type) }}
                            </span>
                        </td>
                        <td class="col-secondary">
                            @if($requester)
                                <div class="req-chip">
                                    <img src="{{ $requester->avatar_url }}" alt="" class="req-avatar">
                                    <div>
                                        <div class="req-name">{{ $requester->full_name }}</div>
                                        @if($requester->matricule)<div class="req-mat">{{ $requester->matricule }}</div>@endif
                                    </div>
                                </div>
                            @else
                                <span style="color:#d1d5db;">—</span>
                            @endif
                        </td>
                        <td class="col-secondary">
                            @if($collabName)
                                <div style="font-size:13px; color:#374151;">{{ $collabName }}</div>
                                @if($collabMat)<div style="font-size:11px; color:#94a3b8;">{{ $collabMat }}</div>@endif
                            @elseif($collabMat)
                                <div style="font-size:13px; color:#374151;">{{ $collabMat }}</div>
                            @else
                                <span style="color:#d1d5db;">—</span>
                            @endif
                        </td>
                        <td class="col-secondary" style="font-size:13px;">
                            @if($lineNumber)
                                <div>{{ $lineNumber }}</div>
                            @endif
                            @php
                                $iccidDisplay = $req->requested_iccid ?? ($req->sim ? $req->sim->iccid : null);
                            @endphp
                            @if($iccidDisplay)
                                <div style="font-size:11px;color:#94a3b8;font-family:monospace;">{{ $iccidDisplay }}</div>
                            @elseif(!$lineNumber)
                                <span style="color:#d1d5db;">—</span>
                            @endif
                        </td>
                        <td>
                            <span class="sbadge {{ $statusClass }}">
                                <span class="sdot"></span>
                                {{ $statusLabels[$req->status] ?? ucfirst($req->status) }}
                            </span>
                            @if($needsAttention)<span class="fix-badge">À corriger</span>@endif
                        </td>
                        <td class="col-secondary">
                            @if($req->isDelivered())
                                <span class="delivered-ok"><i class="bi bi-check-circle-fill"></i> Livré</span>
                                @if(auth()->user()->isValidator())
                                    <form method="POST" action="{{ route('sim-requests.toggle-delivered', $req) }}" class="d-inline"
                                          data-confirm="Retirer la marque « livré » ?" data-confirm-variant="secondary" data-confirm-text="Retirer">
                                        @csrf
                                        <button type="submit" class="btn-undeliver" title="Retirer livré">✕</button>
                                    </form>
                                @endif
                            @else
                                @if(auth()->user()->isValidator())
                                    <form method="POST" action="{{ route('sim-requests.toggle-delivered', $req) }}" class="d-inline"
                                          data-confirm="Marquer cette demande comme livrée ?" data-confirm-variant="success" data-confirm-text="Marquer livré">
                                        @csrf
                                        <button type="submit" class="btn-deliver" title="Marquer livré">
                                            <i class="bi bi-box-seam"></i> Livré
                                        </button>
                                    </form>
                                @else
                                    <span style="color:#d1d5db;">—</span>
                                @endif
                            @endif
                        </td>
                        <td class="col-secondary">
                            @if($req->priority)
                                @php $pClass = $priorityBadge[$req->priority] ?? 'sb-slate'; @endphp
                                <span class="sbadge sbadge-solid {{ $pClass }}">{{ ucfirst($req->priority) }}</span>
                            @else
                                <span style="color:#d1d5db;">—</span>
                            @endif
                        </td>
                        <td class="col-secondary" style="color:#6b7280; font-size:12.5px; white-space:nowrap;">
                            {{ $req->created_at->format('d/m/Y H:i') }}
                        </td>
                        <td class="sticky-right">
                            <div class="row-actions">
                                <button type="button"
                                    class="ra-btn ra-fav {{ $isFav ? 'active' : '' }}"
                                    title="{{ $isFav ? 'Retirer des favoris' : 'Ajouter aux favoris' }}"
                                    onclick="toggleFavorite({{ $req->id }}, this)">
                                    <i class="bi {{ $isFav ? 'bi-star-fill' : 'bi-star' }}"></i>
                                </button>
                                <a href="{{ route('sim-requests.show', $req) }}" class="ra-btn ra-view" title="Voir les détails">
                                    <i class="bi bi-eye"></i>
                                </a>
                                @if(auth()->user()->canValidateRequests() && $req->isRecuperation() && $req->status === 'en_attente' && $req->created_by !== auth()->id())
                                    <form method="POST" action="{{ route('sim-requests.approve', $req) }}" class="d-inline"
                                          data-confirm="Valider cette demande ?" data-confirm-variant="success" data-confirm-text="Valider">
                                        @csrf
                                        <button type="submit" class="ra-btn ra-approve" title="Valider">
                                            <i class="bi bi-check-circle"></i>
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ route('sim-requests.reject', $req) }}" class="d-inline"
                                          data-confirm="Rejeter cette demande ?" data-confirm-variant="danger" data-confirm-text="Rejeter">
                                        @csrf
                                        <button type="submit" class="ra-btn ra-reject" title="Rejeter">
                                            <i class="bi bi-x-circle"></i>
                                        </button>
                                    </form>
                                @endif
                                @if(auth()->user()->isAdmin())
                                    <form method="POST" action="{{ route('sim-requests.quick-update-status', $req) }}" class="d-inline"
                                          data-confirm="Mettre à jour le statut à Pending ?" data-confirm-variant="primary" data-confirm-text="Mettre à jour">
                                        @csrf
                                        <input type="hidden" name="status" value="pending">
                                        <button type="submit" class="ra-btn ra-pending {{ $req->status === 'pending' ? 'status-active' : '' }}" title="Pending">
                                            <i class="bi bi-clock"></i>
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ route('sim-requests.quick-update-status', $req) }}" class="d-inline"
                                          data-confirm="Mettre à jour le statut à Accepted ?" data-confirm-variant="success" data-confirm-text="Mettre à jour">
                                        @csrf
                                        <input type="hidden" name="status" value="accepted">
                                        <button type="submit" class="ra-btn ra-accepted {{ $req->status === 'accepted' ? 'status-active' : '' }}" title="Accepted">
                                            <i class="bi bi-check-circle"></i>
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ route('sim-requests.quick-update-status', $req) }}" class="d-inline"
                                          data-confirm="Mettre à jour le statut à Refused ?" data-confirm-variant="danger" data-confirm-text="Mettre à jour">
                                        @csrf
                                        <input type="hidden" name="status" value="refused">
                                        <button type="submit" class="ra-btn ra-refused {{ $req->status === 'refused' ? 'status-active' : '' }}" title="Refused">
                                            <i class="bi bi-x-circle"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="11">
                            <div class="empty-state">
                                <i class="bi bi-inbox"></i>
                                <p>Aucune demande trouvée</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($requests->hasPages())
    <div class="pag-wrap d-flex justify-content-center" id="pagination-container">
        {{ $requests->appends(request()->query())->links('pagination::bootstrap-5') }}
    </div>
    @else
    <div id="pagination-container"></div>
    @endif
</div>

{{-- ── Bulk reject modal ────────────────────────────── --}}
<div class="modal fade" id="bulkRejectModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:16px; border:none; box-shadow:0 20px 60px rgba(0,0,0,0.15);">
            <div class="modal-header" style="border-bottom:1px solid #f1f5f9; padding:20px 24px;">
                <h5 class="modal-title" style="font-size:15px; font-weight:700; color:#1e293b;">Rejeter des demandes</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
            </div>
            <div class="modal-body" style="padding:20px 24px;">
                <p id="bulkRejectDescription" class="mb-3" style="font-size:13.5px; color:#64748b;">Vous êtes sur le point de rejeter ces demandes.</p>
                <label for="bulkRejectReason" style="font-size:13px; font-weight:600; color:#374151; margin-bottom:6px; display:block;">
                    Motif de rejet <span class="text-danger">*</span>
                </label>
                <textarea id="bulkRejectReason" class="filter-input" rows="3"
                          placeholder="Ex : informations incomplètes…"
                          style="height:auto; padding:10px 12px; resize:vertical;"></textarea>
                <div id="bulkRejectError" class="d-none" style="font-size:12px; color:#dc2626; margin-top:6px;">Le motif est obligatoire.</div>
            </div>
            <div class="modal-footer" style="border-top:1px solid #f1f5f9; padding:16px 24px; gap:8px;">
                <button type="button" class="btn-filter btn-filter-reset" data-bs-dismiss="modal">Annuler</button>
                <button type="button" class="btn-filter btn-filter-danger" id="bulkRejectConfirm">
                    <i class="bi bi-x-circle"></i> Rejeter
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
const baseUrl         = '{{ route('sim-requests.index') }}';
const detailsStoreKey = 'sim-requests-show-details';

/* ── Filter panel toggle (mobile) ───────────────────── */
function toggleFilterPanel() {
    const body = document.getElementById('filter-body');
    const btn  = document.getElementById('filter-toggle-btn');
    const open = body.classList.toggle('open');
    btn.classList.toggle('open', open);
}
// On desktop, always show filter body
function initFilterPanel() {
    if (window.innerWidth >= 768) {
        document.getElementById('filter-body')?.classList.add('open');
    } else if ({{ $hasFilters ? 'true' : 'false' }}) {
        // Auto-open on mobile if filters are active
        document.getElementById('filter-body')?.classList.add('open');
        document.getElementById('filter-toggle-btn')?.classList.add('open');
    }
}
window.addEventListener('resize', function() {
    if (window.innerWidth >= 768) {
        document.getElementById('filter-body')?.classList.add('open');
    }
});

/* ── Column toggle ──────────────────────────────────── */
function applyColumnVisibility(show) {
    document.querySelectorAll('.col-secondary').forEach(el => el.classList.toggle('d-none', !show));
    document.querySelector('.list-card')?.classList.toggle('details-hidden', !show);
    const label = document.getElementById('toggle-cols-label');
    if (label) label.textContent = show ? 'Masquer détails' : 'Afficher détails';
}

document.getElementById('toggle-columns-btn')?.addEventListener('click', function() {
    const current = localStorage.getItem(detailsStoreKey) !== 'false';
    const next    = !current;
    localStorage.setItem(detailsStoreKey, next ? 'true' : 'false');
    applyColumnVisibility(next);
});

/* ── Auto-submit on select change ───────────────────── */
['filter-request-type', 'filter-status', 'filter-delivered'].forEach(id => {
    document.getElementById(id)?.addEventListener('change', () =>
        document.getElementById('filters-form').submit()
    );
});

/* ── Favorite toggle ────────────────────────────────── */
function toggleFavorite(id, btn) {
    fetch(`{{ url('sim-requests') }}/${id}/toggle-favorite`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
        }
    })
    .then(r => r.json())
    .then(data => {
        if (!data.success) return;
        if (window.showToast) showToast(data.message, 'success');
        const icon = btn.querySelector('i');
        if (data.is_favorite) {
            btn.classList.add('active');
            icon.className = 'bi bi-star-fill';
            btn.title = 'Retirer des favoris';
        } else {
            btn.classList.remove('active');
            icon.className = 'bi bi-star';
            btn.title = 'Ajouter aux favoris';
        }
    })
    .catch(() => { if (window.showToast) showToast('Erreur lors de la mise à jour des favoris', 'error'); });
}

/* ── Bulk selection ─────────────────────────────────── */
function toggleSelectAll(cb) {
    document.querySelectorAll('.request-checkbox').forEach(c => c.checked = cb.checked);
    updateBulkActions();
}

function updateBulkActions() {
    const selected = document.querySelectorAll('.request-checkbox:checked');
    const count    = selected.length;
    const all      = document.querySelectorAll('.request-checkbox');
    const bar      = document.getElementById('bulk-actions-bar');
    const sa       = document.getElementById('select-all');
    const countEl  = document.getElementById('selected-count');

    bar?.classList.toggle('visible', count > 0);
    if (countEl) countEl.textContent = count;
    if (sa) {
        sa.checked       = count === all.length && count > 0;
        sa.indeterminate = count > 0 && count < all.length;
    }
}

function clearSelection() {
    document.querySelectorAll('.request-checkbox').forEach(c => c.checked = false);
    const sa = document.getElementById('select-all');
    if (sa) { sa.checked = false; sa.indeterminate = false; }
    updateBulkActions();
}

function attachCheckboxListeners() {
    document.querySelectorAll('.request-checkbox').forEach(cb => {
        cb.removeEventListener('change', updateBulkActions);
        cb.addEventListener('change', updateBulkActions);
    });
    const sa = document.getElementById('select-all');
    if (sa) {
        sa.removeEventListener('change', _saHandler);
        sa.addEventListener('change', _saHandler);
    }
}
function _saHandler() { toggleSelectAll(this); }

function getSelectedIds() {
    return Array.from(document.querySelectorAll('.request-checkbox:checked')).map(c => c.value);
}

/* ── Bulk actions ───────────────────────────────────── */
let bulkRejectModal = null;
let bulkRejectIds   = [];

function bulkAction(action, status = null) {
    const ids = getSelectedIds();
    if (!ids.length) { alert('Veuillez sélectionner au moins une demande.'); return; }

    if (action === 'export') {
        const p = new URLSearchParams();
        ids.forEach(id => p.append('ids[]', id));
        window.location.href = '{{ route('sim-requests.export') }}?format=excel&' + p.toString();
        return;
    }
    if (action === 'reject') {
        bulkRejectIds = ids;
        const desc = document.getElementById('bulkRejectDescription');
        if (desc) desc.textContent = `Vous êtes sur le point de rejeter ${ids.length} demande(s).`;
        document.getElementById('bulkRejectReason').value = '';
        document.getElementById('bulkRejectError')?.classList.add('d-none');
        bulkRejectModal?.show();
        return;
    }
    if (action === 'approve') {
        return window.showConfirmModal(`Valider ${ids.length} demande(s) ?`,
            () => execBulk(action, status, { ids }),
            { confirmText: 'Valider', confirmVariant: 'success', title: 'Confirmer la validation' });
    }
    if (action === 'delete') {
        return window.showConfirmModal(`Supprimer ${ids.length} demande(s) ?`,
            () => execBulk(action, status, { ids }),
            { confirmText: 'Supprimer', confirmVariant: 'danger', title: 'Confirmer la suppression' });
    }
    if (action === 'status') {
        const labels = { pending: 'Pending', accepted: 'Accepted', refused: 'Refused' };
        return window.showConfirmModal(
            `Mettre à jour le statut à "${labels[status] ?? status}" pour ${ids.length} demande(s) ?`,
            () => execBulk(action, status, { ids }),
            { confirmText: 'Mettre à jour', confirmVariant: status === 'refused' ? 'danger' : 'primary', title: 'Confirmer le changement' });
    }
    execBulk(action, status, { ids });
}

function execBulk(action, status, payload) {
    const urlMap = {
        approve: '{{ route('sim-requests.bulk-approve') }}',
        reject:  '{{ route('sim-requests.bulk-reject') }}',
        status:  '{{ route('sim-requests.bulk-update-status') }}',
        delete:  '{{ route('sim-requests.bulk-delete') }}',
    };
    const data = { ...payload };
    if (status) data.status = status;

    fetch(urlMap[action], {
        method: action === 'delete' ? 'DELETE' : 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
        },
        body: JSON.stringify(data)
    })
    .then(r => r.json())
    .then(res => {
        if (res.success) {
            if (window.showToast) showToast(res.message || 'Action effectuée avec succès', 'success');
            clearSelection();
            setTimeout(() => window.location.reload(), 900);
        } else {
            if (window.showToast) showToast(res.message || 'Une erreur est survenue', 'error');
            else alert(res.message || 'Une erreur est survenue.');
        }
    })
    .catch(() => {
        if (window.showToast) showToast("Une erreur est survenue lors de l'action en masse", 'error');
        else alert("Une erreur est survenue lors de l'action en masse.");
    });
}

/* ── AJAX pagination ────────────────────────────────── */
document.addEventListener('click', function(e) {
    const link = e.target.closest('.pagination a');
    if (!link) return;
    e.preventDefault();
    const tbody = document.getElementById('table-body');
    tbody.innerHTML = `<tr><td colspan="11" class="text-center" style="padding:40px; color:#94a3b8;">
        <i class="bi bi-arrow-repeat spin"></i> Chargement…</td></tr>`;

    fetch(link.href, { headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'text/html' } })
    .then(r => r.text())
    .then(html => {
        const doc  = new DOMParser().parseFromString(html, 'text/html');
        const nb   = doc.getElementById('table-body');
        const np   = doc.getElementById('pagination-container');
        if (nb) {
            tbody.innerHTML = nb.innerHTML;
            clearSelection();
            attachCheckboxListeners();
            applyColumnVisibility(true);
        }
        if (np) document.getElementById('pagination-container').innerHTML = np.innerHTML;
        window.scrollTo({ top: 0, behavior: 'smooth' });
    })
    .catch(() => window.location.href = link.href);
});

/* ── Init ───────────────────────────────────────────── */
document.addEventListener('DOMContentLoaded', function() {
    initFilterPanel();
    applyColumnVisibility(true);
    attachCheckboxListeners();

    const modalEl = document.getElementById('bulkRejectModal');
    if (modalEl && typeof bootstrap !== 'undefined') {
        bulkRejectModal = new bootstrap.Modal(modalEl);
    }

    document.getElementById('bulkRejectConfirm')?.addEventListener('click', function() {
        const reason = document.getElementById('bulkRejectReason')?.value.trim();
        const error  = document.getElementById('bulkRejectError');
        if (!reason) { error?.classList.remove('d-none'); return; }
        error?.classList.add('d-none');
        bulkRejectModal?.hide();
        execBulk('reject', null, { ids: bulkRejectIds, rejection_reason: reason });
    });
});
</script>
@endpush
@endsection
