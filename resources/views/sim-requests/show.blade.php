@extends('layouts.bootstrap')

@section('title', 'Détails de la demande')
@section('page-title', 'Demande #' . $simRequest->request_number)

@section('content')
@if(request()->has('from') && request()->from === 'notification')
    <div class="alert alert-info alert-dismissible fade show" role="alert" style="border-left: 4px solid #3b82f6; background: #eff6ff; border-radius: 8px; margin-bottom: 20px;">
        <div class="d-flex align-items-center">
            <i class="bi bi-info-circle me-2" style="font-size: 20px; color: #3b82f6;"></i>
            <div>
                <strong>Rappel de notification</strong>
                <p class="mb-0" style="font-size: 14px;">Vous avez été redirigé depuis une notification de rappel. Cette demande nécessite une action de votre part.</p>
            </div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="row">
    <div class="col-md-8">
        <div class="card mb-3" style="border: none; border-radius: 10px; box-shadow: 0 1px 3px rgba(0,0,0,0.08);">
            <div class="card-header" style="background: white; border-bottom: 1px solid #e2e8f0; border-radius: 10px 10px 0 0;">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-2">
                        <h5 class="mb-0" style="font-weight: 600; color: #1e293b;">Informations de la demande</h5>
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
                            <a href="{{ route('sim-requests.edit', $simRequest) }}" class="btn btn-sm btn-primary" style="border-radius: 6px;" title="Modifier la demande">
                                <i class="bi bi-pencil"></i> Modifier
                            </a>
                        @endif
                        @php
                            $canCopy = $user->isValidator() || ($simRequest->isRecuperation() && $simRequest->user_id === $user->id);
                        @endphp
                        @if($canCopy)
                            <a href="{{ route('sim-requests.create', ['copy_from' => $simRequest->id]) }}" class="btn btn-sm btn-outline-primary" style="border-radius: 6px;" title="Reprendre cette demande">
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
                <dl class="row" style="margin-bottom: 0;">
                    <dt class="col-sm-4" style="color: #64748b; font-weight: 500; margin-bottom: 8px;">N° Demande:</dt>
                    <dd class="col-sm-8" style="color: #1e293b; margin-bottom: 16px;"><strong>{{ $simRequest->request_number }}</strong></dd>

                    <dt class="col-sm-4">Type:</dt>
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

                    <dt class="col-sm-4">Statut:</dt>
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

                    @if($simRequest->isRecuperation())
                        <dt class="col-sm-4">Utilisateur:</dt>
                        <dd class="col-sm-8">{{ $simRequest->user->full_name }} ({{ $simRequest->user->matricule }})</dd>
                    @endif

                    @if(!$simRequest->isCreation() && ($simRequest->collaborator_matricule || $simRequest->collaborator_name || $simRequest->collaborator_first_name || $simRequest->collaborator_agence))
                        <dt class="col-sm-4">Collaborateur:</dt>
                        <dd class="col-sm-8">
                            @if($simRequest->collaborator_name || $simRequest->collaborator_first_name)
                                <strong>{{ trim(($simRequest->collaborator_name ?? '') . ' ' . ($simRequest->collaborator_first_name ?? '')) }}</strong>
                            @endif
                            @if($simRequest->collaborator_matricule)
                                <br><small class="text-muted">Matricule: {{ $simRequest->collaborator_matricule }}</small>
                            @endif
                            @if($simRequest->collaborator_agence)
                                <br><small class="text-muted">Agence: {{ $simRequest->collaborator_agence }}</small>
                            @endif
                        </dd>
                    @endif

                    @if($simRequest->isCreation())
                        <dt class="col-sm-4">Bénéficiaire:</dt>
                        <dd class="col-sm-8">
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

                    @if($simRequest->plan)
                        <dt class="col-sm-4">Forfait:</dt>
                        <dd class="col-sm-8">
                            <strong>{{ $simRequest->plan->name }}</strong><br>
                            <small class="text-muted">
                                Limite crédit: {{ number_format($simRequest->limite_credit ?? $simRequest->plan->limite_credit, 0, ',', ' ') }} XOF<br>
                                Limite data: {{ $simRequest->limite_data ?? $simRequest->plan->limite_data }} GB
                            </small>
                        </dd>
                    @elseif($simRequest->limite_credit || $simRequest->limite_data)
                        <dt class="col-sm-4">Forfait:</dt>
                        <dd class="col-sm-8">
                            <small class="text-muted">
                                Limite crédit: {{ number_format($simRequest->limite_credit, 0, ',', ' ') }} XOF<br>
                                Limite data: {{ $simRequest->limite_data }} GB
                            </small>
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

    <div class="col-md-4">
        {{-- Actions pour le demandeur --}}
        {{-- Si un validateur a créé une demande, il voit la même vue qu'un simple utilisateur --}}
        @if((auth()->user()->id === $simRequest->user_id || auth()->user()->id === $simRequest->created_by) && ($simRequest->isEnAttente() || $simRequest->isPending()))
            <div class="card mb-3" style="border: none; border-radius: 10px; box-shadow: 0 1px 3px rgba(0,0,0,0.08);">
                <div class="card-header bg-info text-white" style="border-radius: 10px 10px 0 0;">
                    <h5 class="mb-0">Mes actions</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('sim-requests.cancel', $simRequest) }}" data-confirm="Êtes-vous sûr de vouloir annuler cette demande ? Cette action est irréversible." data-confirm-variant="danger" data-confirm-text="Annuler">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-warning w-100" data-tooltip="Annuler votre demande">
                            <i class="bi bi-x-circle"></i> Annuler ma demande
                        </button>
                    </form>
                </div>
            </div>
        @endif

        {{-- Marquer comme livré (validateur) --}}
        @if(auth()->user()->isValidator())
            <div class="card mb-3" style="border: none; border-radius: 10px; box-shadow: 0 1px 3px rgba(0,0,0,0.08);">
                <div class="card-header bg-light" style="border-radius: 10px 10px 0 0;">
                    <h5 class="mb-0">Livraison</h5>
                </div>
                <div class="card-body">
                    @if($simRequest->isDelivered())
                        <form method="POST" action="{{ route('sim-requests.toggle-delivered', $simRequest) }}" class="d-inline" data-confirm="Retirer la marque « livré » ?" data-confirm-variant="secondary" data-confirm-text="Retirer">
                            @csrf
                            <button type="submit" class="btn btn-outline-secondary w-100">
                                <i class="bi bi-box-seam"></i> Retirer « livré »
                            </button>
                        </form>
                    @else
                        <form method="POST" action="{{ route('sim-requests.toggle-delivered', $simRequest) }}" class="d-inline" data-confirm="Marquer cette demande comme livrée ?" data-confirm-variant="success" data-confirm-text="Marquer livré">
                            @csrf
                            <button type="submit" class="btn btn-success w-100">
                                <i class="bi bi-box-seam"></i> Marquer comme livrée
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        @endif

        {{-- Actions pour le validateur --}}
        {{-- Le validateur ne peut valider que les demandes de récupération des simples utilisateurs (pas celles qu'il a créées) --}}
        @if($simRequest->isRecuperation() && $simRequest->isEnAttente() && auth()->user()->canValidateRequests() && $simRequest->created_by !== auth()->id())
            <div class="card mb-3" style="border: none; border-radius: 10px; box-shadow: 0 1px 3px rgba(0,0,0,0.08);">
                <div class="card-header bg-warning" style="border-radius: 10px 10px 0 0;">
                    <h5 class="mb-0">Actions de validation</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('sim-requests.approve', $simRequest) }}" class="mb-2">
                        @csrf
                        <div class="mb-2">
                            <textarea name="notes" class="form-control" rows="2" placeholder="Notes (optionnel)"></textarea>
                        </div>
                        <button type="submit" class="btn btn-success w-100">
                            <i class="bi bi-check-circle"></i> Approuver
                        </button>
                    </form>

                    <form method="POST" action="{{ route('sim-requests.reject', $simRequest) }}" class="mb-2" data-confirm="Confirmer le rejet de cette demande ?" data-confirm-variant="danger" data-confirm-text="Rejeter">
                        @csrf
                        <div class="mb-2">
                            <textarea name="rejection_reason" class="form-control" rows="2" placeholder="Raison du rejet *" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-danger w-100">
                            <i class="bi bi-x-circle"></i> Rejeter
                        </button>
                    </form>

                    <form method="POST" action="{{ route('sim-requests.destroy', $simRequest) }}" data-confirm="Êtes-vous sûr de vouloir supprimer cette demande ? Cette action est irréversible." data-confirm-variant="danger" data-confirm-text="Supprimer">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger w-100 mt-2" data-tooltip="Supprimer définitivement cette demande">
                            <i class="bi bi-trash"></i> Supprimer la demande
                        </button>
                    </form>
                </div>
            </div>
        @endif

        {{-- Bouton pour générer le bordereau de transmission (visible pour les validateurs si la demande est acceptée) --}}
        @if(auth()->user()->isValidator())
            <div class="card mb-3" style="border: none; border-radius: 10px; box-shadow: 0 1px 3px rgba(0,0,0,0.08);">
                <div class="card-header bg-info text-white" style="border-radius: 10px 10px 0 0;">
                    <h5 class="mb-0">Bordereau de Transmission</h5>
                </div>
                <div class="card-body">
                    @if($simRequest->status === 'accepted')
                        <a href="{{ route('sim-requests.bordereau', $simRequest) }}" class="btn btn-info w-100" target="_blank">
                            <i class="bi bi-file-text"></i> Voir le Bordereau de Transmission
                        </a>
                    @else
                        <button class="btn btn-secondary w-100" disabled title="Le bordereau de transmission n'est disponible que pour les demandes acceptées">
                            <i class="bi bi-file-text"></i> Voir le Bordereau de Transmission
                        </button>
                    @endif
                </div>
            </div>
        @endif

        @if(auth()->user()->isAdmin())
            <div class="card mb-3" style="border: none; border-radius: 10px; box-shadow: 0 1px 3px rgba(0,0,0,0.08);">
                <div class="card-header bg-primary text-white" style="border-radius: 10px 10px 0 0;">
                    <h5 class="mb-0">Actions Admin</h5>
                </div>
                <div class="card-body">
                    {{-- Bouton pour soumettre au webhook --}}
                    {{-- Afficher pour toutes les demandes non rejetées --}}
                    @if(!$simRequest->isRejetee())
                        <form method="POST" action="{{ route('sim-requests.submit-webhook', $simRequest) }}" class="mb-3" data-confirm="Soumettre cette demande au webhook ? Un email sera envoyé automatiquement." data-confirm-variant="primary" data-confirm-text="Soumettre">
                            @csrf
                            <button type="submit" class="btn btn-success w-100">
                                <i class="bi bi-send"></i> Soumettre la demande au webhook
                            </button>
                        </form>
                    @endif
                    
                    {{-- Raccourcis rapides pour le statut opérateur --}}
                    <div class="mb-3">
                        <label class="form-label">Raccourcis statut opérateur</label>
                        <div class="d-flex gap-2">
                            <form method="POST" action="{{ route('sim-requests.quick-update-status', $simRequest) }}" class="flex-fill" data-confirm="Mettre à jour le statut à Pending (En attente) ?" data-confirm-variant="primary" data-confirm-text="Mettre à jour">
                                @csrf
                                <input type="hidden" name="status" value="pending">
                                <button type="submit" class="btn btn-{{ $simRequest->status === 'pending' ? 'primary' : 'outline-primary' }} w-100" title="Pending (En attente)">
                                    <i class="bi bi-clock"></i> Pending
                                </button>
                            </form>
                            <form method="POST" action="{{ route('sim-requests.quick-update-status', $simRequest) }}" class="flex-fill" data-confirm="Mettre à jour le statut à Accepted (Accepté) ?" data-confirm-variant="success" data-confirm-text="Mettre à jour">
                                @csrf
                                <input type="hidden" name="status" value="accepted">
                                <button type="submit" class="btn btn-{{ $simRequest->status === 'accepted' ? 'success' : 'outline-success' }} w-100" title="Accepted (Accepté)">
                                    <i class="bi bi-check-circle"></i> Accepted
                                </button>
                            </form>
                            <form method="POST" action="{{ route('sim-requests.quick-update-status', $simRequest) }}" class="flex-fill" data-confirm="Mettre à jour le statut à Refused (Refusé) ?" data-confirm-variant="danger" data-confirm-text="Mettre à jour">
                                @csrf
                                <input type="hidden" name="status" value="refused">
                                <button type="submit" class="btn btn-{{ $simRequest->status === 'refused' ? 'danger' : 'outline-danger' }} w-100" title="Refused (Refusé)">
                                    <i class="bi bi-x-circle"></i> Refused
                                </button>
                            </form>
                        </div>
                    </div>
                    
                    <form method="POST" action="{{ route('sim-requests.admin-action', $simRequest) }}">
                        @csrf
                        <div class="mb-3">
                            <label for="admin_status" class="form-label">Statut opérateur (avec commentaire)</label>
                            <select name="status" id="admin_status" class="form-select" required>
                                <option value="pending" {{ $simRequest->status === 'pending' ? 'selected' : '' }}>Pending (En attente)</option>
                                <option value="accepted" {{ $simRequest->status === 'accepted' ? 'selected' : '' }}>Accepted (Accepté)</option>
                                <option value="refused" {{ $simRequest->status === 'refused' ? 'selected' : '' }}>Refused (Refusé)</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="admin_comment" class="form-label">Commentaire</label>
                            <textarea name="admin_comment" id="admin_comment" class="form-control" rows="3" placeholder="Commentaire pour l'opérateur...">{{ old('admin_comment', $simRequest->admin_comment) }}</textarea>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-send"></i> Mettre à jour
                        </button>
                    </form>
                </div>
            </div>
        @endif

        @php
            $recentHistories = $simRequest->histories->sortByDesc('created_at')->take(3);
        @endphp
        <div class="card mb-3" style="border: none; border-radius: 10px; box-shadow: 0 1px 3px rgba(0,0,0,0.08);">
            <div class="card-header" style="background: white; border-bottom: 1px solid #e2e8f0; border-radius: 10px 10px 0 0;">
                <h5 class="mb-0" style="font-weight: 600; color: #1e293b;">
                    <i class="bi bi-lightning-charge" style="margin-right: 8px;"></i>Activité récente
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

        <div class="card" style="border: none; border-radius: 10px; box-shadow: 0 1px 3px rgba(0,0,0,0.08);">
            <div class="card-header" style="background: white; border-bottom: 1px solid #e2e8f0; border-radius: 10px 10px 0 0;">
                <h5 class="mb-0" style="font-weight: 600; color: #1e293b;">
                    <i class="bi bi-clock-history" style="margin-right: 8px;"></i>Historique et traçabilité
                </h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm mb-0" style="margin: 0;">
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
                                    <td style="padding: 12px; color: #4b5563;">{{ $history->created_at->format('d/m/Y H:i') }}</td>
                                    <td style="padding: 12px;">
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
                                    <td style="padding: 12px; color: #4b5563;">
                                        {{ $history->user ? $history->user->full_name : ($history->user_matricule ?? '-') }}
                                    </td>
                                    <td style="padding: 12px; color: #6b7280; font-size: 13px;">
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

<div class="mt-3">
    <a href="{{ route('sim-requests.index') }}" class="btn btn-secondary">
        <i class="bi bi-arrow-left"></i> Retour
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
