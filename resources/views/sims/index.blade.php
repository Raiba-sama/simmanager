@extends('layouts.bootstrap')

@section('title', 'Liste des SIMs')
@section('page-title', 'Liste des SIMs')

@section('content')
<div class="card">
    <div class="card-body">
        <form method="GET" action="{{ route('sims.index') }}" class="mb-3" id="filters-form">
            <div class="row g-3">
                <div class="col-md-4">
                    <input type="text" name="iccid" id="filter-iccid" class="form-control" placeholder="Rechercher par ICCID..." value="{{ request('iccid') }}">
                </div>
                <div class="col-md-3">
                    <select name="status" id="filter-status" class="form-select">
                        <option value="">Tous les statuts</option>
                        <option value="libre" {{ request('status') === 'libre' ? 'selected' : '' }}>Libre</option>
                        <option value="attribue" {{ request('status') === 'attribue' ? 'selected' : '' }}>Attribuée</option>
                        <option value="suspendu" {{ request('status') === 'suspendu' ? 'selected' : '' }}>Suspendue</option>
                        <option value="defectueuse" {{ request('status') === 'defectueuse' ? 'selected' : '' }}>Défectueuse</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="button" class="btn btn-outline-primary w-100" onclick="applyFilters()">
                        <i class="bi bi-search"></i> Rechercher
                    </button>
                </div>
                <div class="col-md-1" id="reset-filter-btn" style="{{ request()->has('iccid') || request()->has('status') ? '' : 'display: none;' }}">
                    <a href="{{ route('sims.index') }}" class="btn btn-outline-secondary w-100" title="Réinitialiser" onclick="event.preventDefault(); resetFilters();">
                        <i class="bi bi-x-circle"></i>
                    </a>
                </div>
                <div class="col-md-3">
                    <div class="btn-group w-100" role="group" id="export-buttons">
                        @php
                            $exportParams = request()->query();
                            $exportParams['format'] = 'excel';
                        @endphp
                        <a href="{{ route('sims.export', $exportParams) }}" 
                           class="btn btn-success" style="border-radius: 8px 0 0 8px;" title="Exporter en Excel" id="export-excel">
                            <i class="bi bi-file-earmark-excel"></i> Excel
                        </a>
                        @php
                            $exportParams['format'] = 'pdf';
                        @endphp
                        <a href="{{ route('sims.export', $exportParams) }}" 
                           class="btn btn-danger" style="border-radius: 0;" title="Exporter en PDF" id="export-pdf">
                            <i class="bi bi-file-earmark-pdf"></i> PDF
                        </a>
                        @if(auth()->user()->isAdmin())
                        <button type="button" class="btn btn-primary" style="border-radius: 0 8px 8px 0;" title="Importer des SIMs" data-bs-toggle="modal" data-bs-target="#importModal">
                            <i class="bi bi-upload"></i> Importer
                        </button>
                        @endif
                    </div>
                </div>
            </div>
        </form>

        <!-- Barre d'actions en masse (affichée seulement si des éléments sont sélectionnés) -->
        <div id="bulk-actions-bar" style="display: none; margin-bottom: 16px; padding: 16px; background: #f0f9ff; border-radius: 8px; border: 1px solid #bae6fd;">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <strong id="selected-count" style="color: #00574A;">0</strong> <span style="color: #64748b;">SIM(s) sélectionnée(s)</span>
                </div>
                <div class="d-flex gap-2">
                    @if(auth()->user()->isAdmin())
                    <button type="button" class="btn btn-sm btn-primary" onclick="bulkAction('assign')" style="border-radius: 6px;">
                        <i class="bi bi-person-plus"></i> Attribuer
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="bulkAction('unassign')" style="border-radius: 6px;">
                        <i class="bi bi-person-dash"></i> Libérer
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="bulkAction('export')" style="border-radius: 6px;">
                        <i class="bi bi-download"></i> Exporter
                    </button>
                    @endif
                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="clearSelection()" style="border-radius: 6px;">
                        <i class="bi bi-x"></i> Annuler
                    </button>
                </div>
            </div>
        </div>

        <div class="table-responsive" id="table-container">
            <table class="table table-hover mb-0" style="margin: 0;">
                <thead style="background: #f9fafb;">
                    <tr>
                        <th style="padding: 16px; font-weight: 600; font-size: 13px; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px; width: 50px;">
                            <input type="checkbox" id="select-all" onchange="toggleSelectAll(this)" style="cursor: pointer;">
                        </th>
                        <th style="padding: 16px; font-weight: 600; font-size: 13px; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px;">ICCID</th>
                        <th style="padding: 16px; font-weight: 600; font-size: 13px; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px;">Téléphone</th>
                        <th style="padding: 16px; font-weight: 600; font-size: 13px; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px;">Statut</th>
                        <th style="padding: 16px; font-weight: 600; font-size: 13px; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px;">Opérateur</th>
                        <th style="padding: 16px; font-weight: 600; font-size: 13px; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px;">Assignée à</th>
                        <th style="padding: 16px; font-weight: 600; font-size: 13px; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px;">Date attribution</th>
                        <th style="padding: 16px; font-weight: 600; font-size: 13px; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px;">Actions</th>
                    </tr>
                </thead>
                <tbody id="table-body">
                    @forelse($sims as $sim)
                        <tr style="border-bottom: 1px solid #e5e7eb; transition: background 0.2s;" onmouseover="this.style.background='#f9fafb'" onmouseout="this.style.background='white'">
                            <td style="padding: 16px;">
                                <input type="checkbox" class="sim-checkbox" value="{{ $sim->id }}" onchange="updateBulkActions()" style="cursor: pointer;">
                            </td>
                            <td style="padding: 16px; font-weight: 600; color: #1a1a1a;"><strong>{{ $sim->iccid }}</strong></td>
                            <td style="padding: 16px; color: #4b5563;">{{ $sim->phone_number ?? '-' }}</td>
                            <td style="padding: 16px;">
                                @php
                                    $statusColors = [
                                        'libre' => ['bg' => '#10b981', 'text' => 'white'],
                                        'attribue' => ['bg' => '#3b82f6', 'text' => 'white'],
                                        'suspendu' => ['bg' => '#ef4444', 'text' => 'white'],
                                        'defectueuse' => ['bg' => '#f59e0b', 'text' => 'white'],
                                    ];
                                    $statusLabels = [
                                        'libre' => 'Libre',
                                        'attribue' => 'Attribuée',
                                        'suspendu' => 'Suspendue',
                                        'defectueuse' => 'Défectueuse',
                                    ];
                                    $statusColor = $statusColors[$sim->status] ?? ['bg' => '#6b7280', 'text' => 'white'];
                                    $statusLabel = $statusLabels[$sim->status] ?? ucfirst($sim->status);
                                @endphp
                                <span class="badge" style="background: {{ $statusColor['bg'] }}; color: {{ $statusColor['text'] }}; padding: 6px 12px; border-radius: 6px; font-size: 12px; font-weight: 500;">
                                    {{ $statusLabel }}
                                </span>
                            </td>
                            <td style="padding: 16px; color: #4b5563;">{{ $sim->operator ?? '-' }}</td>
                            <td style="padding: 16px; color: #4b5563;">
                                @if($sim->assignedUser)
                                    {{ $sim->assignedUser->full_name }}
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td style="padding: 16px; color: #6b7280; font-size: 14px;">{{ $sim->assigned_at ? $sim->assigned_at->format('d/m/Y') : '-' }}</td>
                            <td style="padding: 16px;">
                                <a href="{{ route('sims.show', $sim) }}" class="btn btn-sm" style="background: transparent; border: 1px solid #e5e7eb; color: #3b82f6; padding: 6px 12px; border-radius: 6px;" data-bs-toggle="tooltip" title="Voir les détails">
                                    <i class="bi bi-eye"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center" style="padding: 40px; color: #9ca3af;">Aucune SIM trouvée</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div id="pagination-container" class="mt-4 d-flex justify-content-center">
            @if($sims->hasPages())
                {{ $sims->appends(request()->query())->links('pagination::bootstrap-5') }}
            @endif
        </div>
    </div>
