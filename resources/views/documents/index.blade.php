@extends('layouts.bootstrap')

@section('title', 'Documents - Bordereaux de Transmission')
@section('page-title', 'Bordereaux de Transmission')

@push('styles')
<style>
    /* ── Stat cards ─────────────────────────────────────────── */
    .doc-stats { display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; margin-bottom: 20px; }
    @media (max-width: 767px) { .doc-stats { grid-template-columns: 1fr; } }

    .doc-stat {
        background: white;
        border: 1px solid var(--border);
        border-radius: 14px;
        padding: 18px 20px;
        display: flex; align-items: center; justify-content: space-between;
        box-shadow: 0 1px 4px rgba(0,0,0,0.04);
    }
    .doc-stat-label { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em; color: var(--muted); margin-bottom: 4px; }
    .doc-stat-value { font-size: 24px; font-weight: 700; line-height: 1; }
    .doc-stat-icon { font-size: 28px; opacity: 0.18; }

    /* ── Main card ──────────────────────────────────────────── */
    .doc-card {
        background: white;
        border: 1px solid var(--border);
        border-radius: 14px;
        box-shadow: 0 1px 4px rgba(0,0,0,0.05);
        overflow: hidden;
    }
    .doc-card-body { padding: 20px; }

    /* ── Filters ────────────────────────────────────────────── */
    .doc-filters {
        display: flex; flex-wrap: wrap; gap: 10px;
        align-items: flex-end;
        margin-bottom: 20px;
        padding-bottom: 20px;
        border-bottom: 1px solid var(--border);
    }
    .doc-filter-group { display: flex; flex-direction: column; gap: 5px; }
    .doc-filter-label { font-size: 12px; font-weight: 600; color: var(--muted); }
    .doc-filter-input {
        height: 38px;
        border: 1.5px solid var(--border);
        border-radius: 9px;
        padding: 0 12px;
        font-size: 13.5px;
        font-family: 'Poppins', sans-serif;
        color: var(--text);
        background: white;
        outline: none;
        transition: border-color 0.2s, box-shadow 0.2s;
        min-width: 0;
    }
    .doc-filter-input:focus { border-color: var(--primary); box-shadow: 0 0 0 3px var(--primary-glow); }

    .btn-doc-search {
        height: 38px; padding: 0 16px;
        background: var(--primary); color: white;
        border: none; border-radius: 9px;
        font-size: 13.5px; font-weight: 600; font-family: 'Poppins', sans-serif;
        display: inline-flex; align-items: center; gap: 6px;
        cursor: pointer; transition: background 0.18s;
        white-space: nowrap;
    }
    .btn-doc-search:hover { background: var(--primary-dark); }

    .btn-doc-reset {
        height: 38px; padding: 0 14px;
        background: white; color: var(--muted);
        border: 1.5px solid var(--border); border-radius: 9px;
        font-size: 13.5px; font-family: 'Poppins', sans-serif;
        display: inline-flex; align-items: center; gap: 5px;
        cursor: pointer; text-decoration: none;
        transition: border-color 0.18s, color 0.18s;
        white-space: nowrap;
    }
    .btn-doc-reset:hover { border-color: #ef4444; color: #ef4444; }

    /* ── Table ──────────────────────────────────────────────── */
    .doc-table { width: 100%; border-collapse: collapse; }
    .doc-table th {
        padding: 11px 16px;
        font-size: 11px; font-weight: 700;
        text-transform: uppercase; letter-spacing: 0.05em;
        color: var(--muted); background: #f8fafc;
        border-bottom: 1px solid var(--border);
        text-align: left; white-space: nowrap;
    }
    .doc-table td {
        padding: 12px 16px;
        font-size: 13px; color: var(--text);
        border-bottom: 1px solid #f1f5f9;
        vertical-align: middle;
    }
    .doc-table tbody tr:last-child td { border-bottom: none; }
    .doc-table tbody tr:hover td { background: #f8fafc; }

    .doc-type-badge {
        display: inline-block;
        padding: 3px 10px;
        border-radius: 20px;
        font-size: 12px; font-weight: 600;
    }

    .doc-view-btn {
        display: inline-flex; align-items: center; gap: 5px;
        padding: 5px 12px;
        background: var(--primary); color: white;
        border: none; border-radius: 8px;
        font-size: 12.5px; font-weight: 600;
        text-decoration: none;
        transition: background 0.18s;
        white-space: nowrap;
    }
    .doc-view-btn:hover { background: var(--primary-dark); color: white; }

    /* ── Empty state ────────────────────────────────────────── */
    .doc-empty { padding: 48px; text-align: center; color: var(--muted); }
    .doc-empty i { font-size: 42px; opacity: 0.3; display: block; margin-bottom: 12px; }
</style>
@endpush

@section('content')

{{-- Stat cards --}}
<div class="doc-stats">
    <div class="doc-stat">
        <div>
            <div class="doc-stat-label">Total Bordereaux</div>
            <div class="doc-stat-value" style="color:var(--primary);">{{ $stats['total'] }}</div>
        </div>
        <i class="bi bi-file-earmark-pdf doc-stat-icon" style="color:var(--primary);"></i>
    </div>
    <div class="doc-stat">
        <div>
            <div class="doc-stat-label">Ce mois</div>
            <div class="doc-stat-value" style="color:#3b82f6;">{{ $stats['this_month'] }}</div>
        </div>
        <i class="bi bi-calendar-month doc-stat-icon" style="color:#3b82f6;"></i>
    </div>
    <div class="doc-stat">
        <div>
            <div class="doc-stat-label">Cette semaine</div>
            <div class="doc-stat-value" style="color:#10b981;">{{ $stats['this_week'] }}</div>
        </div>
        <i class="bi bi-calendar-week doc-stat-icon" style="color:#10b981;"></i>
    </div>
</div>

{{-- Main card --}}
<div class="doc-card">
    <div class="doc-card-body">

        {{-- Filters --}}
        <form method="GET" action="{{ route('documents.index') }}" id="filters-form">
            <div class="doc-filters">
                <div class="doc-filter-group">
                    <label class="doc-filter-label">Type de demande</label>
                    <select name="request_type" class="doc-filter-input" style="min-width:160px;">
                        <option value="">Tous les types</option>
                        <option value="recuperation"  {{ request('request_type') === 'recuperation'  ? 'selected' : '' }}>Récupération</option>
                        <option value="creation"      {{ request('request_type') === 'creation'      ? 'selected' : '' }}>Création</option>
                        <option value="suspension"    {{ request('request_type') === 'suspension'    ? 'selected' : '' }}>Suspension</option>
                        <option value="desactivation" {{ request('request_type') === 'desactivation' ? 'selected' : '' }}>Désactivation</option>
                        <option value="ajustement"    {{ request('request_type') === 'ajustement'    ? 'selected' : '' }}>Ajustement</option>
                    </select>
                </div>
                <div class="doc-filter-group">
                    <label class="doc-filter-label">Date début</label>
                    <input type="date" name="date_from" class="doc-filter-input" value="{{ request('date_from') }}">
                </div>
                <div class="doc-filter-group">
                    <label class="doc-filter-label">Date fin</label>
                    <input type="date" name="date_to" class="doc-filter-input" value="{{ request('date_to') }}">
                </div>
                <div class="doc-filter-group" style="flex:1;min-width:200px;">
                    <label class="doc-filter-label">Recherche</label>
                    <input type="text" name="search" class="doc-filter-input" value="{{ request('search') }}" placeholder="N° demande, ICCID, téléphone...">
                </div>
                <div class="d-flex align-items-end gap-2">
                    <button type="submit" class="btn-doc-search">
                        <i class="bi bi-search"></i> Rechercher
                    </button>
                    @if(request()->anyFilled(['request_type', 'date_from', 'date_to', 'search']))
                        <a href="{{ route('documents.index') }}" class="btn-doc-reset">
                            <i class="bi bi-x-circle"></i> Réinitialiser
                        </a>
                    @endif
                </div>
            </div>
        </form>

        {{-- Table --}}
        @php
            $typeLabels = [
                'recuperation' => 'Récupération', 'creation' => 'Création',
                'suspension' => 'Suspension', 'desactivation' => 'Désactivation',
                'ajustement' => 'Ajustement',
            ];
            $typeStyles = [
                'recuperation' => 'background:#fef3c7;color:#92400e;',
                'creation'     => 'background:#d1fae5;color:#065f46;',
                'suspension'   => 'background:#dbeafe;color:#1e40af;',
                'desactivation'=> 'background:#fee2e2;color:#991b1b;',
                'ajustement'   => 'background:#e9d5ff;color:#6b21a8;',
            ];
        @endphp

        <div style="overflow-x:auto;">
            <table class="doc-table">
                <thead>
                    <tr>
                        <th>N° Demande</th>
                        <th>Type</th>
                        <th>Demandeur</th>
                        <th>Collaborateur</th>
                        <th>Ligne</th>
                        <th>ICCID</th>
                        <th>Date validation</th>
                        <th style="width:1%;text-align:center;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($bordereaux as $request)
                        @php
                            $requester = $request->creator ?? $request->user;
                            if ($request->isCreation()) {
                                $collabName = trim(($request->beneficiary_name ?? '') . ' ' . ($request->beneficiary_first_name ?? '')) ?: null;
                                $collabMat  = $request->beneficiary_matricule;
                            } else {
                                $collabName = trim(($request->collaborator_name ?? '') . ' ' . ($request->collaborator_first_name ?? '')) ?: null;
                                $collabMat  = $request->collaborator_matricule;
                                if (!$collabName && !$collabMat) {
                                    $collabName = $request->user->full_name ?? null;
                                    $collabMat  = $request->user->matricule ?? null;
                                }
                            }
                            $lineNum  = $request->phone_number ?? ($request->sim?->phone_number ?? null);
                            $iccidVal = $request->sim?->iccid ?? ($request->requested_iccid ?? null);
                            $style    = $typeStyles[$request->request_type] ?? 'background:#f3f4f6;color:#374151;';
                        @endphp
                        <tr>
                            <td style="font-weight:700;">{{ $request->request_number }}</td>
                            <td>
                                <span class="doc-type-badge" style="{{ $style }}">
                                    {{ $typeLabels[$request->request_type] ?? ucfirst($request->request_type) }}
                                </span>
                            </td>
                            <td>
                                @if($requester)
                                    {{ $requester->full_name }}
                                    @if($requester->matricule)
                                        <br><small style="color:var(--muted);">{{ $requester->matricule }}</small>
                                    @endif
                                @else
                                    <span style="color:var(--muted);">—</span>
                                @endif
                            </td>
                            <td>
                                @if($collabName)
                                    {{ $collabName }}
                                    @if($collabMat)
                                        <br><small style="color:var(--muted);">{{ $collabMat }}</small>
                                    @endif
                                @elseif($collabMat)
                                    {{ $collabMat }}
                                @else
                                    <span style="color:var(--muted);">—</span>
                                @endif
                            </td>
                            <td>{{ $lineNum ?? '—' }}</td>
                            <td>{{ $iccidVal ?? '—' }}</td>
                            <td style="color:var(--muted);">
                                @if($request->validated_at)
                                    {{ $request->validated_at->format('d/m/Y H:i') }}
                                @elseif($request->admin_processed_at)
                                    {{ $request->admin_processed_at->format('d/m/Y H:i') }}
                                @else
                                    —
                                @endif
                            </td>
                            <td style="text-align:center;white-space:nowrap;">
                                <a href="{{ route('documents.bordereau', $request) }}"
                                   class="doc-view-btn" target="_blank">
                                    <i class="bi bi-file-earmark-pdf"></i> Voir BT
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8">
                                <div class="doc-empty">
                                    <i class="bi bi-inbox"></i>
                                    Aucun bordereau trouvé
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($bordereaux->hasPages())
            <div class="mt-4">
                {{ $bordereaux->links('pagination::bootstrap-5') }}
            </div>
        @endif

    </div>
</div>
@endsection
