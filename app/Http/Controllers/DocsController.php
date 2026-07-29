<?php

namespace App\Http\Controllers;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;

class DocsController extends Controller
{
    public function userGuide(): Response
    {
        $pdf = Pdf::loadView('docs.user-guide');
        $pdf->setOption('encoding', 'utf-8');
        $pdf->setOption('defaultFont', 'DejaVu Sans');
        $pdf->setPaper('a4', 'portrait');

        $filename = 'guide-utilisateur-simmanager-' . now()->format('Ymd') . '.pdf';

        return response()->streamDownload(fn () => print($pdf->output()), $filename, [
            'Content-Type' => 'application/pdf',
        ]);
    }

    public function adminGuide(): Response
    {
        abort_unless(auth()->user()?->isValidator(), 403);

        $pdf = Pdf::loadView('docs.admin-guide');
        $pdf->setOption('encoding', 'utf-8');
        $pdf->setOption('defaultFont', 'DejaVu Sans');
        $pdf->setPaper('a4', 'portrait');

        $filename = 'guide-administrateur-simmanager-' . now()->format('Ymd') . '.pdf';

        return response()->streamDownload(fn () => print($pdf->output()), $filename, [
            'Content-Type' => 'application/pdf',
        ]);
    }
}
