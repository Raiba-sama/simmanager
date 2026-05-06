@extends('layouts.bootstrap')

@section('title', 'Demande #' . $simRequest->request_number)
@section('page-title', 'Détail de la demande')

@push('styles')
<style>
    /* ── Layout ──────────────────────────────────────────────────── */
    .sr-grid {
        display: grid;
        grid-template-columns: 1fr 320px;
        gap: 20px;
        align-items: start;
    }
    @media (max-width: 991px) {
        .sr-grid { grid-template-columns: 1fr; }
        .sr-sticky { position: static !important; }
    }
    .sr-sticky { position: sticky; top: calc(var(--navbar-h) + 20px); }

    /* ── Cards ───────────────────────────────────────────────────── */
    .sr-card {
        background: white;
        border: 1px solid var(--border);
        border-radius: 14px;
        box-shadow: 0 1px 4px rgba(0,0,0,0.05);
        overflow: hidden;
        margin-bottom: 16px;
    }
    .sr-card-header {
        padding: 15px 20px;
        border-bottom: 1px solid var(--border);
        display: flex; align-items: center; justify-content: space-between;
        gap: 10px;
        background: #fafafa;
    }
    .sr-card-title {
        display: flex; align-items: center; gap: 8px;
        font-size: 13.5px; font-weight: 700; color: var(--text);
        margin: 0;
    }
    .sr-card-title i { color: var(--primary); font-size: 16px; }
    .sr-card-body { padding: 20px; }

    /* ── Section separator inside card ──────────────────────────── */
    .sr-section {
        padding-top: 16px;
        margin-top: 16px;
        border-top: 1px solid #f1f5f9;
    }
    .sr-section:first-child { padding-top: 0; margin-top: 0; border-top: none; }
    .sr-section-label {
        font-size: 11px; font-weight: 700;
        text-transform: uppercase; letter-spacing: 0.07em;
        color: var(--muted); margin-bottom: 12px;
    }

    /* ── DL grid ─────────────────────────────────────────────────── */
    .sr-dl { display: grid; grid-template-columns: 150px 1fr; gap: 8px 16px; }
    .sr-dt { font-size: 12px; font-weight: 600; color: var(--muted); padding-top: 2px; }
    .sr-dd { font-size: 13.5px; color: var(--text); font-weight: 500; }

    /* ── Badges ──────────────────────────────────────────────────── */
    .sr-badge {
        display: inline-flex; align-items: center; gap: 5px;
        padding: 3px 11px; border-radius: 20px;
        font-size: 12px; font-weight: 600;
    }
    .sr-badge-dot {
        width: 6px; height: 6px; border-radius: 50%;
        background: currentColor; flex-shrink: 0;
    }

    /* ── Avatar user chip ────────────────────────────────────────── */
    .sr-user-chip {
        display: inline-flex; align-items: center; gap: 8px;
    }
    .sr-avatar {
        width: 28px; height: 28px; border-radius: 50%;
        object-fit: cover; flex-shrink: 0;
        border: 1.5px solid var(--border);
    }
    .sr-user-name { font-size: 13.5px; font-weight: 600; color: var(--text); }
    .sr-user-sub  { font-size: 11.5px; color: var(--muted); margin-top: 1px; }

    /* ── Timeline ────────────────────────────────────────────────── */
    .sr-timeline { padding: 0; list-style: none; margin: 0; }
    .sr-timeline-item {
        display: flex; gap: 12px;
        padding-bottom: 14px;
        position: relative;
    }
    .sr-timeline-item::before {
        content: '';
        position: absolute; left: 15px; top: 28px;
        width: 2px; bottom: 0;
        background: var(--border);
    }
    .sr-timeline-item:last-child::before { display: none; }
    .sr-timeline-item:last-child { padding-bottom: 0; }

    .sr-timeline-dot {
        width: 30px; height: 30px; border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-size: 13px; flex-shrink: 0;
        border: 2px solid var(--border);
        background: white;
    }
    .sr-timeline-body { flex: 1; min-width: 0; padding-top: 3px; }
    .sr-timeline-action { font-size: 13.5px; font-weight: 600; color: var(--text); }
    .sr-timeline-meta { font-size: 11.5px; color: var(--muted); margin-top: 2px; }

    /* ── Historique table ────────────────────────────────────────── */
    .sr-table { width: 100%; border-collapse: collapse; }
    .sr-table th {
        padding: 10px 14px; font-size: 11px; font-weight: 700;
        text-transform: uppercase; letter-spacing: 0.05em;
        color: var(--muted); background: #f8fafc;
        border-bottom: 1px solid var(--border); text-align: left;
    }
    .sr-table td {
        padding: 10px 14px; font-size: 12.5px;
        color: var(--text); border-bottom: 1px solid #f1f5f9;
        vertical-align: middle;
    }
    .sr-table tbody tr:last-child td { border-bottom: none; }
    .sr-table tbody tr:hover td { background: #f8fafc; }

    /* ── Action cards (right panel) ──────────────────────────────── */
    .sr-action {
        background: white;
        border: 1px solid var(--border);
        border-radius: 12px;
        overflow: hidden;
        margin-bottom: 12px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.04);
    }
    .sr-action-header {
        padding: 11px 16px;
        display: flex; align-items: center; gap: 8px;
        font-size: 12.5px; font-weight: 700;
        border-bottom: 1px solid var(--border);
        border-left: 3px solid transparent;
    }
    .sr-action-body { padding: 14px 16px; }

    .sr-action-header.green  { border-left-color: var(--primary); color: var(--primary); background: #f0fdf4; }
    .sr-action-header.amber  { border-left-color: #d97706; color: #92400e; background: #fffbeb; }
    .sr-action-header.blue   { border-left-color: #3b82f6; color: #1d4ed8; background: #eff6ff; }
    .sr-action-header.red    { border-left-color: #ef4444; color: #991b1b; background: #fef2f2; }
    .sr-action-header.indigo { border-left-color: #6366f1; color: #3730a3; background: #eef2ff; }

    /* ── Action form fields ──────────────────────────────────────── */
    .sr-input {
        width: 100%; border: 1.5px solid var(--border); border-radius: 9px;
        padding: 8px 12px; font-size: 13px;
        font-family: 'Poppins', sans-serif; color: var(--text);
        outline: none; resize: vertical; box-sizing: border-box;
        transition: border-color 0.2s, box-shadow 0.2s;
    }
    .sr-input:focus { border-color: var(--primary); box-shadow: 0 0 0 3px var(--primary-glow); }
    select.sr-input { height: 36px; padding: 0 12px; cursor: pointer; resize: none; }

    /* ── Action buttons ──────────────────────────────────────────── */
    .sr-btn {
        display: flex; align-items: center; justify-content: center; gap: 6px;
        width: 100%; height: 36px; padding: 0 14px;
        border: none; border-radius: 9px;
        font-size: 13px; font-weight: 600; font-family: 'Poppins', sans-serif;
        cursor: pointer; transition: background 0.18s, opacity 0.18s;
        text-decoration: none; margin-bottom: 8px;
    }
    .sr-btn:last-child { margin-bottom: 0; }
    .sr-btn-primary  { background: var(--primary); color: white; }
    .sr-btn-primary:hover  { background: var(--primary-dark); color: white; }
    .sr-btn-success  { background: #16a34a; color: white; }
    .sr-btn-success:hover  { background: #15803d; color: white; }
    .sr-btn-danger   { background: #ef4444; color: white; }
    .sr-btn-danger:hover   { background: #dc2626; color: white; }
    .sr-btn-outline  { background: white; color: var(--text); border: 1.5px solid var(--border); }
    .sr-btn-outline:hover  { border-color: var(--primary); color: var(--primary); }
    .sr-btn-outline-danger { background: white; color: #ef4444; border: 1.5px solid #fecaca; }
    .sr-btn-outline-danger:hover { background: #fef2f2; border-color: #ef4444; color: #ef4444; }
    .sr-btn-disabled { background: #f3f4f6; color: #9ca3af; cursor: not-allowed; }

    /* ── Top bar ─────────────────────────────────────────────────── */
    .sr-topbar {
        display: flex; align-items: center; justify-content: space-between;
        flex-wrap: wrap; gap: 12px; margin-bottom: 18px;
    }
    .sr-topbar-left .sr-number { font-size: 18px; font-weight: 700; color: var(--text); }
    .sr-topbar-left .sr-meta  { font-size: 12px; color: var(--muted); margin-top: 3px; }

    .sr-topbar-actions { display: flex; gap: 8px; flex-wrap: wrap; }
    .sr-top-btn {
        display: inline-flex; align-items: center; gap: 6px;
        height: 34px; padding: 0 14px;
        border-radius: 9px; font-size: 13px; font-weight: 600;
        font-family: 'Poppins', sans-serif; cursor: pointer;
        text-decoration: none; transition: all 0.18s;
    }
    .sr-top-btn-outline {
        background: white; color: var(--text);
        border: 1.5px solid var(--border);
    }
    .sr-top-btn-outline:hover { border-color: var(--primary); color: var(--primary); }
    .sr-top-btn-primary {
        background: var(--primary); color: white; border: none;
    }
    .sr-top-btn-primary:hover { background: var(--primary-dark); color: white; }
    .sr-top-btn-star {
        background: #fffbeb; color: #d97706;
        border: 1.5px solid #fde68a;
    }
    .sr-top-btn-star:hover { background: #fef3c7; }
    .sr-top-btn-star.active { background: #f59e0b; color: white; border-color: #f59e0b; }

    /* ── Group bar ───────────────────────────────────────────────── */
    .sr-group-bar {
        background: white; border: 1px solid var(--border);
        border-left: 3px solid var(--primary);
        border-radius: 12px; padding: 12px 16px;
        margin-bottom: 16px;
        display: flex; align-items: center; flex-wrap: wrap; gap: 10px;
    }
    .sr-group-bar-label {
        font-size: 12.5px; font-weight: 700; color: var(--primary);
        display: flex; align-items: center; gap: 6px;
        flex-shrink: 0;
    }
    .sr-group-chip {
        display: inline-flex; align-items: center; gap: 4px;
        padding: 4px 10px; border-radius: 8px;
        font-size: 12px; font-weight: 600;
        text-decoration: none; transition: all 0.15s;
    }
    .sr-group-chip-current {
        background: var(--primary); color: white;
    }
    .sr-group-chip-other {
        background: #f0fdf4; color: var(--primary);
        border: 1.5px solid #bbf7d0;
    }
    .sr-group-chip-other:hover { background: #dcfce7; color: var(--primary); }

    /* ── Notification banner ─────────────────────────────────────── */
    .sr-notif-banner {
        display: flex; align-items: flex-start; gap: 12px;
        background: #eff6ff; border: 1px solid #bfdbfe;
        border-left: 3px solid #3b82f6;
        border-radius: 12px; padding: 14px 16px;
        margin-bottom: 16px;
        position: relative;
    }
    .sr-notif-banner i { color: #3b82f6; font-size: 18px; flex-shrink: 0; margin-top: 1px; }
    .sr-notif-banner-title { font-size: 13.5px; font-weight: 700; color: #1e40af; }
    .sr-notif-banner-text  { font-size: 12.5px; color: #3b82f6; margin-top: 2px; }
    .sr-notif-close {
        position: absolute; top: 10px; right: 12px;
        background: none; border: none; cursor: pointer;
        color: #93c5fd; font-size: 16px; line-height: 1;
        transition: color 0.15s;
    }
    .sr-notif-close:hover { color: #3b82f6; }

    /* ── Comment/alert boxes ─────────────────────────────────────── */
    .sr-info-box {
        background: #f0fdf4; border: 1px solid #bbf7d0;
        border-radius: 9px; padding: 10px 14px;
        font-size: 13px; color: #166534;
    }
    .sr-warning-box {
        background: #fffbeb; border: 1px solid #fde68a;
        border-radius: 9px; padding: 10px 14px;
        font-size: 13px; color: #92400e;
    }
    .sr-danger-box {
        background: #fef2f2; border: 1px solid #fecaca;
        border-radius: 9px; padding: 10px 14px;
        font-size: 13px; color: #991b1b;
    }

    /* ── Back button ─────────────────────────────────────────────── */
    .sr-back {
        display: inline-flex; align-items: center; gap: 6px;
        padding: 8px 16px; background: white;
        border: 1.5px solid var(--border); border-radius: 9px;
        font-size: 13px; font-weight: 500; color: var(--text);
        text-decoration: none; transition: border-color 0.18s, color 0.18s;
    }
    .sr-back:hover { border-color: var(--primary); color: var(--primary); }
</style>
@endpush

@section('content')
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
        'ajustement'   => 'background:#ede9fe;color:#5b21b6;',
    ];
    $statusLabels = [
        'en_attente'      => 'En attente',
        'validee'         => 'Validée',
        'rejetee'         => 'Rejetée',
        'demande_envoyee' => 'Demande envoyée',
        'pending'         => 'En attente (opérateur)',
        'refused'         => 'Refusée (opérateur)',
        'accepted'        => 'Acceptée (opérateur)',
    ];
    $statusStyles = [
        'en_attente'      => 'background:#fef3c7;color:#92400e;',
        'validee'         => 'background:#d1fae5;color:#065f46;',
        'rejetee'         => 'background:#fee2e2;color:#991b1b;',
        'demande_envoyee' => 'background:#dbeafe;color:#1e40af;',
        'pending'         => 'background:#dbeafe;color:#1e40af;',
        'refused'         => 'background:#fee2e2;color:#991b1b;',
        'accepted'        => 'background:#d1fae5;color:#065f46;',
    ];
    $actionColors = [
        'created'              => ['bg'=>'#d1fae5','text'=>'#065f46','icon'=>'bi-plus-circle'],
        'validated'            => ['bg'=>'#dbeafe','text'=>'#1e40af','icon'=>'bi-check-circle'],
        'rejected'             => ['bg'=>'#fee2e2','text'=>'#991b1b','icon'=>'bi-x-circle'],
        'status_updated'       => ['bg'=>'#fef3c7','text'=>'#92400e','icon'=>'bi-arrow-repeat'],
        'submitted_to_webhook' => ['bg'=>'#ede9fe','text'=>'#5b21b6','icon'=>'bi-send'],
        'cancelled'            => ['bg'=>'#f3f4f6','text'=>'#6b7280','icon'=>'bi-dash-circle'],
    ];

    $user = auth()->user();
    $canEdit = false;
    if ($user->isValidator()) {
        $canEdit = ($simRequest->created_by === $user->id) || ($simRequest->status === 'en_attente');
    } else {
        $canEdit = ($simRequest->user_id === $user->id) && ($simRequest->status === 'en_attente');
    }
    $canEdit = $canEdit && ($simRequest->status !== 'demande_envoyee');
    $canCopy = $user->isValidator() || ($simRequest->isRecuperation() && $simRequest->user_id === $user->id);

    $collaboratorUser = $simRequest->collaborator_matricule
        ? \App\Models\User::where('matricule', $simRequest->collaborator_matricule)->first()
        : null;
    $beneficiaryUser = $simRequest->beneficiary_matricule
        ? \App\Models\User::where('matricule', $simRequest->beneficiary_matricule)->first()
        : null;

    $daysPending = now()->diffInDays($simRequest->created_at);
@endphp

{{-- Notification banner --}}
@if(request()->has('from') && request()->from === 'notification')
    <div class="sr-notif-banner">
        <i class="bi bi-bell-fill"></i>
        <div>
            <div class="sr-notif-banner-title">Rappel de notification</div>
            <div class="sr-notif-banner-text">Vous avez été redirigé depuis une notification. Cette demande nécessite une action.</div>
        </div>
        <button class="sr-notif-close" onclick="this.closest('.sr-notif-banner').remove()">
            <i class="bi bi-x"></i>
        </button>
    </div>
@endif

{{-- Group bar --}}
@if($simRequest->isGrouped() && $simRequest->groupMembers->count() > 1)
    <div class="sr-group-bar">
        <span class="sr-group-bar-label">
            <i class="bi bi-collection"></i>
            Groupe ({{ $simRequest->groupMembers->count() }} SIMs)
        </span>
        <div class="d-flex flex-wrap gap-2">
            @foreach($simRequest->groupMembers as $member)
                <a href="{{ route('sim-requests.show', $member) }}"
                   class="sr-group-chip {{ $member->id === $simRequest->id ? 'sr-group-chip-current' : 'sr-group-chip-other' }}">
                    {{ $member->request_number }}
                    @if($member->sim) <span style="opacity:.7;">— {{ $member->sim->iccid }}</span> @endif
                </a>
            @endforeach
        </div>
    </div>
@endif

{{-- Top bar --}}
<div class="sr-topbar">
    <div class="sr-topbar-left">
        <div class="d-flex align-items-center gap-3 flex-wrap">
            <div class="sr-number">{{ $simRequest->request_number }}</div>
            <span class="sr-badge" style="{{ $statusStyles[$simRequest->status] ?? 'background:#f3f4f6;color:#6b7280;' }}">
                <span class="sr-badge-dot"></span>
                {{ $statusLabels[$simRequest->status] ?? $simRequest->status }}
            </span>
            @if($simRequest->status === 'en_attente' && $daysPending >= 3)
                <span class="sr-badge" style="background:{{ $daysPending>=7?'#fee2e2':($daysPending>=5?'#fef3c7':'#dbeafe') }};color:{{ $daysPending>=7?'#991b1b':($daysPending>=5?'#92400e':'#1e40af') }};">
                    <i class="bi bi-clock-history" style="font-size:10px;"></i>
                    {{ $daysPending }}j en attente
                </span>
            @endif
        </div>
        <div class="sr-meta">Créée le {{ $simRequest->created_at->format('d/m/Y à H:i') }}</div>
    </div>
    <div class="sr-topbar-actions">
        @if($canCopy)
            <a href="{{ route('sim-requests.create', ['copy_from' => $simRequest->id]) }}"
               class="sr-top-btn sr-top-btn-outline">
                <i class="bi bi-arrow-repeat"></i> Reprendre
            </a>
        @endif
        @if($canEdit)
            <a href="{{ route('sim-requests.edit', $simRequest) }}"
               class="sr-top-btn sr-top-btn-primary">
                <i class="bi bi-pencil"></i> Modifier
            </a>
        @endif
        <button type="button"
                id="fav-btn"
                class="sr-top-btn {{ $isFavorite ? 'sr-top-btn-star active' : 'sr-top-btn-star' }}"
                onclick="toggleFavorite({{ $simRequest->id }}, this)">
            <i class="bi {{ $isFavorite ? 'bi-star-fill' : 'bi-star' }}"></i>
            {{ $isFavorite ? 'Favori' : 'Ajouter' }}
        </button>
    </div>
</div>

{{-- Main grid --}}
<div class="sr-grid">

    {{-- ── Left column ─────────────────────────────────────────── --}}
    <div>

        {{-- Info card --}}
        <div class="sr-card">
            <div class="sr-card-header">
                <h2 class="sr-card-title"><i class="bi bi-file-text"></i> Informations de la demande</h2>
                <span class="sr-badge" style="{{ $typeStyles[$simRequest->request_type] ?? 'background:#f3f4f6;color:#6b7280;' }}">
                    {{ $typeLabels[$simRequest->request_type] ?? ucfirst($simRequest->request_type) }}
                </span>
            </div>
            <div class="sr-card-body">

                {{-- Général --}}
                <div class="sr-section">
                    <div class="sr-section-label">Général</div>
                    <dl class="sr-dl">
                        <dt class="sr-dt">N° Demande</dt>
                        <dd class="sr-dd"><strong>{{ $simRequest->request_number }}</strong></dd>

                        <dt class="sr-dt">Statut</dt>
                        <dd class="sr-dd">
                            <span class="sr-badge" style="{{ $statusStyles[$simRequest->status] ?? 'background:#f3f4f6;color:#6b7280;' }}">
                                <span class="sr-badge-dot"></span>
                                {{ $statusLabels[$simRequest->status] ?? $simRequest->status }}
                            </span>
                        </dd>

                        @if($simRequest->creator)
                            <dt class="sr-dt">Créé par</dt>
                            <dd class="sr-dd">
                                <div class="sr-user-chip">
                                    <img src="{{ $simRequest->creator->avatar }}" class="sr-avatar" alt="">
                                    <div>
                                        <div class="sr-user-name">{{ $simRequest->creator->full_name }}</div>
                                        @if($simRequest->creator->matricule)
                                            <div class="sr-user-sub">{{ $simRequest->creator->matricule }}</div>
                                        @endif
                                    </div>
                                </div>
                            </dd>
                        @endif

                        @if($simRequest->isRecuperation() && $simRequest->user)
                            <dt class="sr-dt">Demandeur</dt>
                            <dd class="sr-dd">
                                <div class="sr-user-chip">
                                    <img src="{{ $simRequest->user->avatar }}" class="sr-avatar" alt="">
                                    <div>
                                        <div class="sr-user-name">{{ $simRequest->user->full_name }}</div>
                                        <div class="sr-user-sub">{{ $simRequest->user->matricule }}</div>
                                    </div>
                                </div>
                            </dd>
                        @endif

                        <dt class="sr-dt">Créée le</dt>
                        <dd class="sr-dd">{{ $simRequest->created_at->format('d/m/Y H:i') }}</dd>

                        @if($simRequest->validated_at)
                            <dt class="sr-dt">Validée le</dt>
                            <dd class="sr-dd">{{ $simRequest->validated_at->format('d/m/Y H:i') }}</dd>
                        @endif

                        @if($simRequest->admin_processed_at)
                            <dt class="sr-dt">Traitement admin</dt>
                            <dd class="sr-dd">{{ $simRequest->admin_processed_at->format('d/m/Y H:i') }}</dd>
                        @endif

                        <dt class="sr-dt">Livraison</dt>
                        <dd class="sr-dd">
                            @if($simRequest->isDelivered())
                                <span class="sr-badge" style="background:#d1fae5;color:#065f46;">
                                    <span class="sr-badge-dot"></span> Livré — {{ $simRequest->delivered_at->format('d/m/Y H:i') }}
                                </span>
                            @else
                                <span style="color:var(--muted);">Non livré</span>
                            @endif
                        </dd>
                    </dl>
                </div>

                {{-- Collaborateur / Bénéficiaire --}}
                @if(!$simRequest->isCreation() && ($simRequest->collaborator_matricule || $simRequest->collaborator_name || $simRequest->collaborator_first_name || $simRequest->collaborator_agence))
                    <div class="sr-section">
                        <div class="sr-section-label">Collaborateur concerné</div>
                        <div class="sr-user-chip" style="align-items:flex-start;">
                            @if($collaboratorUser)
                                <img src="{{ $collaboratorUser->avatar }}" class="sr-avatar" alt="" style="margin-top:2px;">
                            @else
                                <div class="sr-avatar" style="background:var(--primary-glow);display:flex;align-items:center;justify-content:center;color:var(--primary);font-size:12px;font-weight:700;">
                                    {{ strtoupper(substr($simRequest->collaborator_name ?? '?', 0, 1)) }}
                                </div>
                            @endif
                            <div>
                                @if($simRequest->collaborator_name || $simRequest->collaborator_first_name)
                                    <div class="sr-user-name">{{ trim(($simRequest->collaborator_name ?? '') . ' ' . ($simRequest->collaborator_first_name ?? '')) }}</div>
                                @endif
                                @if($simRequest->collaborator_matricule)
                                    <div class="sr-user-sub">Mat : {{ $simRequest->collaborator_matricule }}</div>
                                @endif
                                @if($simRequest->collaborator_agence)
                                    <div class="sr-user-sub">Agence : {{ $simRequest->collaborator_agence }}</div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif

                @if($simRequest->isCreation())
                    <div class="sr-section">
                        <div class="sr-section-label">Bénéficiaire</div>
                        <div class="sr-user-chip" style="align-items:flex-start;">
                            @if($beneficiaryUser)
                                <img src="{{ $beneficiaryUser->avatar }}" class="sr-avatar" alt="" style="margin-top:2px;">
                            @else
                                <div class="sr-avatar" style="background:var(--primary-glow);display:flex;align-items:center;justify-content:center;color:var(--primary);font-size:12px;font-weight:700;">
                                    {{ strtoupper(substr($simRequest->beneficiary_name ?? '?', 0, 1)) }}
                                </div>
                            @endif
                            <div>
                                <div class="sr-user-name">{{ $simRequest->beneficiary_name }} {{ $simRequest->beneficiary_first_name }}</div>
                                @if($simRequest->beneficiary_matricule)
                                    <div class="sr-user-sub">Mat : {{ $simRequest->beneficiary_matricule }}</div>
                                @endif
                                @if($simRequest->beneficiary_fonction)
                                    <div class="sr-user-sub">Fonction : {{ $simRequest->beneficiary_fonction }}</div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Détails SIM --}}
                @if($simRequest->phone_number || $simRequest->sim || $simRequest->requested_iccid || $simRequest->plan || $simRequest->motif || $simRequest->justification)
                    <div class="sr-section">
                        <div class="sr-section-label">Détails SIM & Forfait</div>
                        <dl class="sr-dl">
                            @if($simRequest->phone_number || ($simRequest->sim && $simRequest->sim->phone_number))
                                <dt class="sr-dt">Ligne concernée</dt>
                                <dd class="sr-dd">{{ $simRequest->phone_number ?? $simRequest->sim->phone_number }}</dd>
                            @endif

                            @if($simRequest->sim)
                                <dt class="sr-dt">SIM (ICCID)</dt>
                                <dd class="sr-dd">
                                    <a href="{{ route('sims.show', $simRequest->sim) }}"
                                       style="color:var(--primary);text-decoration:none;font-family:monospace;font-size:13px;">
                                        {{ $simRequest->sim->iccid }}
                                    </a>
                                </dd>
                            @endif

                            @if($simRequest->requested_iccid)
                                <dt class="sr-dt">ICCID demandé</dt>
                                <dd class="sr-dd" style="font-family:monospace;font-size:13px;">{{ $simRequest->requested_iccid }}</dd>
                            @endif

                            @if($simRequest->plan)
                                <dt class="sr-dt">Forfait</dt>
                                <dd class="sr-dd">{{ $simRequest->plan->name }}</dd>
                            @endif

                            @if($simRequest->limite_credit !== null)
                                <dt class="sr-dt">Limite crédit</dt>
                                <dd class="sr-dd">{{ number_format($simRequest->limite_credit, 0, ',', ' ') }} XOF</dd>
                            @endif

                            @if($simRequest->limite_data !== null)
                                <dt class="sr-dt">Limite data</dt>
                                <dd class="sr-dd">{{ $simRequest->limite_data }} GB</dd>
                            @endif

                            @if($simRequest->motif)
                                <dt class="sr-dt">Motif</dt>
                                <dd class="sr-dd">{{ $simRequest->motif }}</dd>
                            @endif

                            @if($simRequest->justification)
                                <dt class="sr-dt">Justification</dt>
                                <dd class="sr-dd">{{ $simRequest->justification }}</dd>
                            @endif
                        </dl>

                        @if($simRequest->is_temporary)
                            <div class="sr-warning-box mt-3">
                                <strong><i class="bi bi-clock me-1"></i> Ajustement temporaire</strong><br>
                                @if($simRequest->temporary_start_date)
                                    Début : {{ $simRequest->temporary_start_date->format('d/m/Y') }} —
                                @endif
                                Fin : {{ $simRequest->temporary_end_date ? $simRequest->temporary_end_date->format('d/m/Y') : 'Non définie' }}
                                @if($simRequest->temporary_end_date && $simRequest->temporary_end_date->isPast())
                                    <span class="sr-badge" style="background:#fee2e2;color:#991b1b;margin-left:6px;">Expiré</span>
                                @elseif($simRequest->temporary_end_date && $simRequest->temporary_end_date->diffInDays(now()) <= 7)
                                    <span class="sr-badge" style="background:#fef3c7;color:#92400e;margin-left:6px;">Expire bientôt</span>
                                @endif
                            </div>
                        @endif
                    </div>
                @endif

                {{-- Traitement --}}
                @if($simRequest->validator || $simRequest->admin || $simRequest->admin_comment || $simRequest->rejection_reason)
                    <div class="sr-section">
                        <div class="sr-section-label">Traitement</div>
                        <dl class="sr-dl">
                            @if($simRequest->validator)
                                <dt class="sr-dt">Validateur</dt>
                                <dd class="sr-dd">{{ $simRequest->validator->full_name }}</dd>
                            @endif
                            @if($simRequest->admin)
                                <dt class="sr-dt">Géré par</dt>
                                <dd class="sr-dd">{{ $simRequest->admin->full_name }}</dd>
                            @endif
                        </dl>
                        @if($simRequest->admin_comment)
                            <div class="sr-info-box mt-3">
                                <i class="bi bi-chat-left-text me-1"></i>
                                <strong>Commentaire admin :</strong> {{ $simRequest->admin_comment }}
                            </div>
                        @endif
                        @if($simRequest->rejection_reason)
                            <div class="sr-danger-box mt-3">
                                <i class="bi bi-x-circle me-1"></i>
                                <strong>Raison du rejet :</strong> {{ $simRequest->rejection_reason }}
                            </div>
                        @endif
                    </div>
                @endif

            </div>
        </div>

        {{-- Activité récente --}}
        @if($simRequest->histories->count())
        <div class="sr-card">
            <div class="sr-card-header">
                <h2 class="sr-card-title"><i class="bi bi-lightning-charge"></i> Activité récente</h2>
            </div>
            <div class="sr-card-body">
                <ul class="sr-timeline">
                    @foreach($simRequest->histories->sortByDesc('created_at')->take(4) as $history)
                        @php
                            $ac = $actionColors[$history->action] ?? ['bg'=>'#f3f4f6','text'=>'#6b7280','icon'=>'bi-circle'];
                        @endphp
                        <li class="sr-timeline-item">
                            <div class="sr-timeline-dot" style="background:{{ $ac['bg'] }};color:{{ $ac['text'] }};border-color:{{ $ac['bg'] }};">
                                <i class="bi {{ $ac['icon'] }}" style="font-size:12px;"></i>
                            </div>
                            <div class="sr-timeline-body">
                                <div class="sr-timeline-action">{{ $history->action_label }}</div>
                                <div class="sr-timeline-meta">
                                    {{ $history->created_at->format('d/m/Y H:i') }}
                                    @if($history->user || $history->user_matricule)
                                        · {{ $history->user ? $history->user->full_name : $history->user_matricule }}
                                    @endif
                                </div>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
        @endif

        {{-- Historique complet --}}
        <div class="sr-card">
            <div class="sr-card-header">
                <h2 class="sr-card-title"><i class="bi bi-clock-history"></i> Historique complet</h2>
            </div>
            <div style="overflow-x:auto;">
                <table class="sr-table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Action</th>
                            <th>Utilisateur</th>
                            <th>Détails</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($simRequest->histories->sortByDesc('created_at') as $history)
                            @php $ac = $actionColors[$history->action] ?? ['bg'=>'#f3f4f6','text'=>'#6b7280','icon'=>'bi-circle']; @endphp
                            <tr>
                                <td style="color:var(--muted);white-space:nowrap;">{{ $history->created_at->format('d/m/Y H:i') }}</td>
                                <td>
                                    <span class="sr-badge" style="background:{{ $ac['bg'] }};color:{{ $ac['text'] }};">
                                        {{ $history->action_label }}
                                    </span>
                                </td>
                                <td>{{ $history->user ? $history->user->full_name : ($history->user_matricule ?? '—') }}</td>
                                <td style="color:var(--muted);">
                                    @if($history->changes_summary)
                                        {{ $history->changes_summary }}
                                    @elseif($history->notes)
                                        {{ \Illuminate\Support\Str::limit($history->notes, 60) }}
                                    @else
                                        —
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" style="text-align:center;padding:24px;color:var(--muted);">Aucun historique</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>{{-- /left --}}

    {{-- ── Right column (actions) ──────────────────────────────── --}}
    <div class="sr-sticky">

        {{-- Mes actions (demandeur) --}}
        @if((auth()->user()->id === $simRequest->user_id || auth()->user()->id === $simRequest->created_by) && ($simRequest->isEnAttente() || $simRequest->isPending()))
            <div class="sr-action">
                <div class="sr-action-header blue">
                    <i class="bi bi-person-lines-fill"></i> Mes actions
                </div>
                <div class="sr-action-body">
                    <form method="POST" action="{{ route('sim-requests.cancel', $simRequest) }}"
                          data-confirm="Êtes-vous sûr de vouloir annuler cette demande ? Cette action est irréversible."
                          data-confirm-variant="danger" data-confirm-text="Annuler">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="sr-btn sr-btn-outline-danger">
                            <i class="bi bi-x-circle"></i> Annuler ma demande
                        </button>
                    </form>
                </div>
            </div>
        @endif

        {{-- Validation (validateur, récupération en attente) --}}
        @if($simRequest->isRecuperation() && $simRequest->isEnAttente() && auth()->user()->canValidateRequests() && $simRequest->created_by !== auth()->id())
            <div class="sr-action">
                <div class="sr-action-header amber">
                    <i class="bi bi-patch-check"></i> Validation
                </div>
                <div class="sr-action-body">
                    <form method="POST" action="{{ route('sim-requests.approve', $simRequest) }}">
                        @csrf
                        <textarea name="notes" class="sr-input mb-2" rows="2" placeholder="Notes (optionnel)"></textarea>
                        <button type="submit" class="sr-btn sr-btn-success">
                            <i class="bi bi-check-circle"></i> Approuver
                        </button>
                    </form>
                    <div style="height:8px;"></div>
                    <form method="POST" action="{{ route('sim-requests.reject', $simRequest) }}"
                          data-confirm="Confirmer le rejet de cette demande ?"
                          data-confirm-variant="danger" data-confirm-text="Rejeter">
                        @csrf
                        <textarea name="rejection_reason" class="sr-input mb-2" rows="2" placeholder="Raison du rejet *" required></textarea>
                        <button type="submit" class="sr-btn sr-btn-danger">
                            <i class="bi bi-x-circle"></i> Rejeter
                        </button>
                    </form>
                    <div style="height:8px;"></div>
                    <form method="POST" action="{{ route('sim-requests.destroy', $simRequest) }}"
                          data-confirm="Supprimer définitivement cette demande ?"
                          data-confirm-variant="danger" data-confirm-text="Supprimer">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="sr-btn sr-btn-outline-danger">
                            <i class="bi bi-trash"></i> Supprimer
                        </button>
                    </form>
                </div>
            </div>
        @endif

        {{-- Livraison (validateur) --}}
        @if(auth()->user()->isValidator())
            <div class="sr-action">
                <div class="sr-action-header green">
                    <i class="bi bi-box-seam"></i> Livraison
                </div>
                <div class="sr-action-body">
                    @if($simRequest->isDelivered())
                        <form method="POST" action="{{ route('sim-requests.toggle-delivered', $simRequest) }}"
                              data-confirm="Retirer la marque « livré » ?"
                              data-confirm-variant="secondary" data-confirm-text="Retirer">
                            @csrf
                            <button type="submit" class="sr-btn sr-btn-outline">
                                <i class="bi bi-box-seam"></i> Retirer « livré »
                            </button>
                        </form>
                    @else
                        <form method="POST" action="{{ route('sim-requests.toggle-delivered', $simRequest) }}"
                              data-confirm="Marquer cette demande comme livrée ?"
                              data-confirm-variant="success" data-confirm-text="Marquer livré">
                            @csrf
                            <button type="submit" class="sr-btn sr-btn-success">
                                <i class="bi bi-check-circle"></i> Marquer comme livrée
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        @endif

        {{-- Bordereau (validateur) --}}
        @if(auth()->user()->isValidator())
            <div class="sr-action">
                <div class="sr-action-header indigo">
                    <i class="bi bi-file-earmark-text"></i> Bordereau
                </div>
                <div class="sr-action-body">
                    @if($simRequest->status === 'accepted')
                        <a href="{{ route('sim-requests.bordereau', $simRequest) }}"
                           class="sr-btn sr-btn-primary" target="_blank" style="text-decoration:none;">
                            <i class="bi bi-file-text"></i> Voir le bordereau
                        </a>
                    @else
                        <button class="sr-btn sr-btn-disabled" disabled title="Disponible pour les demandes acceptées">
                            <i class="bi bi-file-text"></i> Voir le bordereau
                        </button>
                    @endif
                </div>
            </div>
        @endif

        {{-- Actions Admin --}}
        @if(auth()->user()->isAdmin())
            <div class="sr-action">
                <div class="sr-action-header green">
                    <i class="bi bi-gear-wide-connected"></i> Actions Admin
                </div>
                <div class="sr-action-body">
                    @if(!$simRequest->isRejetee())
                        <form method="POST" action="{{ route('sim-requests.submit-webhook', $simRequest) }}"
                              class="mb-3"
                              data-confirm="Soumettre cette demande au webhook ? Un email sera envoyé."
                              data-confirm-variant="primary" data-confirm-text="Soumettre">
                            @csrf
                            <button type="submit" class="sr-btn sr-btn-success">
                                <i class="bi bi-send"></i> Soumettre à l'opérateur
                            </button>
                        </form>
                    @endif
                    <form method="POST" action="{{ route('sim-requests.admin-action', $simRequest) }}">
                        @csrf
                        <div class="mb-2">
                            <select name="status" class="sr-input" required>
                                <option value="pending"  {{ $simRequest->status === 'pending'  ? 'selected' : '' }}>Pending</option>
                                <option value="accepted" {{ $simRequest->status === 'accepted' ? 'selected' : '' }}>Accepted</option>
                                <option value="refused"  {{ $simRequest->status === 'refused'  ? 'selected' : '' }}>Refused</option>
                            </select>
                        </div>
                        <div class="mb-2">
                            <textarea name="admin_comment" class="sr-input" rows="2"
                                      placeholder="Commentaire...">{{ old('admin_comment', $simRequest->admin_comment) }}</textarea>
                        </div>
                        <button type="submit" class="sr-btn sr-btn-primary">
                            <i class="bi bi-check-circle"></i> Mettre à jour
                        </button>
                    </form>
                </div>
            </div>
        @endif

    </div>{{-- /right --}}

</div>{{-- /sr-grid --}}

<div class="mt-2 mb-4">
    <a href="{{ route('sim-requests.index') }}" class="sr-back">
        <i class="bi bi-arrow-left"></i> Retour à la liste
    </a>
</div>
@endsection

@push('scripts')
<script>
function toggleFavorite(requestId, button) {
    fetch(`{{ url('sim-requests') }}/${requestId}/toggle-favorite`, {
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
        const icon = button.querySelector('i');
        if (data.is_favorite) {
            button.classList.add('active');
            icon.className = 'bi bi-star-fill';
            button.innerHTML = '<i class="bi bi-star-fill"></i> Favori';
        } else {
            button.classList.remove('active');
            icon.className = 'bi bi-star';
            button.innerHTML = '<i class="bi bi-star"></i> Ajouter';
        }
    })
    .catch(() => {
        if (window.showToast) showToast('Erreur lors de la mise à jour des favoris', 'error');
    });
}
</script>
@endpush
