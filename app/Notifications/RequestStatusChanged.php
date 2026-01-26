<?php

namespace App\Notifications;

use App\Models\SimRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class RequestStatusChanged extends Notification
{
    use Queueable;

    protected $simRequest;
    protected $oldStatus;
    protected $newStatus;

    /**
     * Create a new notification instance.
     */
    public function __construct(SimRequest $simRequest, $oldStatus, $newStatus)
    {
        $this->simRequest = $simRequest;
        $this->oldStatus = $oldStatus;
        $this->newStatus = $newStatus;
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
        $statusLabels = [
            'en_attente' => 'En attente',
            'validee' => 'Validée',
            'rejetee' => 'Rejetée',
            'demande_envoyee' => 'Demande envoyée',
            'pending' => 'Pending',
            'accepted' => 'Acceptée',
            'refused' => 'Refusée',
        ];

        $oldStatusLabel = $statusLabels[$this->oldStatus] ?? $this->oldStatus;
        $newStatusLabel = $statusLabels[$this->newStatus] ?? $this->newStatus;
        $nextSteps = [
            'accepted' => 'Bordereau disponible',
            'refused' => 'Corriger et soumettre à nouveau',
            'pending' => 'En attente opérateur',
            'demande_envoyee' => 'En attente opérateur',
        ];

        return [
            'type' => 'request_status_changed',
            'title' => 'Statut de demande modifié',
            'message' => "Le statut de la demande #{$this->simRequest->request_number} est passé de \"{$oldStatusLabel}\" à \"{$newStatusLabel}\".",
            'request_id' => $this->simRequest->id,
            'request_number' => $this->simRequest->request_number,
            'old_status' => $this->oldStatus,
            'new_status' => $this->newStatus,
            'next_step' => $nextSteps[$this->newStatus] ?? null,
            'url' => url()->route('sim-requests.show', $this->simRequest, false), // URL relative
            'icon' => 'bi-arrow-repeat',
            'color' => '#06b6d4',
        ];
    }
}
