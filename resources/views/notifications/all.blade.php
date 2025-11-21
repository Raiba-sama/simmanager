@extends('layouts.bootstrap')

@section('title', 'Toutes les notifications')
@section('page-title', 'Toutes les notifications')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card" style="border: none; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.08);">
            <div class="card-header" style="background: white; border-bottom: 1px solid #e2e8f0; border-radius: 12px 12px 0 0; padding: 20px;">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-0" style="font-weight: 600; color: #1e293b;">Toutes les notifications</h5>
                        <p class="mb-0 mt-1" style="font-size: 13px; color: #64748b;">
                            @if($unreadCount > 0)
                                <span class="badge" style="background: #ef4444; color: white; padding: 4px 8px; border-radius: 6px; font-size: 11px; font-weight: 600;">
                                    {{ $unreadCount }} non lue(s)
                                </span>
                            @else
                                Toutes les notifications sont lues
                            @endif
                        </p>
                    </div>
                    @if($unreadCount > 0)
                        <form method="POST" action="{{ route('notifications.mark-all-read') }}" id="markAllReadForm">
                            @csrf
                            <button type="submit" class="btn btn-sm" style="background: #00574A; color: white; border-radius: 6px; padding: 8px 16px; font-size: 13px; font-weight: 500;">
                                <i class="bi bi-check-all"></i> Tout marquer comme lu
                            </button>
                        </form>
                    @endif
                </div>
            </div>
            <div class="card-body" style="padding: 0;">
                @if($notifications->count() > 0)
                    <div class="notifications-list-page">
                        @foreach($notifications as $notification)
                            @php
                                $data = $notification->data;
                                $isUnread = !$notification->read_at;
                                $isReminder = isset($data['type']) && $data['type'] === 'request_reminder';
                                $urgencyLevel = $isReminder && isset($data['urgency_level']) ? $data['urgency_level'] : null;
                                
                                $urgencyColors = [
                                    'urgent' => '#ef4444',
                                    'high' => '#f59e0b',
                                    'normal' => '#3b82f6',
                                ];
                                
                                $urgencyBg = [
                                    'urgent' => '#fef2f2',
                                    'high' => '#fffbeb',
                                    'normal' => '#eff6ff',
                                ];
                                
                                $borderColor = $urgencyLevel ? $urgencyColors[$urgencyLevel] : ($isUnread ? '#00574A' : 'transparent');
                                $bgColor = $urgencyLevel ? $urgencyBg[$urgencyLevel] : ($isUnread ? '#f0f9ff' : 'white');
                            @endphp
                            @php
                                $notificationUrl = $data['url'] ?? '#';
                                // S'assurer que l'URL est relative
                                if ($notificationUrl !== '#' && strpos($notificationUrl, 'http') === 0) {
                                    $parsed = parse_url($notificationUrl);
                                    $notificationUrl = $parsed['path'] ?? '/';
                                    if (isset($parsed['query'])) {
                                        $notificationUrl .= '?' . $parsed['query'];
                                    }
                                }
                            @endphp
                            <div class="notification-item-page {{ $isUnread ? 'unread' : '' }}" 
                                 style="border-left: 4px solid {{ $borderColor }}; background: {{ $bgColor }}; padding: 16px 20px; border-bottom: 1px solid #f1f5f9; cursor: pointer; transition: background 0.2s;"
                                 data-url="{{ $notificationUrl }}"
                                 onclick="markAsReadAndRedirect('{{ $notification->id }}', '{{ $notificationUrl }}', this)">
                                <div class="d-flex gap-3">
                                    <div class="notification-icon-page" style="width: 48px; height: 48px; border-radius: 10px; background: {{ $data['color'] ?? '#00574A' }}; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                        <i class="bi {{ $data['icon'] ?? 'bi-bell' }}" style="font-size: 20px; color: white;"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="d-flex justify-content-between align-items-start mb-1">
                                            <div>
                                                <h6 class="mb-0" style="font-weight: {{ $isUnread ? '600' : '500' }}; color: {{ $isReminder && $urgencyLevel === 'urgent' ? '#ef4444' : '#1e293b' }}; font-size: 14px;">
                                                    {{ $data['title'] ?? 'Notification' }}
                                                </h6>
                                                <p class="mb-1 mt-1" style="font-size: 13px; color: #64748b; line-height: 1.5;">
                                                    {{ $data['message'] ?? '' }}
                                                </p>
                                                @if($isReminder && isset($data['days_pending']))
                                                    <div style="font-size: 11px; color: #64748b; margin-top: 4px;">
                                                        <i class="bi bi-clock-history"></i> En attente depuis {{ $data['days_pending'] }} jour(s)
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="text-end" style="flex-shrink: 0; margin-left: 12px;">
                                                <span style="font-size: 11px; color: #94a3b8;">
                                                    {{ $notification->created_at->diffForHumans() }}
                                                </span>
                                                @if($isUnread)
                                                    <div class="mt-1">
                                                        <span style="width: 8px; height: 8px; background: #00574A; border-radius: 50%; display: inline-block;"></span>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    
                    <div class="card-footer" style="background: white; border-top: 1px solid #e2e8f0; border-radius: 0 0 12px 12px; padding: 20px;">
                        {{ $notifications->links('pagination::bootstrap-5') }}
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="bi bi-bell-slash" style="font-size: 48px; color: #cbd5e1;"></i>
                        <p class="mt-3 mb-0" style="color: #64748b; font-size: 15px;">Aucune notification</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<style>
    .notification-item-page:hover {
        background: #f8fafc !important;
    }
    
    .notification-item-page.unread {
        font-weight: 500;
    }
