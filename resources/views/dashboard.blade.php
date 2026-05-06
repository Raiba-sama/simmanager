@extends('layouts.bootstrap')

@section('title', 'Tableau de bord — SIM Manager')
@section('page-title', 'Tableau de bord')

@if($isValidator)
@section('page-actions')
<div class="period-tabs">
    <button class="period-tab {{ $period === 'day'   ? 'active' : '' }}" data-period="day"   onclick="changePeriod('day',   this)">Aujourd'hui</button>
    <button class="period-tab {{ $period === 'week'  ? 'active' : '' }}" data-period="week"  onclick="changePeriod('week',  this)">Cette semaine</button>
    <button class="period-tab {{ $period === 'month' ? 'active' : '' }}" data-period="month" onclick="changePeriod('month', this)">Ce mois</button>
    <button class="period-tab {{ $period === 'year'  ? 'active' : '' }}" data-period="year"  onclick="changePeriod('year',  this)">Cette année</button>
</div>
@endsection
@endif

@push('styles')
<style>
/* ── Period tabs ─────────────────────────────────────── */
.period-tabs {
    display: flex;
    background: #f1f5f9;
    border-radius: 10px;
    padding: 3px;
    gap: 2px;
}
.period-tab {
    padding: 7px 14px;
    border: none; background: transparent;
    border-radius: 8px; font-size: 12.5px; font-weight: 500;
    color: #64748b; cursor: pointer;
    transition: all 0.18s; font-family: 'Poppins', sans-serif;
    white-space: nowrap;
}
.period-tab:hover { color: #1e293b; }
.period-tab.active {
    background: white; color: #00574A; font-weight: 600;
    box-shadow: 0 1px 4px rgba(0,0,0,0.10);
}

/* ── Stat cards ──────────────────────────────────────── */
.stat-card {
    background: white;
    border: 1px solid #e2e8f0;
    border-radius: 16px;
    padding: 22px 22px;
    display: flex; align-items: center; gap: 18px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.05);
    transition: box-shadow 0.22s, transform 0.22s, border-color 0.22s;
    height: 100%;
}
.stat-card:hover {
    box-shadow: 0 6px 20px rgba(0,0,0,0.09);
    border-color: #cbd5e1; transform: translateY(-2px);
}
.stat-icon {
    width: 52px; height: 52px; border-radius: 14px;
    display: flex; align-items: center; justify-content: center;
    font-size: 22px; flex-shrink: 0;
}
.si-blue    { background: #dbeafe; color: #3b82f6; }
.si-cyan    { background: #cffafe; color: #06b6d4; }
.si-amber   { background: #fef3c7; color: #d97706; }
.si-green   { background: #d1fae5; color: #059669; }
.si-primary { background: rgba(0,87,74,0.10); color: #00574A; }
.si-red     { background: #fee2e2; color: #dc2626; }
.si-slate   { background: #f1f5f9; color: #64748b; }
.si-purple  { background: #f3e8ff; color: #7c3aed; }

.stat-value {
    font-size: 30px; font-weight: 800; color: #0f172a;
    line-height: 1; letter-spacing: -1px; margin-bottom: 5px;
}
.stat-label {
    font-size: 11.5px; font-weight: 600; color: #64748b;
    text-transform: uppercase; letter-spacing: 0.05em;
}

/* ── Dash card ───────────────────────────────────────── */
.dash-card {
    background: white;
    border: 1px solid #e2e8f0;
    border-radius: 14px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.05);
    overflow: hidden;
}
.dash-card-header {
    padding: 16px 20px;
    border-bottom: 1px solid #f1f5f9;
    display: flex; align-items: center; justify-content: space-between; gap: 12px;
}
.dash-card-title {
    display: flex; align-items: center; gap: 10px;
    font-size: 14.5px; font-weight: 700; color: #0f172a; margin: 0;
}
.dct-icon {
    width: 30px; height: 30px; border-radius: 8px;
    display: flex; align-items: center; justify-content: center;
    font-size: 14px;
}
.dash-card-body  { padding: 20px; }
.dash-card-body-0 { padding: 0; }

/* ── Admin sync banner ───────────────────────────────── */
.sync-banner {
    background: linear-gradient(135deg, rgba(0,87,74,0.05) 0%, rgba(0,87,74,0.08) 100%);
    border: 1px solid rgba(0,87,74,0.14);
    border-radius: 14px;
    padding: 18px 22px;
    display: flex; align-items: center; gap: 16px; flex-wrap: wrap;
}
.sync-banner-icon {
    width: 44px; height: 44px;
    background: rgba(0,87,74,0.12); border-radius: 12px;
    display: flex; align-items: center; justify-content: center;
    font-size: 20px; color: #00574A; flex-shrink: 0;
}
.sync-banner-title {
    font-weight: 700; font-size: 14px; color: #0f172a; margin-bottom: 3px;
}
.sync-banner-desc { font-size: 12.5px; color: #64748b; margin: 0; }

/* ── Mini stats (advanced) ───────────────────────────── */
.mini-stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
    gap: 10px;
    margin-bottom: 24px;
}
.mini-stat {
    padding: 14px 12px;
    background: #f8fafc; border: 1px solid #e2e8f0;
    border-radius: 12px; text-align: center;
    transition: box-shadow 0.18s;
}
.mini-stat:hover { box-shadow: 0 2px 8px rgba(0,0,0,0.06); }
.mini-stat-value {
    font-size: 26px; font-weight: 800; color: #0f172a;
    letter-spacing: -0.5px; line-height: 1; margin-bottom: 5px;
}
.mini-stat-label { font-size: 11px; color: #64748b; font-weight: 500; }

.ms-green   { background: #ecfdf5; border-color: #a7f3d0; }
.ms-green   .mini-stat-value { color: #059669; }
.ms-red     { background: #fef2f2; border-color: #fecaca; }
.ms-red     .mini-stat-value { color: #dc2626; }
.ms-amber   { background: #fffbeb; border-color: #fde68a; }
.ms-amber   .mini-stat-value { color: #d97706; }
.ms-cyan    { background: #ecfeff; border-color: #a5f3fc; }
.ms-cyan    .mini-stat-value { color: #0891b2; }
.ms-primary { background: #f0fdf9; border-color: #6ee7b7; }
.ms-primary .mini-stat-value { color: #00574A; }

/* ── Status & type badges ────────────────────────────── */
.sbadge {
    display: inline-flex; align-items: center; gap: 5px;
    padding: 4px 10px; border-radius: 20px;
    font-size: 12px; font-weight: 600; white-space: nowrap;
}
.sdot { width: 6px; height: 6px; border-radius: 50%; flex-shrink: 0; }

.sb-amber   { background: #fef3c7; color: #92400e; } .sb-amber   .sdot { background: #f59e0b; }
.sb-green   { background: #d1fae5; color: #065f46; } .sb-green   .sdot { background: #10b981; }
.sb-red     { background: #fee2e2; color: #991b1b; } .sb-red     .sdot { background: #ef4444; }
.sb-cyan    { background: #cffafe; color: #164e63; } .sb-cyan    .sdot { background: #06b6d4; }
.sb-slate   { background: #f1f5f9; color: #475569; } .sb-slate   .sdot { background: #94a3b8; }
.sb-blue    { background: #dbeafe; color: #1e40af; } .sb-blue    .sdot { background: #3b82f6; }
.sb-emerald { background: #d1fae5; color: #065f46; } .sb-emerald .sdot { background: #10b981; }
.sb-orange  { background: #ffedd5; color: #9a3412; } .sb-orange  .sdot { background: #f97316; }
.sb-rose    { background: #ffe4e6; color: #9f1239; } .sb-rose    .sdot { background: #f43f5e; }
.sb-purple  { background: #f3e8ff; color: #6b21a8; } .sb-purple  .sdot { background: #a855f7; }

/* ── Dashboard table ─────────────────────────────────── */
.dtable { margin: 0; }
.dtable thead tr { background: #f8fafc; }
.dtable thead th {
    padding: 12px 16px;
    font-size: 10.5px; font-weight: 700;
    text-transform: uppercase; letter-spacing: 0.06em;
    color: #64748b; white-space: nowrap;
    border-top: none; border-bottom: 2px solid #e2e8f0 !important;
}
.dtable tbody td {
    padding: 14px 16px;
    border-color: #f1f5f9;
    vertical-align: middle; color: #374151;
}
.dtable tbody tr:last-child td { border-bottom: none; }
.dtable tbody tr:hover td { background: #f8fafc; }

/* ── Sub-section divider ─────────────────────────────── */
.sub-title {
    font-size: 13.5px; font-weight: 700; color: #0f172a;
    margin: 20px 0 14px; padding-bottom: 10px;
    border-bottom: 1px solid #f1f5f9;
}

/* ── Quick links ─────────────────────────────────────── */
.qlink {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 7px 14px; border-radius: 10px;
    font-size: 13px; font-weight: 500;
    text-decoration: none; transition: all 0.18s;
    border: 1.5px solid;
}
.qlink-primary { background: #00574A; color: white; border-color: #00574A; }
.qlink-primary:hover { background: #004a3f; color: white; transform: translateY(-1px); }
.qlink-outline { background: white; color: #475569; border-color: #e2e8f0; }
.qlink-outline:hover { border-color: #00574A; color: #00574A; background: rgba(0,87,74,0.04); }
.qlink-amber  { background: #fffbeb; color: #d97706; border-color: #fde68a; }
.qlink-amber:hover { background: #fef3c7; }

/* ── Chart empty state ───────────────────────────────── */
.chart-empty {
    height: 100%; display: flex; flex-direction: column;
    align-items: center; justify-content: center;
    color: #94a3b8; text-align: center; padding: 24px;
}
.chart-empty i { font-size: 36px; margin-bottom: 10px; opacity: 0.4; }
.chart-empty p { font-size: 13px; margin: 0; }

/* ── Rank badge ──────────────────────────────────────── */
.rank-badge {
    display: inline-flex; align-items: center; justify-content: center;
    width: 24px; height: 24px; border-radius: 6px;
    font-size: 11px; font-weight: 700;
    background: #f1f5f9; color: #64748b;
}
.rank-badge.rank-1 { background: #fef3c7; color: #d97706; }
.rank-badge.rank-2 { background: #f1f5f9; color: #64748b; }
.rank-badge.rank-3 { background: #ffedd5; color: #c2410c; }

/* ── Responsive ──────────────────────────────────────── */
@media (max-width: 768px) {
    .stat-card  { padding: 16px; }
    .stat-value { font-size: 24px; }
    .stat-icon  { width: 44px; height: 44px; font-size: 18px; }
    .period-tabs { flex-wrap: wrap; }
    .mini-stats-grid { grid-template-columns: repeat(2, 1fr); }
}
</style>
@endpush

@section('content')
@php
    $user = auth()->user();

    $typeLabels = [
        'recuperation'  => 'Récupération',
        'creation'      => 'Création',
        'suspension'    => 'Suspension',
        'desactivation' => 'Désactivation',
        'ajustement'    => 'Ajustement',
    ];
    $typeBadge = [
        'recuperation'  => 'sb-blue',
        'creation'      => 'sb-emerald',
        'suspension'    => 'sb-orange',
        'desactivation' => 'sb-rose',
        'ajustement'    => 'sb-purple',
    ];
    $statusLabels = [
        'en_attente'      => 'En attente',
        'validee'         => 'Validée',
        'rejetee'         => 'Rejetée',
        'demande_envoyee' => 'Envoyée',
        'pending'         => 'Pending',
        'accepted'        => 'Acceptée',
        'refused'         => 'Refusée',
    ];
    $statusBadge = [
        'en_attente'      => 'sb-amber',
        'validee'         => 'sb-green',
        'rejetee'         => 'sb-red',
        'demande_envoyee' => 'sb-cyan',
        'pending'         => 'sb-cyan',
        'accepted'        => 'sb-green',
        'refused'         => 'sb-red',
    ];
@endphp

{{-- ═══ Admin sync banner ════════════════════════════════════ --}}
@if($user->isAdmin())
<div class="sync-banner mb-4">
    <div class="sync-banner-icon">
        <i class="bi bi-cloud-arrow-down"></i>
    </div>
    <div style="flex:1;min-width:0;">
        <div class="sync-banner-title">Synchronisation utilisateurs</div>
        <p class="sync-banner-desc">Récupérer les utilisateurs depuis l'application tierce. Les nouveaux comptes (matricule inexistant) seront créés avec le rôle fourni.</p>
    </div>
    <form method="POST" action="{{ route('admin.users.sync-from-webhook') }}" style="margin:0;"
          data-confirm="Synchroniser les utilisateurs depuis le webhook ?" data-confirm-variant="primary" data-confirm-text="Synchroniser">
        @csrf
        <button type="submit" class="btn btn-primary btn-sm">
            <i class="bi bi-arrow-repeat me-1"></i> Synchroniser
        </button>
    </form>
</div>
@endif

{{-- ═══ Stats cards ═══════════════════════════════════════════ --}}
<div class="row g-3 mb-4">
    @if($isValidator)
        <div class="col-6 col-md-3">
            <div class="stat-card">
                <div class="stat-icon si-blue"><i class="bi bi-phone"></i></div>
                <div>
                    <div class="stat-value">{{ $stats['sims_libres'] }}</div>
                    <div class="stat-label">SIMs libres</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card">
                <div class="stat-icon si-cyan"><i class="bi bi-check-circle"></i></div>
                <div>
                    <div class="stat-value">{{ $stats['sims_attribuees'] }}</div>
                    <div class="stat-label">SIMs attribuées</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card">
                <div class="stat-icon si-amber"><i class="bi bi-clock-history"></i></div>
                <div>
                    <div class="stat-value">{{ $stats['demandes_en_attente'] }}</div>
                    <div class="stat-label">En attente</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card">
                <div class="stat-icon si-green"><i class="bi bi-people"></i></div>
                <div>
                    <div class="stat-value">{{ $stats['total_utilisateurs'] }}</div>
                    <div class="stat-label">Utilisateurs</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-4">
            <div class="stat-card">
                <div class="stat-icon si-primary"><i class="bi bi-database"></i></div>
                <div>
                    <div class="stat-value">{{ $stats['total_sims'] ?? 0 }}</div>
                    <div class="stat-label">Total SIMs</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-4">
            <div class="stat-card">
                <div class="stat-icon si-cyan"><i class="bi bi-send-check"></i></div>
                <div>
                    <div class="stat-value">{{ $stats['demandes_envoyees'] ?? 0 }}</div>
                    <div class="stat-label">Demandes envoyées</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-4">
            <div class="stat-card">
                <div class="stat-icon si-amber"><i class="bi bi-pause-circle"></i></div>
                <div>
                    <div class="stat-value">{{ $stats['sims_suspendues'] ?? 0 }}</div>
                    <div class="stat-label">SIMs suspendues</div>
                </div>
            </div>
        </div>
    @else
        {{-- User normal --}}
        <div class="col-12 col-md-4">
            <div class="stat-card">
                <div class="stat-icon si-primary"><i class="bi bi-phone"></i></div>
                <div>
                    <div class="stat-value">{{ $stats['mes_sims'] }}</div>
                    <div class="stat-label">Ma SIM</div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-4">
            <div class="stat-card">
                <div class="stat-icon si-amber"><i class="bi bi-clock-history"></i></div>
                <div>
                    <div class="stat-value">{{ $stats['mes_demandes_en_attente'] }}</div>
                    <div class="stat-label">En attente</div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-4">
            <div class="stat-card">
                <div class="stat-icon si-green"><i class="bi bi-check-circle"></i></div>
                <div>
                    <div class="stat-value">{{ $stats['mes_demandes_validees'] }}</div>
                    <div class="stat-label">Demandes validées</div>
                </div>
            </div>
        </div>
    @endif
</div>

{{-- ═══ Charts (validators only) ══════════════════════════════ --}}
@if($isValidator)
<div class="row g-3 mb-4">

    {{-- Evolution des demandes --}}
    <div class="col-md-8">
        <div class="dash-card h-100">
            <div class="dash-card-header">
                <h5 class="dash-card-title">
                    <span class="dct-icon si-primary"><i class="bi bi-graph-up"></i></span>
                    Évolution des demandes
                </h5>
            </div>
            <div class="dash-card-body">
                <div style="position:relative;height:280px;" id="wrap-evolution">
                    <canvas id="requestsEvolutionChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- Par type --}}
    <div class="col-md-4">
        <div class="dash-card h-100">
            <div class="dash-card-header">
                <h5 class="dash-card-title">
                    <span class="dct-icon si-purple"><i class="bi bi-pie-chart"></i></span>
                    Par type
                </h5>
            </div>
            <div class="dash-card-body">
                <div style="position:relative;height:280px;" id="wrap-type">
                    <canvas id="requestsByTypeChart"></canvas>
                </div>
            </div>
        </div>
    </div>

</div>

<div class="row g-3 mb-4">

    {{-- Par statut --}}
    <div class="col-md-6">
        <div class="dash-card h-100">
            <div class="dash-card-header">
                <h5 class="dash-card-title">
                    <span class="dct-icon si-blue"><i class="bi bi-bar-chart"></i></span>
                    Demandes par statut
                </h5>
            </div>
            <div class="dash-card-body">
                <div style="position:relative;height:260px;">
                    <canvas id="requestsByStatusChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- SIMs par statut --}}
    <div class="col-md-6">
        <div class="dash-card h-100">
            <div class="dash-card-header">
                <h5 class="dash-card-title">
                    <span class="dct-icon si-cyan"><i class="bi bi-phone"></i></span>
                    SIMs par statut
                </h5>
            </div>
            <div class="dash-card-body">
                <div style="position:relative;height:260px;">
                    <canvas id="simsByStatusChart"></canvas>
                </div>
            </div>
        </div>
    </div>

</div>

{{-- SIMs créées (commandes opérateur) --}}
<div class="dash-card mb-4">
    <div class="dash-card-header">
        <h5 class="dash-card-title">
            <span class="dct-icon si-green"><i class="bi bi-graph-up-arrow"></i></span>
            Évolution des SIMs créées — Commandes opérateur
        </h5>
    </div>
    <div class="dash-card-body">
        <div style="position:relative;height:260px;" id="wrap-sims-created">
            <canvas id="simsCreatedEvolutionChart"></canvas>
        </div>
    </div>
</div>
@endif

{{-- ═══ Advanced stats (validators only) ══════════════════════ --}}
@if($isValidator)
<div class="dash-card mb-4">
    <div class="dash-card-header">
        <h5 class="dash-card-title">
            <span class="dct-icon si-amber"><i class="bi bi-speedometer2"></i></span>
            Statistiques avancées <span style="font-size:12px;font-weight:500;color:#94a3b8;margin-left:8px;">— période sélectionnée</span>
        </h5>
    </div>
    <div class="dash-card-body">

        {{-- Mini stats grid --}}
        <div class="mini-stats-grid">
            <div class="mini-stat">
                <div class="mini-stat-value">{{ $advancedStats['total_requests'] ?? 0 }}</div>
                <div class="mini-stat-label">Total demandes</div>
            </div>
            <div class="mini-stat ms-green">
                <div class="mini-stat-value">{{ $advancedStats['validated_requests'] ?? 0 }}</div>
                <div class="mini-stat-label">Validées</div>
            </div>
            <div class="mini-stat ms-red">
                <div class="mini-stat-value">{{ $advancedStats['rejected_requests'] ?? 0 }}</div>
                <div class="mini-stat-label">Rejetées</div>
            </div>
            <div class="mini-stat ms-amber">
                <div class="mini-stat-value">{{ $advancedStats['pending_requests'] ?? 0 }}</div>
                <div class="mini-stat-label">En attente</div>
            </div>
            <div class="mini-stat ms-cyan">
                <div class="mini-stat-value">{{ $advancedStats['sent_requests'] ?? 0 }}</div>
                <div class="mini-stat-label">Envoyées</div>
            </div>
            <div class="mini-stat ms-primary">
                <div class="mini-stat-value">{{ $advancedStats['validation_rate'] ?? 0 }}<span style="font-size:14px;">%</span></div>
                <div class="mini-stat-label">Taux validation</div>
            </div>
            <div class="mini-stat ms-red">
                <div class="mini-stat-value">{{ $advancedStats['rejection_rate'] ?? 0 }}<span style="font-size:14px;">%</span></div>
                <div class="mini-stat-label">Taux de rejet</div>
            </div>
            <div class="mini-stat">
                <div class="mini-stat-value">{{ $advancedStats['avg_processing_time'] ?? 0 }}</div>
                <div class="mini-stat-label">Délai moyen (j)</div>
            </div>
        </div>

        {{-- Top tables --}}
        @if(isset($advancedStats['top_requesters']) && $advancedStats['top_requesters']->count() > 0)
        <div class="row g-3">
            <div class="col-md-6">
                <div class="sub-title">Top 5 créateurs de demandes</div>
                <table class="table dtable">
                    <thead>
                        <tr>
                            <th>Rang</th>
                            <th>Utilisateur</th>
                            <th class="text-end">Demandes</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($advancedStats['top_requesters'] as $i => $req)
                        <tr>
                            <td><span class="rank-badge rank-{{ $i + 1 }}">#{{ $i + 1 }}</span></td>
                            <td style="font-weight:500;">{{ $req['name'] }}</td>
                            <td class="text-end"><span style="color:#00574A;font-weight:700;">{{ $req['count'] }}</span></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if(isset($advancedStats['top_motifs']) && $advancedStats['top_motifs']->count() > 0)
            <div class="col-md-6">
                <div class="sub-title">Top 5 motifs</div>
                <table class="table dtable">
                    <thead>
                        <tr>
                            <th>Motif</th>
                            <th class="text-end">Nombre</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($advancedStats['top_motifs'] as $item)
                        <tr>
                            <td style="font-weight:500;">{{ \Illuminate\Support\Str::limit($item->motif, 50) }}</td>
                            <td class="text-end"><span style="color:#00574A;font-weight:700;">{{ $item->count }}</span></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        </div>
        @endif

        @if(isset($advancedStats['top_recuperation_numbers']) && $advancedStats['top_recuperation_numbers']->count() > 0)
        <div class="sub-title mt-4">
            <i class="bi bi-telephone me-1" style="color:#00574A;"></i>
            Top 10 numéros les plus récupérés
            <span style="font-size:11.5px;font-weight:400;color:#94a3b8;margin-left:8px;">— toutes périodes, données de la dernière demande</span>
        </div>
        <div class="table-responsive">
            <table class="table dtable">
                <thead>
                    <tr>
                        <th>Rang</th>
                        <th>Numéro</th>
                        <th>Demandeur</th>
                        <th>Titulaire de ligne</th>
                        <th class="text-end">Récupérations</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($advancedStats['top_recuperation_numbers'] as $i => $row)
                    <tr>
                        <td><span class="rank-badge rank-{{ $i + 1 }}">#{{ $i + 1 }}</span></td>
                        <td style="font-weight:600;font-family:monospace;font-size:13px;">{{ $row->phone_number }}</td>
                        <td>
                            @if($row->demandeur)
                                <div style="font-weight:500;">{{ $row->demandeur->full_name }}</div>
                                @if($row->demandeur->matricule)
                                    <small class="text-muted">{{ $row->demandeur->matricule }}</small>
                                @endif
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td>
                            @if(!empty($row->titulaire_name) || !empty($row->titulaire_matricule))
                                <div style="font-weight:500;">{{ $row->titulaire_name ?: '—' }}</div>
                                @if(!empty($row->titulaire_matricule))
                                    <small class="text-muted">{{ $row->titulaire_matricule }}</small>
                                @endif
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td class="text-end"><span style="color:#00574A;font-weight:700;">{{ $row->count }}</span></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif

    </div>
</div>
@endif

{{-- ═══ Quick links (validators only) ═════════════════════════ --}}
@if($isValidator)
<div class="dash-card mb-4">
    <div class="dash-card-header">
        <h5 class="dash-card-title">
            <span class="dct-icon si-slate"><i class="bi bi-lightning-charge"></i></span>
            Actions rapides
        </h5>
    </div>
    <div class="dash-card-body">
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('sim-requests.create') }}" class="qlink qlink-primary">
                <i class="bi bi-plus-circle"></i> Nouvelle demande
            </a>
            <a href="{{ route('sim-requests.index') }}" class="qlink qlink-outline">
                <i class="bi bi-list-ul"></i> Toutes les demandes
            </a>
            <a href="{{ route('sim-requests.index', ['status' => 'en_attente']) }}" class="qlink qlink-amber">
                <i class="bi bi-clock"></i> En attente
            </a>
            <a href="{{ route('sim-requests.index', ['status' => 'pending']) }}" class="qlink qlink-outline">
                <i class="bi bi-hourglass-split"></i> Pending opérateur
            </a>
            @if($user->isAdmin())
            <a href="{{ route('sims.index') }}" class="qlink qlink-outline">
                <i class="bi bi-phone"></i> Gestion SIMs
            </a>
            @endif
        </div>
    </div>
</div>
@endif

{{-- ═══ Recent requests ════════════════════════════════════════ --}}
@php
    $recentQuery = \App\Models\SimRequest::with('user')->latest();
    if (!$isValidator) {
        $recentQuery->where('user_id', $user->id);
    }
    $recentRequests = $recentQuery->limit(10)->get();
@endphp

<div class="dash-card mb-4">
    <div class="dash-card-header">
        <h5 class="dash-card-title">
            <span class="dct-icon si-blue"><i class="bi bi-clock-history"></i></span>
            Dernières demandes
        </h5>
        <a href="{{ route('sim-requests.index') }}" class="qlink qlink-outline" style="font-size:12px;padding:5px 12px;">
            Voir tout <i class="bi bi-arrow-right"></i>
        </a>
    </div>
    <div class="dash-card-body-0">
        <div class="table-responsive">
            <table class="table dtable mb-0">
                <thead>
                    <tr>
                        <th>N° Demande</th>
                        <th>{{ $isValidator ? 'Utilisateur / Bénéficiaire' : 'Type' }}</th>
                        <th>Type</th>
                        <th>Statut</th>
                        <th>Date</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentRequests as $req)
                    <tr>
                        <td>
                            <span style="font-weight:700;color:#0f172a;font-size:13px;">{{ $req->request_number }}</span>
                        </td>
                        <td>
                            <span style="font-weight:500;color:#374151;">
                                @if($req->isCreation() && $req->beneficiary_name)
                                    {{ $req->beneficiary_name }} {{ $req->beneficiary_first_name ?? '' }}
                                @else
                                    {{ $req->user?->full_name ?? '—' }}
                                @endif
                            </span>
                        </td>
                        <td>
                            <span class="sbadge {{ $typeBadge[$req->request_type] ?? 'sb-slate' }}">
                                <span class="sdot"></span>
                                {{ $typeLabels[$req->request_type] ?? ucfirst($req->request_type) }}
                            </span>
                        </td>
                        <td>
                            <span class="sbadge {{ $statusBadge[$req->status] ?? 'sb-slate' }}">
                                <span class="sdot"></span>
                                {{ $statusLabels[$req->status] ?? ucfirst($req->status) }}
                            </span>
                        </td>
                        <td style="color:#94a3b8;font-size:12.5px;white-space:nowrap;">
                            {{ $req->created_at->format('d/m/Y') }}
                            <span style="color:#cbd5e1;"> {{ $req->created_at->format('H:i') }}</span>
                        </td>
                        <td>
                            <a href="{{ route('sim-requests.show', $req) }}"
                               class="btn btn-sm"
                               style="background:#f1f5f9;border:none;color:#3b82f6;padding:5px 10px;border-radius:8px;"
                               title="Voir le détail">
                                <i class="bi bi-eye"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6">
                            <div class="chart-empty py-4">
                                <i class="bi bi-inbox"></i>
                                <p>Aucune demande pour le moment</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection

{{-- ═══ Scripts ═══════════════════════════════════════════════ --}}
@push('scripts')
@if($isValidator)
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
// Global Chart.js defaults
Chart.defaults.font.family = "'Poppins', sans-serif";
Chart.defaults.font.size   = 12;
Chart.defaults.color       = '#64748b';
Chart.defaults.borderColor = '#f1f5f9';
Chart.defaults.plugins.tooltip.padding         = 10;
Chart.defaults.plugins.tooltip.cornerRadius    = 8;
Chart.defaults.plugins.tooltip.backgroundColor = '#1e293b';
Chart.defaults.plugins.tooltip.titleFont       = { weight: '600' };

let charts = {};
const chartData = @json($chartData);

document.addEventListener('DOMContentLoaded', initCharts);

function _noData(wrapperId, msg) {
    const el = document.getElementById(wrapperId);
    if (el) el.innerHTML = `<div class="chart-empty"><i class="bi bi-inbox"></i><p>${msg||'Aucune donnée pour cette période'}</p></div>`;
}

function initCharts() {
    // ── Evolution des demandes ─────────────────────────────
    const evoCtx = document.getElementById('requestsEvolutionChart');
    if (evoCtx) {
        const d = chartData.requests_evolution;
        if (!d?.labels?.length) { _noData('wrap-evolution'); }
        else {
            charts.evolution = new Chart(evoCtx, {
                type: 'line',
                data: {
                    labels: d.labels,
                    datasets: [{
                        label: 'Demandes',
                        data: d.data,
                        borderColor: '#00574A',
                        backgroundColor: 'rgba(0,87,74,0.08)',
                        borderWidth: 2.5, fill: true, tension: 0.4,
                        pointRadius: 4, pointHoverRadius: 6,
                        pointBackgroundColor: '#00574A',
                    }]
                },
                options: _lineOpts()
            });
        }
    }

    // ── Par type (doughnut) ────────────────────────────────
    const typeCtx = document.getElementById('requestsByTypeChart');
    if (typeCtx && chartData.requests_by_type) {
        const d   = chartData.requests_by_type;
        const sum = (d.data||[]).reduce((a,b) => a+b, 0);
        charts.type = new Chart(typeCtx, {
            type: 'doughnut',
            data: {
                labels: sum > 0 ? d.labels : ['Aucune donnée'],
                datasets: [{
                    data:            sum > 0 ? d.data   : [1],
                    backgroundColor: sum > 0 ? d.colors : ['#e2e8f0'],
                    borderWidth: 0, hoverOffset: 6,
                }]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                cutout: '65%',
                plugins: { legend: { position: 'bottom', labels: { padding: 14, usePointStyle: true, pointStyleWidth: 8 } } }
            }
        });
    }

    // ── Par statut (bar) ───────────────────────────────────
    const statusCtx = document.getElementById('requestsByStatusChart');
    if (statusCtx && chartData.requests_by_status) {
        const d = chartData.requests_by_status;
        charts.status = new Chart(statusCtx, {
            type: 'bar',
            data: {
                labels: d.labels,
                datasets: [{
                    label: 'Demandes',
                    data: d.data,
                    backgroundColor: d.colors,
                    borderWidth: 0, borderRadius: 6, borderSkipped: false,
                    maxBarThickness: 36,
                }]
            },
            options: _barOpts(d)
        });
    }

    // ── SIMs par statut (bar) ──────────────────────────────
    const simsCtx = document.getElementById('simsByStatusChart');
    if (simsCtx && chartData.sims_by_status) {
        const d = chartData.sims_by_status;
        charts.sims = new Chart(simsCtx, {
            type: 'bar',
            data: {
                labels: d.labels,
                datasets: [{
                    label: 'SIMs',
                    data: d.data,
                    backgroundColor: d.colors,
                    borderWidth: 0, borderRadius: 6, borderSkipped: false,
                    maxBarThickness: 36,
                }]
            },
            options: _barOpts(d)
        });
    }

    // ── SIMs créées (line) ─────────────────────────────────
    const simsCreatedCtx = document.getElementById('simsCreatedEvolutionChart');
    if (simsCreatedCtx && chartData.sims_created_evolution) {
        const d = chartData.sims_created_evolution;
        if (!d?.labels?.length) { _noData('wrap-sims-created'); }
        else {
            charts.simsCreated = new Chart(simsCreatedCtx, {
                type: 'line',
                data: {
                    labels: d.labels,
                    datasets: [{
                        label: 'SIMs créées',
                        data: d.data,
                        borderColor: '#10b981',
                        backgroundColor: 'rgba(16,185,129,0.08)',
                        borderWidth: 2.5, fill: true, tension: 0.4,
                        pointRadius: 4, pointHoverRadius: 6,
                        pointBackgroundColor: '#10b981',
                    }]
                },
                options: _lineOpts()
            });
        }
    }
}

function _lineOpts() {
    return {
        responsive: true, maintainAspectRatio: false,
        plugins: {
            legend: { display: false },
            tooltip: { mode: 'index', intersect: false }
        },
        scales: {
            y: { beginAtZero: true, ticks: { stepSize: 1, precision: 0 }, grid: { color: '#f1f5f9' } },
            x: { ticks: { maxRotation: 45, minRotation: 0 }, grid: { display: false } }
        }
    };
}

function _barOpts(d) {
    return {
        responsive: true, maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom',
                labels: {
                    padding: 12, usePointStyle: true, pointStyleWidth: 8,
                    generateLabels(chart) {
                        return chart.data.labels.map((label, i) => ({
                            text: `${label}: ${chart.data.datasets[0].data[i]}`,
                            fillStyle: chart.data.datasets[0].backgroundColor[i],
                            hidden: false, index: i
                        }));
                    }
                }
            }
        },
        scales: {
            y: { beginAtZero: true, ticks: { stepSize: 1 }, grid: { color: '#f1f5f9' } },
            x: { grid: { display: false } }
        }
    };
}

// ── Period change ────────────────────────────────────────────────
function changePeriod(period, btn) {
    document.querySelectorAll('.period-tab').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');

    fetch(`{{ route('dashboard.chart-data') }}?period=${period}`, {
        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
    })
    .then(r => r.json())
    .then(data => {
        if (charts.evolution && data.requests_evolution) {
            charts.evolution.data.labels = data.requests_evolution.labels;
            charts.evolution.data.datasets[0].data = data.requests_evolution.data;
            charts.evolution.update();
        }
        if (charts.type && data.requests_by_type) {
            const d = data.requests_by_type.data || [];
            const sum = d.reduce((a,b) => a+b, 0);
            charts.type.data.labels                       = sum > 0 ? data.requests_by_type.labels  : ['Aucune donnée'];
            charts.type.data.datasets[0].data             = sum > 0 ? d                              : [1];
            charts.type.data.datasets[0].backgroundColor  = sum > 0 ? data.requests_by_type.colors  : ['#e2e8f0'];
            charts.type.update();
        }
        if (charts.status && data.requests_by_status) {
            charts.status.data.labels = data.requests_by_status.labels;
            charts.status.data.datasets[0].data            = data.requests_by_status.data;
            charts.status.data.datasets[0].backgroundColor = data.requests_by_status.colors;
            charts.status.update();
        }
        if (charts.sims && data.sims_by_status) {
            charts.sims.data.labels = data.sims_by_status.labels;
            charts.sims.data.datasets[0].data            = data.sims_by_status.data;
            charts.sims.data.datasets[0].backgroundColor = data.sims_by_status.colors;
            charts.sims.update();
        }
        if (charts.simsCreated && data.sims_created_evolution) {
            charts.simsCreated.data.labels = data.sims_created_evolution.labels;
            charts.simsCreated.data.datasets[0].data = data.sims_created_evolution.data;
            charts.simsCreated.update();
        }
    })
    .catch(err => console.error('Chart data error:', err));
}
</script>
@endif
@endpush
