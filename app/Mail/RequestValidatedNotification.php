<?php

namespace App\Mail;

use App\Models\SimRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class RequestValidatedNotification extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public SimRequest $request)
    {
    }

    public function build()
    {
        return $this->subject('Demande approuvée - ' . $this->request->request_number)
            ->view('emails.request-validated')
            ->with([
                'request' => $this->request,
                'validator' => $this->request->validator,
            ]);
    }
}

