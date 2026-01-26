@extends('layouts.bootstrap')

@section('title', 'Demande de récupération')
@section('page-title', 'Nouvelle demande de récupération')

@section('content')
<div class="row g-4">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-body">
                <form method="POST" action="{{ route('sim-requests.store') }}">
            @csrf
            <input type="hidden" name="request_type" value="recuperation">

            <div class="wizard-steps mb-3" id="wizard-steps">
                <button type="button" class="step-item active" data-target="#step-1">1. Collaborateur</button>
                <button type="button" class="step-item" data-target="#step-2">2. Détails</button>
                <button type="button" class="step-item" data-target="#step-3">3. Vérification</button>
            </div>

            <div id="step-1" class="step-anchor"></div>

            <h5 class="mb-3">Informations du collaborateur</h5>
            <div class="mb-3">
                <label for="template_select" class="form-label">Modèle de demande</label>
                <div class="d-flex gap-2">
                    <select id="template_select" class="form-select">
                        <option value="">Aucun modèle</option>
                    </select>
                    <button type="button" class="btn btn-outline-secondary" id="template_apply">Appliquer</button>
                    <button type="button" class="btn btn-outline-primary" id="template_save">Enregistrer</button>
                </div>
                <small class="form-text text-muted">Pré-remplissez le formulaire avec un modèle enregistré.</small>
            </div>
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label for="collaborator_matricule" class="form-label">Matricule <span class="text-danger">*</span></label>
                    <input type="text" name="collaborator_matricule" id="collaborator_matricule"
                           class="form-control @error('collaborator_matricule') is-invalid @enderror"
                           value="{{ old('collaborator_matricule', $prefill['collaborator_matricule'] ?? '') }}" placeholder="Ex: M12345" required>
                    @error('collaborator_matricule')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-4 mb-3">
                    <label for="collaborator_name" class="form-label">Nom</label>
                    <input type="text" name="collaborator_name" id="collaborator_name"
                           class="form-control @error('collaborator_name') is-invalid @enderror"
                           value="{{ old('collaborator_name', $prefill['collaborator_name'] ?? '') }}" placeholder="Nom du collaborateur">
                    @error('collaborator_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-4 mb-3">
                    <label for="collaborator_first_name" class="form-label">Prénoms</label>
                    <input type="text" name="collaborator_first_name" id="collaborator_first_name"
                           class="form-control @error('collaborator_first_name') is-invalid @enderror"
                           value="{{ old('collaborator_first_name', $prefill['collaborator_first_name'] ?? '') }}" placeholder="Prénoms du collaborateur">
                    @error('collaborator_first_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="mb-3">
                <label for="collaborator_agence" class="form-label">Agence</label>
                <input type="text" name="collaborator_agence" id="collaborator_agence"
                       class="form-control @error('collaborator_agence') is-invalid @enderror"
                       value="{{ old('collaborator_agence', $prefill['collaborator_agence'] ?? '') }}" placeholder="Agence / Lieu d'affectation">
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

            <div id="step-2" class="step-anchor"></div>

            <div class="mb-3">
                <label for="phone_number" class="form-label">Numéro de ligne concerné</label>
                <input type="text" name="phone_number" id="phone_number" 
                       class="form-control @error('phone_number') is-invalid @enderror" 
                       value="{{ old('phone_number', $prefill['phone_number'] ?? ($currentSim->phone_number ?? '')) }}" 
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
                        <option value="{{ $sim->id }}" {{ old('sim_id', $prefill['sim_id'] ?? null) == $sim->id ? 'selected' : '' }}>
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
                       value="{{ old('requested_iccid', $prefill['requested_iccid'] ?? '') }}" placeholder="Ex: 89261012345678901234">
                <small class="form-text text-muted">Saisissez l'ICCID de votre nouvelle SIM si elle n'est pas dans la liste ci-dessus.</small>
                @error('requested_iccid')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div id="step-3" class="step-anchor"></div>

            <div class="mb-3">
                <label for="motif" class="form-label">Motif <span class="text-danger">*</span></label>
                <textarea name="motif" id="motif" rows="4" 
                          class="form-control @error('motif') is-invalid @enderror" required>{{ old('motif', $prefill['motif'] ?? '') }}</textarea>
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
    </div>
    <div class="col-lg-4">
        <div class="card sticky-summary">
            <div class="card-header bg-light">
                <strong>Récapitulatif</strong>
            </div>
            <div class="card-body">
                <ul class="list-unstyled mb-0 summary-list">
                    <li class="d-flex justify-content-between">
                        <span>Type</span>
                        <strong id="summary-type">Récupération</strong>
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
                        <span>Motif</span>
                        <strong id="summary-motif">-</strong>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
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
    function updateRecuperationSummary() {
        const matricule = document.getElementById('collaborator_matricule')?.value?.trim() || '';
        const name = document.getElementById('collaborator_name')?.value?.trim() || '';
        const firstName = document.getElementById('collaborator_first_name')?.value?.trim() || '';
        const phone = document.getElementById('phone_number')?.value?.trim() || '';
        const simSelect = document.getElementById('sim_id');
        const simLabel = simSelect && simSelect.value
            ? simSelect.options[simSelect.selectedIndex].text
            : '';
        const iccid = document.getElementById('requested_iccid')?.value?.trim() || '';
        const motif = document.getElementById('motif')?.value?.trim() || '';

        const collaborator = [name, firstName].filter(Boolean).join(' ') || '';
        const collaboratorDisplay = collaborator || matricule || '-';

        document.getElementById('summary-collaborator').textContent = collaboratorDisplay;
        document.getElementById('summary-line').textContent = phone || '-';
        document.getElementById('summary-sim').textContent = simLabel || '-';
        document.getElementById('summary-iccid').textContent = iccid || '-';
        document.getElementById('summary-motif').textContent = motif ? motif.substring(0, 60) : '-';
    }

    document.addEventListener('DOMContentLoaded', function () {
        const templateStorageKey = 'sim-requests-templates';
        const templateSelect = document.getElementById('template_select');
        const templateApplyBtn = document.getElementById('template_apply');
        const templateSaveBtn = document.getElementById('template_save');
        const collaboratorMatriculeInput = document.getElementById('collaborator_matricule');
        const collaboratorNameInput = document.getElementById('collaborator_name');
        const collaboratorFirstNameInput = document.getElementById('collaborator_first_name');
        const collaboratorAgenceInput = document.getElementById('collaborator_agence');
        const collaboratorHistory = document.getElementById('collaborator-history');
        const historyLines = document.getElementById('collaborator-history-lines');
        const historyPlans = document.getElementById('collaborator-history-plans');
        const historySim = document.getElementById('collaborator-history-sim');
        let historyTimeout = null;

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
            if (!templateSelect) return;
            const templates = getTemplates().filter(t => t.request_type === 'recuperation');
            templateSelect.innerHTML = '<option value="">Aucun modèle</option>';
            templates.forEach(t => {
                const option = document.createElement('option');
                option.value = t.id;
                option.textContent = t.name;
                templateSelect.appendChild(option);
            });
        }

        function collectTemplateData() {
            return {
                request_type: 'recuperation',
                fields: {
                    collaborator_matricule: document.getElementById('collaborator_matricule')?.value || '',
                    collaborator_name: document.getElementById('collaborator_name')?.value || '',
                    collaborator_first_name: document.getElementById('collaborator_first_name')?.value || '',
                    collaborator_agence: document.getElementById('collaborator_agence')?.value || '',
                    phone_number: document.getElementById('phone_number')?.value || '',
                    sim_id: document.getElementById('sim_id')?.value || '',
                    requested_iccid: document.getElementById('requested_iccid')?.value || '',
                    motif: document.getElementById('motif')?.value || '',
                }
            };
        }

        function applyTemplate(template) {
            if (!template || !template.fields) return;
            Object.keys(template.fields).forEach(key => {
                const el = document.getElementById(key);
                if (el) {
                    el.value = template.fields[key];
                }
            });
            updateRecuperationSummary();
        }

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
                const name = prompt('Nom du modèle ?');
                if (!name) return;
                const templates = getTemplates();
                templates.push({
                    id: Date.now(),
                    name,
                    ...collectTemplateData(),
                });
                saveTemplates(templates);
                renderTemplateOptions();
                if (window.showToast) {
                    showToast('Modèle enregistré.', 'success');
                }
            });
        }

        renderTemplateOptions();

        function applySuggestedSim(simId) {
            if (!simId) return;
            const simSelect = document.getElementById('sim_id');
            if (simSelect && !simSelect.value) {
                simSelect.value = String(simId);
                simSelect.dispatchEvent(new Event('change'));
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
                    }
                    if (historySim) {
                        historySim.textContent = data.suggested_sim_label
                            ? `Suggestion SIM: ${data.suggested_sim_label}`
                            : '';
                    }

                    applySuggestedSim(data.suggested_sim_id);
                    updateRecuperationSummary();
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

        document.querySelectorAll('input[required], textarea[required], select[required]').forEach(el => {
            el.addEventListener('input', () => validateField(el));
            el.addEventListener('change', () => validateField(el));
        });

        ['collaborator_matricule','collaborator_name','collaborator_first_name','phone_number','requested_iccid','motif']
            .forEach(id => {
                const el = document.getElementById(id);
                if (el) {
                    el.addEventListener('input', updateRecuperationSummary);
                }
            });
        const simSelect = document.getElementById('sim_id');
        if (simSelect) {
            simSelect.addEventListener('change', updateRecuperationSummary);
        }
        updateRecuperationSummary();
    });
</script>
@endpush

