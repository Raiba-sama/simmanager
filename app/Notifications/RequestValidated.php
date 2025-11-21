<?php

namespace App\Notifications;

use App\Models\SimRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class RequestValidated extends Notification
{
    use Queueable;

    protected $simRequest;

    /**
     * Create a new notification instance.
     */
    public function __construct(SimRequest $simRequest)
    {
        $this->simRequest = $simRequest;
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

        $typeLabel = $typeLabels[$this->simRequest->request_type] ?? $this->simRequest->request_type;

        return [
            'type' => 'request_validated',
            'title' => 'Demande validée',
            'message' => "Votre demande de {$typeLabel} (#{$this->simRequest->request_number}) a été validée",
            'request_id' => $this->simRequest->id,
            'request_number' => $this->simRequest->request_number,
            'request_type' => $this->simRequest->request_type,
            'validator_name' => $this->simRequest->validator->full_name ?? 'Validateur',
            'url' => url()->route('sim-requests.show', $this->simRequest, false), // URL relative
            'icon' => 'bi-check-circle',
            'color' => '#10b981',
        ];
    }
}
