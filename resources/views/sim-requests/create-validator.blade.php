@extends('layouts.bootstrap')

@section('title', 'Nouvelle demande')
@section('page-title', 'Nouvelle demande')

@section('content')
<div class="card">
    <div class="card-body">
        <form method="POST" action="{{ route('sim-requests.store') }}" id="requestForm">
            @csrf

            <div class="mb-3">
                <label for="request_type" class="form-label">Type de demande <span class="text-danger">*</span></label>
                <select name="request_type" id="request_type" class="form-select @error('request_type') is-invalid @enderror" required>
                    <option value="">Sélectionner...</option>
                    <option value="recuperation" {{ old('request_type') === 'recuperation' ? 'selected' : '' }}>Récupération</option>
                    <option value="creation" {{ old('request_type') === 'creation' ? 'selected' : '' }}>Création</option>
                    <option value="suspension" {{ old('request_type') === 'suspension' ? 'selected' : '' }}>Suspension</option>
                    <option value="desactivation" {{ old('request_type') === 'desactivation' ? 'selected' : '' }}>Désactivation</option>
                    <option value="ajustement" {{ old('request_type') === 'ajustement' ? 'selected' : '' }}>Ajustement</option>
                </select>
                @error('request_type')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Formulaire pour Création -->
            <div id="creation-form" class="form-section-hidden">
                <h5 class="mb-3">Informations du bénéficiaire</h5>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="beneficiary_name" class="form-label">Nom <span class="text-danger">*</span></label>
                        <input type="text" name="beneficiary_name" id="beneficiary_name" 
                               class="form-control @error('beneficiary_name') is-invalid @enderror" 
                               value="{{ old('beneficiary_name') }}"
                               data-required-for="creation">
                        @error('beneficiary_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="beneficiary_first_name" class="form-label">Prénom</label>
                        <input type="text" name="beneficiary_first_name" id="beneficiary_first_name" 
                               class="form-control @error('beneficiary_first_name') is-invalid @enderror" 
                               value="{{ old('beneficiary_first_name') }}">
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
                                <option value="{{ $key }}" {{ old('beneficiary_fonction') == $key ? 'selected' : '' }}>
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
                               value="{{ old('beneficiary_matricule') }}">
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
                            <option value="{{ $plan->id }}" data-credit="{{ $plan->limite_credit }}" data-data="{{ $plan->limite_data }}" {{ old('plan_id') == $plan->id ? 'selected' : '' }}>
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
                            <option value="{{ $sim->id }}" {{ old('sim_id') == $sim->id ? 'selected' : '' }}>
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
                           value="{{ old('requested_iccid') }}">
                    @error('requested_iccid')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="motif_creation" class="form-label">Motif <span class="text-danger">*</span></label>
                    <textarea id="motif_creation" rows="3" 
                              class="form-control @error('motif') is-invalid @enderror"
                              data-required-for="creation"
                              oninput="document.getElementById('motif_hidden').value = this.value">{{ old('motif') }}</textarea>
                    @error('motif')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <!-- Champs cachés pour garantir l'envoi des valeurs (toujours présents dans le DOM) -->
            <input type="hidden" name="phone_number" id="phone_number_hidden" value="{{ old('phone_number') }}">
            <input type="hidden" name="plan_id" id="plan_id_hidden" value="{{ old('plan_id', '') }}">
            <input type="hidden" name="motif" id="motif_hidden" value="{{ old('motif', '') }}">
            
            <!-- Formulaire pour Récupération -->
            <div id="recuperation-form" class="form-section-hidden">
                @if($currentSim ?? null)
                <div class="alert alert-info mb-3">
                    <i class="bi bi-info-circle"></i> 
                    <strong>SIM actuelle détectée :</strong> {{ $currentSim->iccid }} 
                    @if($currentSim->phone_number)
                        - {{ $currentSim->phone_number }}
                    @endif
                </div>
                @endif

                <div class="mb-3">
                    <label for="phone_number_recuperation" class="form-label">Numéro de ligne concerné</label>
                    <input type="text" id="phone_number_recuperation" 
                           class="form-control @error('phone_number') is-invalid @enderror" 
                           value="{{ old('phone_number', $currentSim->phone_number ?? '') }}" 
                           placeholder="Ex: 0341012345 ou +261 34 12 345 67 (laisser vide pour utiliser le numéro de l'utilisateur)"
                           oninput="const hidden = document.getElementById('phone_number_hidden'); if (hidden) hidden.value = this.value.trim();">
                    <small class="form-text text-muted">Numéro de téléphone de la ligne à récupérer. Si vide, le numéro de l'utilisateur sera utilisé.</small>
                    @error('phone_number')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="sim_id_recuperation" class="form-label">SIM disponible</label>
                    <select name="sim_id" id="sim_id_recuperation" class="form-select @error('sim_id') is-invalid @enderror">
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
                    <label for="requested_iccid_recuperation" class="form-label">ICCID demandé (si SIM non listée)</label>
                    <input type="text" name="requested_iccid" id="requested_iccid_recuperation" 
                           class="form-control @error('requested_iccid') is-invalid @enderror" 
                           value="{{ old('requested_iccid') }}" placeholder="Ex: 89261012345678901234">
                    @error('requested_iccid')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="motif_recuperation" class="form-label">Motif <span class="text-danger">*</span></label>
                    <textarea id="motif_recuperation" rows="3" 
                              class="form-control @error('motif') is-invalid @enderror"
                              data-required-for="recuperation"
                              oninput="document.getElementById('motif_hidden').value = this.value">{{ old('motif') }}</textarea>
                    @error('motif')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            
            <!-- Formulaire pour Suspension / Désactivation -->
            <div id="suspension-desactivation-form" class="form-section-hidden">
                <div class="mb-3">
                    <label for="phone_number" class="form-label">Numéro de ligne <span class="text-danger">*</span></label>
                    <input type="text" id="phone_number" 
                           class="form-control @error('phone_number') is-invalid @enderror" 
                           value="{{ old('phone_number') }}" placeholder="Ex: 0341012345 ou +261 34 12 345 67"
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
                              oninput="document.getElementById('motif_hidden').value = this.value">{{ old('motif') }}</textarea>
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
                           value="{{ old('phone_number') }}" placeholder="Ex: 0341012345 ou +261 34 12 345 67"
                           data-required-for="ajustement"
                           oninput="document.getElementById('phone_number_hidden').value = this.value">
                    @error('phone_number')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="plan_id_ajustement" class="form-label">Nouveau forfait <span class="text-danger">*</span></label>
                    <select id="plan_id_ajustement" class="form-select @error('plan_id') is-invalid @enderror"
                            data-required-for="ajustement"
                            onchange="document.getElementById('plan_id_hidden').value = this.value">
                        <option value="">Sélectionner un forfait...</option>
                        @foreach($plans as $plan)
                            <option value="{{ $plan->id }}" data-credit="{{ $plan->limite_credit }}" data-data="{{ $plan->limite_data }}" {{ old('plan_id') == $plan->id ? 'selected' : '' }}>
                                {{ $plan->name }} - {{ number_format($plan->limite_credit, 0, ',', ' ') }} ariary / {{ $plan->limite_data }} GB
                            </option>
                        @endforeach
                    </select>
                    @error('plan_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="d-flex justify-content-between mt-4">
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
    const requestType = document.getElementById('request_type');
    const creationForm = document.getElementById('creation-form');
    const recuperationForm = document.getElementById('recuperation-form');
    const suspensionForm = document.getElementById('suspension-desactivation-form');
    const ajustementForm = document.getElementById('ajustement-form');
    const form = document.getElementById('requestForm');

    function toggleForms() {
        const type = requestType.value;
        
        // Utiliser classList au lieu de style.display pour éviter les problèmes
        if (type === 'recuperation') {
            recuperationForm.classList.remove('form-section-hidden');
            creationForm.classList.add('form-section-hidden');
            suspensionForm.classList.add('form-section-hidden');
            ajustementForm.classList.add('form-section-hidden');
        } else if (type === 'creation') {
            recuperationForm.classList.add('form-section-hidden');
            creationForm.classList.remove('form-section-hidden');
            suspensionForm.classList.add('form-section-hidden');
            ajustementForm.classList.add('form-section-hidden');
        } else if (type === 'suspension' || type === 'desactivation') {
            recuperationForm.classList.add('form-section-hidden');
            creationForm.classList.add('form-section-hidden');
            suspensionForm.classList.remove('form-section-hidden');
            ajustementForm.classList.add('form-section-hidden');
        } else if (type === 'ajustement') {
            recuperationForm.classList.add('form-section-hidden');
            creationForm.classList.add('form-section-hidden');
            suspensionForm.classList.add('form-section-hidden');
            ajustementForm.classList.remove('form-section-hidden');
        } else {
            recuperationForm.classList.add('form-section-hidden');
            creationForm.classList.add('form-section-hidden');
            suspensionForm.classList.add('form-section-hidden');
            ajustementForm.classList.add('form-section-hidden');
        }
        
        // S'assurer que les champs cachés sont toujours actifs (pas disabled)
        const allInputs = form.querySelectorAll('input, textarea, select');
        allInputs.forEach(input => {
            // Ne pas désactiver les champs, même s'ils sont cachés
            if (input.hasAttribute('disabled') && input.getAttribute('data-required-for')) {
                input.removeAttribute('disabled');
            }
            
            // Gérer les attributs required selon le type de demande
            const requiredFor = input.getAttribute('data-required-for');
            if (requiredFor) {
                const types = requiredFor.split(',').map(t => t.trim());
                if (types.includes(type)) {
                    input.setAttribute('required', 'required');
                    input.removeAttribute('disabled');
                } else {
                    input.removeAttribute('required');
                }
            }
        });
    }

    requestType.addEventListener('change', toggleForms);
    toggleForms(); // Initialiser au chargement
    
    // Initialiser les champs cachés au chargement si des valeurs existent
    function initializeHiddenFields() {
        const type = requestType.value;
        
        if (type === 'recuperation') {
            const phoneNumber = document.getElementById('phone_number_recuperation');
            const phoneNumberHidden = document.getElementById('phone_number_hidden');
            const motif = document.getElementById('motif_recuperation');
            const motifHidden = document.getElementById('motif_hidden');
            
            // Copier la valeur du champ visible vers le champ caché (même si vide)
            if (phoneNumber && phoneNumberHidden) {
                phoneNumberHidden.value = phoneNumber.value.trim();
            }
            if (motif && motifHidden) {
                motifHidden.value = motif.value;
            }
        } else if (type === 'creation') {
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
        } else if (type === 'suspension' || type === 'desactivation') {
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
        } else if (type === 'ajustement') {
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
    
    // Initialiser les champs cachés au chargement
    initializeHiddenFields();
    
    // Réinitialiser les champs cachés quand le type change
    requestType.addEventListener('change', function() {
        setTimeout(initializeHiddenFields, 100);
    });
    
    // Validation avant soumission
    form.addEventListener('submit', function(e) {
        const type = requestType.value;
        
        // S'assurer que tous les champs sont actifs avant soumission
        const allInputs = form.querySelectorAll('input, textarea, select');
        allInputs.forEach(input => {
            input.removeAttribute('disabled');
        });
        
        // Vérifier les champs requis selon le type
        if (type === 'recuperation') {
            const phoneNumber = document.getElementById('phone_number_recuperation');
            const phoneNumberHidden = document.getElementById('phone_number_hidden');
            const motif = document.getElementById('motif_recuperation');
            const motifHidden = document.getElementById('motif_hidden');
            
            // S'assurer que le formulaire de récupération est visible avant soumission
            recuperationForm.classList.remove('form-section-hidden');
            
            // Copier les valeurs vers les champs cachés (phone_number peut être vide)
            // Utiliser trim() pour s'assurer qu'une chaîne vide est vraiment vide
            if (phoneNumber && phoneNumberHidden) {
                phoneNumberHidden.value = phoneNumber.value.trim();
            }
            if (motif && motifHidden) {
                motifHidden.value = motif.value;
            }
            
            // Le phone_number n'est plus requis (peut être vide, on utilisera le numéro de l'utilisateur)
            // Vérifier seulement le motif
            const motifValue = motif ? motif.value.trim() : (motifHidden ? motifHidden.value.trim() : '');
            if (!motifValue) {
                e.preventDefault();
                if (motif) {
                    motif.focus();
                    motif.classList.add('is-invalid');
                }
                alert('Le motif est requis.');
                return false;
            }
        } else if (type === 'creation') {
            const beneficiaryName = document.getElementById('beneficiary_name');
            const planId = document.getElementById('plan_id_creation');
            const planIdHidden = document.getElementById('plan_id_hidden');
            const motif = document.getElementById('motif_creation');
            const motifHidden = document.getElementById('motif_hidden');
            
            // S'assurer que le formulaire de création est visible avant soumission
            creationForm.classList.remove('form-section-hidden');
            
            // Copier les valeurs vers les champs cachés
            if (planId && planIdHidden) {
                planIdHidden.value = planId.value;
            }
            if (motif && motifHidden) {
                motifHidden.value = motif.value;
            }
            
            if (!beneficiaryName || !beneficiaryName.value || !beneficiaryName.value.trim()) {
                e.preventDefault();
                if (beneficiaryName) {
                    beneficiaryName.focus();
                    beneficiaryName.classList.add('is-invalid');
                }
                alert('Le nom du bénéficiaire est requis.');
                return false;
            }
            
            const planValue = planId ? planId.value : (planIdHidden ? planIdHidden.value : '');
            if (!planValue) {
                e.preventDefault();
                if (planId) {
                    planId.focus();
                    planId.classList.add('is-invalid');
                }
                alert('Le forfait est requis.');
                return false;
            }
            
            const motifValue = motif ? motif.value.trim() : (motifHidden ? motifHidden.value.trim() : '');
            if (!motifValue) {
                e.preventDefault();
                if (motif) {
                    motif.focus();
                    motif.classList.add('is-invalid');
                }
                alert('Le motif est requis.');
                return false;
            }
            
            // Debug
            console.log('Soumission création - plan_id:', planValue, 'motif:', motifValue);
        } else if (type === 'suspension' || type === 'desactivation') {
            const phoneNumber = document.getElementById('phone_number');
            const phoneNumberHidden = document.getElementById('phone_number_hidden');
            const motif = document.getElementById('motif_suspension');
            const motifHidden = document.getElementById('motif_hidden');
            
            // S'assurer que le formulaire de suspension est visible avant soumission
            suspensionForm.classList.remove('form-section-hidden');
            
            // Copier les valeurs vers les champs cachés
            if (phoneNumber && phoneNumberHidden) {
                phoneNumberHidden.value = phoneNumber.value;
            }
            if (motif && motifHidden) {
                motifHidden.value = motif.value;
            }
            
            // Vérifier que les champs existent et ont une valeur
            const phoneValue = phoneNumber ? phoneNumber.value.trim() : (phoneNumberHidden ? phoneNumberHidden.value.trim() : '');
            
            if (!phoneValue) {
                e.preventDefault();
                if (phoneNumber) {
                    phoneNumber.focus();
                    phoneNumber.classList.add('is-invalid');
                }
                alert('Le numéro de ligne est requis.');
                return false;
            }
            
            const motifValue = motif ? motif.value.trim() : (motifHidden ? motifHidden.value.trim() : '');
            if (!motifValue) {
                e.preventDefault();
                if (motif) {
                    motif.focus();
                    motif.classList.add('is-invalid');
                }
                alert('Le motif est requis.');
                return false;
            }
            
            // Debug: afficher la valeur avant soumission
            console.log('Soumission suspension/desactivation - phone_number:', phoneValue, 'motif:', motifValue);
        } else if (type === 'ajustement') {
            const phoneNumber = document.getElementById('phone_number_ajustement');
            const phoneNumberHidden = document.getElementById('phone_number_hidden');
            const planId = document.getElementById('plan_id_ajustement');
            const planIdHidden = document.getElementById('plan_id_hidden');
            
            // S'assurer que le formulaire d'ajustement est visible avant soumission
            ajustementForm.classList.remove('form-section-hidden');
            
            // Copier les valeurs vers les champs cachés
            if (phoneNumber && phoneNumberHidden) {
                phoneNumberHidden.value = phoneNumber.value;
            }
            if (planId && planIdHidden) {
                planIdHidden.value = planId.value;
            }
            
            const phoneValue = phoneNumber ? phoneNumber.value.trim() : (phoneNumberHidden ? phoneNumberHidden.value.trim() : '');
            
            if (!phoneValue) {
                e.preventDefault();
                if (phoneNumber) {
                    phoneNumber.focus();
                    phoneNumber.classList.add('is-invalid');
                }
                alert('Le numéro de ligne est requis.');
                return false;
            }
            
            const planValue = planId ? planId.value : (planIdHidden ? planIdHidden.value : '');
            if (!planValue) {
                e.preventDefault();
                if (planId) {
                    planId.focus();
                    planId.classList.add('is-invalid');
                }
                alert('Le forfait est requis.');
                return false;
            }
        }
    });
});
</script>
@endpush
@endsection

