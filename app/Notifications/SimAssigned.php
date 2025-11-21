<?php

namespace App\Notifications;

use App\Models\Sim;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class SimAssigned extends Notification
{
    use Queueable;

    protected $sim;

    /**
     * Create a new notification instance.
     */
    public function __construct(Sim $sim)
    {
        $this->sim = $sim;
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
        return [
            'type' => 'sim_assigned',
            'title' => 'SIM attribuée',
            'message' => "Une SIM (ICCID: {$this->sim->iccid}) vous a été attribuée",
            'sim_id' => $this->sim->id,
            'iccid' => $this->sim->iccid,
            'phone_number' => $this->sim->phone_number,
            'url' => url()->route('sims.show', $this->sim, false), // URL relative
            'icon' => 'bi-phone',
            'color' => '#10b981',
        ];
    }
}
