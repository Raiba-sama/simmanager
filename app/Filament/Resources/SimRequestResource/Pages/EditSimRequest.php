<?php

namespace App\Filament\Resources\SimRequestResource\Pages;

use App\Filament\Resources\SimRequestResource;
use App\Http\Controllers\SimRequestController;
use App\Models\SimRequest;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Http\RedirectResponse;

class EditSimRequest extends EditRecord
{
    protected static string $resource = SimRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('resendWebhook')
                ->label('Renvoyer au webhook')
                ->icon('heroicon-o-paper-airplane')
                ->color('success')
                ->visible(fn (SimRequest $record): bool => auth()->user()?->isAdmin() === true
                    && !$record->isRejetee()
                    && ($record->sim_id || $record->phone_number))
                ->requiresConfirmation()
                ->modalHeading('Renvoyer la demande au webhook')
                ->modalDescription('La demande sera renvoyée au webhook avec la carte SIM actuellement sélectionnée. Enregistrez d\'abord les modifications (ex. changement de carte SIM) si nécessaire.')
                ->modalSubmitActionLabel('Renvoyer')
                ->action(function (SimRequest $record) {
                    try {
                        $response = app(SimRequestController::class)->submitToWebhook($record);
                        if ($response instanceof RedirectResponse) {
                            $session = $response->getSession();
                            $success = $session->get('success');
                            $error = $session->get('error');
                            if ($success) {
                                Notification::make()
                                    ->title('Demande renvoyée au webhook')
                                    ->body($success)
                                    ->success()
                                    ->send();
                                $this->record->refresh();
                            } elseif ($error) {
                                Notification::make()
                                    ->title('Erreur')
                                    ->body($error)
                                    ->danger()
                                    ->send();
                            }
                        }
                    } catch (\Throwable $e) {
                        Notification::make()
                            ->title('Erreur lors de l\'envoi au webhook')
                            ->body($e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),
            Actions\DeleteAction::make(),
        ];
    }
}

