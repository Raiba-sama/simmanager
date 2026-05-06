@extends('layouts.bootstrap')

@section('title', 'Demande de récupération')
@section('page-title', 'Nouvelle demande de récupération')

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
.invalid-feedback { color: #ef4444; font-size: 12px; margin-top: 4px; display: none; }
.filter-input.is-invalid ~ .invalid-feedback,
.filter-input.is-invalid + .invalid-feedback { display: block; }
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

/* ── Anchor ────────────────────────────────────────── */
.step-anchor { scroll-margin-top: 110px; }

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
            <form method="POST" action="{{ route('sim-requests.store') }}">
            @csrf
            <input type="hidden" name="request_type" value="recuperation">

            {{-- Wizard steps --}}
            <div class="wizard-steps" id="wizard-steps">
                <button type="button" class="step-item active" data-target="#step-1">1. Collaborateur</button>
                <button type="button" class="step-item" data-target="#step-2">2. Détails</button>
                <button type="button" class="step-item" data-target="#step-3">3. Vérification</button>
            </div>

            {{-- Info box --}}
            <div class="form-info-box">
                <div><strong>1.</strong> Choisissez et renseignez le collaborateur.</div>
                <div><strong>2.</strong> Saisissez la ligne, la SIM/ICCID et le motif.</div>
                <div><strong>3.</strong> Vérifiez le récapitulatif à droite avant d'envoyer.</div>
                <div class="form-info-note">Le récapitulatif se met à jour automatiquement. Les modèles sont enregistrés sur cet appareil.</div>
            </div>

            <div id="step-1" class="step-anchor"></div>

            <div class="form-section-title">
                <i class="bi bi-person" style="color:#00574A;"></i> Informations du collaborateur
            </div>

            <div class="row">
                <div class="col-md-4 mb-3">
                    <label for="collaborator_matricule" class="form-label">Matricule <span style="color:#ef4444;">*</span></label>
                    <input type="text" name="collaborator_matricule" id="collaborator_matricule"
                           class="filter-input @error('collaborator_matricule') is-invalid @enderror"
                           value="{{ old('collaborator_matricule', $prefill['collaborator_matricule'] ?? '') }}" placeholder="Ex: M12345" required>
                    @error('collaborator_matricule')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-4 mb-3">
                    <label for="collaborator_name" class="form-label">Nom</label>
                    <input type="text" name="collaborator_name" id="collaborator_name"
                           class="filter-input @error('collaborator_name') is-invalid @enderror"
                           value="{{ old('collaborator_name', $prefill['collaborator_name'] ?? '') }}" placeholder="Nom du collaborateur">
                    @error('collaborator_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-4 mb-3">
                    <label for="collaborator_first_name" class="form-label">Prénoms</label>
                    <input type="text" name="collaborator_first_name" id="collaborator_first_name"
                           class="filter-input @error('collaborator_first_name') is-invalid @enderror"
                           value="{{ old('collaborator_first_name', $prefill['collaborator_first_name'] ?? '') }}" placeholder="Prénoms du collaborateur">
                    @error('collaborator_first_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="mb-3">
                <label for="collaborator_agence" class="form-label">Agence</label>
                <input type="text" name="collaborator_agence" id="collaborator_agence"
                       class="filter-input @error('collaborator_agence') is-invalid @enderror"
                       value="{{ old('collaborator_agence', $prefill['collaborator_agence'] ?? '') }}" placeholder="Agence / Lieu d'affectation">
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

            <div id="step-2" class="step-anchor"></div>

            <div class="form-section-title" style="margin-top: 22px;">
                <i class="bi bi-sim" style="color:#00574A;"></i> Détails de la récupération
            </div>

            <div class="mb-3">
                <label for="phone_number" class="form-label">Numéro de ligne concerné</label>
                <input type="text" name="phone_number" id="phone_number"
                       class="filter-input @error('phone_number') is-invalid @enderror"
                       value="{{ old('phone_number', $prefill['phone_number'] ?? ($currentSim->phone_number ?? '')) }}"
                       placeholder="Ex: 0341012345 (laisser vide pour utiliser votre numéro)">
                <span class="form-text">Numéro de téléphone de la ligne à récupérer. Si vide, votre numéro actuel sera utilisé.</span>
                @error('phone_number')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="sim_id" class="form-label">SIM disponible</label>
                <select name="sim_id" id="sim_id" class="filter-input @error('sim_id') is-invalid @enderror">
                    <option value="">Sélectionner une SIM libre...</option>
                    @foreach($sims as $sim)
                        <option value="{{ $sim->id }}" {{ old('sim_id', $prefill['sim_id'] ?? null) == $sim->id ? 'selected' : '' }}>
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
                <label for="requested_iccid" class="form-label">ICCID (si SIM non listée)</label>
                <input type="text" name="requested_iccid" id="requested_iccid"
                       class="filter-input @error('requested_iccid') is-invalid @enderror"
                       value="{{ old('requested_iccid', $prefill['requested_iccid'] ?? '') }}" placeholder="Ex: 89261012345678901234">
                <span class="form-text">Saisissez l'ICCID de votre nouvelle SIM si elle n'est pas dans la liste ci-dessus.</span>
                @error('requested_iccid')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div id="step-3" class="step-anchor"></div>

            <div class="mb-3">
                <label for="motif" class="form-label">Motif <span style="color:#ef4444;">*</span></label>
                <textarea name="motif" id="motif" rows="4"
                          class="filter-input @error('motif') is-invalid @enderror" required>{{ old('motif', $prefill['motif'] ?? '') }}</textarea>
                <span class="form-text">Indiquez la raison de votre demande (perte, vol, etc.)</span>
                @error('motif')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

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
                <li><span class="summary-key">Type</span><strong class="summary-val" id="summary-type">Récupération</strong></li>
                <li><span class="summary-key">Collaborateur</span><strong class="summary-val" id="summary-collaborator">-</strong></li>
                <li><span class="summary-key">Ligne concernée</span><strong class="summary-val" id="summary-line">-</strong></li>
                <li><span class="summary-key">SIM</span><strong class="summary-val" id="summary-sim">-</strong></li>
                <li><span class="summary-key">ICCID</span><strong class="summary-val" id="summary-iccid">-</strong></li>
                <li><span class="summary-key">Motif</span><strong class="summary-val" id="summary-motif">-</strong></li>
            </ul>
        </div>
    </div>
</div>
@endsection

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
                        historyLines.textContent = `Lignes récentes : ${lines}`;
                    }
                    if (historyPlans) {
                        const plans = data.recent_plans ? Object.values(data.recent_plans) : [];
                        const label = plans.length ? plans.join(', ') : '-';
                        historyPlans.textContent = `Forfaits récents : ${label}`;
                    }
                    if (historySim) {
                        historySim.textContent = data.suggested_sim_label
                            ? `Suggestion SIM : ${data.suggested_sim_label}`
                            : '';
                    }

                    applySuggestedSim(data.suggested_sim_id);
                    updateRecuperationSummary();
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
