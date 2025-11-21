@extends('layouts.bootstrap')

@section('title', 'Check Mail - Mails Envoyés')
@section('page-title', 'Check Mail - Mails Envoyés')

@section('content')
<div class="row mb-4">
    <!-- Statistiques -->
    <div class="col-md-4 mb-3">
        <div class="card" style="border: none; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.08);">
            <div class="card-body" style="padding: 20px;">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="mb-1" style="font-size: 12px; color: #64748b; text-transform: uppercase; font-weight: 600;">Total Mails</h6>
                        <h3 class="mb-0" style="font-weight: 700; color: #00574A;">{{ $stats['total'] }}</h3>
                    </div>
                    <div style="font-size: 2.5rem; color: #00574A; opacity: 0.2;">
                        <i class="bi bi-envelope"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4 mb-3">
        <div class="card" style="border: none; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.08);">
            <div class="card-body" style="padding: 20px;">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="mb-1" style="font-size: 12px; color: #64748b; text-transform: uppercase; font-weight: 600;">Ce mois</h6>
                        <h3 class="mb-0" style="font-weight: 700; color: #3b82f6;">{{ $stats['this_month'] }}</h3>
                    </div>
                    <div style="font-size: 2.5rem; color: #3b82f6; opacity: 0.2;">
                        <i class="bi bi-calendar-month"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4 mb-3">
        <div class="card" style="border: none; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.08);">
            <div class="card-body" style="padding: 20px;">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="mb-1" style="font-size: 12px; color: #64748b; text-transform: uppercase; font-weight: 600;">Cette semaine</h6>
                        <h3 class="mb-0" style="font-weight: 700; color: #10b981;">{{ $stats['this_week'] }}</h3>
                    </div>
                    <div style="font-size: 2.5rem; color: #10b981; opacity: 0.2;">
                        <i class="bi bi-calendar-week"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card" style="border: none; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.08);">
    <div class="card-body" style="padding: 20px;">
        <!-- Filtres -->
        <form method="GET" action="{{ route('mail-sent.index') }}" class="mb-4" id="filters-form">
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label" style="font-size: 12px; color: #64748b; margin-bottom: 4px; font-weight: 500;">Type de demande</label>
                    <select name="request_type" class="form-select" style="border-radius: 8px; border: 1px solid #e2e8f0;">
                        <option value="">Tous les types</option>
                        <option value="recuperation" {{ request('request_type') === 'recuperation' ? 'selected' : '' }}>Récupération</option>
                        <option value="creation" {{ request('request_type') === 'creation' ? 'selected' : '' }}>Création</option>
                        <option value="suspension" {{ request('request_type') === 'suspension' ? 'selected' : '' }}>Suspension</option>
                        <option value="desactivation" {{ request('request_type') === 'desactivation' ? 'selected' : '' }}>Désactivation</option>
                        <option value="ajustement" {{ request('request_type') === 'ajustement' ? 'selected' : '' }}>Ajustement</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label" style="font-size: 12px; color: #64748b; margin-bottom: 4px; font-weight: 500;">Date début</label>
                    <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}" style="border-radius: 8px; border: 1px solid #e2e8f0;">
                </div>
                <div class="col-md-2">
                    <label class="form-label" style="font-size: 12px; color: #64748b; margin-bottom: 4px; font-weight: 500;">Date fin</label>
                    <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}" style="border-radius: 8px; border: 1px solid #e2e8f0;">
                </div>
                <div class="col-md-3">
                    <label class="form-label" style="font-size: 12px; color: #64748b; margin-bottom: 4px; font-weight: 500;">Recherche</label>
                    <input type="text" name="search" class="form-control" value="{{ request('search') }}" placeholder="N° demande, ICCID, sujet..." style="border-radius: 8px; border: 1px solid #e2e8f0;">
                </div>
                <div class="col-md-2 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-primary w-100" style="border-radius: 8px; background: #00574A;">
                        <i class="bi bi-funnel"></i> Filtrer
                    </button>
                    @if(request()->anyFilled(['request_type', 'date_from', 'date_to', 'search']))
                    <a href="{{ route('mail-sent.index') }}" class="btn btn-outline-secondary" style="border-radius: 8px;" title="Réinitialiser">
                        <i class="bi bi-x-circle"></i>
                    </a>
                    @endif
                </div>
            </div>
        </form>

        <!-- Tableau des mails -->
        <div class="table-responsive">
            <table class="table table-hover mb-0" style="margin: 0;">
                <thead style="background: #f9fafb;">
                    <tr>
                        <th style="padding: 16px; font-weight: 600; font-size: 13px; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px;">N° Demande</th>
                        <th style="padding: 16px; font-weight: 600; font-size: 13px; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px;">Type</th>
                        <th style="padding: 16px; font-weight: 600; font-size: 13px; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px;">Sujet</th>
                        <th style="padding: 16px; font-weight: 600; font-size: 13px; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px;">ICCID</th>
                        <th style="padding: 16px; font-weight: 600; font-size: 13px; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px;">Date envoi</th>
                        <th style="padding: 16px; font-weight: 600; font-size: 13px; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px;">Statut</th>
                        <th style="padding: 16px; font-weight: 600; font-size: 13px; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($mails as $mail)
                        <tr style="border-bottom: 1px solid #e5e7eb; transition: background 0.2s;" onmouseover="this.style.background='#f9fafb'" onmouseout="this.style.background='white'">
                            <td style="padding: 16px; font-weight: 600; color: #1a1a1a;">
                                @if($mail->simRequest)
                                    <a href="{{ route('sim-requests.show', $mail->simRequest) }}" style="color: #00574A; text-decoration: none;">
                                        {{ $mail->request_number }}
                                    </a>
                                @else
                                    {{ $mail->request_number }}
                                @endif
                            </td>
                            <td style="padding: 16px;">
                                @php
                                    $typeLabels = [
                                        'recuperation' => 'Récupération',
                                        'creation' => 'Création',
                                        'suspension' => 'Suspension',
                                        'desactivation' => 'Désactivation',
                                        'ajustement' => 'Ajustement',
                                    ];
                                    $typeColors = [
                                        'recuperation' => ['bg' => '#fef3c7', 'text' => '#92400e'],
                                        'creation' => ['bg' => '#d1fae5', 'text' => '#065f46'],
                                        'suspension' => ['bg' => '#dbeafe', 'text' => '#1e40af'],
                                        'desactivation' => ['bg' => '#fee2e2', 'text' => '#991b1b'],
                                        'ajustement' => ['bg' => '#e9d5ff', 'text' => '#6b21a8'],
                                    ];
                                    $typeColor = $typeColors[$mail->request_type] ?? ['bg' => '#f3f4f6', 'text' => '#374151'];
                                @endphp
                                <span class="badge" style="background: {{ $typeColor['bg'] }}; color: {{ $typeColor['text'] }}; padding: 6px 12px; border-radius: 6px; font-size: 12px; font-weight: 500;">
                                    {{ $typeLabels[$mail->request_type] ?? ucfirst($mail->request_type) }}
                                </span>
                            </td>
                            <td style="padding: 16px; color: #4b5563;">
                                <div style="max-width: 300px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="{{ $mail->message_subject }}">
                                    {{ $mail->message_subject ?? '-' }}
                                </div>
                            </td>
                            <td style="padding: 16px; color: #4b5563;">
                                {{ $mail->sim_iccid ?? '-' }}
                            </td>
                            <td style="padding: 16px; color: #6b7280; font-size: 14px;">
                                {{ $mail->created_at->format('d/m/Y H:i') }}
                            </td>
                            <td style="padding: 16px;">
                                @if($mail->status_after_sent)
                                    <span class="badge bg-success" style="padding: 6px 12px; border-radius: 6px; font-size: 12px; font-weight: 500;">
                                        {{ ucfirst($mail->status_after_sent) }}
                                    </span>
                                @else
                                    <span class="badge bg-secondary" style="padding: 6px 12px; border-radius: 6px; font-size: 12px; font-weight: 500;">
                                        En attente
                                    </span>
                                @endif
                            </td>
                            <td style="padding: 16px;">
                                <button type="button" 
                                        class="btn btn-sm btn-primary check-mail-btn" 
                                        data-mail-id="{{ $mail->id }}"
                                        data-request-type="{{ $mail->request_type ?? '' }}"
                                        data-message-subject="{{ $mail->message_subject ?? '' }}"
                                        data-request-id="{{ $mail->request_id ?? '' }}"
                                        style="border-radius: 6px; padding: 6px 12px; background: #00574A; border: none;"
                                        data-bs-toggle="tooltip" 
                                        title="Vérifier la réponse">
                                    <i class="bi bi-envelope-check"></i> Vérifier
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center" style="padding: 40px; color: #9ca3af;">
                                <i class="bi bi-inbox" style="font-size: 48px; opacity: 0.5; margin-bottom: 12px; display: block;"></i>
                                Aucun mail trouvé
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($mails->hasPages())
        <div class="mt-4">
            {{ $mails->links('pagination::bootstrap-5') }}
        </div>
        @endif
    </div>
