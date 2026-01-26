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

            <h5 class="mb-3">Informations du collaborateur</h5>
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label for="collaborator_matricule" class="form-label">Matricule <span class="text-danger">*</span></label>
                    <input type="text" name="collaborator_matricule" id="collaborator_matricule"
                           class="form-control @error('collaborator_matricule') is-invalid @enderror"
                           value="{{ old('collaborator_matricule', $simRequest->collaborator_matricule) }}" required>
                    @error('collaborator_matricule')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-4 mb-3">
                    <label for="collaborator_name" class="form-label">Nom</label>
                    <input type="text" name="collaborator_name" id="collaborator_name"
                           class="form-control @error('collaborator_name') is-invalid @enderror"
                           value="{{ old('collaborator_name', $simRequest->collaborator_name) }}">
                    @error('collaborator_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-4 mb-3">
                    <label for="collaborator_first_name" class="form-label">Prénoms</label>
                    <input type="text" name="collaborator_first_name" id="collaborator_first_name"
                           class="form-control @error('collaborator_first_name') is-invalid @enderror"
                           value="{{ old('collaborator_first_name', $simRequest->collaborator_first_name) }}">
                    @error('collaborator_first_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="mb-3">
                <label for="collaborator_agence" class="form-label">Agence</label>
                <input type="text" name="collaborator_agence" id="collaborator_agence"
                       class="form-control @error('collaborator_agence') is-invalid @enderror"
                       value="{{ old('collaborator_agence', $simRequest->collaborator_agence) }}">
                @error('collaborator_agence')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="phone_number" class="form-label">Numéro de ligne concerné</label>
                <input type="text" name="phone_number" id="phone_number" 
                       class="form-control @error('phone_number') is-invalid @enderror" 
                       value="{{ old('phone_number', $simRequest->phone_number) }}" 
                       placeholder="Ex: 0341012345 ou +261 34 12 345 67">
                <small class="form-text text-muted">Numéro de téléphone de la ligne à récupérer.</small>
                @error('phone_number')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

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

