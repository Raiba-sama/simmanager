@extends('layouts.bootstrap')

@section('title', 'Nouvelle demande')
@section('page-title', 'Nouvelle demande')

@push('styles')
<style>
/* ── Form card ─────────────────────────────────────── */
.form-card {
    background: white; border: 1px solid #e2e8f0;
    border-radius: 16px; padding: 28px 24px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.05);
}

/* ── Labels ────────────────────────────────────────── */
.form-label {
    font-size: 13px; font-weight: 600; color: #374151;
    margin-bottom: 6px; display: block;
}

/* ── Filter inputs ─────────────────────────────────── */
.filter-input {
    height: 38px; font-size: 13px; border: 1.5px solid #e2e8f0;
    border-radius: 9px; padding: 0 12px;
    font-family: 'Poppins', sans-serif; color: #1e293b;
    transition: border-color 0.18s, box-shadow 0.18s;
    width: 100%; background: #f8fafc; appearance: none;
    display: block;
}
.filter-input:focus {
    outline: none; border-color: #00574A;
    box-shadow: 0 0 0 3px rgba(0,87,74,0.10); background: white;
}
.filter-input.is-invalid { border-color: #ef4444; }
.filter-input.is-invalid:focus { box-shadow: 0 0 0 3px rgba(239,68,68,0.12); }
select.filter-input {
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%2394a3b8' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='m2 5 6 6 6-6'/%3e%3c/svg%3e");
    background-repeat: no-repeat; background-position: right 10px center; background-size: 14px; padding-right: 32px;
}
textarea.filter-input { height: auto; padding: 10px 12px; resize: vertical; }
input[type="date"].filter-input { padding: 0 12px; }
.invalid-feedback { color: #ef4444; font-size: 12px; margin-top: 4px; display: none; }
.filter-input.is-invalid ~ .invalid-feedback,
.filter-input.is-invalid + .invalid-feedback { display: block; }
.d-block.invalid-feedback { display: block; }
.form-text { font-size: 12px; color: #94a3b8; margin-top: 4px; display: block; }

/* ── Wizard steps ──────────────────────────────────── */
.wizard-steps { display: flex; flex-wrap: wrap; gap: 8px; margin-bottom: 20px; }
.wizard-steps .step-item {
    border: 1.5px solid #e2e8f0; background: #f8fafc;
    padding: 7px 16px; border-radius: 999px; font-size: 12.5px;
    font-weight: 600; color: #64748b; cursor: pointer;
    transition: all 0.16s; font-family: 'Poppins', sans-serif;
}
.wizard-steps .step-item.active {
    background: #00574A; border-color: #00574A; color: white;
}
.wizard-steps .step-item:hover:not(.active) { background: #f1f5f9; color: #1e293b; }

/* ── Info box ──────────────────────────────────────── */
.form-info-box {
    background: #f0fdf4; border: 1px solid #bbf7d0;
    border-radius: 10px; padding: 14px 16px; margin-bottom: 22px;
    font-size: 13px; color: #15803d; line-height: 1.8;
}
.form-info-box strong { color: #166534; }
.form-info-note { font-size: 12px; color: #4ade80; margin-top: 6px; }

/* ── Section title ─────────────────────────────────── */
.form-section-title {
    font-size: 14px; font-weight: 700; color: #1e293b;
    margin: 22px 0 14px; padding-bottom: 10px;
    border-bottom: 2px solid #f1f5f9;
    display: flex; align-items: center; gap: 8px;
}

/* ── Collaborator history ──────────────────────────── */
.collab-history {
    background: #f8fafc; border: 1px solid #e2e8f0;
    border-radius: 10px; padding: 12px 14px; font-size: 13px;
    color: #374151; margin-top: 8px;
}
.collab-history strong { font-size: 12.5px; color: #00574A; }

/* ── Inner boxes ───────────────────────────────────── */
.form-inner-box {
    background: #f8fafc; border: 1px solid #e2e8f0;
    border-radius: 10px; padding: 14px 16px; margin-bottom: 14px;
}
.form-inner-box-title {
    font-size: 13px; font-weight: 700; color: #1e293b;
    margin-bottom: 4px; display: block;
}
.form-inner-box-desc { font-size: 12px; color: #94a3b8; margin-bottom: 10px; display: block; }

/* ── Buttons ───────────────────────────────────────── */
.btn-filter {
    height: 38px; padding: 0 16px; border-radius: 9px; font-size: 13px;
    font-weight: 600; display: inline-flex; align-items: center; gap: 6px;
    cursor: pointer; border: 1.5px solid transparent; transition: all 0.18s;
    font-family: 'Poppins', sans-serif; white-space: nowrap; text-decoration: none;
}
.btn-filter-primary { background: #00574A; color: white; border-color: #00574A; }
.btn-filter-primary:hover { background: #003d34; border-color: #003d34; color: white; }
.btn-filter-reset { background: #f1f5f9; color: #475569; border-color: #e2e8f0; }
.btn-filter-reset:hover { background: #e2e8f0; color: #1e293b; }
.btn-filter-sm { height: 34px; padding: 0 12px; font-size: 12.5px; border-radius: 8px; }

/* ── Template row ──────────────────────────────────── */
.template-row { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; }
.template-row .filter-input { height: 38px; flex: 0 0 200px; max-width: 200px; }

/* ── Summary card ──────────────────────────────────── */
.summary-card {
    background: white; border: 1px solid #e2e8f0;
    border-radius: 16px; padding: 24px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.05);
    position: sticky; top: calc(var(--navbar-h) + 20px);
}
.summary-card-title { font-size: 15px; font-weight: 700; color: #1e293b; margin-bottom: 4px; }
.summary-card-subtitle { font-size: 12px; color: #94a3b8; margin-bottom: 16px; }
.summary-list { list-style: none; padding: 0; margin: 0; }
.summary-list li {
    display: flex; justify-content: space-between; align-items: flex-start;
    padding: 9px 0; border-bottom: 1px solid #f1f5f9; font-size: 13px; gap: 12px;
}
.summary-list li:last-child { border-bottom: none; padding-bottom: 0; }
.summary-key { color: #64748b; font-size: 12px; flex-shrink: 0; }
.summary-val { font-weight: 600; color: #1e293b; font-size: 12.5px; text-align: right; word-break: break-word; }

/* ── Form section hidden ───────────────────────────── */
.form-section-hidden { display: none !important; }
.step-anchor { scroll-margin-top: 110px; }

/* ── Checkbox overrides ────────────────────────────── */
.form-check-label { font-size: 13px; color: #374151; }
.form-check-input:checked { background-color: #00574A; border-color: #00574A; }

/* ── SIM checklist ─────────────────────────────────── */
.sim-checklist {
    border: 1.5px solid #e2e8f0; border-radius: 10px;
    padding: 12px 14px; max-height: 220px; overflow-y: auto;
    background: #f8fafc;
}

/* ── Mobile ────────────────────────────────────────── */
@media (max-width: 767px) {
    .form-card { padding: 18px 16px; }
    .summary-card { position: static; margin-top: 12px; }
    .template-row .filter-input { flex: 1; max-width: 100%; }
    .wizard-steps .step-item { padding: 6px 12px; font-size: 12px; }
}
</style>
@endpush

@section('content')
<div class="row g-4">
    <div class="col-lg-8">
        <div class="form-card">
            <form method="POST" action="{{ route('sim-requests.store') }}" id="requestForm">
            @csrf

            {{-- Wizard steps --}}
            <div class="wizard-steps" id="wizard-steps">
                <button type="button" class="step-item active" data-target="#step-1">1. Type &amp; Collaborateur</button>
                <button type="button" class="step-item" data-target="#step-2">2. Détails</button>
                <button type="button" class="step-item" data-target="#step-3">3. Vérification</button>
            </div>

            {{-- Info box --}}
            <div class="form-info-box">
                <div><strong>1.</strong> Choisissez le type et le collaborateur concerné.</div>
                <div><strong>2.</strong> Renseignez la ligne/SIM/ICCID/forfait selon le type.</div>
                <div><strong>3.</strong> Vérifiez le récapitulatif à droite avant d'envoyer.</div>
                <div class="form-info-note">Le récapitulatif se met à jour automatiquement. Les modèles sont enregistrés sur cet appareil.</div>
            </div>

            <div id="step-1" class="step-anchor"></div>

            {{-- Type de demande --}}
            <div class="mb-3">
                <label for="request_type" class="form-label">Type de demande <span style="color:#ef4444;">*</span></label>
                <select name="request_type" id="request_type" class="filter-input @error('request_type') is-invalid @enderror" required>
                    <option value="">Sélectionner...</option>
                    <option value="recuperation" {{ old('request_type', $prefill['request_type'] ?? '') === 'recuperation' ? 'selected' : '' }}>Récupération</option>
                    <option value="creation"     {{ old('request_type', $prefill['request_type'] ?? '') === 'creation'     ? 'selected' : '' }}>Création</option>
                    <option value="suspension"   {{ old('request_type', $prefill['request_type'] ?? '') === 'suspension'   ? 'selected' : '' }}>Suspension</option>
                    <option value="desactivation"{{ old('request_type', $prefill['request_type'] ?? '') === 'desactivation'? 'selected' : '' }}>Désactivation</option>
                    <option value="ajustement"   {{ old('request_type', $prefill['request_type'] ?? '') === 'ajustement'   ? 'selected' : '' }}>Ajustement</option>
                </select>
                @error('request_type')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Collaborator block --}}
            <div id="collaborator-block" class="form-section-hidden">
                <div class="form-section-title">
                    <i class="bi bi-person" style="color:#00574A;"></i> Informations du collaborateur
                </div>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="collaborator_matricule" class="form-label">Matricule <span style="color:#ef4444;">*</span></label>
                        <input type="text" name="collaborator_matricule" id="collaborator_matricule"
                               class="filter-input @error('collaborator_matricule') is-invalid @enderror"
                               value="{{ old('collaborator_matricule', $prefill['collaborator_matricule'] ?? '') }}"
                               data-required-for="recuperation,suspension,desactivation,ajustement">
                        @error('collaborator_matricule')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="collaborator_name" class="form-label">Nom</label>
                        <input type="text" name="collaborator_name" id="collaborator_name"
                               class="filter-input @error('collaborator_name') is-invalid @enderror"
                               value="{{ old('collaborator_name', $prefill['collaborator_name'] ?? '') }}">
                        @error('collaborator_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="collaborator_first_name" class="form-label">Prénoms</label>
                        <input type="text" name="collaborator_first_name" id="collaborator_first_name"
                               class="filter-input @error('collaborator_first_name') is-invalid @enderror"
                               value="{{ old('collaborator_first_name', $prefill['collaborator_first_name'] ?? '') }}">
                        @error('collaborator_first_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="mb-3">
                    <label for="collaborator_agence" class="form-label">Agence</label>
                    <input type="text" name="collaborator_agence" id="collaborator_agence"
                           class="filter-input @error('collaborator_agence') is-invalid @enderror"
                           value="{{ old('collaborator_agence', $prefill['collaborator_agence'] ?? '') }}">
                    @error('collaborator_agence')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div id="collaborator-history" class="collab-history d-none">
                    <strong>Historique collaborateur</strong>
                    <div id="collaborator-history-lines" class="mt-1" style="color:#64748b; font-size:12px;">Lignes récentes : —</div>
                    <div id="collaborator-history-plans" class="mt-1" style="color:#64748b; font-size:12px;">Forfaits récents : —</div>
                    <div id="collaborator-history-sim" class="mt-1" style="color:#64748b; font-size:12px;"></div>
                </div>
            </div>

            <div id="step-2" class="step-anchor"></div>

            {{-- Création form --}}
            <div id="creation-form" class="form-section-hidden">
                <div class="form-section-title">
                    <i class="bi bi-person-plus" style="color:#00574A;"></i> Informations du bénéficiaire
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="beneficiary_name" class="form-label">Nom <span style="color:#ef4444;">*</span></label>
                        <input type="text" name="beneficiary_name" id="beneficiary_name"
                               class="filter-input @error('beneficiary_name') is-invalid @enderror"
                               value="{{ old('beneficiary_name', $prefill['beneficiary_name'] ?? '') }}"
                               data-required-for="creation">
                        @error('beneficiary_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="beneficiary_first_name" class="form-label">Prénom</label>
                        <input type="text" name="beneficiary_first_name" id="beneficiary_first_name"
                               class="filter-input @error('beneficiary_first_name') is-invalid @enderror"
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
                                class="filter-input @error('beneficiary_fonction') is-invalid @enderror">
                            <option value="">Sélectionner une fonction...</option>
                            @foreach($fonctions as $key => $fonction)
                                <option value="{{ $key }}" {{ old('beneficiary_fonction', $prefill['beneficiary_fonction'] ?? '') == $key ? 'selected' : '' }}>{{ $fonction }}</option>
                            @endforeach
                        </select>
                        @error('beneficiary_fonction')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="beneficiary_matricule" class="form-label">Matricule</label>
                        <input type="text" name="beneficiary_matricule" id="beneficiary_matricule"
                               class="filter-input @error('beneficiary_matricule') is-invalid @enderror"
                               value="{{ old('beneficiary_matricule', $prefill['beneficiary_matricule'] ?? '') }}">
                        @error('beneficiary_matricule')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="mb-3">
                    <label for="plan_id_creation" class="form-label">Forfait <span style="color:#ef4444;">*</span></label>
                    <select id="plan_id_creation" class="filter-input @error('plan_id') is-invalid @enderror"
                            data-required-for="creation"
                            onchange="document.getElementById('plan_id_hidden').value = this.value">
                        <option value="">Sélectionner un forfait...</option>
                        @foreach($plans as $plan)
                            <option value="{{ $plan->id }}" data-credit="{{ $plan->limite_credit }}" data-data="{{ $plan->limite_data }}" {{ old('plan_id', $prefill['plan_id'] ?? '') == $plan->id ? 'selected' : '' }}>
                                {{ $plan->name }} — {{ number_format($plan->limite_credit, 0, ',', ' ') }} ariary / {{ $plan->limite_data }} GB
                            </option>
                        @endforeach
                    </select>
                    @error('plan_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="creation_batch_mode" name="creation_batch_mode" value="1" {{ !empty(old('sim_ids')) ? 'checked' : '' }}>
                        <label class="form-check-label" for="creation_batch_mode">
                            <strong>Demande groupée</strong> — attribuer plusieurs cartes SIM au même bénéficiaire (une demande par SIM)
                        </label>
                    </div>
                </div>
                <div id="creation-single-sim" class="mb-3">
                    <label for="sim_id_creation" class="form-label">SIM (optionnel)</label>
                    <select id="sim_id_creation" class="filter-input @error('sim_id') is-invalid @enderror"
                            onchange="document.getElementById('sim_id_hidden').value = this.value">
                        <option value="">Sélectionner une SIM...</option>
                        @foreach($sims as $sim)
                            <option value="{{ $sim->id }}" {{ old('sim_id', $prefill['sim_id'] ?? '') == $sim->id ? 'selected' : '' }}>
                                {{ $sim->iccid }} — {{ $sim->operator ?? 'N/A' }}
                            </option>
                        @endforeach
                    </select>
                    @error('sim_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div id="creation-batch-sims" class="mb-3 form-section-hidden">
                    <label class="form-label">Cartes SIM à attribuer <span style="color:#ef4444;">*</span></label>
                    <p class="form-text mb-2">Sélectionnez une ou plusieurs cartes. Une demande sera créée pour chaque SIM, avec le même bénéficiaire et forfait.</p>
                    <div class="sim-checklist">
                        @foreach($sims as $sim)
                            <div class="form-check">
                                <input class="form-check-input creation-sim-check" type="checkbox" name="sim_ids[]" value="{{ $sim->id }}" id="sim_batch_{{ $sim->id }}"
                                    {{ in_array($sim->id, old('sim_ids', [])) ? 'checked' : '' }}>
                                <label class="form-check-label" for="sim_batch_{{ $sim->id }}">{{ $sim->iccid }} — {{ $sim->operator ?? 'N/A' }}</label>
                            </div>
                        @endforeach
                    </div>
                    @error('sim_ids')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="requested_iccid_creation" class="form-label">ICCID (si SIM non listée)</label>
                    <input type="text" name="requested_iccid" id="requested_iccid_creation"
                           class="filter-input @error('requested_iccid') is-invalid @enderror"
                           value="{{ old('requested_iccid', $prefill['requested_iccid'] ?? '') }}">
                    @error('requested_iccid')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="motif_creation" class="form-label">Motif <span style="color:#ef4444;">*</span></label>
                    <textarea id="motif_creation" rows="3"
                              class="filter-input @error('motif') is-invalid @enderror"
                              data-required-for="creation"
                              oninput="document.getElementById('motif_hidden').value = this.value">{{ old('motif', $prefill['motif'] ?? '') }}</textarea>
                    @error('motif')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            {{-- Hidden fields --}}
            <input type="hidden" name="phone_number" id="phone_number_hidden" value="{{ old('phone_number', $prefill['phone_number'] ?? '') }}">
            <input type="hidden" name="plan_id" id="plan_id_hidden" value="{{ old('plan_id', $prefill['plan_id'] ?? '') }}">
            <input type="hidden" name="motif" id="motif_hidden" value="{{ old('motif', $prefill['motif'] ?? '') }}">
            <input type="hidden" name="sim_id" id="sim_id_hidden" value="{{ old('sim_id', $prefill['sim_id'] ?? '') }}">

            {{-- Récupération form --}}
            <div id="recuperation-form" class="form-section-hidden">
                <div class="mb-3">
                    <label for="phone_number_recuperation" class="form-label">Numéro de ligne concerné</label>
                    <input type="text" id="phone_number_recuperation"
                           class="filter-input @error('phone_number') is-invalid @enderror"
                           value="{{ old('phone_number', $prefill['phone_number'] ?? ($currentSim->phone_number ?? '')) }}"
                           placeholder="Ex: 0341012345 (laisser vide pour utiliser le numéro de l'utilisateur)"
                           oninput="const hidden = document.getElementById('phone_number_hidden'); if (hidden) hidden.value = this.value.trim();">
                    <span class="form-text">Numéro de téléphone de la ligne à récupérer. Si vide, le numéro de l'utilisateur sera utilisé.</span>
                    @error('phone_number')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="sim_id_recuperation" class="form-label">SIM disponible</label>
                    <select id="sim_id_recuperation" class="filter-input @error('sim_id') is-invalid @enderror"
                            onchange="document.getElementById('sim_id_hidden').value = this.value">
                        <option value="">Sélectionner une SIM libre...</option>
                        @foreach($sims as $sim)
                            <option value="{{ $sim->id }}" {{ old('sim_id', $prefill['sim_id'] ?? '') == $sim->id ? 'selected' : '' }}>
                                {{ $sim->iccid }} — {{ $sim->operator ?? 'N/A' }}
                            </option>
                        @endforeach
                    </select>
                    <span class="form-text">Si vous avez une SIM blanche, sélectionnez-la ici. Sinon, laissez vide et saisissez l'ICCID ci-dessous.</span>
                    @error('sim_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="requested_iccid_recuperation" class="form-label">ICCID demandé (si SIM non listée)</label>
                    <input type="text" name="requested_iccid" id="requested_iccid_recuperation"
                           class="filter-input @error('requested_iccid') is-invalid @enderror"
                           value="{{ old('requested_iccid', $prefill['requested_iccid'] ?? '') }}" placeholder="Ex: 89261012345678901234">
                    @error('requested_iccid')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="motif_recuperation" class="form-label">Motif <span style="color:#ef4444;">*</span></label>
                    <textarea id="motif_recuperation" rows="3"
                              class="filter-input @error('motif') is-invalid @enderror"
                              data-required-for="recuperation"
                              oninput="document.getElementById('motif_hidden').value = this.value">{{ old('motif', $prefill['motif'] ?? '') }}</textarea>
                    @error('motif')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            {{-- Suspension / Désactivation form --}}
            <div id="suspension-desactivation-form" class="form-section-hidden">
                <div class="mb-3">
                    <label for="phone_number" class="form-label">Numéro de ligne concerné <span style="color:#ef4444;">*</span></label>
                    <input type="text" id="phone_number"
                           class="filter-input @error('phone_number') is-invalid @enderror"
                           value="{{ old('phone_number', $prefill['phone_number'] ?? '') }}" placeholder="Ex: 0341012345"
                           data-required-for="suspension,desactivation"
                           oninput="document.getElementById('phone_number_hidden').value = this.value">
                    <span class="form-text">Numéro de la ligne à suspendre/désactiver.</span>
                    @error('phone_number')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="motif_suspension" class="form-label">Motif <span style="color:#ef4444;">*</span></label>
                    <textarea id="motif_suspension" rows="4"
                              class="filter-input @error('motif') is-invalid @enderror"
                              data-required-for="suspension,desactivation"
                              oninput="document.getElementById('motif_hidden').value = this.value">{{ old('motif', $prefill['motif'] ?? '') }}</textarea>
                    @error('motif')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            {{-- Ajustement form --}}
            <div id="ajustement-form" class="form-section-hidden">
                <div class="mb-3">
                    <label for="phone_number_ajustement" class="form-label">Numéro de ligne concerné <span style="color:#ef4444;">*</span></label>
                    <input type="text" id="phone_number_ajustement"
                           class="filter-input @error('phone_number') is-invalid @enderror"
                           value="{{ old('phone_number', $prefill['phone_number'] ?? '') }}" placeholder="Ex: 0341012345"
                           data-required-for="ajustement"
                           oninput="document.getElementById('phone_number_hidden').value = this.value">
                    <span class="form-text">Numéro de la ligne à ajuster.</span>
                    @error('phone_number')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="form-inner-box">
                    <span class="form-inner-box-title">Modification partielle (optionnel)</span>
                    <span class="form-inner-box-desc">Modifier uniquement la limite crédit (LC) et/ou la data sans changer l'autre. Si vous renseignez une valeur ci-dessous, l'autre limite reste celle du forfait actuel.</span>
                    <div class="row g-2">
                        <div class="col-md-6">
                            <label for="limite_credit_override" class="form-label">Limite crédit uniquement (ariary)</label>
                            <input type="number" name="limite_credit_override" id="limite_credit_override" min="0" step="1"
                                   class="filter-input @error('limite_credit_override') is-invalid @enderror"
                                   value="{{ old('limite_credit_override', $prefill['limite_credit_override'] ?? '') }}" placeholder="Ex: 25000">
                            @error('limite_credit_override')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="limite_data_override" class="form-label">Limite data uniquement (Go)</label>
                            <input type="number" name="limite_data_override" id="limite_data_override" min="0" step="0.1"
                                   class="filter-input @error('limite_data_override') is-invalid @enderror"
                                   value="{{ old('limite_data_override', $prefill['limite_data_override'] ?? '') }}" placeholder="Ex: 4.5">
                            @error('limite_data_override')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="mb-3">
                    <label for="plan_id_ajustement" class="form-label">Nouveau forfait complet</label>
                    <select id="plan_id_ajustement" class="filter-input @error('plan_id') is-invalid @enderror"
                            onchange="document.getElementById('plan_id_hidden').value = this.value">
                        <option value="">Sélectionner un forfait (ou utiliser la modification partielle ci-dessus)...</option>
                        @foreach($plans as $plan)
                            <option value="{{ $plan->id }}" data-credit="{{ $plan->limite_credit }}" data-data="{{ $plan->limite_data }}" {{ old('plan_id', $prefill['plan_id'] ?? '') == $plan->id ? 'selected' : '' }}>
                                {{ $plan->name }} — {{ number_format($plan->limite_credit, 0, ',', ' ') }} ariary / {{ $plan->limite_data }} GB
                            </option>
                        @endforeach
                    </select>
                    <span class="form-text">Forfait complet OU au moins une limite (crédit ou data) ci-dessus.</span>
                    @error('plan_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="form-inner-box">
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" name="is_temporary" id="is_temporary_ajustement"
                               value="1" {{ old('is_temporary', $prefill['is_temporary'] ?? false) ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_temporary_ajustement">
                            <strong>Ajustement temporaire</strong>
                        </label>
                        <div class="form-text">Cochez cette case si l'ajustement doit être temporaire et revenir automatiquement aux valeurs précédentes après la date de fin.</div>
                    </div>
                    <div id="temporary_date_group" style="display: none;">
                        <div class="row g-2">
                            <div class="col-md-6">
                                <label for="temporary_start_date_ajustement" class="form-label">Date de début d'ajustement</label>
                                <input type="date" name="temporary_start_date" id="temporary_start_date_ajustement"
                                       class="filter-input @error('temporary_start_date') is-invalid @enderror"
                                       value="{{ old('temporary_start_date', $prefill['temporary_start_date'] ?? date('Y-m-d')) }}"
                                       min="{{ date('Y-m-d') }}">
                                <span class="form-text">À partir de quand l'ajustement s'applique (optionnel, défaut : aujourd'hui).</span>
                                @error('temporary_start_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="temporary_end_date_ajustement" class="form-label">Date de fin <span style="color:#ef4444;">*</span></label>
                                <input type="date" name="temporary_end_date" id="temporary_end_date_ajustement"
                                       class="filter-input @error('temporary_end_date') is-invalid @enderror"
                                       value="{{ old('temporary_end_date', $prefill['temporary_end_date'] ?? '') }}"
                                       min="{{ date('Y-m-d', strtotime('+1 day')) }}">
                                <span class="form-text">L'ajustement sera restauré aux valeurs précédentes après cette date.</span>
                                @error('temporary_end_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div id="step-3" class="step-anchor"></div>

            {{-- Template --}}
            <div class="mb-4">
                <label class="form-label">Modèle de demande (optionnel)</label>
                <div class="template-row">
                    <select id="template_select" class="filter-input">
                        <option value="">Aucun modèle</option>
                    </select>
                    <button type="button" class="btn-filter btn-filter-reset btn-filter-sm" id="template_apply">Appliquer</button>
                    <button type="button" class="btn-filter btn-filter-primary btn-filter-sm" id="template_save">Enregistrer</button>
                </div>
                <span class="form-text">Appliquer = pré-remplir. Enregistrer = sauver le formulaire actuel (sur cet appareil).</span>
            </div>

            {{-- Footer --}}
            <div class="d-flex justify-content-between mt-4 pt-3" style="border-top: 1px solid #f1f5f9;">
                <a href="{{ route('sim-requests.index') }}" class="btn-filter btn-filter-reset">
                    <i class="bi bi-arrow-left"></i> Annuler
                </a>
                <button type="submit" class="btn-filter btn-filter-primary">
                    <i class="bi bi-check-circle"></i> Soumettre la demande
                </button>
            </div>
            </form>
        </div>
    </div>

    {{-- Summary sidebar --}}
    <div class="col-lg-4">
        <div class="summary-card">
            <div class="summary-card-title">Récapitulatif</div>
            <div class="summary-card-subtitle">Mise à jour automatique</div>
            <ul class="summary-list">
                <li><span class="summary-key">Type</span><strong class="summary-val" id="summary-type">-</strong></li>
                <li><span class="summary-key">Collaborateur</span><strong class="summary-val" id="summary-collaborator">-</strong></li>
                <li><span class="summary-key">Ligne concernée</span><strong class="summary-val" id="summary-line">-</strong></li>
                <li><span class="summary-key">SIM</span><strong class="summary-val" id="summary-sim">-</strong></li>
                <li><span class="summary-key">ICCID</span><strong class="summary-val" id="summary-iccid">-</strong></li>
                <li><span class="summary-key">Forfait</span><strong class="summary-val" id="summary-plan">-</strong></li>
                <li><span class="summary-key">Motif</span><strong class="summary-val" id="summary-motif">-</strong></li>
            </ul>
        </div>
    </div>
</div>
@endsection

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

        if (type === 'creation' && typeof toggleCreationBatchMode === 'function') {
            toggleCreationBatchMode();
        }

        const allInputs = form.querySelectorAll('input, textarea, select');
        allInputs.forEach(input => {
            if (input.hasAttribute('disabled') && input.getAttribute('data-required-for')) {
                input.removeAttribute('disabled');
            }

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

    function toggleCreationBatchMode() {
        const batchMode = document.getElementById('creation_batch_mode');
        const singleBlock = document.getElementById('creation-single-sim');
        const batchBlock = document.getElementById('creation-batch-sims');
        const simIdHidden = document.getElementById('sim_id_hidden');
        if (!batchMode || !singleBlock || !batchBlock) return;
        if (batchMode.checked) {
            singleBlock.classList.add('form-section-hidden');
            batchBlock.classList.remove('form-section-hidden');
            if (simIdHidden) simIdHidden.value = '';
            document.querySelectorAll('.creation-sim-check').forEach(cb => cb.removeAttribute('disabled'));
        } else {
            singleBlock.classList.remove('form-section-hidden');
            batchBlock.classList.add('form-section-hidden');
            document.querySelectorAll('.creation-sim-check').forEach(cb => { cb.checked = false; cb.setAttribute('disabled', 'disabled'); });
        }
    }

    requestType.addEventListener('change', toggleForms);
    toggleForms();
    const creationBatchCheck = document.getElementById('creation_batch_mode');
    if (creationBatchCheck) {
        creationBatchCheck.addEventListener('change', function() {
            toggleCreationBatchMode();
            updateSummary();
        });
    }
    toggleCreationBatchMode();

    function initializeHiddenFields() {
        const type = requestType.value;
        const simIdHidden = document.getElementById('sim_id_hidden');

        if (type === 'recuperation') {
            const phoneNumber = document.getElementById('phone_number_recuperation');
            const phoneNumberHidden = document.getElementById('phone_number_hidden');
            const motif = document.getElementById('motif_recuperation');
            const motifHidden = document.getElementById('motif_hidden');
            const simId = document.getElementById('sim_id_recuperation');

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
            if (simIdHidden) {
                simIdHidden.value = '';
            }
        } else {
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

    initializeHiddenFields();
    updateSummary();
    renderTemplateOptions();

    requestType.addEventListener('change', function() {
        setTimeout(initializeHiddenFields, 100);
        setTimeout(updateSummary, 120);
        setTimeout(renderTemplateOptions, 150);
    });

    form.addEventListener('submit', function(e) {
        const type = requestType.value;

        const allInputs = form.querySelectorAll('input, textarea, select');
        allInputs.forEach(input => {
            input.removeAttribute('disabled');
        });

        if (type === 'recuperation') {
            const phoneNumber = document.getElementById('phone_number_recuperation');
            const phoneNumberHidden = document.getElementById('phone_number_hidden');
            const motif = document.getElementById('motif_recuperation');
            const motifHidden = document.getElementById('motif_hidden');
            const simId = document.getElementById('sim_id_recuperation');
            const simIdHidden = document.getElementById('sim_id_hidden');

            recuperationForm.classList.remove('form-section-hidden');

            if (phoneNumber && phoneNumberHidden) {
                phoneNumberHidden.value = phoneNumber.value.trim();
            }
            if (motif && motifHidden) {
                motifHidden.value = motif.value;
            }
            if (simId && simIdHidden) {
                simIdHidden.value = simId.value;
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
        } else if (type === 'creation') {
            const beneficiaryName = document.getElementById('beneficiary_name');
            const planId = document.getElementById('plan_id_creation');
            const planIdHidden = document.getElementById('plan_id_hidden');
            const motif = document.getElementById('motif_creation');
            const motifHidden = document.getElementById('motif_hidden');
            const simId = document.getElementById('sim_id_creation');
            const simIdHidden = document.getElementById('sim_id_hidden');
            const batchMode = document.getElementById('creation_batch_mode');

            creationForm.classList.remove('form-section-hidden');

            if (planId && planIdHidden) {
                planIdHidden.value = planId.value;
            }
            if (motif && motifHidden) {
                motifHidden.value = motif.value;
            }
            if (batchMode && batchMode.checked) {
                const checked = form.querySelectorAll('.creation-sim-check:checked');
                if (simIdHidden) simIdHidden.value = '';
                if (checked.length === 0) {
                    e.preventDefault();
                    alert('En mode demande groupée, sélectionnez au moins une carte SIM.');
                    document.getElementById('creation-batch-sims').scrollIntoView({ behavior: 'smooth' });
                    return false;
                }
            } else {
                if (simId && simIdHidden) {
                    simIdHidden.value = simId.value;
                }
                form.querySelectorAll('.creation-sim-check').forEach(cb => cb.removeAttribute('name'));
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
        } else if (type === 'suspension' || type === 'desactivation') {
            const phoneNumber = document.getElementById('phone_number');
            const phoneNumberHidden = document.getElementById('phone_number_hidden');
            const motif = document.getElementById('motif_suspension');
            const motifHidden = document.getElementById('motif_hidden');

            suspensionForm.classList.remove('form-section-hidden');

            if (phoneNumber && phoneNumberHidden) {
                phoneNumberHidden.value = phoneNumber.value;
            }
            if (motif && motifHidden) {
                motifHidden.value = motif.value;
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
        } else if (type === 'ajustement') {
            const phoneNumber = document.getElementById('phone_number_ajustement');
            const phoneNumberHidden = document.getElementById('phone_number_hidden');
            const planId = document.getElementById('plan_id_ajustement');
            const planIdHidden = document.getElementById('plan_id_hidden');
            const creditOverride = document.getElementById('limite_credit_override');
            const dataOverride = document.getElementById('limite_data_override');

            ajustementForm.classList.remove('form-section-hidden');

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

            const isTemporary = document.getElementById('is_temporary_ajustement');
            const temporaryStartDate = document.getElementById('temporary_start_date_ajustement');
            const temporaryEndDate = document.getElementById('temporary_end_date_ajustement');
            if (isTemporary && isTemporary.checked) {
                if (!temporaryEndDate || !temporaryEndDate.value) {
                    e.preventDefault();
                    if (temporaryEndDate) {
                        temporaryEndDate.focus();
                        temporaryEndDate.classList.add('is-invalid');
                    }
                    alert('La date de fin est requise pour un ajustement temporaire.');
                    return false;
                }
                if (temporaryStartDate && temporaryStartDate.value && temporaryEndDate.value && temporaryStartDate.value >= temporaryEndDate.value) {
                    e.preventDefault();
                    temporaryEndDate.classList.add('is-invalid');
                    alert('La date de fin doit être postérieure à la date de début.');
                    return false;
                }
            }
        }
    });

    function toggleTemporaryDate(checkbox) {
        const dateGroup = document.getElementById('temporary_date_group');
        const endDateInput = document.getElementById('temporary_end_date_ajustement');
        if (!dateGroup) return;
        if (checkbox && checkbox.checked) {
            dateGroup.style.display = 'block';
            if (endDateInput) {
                endDateInput.setAttribute('required', 'required');
            }
        } else {
            dateGroup.style.display = 'none';
            if (endDateInput) {
                endDateInput.removeAttribute('required');
                endDateInput.value = '';
            }
            const startDateInput = document.getElementById('temporary_start_date_ajustement');
            if (startDateInput) {
                startDateInput.value = '';
            }
        }
    }

    window.toggleTemporaryDate = toggleTemporaryDate;

    const isTemporaryCheckbox = document.getElementById('is_temporary_ajustement');
    if (isTemporaryCheckbox) {
        isTemporaryCheckbox.addEventListener('change', function() {
            toggleTemporaryDate(this);
        });
        toggleTemporaryDate(isTemporaryCheckbox);
    }

    const origToggleForms = toggleForms;
    toggleForms = function() {
        origToggleForms();
        const cb = document.getElementById('is_temporary_ajustement');
        if (cb && requestType.value === 'ajustement') {
            toggleTemporaryDate(cb);
        }
    };
    requestType.removeEventListener('change', origToggleForms);
    requestType.addEventListener('change', toggleForms);

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
                alert('Veuillez choisir un type de demande avant d\'enregistrer un modèle.');
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
                    historyLines.textContent = `Lignes récentes : ${lines}`;
                }
                if (historyPlans) {
                    const plans = data.recent_plans ? Object.values(data.recent_plans) : [];
                    const label = plans.length ? plans.join(', ') : '-';
                    historyPlans.textContent = `Forfaits récents : ${label}`;
                    applySuggestedPlan(Object.keys(data.recent_plans || {}));
                }
                if (historySim) {
                    historySim.textContent = data.suggested_sim_label
                        ? `Suggestion SIM : ${data.suggested_sim_label}`
                        : '';
                }

                applySuggestedSim(data.suggested_sim_id);
                updateSummary();
            })
            .catch(() => {});
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
