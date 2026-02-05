@extends('layouts.bootstrap')

@section('title', 'Nouvelle demande')
@section('page-title', 'Nouvelle demande')

@section('content')
<div class="row g-4">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-body">
                <form method="POST" action="{{ route('sim-requests.store') }}" id="requestForm">
            @csrf

            <div class="wizard-steps mb-3" id="wizard-steps">
                <button type="button" class="step-item active" data-target="#step-1">1. Type & Collaborateur</button>
                <button type="button" class="step-item" data-target="#step-2">2. Détails</button>
                <button type="button" class="step-item" data-target="#step-3">3. Vérification</button>
            </div>
            <div class="alert alert-info" style="font-size: 13px;">
                <div><strong>1.</strong> Choisissez le type et le collaborateur concerné.</div>
                <div><strong>2.</strong> Renseignez la ligne/SIM/ICCID/forfait selon le type.</div>
                <div><strong>3.</strong> Vérifiez le récapitulatif à droite avant d'envoyer.</div>
                <div class="text-muted mt-2">Le récapitulatif se met à jour automatiquement. Les modèles sont enregistrés sur cet appareil.</div>
            </div>

            <div id="step-1" class="step-anchor"></div>

            <div class="mb-3">
                <label for="request_type" class="form-label">Type de demande <span class="text-danger">*</span></label>
                <select name="request_type" id="request_type" class="form-select @error('request_type') is-invalid @enderror" required>
                    <option value="">Sélectionner...</option>
                    <option value="recuperation" {{ old('request_type', $prefill['request_type'] ?? '') === 'recuperation' ? 'selected' : '' }}>Récupération</option>
                    <option value="creation" {{ old('request_type', $prefill['request_type'] ?? '') === 'creation' ? 'selected' : '' }}>Création</option>
                    <option value="suspension" {{ old('request_type', $prefill['request_type'] ?? '') === 'suspension' ? 'selected' : '' }}>Suspension</option>
                    <option value="desactivation" {{ old('request_type', $prefill['request_type'] ?? '') === 'desactivation' ? 'selected' : '' }}>Désactivation</option>
                    <option value="ajustement" {{ old('request_type', $prefill['request_type'] ?? '') === 'ajustement' ? 'selected' : '' }}>Ajustement</option>
                </select>
                @error('request_type')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div id="collaborator-block" class="form-section-hidden">
                <h5 class="mb-3">Informations du collaborateur</h5>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="collaborator_matricule" class="form-label">Matricule <span class="text-danger">*</span></label>
                        <input type="text" name="collaborator_matricule" id="collaborator_matricule"
                               class="form-control @error('collaborator_matricule') is-invalid @enderror"
                               value="{{ old('collaborator_matricule', $prefill['collaborator_matricule'] ?? '') }}"
                               data-required-for="recuperation,suspension,desactivation,ajustement">
                        @error('collaborator_matricule')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="collaborator_name" class="form-label">Nom</label>
                        <input type="text" name="collaborator_name" id="collaborator_name"
                               class="form-control @error('collaborator_name') is-invalid @enderror"
                               value="{{ old('collaborator_name', $prefill['collaborator_name'] ?? '') }}">
                        @error('collaborator_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="collaborator_first_name" class="form-label">Prénoms</label>
                        <input type="text" name="collaborator_first_name" id="collaborator_first_name"
                               class="form-control @error('collaborator_first_name') is-invalid @enderror"
                               value="{{ old('collaborator_first_name', $prefill['collaborator_first_name'] ?? '') }}">
                        @error('collaborator_first_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="mb-3">
                    <label for="collaborator_agence" class="form-label">Agence</label>
                    <input type="text" name="collaborator_agence" id="collaborator_agence"
                           class="form-control @error('collaborator_agence') is-invalid @enderror"
                           value="{{ old('collaborator_agence', $prefill['collaborator_agence'] ?? '') }}">
                    @error('collaborator_agence')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div id="collaborator-history" class="alert alert-light border d-none" style="font-size: 13px;">
                    <strong>Historique collaborateur</strong>
                    <div id="collaborator-history-lines" class="mt-1 text-muted">Lignes récentes: -</div>
                    <div id="collaborator-history-plans" class="mt-1 text-muted">Forfaits récents: -</div>
                    <div id="collaborator-history-sim" class="mt-1 text-muted"></div>
                </div>
            </div>

            <div id="step-2" class="step-anchor"></div>

            <!-- Formulaire pour Création -->
            <div id="creation-form" class="form-section-hidden">
                <h5 class="mb-3">Informations du bénéficiaire</h5>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="beneficiary_name" class="form-label">Nom <span class="text-danger">*</span></label>
                        <input type="text" name="beneficiary_name" id="beneficiary_name" 
                               class="form-control @error('beneficiary_name') is-invalid @enderror" 
                               value="{{ old('beneficiary_name', $prefill['beneficiary_name'] ?? '') }}"
                               data-required-for="creation">
                        @error('beneficiary_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="beneficiary_first_name" class="form-label">Prénom</label>
                        <input type="text" name="beneficiary_first_name" id="beneficiary_first_name" 
                               class="form-control @error('beneficiary_first_name') is-invalid @enderror" 
                               value="{{ old('beneficiary_first_name', $prefill['beneficiary_first_name'] ?? '') }}">
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
                                <option value="{{ $key }}" {{ old('beneficiary_fonction', $prefill['beneficiary_fonction'] ?? '') == $key ? 'selected' : '' }}>
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
                               value="{{ old('beneficiary_matricule', $prefill['beneficiary_matricule'] ?? '') }}">
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
                            <option value="{{ $plan->id }}" data-credit="{{ $plan->limite_credit }}" data-data="{{ $plan->limite_data }}" {{ old('plan_id', $prefill['plan_id'] ?? '') == $plan->id ? 'selected' : '' }}>
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
                    <select id="sim_id_creation" class="form-select @error('sim_id') is-invalid @enderror"
                            onchange="document.getElementById('sim_id_hidden').value = this.value">
                        <option value="">Sélectionner une SIM...</option>
                        @foreach($sims as $sim)
                            <option value="{{ $sim->id }}" {{ old('sim_id', $prefill['sim_id'] ?? '') == $sim->id ? 'selected' : '' }}>
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
                           value="{{ old('requested_iccid', $prefill['requested_iccid'] ?? '') }}">
                    @error('requested_iccid')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="motif_creation" class="form-label">Motif <span class="text-danger">*</span></label>
                    <textarea id="motif_creation" rows="3" 
                              class="form-control @error('motif') is-invalid @enderror"
                              data-required-for="creation"
                              oninput="document.getElementById('motif_hidden').value = this.value">{{ old('motif', $prefill['motif'] ?? '') }}</textarea>
                    @error('motif')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <!-- Champs cachés pour garantir l'envoi des valeurs (toujours présents dans le DOM) -->
            <input type="hidden" name="phone_number" id="phone_number_hidden" value="{{ old('phone_number', $prefill['phone_number'] ?? '') }}">
            <input type="hidden" name="plan_id" id="plan_id_hidden" value="{{ old('plan_id', $prefill['plan_id'] ?? '') }}">
            <input type="hidden" name="motif" id="motif_hidden" value="{{ old('motif', $prefill['motif'] ?? '') }}">
            <input type="hidden" name="sim_id" id="sim_id_hidden" value="{{ old('sim_id', $prefill['sim_id'] ?? '') }}">
            
            <!-- Formulaire pour Récupération -->
            <div id="recuperation-form" class="form-section-hidden">
                <div class="mb-3">
                    <label for="phone_number_recuperation" class="form-label">Numéro de ligne concerné</label>
                    <input type="text" id="phone_number_recuperation" 
                           class="form-control @error('phone_number') is-invalid @enderror" 
                           value="{{ old('phone_number', $prefill['phone_number'] ?? ($currentSim->phone_number ?? '')) }}" 
                           placeholder="Ex: 0341012345 ou +261 34 12 345 67 (laisser vide pour utiliser le numéro de l'utilisateur)"
                           oninput="const hidden = document.getElementById('phone_number_hidden'); if (hidden) hidden.value = this.value.trim();">
                    <small class="form-text text-muted">Numéro de téléphone de la ligne à récupérer. Si vide, le numéro de l'utilisateur sera utilisé.</small>
                    @error('phone_number')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="sim_id_recuperation" class="form-label">SIM disponible</label>
                    <select id="sim_id_recuperation" class="form-select @error('sim_id') is-invalid @enderror"
                            onchange="document.getElementById('sim_id_hidden').value = this.value">
                        <option value="">Sélectionner une SIM libre...</option>
                        @foreach($sims as $sim)
                            <option value="{{ $sim->id }}" {{ old('sim_id', $prefill['sim_id'] ?? '') == $sim->id ? 'selected' : '' }}>
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
                           value="{{ old('requested_iccid', $prefill['requested_iccid'] ?? '') }}" placeholder="Ex: 89261012345678901234">
                    @error('requested_iccid')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="motif_recuperation" class="form-label">Motif <span class="text-danger">*</span></label>
                    <textarea id="motif_recuperation" rows="3" 
                              class="form-control @error('motif') is-invalid @enderror"
                              data-required-for="recuperation"
                              oninput="document.getElementById('motif_hidden').value = this.value">{{ old('motif', $prefill['motif'] ?? '') }}</textarea>
                    @error('motif')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            
            <!-- Formulaire pour Suspension / Désactivation -->
            <div id="suspension-desactivation-form" class="form-section-hidden">
                <div class="mb-3">
                    <label for="phone_number" class="form-label">Numéro de ligne concerné <span class="text-danger">*</span></label>
                    <input type="text" id="phone_number" 
                           class="form-control @error('phone_number') is-invalid @enderror" 
                           value="{{ old('phone_number', $prefill['phone_number'] ?? '') }}" placeholder="Ex: 0341012345 ou +261 34 12 345 67"
                           data-required-for="suspension,desactivation"
                           oninput="document.getElementById('phone_number_hidden').value = this.value">
                    <small class="form-text text-muted">Numéro de la ligne à suspendre/désactiver.</small>
                    @error('phone_number')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="motif_suspension" class="form-label">Motif <span class="text-danger">*</span></label>
                    <textarea id="motif_suspension" rows="4" 
                              class="form-control @error('motif') is-invalid @enderror"
                              data-required-for="suspension,desactivation"
                              oninput="document.getElementById('motif_hidden').value = this.value">{{ old('motif', $prefill['motif'] ?? '') }}</textarea>
                    @error('motif')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <!-- Formulaire pour Ajustement -->
            <div id="ajustement-form" class="form-section-hidden">
                <div class="mb-3">
                    <label for="phone_number_ajustement" class="form-label">Numéro de ligne concerné <span class="text-danger">*</span></label>
                    <input type="text" id="phone_number_ajustement" 
                           class="form-control @error('phone_number') is-invalid @enderror" 
                           value="{{ old('phone_number', $prefill['phone_number'] ?? '') }}" placeholder="Ex: 0341012345 ou +261 34 12 345 67"
                           data-required-for="ajustement"
                           oninput="document.getElementById('phone_number_hidden').value = this.value">
                    <small class="form-text text-muted">Numéro de la ligne à ajuster.</small>
                    @error('phone_number')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3 p-3 bg-light rounded">
                    <strong class="d-block mb-2">Modification partielle (optionnel)</strong>
                    <p class="text-muted small mb-2">Modifier uniquement la limite crédit (LC) et/ou la data sans changer l'autre. Si vous renseignez une valeur ci-dessous, l'autre limite reste celle du forfait actuel.</p>
                    <div class="row g-2">
                        <div class="col-md-6">
                            <label for="limite_credit_override" class="form-label small">Limite crédit uniquement (ariary)</label>
                            <input type="number" name="limite_credit_override" id="limite_credit_override" min="0" step="1"
                                   class="form-control form-control-sm @error('limite_credit_override') is-invalid @enderror"
                                   value="{{ old('limite_credit_override', $prefill['limite_credit_override'] ?? '') }}" placeholder="Ex: 25000">
                            @error('limite_credit_override')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="limite_data_override" class="form-label small">Limite data uniquement (Go)</label>
                            <input type="number" name="limite_data_override" id="limite_data_override" min="0" step="0.1"
                                   class="form-control form-control-sm @error('limite_data_override') is-invalid @enderror"
                                   value="{{ old('limite_data_override', $prefill['limite_data_override'] ?? '') }}" placeholder="Ex: 4.5">
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
                            <option value="{{ $plan->id }}" data-credit="{{ $plan->limite_credit }}" data-data="{{ $plan->limite_data }}" {{ old('plan_id', $prefill['plan_id'] ?? '') == $plan->id ? 'selected' : '' }}>
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

            <div id="step-3" class="step-anchor"></div>

            <div class="mb-3">
                <label for="template_select" class="form-label">Modèle de demande (optionnel)</label>
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <select id="template_select" class="form-select form-select-sm w-auto" style="max-width: 260px;">
                        <option value="">Aucun modèle</option>
                    </select>
                    <button type="button" class="btn btn-sm btn-outline-secondary" id="template_apply">
                        Appliquer
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-primary" id="template_save">
                        Enregistrer
                    </button>
                </div>
                <small class="form-text text-muted">Appliquer = pré-remplir. Enregistrer = sauver le formulaire actuel (sur cet appareil).</small>
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
    </div>
    <div class="col-lg-4">
        <div class="card sticky-summary">
            <div class="card-header bg-light">
                <strong>Récapitulatif</strong>
            </div>
            <div class="card-body">
                <div class="text-muted mb-2" style="font-size: 12px;">Mise à jour automatique</div>
                <ul class="list-unstyled mb-0 summary-list">
                    <li class="d-flex justify-content-between">
                        <span>Type</span>
                        <strong id="summary-type">-</strong>
                    </li>
                    <li class="d-flex justify-content-between">
                        <span>Collaborateur</span>
                        <strong id="summary-collaborator">-</strong>
                    </li>
                    <li class="d-flex justify-content-between">
                        <span>Ligne concernée</span>
                        <strong id="summary-line">-</strong>
                    </li>
                    <li class="d-flex justify-content-between">
                        <span>SIM</span>
                        <strong id="summary-sim">-</strong>
                    </li>
                    <li class="d-flex justify-content-between">
                        <span>ICCID</span>
                        <strong id="summary-iccid">-</strong>
                    </li>
                    <li class="d-flex justify-content-between">
                        <span>Forfait</span>
                        <strong id="summary-plan">-</strong>
                    </li>
                    <li class="d-flex justify-content-between">
                        <span>Motif</span>
                        <strong id="summary-motif">-</strong>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
.form-section-hidden {
    display: none !important;
}
.sticky-summary {
    position: sticky;
    top: 90px;
}
.summary-list li {
    padding: 6px 0;
    border-bottom: 1px dashed #e5e7eb;
    font-size: 14px;
}
.summary-list li:last-child {
    border-bottom: none;
}
.wizard-steps {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
}
.wizard-steps .step-item {
    border: 1px solid #e2e8f0;
    background: #f8fafc;
    padding: 6px 10px;
    border-radius: 999px;
    font-size: 12px;
}
.wizard-steps .step-item.active {
    background: #3b82f6;
    border-color: #3b82f6;
    color: white;
}
.step-anchor {
    scroll-margin-top: 110px;
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const requestType = document.getElementById('request_type');
    const creationForm = document.getElementById('creation-form');
    const collaboratorBlock = document.getElementById('collaborator-block');
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

        if (collaboratorBlock) {
            if (type && type !== 'creation') {
                collaboratorBlock.classList.remove('form-section-hidden');
            } else {
                collaboratorBlock.classList.add('form-section-hidden');
            }
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

        updateSummary();
    }

    requestType.addEventListener('change', toggleForms);
    toggleForms(); // Initialiser au chargement
    
    // Initialiser les champs cachés au chargement si des valeurs existent
    function initializeHiddenFields() {
        const type = requestType.value;
        const simIdHidden = document.getElementById('sim_id_hidden');
        
        if (type === 'recuperation') {
            const phoneNumber = document.getElementById('phone_number_recuperation');
            const phoneNumberHidden = document.getElementById('phone_number_hidden');
            const motif = document.getElementById('motif_recuperation');
            const motifHidden = document.getElementById('motif_hidden');
            const simId = document.getElementById('sim_id_recuperation');
            
            // Copier la valeur du champ visible vers le champ caché (même si vide)
            if (phoneNumber && phoneNumberHidden) {
                phoneNumberHidden.value = phoneNumber.value.trim();
            }
            if (motif && motifHidden) {
                motifHidden.value = motif.value;
            }
            if (simId && simIdHidden) {
                simIdHidden.value = simId.value;
            }
        } else if (type === 'creation') {
            const planId = document.getElementById('plan_id_creation');
            const planIdHidden = document.getElementById('plan_id_hidden');
            const motif = document.getElementById('motif_creation');
            const motifHidden = document.getElementById('motif_hidden');
            const simId = document.getElementById('sim_id_creation');
            
            if (planId && planIdHidden) {
                planIdHidden.value = planId.value;
            }
            if (motif && motifHidden) {
                motifHidden.value = motif.value;
            }
            if (simId && simIdHidden) {
                simIdHidden.value = simId.value;
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
            // Réinitialiser sim_id pour suspension/desactivation
            if (simIdHidden) {
                simIdHidden.value = '';
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
            // Réinitialiser sim_id pour ajustement
            if (simIdHidden) {
                simIdHidden.value = '';
            }
        } else {
            // Réinitialiser sim_id si aucun type n'est sélectionné
            if (simIdHidden) {
                simIdHidden.value = '';
            }
        }
    }

    function updateSummary() {
        const type = requestType.value;
        const typeLabels = {
            recuperation: 'Récupération',
            creation: 'Création',
            suspension: 'Suspension',
            desactivation: 'Désactivation',
            ajustement: 'Ajustement',
        };
        const typeLabel = typeLabels[type] || '-';

        const collaboratorName = [
            document.getElementById('collaborator_name')?.value?.trim() || '',
            document.getElementById('collaborator_first_name')?.value?.trim() || ''
        ].filter(Boolean).join(' ');
        const collaboratorMatricule = document.getElementById('collaborator_matricule')?.value?.trim() || '';

        const beneficiaryName = [
            document.getElementById('beneficiary_name')?.value?.trim() || '',
            document.getElementById('beneficiary_first_name')?.value?.trim() || ''
        ].filter(Boolean).join(' ');
        const beneficiaryMatricule = document.getElementById('beneficiary_matricule')?.value?.trim() || '';

        let collaboratorDisplay = '-';
        if (type === 'creation') {
            collaboratorDisplay = beneficiaryName || beneficiaryMatricule || '-';
        } else if (type) {
            collaboratorDisplay = collaboratorName || collaboratorMatricule || '-';
        }

        const lineByType = {
            recuperation: document.getElementById('phone_number_recuperation')?.value?.trim() || '',
            suspension: document.getElementById('phone_number')?.value?.trim() || '',
            desactivation: document.getElementById('phone_number')?.value?.trim() || '',
            ajustement: document.getElementById('phone_number_ajustement')?.value?.trim() || '',
        };
        const lineNumber = lineByType[type] || '';

        const simByType = {
            recuperation: document.getElementById('sim_id_recuperation'),
            creation: document.getElementById('sim_id_creation'),
        };
        const simSelect = simByType[type] || null;
        const simLabel = simSelect && simSelect.value ? simSelect.options[simSelect.selectedIndex].text : '';

        const iccidByType = {
            recuperation: document.getElementById('requested_iccid_recuperation')?.value?.trim() || '',
            creation: document.getElementById('requested_iccid_creation')?.value?.trim() || '',
        };
        const iccidValue = iccidByType[type] || '';

        const planByType = {
            creation: document.getElementById('plan_id_creation'),
            ajustement: document.getElementById('plan_id_ajustement'),
        };
        const planSelect = planByType[type] || null;
        let planLabel = planSelect && planSelect.value ? planSelect.options[planSelect.selectedIndex].text : '';
        if (type === 'ajustement') {
            const co = document.getElementById('limite_credit_override')?.value;
            const dto = document.getElementById('limite_data_override')?.value;
            if (co || dto) {
                const parts = [];
                if (co) parts.push('LC: ' + co + ' ar');
                if (dto) parts.push('Data: ' + dto + ' Go');
                if (planLabel) planLabel = parts.join(' / ') + ' (partiel) — ' + planLabel;
                else planLabel = parts.join(' / ') + ' (partiel)';
            }
        }

        const motifByType = {
            recuperation: document.getElementById('motif_recuperation')?.value?.trim() || '',
            creation: document.getElementById('motif_creation')?.value?.trim() || '',
            suspension: document.getElementById('motif_suspension')?.value?.trim() || '',
            desactivation: document.getElementById('motif_suspension')?.value?.trim() || '',
        };
        const motifValue = motifByType[type] || '';

        document.getElementById('summary-type').textContent = typeLabel;
        document.getElementById('summary-collaborator').textContent = collaboratorDisplay;
        document.getElementById('summary-line').textContent = lineNumber || '-';
        document.getElementById('summary-sim').textContent = simLabel || '-';
        document.getElementById('summary-iccid').textContent = iccidValue || '-';
        document.getElementById('summary-plan').textContent = planLabel || '-';
        document.getElementById('summary-motif').textContent = motifValue ? motifValue.substring(0, 60) : '-';
    }

    const templateStorageKey = 'sim-requests-templates';

    function getTemplates() {
        try {
            return JSON.parse(localStorage.getItem(templateStorageKey)) || [];
        } catch (e) {
            return [];
        }
    }

    function saveTemplates(templates) {
        localStorage.setItem(templateStorageKey, JSON.stringify(templates));
    }

    function renderTemplateOptions() {
        const select = document.getElementById('template_select');
        if (!select) return;
        const type = requestType.value;
        const templates = getTemplates().filter(t => !type || t.request_type === type);

        select.innerHTML = '<option value="">Aucun modèle</option>';
        templates.forEach(t => {
            const option = document.createElement('option');
            option.value = t.id;
            option.textContent = t.name;
            select.appendChild(option);
        });
    }

    function collectTemplateData() {
        return {
            request_type: requestType.value,
            fields: {
                collaborator_matricule: document.getElementById('collaborator_matricule')?.value || '',
                collaborator_name: document.getElementById('collaborator_name')?.value || '',
                collaborator_first_name: document.getElementById('collaborator_first_name')?.value || '',
                collaborator_agence: document.getElementById('collaborator_agence')?.value || '',
                beneficiary_name: document.getElementById('beneficiary_name')?.value || '',
                beneficiary_first_name: document.getElementById('beneficiary_first_name')?.value || '',
                beneficiary_fonction: document.getElementById('beneficiary_fonction')?.value || '',
                beneficiary_matricule: document.getElementById('beneficiary_matricule')?.value || '',
                phone_number: document.getElementById('phone_number')?.value || '',
                phone_number_recuperation: document.getElementById('phone_number_recuperation')?.value || '',
                phone_number_ajustement: document.getElementById('phone_number_ajustement')?.value || '',
                sim_id_creation: document.getElementById('sim_id_creation')?.value || '',
                sim_id_recuperation: document.getElementById('sim_id_recuperation')?.value || '',
                requested_iccid_creation: document.getElementById('requested_iccid_creation')?.value || '',
                requested_iccid_recuperation: document.getElementById('requested_iccid_recuperation')?.value || '',
                plan_id_creation: document.getElementById('plan_id_creation')?.value || '',
                plan_id_ajustement: document.getElementById('plan_id_ajustement')?.value || '',
                limite_credit_override: document.getElementById('limite_credit_override')?.value || '',
                limite_data_override: document.getElementById('limite_data_override')?.value || '',
                motif_creation: document.getElementById('motif_creation')?.value || '',
                motif_recuperation: document.getElementById('motif_recuperation')?.value || '',
                motif_suspension: document.getElementById('motif_suspension')?.value || '',
            }
        };
    }

    function applyTemplate(template) {
        if (!template) return;
        requestType.value = template.request_type;
        toggleForms();
        const fields = template.fields || {};

        Object.keys(fields).forEach(key => {
            const el = document.getElementById(key);
            if (el) {
                el.value = fields[key];
            }
        });

        initializeHiddenFields();
        updateSummary();
    }
    
    // Initialiser les champs cachés au chargement
    initializeHiddenFields();
    updateSummary();
    renderTemplateOptions();
    
    // Réinitialiser les champs cachés quand le type change
    requestType.addEventListener('change', function() {
        setTimeout(initializeHiddenFields, 100);
        setTimeout(updateSummary, 120);
        setTimeout(renderTemplateOptions, 150);
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
            const simId = document.getElementById('sim_id_recuperation');
            const simIdHidden = document.getElementById('sim_id_hidden');
            
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
            if (simId && simIdHidden) {
                simIdHidden.value = simId.value;
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
            const simId = document.getElementById('sim_id_creation');
            const simIdHidden = document.getElementById('sim_id_hidden');
            
            // S'assurer que le formulaire de création est visible avant soumission
            creationForm.classList.remove('form-section-hidden');
            
            // Copier les valeurs vers les champs cachés
            if (planId && planIdHidden) {
                planIdHidden.value = planId.value;
            }
            if (motif && motifHidden) {
                motifHidden.value = motif.value;
            }
            if (simId && simIdHidden) {
                simIdHidden.value = simId.value;
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
            const creditOverride = document.getElementById('limite_credit_override');
            const dataOverride = document.getElementById('limite_data_override');
            
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
            const hasCreditOverride = creditOverride && creditOverride.value !== '' && creditOverride.value !== null;
            const hasDataOverride = dataOverride && dataOverride.value !== '' && dataOverride.value !== null;
            if (!planValue && !hasCreditOverride && !hasDataOverride) {
                e.preventDefault();
                if (planId) {
                    planId.focus();
                    planId.classList.add('is-invalid');
                }
                alert('Veuillez sélectionner un forfait complet ou renseigner une limite crédit et/ou data à modifier.');
                return false;
            }
        }
    });

    form.addEventListener('input', updateSummary);
    form.addEventListener('change', updateSummary);

    const templateApplyBtn = document.getElementById('template_apply');
    const templateSaveBtn = document.getElementById('template_save');
    const templateSelect = document.getElementById('template_select');

    if (templateApplyBtn && templateSelect) {
        templateApplyBtn.addEventListener('click', function () {
            const id = templateSelect.value;
            if (!id) return;
            const templates = getTemplates();
            const template = templates.find(t => String(t.id) === String(id));
            applyTemplate(template);
        });
    }

    if (templateSaveBtn) {
        templateSaveBtn.addEventListener('click', function () {
            const type = requestType.value;
            if (!type) {
                alert('Veuillez choisir un type de demande avant d’enregistrer un modèle.');
                return;
            }
            const name = prompt('Nom du modèle ?');
            if (!name) return;

            const templates = getTemplates();
            const newTemplate = {
                id: Date.now(),
                name,
                ...collectTemplateData(),
            };
            templates.push(newTemplate);
            saveTemplates(templates);
            renderTemplateOptions();

            if (window.showToast) {
                showToast('Modèle enregistré.', 'success');
            }
        });
    }

    const collaboratorMatriculeInput = document.getElementById('collaborator_matricule');
    const collaboratorNameInput = document.getElementById('collaborator_name');
    const collaboratorFirstNameInput = document.getElementById('collaborator_first_name');
    const collaboratorAgenceInput = document.getElementById('collaborator_agence');
    const collaboratorHistory = document.getElementById('collaborator-history');
    const historyLines = document.getElementById('collaborator-history-lines');
    const historyPlans = document.getElementById('collaborator-history-plans');
    const historySim = document.getElementById('collaborator-history-sim');
    let historyTimeout = null;

    function applySuggestedSim(simId) {
        if (!simId) return;
        const type = requestType.value;
        const simSelect = type === 'recuperation'
            ? document.getElementById('sim_id_recuperation')
            : (type === 'creation' ? document.getElementById('sim_id_creation') : null);
        if (simSelect && !simSelect.value) {
            simSelect.value = String(simId);
            simSelect.dispatchEvent(new Event('change'));
        }
    }

    function applySuggestedPlan(planIds) {
        if (!planIds || !planIds.length) return;
        const type = requestType.value;
        const planSelect = type === 'creation'
            ? document.getElementById('plan_id_creation')
            : (type === 'ajustement' ? document.getElementById('plan_id_ajustement') : null);
        if (planSelect && !planSelect.value) {
            planSelect.value = String(planIds[0]);
            planSelect.dispatchEvent(new Event('change'));
        }
    }

    function loadCollaboratorHistory(matricule) {
        if (!matricule) return;
        fetch(`{{ route('sim-requests.collaborator-history') }}?matricule=${encodeURIComponent(matricule)}`, {
            headers: { 'Accept': 'application/json' }
        })
            .then(response => response.json())
            .then(data => {
                if (!data.success) return;

                if (data.collaborator) {
                    if (collaboratorNameInput && !collaboratorNameInput.value) {
                        collaboratorNameInput.value = data.collaborator.name || '';
                    }
                    if (collaboratorFirstNameInput && !collaboratorFirstNameInput.value) {
                        collaboratorFirstNameInput.value = data.collaborator.first_name || '';
                    }
                    if (collaboratorAgenceInput && !collaboratorAgenceInput.value) {
                        collaboratorAgenceInput.value = data.collaborator.agence || '';
                    }
                }

                if (collaboratorHistory) {
                    collaboratorHistory.classList.remove('d-none');
                }

                if (historyLines) {
                    const lines = (data.recent_lines || []).length ? data.recent_lines.join(', ') : '-';
                    historyLines.textContent = `Lignes récentes: ${lines}`;
                }
                if (historyPlans) {
                    const plans = data.recent_plans ? Object.values(data.recent_plans) : [];
                    const label = plans.length ? plans.join(', ') : '-';
                    historyPlans.textContent = `Forfaits récents: ${label}`;
                    applySuggestedPlan(Object.keys(data.recent_plans || {}));
                }
                if (historySim) {
                    historySim.textContent = data.suggested_sim_label
                        ? `Suggestion SIM: ${data.suggested_sim_label}`
                        : '';
                }

                applySuggestedSim(data.suggested_sim_id);
                updateSummary();
            })
            .catch(() => {
                // ignore
            });
    }

    if (collaboratorMatriculeInput) {
        collaboratorMatriculeInput.addEventListener('input', function () {
            const value = this.value.trim();
            if (historyTimeout) {
                clearTimeout(historyTimeout);
            }
            historyTimeout = setTimeout(() => {
                loadCollaboratorHistory(value);
            }, 400);
        });
        if (collaboratorMatriculeInput.value) {
            loadCollaboratorHistory(collaboratorMatriculeInput.value.trim());
        }
    }

    const stepButtons = document.querySelectorAll('#wizard-steps .step-item');
    stepButtons.forEach(btn => {
        btn.addEventListener('click', function () {
            const target = document.querySelector(this.dataset.target);
            if (target) {
                target.scrollIntoView({ behavior: 'smooth' });
            }
        });
    });

    function setActiveStep(targetId) {
        stepButtons.forEach(btn => {
            btn.classList.toggle('active', btn.dataset.target === targetId);
        });
    }

    function handleScrollSteps() {
        const anchors = ['#step-1', '#step-2', '#step-3'].map(sel => document.querySelector(sel));
        const visibleAnchor = anchors.reduce((current, anchor) => {
            if (!anchor) return current;
            const rect = anchor.getBoundingClientRect();
            return rect.top <= 140 ? anchor : current;
        }, anchors[0]);

        if (visibleAnchor) {
            setActiveStep('#' + visibleAnchor.id);
        }
    }

    window.addEventListener('scroll', handleScrollSteps);
    handleScrollSteps();

    function validateField(el) {
        if (!el || !el.hasAttribute('required')) return;
        const parentHidden = el.closest('.form-section-hidden');
        if (parentHidden) return;

        if (!el.value || !el.value.trim()) {
            el.classList.add('is-invalid');
        } else {
            el.classList.remove('is-invalid');
        }
    }

    form.querySelectorAll('input[required], textarea[required], select[required]').forEach(el => {
        el.addEventListener('input', () => validateField(el));
        el.addEventListener('change', () => validateField(el));
    });
});
</script>
@endpush
@endsection

