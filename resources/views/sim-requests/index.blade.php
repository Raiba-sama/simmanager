@extends('layouts.bootstrap')

@section('title', 'Demandes de SIM')
@section('page-title', 'Demandes de SIM')

@section('page-actions')
    <a href="{{ route('sim-requests.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Nouvelle demande
    </a>
@endsection

@section('content')
<div class="card" style="border: none; border-radius: 10px; box-shadow: 0 1px 3px rgba(0,0,0,0.08);">
    <div class="card-body" style="padding: 20px;">
        <!-- Filters -->
        <form method="GET" action="{{ route('sim-requests.index') }}" class="mb-4" id="filters-form">
            <div class="row g-3">
                <div class="col-md-2">
                    <select name="request_type" id="filter-request-type" class="form-select" style="border-radius: 8px; border: 1px solid #e2e8f0;">
                        <option value="">Tous les types</option>
                        <option value="recuperation" {{ request('request_type') === 'recuperation' ? 'selected' : '' }}>Récupération</option>
                        <option value="creation" {{ request('request_type') === 'creation' ? 'selected' : '' }}>Création</option>
                        <option value="suspension" {{ request('request_type') === 'suspension' ? 'selected' : '' }}>Suspension</option>
                        <option value="desactivation" {{ request('request_type') === 'desactivation' ? 'selected' : '' }}>Désactivation</option>
                        <option value="ajustement" {{ request('request_type') === 'ajustement' ? 'selected' : '' }}>Ajustement</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="status" id="filter-status" class="form-select" style="border-radius: 8px; border: 1px solid #e2e8f0;">
                        <option value="">Tous les statuts</option>
                        <option value="en_attente" {{ request('status') === 'en_attente' ? 'selected' : '' }}>En attente</option>
                        <option value="validee" {{ request('status') === 'validee' ? 'selected' : '' }}>Validée</option>
                        <option value="rejetee" {{ request('status') === 'rejetee' ? 'selected' : '' }}>Rejetée</option>
                        <option value="demande_envoyee" {{ request('status') === 'demande_envoyee' ? 'selected' : '' }}>Demande envoyée</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending (opérateur)</option>
                        <option value="accepted" {{ request('status') === 'accepted' ? 'selected' : '' }}>Acceptée (opérateur)</option>
                        <option value="refused" {{ request('status') === 'refused' ? 'selected' : '' }}>Refusée (opérateur)</option>
                    </select>
                </div>
                <div class="col-md-1">
                    <label class="form-check-label" style="font-size: 12px; color: #64748b; margin-bottom: 4px; display: block;">Favoris</label>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="favorites" id="filter-favorites" value="1" {{ request('favorites') === '1' ? 'checked' : '' }} style="cursor: pointer;">
                    </div>
                </div>
                <div class="col-md-2">
                    <select name="delivered" id="filter-delivered" class="form-select" style="border-radius: 8px; border: 1px solid #e2e8f0;">
                        <option value="">Livré : tous</option>
                        <option value="1" {{ request('delivered') === '1' ? 'selected' : '' }}>Livrées</option>
                        <option value="0" {{ request('delivered') === '0' ? 'selected' : '' }}>Non livrées</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-outline-primary w-100" style="border-radius: 8px;">
                        <i class="bi bi-funnel"></i> Filtrer
                    </button>
                </div>
                <div class="col-md-1" id="reset-filter-btn" style="{{ request()->hasAny(['request_type','status','delivered','collaborator','agence','phone_number','iccid']) ? '' : 'display: none;' }}">
                    <a href="{{ route('sim-requests.index') }}" class="btn btn-outline-secondary w-100" style="border-radius: 8px;" title="Réinitialiser" onclick="event.preventDefault(); resetFilters();">
                        <i class="bi bi-x-circle"></i>
                    </a>
                </div>
                <div class="col-md-2">
                    <div class="btn-group w-100" role="group" id="export-buttons">
                        @php
                            $exportParams = request()->query();
                            $exportParams['format'] = 'excel';
                        @endphp
                        <a href="{{ route('sim-requests.export', $exportParams) }}" 
                           class="btn btn-success" style="border-radius: 8px 0 0 8px;" title="Exporter en Excel" id="export-excel">
                            <i class="bi bi-file-earmark-excel"></i> Excel
                        </a>
                        @php
                            $exportParams['format'] = 'pdf';
                        @endphp
                        <a href="{{ route('sim-requests.export', $exportParams) }}" 
                           class="btn btn-danger" style="border-radius: 0 8px 8px 0;" title="Exporter en PDF" id="export-pdf">
                            <i class="bi bi-file-earmark-pdf"></i> PDF
                        </a>
                    </div>
                </div>
            </div>
            <div class="row g-3 mt-2">
                <div class="col-md-3">
                    <input type="text" name="collaborator" class="form-control" value="{{ request('collaborator') }}" placeholder="Collaborateur (matricule ou nom)" style="border-radius: 8px; border: 1px solid #e2e8f0;">
                </div>
                <div class="col-md-3">
                    <input type="text" name="agence" class="form-control" value="{{ request('agence') }}" placeholder="Agence" style="border-radius: 8px; border: 1px solid #e2e8f0;">
                </div>
                <div class="col-md-3">
                    <input type="text" name="phone_number" class="form-control" value="{{ request('phone_number') }}" placeholder="Numéro de ligne" style="border-radius: 8px; border: 1px solid #e2e8f0;">
                </div>
                <div class="col-md-3">
                    <input type="text" name="iccid" class="form-control" value="{{ request('iccid') }}" placeholder="ICCID" style="border-radius: 8px; border: 1px solid #e2e8f0;">
                </div>
            </div>
        </form>

        <div class="d-flex justify-content-end mb-2">
            <button type="button" class="btn btn-outline-secondary btn-sm" id="toggle-columns-btn" style="border-radius: 6px;">
                <i class="bi bi-layout-three-columns"></i> Afficher détails
            </button>
        </div>

        <!-- Barre d'actions en masse (affichée seulement si des éléments sont sélectionnés) -->
        <div id="bulk-actions-bar" style="display: none; margin-bottom: 16px; padding: 16px; background: #f0f9ff; border-radius: 8px; border: 1px solid #bae6fd;">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <strong id="selected-count" style="color: #00574A;">0</strong> <span style="color: #64748b;">élément(s) sélectionné(s)</span>
                </div>
                <div class="d-flex gap-2">
                    @if(auth()->user()->isValidator())
                    <button type="button" class="btn btn-sm btn-success" onclick="bulkAction('approve')" style="border-radius: 6px;">
                        <i class="bi bi-check-circle"></i> Valider
                    </button>
                    <button type="button" class="btn btn-sm btn-danger" onclick="bulkAction('reject')" style="border-radius: 6px;">
                        <i class="bi bi-x-circle"></i> Rejeter
                    </button>
                    @endif
                    @if(auth()->user()->isAdmin())
                    <button type="button" class="btn btn-sm btn-info" onclick="bulkAction('status', 'pending')" style="border-radius: 6px;">
                        <i class="bi bi-clock"></i> Pending
                    </button>
                    <button type="button" class="btn btn-sm btn-success" onclick="bulkAction('status', 'accepted')" style="border-radius: 6px;">
                        <i class="bi bi-check"></i> Accepted
                    </button>
                    <button type="button" class="btn btn-sm btn-danger" onclick="bulkAction('status', 'refused')" style="border-radius: 6px;">
                        <i class="bi bi-x"></i> Refused
                    </button>
                    @endif
                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="bulkAction('export')" style="border-radius: 6px;">
                        <i class="bi bi-download"></i> Exporter
                    </button>
                    @if(auth()->user()->canValidateRequests())
                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="bulkAction('delete')" style="border-radius: 6px;">
                        <i class="bi bi-trash"></i> Supprimer
                    </button>
                    @endif
                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="clearSelection()" style="border-radius: 6px;">
                        <i class="bi bi-x"></i> Annuler
                    </button>
                </div>
            </div>
        </div>

        <!-- Table -->
        <div class="table-responsive" id="table-container">
            <table class="table table-hover mb-0" style="margin: 0;">
                <thead style="background: #f9fafb;">
                    <tr>
                        <th class="sticky-col-left" style="padding: 16px; font-weight: 600; font-size: 13px; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px; width: 50px;">
                            <input type="checkbox" id="select-all" onchange="toggleSelectAll(this)" style="cursor: pointer;">
                        </th>
                        <th class="sticky-col-left second" style="padding: 16px; font-weight: 600; font-size: 13px; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px;">N° Demande</th>
                        <th style="padding: 16px; font-weight: 600; font-size: 13px; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px;">Type</th>
                        <th class="col-secondary" style="padding: 16px; font-weight: 600; font-size: 13px; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px;">Demandeur</th>
                        <th class="col-secondary" style="padding: 16px; font-weight: 600; font-size: 13px; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px;">Collaborateur concerné</th>
                        <th class="col-secondary" style="padding: 16px; font-weight: 600; font-size: 13px; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px;">Ligne concernée</th>
                        <th style="padding: 16px; font-weight: 600; font-size: 13px; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px;">Statut</th>
                        <th class="col-secondary" style="padding: 16px; font-weight: 600; font-size: 13px; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px;">Livré</th>
                        <th class="col-secondary" style="padding: 16px; font-weight: 600; font-size: 13px; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px;">Priorité</th>
                        <th class="col-secondary" style="padding: 16px; font-weight: 600; font-size: 13px; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px;">Date</th>
                        <th class="sticky-col-right" style="padding: 16px; font-weight: 600; font-size: 13px; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px;">Actions</th>
                    </tr>
                </thead>
                <tbody id="table-body">
                    @forelse($requests as $request)
                        @php
                            $typeLabels = [
                                'recuperation' => 'Récupération',
                                'creation' => 'Création',
                                'suspension' => 'Suspension',
                                'desactivation' => 'Désactivation',
                                'ajustement' => 'Ajustement',
                            ];
                            $typeColors = [
                                'recuperation' => ['bg' => '#f59e0b', 'text' => 'white'],
                                'creation' => ['bg' => '#10b981', 'text' => 'white'],
                                'suspension' => ['bg' => '#3b82f6', 'text' => 'white'],
                                'desactivation' => ['bg' => '#ef4444', 'text' => 'white'],
                                'ajustement' => ['bg' => '#8b5cf6', 'text' => 'white'],
                            ];
                            $statusLabels = [
                                'en_attente' => 'En attente',
                                'validee' => 'Validée',
                                'rejetee' => 'Rejetée',
                                'demande_envoyee' => 'Demande envoyée',
                                'pending' => 'Pending',
                                'accepted' => 'Acceptée',
                                'refused' => 'Refusée',
                            ];
                            $statusColors = [
                                'en_attente' => ['bg' => '#f59e0b', 'text' => 'white'],
                                'validee' => ['bg' => '#10b981', 'text' => 'white'],
                                'rejetee' => ['bg' => '#ef4444', 'text' => 'white'],
                                'demande_envoyee' => ['bg' => '#06b6d4', 'text' => 'white'],
                                'pending' => ['bg' => '#06b6d4', 'text' => 'white'],
                                'accepted' => ['bg' => '#10b981', 'text' => 'white'],
                                'refused' => ['bg' => '#ef4444', 'text' => 'white'],
                            ];
                            $typeColor = $typeColors[$request->request_type] ?? ['bg' => '#6b7280', 'text' => 'white'];
                            $statusColor = $statusColors[$request->status] ?? ['bg' => '#6b7280', 'text' => 'white'];
                        @endphp
                        @php
                            $daysPending = $request->status === 'en_attente' ? now()->diffInDays($request->created_at) : 0;
                            $urgencyLevel = $daysPending >= 7 ? 'urgent' : ($daysPending >= 5 ? 'high' : ($daysPending >= 3 ? 'normal' : 'low'));
                            $urgencyColors = [
                                'urgent' => '#ef4444',
                                'high' => '#f59e0b',
                                'normal' => '#3b82f6',
                                'low' => 'transparent',
                            ];
                            $urgencyBg = [
                                'urgent' => '#fef2f2',
                                'high' => '#fffbeb',
                                'normal' => '#eff6ff',
                                'low' => 'transparent',
                            ];
                        @endphp
                        <tr style="border-bottom: 1px solid #e5e7eb; transition: background 0.2s; {{ $urgencyLevel !== 'low' ? 'border-left: 4px solid ' . $urgencyColors[$urgencyLevel] . ';' : '' }} background: {{ $urgencyBg[$urgencyLevel] }};" onmouseover="this.style.background='{{ $urgencyLevel !== 'low' ? $urgencyBg[$urgencyLevel] : '#f9fafb' }}'" onmouseout="this.style.background='{{ $urgencyBg[$urgencyLevel] }}'">
                            <td class="sticky-col-left" style="padding: 16px;">
                                <input type="checkbox" class="request-checkbox" value="{{ $request->id }}" onchange="updateBulkActions()" style="cursor: pointer;">
                            </td>
                            <td class="sticky-col-left second" style="padding: 16px; font-weight: 600; color: #1a1a1a;">
                                {{ $request->request_number }}
                                @if($urgencyLevel !== 'low')
                                    <span class="badge" style="background: {{ $urgencyColors[$urgencyLevel] }}; color: white; padding: 2px 6px; border-radius: 4px; font-size: 10px; margin-left: 6px; font-weight: 600;" title="En attente depuis {{ $daysPending }} jour(s)">
                                        {{ $daysPending }}j
                                    </span>
                                @endif
                            </td>
                            @php
                                $requester = $request->creator ?? $request->user;
                                $collaboratorName = null;
                                $collaboratorMatricule = null;
                                if ($request->isCreation()) {
                                    $collaboratorName = trim(($request->beneficiary_name ?? '') . ' ' . ($request->beneficiary_first_name ?? ''));
                                    if ($collaboratorName === '') {
                                        $collaboratorName = null;
                                    }
                                    $collaboratorMatricule = $request->beneficiary_matricule;
                                } else {
                                    $collaboratorName = trim(($request->collaborator_name ?? '') . ' ' . ($request->collaborator_first_name ?? ''));
                                    if ($collaboratorName === '') {
                                        $collaboratorName = null;
                                    }
                                    $collaboratorMatricule = $request->collaborator_matricule;
                                    if (!$collaboratorName && !$collaboratorMatricule) {
                                        $collaboratorName = $request->user->full_name ?? null;
                                        $collaboratorMatricule = $request->user->matricule ?? null;
                                    }
                                }
                                $lineNumber = $request->phone_number ?? ($request->sim ? $request->sim->phone_number : null);
                                $needsAttention = false;
                                if ($request->isCreation()) {
                                    $needsAttention = empty($request->beneficiary_name);
                                } else {
                                    $needsAttention = empty($request->collaborator_matricule) || empty($lineNumber);
                                }
                            @endphp
                            <td style="padding: 16px;">
                                <span class="badge" style="background: {{ $typeColor['bg'] }}; color: {{ $typeColor['text'] }}; padding: 6px 12px; border-radius: 6px; font-size: 12px; font-weight: 500;">
                                    {{ $typeLabels[$request->request_type] ?? ucfirst($request->request_type) }}
                                </span>
                            </td>
                            <td class="col-secondary" style="padding: 16px; color: #4b5563;">
                                @if($requester)
                                    <div class="d-flex align-items-center gap-2">
                                        <img src="{{ $requester->avatar }}" alt="" class="rounded-circle flex-shrink-0" style="width: 32px; height: 32px; object-fit: cover;">
                                        <div>
                                            {{ $requester->full_name }}
                                            @if($requester->matricule)
                                                <br><small class="text-muted">Mat: {{ $requester->matricule }}</small>
                                            @endif
                                        </div>
                                    </div>
                                @else
                                    <span class="text-muted" style="color: #9ca3af;">-</span>
                                @endif
                            </td>
                            <td class="col-secondary" style="padding: 16px; color: #4b5563;">
                                @if($collaboratorName)
                                    {{ $collaboratorName }}
                                    @if($collaboratorMatricule)
                                        <br><small class="text-muted">Mat: {{ $collaboratorMatricule }}</small>
                                    @endif
                                @elseif($collaboratorMatricule)
                                    {{ $collaboratorMatricule }}
                                @else
                                    <span class="text-muted" style="color: #9ca3af;">-</span>
                                @endif
                            </td>
                            <td class="col-secondary" style="padding: 16px; color: #4b5563;">
                                @if($lineNumber)
                                    {{ $lineNumber }}
                                @else
                                    <span class="text-muted" style="color: #9ca3af;">-</span>
                                @endif
                            </td>
                            <td style="padding: 16px;">
                                <span class="badge" style="background: {{ $statusColor['bg'] }}; color: {{ $statusColor['text'] }}; padding: 6px 12px; border-radius: 6px; font-size: 12px; font-weight: 500;">
                                    {{ $statusLabels[$request->status] ?? ucfirst($request->status) }}
                                </span>
                                @if($needsAttention)
                                    <span class="badge ms-1" style="background: #ef4444; color: white; padding: 4px 8px; border-radius: 6px; font-size: 11px;">
                                        À corriger
                                    </span>
                                @endif
                            </td>
                            <td class="col-secondary" style="padding: 16px;">
                                @if($request->isDelivered())
                                    <span class="badge" style="background: #10b981; color: white; padding: 4px 8px; border-radius: 6px; font-size: 11px;">Livré</span>
                                    @if(auth()->user()->isValidator())
                                        <form method="POST" action="{{ route('sim-requests.toggle-delivered', $request) }}" class="d-inline" data-confirm="Retirer la marque « livré » ?" data-confirm-variant="secondary" data-confirm-text="Retirer">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-link p-0 ms-1" style="font-size: 11px;" title="Retirer livré">✕</button>
                                        </form>
                                    @endif
                                @else
                                    @if(auth()->user()->isValidator())
                                        <form method="POST" action="{{ route('sim-requests.toggle-delivered', $request) }}" class="d-inline" data-confirm="Marquer cette demande comme livrée ?" data-confirm-variant="success" data-confirm-text="Marquer livré">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-success" style="border-radius: 6px; padding: 4px 8px; font-size: 11px;" title="Marquer livré">
                                                <i class="bi bi-box-seam"></i> Livré
                                            </button>
                                        </form>
                                    @else
                                        <span class="text-muted" style="font-size: 12px;">-</span>
                                    @endif
                                @endif
                            </td>
                            <td class="col-secondary" style="padding: 16px;">
                                @if($request->priority)
                                    @php
                                        $priorityColors = [
                                            'urgent' => ['bg' => '#ef4444', 'text' => 'white'],
                                            'high' => ['bg' => '#f59e0b', 'text' => 'white'],
                                            'normal' => ['bg' => '#06b6d4', 'text' => 'white'],
                                        ];
                                        $priorityColor = $priorityColors[$request->priority] ?? ['bg' => '#6b7280', 'text' => 'white'];
                                    @endphp
                                    <span class="badge" style="background: {{ $priorityColor['bg'] }}; color: {{ $priorityColor['text'] }}; padding: 6px 12px; border-radius: 6px; font-size: 12px; font-weight: 500;">
                                        {{ ucfirst($request->priority) }}
                                    </span>
                                @else
                                    <span class="text-muted" style="color: #9ca3af;">-</span>
                                @endif
                            </td>
                            <td class="col-secondary" style="padding: 16px; color: #6b7280; font-size: 14px;">{{ $request->created_at->format('d/m/Y H:i') }}</td>
                            <td class="sticky-col-right" style="padding: 16px;">
                                <div class="d-flex align-items-center gap-2">
                                    <button type="button" 
                                            class="btn btn-sm {{ isset($request->is_favorite) && $request->is_favorite ? 'btn-warning' : 'btn-outline-warning' }}" 
                                            style="border-radius: 6px; padding: 6px 10px;" 
                                            title="{{ isset($request->is_favorite) && $request->is_favorite ? 'Retirer des favoris' : 'Ajouter aux favoris' }}"
                                            onclick="toggleFavorite({{ $request->id }}, this)">
                                        <i class="bi {{ isset($request->is_favorite) && $request->is_favorite ? 'bi-star-fill' : 'bi-star' }}"></i>
                                    </button>
                                    <a href="{{ route('sim-requests.show', $request) }}" class="btn btn-sm" style="background: transparent; border: 1px solid #e5e7eb; color: #3b82f6; padding: 6px 12px; border-radius: 6px; text-decoration: none;" data-bs-toggle="tooltip" title="Voir les détails">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    @if(auth()->user()->canValidateRequests() && $request->isRecuperation() && $request->status === 'en_attente' && $request->created_by !== auth()->id())
                                        <form method="POST" action="{{ route('sim-requests.approve', $request) }}" class="d-inline" data-confirm="Valider cette demande ?" data-confirm-variant="success" data-confirm-text="Valider">
                                            @csrf
                                            <button type="submit" class="btn btn-sm" style="background: #10b981; border: 1px solid #10b981; color: white; padding: 6px 10px; border-radius: 6px;" data-bs-toggle="tooltip" title="Valider">
                                                <i class="bi bi-check-circle"></i>
                                            </button>
                                        </form>
                                        <form method="POST" action="{{ route('sim-requests.reject', $request) }}" class="d-inline" data-confirm="Rejeter cette demande ?" data-confirm-variant="danger" data-confirm-text="Rejeter">
                                            @csrf
                                            <button type="submit" class="btn btn-sm" style="background: #ef4444; border: 1px solid #ef4444; color: white; padding: 6px 10px; border-radius: 6px;" data-bs-toggle="tooltip" title="Rejeter">
                                                <i class="bi bi-x-circle"></i>
                                            </button>
                                        </form>
                                    @endif
                                    @if(auth()->user()->isAdmin())
                                        <form method="POST" action="{{ route('sim-requests.quick-update-status', $request) }}" class="d-inline" data-confirm="Mettre à jour le statut à Pending ?" data-confirm-variant="primary" data-confirm-text="Mettre à jour">
                                            @csrf
                                            <input type="hidden" name="status" value="pending">
                                            <button type="submit" class="btn btn-sm" style="background: {{ $request->status === 'pending' ? '#06b6d4' : 'transparent' }}; border: 1px solid {{ $request->status === 'pending' ? '#06b6d4' : '#e5e7eb' }}; color: {{ $request->status === 'pending' ? 'white' : '#06b6d4' }}; padding: 6px 10px; border-radius: 6px;" data-bs-toggle="tooltip" title="Pending (En attente)">
                                                <i class="bi bi-clock"></i>
                                            </button>
                                        </form>
                                        <form method="POST" action="{{ route('sim-requests.quick-update-status', $request) }}" class="d-inline" data-confirm="Mettre à jour le statut à Accepted ?" data-confirm-variant="success" data-confirm-text="Mettre à jour">
                                            @csrf
                                            <input type="hidden" name="status" value="accepted">
                                            <button type="submit" class="btn btn-sm" style="background: {{ $request->status === 'accepted' ? '#10b981' : 'transparent' }}; border: 1px solid {{ $request->status === 'accepted' ? '#10b981' : '#e5e7eb' }}; color: {{ $request->status === 'accepted' ? 'white' : '#10b981' }}; padding: 6px 10px; border-radius: 6px;" data-bs-toggle="tooltip" title="Accepted (Accepté)">
                                                <i class="bi bi-check-circle"></i>
                                            </button>
                                        </form>
                                        <form method="POST" action="{{ route('sim-requests.quick-update-status', $request) }}" class="d-inline" data-confirm="Mettre à jour le statut à Refused ?" data-confirm-variant="danger" data-confirm-text="Mettre à jour">
                                            @csrf
                                            <input type="hidden" name="status" value="refused">
                                            <button type="submit" class="btn btn-sm" style="background: {{ $request->status === 'refused' ? '#ef4444' : 'transparent' }}; border: 1px solid {{ $request->status === 'refused' ? '#ef4444' : '#e5e7eb' }}; color: {{ $request->status === 'refused' ? 'white' : '#ef4444' }}; padding: 6px 10px; border-radius: 6px;" data-bs-toggle="tooltip" title="Refused (Refusé)">
                                                <i class="bi bi-x-circle"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11" class="text-center" style="padding: 40px; color: #9ca3af;">Aucune demande trouvée</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div id="pagination-container" class="mt-4 d-flex justify-content-center">
            @if($requests->hasPages())
                {{ $requests->appends(request()->query())->links('pagination::bootstrap-5') }}
            @endif
        </div>
    </div>
