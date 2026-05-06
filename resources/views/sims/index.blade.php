@extends('layouts.bootstrap')

@section('title', 'Inventaire SIM')
@section('page-title', 'Inventaire SIM')

@section('page-actions')
@if(auth()->user()->isAdmin())
<button type="button" class="btn-create" data-bs-toggle="modal" data-bs-target="#importModal">
    <i class="bi bi-upload"></i> Importer
</button>
@endif
@endsection

@push('styles')
<style>
/* ── Create/import button ───────────────────────────── */
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
.filter-panel-head { display: flex; align-items: center; justify-content: space-between; }
.filter-panel-title {
    font-size: 11.5px; font-weight: 700; color: #64748b;
    text-transform: uppercase; letter-spacing: 0.6px;
    display: flex; align-items: center; gap: 6px; margin-bottom: 0;
}
.filter-toggle-btn {
    display: none; align-items: center; justify-content: center;
    width: 32px; height: 32px; border-radius: 8px;
    background: #f1f5f9; border: 1.5px solid #e2e8f0;
    color: #64748b; cursor: pointer; transition: all 0.18s; flex-shrink: 0;
}
.filter-toggle-btn:hover { background: #e2e8f0; color: #1e293b; }
.filter-toggle-btn i { font-size: 14px; transition: transform 0.2s; }
.filter-toggle-btn.open i { transform: rotate(180deg); }
.filter-body { margin-top: 14px; }

.filter-input {
    height: 38px; font-size: 13px; border: 1.5px solid #e2e8f0;
    border-radius: 9px; padding: 0 12px;
    font-family: 'Poppins', sans-serif; color: #1e293b;
    transition: border-color 0.18s, box-shadow 0.18s;
    width: 100%; background: #f8fafc; appearance: none;
}
.filter-input:focus {
    outline: none; border-color: #00574A;
    box-shadow: 0 0 0 3px rgba(0,87,74,0.10); background: white;
}
select.filter-input { background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%2394a3b8' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='m2 5 6 6 6-6'/%3e%3c/svg%3e"); background-repeat: no-repeat; background-position: right 10px center; background-size: 14px; padding-right: 32px; }
.fi-wrap { position: relative; }
.fi-wrap .fi-icon { position: absolute; left: 10px; top: 50%; transform: translateY(-50%); color: #94a3b8; font-size: 13px; pointer-events: none; }
.fi-wrap .filter-input { padding-left: 30px; }

.btn-filter {
    height: 38px; padding: 0 16px; border-radius: 9px; font-size: 13px;
    font-weight: 600; display: inline-flex; align-items: center; gap: 6px;
    cursor: pointer; border: 1.5px solid transparent; transition: all 0.18s;
    font-family: 'Poppins', sans-serif; white-space: nowrap; text-decoration: none;
}
.btn-filter-primary { background: #00574A; color: white; border-color: #00574A; }
.btn-filter-primary:hover { background: #003d34; border-color: #003d34; color: white; }
.btn-filter-reset   { background: #f1f5f9; color: #475569; border-color: #e2e8f0; }
.btn-filter-reset:hover   { background: #e2e8f0; color: #1e293b; }
.btn-filter-excel   { background: #f0fdf4; color: #16a34a; border-color: #bbf7d0; }
.btn-filter-excel:hover   { background: #dcfce7; color: #15803d; }
.btn-filter-pdf     { background: #fff1f2; color: #dc2626; border-color: #fecaca; }
.btn-filter-pdf:hover     { background: #ffe4e6; color: #b91c1c; }
.btn-filter-danger  { background: #ef4444; color: white; border-color: #ef4444; }
.btn-filter-danger:hover  { background: #dc2626; border-color: #dc2626; }

/* ── Toolbar ─────────────────────────────────────────── */
.list-toolbar {
    display: flex; align-items: center; justify-content: space-between;
    margin-bottom: 10px; flex-wrap: wrap; gap: 8px;
}
.list-count { font-size: 12.5px; color: #64748b; font-weight: 500; }

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
.bb-assign  { background: #00574A; color: white; }
.bb-assign:hover  { background: #003d34; }
.bb-release { background: #f8fafc; color: #475569; border: 1.5px solid #e2e8f0; }
.bb-release:hover { background: #f1f5f9; color: #1e293b; }
.bb-export  { background: #f0fdf4; color: #16a34a; border: 1.5px solid #bbf7d0; }
.bb-export:hover  { background: #dcfce7; }
.bb-cancel  { background: white; color: #64748b; border: 1.5px solid #e2e8f0; }
.bb-cancel:hover  { background: #f1f5f9; color: #475569; }

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
.dtable tbody tr:hover { background: #f8fafc; }
.dtable td { padding: 13px 14px; font-size: 13px; color: #374151; vertical-align: middle; }

/* ── Badges ──────────────────────────────────────────── */
.sbadge {
    display: inline-flex; align-items: center; gap: 5px;
    padding: 3px 9px; border-radius: 20px;
    font-size: 11.5px; font-weight: 600; white-space: nowrap;
}
.sdot { width: 6px; height: 6px; border-radius: 50%; display: inline-block; flex-shrink: 0; }
.sb-emerald { background: #d1fae5; color: #059669; } .sb-emerald .sdot { background: #10b981; }
.sb-blue    { background: #dbeafe; color: #2563eb; } .sb-blue    .sdot { background: #3b82f6; }
.sb-red     { background: #fee2e2; color: #dc2626; } .sb-red     .sdot { background: #ef4444; }
.sb-amber   { background: #fef3c7; color: #d97706; } .sb-amber   .sdot { background: #f59e0b; }
.sb-slate   { background: #f1f5f9; color: #475569; } .sb-slate   .sdot { background: #94a3b8; }

/* ── Assigned user chip ──────────────────────────────── */
.user-chip { display: flex; align-items: center; gap: 7px; }
.user-avatar {
    width: 28px; height: 28px; border-radius: 50%; object-fit: cover;
    flex-shrink: 0; border: 1.5px solid #e2e8f0;
    background: #f1f5f9; display: flex; align-items: center; justify-content: center;
    font-size: 11px; font-weight: 700; color: #475569;
}
.user-name { font-size: 13px; font-weight: 500; color: #1e293b; }
.user-mat  { font-size: 11px; color: #94a3b8; }

/* ── Table column sizing ─────────────────────────────── */
.dtable th:last-child, .dtable td:last-child {
    width: 1%; text-align: center; white-space: nowrap;
}

/* ── Row action button ───────────────────────────────── */
.ra-btn {
    display: inline-flex; align-items: center; justify-content: center;
    width: 30px; height: 30px; border-radius: 7px; font-size: 14px;
    border: 1.5px solid #e2e8f0; background: #f8fafc;
    color: #64748b; cursor: pointer; transition: all 0.16s;
    text-decoration: none; padding: 0;
}
.ra-btn:hover          { border-color: #cbd5e1; background: #f1f5f9; color: #1e293b; }
.ra-btn.ra-view:hover  { border-color: #93c5fd; background: #eff6ff; color: #2563eb; }

/* ── Empty state ─────────────────────────────────────── */
.empty-state { padding: 60px 20px; text-align: center; color: #94a3b8; }
.empty-state i { font-size: 42px; display: block; margin-bottom: 10px; opacity: 0.5; }
.empty-state p { font-size: 14px; margin: 0; }

/* ── Pagination ──────────────────────────────────────── */
.pag-wrap { padding: 14px 20px; border-top: 1px solid #f1f5f9; }

/* ── Spin ────────────────────────────────────────────── */
@keyframes spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }
.spin { animation: spin 1s linear infinite; display: inline-block; }

/* ── User select in assign modal ─────────────────────── */
.assign-result-item {
    display: flex; align-items: center; gap: 10px;
    padding: 10px 14px; cursor: pointer;
    border-bottom: 1px solid #f1f5f9;
    transition: background 0.14s; font-size: 13px;
}
.assign-result-item:last-child { border-bottom: none; }
.assign-result-item:hover { background: #f8fafc; }
.assign-result-item .ari-name { font-weight: 600; color: #1e293b; }
.assign-result-item .ari-mat  { font-size: 11.5px; color: #94a3b8; margin-left: 6px; }
.assign-selected-card {
    display: flex; align-items: center; gap: 10px;
    background: #eef9f7; border: 1.5px solid #6ee7b7;
    border-radius: 10px; padding: 10px 14px; font-size: 13px;
}
.assign-selected-card i { color: #10b981; font-size: 16px; }

/* ── Mobile responsive ───────────────────────────────── */
@media (max-width: 767px) {
    .filter-toggle-btn { display: flex; }
    .filter-body { display: none; margin-top: 12px; }
    .filter-body.open { display: block; }
    .filter-panel-title { margin-bottom: 0; }
    .list-toolbar { margin-bottom: 8px; }
    .bulk-bar { padding: 10px 14px; }
    .bulk-bar-actions { gap: 5px; }
    .btn-bulk { padding: 5px 9px; font-size: 11.5px; }
    .dtable thead th { padding: 10px 10px; font-size: 10.5px; }
    .dtable td { padding: 10px 10px; }
    .ra-btn { width: 28px; height: 28px; font-size: 13px; }
    .pag-wrap { padding: 10px 14px; }
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
    $statusBadge = [
        'libre'       => 'sb-emerald',
        'attribue'    => 'sb-blue',
        'suspendu'    => 'sb-red',
        'defectueuse' => 'sb-amber',
    ];
    $statusLabels = [
        'libre'       => 'Libre',
        'attribue'    => 'Attribuée',
        'suspendu'    => 'Suspendue',
        'defectueuse' => 'Défectueuse',
    ];
    $hasFilters = request()->hasAny(['iccid', 'status', 'phone_number', 'operator', 'assigned']);
@endphp

{{-- ── Filter panel ─────────────────────────────────── --}}
<div class="filter-panel">
    <div class="filter-panel-head">
        <div class="filter-panel-title"><i class="bi bi-funnel"></i> Filtres</div>
        <button type="button" class="filter-toggle-btn" id="filter-toggle-btn" onclick="toggleFilterPanel()" aria-label="Afficher/masquer les filtres">
            <i class="bi bi-chevron-down"></i>
        </button>
    </div>
    <form method="GET" action="{{ route('sims.index') }}" id="filters-form">
    <div class="filter-body" id="filter-body">
        <div class="row g-2 mb-2">
            <div class="col-md-3">
                <div class="fi-wrap">
                    <i class="bi bi-upc-scan fi-icon"></i>
                    <input type="text" name="iccid" id="filter-iccid" class="filter-input"
                           placeholder="ICCID" value="{{ request('iccid') }}">
                </div>
            </div>
            <div class="col-md-2">
                <select name="status" id="filter-status" class="filter-input">
                    <option value="">Tous les statuts</option>
                    <option value="libre"       {{ request('status') === 'libre'       ? 'selected' : '' }}>Libre</option>
                    <option value="attribue"    {{ request('status') === 'attribue'    ? 'selected' : '' }}>Attribuée</option>
                    <option value="suspendu"    {{ request('status') === 'suspendu'    ? 'selected' : '' }}>Suspendue</option>
                    <option value="defectueuse" {{ request('status') === 'defectueuse' ? 'selected' : '' }}>Défectueuse</option>
                </select>
            </div>
            <div class="col-md-2">
                <div class="fi-wrap">
                    <i class="bi bi-telephone fi-icon"></i>
                    <input type="text" name="phone_number" class="filter-input"
                           placeholder="N° de ligne" value="{{ request('phone_number') }}">
                </div>
            </div>
            <div class="col-md-2">
                <div class="fi-wrap">
                    <i class="bi bi-broadcast fi-icon"></i>
                    <input type="text" name="operator" class="filter-input"
                           placeholder="Opérateur" value="{{ request('operator') }}">
                </div>
            </div>
            <div class="col-md-2">
                <select name="assigned" class="filter-input">
                    <option value="">Attribution : tous</option>
                    <option value="1" {{ request('assigned') === '1' ? 'selected' : '' }}>Attribuées</option>
                    <option value="0" {{ request('assigned') === '0' ? 'selected' : '' }}>Non attribuées</option>
                </select>
            </div>
            <div class="col-md-1 col-auto ms-md-auto d-flex gap-2 align-items-center justify-content-end flex-wrap">
                @if($hasFilters)
                <a href="{{ route('sims.index') }}" class="btn-filter btn-filter-reset">
                    <i class="bi bi-x-circle"></i> <span class="d-none d-sm-inline">Réinitialiser</span>
                </a>
                @endif
            </div>
        </div>
        <div class="d-flex gap-2 flex-wrap align-items-center justify-content-end">
            @php $ep = request()->query(); $ep['format'] = 'excel'; @endphp
            <a href="{{ route('sims.export', $ep) }}" class="btn-filter btn-filter-excel" id="export-excel">
                <i class="bi bi-file-earmark-excel"></i> Excel
            </a>
            @php $ep['format'] = 'pdf'; @endphp
            <a href="{{ route('sims.export', $ep) }}" class="btn-filter btn-filter-pdf" id="export-pdf">
                <i class="bi bi-file-earmark-pdf"></i> PDF
            </a>
            <button type="submit" class="btn-filter btn-filter-primary">
                <i class="bi bi-search"></i> Rechercher
            </button>
        </div>
    </div>{{-- /.filter-body --}}
    </form>
</div>

{{-- ── Toolbar ──────────────────────────────────────── --}}
<div class="list-toolbar">
    <span class="list-count">{{ $sims->total() }} SIM(s)</span>
</div>

{{-- ── Bulk bar ─────────────────────────────────────── --}}
<div class="bulk-bar" id="bulk-actions-bar">
    <div class="bulk-bar-info">
        <strong id="selected-count">0</strong> SIM(s) sélectionnée(s)
    </div>
    <div class="bulk-bar-actions">
        @if(auth()->user()->isAdmin())
        <button type="button" class="btn-bulk bb-assign"  onclick="openAssignModal()"><i class="bi bi-person-plus"></i> Attribuer</button>
        <button type="button" class="btn-bulk bb-release" onclick="confirmBulkRelease()"><i class="bi bi-person-dash"></i> Libérer</button>
        <button type="button" class="btn-bulk bb-export"  onclick="bulkExport()"><i class="bi bi-download"></i> Exporter</button>
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
                    @if(auth()->user()->isAdmin())
                    <th style="width:46px;">
                        <input type="checkbox" id="select-all" onchange="toggleSelectAll(this)" style="cursor:pointer;">
                    </th>
                    @endif
                    <th>ICCID</th>
                    <th>Téléphone</th>
                    <th>Statut</th>
                    <th>Opérateur</th>
                    <th>Assignée à</th>
                    <th>Date attribution</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="table-body">
                @forelse($sims as $sim)
                    @php
                        $stClass = $statusBadge[$sim->status] ?? 'sb-slate';
                        $stLabel = $statusLabels[$sim->status] ?? ucfirst($sim->status);
                    @endphp
                    <tr>
                        @if(auth()->user()->isAdmin())
                        <td>
                            <input type="checkbox" class="sim-checkbox" value="{{ $sim->id }}" onchange="updateBulkActions()" style="cursor:pointer;">
                        </td>
                        @endif
                        <td style="font-weight:600; color:#1a1a1a; font-family:monospace; font-size:12.5px;">
                            {{ $sim->iccid }}
                        </td>
                        <td style="color:#374151;">{{ $sim->phone_number ?? '—' }}</td>
                        <td>
                            <span class="sbadge {{ $stClass }}">
                                <span class="sdot"></span>{{ $stLabel }}
                            </span>
                        </td>
                        <td style="color:#374151;">{{ $sim->operator ?? '—' }}</td>
                        <td>
                            @if($sim->assignedUser)
                                <div class="user-chip">
                                    @if($sim->assignedUser->avatar_url)
                                        <img src="{{ $sim->assignedUser->avatar_url }}" alt="" class="user-avatar">
                                    @else
                                        <div class="user-avatar">
                                            {{ strtoupper(substr($sim->assignedUser->full_name ?? 'U', 0, 1)) }}
                                        </div>
                                    @endif
                                    <div>
                                        <div class="user-name">{{ $sim->assignedUser->full_name }}</div>
                                        @if($sim->assignedUser->matricule)
                                            <div class="user-mat">{{ $sim->assignedUser->matricule }}</div>
                                        @endif
                                    </div>
                                </div>
                            @else
                                <span style="color:#d1d5db;">—</span>
                            @endif
                        </td>
                        <td style="color:#6b7280; font-size:12.5px; white-space:nowrap;">
                            {{ $sim->assigned_at ? $sim->assigned_at->format('d/m/Y') : '—' }}
                        </td>
                        <td>
                            <a href="{{ route('sims.show', $sim) }}" class="ra-btn ra-view" title="Voir les détails">
                                <i class="bi bi-eye"></i>
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ auth()->user()->isAdmin() ? 8 : 7 }}">
                            <div class="empty-state">
                                <i class="bi bi-phone"></i>
                                <p>Aucune SIM trouvée</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($sims->hasPages())
    <div class="pag-wrap d-flex justify-content-center" id="pagination-container">
        {{ $sims->appends(request()->query())->links('pagination::bootstrap-5') }}
    </div>
    @else
    <div id="pagination-container"></div>
    @endif
</div>

{{-- ── Assign modal ─────────────────────────────────── --}}
@if(auth()->user()->isAdmin())
<div class="modal fade" id="assignModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:16px; border:none; box-shadow:0 20px 60px rgba(0,0,0,0.15);">
            <div class="modal-header" style="border-bottom:1px solid #f1f5f9; padding:20px 24px;">
                <h5 class="modal-title" style="font-size:15px; font-weight:700; color:#1e293b;">
                    <i class="bi bi-person-plus" style="color:#00574A;"></i> Attribuer des SIMs
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
            </div>
            <div class="modal-body" style="padding:20px 24px;">
                <p id="assign-description" class="mb-3" style="font-size:13.5px; color:#64748b;"></p>
                <label style="font-size:13px; font-weight:600; color:#374151; margin-bottom:6px; display:block;">
                    Rechercher un collaborateur
                </label>
                <div class="fi-wrap">
                    <i class="bi bi-search fi-icon"></i>
                    <input type="text" id="assign-search" class="filter-input"
                           placeholder="Nom, matricule ou email…">
                </div>
                <div id="assign-results" style="border:1px solid #e2e8f0; border-radius:9px; max-height:200px; overflow-y:auto; margin-top:6px; display:none;"></div>
                <div id="assign-selected" style="margin-top:10px;"></div>
            </div>
            <div class="modal-footer" style="border-top:1px solid #f1f5f9; padding:16px 24px; gap:8px;">
                <button type="button" class="btn-filter btn-filter-reset" data-bs-dismiss="modal">Annuler</button>
                <button type="button" class="btn-filter btn-filter-primary" id="assign-confirm" disabled>
                    <i class="bi bi-check"></i> Attribuer
                </button>
            </div>
        </div>
    </div>
</div>

{{-- ── Import modal ──────────────────────────────────── --}}
<div class="modal fade" id="importModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content" style="border-radius:16px; border:none; box-shadow:0 20px 60px rgba(0,0,0,0.15);">
            <div class="modal-header" style="background:var(--primary); border-radius:16px 16px 0 0; padding:18px 24px;">
                <h5 class="modal-title" style="color:white; font-size:15px; font-weight:700;">
                    <i class="bi bi-upload"></i> Importer des SIMs
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fermer"></button>
            </div>
            <form method="POST" action="{{ route('sims.import-csv') }}" enctype="multipart/form-data" id="importForm">
                @csrf
                <div class="modal-body" style="padding:24px;">
                    <div class="mb-4">
                        <label style="font-size:13px; font-weight:600; color:#374151; margin-bottom:8px; display:block;">Méthode d'import</label>
                        <div class="btn-group w-100" role="group">
                            <input type="radio" class="btn-check" name="import_method" id="method_file" value="file" checked>
                            <label class="btn btn-outline-primary" for="method_file">
                                <i class="bi bi-file-earmark"></i> Fichier CSV
                            </label>
                            <input type="radio" class="btn-check" name="import_method" id="method_text" value="text">
                            <label class="btn btn-outline-primary" for="method_text">
                                <i class="bi bi-textarea-t"></i> Liste d'ICCID
                            </label>
                        </div>
                    </div>

                    <div id="file-option">
                        <label style="font-size:13px; font-weight:600; color:#374151; margin-bottom:6px; display:block;">Fichier CSV</label>
                        <input type="file" class="filter-input" id="csv_file" name="csv_file"
                               accept=".csv,.txt" style="height:auto; padding:8px 12px;">
                        <p style="font-size:12px; color:#64748b; margin-top:6px;">
                            Format : ICCID, Numéro, Opérateur, Plan, Coût (optionnel). La première ligne peut être un en-tête.
                        </p>
                    </div>

                    <div id="text-option" style="display:none;">
                        <label style="font-size:13px; font-weight:600; color:#374151; margin-bottom:6px; display:block;">Liste d'ICCID</label>
                        <textarea id="iccid_list" name="iccid_list" class="filter-input" rows="8"
                                  style="height:auto; padding:10px 12px; resize:vertical;"
                                  placeholder="Un ICCID par ligne, ou séparés par virgule / point-virgule"></textarea>
                        <p style="font-size:12px; color:#64748b; margin-top:6px;">
                            Toutes les SIMs seront créées avec le statut <strong>libre</strong> par défaut.
                        </p>
                    </div>

                    <div style="background:#eff6ff; border:1px solid #bfdbfe; border-radius:10px; padding:12px 14px; margin-top:14px; font-size:13px; color:#1d4ed8;">
                        <i class="bi bi-info-circle"></i>
                        Les ICCID déjà existants seront ignorés automatiquement.
                    </div>
                </div>
                <div class="modal-footer" style="border-top:1px solid #f1f5f9; padding:16px 24px; gap:8px;">
                    <button type="button" class="btn-filter btn-filter-reset" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn-filter btn-filter-primary">
                        <i class="bi bi-upload"></i> Importer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

@push('scripts')
<script>
const baseUrl      = '{{ route('sims.index') }}';
const storeKey     = 'sims-filter-open';

/* ── Filter panel toggle (mobile) ───────────────────── */
function toggleFilterPanel() {
    const body = document.getElementById('filter-body');
    const btn  = document.getElementById('filter-toggle-btn');
    const open = body.classList.toggle('open');
    btn.classList.toggle('open', open);
}
function initFilterPanel() {
    if (window.innerWidth >= 768) {
        document.getElementById('filter-body')?.classList.add('open');
    } else if ({{ $hasFilters ? 'true' : 'false' }}) {
        document.getElementById('filter-body')?.classList.add('open');
        document.getElementById('filter-toggle-btn')?.classList.add('open');
    }
}
window.addEventListener('resize', function() {
    if (window.innerWidth >= 768)
        document.getElementById('filter-body')?.classList.add('open');
});

/* ── Auto-submit on select change ───────────────────── */
document.getElementById('filter-status')?.addEventListener('change', () =>
    document.getElementById('filters-form').submit()
);

/* ── AJAX filter ─────────────────────────────────────── */
let filterTimer;
document.getElementById('filter-iccid')?.addEventListener('input', function() {
    clearTimeout(filterTimer);
    filterTimer = setTimeout(() => document.getElementById('filters-form').submit(), 500);
});

/* ── Bulk selection ─────────────────────────────────── */
function toggleSelectAll(cb) {
    document.querySelectorAll('.sim-checkbox').forEach(c => c.checked = cb.checked);
    updateBulkActions();
}

function updateBulkActions() {
    const selected = document.querySelectorAll('.sim-checkbox:checked');
    const count    = selected.length;
    const all      = document.querySelectorAll('.sim-checkbox');
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
    document.querySelectorAll('.sim-checkbox').forEach(c => c.checked = false);
    const sa = document.getElementById('select-all');
    if (sa) { sa.checked = false; sa.indeterminate = false; }
    updateBulkActions();
}

function attachCheckboxListeners() {
    document.querySelectorAll('.sim-checkbox').forEach(cb => {
        cb.removeEventListener('change', updateBulkActions);
        cb.addEventListener('change', updateBulkActions);
    });
    const sa = document.getElementById('select-all');
    if (sa) {
        sa.removeEventListener('change', _saH);
        sa.addEventListener('change', _saH);
    }
}
function _saH() { toggleSelectAll(this); }

function getSelectedIds() {
    return Array.from(document.querySelectorAll('.sim-checkbox:checked')).map(c => c.value);
}

/* ── Assign modal ────────────────────────────────────── */
let assignModal   = null;
let selectedUserId = null;
let assignTimer;

function openAssignModal() {
    const ids = getSelectedIds();
    if (!ids.length) return;
    selectedUserId = null;
    const desc = document.getElementById('assign-description');
    if (desc) desc.textContent = `Attribuer ${ids.length} SIM(s) à :`;
    document.getElementById('assign-search').value = '';
    document.getElementById('assign-results').innerHTML = '';
    document.getElementById('assign-results').style.display = 'none';
    document.getElementById('assign-selected').innerHTML = '';
    document.getElementById('assign-confirm').disabled = true;
    assignModal?.show();
}

document.getElementById('assign-search')?.addEventListener('input', function() {
    clearTimeout(assignTimer);
    const q = this.value.trim();
    const res = document.getElementById('assign-results');
    if (q.length < 2) { res.style.display = 'none'; res.innerHTML = ''; return; }
    assignTimer = setTimeout(() => {
        fetch(`{{ route('search') }}?q=${encodeURIComponent(q)}&type=users`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
        })
        .then(r => r.json())
        .then(data => renderAssignResults(data.users || []))
        .catch(() => {});
    }, 300);
});

function renderAssignResults(users) {
    const el = document.getElementById('assign-results');
    if (!users.length) {
        el.innerHTML = '<div style="padding:12px;text-align:center;color:#94a3b8;font-size:13px;">Aucun résultat</div>';
        el.style.display = 'block';
        return;
    }
    el.innerHTML = users.map(u => `
        <div class="assign-result-item" onclick="selectAssignUser(${u.id},'${(u.name||'').replace(/'/g,"\\'")}','${(u.matricule||'').replace(/'/g,"\\'")}')">
            <span style="width:8px;height:8px;border-radius:50%;background:#00574A;flex-shrink:0;"></span>
            <span class="ari-name">${u.name || ''}</span>
            <span class="ari-mat">${u.matricule || ''}</span>
        </div>`).join('');
    el.style.display = 'block';
}

function selectAssignUser(id, name, matricule) {
    selectedUserId = id;
    document.getElementById('assign-results').style.display = 'none';
    document.getElementById('assign-search').value = '';
    document.getElementById('assign-selected').innerHTML = `
        <div class="assign-selected-card">
            <i class="bi bi-check-circle-fill"></i>
            <div>
                <div style="font-weight:600; font-size:13px; color:#1e293b;">${name}</div>
                ${matricule ? `<div style="font-size:11px;color:#94a3b8;">${matricule}</div>` : ''}
            </div>
        </div>`;
    document.getElementById('assign-confirm').disabled = false;
}

document.getElementById('assign-confirm')?.addEventListener('click', function() {
    const ids = getSelectedIds();
    if (!ids.length || !selectedUserId) return;
    this.disabled = true;
    this.innerHTML = '<span class="loading-spinner"></span> Attribution…';

    fetch('{{ route('sims.bulk-assign') }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
        },
        body: JSON.stringify({ ids, user_id: selectedUserId })
    })
    .then(r => r.json())
    .then(data => {
        assignModal?.hide();
        if (data.success) {
            if (window.showToast) showToast(data.message || 'SIMs attribuées avec succès', 'success');
            clearSelection();
            setTimeout(() => window.location.reload(), 900);
        } else {
            if (window.showToast) showToast(data.message || 'Une erreur est survenue', 'error');
            else alert(data.message || 'Une erreur est survenue.');
        }
    })
    .catch(() => {
        assignModal?.hide();
        if (window.showToast) showToast("Erreur lors de l'attribution", 'error');
    });
});

/* ── Bulk release ────────────────────────────────────── */
function confirmBulkRelease() {
    const ids = getSelectedIds();
    if (!ids.length) return;
    window.showConfirmModal(
        `Libérer ${ids.length} SIM(s) ?`,
        () => executeBulkRelease(ids),
        { confirmText: 'Libérer', confirmVariant: 'danger', title: 'Confirmer la libération' }
    );
}

function executeBulkRelease(ids) {
    fetch('{{ route('sims.bulk-unassign') }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
        },
        body: JSON.stringify({ ids })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            if (window.showToast) showToast(data.message || 'SIMs libérées avec succès', 'success');
            clearSelection();
            setTimeout(() => window.location.reload(), 900);
        } else {
            if (window.showToast) showToast(data.message || 'Une erreur est survenue', 'error');
            else alert(data.message || 'Une erreur est survenue.');
        }
    })
    .catch(() => {
        if (window.showToast) showToast("Erreur lors de la libération", 'error');
    });
}

/* ── Bulk export ─────────────────────────────────────── */
function bulkExport() {
    const ids = getSelectedIds();
    if (!ids.length) return;
    const p = new URLSearchParams();
    ids.forEach(id => p.append('ids[]', id));
    window.location.href = '{{ route('sims.export') }}?format=excel&' + p.toString();
}

/* ── AJAX pagination ────────────────────────────────── */
document.addEventListener('click', function(e) {
    const link = e.target.closest('.pagination a');
    if (!link) return;
    e.preventDefault();
    const tbody = document.getElementById('table-body');
    tbody.innerHTML = `<tr><td colspan="8" class="text-center" style="padding:40px;color:#94a3b8;">
        <i class="bi bi-arrow-repeat spin"></i> Chargement…</td></tr>`;
    fetch(link.href, { headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'text/html' } })
    .then(r => r.text())
    .then(html => {
        const doc = new DOMParser().parseFromString(html, 'text/html');
        const nb  = doc.getElementById('table-body');
        const np  = doc.getElementById('pagination-container');
        if (nb) { tbody.innerHTML = nb.innerHTML; clearSelection(); attachCheckboxListeners(); }
        if (np) document.getElementById('pagination-container').innerHTML = np.innerHTML;
        window.scrollTo({ top: 0, behavior: 'smooth' });
    })
    .catch(() => window.location.href = link.href);
});

/* ── Import modal ────────────────────────────────────── */
const methodFile = document.getElementById('method_file');
const methodText = document.getElementById('method_text');
const fileOpt    = document.getElementById('file-option');
const textOpt    = document.getElementById('text-option');
const csvFile    = document.getElementById('csv_file');
const iccidList  = document.getElementById('iccid_list');

function toggleImportMethod() {
    const isFile = methodFile?.checked;
    if (fileOpt) fileOpt.style.display = isFile ? 'block' : 'none';
    if (textOpt) textOpt.style.display = isFile ? 'none'  : 'block';
    if (csvFile)   csvFile.required   = !!isFile;
    if (iccidList) iccidList.required = !isFile;
    if (isFile && iccidList) iccidList.value = '';
    if (!isFile && csvFile)  csvFile.value   = '';
}

methodFile?.addEventListener('change', toggleImportMethod);
methodText?.addEventListener('change', toggleImportMethod);

document.getElementById('importForm')?.addEventListener('submit', function(e) {
    if (methodFile?.checked && !csvFile?.files.length) {
        e.preventDefault();
        if (window.showToast) showToast('Veuillez sélectionner un fichier CSV.', 'error');
        return;
    }
    if (methodText?.checked && !iccidList?.value.trim()) {
        e.preventDefault();
        if (window.showToast) showToast('Veuillez entrer au moins un ICCID.', 'error');
    }
});

/* ── Init ────────────────────────────────────────────── */
document.addEventListener('DOMContentLoaded', function() {
    initFilterPanel();
    attachCheckboxListeners();

    const assignEl = document.getElementById('assignModal');
    if (assignEl && typeof bootstrap !== 'undefined') {
        assignModal = new bootstrap.Modal(assignEl);
    }
});
</script>
@endpush
@endsection
