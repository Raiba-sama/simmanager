<?php

namespace App\Mail;

use App\Models\TransmissionSheet;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;
use Barryvdh\DomPDF\Facade\Pdf;

class TransmissionSheetsEmail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Collection $transmissionSheets,
        public ?string $recipientEmail = null,
        public ?string $customMessage = null
    ) {
    }

    public function build()
    {
        // Augmenter le temps d'exécution pour la génération des PDFs
        set_time_limit(300); // 5 minutes
        
        $mail = $this->subject('Bordereaux de transmission d\'équipements')
            ->view('emails.transmission-sheets')
            ->with([
                'transmissionSheets' => $this->transmissionSheets,
                'count' => $this->transmissionSheets->count(),
                'customMessage' => $this->customMessage,
            ]);

        // Générer et attacher les PDFs pour chaque bordereau
        foreach ($this->transmissionSheets as $transmissionSheet) {
            try {
                $transmissionSheet->load([
                    'toUser', 
                    'fromUser', 
                    'toAgency', 
                    'fromAgency', 
                    'creator', 
                    'items.equipment.equipmentType'
                ]);

                $pdf = Pdf::loadView('transmission-sheets.pdf', compact('transmissionSheet'));
                $pdf->setOption('encoding', 'utf-8');
                $pdf->setOption('defaultFont', 'DejaVu Sans');
                $pdf->setOption('enable-remote', true);
                $pdf->setPaper('a4', 'portrait');

                $filename = 'bordereau_' . preg_replace('/[^a-zA-Z0-9_-]/', '_', $transmissionSheet->sheet_number ?? '') . '.pdf';

                $mail->attachData($pdf->output(), $filename, [
                    'mime' => 'application/pdf',
                ]);
            } catch (\Exception $e) {
                \Log::error('Erreur lors de la génération du PDF pour le bordereau', [
                    'transmission_sheet_id' => $transmissionSheet->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return $mail;
    }
}

