@extends('layouts.bootstrap')

@section('title', 'Demande de récupération')
@section('page-title', 'Nouvelle demande de récupération')

@section('content')
<div class="card">
    <div class="card-body">
        <form method="POST" action="{{ route('sim-requests.store') }}">
            @csrf
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
                        <option value="{{ $sim->id }}" {{ old('sim_id') == $sim->id ? 'selected' : '' }}>
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
                <label for="requested_iccid" class="form-label">ICCID (si SIM non listée)</label>
                <input type="text" name="requested_iccid" id="requested_iccid" 
                       class="form-control @error('requested_iccid') is-invalid @enderror" 
                       value="{{ old('requested_iccid') }}" placeholder="Ex: 89261012345678901234">
                <small class="form-text text-muted">Saisissez l'ICCID de votre nouvelle SIM si elle n'est pas dans la liste ci-dessus.</small>
                @error('requested_iccid')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="motif" class="form-label">Motif de la récupération <span class="text-danger">*</span></label>
                <textarea name="motif" id="motif" rows="4" 
                          class="form-control @error('motif') is-invalid @enderror" required>{{ old('motif') }}</textarea>
                <small class="form-text text-muted">Indiquez la raison de votre demande (perte, vol, etc.)</small>
                @error('motif')
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