</div>

<!-- Modal pour afficher les résultats du webhook CheckMail -->
<div class="modal fade" id="checkMailModal" tabindex="-1" aria-labelledby="checkMailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" style="border-radius: 12px; border: none; box-shadow: 0 10px 25px rgba(0,0,0,0.15);">
            <div class="modal-header" style="border-bottom: 1px solid #e2e8f0; padding: 20px;">
                <h5 class="modal-title" id="checkMailModalLabel" style="font-weight: 600; color: #1e293b;">
                    <i class="bi bi-envelope-check"></i> Réponse de l'opérateur
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" style="padding: 20px;">
                <div id="checkMailLoading" class="text-center" style="padding: 40px;">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Chargement...</span>
                    </div>
                    <p class="mt-3" style="color: #64748b;">Vérification en cours...</p>
                </div>
                <div id="checkMailContent" style="display: none;">
                    <div class="mb-3">
                        <label class="form-label" style="font-weight: 600; color: #1e293b; margin-bottom: 8px;">Sujet de la demande</label>
                        <div class="form-control" style="background: #f9fafb; border: 1px solid #e2e8f0; border-radius: 8px; padding: 12px;" id="modal-request-subject">
                            -
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" style="font-weight: 600; color: #1e293b; margin-bottom: 8px;">Statut</label>
                        <div id="modal-status">
                            <span class="badge bg-secondary">-</span>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" style="font-weight: 600; color: #1e293b; margin-bottom: 8px;">Message</label>
                        <div class="form-control" style="background: #f9fafb; border: 1px solid #e2e8f0; border-radius: 8px; padding: 12px; min-height: 100px; white-space: pre-wrap;" id="modal-message">
                            -
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" style="font-weight: 600; color: #1e293b; margin-bottom: 8px;">Réponse de l'opérateur</label>
                        <textarea class="form-control" id="operator-response" rows="4" style="border-radius: 8px; border: 1px solid #e2e8f0;" placeholder="Saisir la réponse de l'opérateur..."></textarea>
                    </div>
                </div>
                <div id="checkMailError" style="display: none;">
                    <div class="alert alert-danger" style="border-radius: 8px; border: none;">
                        <i class="bi bi-exclamation-triangle"></i> <span id="error-message"></span>
                    </div>
                </div>
            </div>
            <div class="modal-footer" style="border-top: 1px solid #e2e8f0; padding: 20px;">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" style="border-radius: 8px;">
                    <i class="bi bi-x-circle"></i> Annuler
                </button>
                <button type="button" class="btn btn-primary" id="updateStatusBtn" style="border-radius: 8px; background: #00574A; border: none; display: none;">
                    <i class="bi bi-check-circle"></i> Mettre à jour
                </button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const checkMailButtons = document.querySelectorAll('.check-mail-btn');
    const modal = new bootstrap.Modal(document.getElementById('checkMailModal'));
    let currentMailId = null;

    checkMailButtons.forEach(button => {
        button.addEventListener('click', function() {
            currentMailId = this.dataset.mailId;
            const requestType = this.dataset.requestType || '';
            const messageSubject = this.dataset.messageSubject || '';
            const requestId = this.dataset.requestId || '';

            // Vérifier que tous les paramètres sont présents
            if (!currentMailId || !requestType || !messageSubject || !requestId) {
                showToast('Erreur: Paramètres manquants pour vérifier le mail', 'error');
                console.error('Missing parameters:', {
                    id: currentMailId,
                    request_type: requestType,
                    message_subject: messageSubject,
                    request_id: requestId
                });
                return;
            }

            // Afficher le modal
            modal.show();

            // Réinitialiser le contenu
            document.getElementById('checkMailLoading').style.display = 'block';
            document.getElementById('checkMailContent').style.display = 'none';
            document.getElementById('checkMailError').style.display = 'none';
            document.getElementById('updateStatusBtn').style.display = 'none';
            document.getElementById('operator-response').value = '';

            // Appeler le webhook CheckMail
            fetch('{{ route("mail-sent.check-mail") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    id: currentMailId,
                    request_type: requestType,
                    message_subject: messageSubject,
                    request_id: requestId
                })
            })
            .then(response => response.json())
            .then(data => {
                document.getElementById('checkMailLoading').style.display = 'none';

                if (data.success) {
                    document.getElementById('checkMailContent').style.display = 'block';
                    
                    // Afficher les données
                    document.getElementById('modal-request-subject').textContent = data.data.request_subject || messageSubject || '-';
                    
                    const statusBadge = document.getElementById('modal-status');
                    if (data.data.status) {
                        const statusColors = {
                            'ok': 'success',
                            'error': 'danger',
                            'pending': 'warning'
                        };
                        const color = statusColors[data.data.status.toLowerCase()] || 'secondary';
                        statusBadge.innerHTML = `<span class="badge bg-${color}">${data.data.status}</span>`;
                    } else {
                        statusBadge.innerHTML = '<span class="badge bg-secondary">-</span>';
                    }
                    
                    document.getElementById('modal-message').textContent = data.data.msg || '-';
                    
                    // Afficher le bouton de mise à jour si le statut est OK
                    if (data.data.status && data.data.status.toLowerCase() === 'ok') {
                        document.getElementById('updateStatusBtn').style.display = 'block';
                    }
                } else {
                    document.getElementById('checkMailError').style.display = 'block';
                    document.getElementById('error-message').textContent = data.message || 'Erreur lors de la vérification';
                }
            })
            .catch(error => {
                document.getElementById('checkMailLoading').style.display = 'none';
                document.getElementById('checkMailError').style.display = 'block';
                document.getElementById('error-message').textContent = 'Erreur de connexion: ' + error.message;
            });
        });
    });

    // Gérer la mise à jour du statut
    document.getElementById('updateStatusBtn').addEventListener('click', function() {
        const operatorResponse = document.getElementById('operator-response').value;
        
        if (!currentMailId) {
            showToast('Erreur: ID du mail non trouvé', 'error');
            return;
        }

        // Afficher un loader
        this.disabled = true;
        this.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Mise à jour...';

        fetch(`/mail-sent/${currentMailId}/update-status`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                operator_response: operatorResponse
            })
        })
        .then(response => {
            if (!response.ok) {
                return response.json().then(data => {
                    throw new Error(data.message || 'Erreur lors de la mise à jour');
                });
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                modal.hide();
                showToast('Statut mis à jour avec succès', 'success');
                setTimeout(() => {
                    window.location.reload();
                }, 1000);
            } else {
                showToast(data.message || 'Erreur lors de la mise à jour', 'error');
                this.disabled = false;
                this.innerHTML = '<i class="bi bi-check-circle"></i> Mettre à jour';
            }
        })
        .catch(error => {
            showToast('Erreur de connexion: ' + error.message, 'error');
            this.disabled = false;
            this.innerHTML = '<i class="bi bi-check-circle"></i> Mettre à jour';
        });
    });
});
</script>
@endsection