</div>

<!-- Modal d'import -->
@if(auth()->user()->isAdmin())
<div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header" style="background: #00574A; color: white;">
                <h5 class="modal-title" id="importModalLabel">
                    <i class="bi bi-upload"></i> Importer des SIMs
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="{{ route('sims.import-csv') }}" enctype="multipart/form-data" id="importForm">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label" style="font-weight: 500;">Méthode d'import</label>
                        <div class="btn-group w-100" role="group">
                            <input type="radio" class="btn-check" name="import_method" id="method_file" value="file" checked>
                            <label class="btn btn-outline-primary" for="method_file">
                                <i class="bi bi-file-earmark"></i> Fichier CSV
                            </label>
                            
                            <input type="radio" class="btn-check" name="import_method" id="method_text" value="text">
                            <label class="btn btn-outline-primary" for="method_text">
                                <i class="bi bi-textarea-t"></i> Liste d'ICCID
                            </label>
                        </div>
                    </div>

                    <!-- Option Fichier CSV -->
                    <div id="file-option" class="import-option">
                        <div class="mb-3">
                            <label for="csv_file" class="form-label" style="font-weight: 500;">Fichier CSV</label>
                            <input type="file" class="form-control" id="csv_file" name="csv_file" accept=".csv,.txt">
                            <small class="form-text text-muted">
                                Format CSV: ICCID,Numéro,Opérateur,Plan,Coût (optionnel). La première ligne peut être un en-tête.
                            </small>
                        </div>
                    </div>

                    <!-- Option Liste d'ICCID -->
                    <div id="text-option" class="import-option" style="display: none;">
                        <div class="mb-3">
                            <label for="iccid_list" class="form-label" style="font-weight: 500;">Liste d'ICCID</label>
                            <textarea class="form-control" id="iccid_list" name="iccid_list" rows="10" placeholder="Entrez les ICCID, un par ligne ou séparés par virgule/semicolon&#10;&#10;Exemple:&#10;89012345678901234567&#10;89012345678901234568&#10;89012345678901234569"></textarea>
                            <small class="form-text text-muted">
                                Entrez les ICCID, un par ligne ou séparés par virgule (,) ou point-virgule (;). Toutes les SIMs seront créées avec le statut "libre" par défaut.
                            </small>
                        </div>
                    </div>

                    <div class="alert alert-info" style="background: #f0f9ff; border: 1px solid #bae6fd; color: #0369a1;">
                        <i class="bi bi-info-circle"></i> <strong>Note:</strong> Les SIMs importées auront automatiquement le statut "libre". Les ICCID déjà existants seront ignorés.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary" style="background: #00574A;">
                        <i class="bi bi-upload"></i> Importer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

