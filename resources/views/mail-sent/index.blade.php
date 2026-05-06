@extends('layouts.bootstrap')

@section('title', 'Check Mail - Mails Envoyés')
@section('page-title', 'Check Mail')

@push('styles')
<style>
    /* ── Stat cards ─────────────────────────────────────────── */
    .ms-stats { display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; margin-bottom: 20px; }
    @media (max-width: 767px) { .ms-stats { grid-template-columns: 1fr; } }

    .ms-stat {
        background: white;
        border: 1px solid var(--border);
        border-radius: 14px;
        padding: 18px 20px;
        display: flex; align-items: center; justify-content: space-between;
        box-shadow: 0 1px 4px rgba(0,0,0,0.04);
    }
    .ms-stat-label { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em; color: var(--muted); margin-bottom: 4px; }
    .ms-stat-value { font-size: 24px; font-weight: 700; line-height: 1; }
    .ms-stat-icon  { font-size: 28px; opacity: 0.18; }

    /* ── Main card ──────────────────────────────────────────── */
    .ms-card {
        background: white;
        border: 1px solid var(--border);
        border-radius: 14px;
        box-shadow: 0 1px 4px rgba(0,0,0,0.05);
        overflow: hidden;
    }
    .ms-card-body { padding: 20px; }

    /* ── Filters ────────────────────────────────────────────── */
    .ms-filters {
        display: flex; flex-wrap: wrap; gap: 10px; align-items: flex-end;
        margin-bottom: 20px; padding-bottom: 20px; border-bottom: 1px solid var(--border);
    }
    .ms-filter-group { display: flex; flex-direction: column; gap: 5px; }
    .ms-filter-label { font-size: 12px; font-weight: 600; color: var(--muted); }
    .ms-filter-input {
        height: 38px; border: 1.5px solid var(--border); border-radius: 9px;
        padding: 0 12px; font-size: 13.5px; font-family: 'Poppins', sans-serif;
        color: var(--text); background: white; outline: none; min-width: 0;
        transition: border-color 0.2s, box-shadow 0.2s;
    }
    .ms-filter-input:focus { border-color: var(--primary); box-shadow: 0 0 0 3px var(--primary-glow); }

    .btn-ms-search {
        height: 38px; padding: 0 16px;
        background: var(--primary); color: white; border: none; border-radius: 9px;
        font-size: 13.5px; font-weight: 600; font-family: 'Poppins', sans-serif;
        display: inline-flex; align-items: center; gap: 6px;
        cursor: pointer; transition: background 0.18s; white-space: nowrap;
    }
    .btn-ms-search:hover { background: var(--primary-dark); }

    .btn-ms-reset {
        height: 38px; padding: 0 14px;
        background: white; color: var(--muted);
        border: 1.5px solid var(--border); border-radius: 9px;
        font-size: 13.5px; font-family: 'Poppins', sans-serif;
        display: inline-flex; align-items: center; gap: 5px;
        cursor: pointer; text-decoration: none; white-space: nowrap;
        transition: border-color 0.18s, color 0.18s;
    }
    .btn-ms-reset:hover { border-color: #ef4444; color: #ef4444; }

    /* ── Table ──────────────────────────────────────────────── */
    .ms-table { width: 100%; border-collapse: collapse; }
    .ms-table th {
        padding: 11px 16px; font-size: 11px; font-weight: 700;
        text-transform: uppercase; letter-spacing: 0.05em;
        color: var(--muted); background: #f8fafc;
        border-bottom: 1px solid var(--border); text-align: left; white-space: nowrap;
    }
    .ms-table td {
        padding: 12px 16px; font-size: 13px; color: var(--text);
        border-bottom: 1px solid #f1f5f9; vertical-align: middle;
    }
    .ms-table tbody tr:last-child td { border-bottom: none; }
    .ms-table tbody tr:hover td { background: #f8fafc; }

    .ms-type-badge { display: inline-block; padding: 3px 10px; border-radius: 20px; font-size: 12px; font-weight: 600; }

    .btn-ms-check {
        display: inline-flex; align-items: center; gap: 5px;
        padding: 5px 12px;
        background: var(--primary); color: white; border: none; border-radius: 8px;
        font-size: 12.5px; font-weight: 600; font-family: 'Poppins', sans-serif;
        cursor: pointer; transition: background 0.18s; white-space: nowrap;
    }
    .btn-ms-check:hover { background: var(--primary-dark); }

    .ms-empty { padding: 48px; text-align: center; color: var(--muted); }
    .ms-empty i { font-size: 42px; opacity: 0.3; display: block; margin-bottom: 12px; }

    /* ── Modal overrides ────────────────────────────────────── */
    .modal-content { border-radius: 14px; border: none; box-shadow: 0 10px 40px rgba(0,0,0,0.15); }
    .modal-header  { border-bottom: 1px solid var(--border); padding: 18px 22px; }
    .modal-footer  { border-top: 1px solid var(--border); padding: 14px 22px; }
    .modal-body    { padding: 22px; }
    .modal-title   { font-size: 15px; font-weight: 700; color: var(--text); }

    .modal-field-label { font-size: 12.5px; font-weight: 600; color: var(--text); margin-bottom: 6px; display: block; }
    .modal-field-display {
        background: #f8fafc; border: 1px solid var(--border); border-radius: 8px;
        padding: 10px 14px; font-size: 13.5px; color: var(--text);
    }
    .modal-textarea {
        width: 100%; border: 1.5px solid var(--border); border-radius: 9px;
        padding: 10px 12px; font-size: 13.5px; font-family: 'Poppins', sans-serif;
        color: var(--text); resize: vertical; outline: none;
        transition: border-color 0.2s, box-shadow 0.2s;
    }
    .modal-textarea:focus { border-color: var(--primary); box-shadow: 0 0 0 3px var(--primary-glow); }

    .btn-modal-primary {
        display: inline-flex; align-items: center; gap: 6px;
        height: 36px; padding: 0 16px;
        background: var(--primary); color: white; border: none; border-radius: 9px;
        font-size: 13px; font-weight: 600; font-family: 'Poppins', sans-serif;
        cursor: pointer; transition: background 0.18s;
    }
    .btn-modal-primary:hover { background: var(--primary-dark); }
    .btn-modal-primary:disabled { opacity: 0.6; cursor: not-allowed; }

    .btn-modal-cancel {
        display: inline-flex; align-items: center; gap: 6px;
        height: 36px; padding: 0 14px;
        background: white; color: var(--text);
        border: 1.5px solid var(--border); border-radius: 9px;
        font-size: 13px; font-weight: 500; font-family: 'Poppins', sans-serif;
        cursor: pointer; transition: border-color 0.18s, color 0.18s;
    }
    .btn-modal-cancel:hover { border-color: var(--primary); color: var(--primary); }
</style>
@endpush

@section('content')

{{-- Stat cards --}}
<div class="ms-stats">
    <div class="ms-stat">
        <div>
            <div class="ms-stat-label">Total Mails</div>
            <div class="ms-stat-value" style="color:var(--primary);">{{ $stats['total'] }}</div>
        </div>
        <i class="bi bi-envelope ms-stat-icon" style="color:var(--primary);"></i>
    </div>
    <div class="ms-stat">
        <div>
            <div class="ms-stat-label">Ce mois</div>
            <div class="ms-stat-value" style="color:#3b82f6;">{{ $stats['this_month'] }}</div>
        </div>
        <i class="bi bi-calendar-month ms-stat-icon" style="color:#3b82f6;"></i>
    </div>
    <div class="ms-stat">
        <div>
            <div class="ms-stat-label">Cette semaine</div>
            <div class="ms-stat-value" style="color:#10b981;">{{ $stats['this_week'] }}</div>
        </div>
        <i class="bi bi-calendar-week ms-stat-icon" style="color:#10b981;"></i>
    </div>
</div>

{{-- Main card --}}
<div class="ms-card">
    <div class="ms-card-body">

        {{-- Filters --}}
        <form method="GET" action="{{ route('mail-sent.index') }}" id="filters-form">
            <div class="ms-filters">
                <div class="ms-filter-group">
                    <label class="ms-filter-label">Type de demande</label>
                    <select name="request_type" class="ms-filter-input" style="min-width:160px;">
                        <option value="">Tous les types</option>
                        <option value="recuperation"  {{ request('request_type') === 'recuperation'  ? 'selected' : '' }}>Récupération</option>
                        <option value="creation"      {{ request('request_type') === 'creation'      ? 'selected' : '' }}>Création</option>
                        <option value="suspension"    {{ request('request_type') === 'suspension'    ? 'selected' : '' }}>Suspension</option>
                        <option value="desactivation" {{ request('request_type') === 'desactivation' ? 'selected' : '' }}>Désactivation</option>
                        <option value="ajustement"    {{ request('request_type') === 'ajustement'    ? 'selected' : '' }}>Ajustement</option>
                    </select>
                </div>
                <div class="ms-filter-group">
                    <label class="ms-filter-label">Date début</label>
                    <input type="date" name="date_from" class="ms-filter-input" value="{{ request('date_from') }}">
                </div>
                <div class="ms-filter-group">
                    <label class="ms-filter-label">Date fin</label>
                    <input type="date" name="date_to" class="ms-filter-input" value="{{ request('date_to') }}">
                </div>
                <div class="ms-filter-group" style="flex:1;min-width:200px;">
                    <label class="ms-filter-label">Recherche</label>
                    <input type="text" name="search" class="ms-filter-input" value="{{ request('search') }}" placeholder="N° demande, ICCID, sujet...">
                </div>
                <div class="d-flex align-items-end gap-2">
                    <button type="submit" class="btn-ms-search">
                        <i class="bi bi-search"></i> Rechercher
                    </button>
                    @if(request()->anyFilled(['request_type', 'date_from', 'date_to', 'search']))
                        <a href="{{ route('mail-sent.index') }}" class="btn-ms-reset">
                            <i class="bi bi-x-circle"></i> Réinitialiser
                        </a>
                    @endif
                </div>
            </div>
        </form>

        {{-- Table --}}
        @php
            $typeLabels = [
                'recuperation' => 'Récupération', 'creation' => 'Création',
                'suspension' => 'Suspension', 'desactivation' => 'Désactivation',
                'ajustement' => 'Ajustement',
            ];
            $typeStyles = [
                'recuperation' => 'background:#fef3c7;color:#92400e;',
                'creation'     => 'background:#d1fae5;color:#065f46;',
                'suspension'   => 'background:#dbeafe;color:#1e40af;',
                'desactivation'=> 'background:#fee2e2;color:#991b1b;',
                'ajustement'   => 'background:#e9d5ff;color:#6b21a8;',
            ];
        @endphp

        <div style="overflow-x:auto;">
            <table class="ms-table">
                <thead>
                    <tr>
                        <th>N° Demande</th>
                        <th>Type</th>
                        <th>Sujet</th>
                        <th>ICCID</th>
                        <th>Date envoi</th>
                        <th>Statut</th>
                        <th style="width:1%;text-align:center;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($mails as $mail)
                        @php $style = $typeStyles[$mail->request_type] ?? 'background:#f3f4f6;color:#374151;'; @endphp
                        <tr>
                            <td style="font-weight:700;">
                                @if($mail->simRequest)
                                    <a href="{{ route('sim-requests.show', $mail->simRequest) }}"
                                       style="color:var(--primary);text-decoration:none;">
                                        {{ $mail->request_number }}
                                    </a>
                                @else
                                    {{ $mail->request_number }}
                                @endif
                            </td>
                            <td>
                                <span class="ms-type-badge" style="{{ $style }}">
                                    {{ $typeLabels[$mail->request_type] ?? ucfirst($mail->request_type) }}
                                </span>
                            </td>
                            <td>
                                <span style="display:block;max-width:280px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;"
                                      title="{{ $mail->message_subject }}">
                                    {{ $mail->message_subject ?? '—' }}
                                </span>
                            </td>
                            <td style="color:var(--muted);">{{ $mail->sim_iccid ?? '—' }}</td>
                            <td style="color:var(--muted);white-space:nowrap;">{{ $mail->created_at->format('d/m/Y H:i') }}</td>
                            <td>
                                @if($mail->status_after_sent)
                                    <span style="display:inline-block;padding:3px 10px;border-radius:20px;font-size:12px;font-weight:600;background:#d1fae5;color:#065f46;">
                                        {{ ucfirst($mail->status_after_sent) }}
                                    </span>
                                @else
                                    <span style="display:inline-block;padding:3px 10px;border-radius:20px;font-size:12px;font-weight:600;background:#f3f4f6;color:#6b7280;">
                                        En attente
                                    </span>
                                @endif
                            </td>
                            <td style="text-align:center;white-space:nowrap;">
                                <button type="button"
                                        class="btn-ms-check check-mail-btn"
                                        data-mail-id="{{ $mail->id }}"
                                        data-request-type="{{ $mail->request_type ?? '' }}"
                                        data-message-subject="{{ $mail->message_subject ?? '' }}"
                                        data-request-id="{{ $mail->request_id ?? '' }}">
                                    <i class="bi bi-envelope-check"></i> Vérifier
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7">
                                <div class="ms-empty">
                                    <i class="bi bi-inbox"></i>
                                    Aucun mail trouvé
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($mails->hasPages())
            <div class="mt-4">
                {{ $mails->links('pagination::bootstrap-5') }}
            </div>
        @endif

    </div>
</div>

{{-- Modal CheckMail --}}
<div class="modal fade" id="checkMailModal" tabindex="-1" aria-labelledby="checkMailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="checkMailModalLabel">
                    <i class="bi bi-envelope-check" style="color:var(--primary);margin-right:6px;"></i>
                    Réponse de l'opérateur
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="checkMailLoading" class="text-center" style="padding:40px;">
                    <div class="spinner-border" role="status" style="color:var(--primary);"></div>
                    <p class="mt-3" style="color:var(--muted);">Vérification en cours...</p>
                </div>
                <div id="checkMailContent" style="display:none;">
                    <div class="mb-3">
                        <label class="modal-field-label">Sujet de la demande</label>
                        <div class="modal-field-display" id="modal-request-subject">—</div>
                    </div>
                    <div class="mb-3">
                        <label class="modal-field-label">Statut</label>
                        <div id="modal-status"><span style="padding:3px 10px;border-radius:20px;font-size:12px;font-weight:600;background:#f3f4f6;color:#6b7280;">—</span></div>
                    </div>
                    <div class="mb-3">
                        <label class="modal-field-label">Message</label>
                        <div class="modal-field-display" id="modal-message" style="min-height:80px;white-space:pre-wrap;">—</div>
                    </div>
                    <div class="mb-3">
                        <label class="modal-field-label">Réponse de l'opérateur</label>
                        <textarea class="modal-textarea" id="operator-response" rows="4" placeholder="Saisir la réponse de l'opérateur..."></textarea>
                    </div>
                </div>
                <div id="checkMailError" style="display:none;">
                    <div style="background:#fef2f2;border:1px solid #fecaca;border-radius:10px;padding:14px 16px;color:#ef4444;font-size:13.5px;">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        <span id="error-message"></span>
                    </div>
                </div>
            </div>
            <div class="modal-footer" style="gap:10px;">
                <button type="button" class="btn-modal-cancel" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle"></i> Annuler
                </button>
                <button type="button" class="btn-modal-primary" id="updateStatusBtn" style="display:none;">
                    <i class="bi bi-check-circle"></i> Mettre à jour
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const modal = new bootstrap.Modal(document.getElementById('checkMailModal'));
    let currentMailId = null;

    document.querySelectorAll('.check-mail-btn').forEach(function(button) {
        button.addEventListener('click', function() {
            currentMailId    = this.dataset.mailId;
            const requestType    = this.dataset.requestType    || '';
            const messageSubject = this.dataset.messageSubject || '';
            const requestId      = this.dataset.requestId      || '';

            if (!currentMailId || !requestType || !messageSubject || !requestId) {
                showToast('Erreur : paramètres manquants pour vérifier le mail', 'error');
                return;
            }

            modal.show();
            document.getElementById('checkMailLoading').style.display = 'block';
            document.getElementById('checkMailContent').style.display = 'none';
            document.getElementById('checkMailError').style.display   = 'none';
            document.getElementById('updateStatusBtn').style.display  = 'none';
            document.getElementById('operator-response').value        = '';

            fetch('{{ route("mail-sent.check-mail") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ id: currentMailId, request_type: requestType, message_subject: messageSubject, request_id: requestId })
            })
            .then(r => r.json())
            .then(data => {
                document.getElementById('checkMailLoading').style.display = 'none';
                if (data.success) {
                    document.getElementById('checkMailContent').style.display = 'block';
                    document.getElementById('modal-request-subject').textContent = data.data.request_subject || messageSubject || '—';
                    document.getElementById('modal-message').textContent = data.data.msg || '—';

                    const statusEl = document.getElementById('modal-status');
                    if (data.data.status) {
                        const colors = { ok: '#d1fae5|#065f46', error: '#fee2e2|#991b1b', pending: '#fef3c7|#92400e' };
                        const [bg, text] = (colors[data.data.status.toLowerCase()] || '#f3f4f6|#6b7280').split('|');
                        statusEl.innerHTML = `<span style="display:inline-block;padding:3px 10px;border-radius:20px;font-size:12px;font-weight:600;background:${bg};color:${text};">${data.data.status}</span>`;
                        if (data.data.status.toLowerCase() === 'ok') {
                            document.getElementById('updateStatusBtn').style.display = 'inline-flex';
                        }
                    } else {
                        statusEl.innerHTML = '<span style="display:inline-block;padding:3px 10px;border-radius:20px;font-size:12px;font-weight:600;background:#f3f4f6;color:#6b7280;">—</span>';
                    }
                } else {
                    document.getElementById('checkMailError').style.display = 'block';
                    document.getElementById('error-message').textContent = data.message || 'Erreur lors de la vérification';
                }
            })
            .catch(err => {
                document.getElementById('checkMailLoading').style.display = 'none';
                document.getElementById('checkMailError').style.display   = 'block';
                document.getElementById('error-message').textContent = 'Erreur de connexion : ' + err.message;
            });
        });
    });

    document.getElementById('updateStatusBtn').addEventListener('click', function() {
        const operatorResponse = document.getElementById('operator-response').value;
        if (!currentMailId) { showToast('Erreur : ID du mail non trouvé', 'error'); return; }

        this.disabled = true;
        this.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Mise à jour...';

        fetch(`/mail-sent/${currentMailId}/update-status`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ operator_response: operatorResponse })
        })
        .then(r => {
            if (!r.ok) return r.json().then(d => { throw new Error(d.message || 'Erreur'); });
            return r.json();
        })
        .then(data => {
            if (data.success) {
                modal.hide();
                showToast('Statut mis à jour avec succès', 'success');
                setTimeout(() => window.location.reload(), 1000);
            } else {
                showToast(data.message || 'Erreur lors de la mise à jour', 'error');
                this.disabled = false;
                this.innerHTML = '<i class="bi bi-check-circle"></i> Mettre à jour';
            }
        })
        .catch(err => {
            showToast('Erreur : ' + err.message, 'error');
            this.disabled = false;
            this.innerHTML = '<i class="bi bi-check-circle"></i> Mettre à jour';
        });
    });
});
</script>
@endpush
