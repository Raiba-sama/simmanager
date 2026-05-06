@extends('layouts.bootstrap')

@section('title', 'Toutes les notifications')
@section('page-title', 'Toutes les notifications')

@push('styles')
<style>
    /* ── Notification card ───────────────────────────────────────── */
    .notif-card {
        background: white;
        border: 1px solid var(--border);
        border-radius: 14px;
        box-shadow: 0 1px 4px rgba(0,0,0,0.05);
        overflow: hidden;
    }

    .notif-card-header {
        padding: 18px 22px;
        border-bottom: 1px solid var(--border);
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 12px;
    }

    .notif-card-title {
        font-size: 15px;
        font-weight: 700;
        color: var(--text);
        margin: 0;
    }

    .notif-unread-badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        background: #fef2f2;
        color: #ef4444;
        border: 1px solid #fecaca;
        padding: 3px 10px;
        border-radius: 20px;
        font-size: 11.5px;
        font-weight: 600;
    }

    .notif-read-badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        background: #f0fdf4;
        color: #16a34a;
        border: 1px solid #bbf7d0;
        padding: 3px 10px;
        border-radius: 20px;
        font-size: 11.5px;
        font-weight: 600;
    }

    /* ── List ────────────────────────────────────────────────────── */
    .notif-list { padding: 0; }

    .notif-item {
        display: flex;
        gap: 14px;
        padding: 16px 22px;
        border-bottom: 1px solid #f1f5f9;
        cursor: pointer;
        transition: background 0.18s;
        border-left: 3px solid transparent;
    }

    .notif-item:last-child { border-bottom: none; }
    .notif-item:hover { background: #f8fafc; }

    .notif-item.is-unread {
        background: #f0fdf4;
        border-left-color: var(--primary);
    }

    .notif-item.urgency-urgent {
        background: #fef2f2;
        border-left-color: #ef4444;
    }

    .notif-item.urgency-high {
        background: #fffbeb;
        border-left-color: #f59e0b;
    }

    .notif-icon {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        font-size: 18px;
        color: white;
    }

    .notif-body { flex: 1; min-width: 0; }

    .notif-title {
        font-size: 13.5px;
        font-weight: 600;
        color: var(--text);
        margin-bottom: 3px;
        line-height: 1.4;
    }

    .notif-item.urgency-urgent .notif-title { color: #ef4444; }

    .notif-message {
        font-size: 12.5px;
        color: var(--muted);
        line-height: 1.5;
        margin-bottom: 4px;
    }

    .notif-pending-label {
        font-size: 11px;
        color: #94a3b8;
    }

    .notif-meta {
        flex-shrink: 0;
        text-align: right;
        display: flex;
        flex-direction: column;
        align-items: flex-end;
        gap: 6px;
    }

    .notif-time {
        font-size: 11px;
        color: #94a3b8;
        white-space: nowrap;
    }

    .notif-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: var(--primary);
    }

    /* ── Mark all btn ────────────────────────────────────────────── */
    .btn-mark-all {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 7px 14px;
        background: var(--primary);
        color: white;
        border: none;
        border-radius: 9px;
        font-size: 12.5px;
        font-weight: 600;
        font-family: 'Poppins', sans-serif;
        cursor: pointer;
        transition: background 0.18s;
        text-decoration: none;
    }

    .btn-mark-all:hover { background: var(--primary-dark); color: white; }

    /* ── Empty state ─────────────────────────────────────────────── */
    .notif-empty {
        padding: 60px 20px;
        text-align: center;
        color: var(--muted);
    }

    .notif-empty i { font-size: 48px; opacity: 0.3; display: block; margin-bottom: 12px; }
    .notif-empty p { font-size: 14px; margin: 0; }

    /* ── Footer / pagination ─────────────────────────────────────── */
    .notif-card-footer {
        padding: 14px 22px;
        border-top: 1px solid var(--border);
        background: #fafafa;
    }
</style>
@endpush

@section('content')
<div class="notif-card">

    {{-- Header --}}
    <div class="notif-card-header">
        <div class="d-flex align-items-center gap-3">
            <h2 class="notif-card-title">Toutes les notifications</h2>
            @if($unreadCount > 0)
                <span class="notif-unread-badge">
                    <i class="bi bi-circle-fill" style="font-size:7px;"></i>
                    {{ $unreadCount }} non lue(s)
                </span>
            @else
                <span class="notif-read-badge">
                    <i class="bi bi-check-circle-fill" style="font-size:12px;"></i>
                    Tout est lu
                </span>
            @endif
        </div>

        @if($unreadCount > 0)
            <button type="button" class="btn-mark-all" onclick="markAllRead()">
                <i class="bi bi-check-all"></i> Tout marquer comme lu
            </button>
        @endif
    </div>

    {{-- List --}}
    @if($notifications->count() > 0)
        <div class="notif-list">
            @foreach($notifications as $notification)
                @php
                    $data        = $notification->data;
                    $isUnread    = !$notification->read_at;
                    $isReminder  = isset($data['type']) && $data['type'] === 'request_reminder';
                    $urgency     = $isReminder && isset($data['urgency_level']) ? $data['urgency_level'] : null;

                    $itemClass = 'notif-item';
                    if ($urgency) {
                        $itemClass .= ' urgency-' . $urgency;
                    } elseif ($isUnread) {
                        $itemClass .= ' is-unread';
                    }

                    $notifUrl = $data['url'] ?? '#';
                    if ($notifUrl !== '#' && str_starts_with($notifUrl, 'http')) {
                        $parsed   = parse_url($notifUrl);
                        $notifUrl = ($parsed['path'] ?? '/') . (isset($parsed['query']) ? '?' . $parsed['query'] : '');
                    }
                @endphp
                <div class="{{ $itemClass }}"
                     data-url="{{ $notifUrl }}"
                     onclick="markAsReadAndRedirect('{{ $notification->id }}', '{{ $notifUrl }}', this)">

                    <div class="notif-icon" style="background:{{ $data['color'] ?? 'var(--primary)' }};">
                        <i class="bi {{ $data['icon'] ?? 'bi-bell' }}"></i>
                    </div>

                    <div class="notif-body">
                        <div class="notif-title">{{ $data['title'] ?? 'Notification' }}</div>
                        <div class="notif-message">{{ $data['message'] ?? '' }}</div>
                        @if($isReminder && isset($data['days_pending']))
                            <div class="notif-pending-label">
                                <i class="bi bi-clock-history"></i>
                                En attente depuis {{ $data['days_pending'] }} jour(s)
                            </div>
                        @endif
                    </div>

                    <div class="notif-meta">
                        <span class="notif-time">{{ $notification->created_at->diffForHumans() }}</span>
                        @if($isUnread)
                            <span class="notif-dot"></span>
                        @endif
                    </div>

                </div>
            @endforeach
        </div>

        <div class="notif-card-footer">
            {{ $notifications->links('pagination::bootstrap-5') }}
        </div>
    @else
        <div class="notif-empty">
            <i class="bi bi-bell-slash"></i>
            <p>Aucune notification pour le moment</p>
        </div>
    @endif

</div>
@endsection

@push('scripts')
<script>
function markAsReadAndRedirect(notificationId, url, element) {
    if (!url || url === '#' || url === 'undefined' || url === 'null') {
        url = element.getAttribute('data-url') || url;
    }
    if (!url || url === '#' || url.trim() === '') return;

    if (url.startsWith('http')) {
        try { const u = new URL(url); url = u.pathname + u.search; } catch(e) {}
    }

    element.classList.remove('is-unread', 'urgency-urgent', 'urgency-high');
    element.style.borderLeftColor = 'transparent';
    element.style.background = '';

    const dot = element.querySelector('.notif-dot');
    if (dot) dot.remove();

    fetch(`{{ route('notifications.mark-read', '') }}/${notificationId}`, {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
        }
    })
    .then(r => r.json())
    .then(data => {
        let dest = data.url || url;
        if (dest && dest.startsWith('http')) {
            try { const u = new URL(dest); dest = u.pathname + u.search; } catch(e) {}
        }
        if (dest && dest !== '#' && dest.trim() !== '') window.location.href = dest;
    })
    .catch(() => {
        if (url && url !== '#') window.location.href = url;
    });
}

function markAllRead() {
    fetch('{{ route('notifications.mark-all-read') }}', {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
        }
    })
    .then(r => r.json())
    .then(data => { if (data.success) window.location.reload(); })
    .catch(() => window.location.reload());
}
</script>
@endpush