@push('scripts')
<script>
    // Gestion de l'affichage des options d'import
    document.addEventListener('DOMContentLoaded', function() {
        const fileOption = document.getElementById('file-option');
        const textOption = document.getElementById('text-option');
        const methodFile = document.getElementById('method_file');
        const methodText = document.getElementById('method_text');
        const csvFileInput = document.getElementById('csv_file');
        const iccidListInput = document.getElementById('iccid_list');

        if (fileOption && textOption && methodFile && methodText) {
            function toggleImportOptions() {
                if (methodFile.checked) {
                    fileOption.style.display = 'block';
                    textOption.style.display = 'none';
                    csvFileInput.required = true;
                    iccidListInput.required = false;
                    iccidListInput.value = '';
                } else {
                    fileOption.style.display = 'none';
                    textOption.style.display = 'block';
                    csvFileInput.required = false;
                    iccidListInput.required = true;
                    csvFileInput.value = '';
                }
            }

            methodFile.addEventListener('change', toggleImportOptions);
            methodText.addEventListener('change', toggleImportOptions);

            // Validation du formulaire
            const importForm = document.getElementById('importForm');
            if (importForm) {
                importForm.addEventListener('submit', function(e) {
                    if (methodFile.checked && !csvFileInput.files.length) {
                        e.preventDefault();
                        alert('Veuillez sélectionner un fichier CSV.');
                        return false;
                    }
                    if (methodText.checked && !iccidListInput.value.trim()) {
                        e.preventDefault();
                        alert('Veuillez entrer au moins un ICCID.');
                        return false;
                    }
                });
            }
        }
    });