</style>

<script>
    function markAsReadAndRedirect(notificationId, url, element) {
        // Récupérer l'URL depuis l'attribut data-url si elle n'est pas fournie
        if (!url || url === '#' || url === 'undefined' || url === 'null') {
            url = element.getAttribute('data-url') || url;
        }
        
        // Si toujours pas d'URL valide, ne rien faire
        if (!url || url === '#' || url === 'undefined' || url === 'null' || url.trim() === '') {
            console.warn('No valid URL for notification:', notificationId, 'URL:', url);
            return;
        }
        
        // S'assurer que l'URL est relative
        if (url.startsWith('http')) {
            try {
                const urlObj = new URL(url);
                url = urlObj.pathname + urlObj.search;
            } catch (e) {
                console.warn('Error parsing URL:', e);
            }
        }
        
        console.log('markAsReadAndRedirect:', { notificationId, url });
        
        // Marquer visuellement comme lu immédiatement
        element.classList.remove('unread');
        element.style.borderLeftColor = 'transparent';
        element.style.background = 'white';
        
        // Marquer comme lu côté serveur
        fetch(`{{ route('notifications.mark-read', '') }}/${notificationId}`, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            console.log('Notification marked as read, response:', data);
            if (data.success) {
                // Rediriger vers l'URL (priorité à l'URL du serveur)
                let redirectUrl = data.url || url;
                
                // S'assurer que l'URL est relative
                if (redirectUrl && redirectUrl.startsWith('http')) {
                    try {
                        const urlObj = new URL(redirectUrl);
                        redirectUrl = urlObj.pathname + urlObj.search;
                    } catch (e) {
                        console.warn('Error parsing redirect URL:', e);
                    }
                }
                
                if (redirectUrl && redirectUrl !== '#' && redirectUrl !== 'undefined' && redirectUrl !== 'null' && redirectUrl.trim() !== '') {
                    console.log('Redirecting to:', redirectUrl);
                    window.location.href = redirectUrl;
                } else {
                    console.warn('Invalid redirect URL:', redirectUrl);
                }
            }
        })
        .catch(error => {
            console.error('Error marking notification as read:', error);
            // En cas d'erreur, essayer quand même de rediriger
            if (url && url !== '#' && url !== 'undefined' && url !== 'null' && url.trim() !== '') {
                console.log('Fallback redirect to:', url);
                window.location.href = url;
            }
        });
    }
    
    // Gérer le formulaire "Tout marquer comme lu"
    document.getElementById('markAllReadForm')?.addEventListener('submit', function(e) {
        e.preventDefault();
        
        fetch('{{ route('notifications.mark-all-read') }}', {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Recharger la page pour mettre à jour l'affichage
                window.location.reload();
            }
        })
        .catch(error => {
            console.error('Error marking all as read:', error);
        });
    });
</script>
@endsection
