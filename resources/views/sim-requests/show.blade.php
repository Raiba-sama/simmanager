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
.sim-request-page .btn-action-main { border-radius: 8px; padding: 0.42rem 0.75rem; font-weight: 500; }
.sim-request-page .btn-action-mini { border-radius: 8px; padding: 0.35rem 0.5rem; line-height: 1; }
.sim-request-page .action-card .form-control,
.sim-request-page .action-card .form-select { font-size: 0.875rem; }
.sim-request-page .action-card .form-control::placeholder { font-size: 0.875rem; }
.sim-request-page .action-card .btn { font-size: 0.9rem; }
.sim-request-page .group-badge { font-size: 0.75rem; padding: 0.25rem 0.5rem; }
.sim-request-page .info-dt { color: #64748b; font-weight: 500; font-size: 0.875rem; }
.sim-request-page .info-dd { color: #1e293b; font-size: 0.9375rem; }
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

<div class="row sim-request-page">
    <div class="col-lg-8">
        <div class="card mb-3">
            <div class="card-header" style="background: #f8fafc; border-bottom: 1px solid #e2e8f0;">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div class="d-flex align-items-center gap-2">
                        <h5 class="mb-0" style="font-weight: 600; color: #1e293b; font-size: 1.1rem;"><i class="bi bi-file-text text-primary me-1"></i>Informations de la demande</h5>
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
                    <div class="d-flex gap-2">
                        @php
                            $user = auth()->user();
                            $canEdit = false;
                            if ($user->isValidator()) {
                                $canEdit = ($simRequest->created_by === $user->id) || ($simRequest->status === 'en_attente');
                            } else {
                                $canEdit = ($simRequest->user_id === $user->id) && ($simRequest->status === 'en_attente');
                            }
                            $canEdit = $canEdit && ($simRequest->status !== 'demande_envoyee');
                        @endphp
                        @if($canEdit)
                            <a href="{{ route('sim-requests.edit', $simRequest) }}" class="btn btn-sm btn-primary btn-action-main" title="Modifier la demande">
                                <i class="bi bi-pencil"></i> Modifier
                            </a>
                        @endif
                        @php
                            $canCopy = $user->isValidator() || ($simRequest->isRecuperation() && $simRequest->user_id === $user->id);
                        @endphp
                        @if($canCopy)
                            <a href="{{ route('sim-requests.create', ['copy_from' => $simRequest->id]) }}" class="btn btn-sm btn-outline-primary btn-action-main" title="Reprendre cette demande">
                                <i class="bi bi-arrow-repeat"></i> Reprendre
                            </a>
                        @endif
                        <button type="button" 
                                class="btn btn-sm {{ $isFavorite ? 'btn-warning' : 'btn-outline-warning' }}" 
                                style="border-radius: 6px;" 
                                title="{{ $isFavorite ? 'Retirer des favoris' : 'Ajouter aux favoris' }}"
                                onclick="toggleFavorite({{ $simRequest->id }}, this)">
                            <i class="bi {{ $isFavorite ? 'bi-star-fill' : 'bi-star' }}"></i>
                            {{ $isFavorite ? ' Favori' : ' Ajouter aux favoris' }}
                        </button>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <dl class="row mb-0">
                    <dt class="col-sm-4 info-dt mb-2">N° Demande</dt>
                    <dd class="col-sm-8 info-dd mb-3"><strong>{{ $simRequest->request_number }}</strong></dd>

                    <dt class="col-sm-4 info-dt mb-2">Type</dt>
                    <dd class="col-sm-8">
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
                        @endphp
                        <span class="badge bg-{{ $typeColors[$simRequest->request_type] ?? 'secondary' }}">
                            {{ $typeLabels[$simRequest->request_type] ?? ucfirst($simRequest->request_type) }}
                        </span>
                    </dd>

                    <dt class="col-sm-4 info-dt mb-2">Statut</dt>
                    <dd class="col-sm-8">
                        @php
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
                        @endphp
                        <span class="badge bg-{{ $statusColors[$simRequest->status] ?? 'secondary' }}">
                            {{ $statusLabels[$simRequest->status] ?? ucfirst($simRequest->status) }}
                        </span>
                    </dd>

                    @php
                        $collaboratorUser = $simRequest->collaborator_matricule ? \App\Models\User::where('matricule', $simRequest->collaborator_matricule)->first() : null;
                        $beneficiaryUser = $simRequest->beneficiary_matricule ? \App\Models\User::where('matricule', $simRequest->beneficiary_matricule)->first() : null;
                    @endphp

                    @if($simRequest->creator)
                        <dt class="col-sm-4">Demandeur / Créé par:</dt>
                        <dd class="col-sm-8">
                            <div class="d-flex align-items-center gap-2">
                                <img src="{{ $simRequest->creator->avatar }}" alt="" class="rounded-circle" style="width: 36px; height: 36px; object-fit: cover;">
                                <span>{{ $simRequest->creator->full_name }}</span>
                                @if($simRequest->creator->matricule)
                                    <small class="text-muted">({{ $simRequest->creator->matricule }})</small>
                                @endif
                            </div>
                        </dd>
                    @endif

                    @if($simRequest->isRecuperation())
                        <dt class="col-sm-4">Utilisateur:</dt>
                        <dd class="col-sm-8">
                            <div class="d-flex align-items-center gap-2">
                                @if($simRequest->user)
                                    <img src="{{ $simRequest->user->avatar }}" alt="" class="rounded-circle" style="width: 36px; height: 36px; object-fit: cover;">
                                    <span>{{ $simRequest->user->full_name }} ({{ $simRequest->user->matricule }})</span>
                                @else
                                    —
                                @endif
                            </div>
                        </dd>
                    @endif

                    @if(!$simRequest->isCreation() && ($simRequest->collaborator_matricule || $simRequest->collaborator_name || $simRequest->collaborator_first_name || $simRequest->collaborator_agence))
                        <dt class="col-sm-4">Collaborateur:</dt>
                        <dd class="col-sm-8">
                            <div class="d-flex align-items-center gap-2">
                                @if($collaboratorUser)
                                    <img src="{{ $collaboratorUser->avatar }}" alt="" class="rounded-circle" style="width: 36px; height: 36px; object-fit: cover;">
                                @endif
                                <div>
                                    @if($simRequest->collaborator_name || $simRequest->collaborator_first_name)
                                        <strong>{{ trim(($simRequest->collaborator_name ?? '') . ' ' . ($simRequest->collaborator_first_name ?? '')) }}</strong>
                                    @endif
                                    @if($simRequest->collaborator_matricule)
                                        <br><small class="text-muted">Matricule: {{ $simRequest->collaborator_matricule }}</small>
                                    @endif
                                    @if($simRequest->collaborator_agence)
                                        <br><small class="text-muted">Agence: {{ $simRequest->collaborator_agence }}</small>
                                    @endif
                                </div>
                            </div>
                        </dd>
                    @endif

                    @if($simRequest->isCreation())
                        <dt class="col-sm-4">Bénéficiaire:</dt>
                        <dd class="col-sm-8">
                            <div class="d-flex align-items-center gap-2">
                                @if($beneficiaryUser)
                                    <img src="{{ $beneficiaryUser->avatar }}" alt="" class="rounded-circle" style="width: 36px; height: 36px; object-fit: cover;">
                                @endif
                                <div>
                                    <strong>{{ $simRequest->beneficiary_name }}</strong>
                                    @if($simRequest->beneficiary_first_name)
                                        {{ $simRequest->beneficiary_first_name }}
                                    @endif
                                    @if($simRequest->beneficiary_matricule)
                                        <br><small class="text-muted">Matricule: {{ $simRequest->beneficiary_matricule }}</small>
                                    @endif
                                    @if($simRequest->beneficiary_fonction)
                                        <br><small class="text-muted">Fonction: {{ $simRequest->beneficiary_fonction }}</small>
                                    @endif
                                </div>
                            </div>
                        </dd>
                    @endif

                    @if($simRequest->phone_number || ($simRequest->sim && $simRequest->sim->phone_number))
                        <dt class="col-sm-4">Ligne concernée:</dt>
                        <dd class="col-sm-8">{{ $simRequest->phone_number ?? $simRequest->sim->phone_number }}</dd>
                    @endif

                    @if($simRequest->sim)
                        <dt class="col-sm-4">SIM:</dt>
                        <dd class="col-sm-8">
                            <a href="{{ route('sims.show', $simRequest->sim) }}">{{ $simRequest->sim->iccid }}</a>
                        </dd>
                    @endif

                    @if($simRequest->requested_iccid)
                        <dt class="col-sm-4">ICCID demandé:</dt>
                        <dd class="col-sm-8">{{ $simRequest->requested_iccid }}</dd>
                    @endif

                    @if($simRequest->plan || $simRequest->limite_credit !== null || $simRequest->limite_data !== null)
                        <dt class="col-sm-4">Forfait:</dt>
                        <dd class="col-sm-8">
                            @if($simRequest->plan)
                                <strong>{{ $simRequest->plan->name }}</strong><br>
                            @endif
                            @if($simRequest->is_temporary)
                                <span class="badge bg-warning text-dark mb-2">
                                    <i class="bi bi-clock"></i> Ajustement temporaire
                                </span>
                                <br>
                                <small class="text-muted">
                                    @if($simRequest->temporary_start_date)
                                        <strong>Date de début:</strong> {{ $simRequest->temporary_start_date->format('d/m/Y') }}<br>
                                    @endif
                                    <strong>Date de fin:</strong> {{ $simRequest->temporary_end_date ? $simRequest->temporary_end_date->format('d/m/Y') : 'Non définie' }}
                                    @if($simRequest->temporary_end_date && $simRequest->temporary_end_date->isPast())
                                        <span class="badge bg-danger ms-2">Expiré</span>
                                    @elseif($simRequest->temporary_end_date && $simRequest->temporary_end_date->diffInDays(now()) <= 7)
                                        <span class="badge bg-warning text-dark ms-2">Expire bientôt</span>
                                    @endif
                                </small>
                                <br>
                            @endif
                            <small class="text-muted">
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
                            </small>
                            @if($simRequest->is_temporary && ($simRequest->previous_limite_credit !== null || $simRequest->previous_limite_data !== null || $simRequest->previous_plan_id))
                                <br>
                                <small class="text-muted mt-2 d-block">
                                    <strong>Valeurs précédentes (seront restaurées):</strong><br>
                                    @if($simRequest->previous_plan_id && $simRequest->previousPlan)
                                        Forfait: {{ $simRequest->previousPlan->name }}<br>
                                    @endif
                                    @if($simRequest->previous_limite_credit !== null)
                                        Limite crédit: {{ number_format($simRequest->previous_limite_credit, 0, ',', ' ') }} XOF<br>
                                    @endif
                                    @if($simRequest->previous_limite_data !== null)
                                        Limite data: {{ $simRequest->previous_limite_data }} GB
                                    @endif
                                </small>
                            @endif
                        </dd>
                    @endif

                    @if($simRequest->motif)
                        <dt class="col-sm-4">Motif:</dt>
                        <dd class="col-sm-8">{{ $simRequest->motif }}</dd>
                    @endif

                    @if($simRequest->justification)
                        <dt class="col-sm-4">Justification:</dt>
                        <dd class="col-sm-8">{{ $simRequest->justification }}</dd>
                    @endif

                    @if($simRequest->validator)
                        <dt class="col-sm-4">Validateur:</dt>
                        <dd class="col-sm-8">{{ $simRequest->validator->full_name }}</dd>
                    @endif

                    @if($simRequest->admin)
                        <dt class="col-sm-4">Géré par:</dt>
                        <dd class="col-sm-8">{{ $simRequest->admin->full_name }}</dd>
                    @endif

                    @if($simRequest->admin_comment)
                        <dt class="col-sm-4">Commentaire admin:</dt>
                        <dd class="col-sm-8">
                            <div class="alert alert-info mb-0">
                                {{ $simRequest->admin_comment }}
                            </div>
                        </dd>
                    @endif

                    @if($simRequest->rejection_reason)
                        <dt class="col-sm-4">Raison du rejet:</dt>
                        <dd class="col-sm-8 text-danger">{{ $simRequest->rejection_reason }}</dd>
                    @endif

                    <dt class="col-sm-4">Date de création:</dt>
                    <dd class="col-sm-8">{{ $simRequest->created_at->format('d/m/Y H:i') }}</dd>

                    @if($simRequest->validated_at)
                        <dt class="col-sm-4">Date de validation:</dt>
                        <dd class="col-sm-8">{{ $simRequest->validated_at->format('d/m/Y H:i') }}</dd>
                    @endif

                    @if($simRequest->admin_processed_at)
                        <dt class="col-sm-4">Date traitement admin:</dt>
                        <dd class="col-sm-8">{{ $simRequest->admin_processed_at->format('d/m/Y H:i') }}</dd>
                    @endif

                    <dt class="col-sm-4">Livré:</dt>
                    <dd class="col-sm-8">
                        @if($simRequest->isDelivered())
                            <span class="badge bg-success">Livré</span> le {{ $simRequest->delivered_at->format('d/m/Y H:i') }}
                        @else
                            <span class="text-muted">Non livré</span>
                        @endif
                    </dd>
                </dl>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
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
                                <i class="bi bi-send"></i> Envoyer au webhook
                            </button>
                        </form>
                    @endif
                    <label class="form-label small text-muted mb-2">Statut opérateur</label>
                    <div class="d-flex gap-2 mb-2">
                        <form method="POST" action="{{ route('sim-requests.quick-update-status', $simRequest) }}" class="flex-fill" data-confirm="Passer en Pending ?" data-confirm-variant="primary" data-confirm-text="Oui">
                            @csrf
                            <input type="hidden" name="status" value="pending">
                            <button type="submit" class="btn btn-action-mini {{ $simRequest->status === 'pending' ? 'btn-primary' : 'btn-outline-primary' }} w-100" title="Pending">
                                <i class="bi bi-clock"></i>
                            </button>
                        </form>
                        <form method="POST" action="{{ route('sim-requests.quick-update-status', $simRequest) }}" class="flex-fill" data-confirm="Passer en Accepted ?" data-confirm-variant="success" data-confirm-text="Oui">
                            @csrf
                            <input type="hidden" name="status" value="accepted">
                            <button type="submit" class="btn btn-action-mini {{ $simRequest->status === 'accepted' ? 'btn-success' : 'btn-outline-success' }} w-100" title="Accepted">
                                <i class="bi bi-check-circle"></i>
                            </button>
                        </form>
                        <form method="POST" action="{{ route('sim-requests.quick-update-status', $simRequest) }}" class="flex-fill" data-confirm="Passer en Refused ?" data-confirm-variant="danger" data-confirm-text="Oui">
                            @csrf
                            <input type="hidden" name="status" value="refused">
                            <button type="submit" class="btn btn-action-mini {{ $simRequest->status === 'refused' ? 'btn-danger' : 'btn-outline-danger' }} w-100" title="Refused">
                                <i class="bi bi-x-circle"></i>
                            </button>
                        </form>
                    </div>
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

        @php
            $recentHistories = $simRequest->histories->sortByDesc('created_at')->take(3);
        @endphp
        <div class="card action-card">
            <div class="card-header" style="background: #f8fafc; border-bottom: 1px solid #e2e8f0;">
                <h5 class="mb-0" style="font-weight: 600; color: #1e293b; font-size: 0.95rem;">
                    <i class="bi bi-lightning-charge me-2"></i>Activité récente
                </h5>
            </div>
            <div class="card-body">
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

        <div class="card action-card">
            <div class="card-header" style="background: #f8fafc; border-bottom: 1px solid #e2e8f0;">
                <h5 class="mb-0" style="font-weight: 600; color: #1e293b; font-size: 0.95rem;">
                    <i class="bi bi-clock-history me-2"></i>Historique
                </h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm mb-0" style="margin: 0; font-size: 12.5px;">
                        <thead style="background: #f9fafb;">
                            <tr>
                                <th style="padding: 12px; font-weight: 600; font-size: 13px; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px;">Date</th>
                                <th style="padding: 12px; font-weight: 600; font-size: 13px; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px;">Action</th>
                                <th style="padding: 12px; font-weight: 600; font-size: 13px; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px;">Utilisateur</th>
                                <th style="padding: 12px; font-weight: 600; font-size: 13px; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px;">Détails</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($simRequest->histories->sortByDesc('created_at') as $history)
                                <tr style="border-bottom: 1px solid #e5e7eb; transition: background 0.2s;" onmouseover="this.style.background='#f9fafb'" onmouseout="this.style.background='white'">
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
                                        <span class="badge" style="background: {{ $color }}; color: white; padding: 4px 10px; border-radius: 6px; font-size: 12px; font-weight: 500;">
                                            {{ $history->action_label }}
                                        </span>
                                    </td>
                                    <td style="padding: 10px; color: #4b5563;">
                                        {{ $history->user ? $history->user->full_name : ($history->user_matricule ?? '-') }}
                                    </td>
                                    <td style="padding: 10px; color: #6b7280; font-size: 12.5px;">
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
                                    <td colspan="4" class="text-center" style="padding: 40px; color: #9ca3af;">Aucun historique</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
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