</script>
<script>
    let filterTimeout;
    const baseUrl = '{{ route('sims.index') }}';
    
    // Auto-filter on change
    document.getElementById('filter-iccid').addEventListener('input', function() {
        clearTimeout(filterTimeout);
        filterTimeout = setTimeout(() => {
            applyFilters();
        }, 500);
    });
    
    document.getElementById('filter-status').addEventListener('change', function() {
        clearTimeout(filterTimeout);
        filterTimeout = setTimeout(() => {
            applyFilters();
        }, 300);
    });
    
    function applyFilters() {
        const iccid = document.getElementById('filter-iccid').value.trim();
        const status = document.getElementById('filter-status').value;
        
        const params = new URLSearchParams();
        if (iccid) params.append('iccid', iccid);
        if (status) params.append('status', status);
        
        const url = baseUrl + (params.toString() ? '?' + params.toString() : '');
        
        // Show loading state
        const tableBody = document.getElementById('table-body');
        const paginationContainer = document.getElementById('pagination-container');
        tableBody.innerHTML = '<tr><td colspan="8" class="text-center" style="padding: 40px;"><i class="bi bi-arrow-repeat spin"></i> Chargement...</td></tr>';
        
        // Update URL without reload
        window.history.pushState({}, '', url);
        
        // Fetch new content
        fetch(url, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'text/html',
            }
        })
        .then(response => response.text())
        .then(html => {
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            
            // Update table body
            const newTableBody = doc.getElementById('table-body');
            if (newTableBody) {
                tableBody.innerHTML = newTableBody.innerHTML;
                // Réinitialiser les checkboxes après le chargement dynamique
                clearSelection();
                // Réattacher les event listeners aux nouvelles checkboxes
                attachCheckboxListeners();
            }
            
            // Update pagination
            const newPagination = doc.getElementById('pagination-container');
            if (newPagination) {
                paginationContainer.innerHTML = newPagination.innerHTML;
            }
            
            // Update export buttons
            updateExportButtons(iccid, status);
            
            // Update reset button visibility
            const resetBtn = document.getElementById('reset-filter-btn');
            if (iccid || status) {
                resetBtn.style.display = '';
            } else {
                resetBtn.style.display = 'none';
            }
        })
        .catch(error => {
            console.error('Filter error:', error);
            tableBody.innerHTML = '<tr><td colspan="8" class="text-center" style="padding: 40px; color: #ef4444;">Erreur lors du chargement</td></tr>';
        });
    }
    
    function resetFilters() {
        document.getElementById('filter-iccid').value = '';
        document.getElementById('filter-status').value = '';
        applyFilters();
    }
    
    function updateExportButtons(iccid, status) {
        const params = new URLSearchParams();
        if (iccid) params.append('iccid', iccid);
        if (status) params.append('status', status);
        
        const excelBtn = document.getElementById('export-excel');
        const pdfBtn = document.getElementById('export-pdf');
        
        const queryString = params.toString();
        const baseExportUrl = '{{ route('sims.export') }}';
        
        excelBtn.href = baseExportUrl + (queryString ? '?' + queryString + '&format=excel' : '?format=excel');
        pdfBtn.href = baseExportUrl + (queryString ? '?' + queryString + '&format=pdf' : '?format=pdf');
    }
    
    // Handle pagination clicks
    document.addEventListener('click', function(e) {
        if (e.target.closest('.pagination a')) {
            e.preventDefault();
            const url = e.target.closest('.pagination a').href;
            
            const tableBody = document.getElementById('table-body');
            tableBody.innerHTML = '<tr><td colspan="8" class="text-center" style="padding: 40px;"><i class="bi bi-arrow-repeat spin"></i> Chargement...</td></tr>';
            
            fetch(url, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'text/html',
                }
            })
            .then(response => response.text())
            .then(html => {
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                
                const newTableBody = doc.getElementById('table-body');
                if (newTableBody) {
                    tableBody.innerHTML = newTableBody.innerHTML;
                    // Réinitialiser les sélections après pagination
                    clearSelection();
                    attachCheckboxListeners();
                }
                
                const newPagination = doc.getElementById('pagination-container');
                if (newPagination) {
                    document.getElementById('pagination-container').innerHTML = newPagination.innerHTML;
                }
                
                // Scroll to top
                window.scrollTo({ top: 0, behavior: 'smooth' });
            })
            .catch(error => {
                console.error('Pagination error:', error);
                window.location.href = url;
            });
        }
    });
    
    // Actions en masse pour SIMs
    function attachCheckboxListeners() {
        // Réattacher les event listeners aux checkboxes
        document.querySelectorAll('.sim-checkbox').forEach(cb => {
            cb.removeEventListener('change', updateBulkActions);
            cb.addEventListener('change', updateBulkActions);
        });
        
        // Réattacher le listener pour "select all"
        const selectAll = document.getElementById('select-all');
        if (selectAll) {
            selectAll.removeEventListener('change', function() { toggleSelectAll(this); });
            selectAll.addEventListener('change', function() { toggleSelectAll(this); });
        }
    }
    
    function toggleSelectAll(checkbox) {
        const checkboxes = document.querySelectorAll('.sim-checkbox');
        checkboxes.forEach(cb => {
            cb.checked = checkbox.checked;
        });
        updateBulkActions();
    }
    
    function updateBulkActions() {
        const selected = document.querySelectorAll('.sim-checkbox:checked');
        const count = selected.length;
        const bulkBar = document.getElementById('bulk-actions-bar');
        const selectAll = document.getElementById('select-all');
        
        if (count > 0) {
            if (bulkBar) {
                bulkBar.style.display = 'block';
            }
            const selectedCountEl = document.getElementById('selected-count');
            if (selectedCountEl) {
                selectedCountEl.textContent = count;
            }
        } else {
            if (bulkBar) {
                bulkBar.style.display = 'none';
            }
        }
        
        // Mettre à jour la checkbox "select all"
        const allCheckboxes = document.querySelectorAll('.sim-checkbox');
        if (selectAll) {
            selectAll.checked = count === allCheckboxes.length && count > 0;
            selectAll.indeterminate = count > 0 && count < allCheckboxes.length;
        }
    }
    
    function clearSelection() {
        document.querySelectorAll('.sim-checkbox').forEach(cb => cb.checked = false);
        const selectAll = document.getElementById('select-all');
        if (selectAll) {
            selectAll.checked = false;
            selectAll.indeterminate = false;
        }
        updateBulkActions();
    }
    
    // Initialiser les listeners au chargement de la page
    document.addEventListener('DOMContentLoaded', function() {
        attachCheckboxListeners();
    });
    
    function getSelectedIds() {
        const selected = document.querySelectorAll('.sim-checkbox:checked');
        return Array.from(selected).map(cb => cb.value);
    }
    
    function bulkAction(action) {
        const ids = getSelectedIds();
        if (ids.length === 0) {
            alert('Veuillez sélectionner au moins une SIM.');
            return;
        }
        
        if (action === 'assign') {
            // TODO: Ouvrir un modal pour sélectionner l'utilisateur
            const userId = prompt('ID de l\'utilisateur à qui attribuer les SIMs :');
            if (!userId) return;
            
            fetch('{{ route('sims.bulk-assign') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ ids: ids, user_id: userId })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    clearSelection();
                    window.location.reload();
                } else {
                    alert(data.message || 'Une erreur est survenue.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Une erreur est survenue lors de l\'action en masse.');
            });
        } else if (action === 'unassign') {
            if (window.showConfirmModal) {
                return window.showConfirmModal(
                    `Libérer ${ids.length} SIM(s) ?`,
                    () => executeUnassign(ids),
                    { confirmText: 'Libérer', confirmVariant: 'danger', title: 'Confirmer la libération' }
                );
            }
            
            executeUnassign(ids);
        } else if (action === 'export') {
            const params = new URLSearchParams();
            ids.forEach(id => params.append('ids[]', id));
            window.location.href = '{{ route('sims.export') }}?format=excel&' + params.toString();
        }
    }

    function executeUnassign(ids) {
        fetch('{{ route('sims.bulk-unassign') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ ids: ids })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    clearSelection();
                    window.location.reload();
                } else {
                    alert(data.message || 'Une erreur est survenue.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Une erreur est survenue lors de l\'action en masse.');
            });
    }
</script>
<style>
    @keyframes spin {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }
    .spin {
        animation: spin 1s linear infinite;
        display: inline-block;
    }
</style>
@endpush
@endsection

