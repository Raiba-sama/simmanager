@extends('layouts.bootstrap')

@section('title', 'Détails SIM')
@section('page-title', 'SIM #' . $sim->iccid)

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card mb-3" style="border: none; border-radius: 10px; box-shadow: 0 1px 3px rgba(0,0,0,0.08);">
            <div class="card-header" style="background: white; border-bottom: 1px solid #e2e8f0; border-radius: 10px 10px 0 0;">
                <h5 class="mb-0" style="font-weight: 600; color: #1e293b;">Informations SIM</h5>
            </div>
            <div class="card-body">
                <dl class="row" style="margin-bottom: 0;">
                    <dt class="col-sm-4" style="color: #64748b; font-weight: 500; margin-bottom: 8px;">ICCID:</dt>
                    <dd class="col-sm-8" style="color: #1e293b; margin-bottom: 16px;"><strong>{{ $sim->iccid }}</strong></dd>

                    <dt class="col-sm-4">Numéro:</dt>
                    <dd class="col-sm-8">{{ $sim->phone_number ?? '-' }}</dd>

                    <dt class="col-sm-4">Statut:</dt>
                    <dd class="col-sm-8">
                        @php
                            $statusColors = [
                                'libre' => '#10b981',
                                'attribue' => '#3b82f6',
                                'suspendu' => '#ef4444',
                                'defectueuse' => '#f59e0b',
                            ];
                            $statusLabels = [
                                'libre' => 'Libre',
                                'attribue' => 'Attribuée',
                                'suspendu' => 'Suspendue',
                                'defectueuse' => 'Défectueuse',
                            ];
                            $color = $statusColors[$sim->status] ?? '#6b7280';
                            $label = $statusLabels[$sim->status] ?? ucfirst($sim->status);
                        @endphp
                        <span class="badge" style="background: {{ $color }}; color: white; padding: 6px 12px; border-radius: 6px; font-size: 13px; font-weight: 500;">
                            {{ $label }}
                        </span>
                    </dd>

                    <dt class="col-sm-4">Opérateur:</dt>
                    <dd class="col-sm-8">{{ $sim->operator ?? '-' }}</dd>

                    <dt class="col-sm-4">Type de plan:</dt>
                    <dd class="col-sm-8">{{ $sim->plan_type ?? '-' }}</dd>

                    <dt class="col-sm-4">Coût mensuel:</dt>
                    <dd class="col-sm-8">{{ $sim->monthly_cost ? number_format($sim->monthly_cost, 0, ',', ' ') . ' XOF' : '-' }}</dd>

                    @if($sim->assignedUser)
                        <dt class="col-sm-4">Assignée à:</dt>
                        <dd class="col-sm-8">{{ $sim->assignedUser->full_name }} ({{ $sim->assignedUser->matricule }})</dd>

                        <dt class="col-sm-4">Date d'attribution:</dt>
                        <dd class="col-sm-8">{{ $sim->assigned_at ? $sim->assigned_at->format('d/m/Y H:i') : '-' }}</dd>
                    @endif
                </dl>
            </div>
        </div>

        <div class="card" style="border: none; border-radius: 10px; box-shadow: 0 1px 3px rgba(0,0,0,0.08);">
            <div class="card-header" style="background: white; border-bottom: 1px solid #e2e8f0; border-radius: 10px 10px 0 0;">
                <h5 class="mb-0" style="font-weight: 600; color: #1e293b;">Historique</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm mb-0" style="margin: 0;">
                        <thead style="background: #f9fafb;">
                            <tr>
                                <th style="padding: 12px; font-weight: 600; font-size: 13px; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px;">Date</th>
                                <th style="padding: 12px; font-weight: 600; font-size: 13px; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px;">Action</th>
                                <th style="padding: 12px; font-weight: 600; font-size: 13px; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px;">Utilisateur</th>
                                <th style="padding: 12px; font-weight: 600; font-size: 13px; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px;">Notes</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($sim->histories as $history)
                                <tr style="border-bottom: 1px solid #e5e7eb; transition: background 0.2s;" onmouseover="this.style.background='#f9fafb'" onmouseout="this.style.background='white'">
                                    <td style="padding: 12px; color: #4b5563;">{{ $history->created_at->format('d/m/Y H:i') }}</td>
                                    <td style="padding: 12px;">
                                        <span class="badge" style="background: #3b82f6; color: white; padding: 4px 10px; border-radius: 6px; font-size: 12px; font-weight: 500;">{{ $history->action }}</span>
                                    </td>
                                    <td style="padding: 12px; color: #4b5563;">{{ $history->user ? $history->user->full_name : ($history->user_matricule ?? '-') }}</td>
                                    <td style="padding: 12px; color: #6b7280;">{{ \Illuminate\Support\Str::limit($history->notes ?? '', 50) }}</td>
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

    <div class="col-md-4">
        @if(auth()->user()->isAdmin())
            <div class="card mb-3" style="border: none; border-radius: 10px; box-shadow: 0 1px 3px rgba(0,0,0,0.08);">
                <div class="card-header" style="background: white; border-bottom: 1px solid #e2e8f0; border-radius: 10px 10px 0 0;">
                    <h5 class="mb-0" style="font-weight: 600; color: #1e293b;">Changer le statut</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('sims.update-status', $sim) }}" id="updateStatusForm">
                        @csrf
                        <div class="mb-3">
                            <label for="status" class="form-label" style="font-weight: 500; color: #1e293b; font-size: 14px;">Nouveau statut</label>
                            <select name="status" id="status" class="form-select" required style="border-radius: 8px; border: 1px solid #e2e8f0; padding: 10px;">
                                <option value="libre" {{ $sim->status === 'libre' ? 'selected' : '' }}>Libre</option>
                                <option value="attribue" {{ $sim->status === 'attribue' ? 'selected' : '' }}>Attribuée</option>
                                <option value="suspendu" {{ $sim->status === 'suspendu' ? 'selected' : '' }}>Suspendue</option>
                                <option value="defectueuse" {{ $sim->status === 'defectueuse' ? 'selected' : '' }}>Défectueuse</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="reason" class="form-label" style="font-weight: 500; color: #1e293b; font-size: 14px;">Raison (optionnel)</label>
                            <textarea name="reason" id="reason" class="form-control" rows="3" placeholder="Ex: Carte SIM endommagée lors de la réception" style="border-radius: 8px; border: 1px solid #e2e8f0;"></textarea>
                        </div>
                        <button type="submit" class="btn w-100" style="background: #00574A; color: white; border-radius: 8px; padding: 10px; font-weight: 500;">
                            <i class="bi bi-check-circle"></i> Mettre à jour le statut
                        </button>
                    </form>
                </div>
            </div>
        @endif
    </div>
</div>

<div class="mt-3">
    <a href="{{ route('sims.index') }}" class="btn btn-secondary">
        <i class="bi bi-arrow-left"></i> Retour
    </a>
</div>
@endsection