</div>

<!-- Modal de rejet en masse -->
<div class="modal fade" id="bulkRejectModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Rejeter des demandes</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
            </div>
            <div class="modal-body">
                <p id="bulkRejectDescription" class="mb-2">Vous êtes sur le point de rejeter ces demandes.</p>
                <label for="bulkRejectReason" class="form-label">Motif de rejet <span class="text-danger">*</span></label>
                <textarea id="bulkRejectReason" class="form-control" rows="3" placeholder="Ex: informations incomplètes"></textarea>
                <div id="bulkRejectError" class="text-danger mt-2 d-none" style="font-size: 12px;">Le motif est obligatoire.</div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Annuler</button>
                <button type="button" class="btn btn-danger" id="bulkRejectConfirm">Rejeter</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    let filterTimeout;
    const baseUrl = '{{ route('sim-requests.index') }}';
    const detailsStorageKey = 'sim-requests-show-details';

    function applyColumnVisibility(showDetails) {
        document.querySelectorAll('.col-secondary').forEach(col => {
            col.classList.toggle('d-none', !showDetails);
        });
        const btn = document.getElementById('toggle-columns-btn');
        if (btn) {
            btn.innerHTML = showDetails
                ? '<i class="bi bi-layout-three-columns"></i> Masquer détails'
                : '<i class="bi bi-layout-three-columns"></i> Afficher détails';
        }
    }

    function initializeColumnToggle() {
        const stored = localStorage.getItem(detailsStorageKey);
        const showDetails = stored === null ? true : stored === 'true';
        applyColumnVisibility(showDetails);
        const btn = document.getElementById('toggle-columns-btn');
        if (btn) {
            btn.addEventListener('click', function () {
                const current = localStorage.getItem(detailsStorageKey);
                const nextValue = current === 'true' ? 'false' : 'true';
                localStorage.setItem(detailsStorageKey, nextValue);
                applyColumnVisibility(nextValue === 'true');
            });
        }
    }
    
    // Soumission du formulaire au changement des selects (filtres appliqués par rechargement GET)
    const filtersForm = document.getElementById('filters-form');
    if (filtersForm) {
        ['filter-request-type', 'filter-status', 'filter-delivered'].forEach(id => {
            const el = document.getElementById(id);
            if (el) {
                el.addEventListener('change', function() {
                    filtersForm.submit();
                });
            }
        });
    }

    function resetFilters() {
        window.location.href = baseUrl;
    }
    
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
                } else {
                    button.classList.remove('btn-warning');
                    button.classList.add('btn-outline-warning');
                    icon.classList.remove('bi-star-fill');
                    icon.classList.add('bi-star');
                    button.title = 'Ajouter aux favoris';
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
    
    function updateExportButtons(requestType, status) {
        const params = new URLSearchParams();
        if (requestType) params.append('request_type', requestType);
        if (status) params.append('status', status);
        const collaborator = document.querySelector('input[name="collaborator"]')?.value?.trim() || '';
        const agence = document.querySelector('input[name="agence"]')?.value?.trim() || '';
        const phoneNumber = document.querySelector('input[name="phone_number"]')?.value?.trim() || '';
        const iccid = document.querySelector('input[name="iccid"]')?.value?.trim() || '';
        if (collaborator) params.append('collaborator', collaborator);
        if (agence) params.append('agence', agence);
        if (phoneNumber) params.append('phone_number', phoneNumber);
        if (iccid) params.append('iccid', iccid);
        const favorites = document.getElementById('filter-favorites');
        if (favorites && favorites.checked) params.append('favorites', '1');
        
        const excelBtn = document.getElementById('export-excel');
        const pdfBtn = document.getElementById('export-pdf');
        
        const queryString = params.toString();
        const baseExportUrl = '{{ route('sim-requests.export') }}';
        
        excelBtn.href = baseExportUrl + (queryString ? '?' + queryString + '&format=excel' : '?format=excel');
        pdfBtn.href = baseExportUrl + (queryString ? '?' + queryString + '&format=pdf' : '?format=pdf');
    }
    
    // Handle pagination clicks
    document.addEventListener('click', function(e) {
        if (e.target.closest('.pagination a')) {
            e.preventDefault();
            const url = e.target.closest('.pagination a').href;
            
            const tableBody = document.getElementById('table-body');
            tableBody.innerHTML = '<tr><td colspan="11" class="text-center" style="padding: 40px;"><i class="bi bi-arrow-repeat spin"></i> Chargement...</td></tr>';
            
            fetch(url, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'text/html',
                }
            })
            .then(response => response.text())
            .then(html => {
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                
                const newTableBody = doc.getElementById('table-body');
                if (newTableBody) {
                    tableBody.innerHTML = newTableBody.innerHTML;
                    // Réinitialiser les sélections après pagination
                    clearSelection();
                    attachCheckboxListeners();
                    const stored = localStorage.getItem(detailsStorageKey);
                    applyColumnVisibility(stored === null ? true : stored === 'true');
                }
                
                const newPagination = doc.getElementById('pagination-container');
                if (newPagination) {
                    document.getElementById('pagination-container').innerHTML = newPagination.innerHTML;
                }
                
                // Scroll to top
                window.scrollTo({ top: 0, behavior: 'smooth' });
            })
            .catch(error => {
                console.error('Pagination error:', error);
                window.location.href = url;
            });
        }
    });
    
    // Actions en masse
    function attachCheckboxListeners() {
        // Réattacher les event listeners aux checkboxes
        document.querySelectorAll('.request-checkbox').forEach(cb => {
            cb.removeEventListener('change', updateBulkActions);
            cb.addEventListener('change', updateBulkActions);
        });
        
        // Réattacher le listener pour "select all"
        const selectAll = document.getElementById('select-all');
        if (selectAll) {
            selectAll.removeEventListener('change', function() { toggleSelectAll(this); });
            selectAll.addEventListener('change', function() { toggleSelectAll(this); });
        }
    }
    
    function toggleSelectAll(checkbox) {
        const checkboxes = document.querySelectorAll('.request-checkbox');
        checkboxes.forEach(cb => {
            cb.checked = checkbox.checked;
        });
        updateBulkActions();
    }
    
    function updateBulkActions() {
        const selected = document.querySelectorAll('.request-checkbox:checked');
        const count = selected.length;
        const bulkBar = document.getElementById('bulk-actions-bar');
        const selectAll = document.getElementById('select-all');
        
        if (count > 0) {
            if (bulkBar) {
                bulkBar.style.display = 'block';
            }
            const selectedCountEl = document.getElementById('selected-count');
            if (selectedCountEl) {
                selectedCountEl.textContent = count;
            }
        } else {
            if (bulkBar) {
                bulkBar.style.display = 'none';
            }
        }
        
        // Mettre à jour la checkbox "select all"
        const allCheckboxes = document.querySelectorAll('.request-checkbox');
        if (selectAll) {
            selectAll.checked = count === allCheckboxes.length && count > 0;
            selectAll.indeterminate = count > 0 && count < allCheckboxes.length;
        }
    }
    
    function clearSelection() {
        document.querySelectorAll('.request-checkbox').forEach(cb => cb.checked = false);
        const selectAll = document.getElementById('select-all');
        if (selectAll) {
            selectAll.checked = false;
            selectAll.indeterminate = false;
        }
        updateBulkActions();
    }
    
    // Initialiser les listeners au chargement de la page
    document.addEventListener('DOMContentLoaded', function() {
        attachCheckboxListeners();
        initializeColumnToggle();
    });
    
    let bulkRejectModal = null;
    let bulkRejectIds = [];

    function getSelectedIds() {
        const selected = document.querySelectorAll('.request-checkbox:checked');
        return Array.from(selected).map(cb => cb.value);
    }
    
    function bulkAction(action, status = null) {
        const ids = getSelectedIds();
        if (ids.length === 0) {
            alert('Veuillez sélectionner au moins une demande.');
            return;
        }
        
        if (action === 'export') {
            const params = new URLSearchParams();
            ids.forEach(id => params.append('ids[]', id));
            window.location.href = '{{ route('sim-requests.export') }}?format=excel&' + params.toString();
            return;
        }

        if (action === 'reject') {
            bulkRejectIds = ids;
            const description = document.getElementById('bulkRejectDescription');
            const reasonInput = document.getElementById('bulkRejectReason');
            const error = document.getElementById('bulkRejectError');
            if (description) {
                description.textContent = `Vous êtes sur le point de rejeter ${ids.length} demande(s).`;
            }
            if (reasonInput) {
                reasonInput.value = '';
            }
            if (error) {
                error.classList.add('d-none');
            }
            if (bulkRejectModal) {
                bulkRejectModal.show();
            }
            return;
        }

        if (action === 'approve') {
            return window.showConfirmModal(`Valider ${ids.length} demande(s) ?`, () => executeBulkAction(action, status, { ids }), {
                confirmText: 'Valider',
                confirmVariant: 'success',
                title: 'Confirmer la validation'
            });
        }

        if (action === 'delete') {
            return window.showConfirmModal(`Supprimer ${ids.length} demande(s) ?`, () => executeBulkAction(action, status, { ids }), {
                confirmText: 'Supprimer',
                confirmVariant: 'danger',
                title: 'Confirmer la suppression'
            });
        }

        if (action === 'status') {
            const labels = {
                pending: 'Pending (En attente)',
                accepted: 'Accepted (Accepté)',
                refused: 'Refused (Refusé)'
            };
            const label = labels[status] || status;
            return window.showConfirmModal(`Mettre à jour le statut à "${label}" pour ${ids.length} demande(s) ?`, () => executeBulkAction(action, status, { ids }), {
                confirmText: 'Mettre à jour',
                confirmVariant: status === 'refused' ? 'danger' : 'primary',
                title: 'Confirmer le changement'
            });
        }

        executeBulkAction(action, status, { ids });
    }

    function executeBulkAction(action, status, payload) {
        let url = '';
        let method = 'POST';
        let data = payload || {};

        if (action === 'approve') {
            url = '{{ route('sim-requests.bulk-approve') }}';
        } else if (action === 'reject') {
            url = '{{ route('sim-requests.bulk-reject') }}';
        } else if (action === 'status') {
            url = '{{ route('sim-requests.bulk-update-status') }}';
            data.status = status;
        } else if (action === 'delete') {
            url = '{{ route('sim-requests.bulk-delete') }}';
            method = 'DELETE';
        }

        fetch(url, {
            method: method,
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
            },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                if (window.showToast) {
                    showToast(data.message || 'Action effectuée avec succès', 'success');
                }
                clearSelection();
                // Recharger la page ou mettre à jour le tableau
                setTimeout(() => {
                    window.location.reload();
                }, 1000);
            } else {
                if (window.showToast) {
                    showToast(data.message || 'Une erreur est survenue', 'error');
                } else {
                    alert(data.message || 'Une erreur est survenue.');
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);
            if (window.showToast) {
                showToast('Une erreur est survenue lors de l\'action en masse', 'error');
            } else {
                alert('Une erreur est survenue lors de l\'action en masse.');
            }
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        const modalEl = document.getElementById('bulkRejectModal');
        if (modalEl && typeof bootstrap !== 'undefined') {
            bulkRejectModal = new bootstrap.Modal(modalEl);
        }
        const confirmBtn = document.getElementById('bulkRejectConfirm');
        if (confirmBtn) {
            confirmBtn.addEventListener('click', function() {
                const reasonInput = document.getElementById('bulkRejectReason');
                const error = document.getElementById('bulkRejectError');
                const reason = reasonInput ? reasonInput.value.trim() : '';

                if (!reason) {
                    if (error) {
                        error.classList.remove('d-none');
                    }
                    return;
                }
                if (error) {
                    error.classList.add('d-none');
                }
                if (bulkRejectModal) {
                    bulkRejectModal.hide();
                }
                executeBulkAction('reject', null, { ids: bulkRejectIds, rejection_reason: reason });
            });
        }
    });
    
</script>
<style>
    @keyframes spin {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }
    .spin {
        animation: spin 1s linear infinite;
        display: inline-block;
    }
    .sticky-col-left {
        position: sticky;
        left: 0;
        background: white;
        z-index: 2;
    }
    .sticky-col-left.second {
        left: 50px;
    }
    .sticky-col-right {
        position: sticky;
        right: 0;
        background: white;
        z-index: 2;
    }
    .col-secondary.d-none {
        display: none !important;
    }
</style>
@endpush
@endsection
