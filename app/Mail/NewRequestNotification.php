<?php

namespace App\Mail;

use App\Models\SimRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NewRequestNotification extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public SimRequest $request)
    {
    }

    public function build()
    {
        return $this->subject('Nouvelle demande de SIM - ' . $this->request->request_number)
            ->view('emails.new-request')
            ->with([
                'request' => $this->request,
                'user' => $this->request->user,
            ]);
    }
}

