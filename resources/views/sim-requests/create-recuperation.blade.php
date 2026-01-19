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

            <h5 class="mb-3">Informations du collaborateur</h5>
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label for="collaborator_matricule" class="form-label">Matricule <span class="text-danger">*</span></label>
                    <input type="text" name="collaborator_matricule" id="collaborator_matricule"
                           class="form-control @error('collaborator_matricule') is-invalid @enderror"
                           value="{{ old('collaborator_matricule') }}" placeholder="Ex: M12345" required>
                    @error('collaborator_matricule')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-4 mb-3">
                    <label for="collaborator_name" class="form-label">Nom</label>
                    <input type="text" name="collaborator_name" id="collaborator_name"
                           class="form-control @error('collaborator_name') is-invalid @enderror"
                           value="{{ old('collaborator_name') }}" placeholder="Nom du collaborateur">
                    @error('collaborator_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-4 mb-3">
                    <label for="collaborator_first_name" class="form-label">Prénoms</label>
                    <input type="text" name="collaborator_first_name" id="collaborator_first_name"
                           class="form-control @error('collaborator_first_name') is-invalid @enderror"
                           value="{{ old('collaborator_first_name') }}" placeholder="Prénoms du collaborateur">
                    @error('collaborator_first_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="mb-3">
                <label for="collaborator_agence" class="form-label">Agence</label>
                <input type="text" name="collaborator_agence" id="collaborator_agence"
                       class="form-control @error('collaborator_agence') is-invalid @enderror"
                       value="{{ old('collaborator_agence') }}" placeholder="Agence / Lieu d'affectation">
                @error('collaborator_agence')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="phone_number" class="form-label">Numéro de ligne concerné</label>
                <input type="text" name="phone_number" id="phone_number" 
                       class="form-control @error('phone_number') is-invalid @enderror" 
                       value="{{ old('phone_number', $currentSim->phone_number ?? '') }}" 
                       placeholder="Ex: 0341012345 ou +261 34 12 345 67 (laisser vide pour utiliser votre numéro)">
                <small class="form-text text-muted">Numéro de téléphone de la ligne à récupérer. Si vide, votre numéro actuel sera utilisé.</small>
                @error('phone_number')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

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

