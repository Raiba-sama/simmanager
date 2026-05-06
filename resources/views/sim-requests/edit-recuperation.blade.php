@extends('layouts.bootstrap')

@section('title', 'Modifier la demande')
@section('page-title', 'Modifier la demande de récupération')

@push('styles')
<style>
    .form-card {
        background: white;
        border: 1px solid var(--border);
        border-radius: 14px;
        box-shadow: 0 1px 4px rgba(0,0,0,0.05);
        padding: 24px;
        margin-bottom: 20px;
    }
    .form-section-title {
        font-size: 13.5px;
        font-weight: 700;
        color: var(--text);
        padding-bottom: 12px;
        border-bottom: 1px solid var(--border);
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .form-section-title i { color: var(--primary); font-size: 15px; }

    .form-label {
        display: block;
        font-size: 12.5px;
        font-weight: 600;
        color: #374151;
        margin-bottom: 6px;
    }
    .form-label .req { color: #ef4444; }

    .filter-input {
        width: 100%;
        height: 38px;
        border: 1.5px solid var(--border);
        border-radius: 9px;
        padding: 0 12px;
        font-size: 13.5px;
        font-family: 'Poppins', sans-serif;
        color: var(--text);
        background: white;
        outline: none;
        transition: border-color 0.2s, box-shadow 0.2s;
        box-sizing: border-box;
    }
    .filter-input:focus { border-color: var(--primary); box-shadow: 0 0 0 3px var(--primary-glow); }
    .filter-input.is-invalid { border-color: #ef4444; }
    .filter-input.is-invalid:focus { box-shadow: 0 0 0 3px rgba(239,68,68,0.12); }
    textarea.filter-input { height: auto; padding: 10px 12px; resize: vertical; }
    select.filter-input { cursor: pointer; }

    .filter-input.is-invalid ~ .invalid-feedback,
    .filter-input.is-invalid + .invalid-feedback { display: block; }
    .invalid-feedback { font-size: 12px; color: #ef4444; margin-top: 5px; }

    .form-help { font-size: 12px; color: var(--muted); margin-top: 5px; }

    .btn-filter-primary {
        display: inline-flex; align-items: center; gap: 6px;
        height: 38px; padding: 0 18px;
        background: var(--primary); color: white;
        border: none; border-radius: 9px;
        font-size: 13.5px; font-weight: 600; font-family: 'Poppins', sans-serif;
        cursor: pointer; text-decoration: none;
        transition: background 0.18s;
    }
    .btn-filter-primary:hover { background: var(--primary-dark); color: white; }

    .btn-filter-reset {
        display: inline-flex; align-items: center; gap: 6px;
        height: 38px; padding: 0 16px;
        background: white; color: var(--text);
        border: 1.5px solid var(--border); border-radius: 9px;
        font-size: 13.5px; font-weight: 500; font-family: 'Poppins', sans-serif;
        cursor: pointer; text-decoration: none;
        transition: border-color 0.18s, color 0.18s;
    }
    .btn-filter-reset:hover { border-color: var(--primary); color: var(--primary); }
</style>
@endpush

@section('content')
<div style="max-width:800px;">
    <form method="POST" action="{{ route('sim-requests.update', $simRequest) }}">
        @csrf
        @method('PUT')
        <input type="hidden" name="request_type" value="recuperation">

        {{-- Collaborateur --}}
        <div class="form-card">
            <div class="form-section-title">
                <i class="bi bi-person"></i> Informations du collaborateur
            </div>

            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Matricule <span class="req">*</span></label>
                    <input type="text" name="collaborator_matricule" id="collaborator_matricule"
                           class="filter-input @error('collaborator_matricule') is-invalid @enderror"
                           value="{{ old('collaborator_matricule', $simRequest->collaborator_matricule) }}" required>
                    @error('collaborator_matricule')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label">Nom</label>
                    <input type="text" name="collaborator_name" id="collaborator_name"
                           class="filter-input @error('collaborator_name') is-invalid @enderror"
                           value="{{ old('collaborator_name', $simRequest->collaborator_name) }}">
                    @error('collaborator_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label">Prénoms</label>
                    <input type="text" name="collaborator_first_name" id="collaborator_first_name"
                           class="filter-input @error('collaborator_first_name') is-invalid @enderror"
                           value="{{ old('collaborator_first_name', $simRequest->collaborator_first_name) }}">
                    @error('collaborator_first_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-12">
                    <label class="form-label">Agence</label>
                    <input type="text" name="collaborator_agence" id="collaborator_agence"
                           class="filter-input @error('collaborator_agence') is-invalid @enderror"
                           value="{{ old('collaborator_agence', $simRequest->collaborator_agence) }}">
                    @error('collaborator_agence')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        {{-- Détails récupération --}}
        <div class="form-card">
            <div class="form-section-title">
                <i class="bi bi-sim"></i> Détails de la récupération
            </div>

            <div class="row g-3">
                <div class="col-md-12">
                    <label class="form-label">Numéro de ligne concerné</label>
                    <input type="text" name="phone_number" id="phone_number"
                           class="filter-input @error('phone_number') is-invalid @enderror"
                           value="{{ old('phone_number', $simRequest->phone_number) }}"
                           placeholder="Ex: 0341012345 ou +261 34 12 345 67">
                    <div class="form-help">Numéro de téléphone de la ligne à récupérer.</div>
                    @error('phone_number')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-12">
                    <label class="form-label">SIM disponible</label>
                    <select name="sim_id" id="sim_id"
                            class="filter-input @error('sim_id') is-invalid @enderror">
                        <option value="">Sélectionner une SIM libre...</option>
                        @foreach($sims as $sim)
                            <option value="{{ $sim->id }}" {{ old('sim_id', $simRequest->sim_id) == $sim->id ? 'selected' : '' }}>
                                {{ $sim->iccid }} – {{ $sim->operator ?? 'N/A' }}
                            </option>
                        @endforeach
                    </select>
                    <div class="form-help">Si vous avez une SIM blanche, sélectionnez-la ici. Sinon, laissez vide et saisissez l'ICCID ci-dessous.</div>
                    @error('sim_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-12">
                    <label class="form-label">ICCID demandé <span style="color:var(--muted);font-weight:400;">(si SIM non listée)</span></label>
                    <input type="text" name="requested_iccid" id="requested_iccid"
                           class="filter-input @error('requested_iccid') is-invalid @enderror"
                           value="{{ old('requested_iccid', $simRequest->requested_iccid) }}"
                           placeholder="Ex: 89261012345678901234">
                    @error('requested_iccid')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-12">
                    <label class="form-label">Motif <span class="req">*</span></label>
                    <textarea name="motif" id="motif" rows="3"
                              class="filter-input @error('motif') is-invalid @enderror"
                              required>{{ old('motif', $simRequest->motif) }}</textarea>
                    @error('motif')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        {{-- Actions --}}
        <div class="d-flex align-items-center justify-content-between">
            <a href="{{ route('sim-requests.show', $simRequest) }}" class="btn-filter-reset">
                <i class="bi bi-arrow-left"></i> Annuler
            </a>
            <button type="submit" class="btn-filter-primary">
                <i class="bi bi-check-circle"></i> Enregistrer les modifications
            </button>
        </div>
    </form>
</div>
@endsection
