@extends('layouts.bootstrap')

@section('title', 'Modifier la demande')
@section('page-title', 'Modifier la demande')

@section('content')
<div class="card">
    <div class="card-body">
        <form method="POST" action="{{ route('sim-requests.update', $simRequest) }}" id="requestForm">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label for="request_type" class="form-label">Type de demande <span class="text-danger">*</span></label>
                <select name="request_type" id="request_type" class="form-select @error('request_type') is-invalid @enderror" required disabled>
                    <option value="">{{ $simRequest->request_type }}</option>
                </select>
                <input type="hidden" name="request_type" value="{{ $simRequest->request_type }}">
                <small class="form-text text-muted">Le type de demande ne peut pas être modifié.</small>
                @error('request_type')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            @if($simRequest->request_type !== 'creation')
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
            @endif

            <!-- Formulaire pour Création -->
            <div id="creation-form" class="form-section-hidden">
                <h5 class="mb-3">Informations du bénéficiaire</h5>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="beneficiary_name" class="form-label">Nom <span class="text-danger">*</span></label>
                        <input type="text" name="beneficiary_name" id="beneficiary_name" 
                               class="form-control @error('beneficiary_name') is-invalid @enderror" 
                               value="{{ old('beneficiary_name', $simRequest->beneficiary_name) }}"
                               data-required-for="creation">
                        @error('beneficiary_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="beneficiary_first_name" class="form-label">Prénom</label>
                        <input type="text" name="beneficiary_first_name" id="beneficiary_first_name" 
                               class="form-control @error('beneficiary_first_name') is-invalid @enderror" 
                               value="{{ old('beneficiary_first_name', $simRequest->beneficiary_first_name) }}">
                        @error('beneficiary_first_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="beneficiary_fonction" class="form-label">Fonction</label>
                        <select name="beneficiary_fonction" id="beneficiary_fonction" 
                                class="form-select @error('beneficiary_fonction') is-invalid @enderror">
                            <option value="">Sélectionner une fonction...</option>
                            @foreach($fonctions as $key => $fonction)
                                <option value="{{ $key }}" {{ old('beneficiary_fonction', $simRequest->beneficiary_fonction) == $key ? 'selected' : '' }}>
                                    {{ $fonction }}
                                </option>
                            @endforeach
                        </select>
                        @error('beneficiary_fonction')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="beneficiary_matricule" class="form-label">Matricule</label>
                        <input type="text" name="beneficiary_matricule" id="beneficiary_matricule" 
                               class="form-control @error('beneficiary_matricule') is-invalid @enderror" 
                               value="{{ old('beneficiary_matricule', $simRequest->beneficiary_matricule) }}">
                        @error('beneficiary_matricule')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label for="plan_id_creation" class="form-label">Forfait <span class="text-danger">*</span></label>
                    <select id="plan_id_creation" class="form-select @error('plan_id') is-invalid @enderror"
                            data-required-for="creation"
                            onchange="document.getElementById('plan_id_hidden').value = this.value">
                        <option value="">Sélectionner un forfait...</option>
                        @foreach($plans as $plan)
                            <option value="{{ $plan->id }}" 
                                    data-credit="{{ $plan->limite_credit }}" 
                                    data-data="{{ $plan->limite_data }}" 
                                    {{ old('plan_id', $simRequest->plan_id) == $plan->id ? 'selected' : '' }}>
                                {{ $plan->name }} - {{ number_format($plan->limite_credit, 0, ',', ' ') }} ariary / {{ $plan->limite_data }} GB
                            </option>
                        @endforeach
                    </select>
                    @error('plan_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="sim_id_creation" class="form-label">SIM (optionnel)</label>
                    <select name="sim_id" id="sim_id_creation" class="form-select @error('sim_id') is-invalid @enderror">
                        <option value="">Sélectionner une SIM...</option>
                        @foreach($sims as $sim)
                            <option value="{{ $sim->id }}" {{ old('sim_id', $simRequest->sim_id) == $sim->id ? 'selected' : '' }}>
                                {{ $sim->iccid }} - {{ $sim->operator ?? 'N/A' }}
                            </option>
                        @endforeach
                    </select>
                    @error('sim_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="requested_iccid_creation" class="form-label">ICCID (si SIM non listée)</label>
                    <input type="text" name="requested_iccid" id="requested_iccid_creation" 
                           class="form-control @error('requested_iccid') is-invalid @enderror" 
                           value="{{ old('requested_iccid', $simRequest->requested_iccid) }}">
                    @error('requested_iccid')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="motif_creation" class="form-label">Motif <span class="text-danger">*</span></label>
                    <textarea id="motif_creation" rows="3" 
                              class="form-control @error('motif') is-invalid @enderror"
                              data-required-for="creation"
                              oninput="document.getElementById('motif_hidden').value = this.value">{{ old('motif', $simRequest->motif) }}</textarea>
                    @error('motif')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <!-- Champs cachés pour garantir l'envoi des valeurs -->
            <input type="hidden" name="phone_number" id="phone_number_hidden" value="{{ old('phone_number', $simRequest->phone_number) }}">
            <input type="hidden" name="plan_id" id="plan_id_hidden" value="{{ old('plan_id', $simRequest->plan_id) }}">
            <input type="hidden" name="motif" id="motif_hidden" value="{{ old('motif', $simRequest->motif) }}">
            
            <!-- Formulaire pour Suspension / Désactivation -->
            <div id="suspension-desactivation-form" class="form-section-hidden">
                <div class="mb-3">
                    <label for="phone_number" class="form-label">Numéro de ligne <span class="text-danger">*</span></label>
                    <input type="text" id="phone_number" 
                           class="form-control @error('phone_number') is-invalid @enderror" 
                           value="{{ old('phone_number', $simRequest->phone_number) }}" 
                           placeholder="Ex: 0341012345 ou +261 34 12 345 67"
                           data-required-for="suspension,desactivation"
                           oninput="document.getElementById('phone_number_hidden').value = this.value">
                    @error('phone_number')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="motif_suspension" class="form-label">Motif <span class="text-danger">*</span></label>
                    <textarea id="motif_suspension" rows="4" 
                              class="form-control @error('motif') is-invalid @enderror"
                              data-required-for="suspension,desactivation"
                              oninput="document.getElementById('motif_hidden').value = this.value">{{ old('motif', $simRequest->motif) }}</textarea>
                    @error('motif')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <!-- Formulaire pour Ajustement -->
            <div id="ajustement-form" class="form-section-hidden">
                <div class="mb-3">
                    <label for="phone_number_ajustement" class="form-label">Numéro de ligne <span class="text-danger">*</span></label>
                    <input type="text" id="phone_number_ajustement" 
                           class="form-control @error('phone_number') is-invalid @enderror" 
                           value="{{ old('phone_number', $simRequest->phone_number) }}" 
                           placeholder="Ex: 0341012345 ou +261 34 12 345 67"
                           data-required-for="ajustement"
                           oninput="document.getElementById('phone_number_hidden').value = this.value">
                    @error('phone_number')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3 p-3 bg-light rounded">
                    <strong class="d-block mb-2">Modification partielle (optionnel)</strong>
                    <p class="text-muted small mb-2">Modifier uniquement la limite crédit (LC) et/ou la data sans changer l'autre.</p>
                    <div class="row g-2">
                        <div class="col-md-6">
                            <label for="limite_credit_override" class="form-label small">Limite crédit uniquement (ariary)</label>
                            <input type="number" name="limite_credit_override" id="limite_credit_override" min="0" step="1"
                                   class="form-control form-control-sm @error('limite_credit_override') is-invalid @enderror"
                                   value="{{ old('limite_credit_override', $simRequest->limite_credit !== null ? $simRequest->limite_credit : '') }}" placeholder="Ex: 25000 (vide = ne pas changer)">
                            @error('limite_credit_override')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="limite_data_override" class="form-label small">Limite data uniquement (Go)</label>
                            <input type="number" name="limite_data_override" id="limite_data_override" min="0" step="0.1"
                                   class="form-control form-control-sm @error('limite_data_override') is-invalid @enderror"
                                   value="{{ old('limite_data_override', $simRequest->limite_data !== null ? $simRequest->limite_data : '') }}" placeholder="Ex: 4.5 (vide = ne pas changer)">
                            @error('limite_data_override')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="plan_id_ajustement" class="form-label">Nouveau forfait complet</label>
                    <select id="plan_id_ajustement" class="form-select @error('plan_id') is-invalid @enderror"
                            onchange="document.getElementById('plan_id_hidden').value = this.value">
                        <option value="">Sélectionner un forfait (ou utiliser la modification partielle ci-dessus)...</option>
                        @foreach($plans as $plan)
                            <option value="{{ $plan->id }}" 
                                    data-credit="{{ $plan->limite_credit }}" 
                                    data-data="{{ $plan->limite_data }}" 
                                    {{ old('plan_id', $simRequest->plan_id) == $plan->id ? 'selected' : '' }}>
                                {{ $plan->name }} - {{ number_format($plan->limite_credit, 0, ',', ' ') }} ariary / {{ $plan->limite_data }} GB
                            </option>
                        @endforeach
                    </select>
                    <small class="form-text text-muted">Forfait complet OU au moins une limite (crédit ou data) ci-dessus.</small>
                    @error('plan_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
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

@push('styles')
<style>
.form-section-hidden {
    display: none !important;
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const requestType = '{{ $simRequest->request_type }}';
    const creationForm = document.getElementById('creation-form');
    const suspensionForm = document.getElementById('suspension-desactivation-form');
    const ajustementForm = document.getElementById('ajustement-form');
    const form = document.getElementById('requestForm');

    function toggleForms() {
        // Afficher le formulaire correspondant au type de demande
        if (requestType === 'creation') {
            creationForm.classList.remove('form-section-hidden');
            suspensionForm.classList.add('form-section-hidden');
            ajustementForm.classList.add('form-section-hidden');
        } else if (requestType === 'suspension' || requestType === 'desactivation') {
            creationForm.classList.add('form-section-hidden');
            suspensionForm.classList.remove('form-section-hidden');
            ajustementForm.classList.add('form-section-hidden');
        } else if (requestType === 'ajustement') {
            creationForm.classList.add('form-section-hidden');
            suspensionForm.classList.add('form-section-hidden');
            ajustementForm.classList.remove('form-section-hidden');
        }
        
        // Initialiser les champs cachés
        initializeHiddenFields();
    }

    function initializeHiddenFields() {
        if (requestType === 'creation') {
            const planId = document.getElementById('plan_id_creation');
            const planIdHidden = document.getElementById('plan_id_hidden');
            const motif = document.getElementById('motif_creation');
            const motifHidden = document.getElementById('motif_hidden');
            
            if (planId && planIdHidden) {
                planIdHidden.value = planId.value;
            }
            if (motif && motifHidden) {
                motifHidden.value = motif.value;
            }
        } else if (requestType === 'suspension' || requestType === 'desactivation') {
            const phoneNumber = document.getElementById('phone_number');
            const phoneNumberHidden = document.getElementById('phone_number_hidden');
            const motif = document.getElementById('motif_suspension');
            const motifHidden = document.getElementById('motif_hidden');
            
            if (phoneNumber && phoneNumberHidden) {
                phoneNumberHidden.value = phoneNumber.value;
            }
            if (motif && motifHidden) {
                motifHidden.value = motif.value;
            }
        } else if (requestType === 'ajustement') {
            const phoneNumber = document.getElementById('phone_number_ajustement');
            const phoneNumberHidden = document.getElementById('phone_number_hidden');
            const planId = document.getElementById('plan_id_ajustement');
            const planIdHidden = document.getElementById('plan_id_hidden');
            
            if (phoneNumber && phoneNumberHidden) {
                phoneNumberHidden.value = phoneNumber.value;
            }
            if (planId && planIdHidden) {
                planIdHidden.value = planId.value;
            }
        }
    }
    
    // Initialiser au chargement
    toggleForms();
    
    // Validation avant soumission
    form.addEventListener('submit', function(e) {
        // S'assurer que tous les champs sont actifs avant soumission
        const allInputs = form.querySelectorAll('input, textarea, select');
        allInputs.forEach(input => {
            input.removeAttribute('disabled');
        });
        
        // Initialiser les champs cachés une dernière fois
        initializeHiddenFields();
    });
});
</script>
@endpush
@endsection

