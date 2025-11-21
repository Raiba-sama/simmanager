@extends('layouts.bootstrap')

@section('title', 'Nouvelle demande')
@section('page-title', 'Nouvelle demande de SIM')

@section('content')
<div class="card">
    <div class="card-body">
        <form method="POST" action="{{ route('sim-requests.store') }}">
            @csrf

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="request_type" class="form-label">Type de demande <span class="text-danger">*</span></label>
                    <select name="request_type" id="request_type" class="form-select @error('request_type') is-invalid @enderror" required>
                        <option value="">Sélectionner...</option>
                        <option value="attribution" {{ old('request_type') === 'attribution' ? 'selected' : '' }}>Attribution</option>
                        <option value="suspension" {{ old('request_type') === 'suspension' ? 'selected' : '' }}>Suspension</option>
                        <option value="reactivation" {{ old('request_type') === 'reactivation' ? 'selected' : '' }}>Réactivation</option>
                        <option value="retour" {{ old('request_type') === 'retour' ? 'selected' : '' }}>Retour</option>
                    </select>
                    @error('request_type')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label for="sim_id" class="form-label">SIM (si disponible)</label>
                    <select name="sim_id" id="sim_id" class="form-select @error('sim_id') is-invalid @enderror">
                        <option value="">Sélectionner une SIM...</option>
                        @foreach($sims as $sim)
                            <option value="{{ $sim->id }}" {{ old('sim_id') == $sim->id ? 'selected' : '' }}>
                                {{ $sim->iccid }} - {{ $sim->operator ?? 'N/A' }}
                            </option>
                        @endforeach
                    </select>
                    @error('sim_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="mb-3">
                <label for="requested_iccid" class="form-label">ICCID demandé (si SIM non listée)</label>
                <input type="text" name="requested_iccid" id="requested_iccid" 
                       class="form-control @error('requested_iccid') is-invalid @enderror" 
                       value="{{ old('requested_iccid') }}" placeholder="Ex: 89261012345678901234">
                @error('requested_iccid')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="motif" class="form-label">Motif</label>
                <textarea name="motif" id="motif" rows="3" 
                          class="form-control @error('motif') is-invalid @enderror">{{ old('motif') }}</textarea>
                @error('motif')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="justification" class="form-label">Justification <span class="text-danger">*</span></label>
                <textarea name="justification" id="justification" rows="4" 
                          class="form-control @error('justification') is-invalid @enderror" required>{{ old('justification') }}</textarea>
                @error('justification')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="priority" class="form-label">Priorité</label>
                <select name="priority" id="priority" class="form-select @error('priority') is-invalid @enderror">
                    <option value="normal" {{ old('priority', 'normal') === 'normal' ? 'selected' : '' }}>Normale</option>
                    <option value="low" {{ old('priority') === 'low' ? 'selected' : '' }}>Basse</option>
                    <option value="high" {{ old('priority') === 'high' ? 'selected' : '' }}>Haute</option>
                    <option value="urgent" {{ old('priority') === 'urgent' ? 'selected' : '' }}>Urgente</option>
                </select>
                @error('priority')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="d-flex justify-content-between">
                <a href="{{ route('sim-requests.index') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Annuler
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-circle"></i> Soumettre la demande
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

