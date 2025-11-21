@extends('layouts.bootstrap')

@section('title', 'Documents - Bordereaux de Transmission')
@section('page-title', 'Documents - Bordereaux de Transmission')

@section('content')
<div class="row mb-4">
    <!-- Statistiques -->
    <div class="col-md-4 mb-3">
        <div class="card" style="border: none; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.08);">
            <div class="card-body" style="padding: 20px;">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="mb-1" style="font-size: 12px; color: #64748b; text-transform: uppercase; font-weight: 600;">Total Bordereaux</h6>
                        <h3 class="mb-0" style="font-weight: 700; color: #00574A;">{{ $stats['total'] }}</h3>
                    </div>
                    <div style="font-size: 2.5rem; color: #00574A; opacity: 0.2;">
                        <i class="bi bi-file-earmark-pdf"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4 mb-3">
        <div class="card" style="border: none; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.08);">
            <div class="card-body" style="padding: 20px;">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="mb-1" style="font-size: 12px; color: #64748b; text-transform: uppercase; font-weight: 600;">Ce mois</h6>
                        <h3 class="mb-0" style="font-weight: 700; color: #3b82f6;">{{ $stats['this_month'] }}</h3>
                    </div>
                    <div style="font-size: 2.5rem; color: #3b82f6; opacity: 0.2;">
                        <i class="bi bi-calendar-month"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4 mb-3">
        <div class="card" style="border: none; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.08);">
            <div class="card-body" style="padding: 20px;">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="mb-1" style="font-size: 12px; color: #64748b; text-transform: uppercase; font-weight: 600;">Cette semaine</h6>
                        <h3 class="mb-0" style="font-weight: 700; color: #10b981;">{{ $stats['this_week'] }}</h3>
                    </div>
                    <div style="font-size: 2.5rem; color: #10b981; opacity: 0.2;">
                        <i class="bi bi-calendar-week"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card" style="border: none; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.08);">
    <div class="card-body" style="padding: 20px;">
        <!-- Filtres -->
        <form method="GET" action="{{ route('documents.index') }}" class="mb-4" id="filters-form">
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label" style="font-size: 12px; color: #64748b; margin-bottom: 4px; font-weight: 500;">Type de demande</label>
                    <select name="request_type" class="form-select" style="border-radius: 8px; border: 1px solid #e2e8f0;">
                        <option value="">Tous les types</option>
                        <option value="recuperation" {{ request('request_type') === 'recuperation' ? 'selected' : '' }}>Récupération</option>
                        <option value="creation" {{ request('request_type') === 'creation' ? 'selected' : '' }}>Création</option>
                        <option value="suspension" {{ request('request_type') === 'suspension' ? 'selected' : '' }}>Suspension</option>
                        <option value="desactivation" {{ request('request_type') === 'desactivation' ? 'selected' : '' }}>Désactivation</option>
                        <option value="ajustement" {{ request('request_type') === 'ajustement' ? 'selected' : '' }}>Ajustement</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label" style="font-size: 12px; color: #64748b; margin-bottom: 4px; font-weight: 500;">Date début</label>
                    <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}" style="border-radius: 8px; border: 1px solid #e2e8f0;">
                </div>
                <div class="col-md-2">
                    <label class="form-label" style="font-size: 12px; color: #64748b; margin-bottom: 4px; font-weight: 500;">Date fin</label>
                    <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}" style="border-radius: 8px; border: 1px solid #e2e8f0;">
                </div>
                <div class="col-md-3">
                    <label class="form-label" style="font-size: 12px; color: #64748b; margin-bottom: 4px; font-weight: 500;">Recherche</label>
                    <input type="text" name="search" class="form-control" value="{{ request('search') }}" placeholder="N° demande, ICCID, téléphone..." style="border-radius: 8px; border: 1px solid #e2e8f0;">
                </div>
                <div class="col-md-2 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-primary w-100" style="border-radius: 8px; background: #00574A;">
                        <i class="bi bi-funnel"></i> Filtrer
                    </button>
                    @if(request()->anyFilled(['request_type', 'date_from', 'date_to', 'search']))
                    <a href="{{ route('documents.index') }}" class="btn btn-outline-secondary" style="border-radius: 8px;" title="Réinitialiser">
                        <i class="bi bi-x-circle"></i>
                    </a>
                    @endif
                </div>
            </div>
        </form>

        <!-- Tableau des bordereaux -->
        <div class="table-responsive">
            <table class="table table-hover mb-0" style="margin: 0;">
                <thead style="background: #f9fafb;">
                    <tr>
                        <th style="padding: 16px; font-weight: 600; font-size: 13px; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px;">N° Demande</th>
                        <th style="padding: 16px; font-weight: 600; font-size: 13px; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px;">Type</th>
                        <th style="padding: 16px; font-weight: 600; font-size: 13px; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px;">Bénéficiaire</th>
                        <th style="padding: 16px; font-weight: 600; font-size: 13px; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px;">ICCID / Téléphone</th>
                        <th style="padding: 16px; font-weight: 600; font-size: 13px; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px;">Date validation</th>
                        <th style="padding: 16px; font-weight: 600; font-size: 13px; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($bordereaux as $request)
                        <tr style="border-bottom: 1px solid #e5e7eb; transition: background 0.2s;" onmouseover="this.style.background='#f9fafb'" onmouseout="this.style.background='white'">
                            <td style="padding: 16px; font-weight: 600; color: #1a1a1a;">{{ $request->request_number }}</td>
                            <td style="padding: 16px;">
                                @php
                                    $typeLabels = [
                                        'recuperation' => 'Récupération',
                                        'creation' => 'Création',
                                        'suspension' => 'Suspension',
                                        'desactivation' => 'Désactivation',
                                        'ajustement' => 'Ajustement',
                                    ];
                                    $typeColors = [
                                        'recuperation' => ['bg' => '#fef3c7', 'text' => '#92400e'],
                                        'creation' => ['bg' => '#d1fae5', 'text' => '#065f46'],
                                        'suspension' => ['bg' => '#dbeafe', 'text' => '#1e40af'],
                                        'desactivation' => ['bg' => '#fee2e2', 'text' => '#991b1b'],
                                        'ajustement' => ['bg' => '#e9d5ff', 'text' => '#6b21a8'],
                                    ];
                                    $typeColor = $typeColors[$request->request_type] ?? ['bg' => '#f3f4f6', 'text' => '#374151'];
                                @endphp
                                <span class="badge" style="background: {{ $typeColor['bg'] }}; color: {{ $typeColor['text'] }}; padding: 6px 12px; border-radius: 6px; font-size: 12px; font-weight: 500;">
                                    {{ $typeLabels[$request->request_type] ?? ucfirst($request->request_type) }}
                                </span>
                            </td>
                            <td style="padding: 16px; color: #4b5563;">
                                @if($request->isCreation() && $request->beneficiary_name)
                                    <strong>{{ $request->beneficiary_name }}</strong>
                                    @if($request->beneficiary_first_name)
                                        {{ $request->beneficiary_first_name }}
                                    @endif
                                    @if($request->beneficiary_matricule)
                                        <br><small style="color: #9ca3af;">Mat: {{ $request->beneficiary_matricule }}</small>
                                    @endif
                                @else
                                    {{ $request->user->full_name ?? 'N/A' }}
                                    @if($request->user)
                                        <br><small style="color: #9ca3af;">Mat: {{ $request->user->matricule }}</small>
                                    @endif
                                @endif
                            </td>
                            <td style="padding: 16px; color: #4b5563;">
                                @if($request->sim)
                                    <strong>ICCID:</strong> {{ $request->sim->iccid }}<br>
                                    @if($request->sim->phone_number)
                                        <small style="color: #9ca3af;">Tel: {{ $request->sim->phone_number }}</small>
                                    @endif
                                @elseif($request->requested_iccid)
                                    <strong>ICCID:</strong> {{ $request->requested_iccid }}<br>
                                    @if($request->phone_number)
                                        <small style="color: #9ca3af;">Tel: {{ $request->phone_number }}</small>
                                    @endif
                                @elseif($request->phone_number)
                                    <strong>Tel:</strong> {{ $request->phone_number }}
                                @else
                                    <span style="color: #9ca3af;">-</span>
                                @endif
                            </td>
                            <td style="padding: 16px; color: #6b7280; font-size: 14px;">
                                @if($request->validated_at)
                                    {{ $request->validated_at->format('d/m/Y H:i') }}
                                @elseif($request->admin_processed_at)
                                    {{ $request->admin_processed_at->format('d/m/Y H:i') }}
                                @else
                                    <span style="color: #9ca3af;">-</span>
                                @endif
                            </td>
                            <td style="padding: 16px;">
                                <a href="{{ route('documents.bordereau', $request) }}" 
                                   class="btn btn-sm btn-primary" 
                                   target="_blank"
                                   style="border-radius: 6px; padding: 6px 12px; background: #00574A; border: none;"
                                   data-bs-toggle="tooltip" 
                                   title="Voir le bordereau">
                                    <i class="bi bi-file-earmark-pdf"></i> Voir BT
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center" style="padding: 40px; color: #9ca3af;">
                                <i class="bi bi-inbox" style="font-size: 48px; opacity: 0.5; margin-bottom: 12px; display: block;"></i>
                                Aucun bordereau trouvé
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($bordereaux->hasPages())
        <div class="mt-4">
            {{ $bordereaux->links('pagination::bootstrap-5') }}
        </div>
        @endif
    </div>
</div>
@endsection

