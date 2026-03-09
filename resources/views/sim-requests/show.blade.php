@extends('layouts.bootstrap')

@section('title', 'Détails de la demande')
@section('page-title', 'Demande #' . $simRequest->request_number)

@section('content')
<style>
.sim-request-page { --sr-card-radius: 12px; --sr-shadow: 0 1px 3px rgba(0,0,0,0.06); --sr-shadow-hover: 0 4px 12px rgba(0,0,0,0.08); }
.sim-request-page .card { border: none; border-radius: var(--sr-card-radius); box-shadow: var(--sr-shadow); transition: box-shadow 0.2s; }
.sim-request-page .card:hover { box-shadow: var(--sr-shadow-hover); }
.sim-request-page .action-panel { position: sticky; top: 90px; }
.sim-request-page .action-card { border-radius: var(--sr-card-radius); border: none; overflow: hidden; margin-bottom: 0.75rem; }
.sim-request-page .action-card .card-header { font-weight: 600; font-size: 0.9rem; padding: 0.6rem 0.85rem; }
.sim-request-page .action-card .card-body { padding: 0.75rem 0.85rem; }
.sim-request-page .btn-action-main { border-radius: 8px; padding: 0.38rem 0.65rem; font-weight: 500; }
.sim-request-page .btn-action-mini { border-radius: 8px; padding: 0.35rem 0.5rem; line-height: 1; }
.sim-request-page .action-card .form-control,
.sim-request-page .action-card .form-select { font-size: 0.875rem; }
.sim-request-page .action-card .form-control::placeholder { font-size: 0.875rem; }
.sim-request-page .action-card .btn { font-size: 0.9rem; }
.sim-request-page .accordion-button { padding: 0.65rem 0.85rem; font-size: 0.95rem; }
.sim-request-page .accordion-body { padding: 0.75rem 0.85rem; }
.sim-request-page .accordion-button:not(.collapsed) { background: #f8fafc; color: #0f172a; }
.sim-request-page .group-badge { font-size: 0.75rem; padding: 0.25rem 0.5rem; }
.sim-request-page .info-dt { color: #64748b; font-weight: 500; font-size: 0.875rem; }
.sim-request-page .info-dd { color: #1e293b; font-size: 0.9375rem; }

.sim-request-page .sr-shell { display: grid; grid-template-columns: 1fr 360px; gap: 16px; align-items: start; }
.sim-request-page .sr-topbar { display: flex; align-items: center; justify-content: space-between; gap: 12px; margin-bottom: 12px; }
.sim-request-page .sr-topbar .sr-title { font-size: 18px; font-weight: 650; color: #0f172a; }
.sim-request-page .sr-topbar .sr-subtitle { font-size: 12px; color: #64748b; }
.sim-request-page .sr-panel { background: rgba(255,255,255,0.75); backdrop-filter: blur(6px); border: 1px solid rgba(226,232,240,0.9); border-radius: var(--sr-card-radius); box-shadow: var(--sr-shadow); overflow: hidden; }
.sim-request-page .sr-panel-header { padding: 12px 14px; background: linear-gradient(180deg, rgba(248,250,252,1) 0%, rgba(255,255,255,1) 100%); border-bottom: 1px solid #e2e8f0; }
.sim-request-page .sr-panel-body { padding: 12px 14px; }
.sim-request-page .sr-panel + .sr-panel { margin-top: 12px; }

@media (max-width: 992px) {
  .sim-request-page .sr-shell { grid-template-columns: 1fr; }
  .sim-request-page .action-panel { position: static; top: auto; }
}
</style>

@if(request()->has('from') && request()->from === 'notification')
    <div class="alert alert-info alert-dismissible fade show sim-request-page" role="alert" style="border-left: 4px solid #3b82f6; background: #eff6ff; border-radius: 10px; margin-bottom: 1.25rem;">
        <div class="d-flex align-items-center">
            <i class="bi bi-info-circle me-2" style="font-size: 1.25rem; color: #3b82f6;"></i>
            <div>
                <strong>Rappel de notification</strong>
                <p class="mb-0" style="font-size: 0.875rem;">Vous avez été redirigé depuis une notification. Cette demande nécessite une action.</p>
            </div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if($simRequest->isGrouped() && $simRequest->groupMembers->count() > 1)
    <div class="card mb-3 sim-request-page border-primary" style="border-width: 1px !important;">
        <div class="card-header bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-between">
            <span><i class="bi bi-collection me-2"></i>Demandes du même groupe ({{ $simRequest->groupMembers->count() }})</span>
            <span class="badge bg-primary">{{ $simRequest->groupMembers->count() }} carte(s) SIM</span>
        </div>
        <div class="card-body py-2">
            <div class="d-flex flex-wrap gap-2 align-items-center">
                @foreach($simRequest->groupMembers as $member)
                    <a href="{{ route('sim-requests.show', $member) }}" class="btn btn-sm {{ $member->id === $simRequest->id ? 'btn-primary' : 'btn-outline-primary' }}" style="border-radius: 8px;">
                        {{ $member->request_number }} @if($member->sim) — {{ $member->sim->iccid }} @endif
                    </a>
                @endforeach
            </div>
        </div>
    </div>
@endif

<div class="sim-request-page">
    @php
        $statusLabelsTop = [
            'en_attente' => 'En attente',
            'validee' => 'Validée',
            'rejetee' => 'Rejetée',
            'demande_envoyee' => 'Demande envoyée',
            'pending' => 'En attente (opérateur)',
            'refused' => 'Refusée (opérateur)',
            'accepted' => 'Acceptée (opérateur)',
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
    @endphp

    <div class="sr-topbar">
        <div>
            <div class="sr-title">Demande #{{ $simRequest->request_number }}</div>
            <div class="sr-subtitle">{{ $statusLabelsTop[$simRequest->status] ?? $simRequest->status }} • Créée le {{ $simRequest->created_at->format('d/m/Y H:i') }}</div>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            @if($canCopy)
                <a href="{{ route('sim-requests.create', ['copy_from' => $simRequest->id]) }}" class="btn btn-sm btn-outline-primary btn-action-main" title="Reprendre cette demande">
                    <i class="bi bi-arrow-repeat"></i> Reprendre
                </a>
            @endif
            @if($canEdit)
                <a href="{{ route('sim-requests.edit', $simRequest) }}" class="btn btn-sm btn-primary btn-action-main" title="Modifier la demande">
                    <i class="bi bi-pencil"></i> Modifier
                </a>
            @endif
            <button type="button"
                    class="btn btn-sm {{ $isFavorite ? 'btn-warning' : 'btn-outline-warning' }} btn-action-main"
                    title="{{ $isFavorite ? 'Retirer des favoris' : 'Ajouter aux favoris' }}"
                    onclick="toggleFavorite({{ $simRequest->id }}, this)">
                <i class="bi {{ $isFavorite ? 'bi-star-fill' : 'bi-star' }}"></i> Favori
            </button>
        </div>
    </div>

    <div class="sr-shell">
        <div>
            <div class="sr-panel">
                <div class="sr-panel-header">
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-file-text text-primary"></i>
                            <span style="font-weight: 650;">Informations de la demande</span>
                        </div>
                        @if($simRequest->status === 'en_attente')
                            @php
                                $daysPending = now()->diffInDays($simRequest->created_at);
                                $urgencyLevel = $daysPending >= 7 ? 'urgent' : ($daysPending >= 5 ? 'high' : ($daysPending >= 3 ? 'normal' : 'low'));
                                $urgencyColors = [
                                    'urgent' => '#ef4444',
                                    'high' => '#f59e0b',
                                    'normal' => '#3b82f6',
                                    'low' => 'transparent',
                                ];
                            @endphp
                            @if($urgencyLevel !== 'low')
                                <span class="badge" style="background: {{ $urgencyColors[$urgencyLevel] }}; color: white; padding: 4px 8px; border-radius: 6px; font-size: 11px; font-weight: 600;" title="En attente depuis {{ $daysPending }} jour(s)">
                                    <i class="bi bi-clock-history"></i> {{ $daysPending }} jour(s)
                                </span>
                            @endif
                        @endif
                    </div>
                </div>
                <div class="sr-panel-body">
                @php
                    $typeLabels = [
                        'recuperation' => 'Récupération',
                        'creation' => 'Création',
                        'suspension' => 'Suspension',
                        'desactivation' => 'Désactivation',
                        'ajustement' => 'Ajustement',
                    ];
                    $typeColors = [
                        'recuperation' => 'warning',
                        'creation' => 'success',
                        'suspension' => 'info',
                        'desactivation' => 'danger',
                        'ajustement' => 'primary',
                    ];
                    $statusLabels = [
                        'en_attente' => 'En attente',
                        'validee' => 'Validée',
                        'rejetee' => 'Rejetée',
                        'demande_envoyee' => 'Demande envoyée',
                        'pending' => 'En attente (opérateur)',
                        'refused' => 'Refusée (opérateur)',
                        'accepted' => 'Acceptée (opérateur)',
                    ];
                    $statusColors = [
                        'en_attente' => 'warning',
                        'validee' => 'success',
                        'rejetee' => 'danger',
                        'demande_envoyee' => 'info',
                        'pending' => 'info',
                        'refused' => 'danger',
                        'accepted' => 'success',
                    ];
                    $collaboratorUser = $simRequest->collaborator_matricule ? \App\Models\User::where('matricule', $simRequest->collaborator_matricule)->first() : null;
                    $beneficiaryUser = $simRequest->beneficiary_matricule ? \App\Models\User::where('matricule', $simRequest->beneficiary_matricule)->first() : null;
                @endphp

                <div class="accordion" id="requestInfoAccordion">
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="requestInfoGeneralHeading">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#requestInfoGeneral" aria-expanded="true" aria-controls="requestInfoGeneral">
                                <i class="bi bi-info-circle me-2 text-primary"></i>Général
                            </button>
                        </h2>
                        <div id="requestInfoGeneral" class="accordion-collapse collapse show" aria-labelledby="requestInfoGeneralHeading" data-bs-parent="#requestInfoAccordion">
                            <div class="accordion-body">
                                <dl class="row mb-0">
                                    <dt class="col-sm-4 info-dt mb-2">N° Demande</dt>
                                    <dd class="col-sm-8 info-dd mb-3"><strong>{{ $simRequest->request_number }}</strong></dd>

                                    <dt class="col-sm-4 info-dt mb-2">Type</dt>
                                    <dd class="col-sm-8 mb-3">
                                        <span class="badge bg-{{ $typeColors[$simRequest->request_type] ?? 'secondary' }}">
                                            {{ $typeLabels[$simRequest->request_type] ?? ucfirst($simRequest->request_type) }}
                                        </span>
                                    </dd>

                                    <dt class="col-sm-4 info-dt mb-2">Statut</dt>
                                    <dd class="col-sm-8 mb-3">
                                        <span class="badge bg-{{ $statusColors[$simRequest->status] ?? 'secondary' }}">
                                            {{ $statusLabels[$simRequest->status] ?? ucfirst($simRequest->status) }}
                                        </span>
                                    </dd>

                                    @if($simRequest->creator)
                                        <dt class="col-sm-4 info-dt mb-2">Demandeur / Créé par</dt>
                                        <dd class="col-sm-8 mb-3">
                                            <div class="d-flex align-items-center gap-2">
                                                <img src="{{ $simRequest->creator->avatar }}" alt="" class="rounded-circle" style="width: 28px; height: 28px; object-fit: cover;">
                                                <span class="info-dd">{{ $simRequest->creator->full_name }}</span>
                                                @if($simRequest->creator->matricule)
                                                    <small class="text-muted">({{ $simRequest->creator->matricule }})</small>
                                                @endif
                                            </div>
                                        </dd>
                                    @endif

                                    @if($simRequest->isRecuperation())
                                        <dt class="col-sm-4 info-dt mb-2">Utilisateur</dt>
                                        <dd class="col-sm-8 mb-3">
                                            @if($simRequest->user)
                                                <div class="d-flex align-items-center gap-2">
                                                    <img src="{{ $simRequest->user->avatar }}" alt="" class="rounded-circle" style="width: 28px; height: 28px; object-fit: cover;">
                                                    <span class="info-dd">{{ $simRequest->user->full_name }} ({{ $simRequest->user->matricule }})</span>
                                                </div>
                                            @else
                                                —
                                            @endif
                                        </dd>
                                    @endif

                                    @if(!$simRequest->isCreation() && ($simRequest->collaborator_matricule || $simRequest->collaborator_name || $simRequest->collaborator_first_name || $simRequest->collaborator_agence))
                                        <dt class="col-sm-4 info-dt mb-2">Collaborateur</dt>
                                        <dd class="col-sm-8 mb-3">
                                            <div class="d-flex align-items-center gap-2">
                                                @if($collaboratorUser)
                                                    <img src="{{ $collaboratorUser->avatar }}" alt="" class="rounded-circle" style="width: 28px; height: 28px; object-fit: cover;">
                                                @endif
                                                <div class="info-dd">
                                                    @if($simRequest->collaborator_name || $simRequest->collaborator_first_name)
                                                        <strong>{{ trim(($simRequest->collaborator_name ?? '') . ' ' . ($simRequest->collaborator_first_name ?? '')) }}</strong>
                                                    @endif
                                                    @if($simRequest->collaborator_matricule)
                                                        <div class="text-muted small">Matricule: {{ $simRequest->collaborator_matricule }}</div>
                                                    @endif
                                                    @if($simRequest->collaborator_agence)
                                                        <div class="text-muted small">Agence: {{ $simRequest->collaborator_agence }}</div>
                                                    @endif
                                                </div>
                                            </div>
                                        </dd>
                                    @endif

                                    @if($simRequest->isCreation())
                                        <dt class="col-sm-4 info-dt mb-2">Bénéficiaire</dt>
                                        <dd class="col-sm-8 mb-3">
                                            <div class="d-flex align-items-center gap-2">
                                                @if($beneficiaryUser)
                                                    <img src="{{ $beneficiaryUser->avatar }}" alt="" class="rounded-circle" style="width: 28px; height: 28px; object-fit: cover;">
                                                @endif
                                                <div class="info-dd">
                                                    <strong>{{ $simRequest->beneficiary_name }}</strong>
                                                    @if($simRequest->beneficiary_first_name)
                                                        {{ $simRequest->beneficiary_first_name }}
                                                    @endif
                                                    @if($simRequest->beneficiary_matricule)
                                                        <div class="text-muted small">Matricule: {{ $simRequest->beneficiary_matricule }}</div>
                                                    @endif
                                                    @if($simRequest->beneficiary_fonction)
                                                        <div class="text-muted small">Fonction: {{ $simRequest->beneficiary_fonction }}</div>
                                                    @endif
                                                </div>
                                            </div>
                                        </dd>
                                    @endif

                                    <dt class="col-sm-4 info-dt mb-2">Date de création</dt>
                                    <dd class="col-sm-8 mb-3">{{ $simRequest->created_at->format('d/m/Y H:i') }}</dd>

                                    @if($simRequest->validated_at)
                                        <dt class="col-sm-4 info-dt mb-2">Date de validation</dt>
                                        <dd class="col-sm-8 mb-3">{{ $simRequest->validated_at->format('d/m/Y H:i') }}</dd>
                                    @endif

                                    @if($simRequest->admin_processed_at)
                                        <dt class="col-sm-4 info-dt mb-2">Traitement admin</dt>
                                        <dd class="col-sm-8 mb-3">{{ $simRequest->admin_processed_at->format('d/m/Y H:i') }}</dd>
                                    @endif

                                    <dt class="col-sm-4 info-dt mb-2">Livré</dt>
                                    <dd class="col-sm-8 mb-0">
                                        @if($simRequest->isDelivered())
                                            <span class="badge bg-success">Livré</span> <span class="text-muted small">({{ $simRequest->delivered_at->format('d/m/Y H:i') }})</span>
                                        @else
                                            <span class="text-muted">Non livré</span>
                                        @endif
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </div>

                    <div class="accordion-item">
                        <h2 class="accordion-header" id="requestInfoDetailsHeading">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#requestInfoDetails" aria-expanded="false" aria-controls="requestInfoDetails">
                                <i class="bi bi-list-check me-2 text-primary"></i>Détails
                            </button>
                        </h2>
                        <div id="requestInfoDetails" class="accordion-collapse collapse" aria-labelledby="requestInfoDetailsHeading" data-bs-parent="#requestInfoAccordion">
                            <div class="accordion-body">
                                <dl class="row mb-0">
                                    @if($simRequest->phone_number || ($simRequest->sim && $simRequest->sim->phone_number))
                                        <dt class="col-sm-4 info-dt mb-2">Ligne concernée</dt>
                                        <dd class="col-sm-8 mb-3">{{ $simRequest->phone_number ?? $simRequest->sim->phone_number }}</dd>
                                    @endif

                                    @if($simRequest->sim)
                                        <dt class="col-sm-4 info-dt mb-2">SIM</dt>
                                        <dd class="col-sm-8 mb-3">
                                            <a href="{{ route('sims.show', $simRequest->sim) }}">{{ $simRequest->sim->iccid }}</a>
                                        </dd>
                                    @endif

                                    @if($simRequest->requested_iccid)
                                        <dt class="col-sm-4 info-dt mb-2">ICCID demandé</dt>
                                        <dd class="col-sm-8 mb-3">{{ $simRequest->requested_iccid }}</dd>
                                    @endif

                                    @if($simRequest->plan || $simRequest->limite_credit !== null || $simRequest->limite_data !== null)
                                        <dt class="col-sm-4 info-dt mb-2">Forfait</dt>
                                        <dd class="col-sm-8 mb-3">
                                            @if($simRequest->plan)
                                                <strong>{{ $simRequest->plan->name }}</strong><br>
                                            @endif
                                            @if($simRequest->is_temporary)
                                                <span class="badge bg-warning text-dark mb-2">
                                                    <i class="bi bi-clock"></i> Ajustement temporaire
                                                </span>
                                                <div class="text-muted small">
                                                    @if($simRequest->temporary_start_date)
                                                        <strong>Début:</strong> {{ $simRequest->temporary_start_date->format('d/m/Y') }}<br>
                                                    @endif
                                                    <strong>Fin:</strong> {{ $simRequest->temporary_end_date ? $simRequest->temporary_end_date->format('d/m/Y') : 'Non définie' }}
                                                    @if($simRequest->temporary_end_date && $simRequest->temporary_end_date->isPast())
                                                        <span class="badge bg-danger ms-2">Expiré</span>
                                                    @elseif($simRequest->temporary_end_date && $simRequest->temporary_end_date->diffInDays(now()) <= 7)
                                                        <span class="badge bg-warning text-dark ms-2">Expire bientôt</span>
                                                    @endif
                                                </div>
                                            @endif
                                            <div class="text-muted small mt-1">
                                                Limite crédit:
                                                @if($simRequest->limite_credit !== null)
                                                    {{ number_format($simRequest->limite_credit, 0, ',', ' ') }} XOF
                                                @else
                                                    <span class="text-muted">Inchangé</span>
                                                @endif
                                                <br>
                                                Limite data:
                                                @if($simRequest->limite_data !== null)
                                                    {{ $simRequest->limite_data }} GB
                                                @else
                                                    <span class="text-muted">Inchangé</span>
                                                @endif
                                            </div>
                                        </dd>
                                    @endif

                                    @if($simRequest->motif)
                                        <dt class="col-sm-4 info-dt mb-2">Motif</dt>
                                        <dd class="col-sm-8 mb-3">{{ $simRequest->motif }}</dd>
                                    @endif

                                    @if($simRequest->justification)
                                        <dt class="col-sm-4 info-dt mb-2">Justification</dt>
                                        <dd class="col-sm-8 mb-3">{{ $simRequest->justification }}</dd>
                                    @endif

                                    @if($simRequest->validator)
                                        <dt class="col-sm-4 info-dt mb-2">Validateur</dt>
                                        <dd class="col-sm-8 mb-3">{{ $simRequest->validator->full_name }}</dd>
                                    @endif

                                    @if($simRequest->admin)
                                        <dt class="col-sm-4 info-dt mb-2">Géré par</dt>
                                        <dd class="col-sm-8 mb-3">{{ $simRequest->admin->full_name }}</dd>
                                    @endif

                                    @if($simRequest->admin_comment)
                                        <dt class="col-sm-4 info-dt mb-2">Commentaire admin</dt>
                                        <dd class="col-sm-8 mb-3">
                                            <div class="alert alert-info mb-0 py-2 px-3">{{ $simRequest->admin_comment }}</div>
                                        </dd>
                                    @endif

                                    @if($simRequest->rejection_reason)
                                        <dt class="col-sm-4 info-dt mb-2">Raison du rejet</dt>
                                        <dd class="col-sm-8 mb-0 text-danger">{{ $simRequest->rejection_reason }}</dd>
                                    @endif
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            </div>
        </div>

        <div class="action-panel">
        {{-- Actions pour le demandeur --}}
        @if((auth()->user()->id === $simRequest->user_id || auth()->user()->id === $simRequest->created_by) && ($simRequest->isEnAttente() || $simRequest->isPending()))
            <div class="card action-card">
                <div class="card-header bg-info text-white">
                    <i class="bi bi-person-lines-fill me-2"></i>Mes actions
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('sim-requests.cancel', $simRequest) }}" data-confirm="Êtes-vous sûr de vouloir annuler cette demande ? Cette action est irréversible." data-confirm-variant="danger" data-confirm-text="Annuler">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-warning w-100 btn-action-main">
                            <i class="bi bi-x-circle"></i> Annuler ma demande
                        </button>
                    </form>
                </div>
            </div>
        @endif

        {{-- Livraison (validateur) --}}
        @if(auth()->user()->isValidator())
            <div class="card action-card">
                <div class="card-header" style="background: #f0fdf4; color: #166534;">
                    <i class="bi bi-box-seam me-2"></i>Livraison
                </div>
                <div class="card-body">
                    @if($simRequest->isDelivered())
                        <form method="POST" action="{{ route('sim-requests.toggle-delivered', $simRequest) }}" data-confirm="Retirer la marque « livré » ?" data-confirm-variant="secondary" data-confirm-text="Retirer">
                            @csrf
                            <button type="submit" class="btn btn-outline-secondary w-100 btn-action-main">
                                <i class="bi bi-box-seam"></i> Retirer « livré »
                            </button>
                        </form>
                    @else
                        <form method="POST" action="{{ route('sim-requests.toggle-delivered', $simRequest) }}" data-confirm="Marquer cette demande comme livrée ?" data-confirm-variant="success" data-confirm-text="Marquer livré">
                            @csrf
                            <button type="submit" class="btn btn-success w-100 btn-action-main">
                                <i class="bi bi-check-circle"></i> Marquer comme livrée
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        @endif

        {{-- Validation (validateur, récupération uniquement) --}}
        @if($simRequest->isRecuperation() && $simRequest->isEnAttente() && auth()->user()->canValidateRequests() && $simRequest->created_by !== auth()->id())
            <div class="card action-card">
                <div class="card-header" style="background: #fef3c7; color: #92400e;">
                    <i class="bi bi-patch-check me-2"></i>Validation
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('sim-requests.approve', $simRequest) }}" class="mb-2">
                        @csrf
                        <div class="mb-2">
                            <textarea name="notes" class="form-control form-control-sm" rows="2" placeholder="Notes (optionnel)"></textarea>
                        </div>
                        <button type="submit" class="btn btn-success w-100 btn-action-main">
                            <i class="bi bi-check-circle"></i> Approuver
                        </button>
                    </form>
                    <form method="POST" action="{{ route('sim-requests.reject', $simRequest) }}" class="mb-2" data-confirm="Confirmer le rejet de cette demande ?" data-confirm-variant="danger" data-confirm-text="Rejeter">
                        @csrf
                        <div class="mb-2">
                            <textarea name="rejection_reason" class="form-control form-control-sm" rows="2" placeholder="Raison du rejet *" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-danger w-100 btn-action-main">
                            <i class="bi bi-x-circle"></i> Rejeter
                        </button>
                    </form>
                    <form method="POST" action="{{ route('sim-requests.destroy', $simRequest) }}" data-confirm="Supprimer définitivement cette demande ?" data-confirm-variant="danger" data-confirm-text="Supprimer">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger w-100 btn-action-main">
                            <i class="bi bi-trash"></i> Supprimer
                        </button>
                    </form>
                </div>
            </div>
        @endif

        {{-- Bordereau (validateur) --}}
        @if(auth()->user()->isValidator())
            <div class="card action-card">
                <div class="card-header" style="background: #eff6ff; color: #1e40af;">
                    <i class="bi bi-file-earmark-text me-2"></i>Bordereau
                </div>
                <div class="card-body">
                    @if($simRequest->status === 'accepted')
                        <a href="{{ route('sim-requests.bordereau', $simRequest) }}" class="btn btn-outline-primary w-100 btn-action-main" target="_blank">
                            <i class="bi bi-file-text"></i> Voir le bordereau
                        </a>
                    @else
                        <button class="btn btn-secondary w-100 btn-action-main" disabled title="Disponible pour les demandes acceptées">
                            <i class="bi bi-file-text"></i> Voir le bordereau
                        </button>
                    @endif
                </div>
            </div>
        @endif

        {{-- Admin : webhook + statuts --}}
        @if(auth()->user()->isAdmin())
            <div class="card action-card">
                <div class="card-header bg-primary text-white">
                    <i class="bi bi-gear-wide-connected me-2"></i>Actions Admin
                </div>
                <div class="card-body">
                    @if(!$simRequest->isRejetee())
                        <form method="POST" action="{{ route('sim-requests.submit-webhook', $simRequest) }}" class="mb-3" data-confirm="Soumettre cette demande au webhook ? Un email sera envoyé." data-confirm-variant="primary" data-confirm-text="Soumettre">
                            @csrf
                            <button type="submit" class="btn btn-success w-100 btn-action-main">
                                <i class="bi bi-send"></i> Soumettre à l’opérateur
                            </button>
                        </form>
                    @endif
                    <form method="POST" action="{{ route('sim-requests.admin-action', $simRequest) }}">
                        @csrf
                        <div class="mb-2">
                            <select name="status" id="admin_status" class="form-select form-select-sm" required>
                                <option value="pending" {{ $simRequest->status === 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="accepted" {{ $simRequest->status === 'accepted' ? 'selected' : '' }}>Accepted</option>
                                <option value="refused" {{ $simRequest->status === 'refused' ? 'selected' : '' }}>Refused</option>
                            </select>
                        </div>
                        <div class="mb-2">
                            <textarea name="admin_comment" id="admin_comment" class="form-control form-control-sm" rows="2" placeholder="Commentaire...">{{ old('admin_comment', $simRequest->admin_comment) }}</textarea>
                        </div>
                        <button type="submit" class="btn btn-primary w-100 btn-action-main btn-sm">
                            <i class="bi bi-send"></i> Mettre à jour
                        </button>
                    </form>
                </div>
            </div>
        @endif
        </div>

        {{-- Activité/Historique déplacés dans la zone principale (layout type dashboard) --}}
        </div>

        <div>
            @php
                $recentHistories = $simRequest->histories->sortByDesc('created_at')->take(3);
            @endphp

            <div class="sr-panel">
                <div class="sr-panel-header">
                    <i class="bi bi-lightning-charge me-2"></i><span style="font-weight:650;">Activité récente</span>
                </div>
                <div class="sr-panel-body">
                    @forelse($recentHistories as $history)
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div>
                                <div style="font-weight: 600;">{{ $history->action_label }}</div>
                                <div class="text-muted" style="font-size: 12px;">{{ $history->created_at->format('d/m/Y H:i') }}</div>
                            </div>
                            <div class="text-muted" style="font-size: 12px;">
                                {{ $history->user ? $history->user->full_name : ($history->user_matricule ?? '-') }}
                            </div>
                        </div>
                    @empty
                        <span class="text-muted">Aucune activité récente</span>
                    @endforelse
                </div>
            </div>

            <div class="sr-panel">
                <div class="sr-panel-header">
                    <i class="bi bi-clock-history me-2"></i><span style="font-weight:650;">Historique</span>
                </div>
                <div class="sr-panel-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm mb-0" style="margin: 0; font-size: 12.5px;">
                            <thead style="background: #f9fafb;">
                                <tr>
                                    <th style="padding: 10px; font-weight: 600; font-size: 12px; color: #6b7280; text-transform: uppercase; letter-spacing: 0.4px;">Date</th>
                                    <th style="padding: 10px; font-weight: 600; font-size: 12px; color: #6b7280; text-transform: uppercase; letter-spacing: 0.4px;">Action</th>
                                    <th style="padding: 10px; font-weight: 600; font-size: 12px; color: #6b7280; text-transform: uppercase; letter-spacing: 0.4px;">Utilisateur</th>
                                    <th style="padding: 10px; font-weight: 600; font-size: 12px; color: #6b7280; text-transform: uppercase; letter-spacing: 0.4px;">Détails</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($simRequest->histories->sortByDesc('created_at') as $history)
                                    <tr style="border-bottom: 1px solid #e5e7eb;">
                                        <td style="padding: 10px; color: #4b5563;">{{ $history->created_at->format('d/m/Y H:i') }}</td>
                                        <td style="padding: 10px;">
                                            @php
                                                $actionColors = [
                                                    'created' => '#10b981',
                                                    'validated' => '#3b82f6',
                                                    'rejected' => '#ef4444',
                                                    'status_updated' => '#f59e0b',
                                                    'submitted_to_webhook' => '#8b5cf6',
                                                    'cancelled' => '#6b7280',
                                                ];
                                                $color = $actionColors[$history->action] ?? '#3b82f6';
                                            @endphp
                                            <span class="badge" style="background: {{ $color }}; color: white; padding: 3px 8px; border-radius: 6px; font-size: 11px; font-weight: 600;">
                                                {{ $history->action_label }}
                                            </span>
                                        </td>
                                        <td style="padding: 10px; color: #4b5563;">
                                            {{ $history->user ? $history->user->full_name : ($history->user_matricule ?? '-') }}
                                        </td>
                                        <td style="padding: 10px; color: #6b7280;">
                                            @if($history->changes_summary)
                                                {{ $history->changes_summary }}
                                            @elseif($history->notes)
                                                {{ \Illuminate\Support\Str::limit($history->notes, 50) }}
                                            @else
                                                -
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center" style="padding: 18px; color: #9ca3af;">Aucun historique</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="mt-4 sim-request-page">
    <a href="{{ route('sim-requests.index') }}" class="btn btn-outline-secondary btn-action-main">
        <i class="bi bi-arrow-left"></i> Retour à la liste
    </a>
</div>

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
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                if (window.showToast) {
                    showToast(data.message, 'success');
                }
                
                // Mettre à jour le bouton
                const icon = button.querySelector('i');
                if (data.is_favorite) {
                    button.classList.remove('btn-outline-warning');
                    button.classList.add('btn-warning');
                    icon.classList.remove('bi-star');
                    icon.classList.add('bi-star-fill');
                    button.title = 'Retirer des favoris';
                    button.innerHTML = '<i class="bi bi-star-fill"></i> Favori';
                } else {
                    button.classList.remove('btn-warning');
                    button.classList.add('btn-outline-warning');
                    icon.classList.remove('bi-star-fill');
                    icon.classList.add('bi-star');
                    button.title = 'Ajouter aux favoris';
                    button.innerHTML = '<i class="bi bi-star"></i> Ajouter aux favoris';
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);
            if (window.showToast) {
                showToast('Erreur lors de la mise à jour des favoris', 'error');
            }
        });
    }
</script>
@endsection
