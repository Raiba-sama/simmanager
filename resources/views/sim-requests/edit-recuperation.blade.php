@extends('layouts.bootstrap')

@section('title', 'Modifier la demande')
@section('page-title', 'Modifier la demande de récupération')

@section('content')
<div class="card">
    <div class="card-body">
        <form method="POST" action="{{ route('sim-requests.update', $simRequest) }}">
            @csrf
            @method('PUT')
            <input type="hidden" name="request_type" value="recuperation">

            @if($currentSim)
            <div class="alert alert-info">
                <i class="bi bi-info-circle"></i> 
                <strong>SIM actuelle détectée :</strong> {{ $currentSim->iccid }} 
                @if($currentSim->phone_number)
                    - {{ $currentSim->phone_number }}
                @endif
            </div>
            @endif

            <div class="mb-3">
                <label for="sim_id" class="form-label">SIM disponible</label>
                <select name="sim_id" id="sim_id" class="form-select @error('sim_id') is-invalid @enderror">
                    <option value="">Sélectionner une SIM libre...</option>
                    @foreach($sims as $sim)
                        <option value="{{ $sim->id }}" {{ old('sim_id', $simRequest->sim_id) == $sim->id ? 'selected' : '' }}>
                            {{ $sim->iccid }} - {{ $sim->operator ?? 'N/A' }}
                        </option>
                    @endforeach
                </select>
                <small class="form-text text-muted">Si vous avez une SIM blanche, sélectionnez-la ici. Sinon, laissez vide et saisissez l'ICCID ci-dessous.</small>
                @error('sim_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="requested_iccid" class="form-label">ICCID demandé (si SIM non listée)</label>
                <input type="text" name="requested_iccid" id="requested_iccid" 
                       class="form-control @error('requested_iccid') is-invalid @enderror" 
                       value="{{ old('requested_iccid', $simRequest->requested_iccid) }}" placeholder="Ex: 89261012345678901234">
                @error('requested_iccid')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="motif" class="form-label">Motif <span class="text-danger">*</span></label>
                <textarea name="motif" id="motif" rows="3" 
                          class="form-control @error('motif') is-invalid @enderror" required>{{ old('motif', $simRequest->motif) }}</textarea>
                @error('motif')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="d-flex justify-content-between mt-4">
                <a href="{{ route('sim-requests.show', $simRequest) }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Annuler
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-circle"></i> Enregistrer les modifications
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

