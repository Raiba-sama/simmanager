<?php

namespace App\Notifications;

use App\Models\SimRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class RequestReminder extends Notification
{
    use Queueable;

    protected $simRequest;
    protected $daysPending;

    /**
     * Create a new notification instance.
     */
    public function __construct(SimRequest $simRequest, int $daysPending)
    {
        $this->simRequest = $simRequest;
        $this->daysPending = $daysPending;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $typeLabels = [
            'recuperation' => 'Récupération',
            'creation' => 'Création',
            'suspension' => 'Suspension',
            'desactivation' => 'Désactivation',
            'ajustement' => 'Ajustement',
        ];

        $urgencyLevel = $this->daysPending >= 7 ? 'urgent' : ($this->daysPending >= 5 ? 'high' : 'normal');
        $urgencyMessages = [
            'urgent' => "URGENT : Demande en attente depuis {$this->daysPending} jours",
            'high' => "Important : Demande en attente depuis {$this->daysPending} jours",
            'normal' => "Rappel : Demande en attente depuis {$this->daysPending} jours",
        ];

        $typeLabel = $typeLabels[$this->simRequest->request_type] ?? $this->simRequest->request_type;
        
        return [
            'type' => 'request_reminder',
            'title' => $urgencyMessages[$urgencyLevel],
            'message' => "La demande #{$this->simRequest->request_number} ({$typeLabel}) nécessite une action",
            'request_id' => $this->simRequest->id,
            'request_number' => $this->simRequest->request_number,
            'request_type' => $this->simRequest->request_type,
            'user_name' => $this->simRequest->user->full_name ?? 'Utilisateur inconnu',
            'days_pending' => $this->daysPending,
            'urgency_level' => $urgencyLevel,
            'url' => url()->route('sim-requests.show', $this->simRequest, false), // URL relative
            'icon' => 'bi-clock-history',
            'color' => $urgencyLevel === 'urgent' ? '#ef4444' : ($urgencyLevel === 'high' ? '#f59e0b' : '#3b82f6'),
        ];
    }
}
