@extends('layouts.bootstrap')

@section('title', 'Détails SIM')
@section('page-title', 'SIM #' . $sim->iccid)

@push('styles')
<style>
    .sim-detail-grid {
        display: grid;
        grid-template-columns: 1fr 300px;
        gap: 20px;
        align-items: start;
    }
    @media (max-width: 991px) {
        .sim-detail-grid { grid-template-columns: 1fr; }
    }

    .sd-card {
        background: white;
        border: 1px solid var(--border);
        border-radius: 14px;
        box-shadow: 0 1px 4px rgba(0,0,0,0.05);
        overflow: hidden;
        margin-bottom: 20px;
    }
    .sd-card-header {
        padding: 15px 20px;
        border-bottom: 1px solid var(--border);
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .sd-card-title { font-size: 14px; font-weight: 700; color: var(--text); margin: 0; }
    .sd-card-body  { padding: 20px; }

    .sd-dl { display: grid; grid-template-columns: 150px 1fr; gap: 10px 16px; }
    .sd-dt { font-size: 12px; font-weight: 600; color: var(--muted); padding-top: 2px; }
    .sd-dd { font-size: 13.5px; color: var(--text); font-weight: 500; }

    .sdot {
        display: inline-flex; align-items: center; gap: 6px;
        padding: 3px 11px;
        border-radius: 20px;
        font-size: 12px; font-weight: 600;
    }

    .sd-table { width: 100%; border-collapse: collapse; }
    .sd-table th {
        padding: 10px 16px;
        font-size: 11px; font-weight: 700;
        text-transform: uppercase; letter-spacing: 0.05em;
        color: var(--muted); background: #f8fafc;
        border-bottom: 1px solid var(--border);
        text-align: left;
    }
    .sd-table td {
        padding: 11px 16px;
        font-size: 13px; color: var(--text);
        border-bottom: 1px solid #f1f5f9;
    }
    .sd-table tbody tr:last-child td { border-bottom: none; }
    .sd-table tbody tr:hover td { background: #f8fafc; }

    .sd-label {
        display: block;
        font-size: 12.5px; font-weight: 600; color: #374151;
        margin-bottom: 6px;
    }
    .sd-input {
        width: 100%; height: 38px;
        border: 1.5px solid var(--border); border-radius: 9px;
        padding: 0 12px;
        font-size: 13.5px; font-family: 'Poppins', sans-serif; color: var(--text);
        outline: none; box-sizing: border-box;
        transition: border-color 0.2s, box-shadow 0.2s;
    }
    .sd-input:focus { border-color: var(--primary); box-shadow: 0 0 0 3px var(--primary-glow); }
    textarea.sd-input { height: auto; padding: 10px 12px; resize: vertical; }

    .sd-submit {
        width: 100%; height: 38px;
        background: var(--primary); color: white;
        border: none; border-radius: 9px;
        font-size: 13.5px; font-weight: 600; font-family: 'Poppins', sans-serif;
        display: flex; align-items: center; justify-content: center; gap: 6px;
        cursor: pointer; transition: background 0.18s;
    }
    .sd-submit:hover { background: var(--primary-dark); }

    .sd-back {
        display: inline-flex; align-items: center; gap: 6px;
        padding: 8px 16px;
        background: white; border: 1.5px solid var(--border); border-radius: 9px;
        font-size: 13px; font-weight: 500; color: var(--text);
        text-decoration: none; transition: border-color 0.18s, color 0.18s;
    }
    .sd-back:hover { border-color: var(--primary); color: var(--primary); }
</style>
@endpush

@section('content')
@php
    $sc = [
        'libre'       => ['bg' => '#f0fdf4', 'text' => '#16a34a'],
        'attribue'    => ['bg' => '#eff6ff', 'text' => '#2563eb'],
        'suspendu'    => ['bg' => '#fef2f2', 'text' => '#ef4444'],
        'defectueuse' => ['bg' => '#fffbeb', 'text' => '#d97706'],
    ][$sim->status] ?? ['bg' => '#f3f4f6', 'text' => '#6b7280'];

    $sl = [
        'libre'       => 'Libre',
        'attribue'    => 'Attribuée',
        'suspendu'    => 'Suspendue',
        'defectueuse' => 'Défectueuse',
    ][$sim->status] ?? ucfirst($sim->status);
@endphp

<div class="sim-detail-grid">

    {{-- Left --}}
    <div>
        <div class="sd-card">
            <div class="sd-card-header">
                <i class="bi bi-sim-fill" style="color:var(--primary);font-size:17px;"></i>
                <h2 class="sd-card-title">Informations SIM</h2>
            </div>
            <div class="sd-card-body">
                <dl class="sd-dl">
                    <dt class="sd-dt">ICCID</dt>
                    <dd class="sd-dd"><strong>{{ $sim->iccid }}</strong></dd>

                    <dt class="sd-dt">Numéro</dt>
                    <dd class="sd-dd">{{ $sim->phone_number ?? '—' }}</dd>

                    <dt class="sd-dt">Statut</dt>
                    <dd class="sd-dd">
                        <span class="sdot" style="background:{{ $sc['bg'] }};color:{{ $sc['text'] }};">{{ $sl }}</span>
                    </dd>

                    <dt class="sd-dt">Opérateur</dt>
                    <dd class="sd-dd">{{ $sim->operator ?? '—' }}</dd>

                    <dt class="sd-dt">Type de plan</dt>
                    <dd class="sd-dd">{{ $sim->plan_type ?? '—' }}</dd>

                    <dt class="sd-dt">Coût mensuel</dt>
                    <dd class="sd-dd">{{ $sim->monthly_cost ? number_format($sim->monthly_cost, 0, ',', ' ') . ' XOF' : '—' }}</dd>

                    @if($sim->assignedUser)
                        <dt class="sd-dt">Assignée à</dt>
                        <dd class="sd-dd">
                            {{ $sim->assignedUser->full_name }}
                            <span style="color:var(--muted);font-size:12px;">({{ $sim->assignedUser->matricule }})</span>
                        </dd>

                        <dt class="sd-dt">Date attribution</dt>
                        <dd class="sd-dd">{{ $sim->assigned_at ? $sim->assigned_at->format('d/m/Y H:i') : '—' }}</dd>
                    @endif
                </dl>
            </div>
        </div>

        <div class="sd-card">
            <div class="sd-card-header">
                <i class="bi bi-clock-history" style="color:var(--muted);font-size:16px;"></i>
                <h2 class="sd-card-title">Historique</h2>
            </div>
            <div style="overflow-x:auto;">
                <table class="sd-table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Action</th>
                            <th>Utilisateur</th>
                            <th>Notes</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($sim->histories as $history)
                            <tr>
                                <td style="color:var(--muted);white-space:nowrap;">{{ $history->created_at->format('d/m/Y H:i') }}</td>
                                <td>
                                    <span style="display:inline-block;padding:3px 10px;border-radius:20px;font-size:11.5px;font-weight:600;background:#eff6ff;color:#2563eb;">
                                        {{ $history->action }}
                                    </span>
                                </td>
                                <td>{{ $history->user ? $history->user->full_name : ($history->user_matricule ?? '—') }}</td>
                                <td style="color:var(--muted);">{{ \Illuminate\Support\Str::limit($history->notes ?? '', 50) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" style="text-align:center;padding:36px;color:var(--muted);">
                                    <i class="bi bi-clock-history" style="font-size:28px;opacity:0.3;display:block;margin-bottom:8px;"></i>
                                    Aucun historique
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Right --}}
    <div>
        @if(auth()->user()->isAdmin())
        <div class="sd-card">
            <div class="sd-card-header">
                <i class="bi bi-pencil-square" style="color:var(--primary);font-size:15px;"></i>
                <h2 class="sd-card-title">Changer le statut</h2>
            </div>
            <div class="sd-card-body">
                <form method="POST" action="{{ route('sims.update-status', $sim) }}">
                    @csrf
                    <div class="mb-3">
                        <label class="sd-label">Nouveau statut</label>
                        <select name="status" class="sd-input" required>
                            <option value="libre"       {{ $sim->status === 'libre'       ? 'selected' : '' }}>Libre</option>
                            <option value="attribue"    {{ $sim->status === 'attribue'    ? 'selected' : '' }}>Attribuée</option>
                            <option value="suspendu"    {{ $sim->status === 'suspendu'    ? 'selected' : '' }}>Suspendue</option>
                            <option value="defectueuse" {{ $sim->status === 'defectueuse' ? 'selected' : '' }}>Défectueuse</option>
                        </select>
                    </div>
                    <div class="mb-4">
                        <label class="sd-label">Raison <span style="color:var(--muted);font-weight:400;">(optionnel)</span></label>
                        <textarea name="reason" class="sd-input" rows="3" placeholder="Ex: Carte SIM endommagée lors de la réception"></textarea>
                    </div>
                    <button type="submit" class="sd-submit">
                        <i class="bi bi-check-circle"></i> Mettre à jour le statut
                    </button>
                </form>
            </div>
        </div>
        @endif
    </div>

</div>

<div class="mt-1">
    <a href="{{ route('sims.index') }}" class="sd-back">
        <i class="bi bi-arrow-left"></i> Retour à l'inventaire
    </a>
</div>
@endsection
