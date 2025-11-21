<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Get all notifications for the authenticated user
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $limit = $request->get('limit', 20);
        
        $notifications = $user->notifications()
            ->orderBy('created_at', 'desc')
            ->take($limit)
            ->get();
        
        $unreadCount = $user->unreadNotifications()->count();
        
        return response()->json([
            'notifications' => $notifications,
            'unread_count' => $unreadCount,
        ]);
    }

    /**
     * Mark a notification as read
     */
    public function markAsRead(Request $request, $id)
    {
        $user = Auth::user();
        $notification = $user->notifications()->where('id', $id)->first();
        
        if ($notification) {
            $notification->markAsRead();
            $data = $notification->data;
            $url = $data['url'] ?? null;
            
            // Log pour débogage
            \Log::info('Notification markAsRead', [
                'notification_id' => $id,
                'url_from_data' => $url,
                'data' => $data
            ]);
            
            // Ajouter un paramètre pour indiquer que la redirection vient d'une notification
            if ($url && $url !== '#' && $url !== null) {
                // S'assurer que l'URL est relative (sans domaine) pour éviter les problèmes
                // Si l'URL est absolue, extraire seulement le chemin
                if (strpos($url, 'http') === 0) {
                    $parsed = parse_url($url);
                    $url = $parsed['path'] ?? '/';
                    if (isset($parsed['query'])) {
                        $url .= '?' . $parsed['query'];
                    }
                }
                // Ajouter le paramètre from=notification
                $separator = strpos($url, '?') !== false ? '&' : '?';
                $url .= $separator . 'from=notification';
            }
            
            return response()->json([
                'success' => true,
                'url' => $url,
                'notification_id' => $id,
                'debug' => [
                    'original_url' => $data['url'] ?? null,
                    'processed_url' => $url
                ]
            ]);
        }
        
        return response()->json(['success' => false, 'message' => 'Notification not found'], 404);
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead()
    {
        $user = Auth::user();
        $user->unreadNotifications->markAsRead();
        
        return response()->json(['success' => true]);
    }

    /**
     * Get unread count
     */
    public function unreadCount()
    {
        $user = Auth::user();
        $count = $user->unreadNotifications()->count();
        
        return response()->json(['count' => $count]);
    }

    /**
     * Display all notifications page
     */
    public function all(Request $request)
    {
        $user = Auth::user();
        $perPage = 20;
        
        $notifications = $user->notifications()
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
        
        $unreadCount = $user->unreadNotifications()->count();
        
        return view('notifications.all', compact('notifications', 'unreadCount'));
    }
}
