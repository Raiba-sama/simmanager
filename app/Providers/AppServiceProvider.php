<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\Mailer\Transport\Smtp\EsmtpTransport;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Configuration SSL pour SMTP si nécessaire
        if (config('mail.mailers.smtp.transport') === 'smtp') {
            $this->configureSmtpSsl();
        }
    }

    /**
     * Configure SSL options for SMTP transport
     */
    protected function configureSmtpSsl(): void
    {
        // Vérifier si on doit désactiver la vérification SSL
        $verifyPeer = filter_var(config('mail.mailers.smtp.verify_peer', true), FILTER_VALIDATE_BOOLEAN);
        $verifyPeerName = filter_var(config('mail.mailers.smtp.verify_peer_name', true), FILTER_VALIDATE_BOOLEAN);
        $allowSelfSigned = filter_var(config('mail.mailers.smtp.allow_self_signed', false), FILTER_VALIDATE_BOOLEAN);

        // Toujours configurer le stream context pour les connexions SSL
        $streamContextOptions = [
            'ssl' => [
                'verify_peer' => $verifyPeer,
                'verify_peer_name' => $verifyPeerName,
                'allow_self_signed' => $allowSelfSigned,
            ],
        ];

        // Configurer le stream context global pour les connexions SMTP
        stream_context_set_default($streamContextOptions);
    }
}
