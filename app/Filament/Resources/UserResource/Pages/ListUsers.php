<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('syncFromWebhook')
                ->label('Synchroniser depuis le webhook')
                ->url(route('sync-users.form'))
                ->openUrlInNewTab()
                ->icon('heroicon-o-cloud-arrow-down')
                ->color('primary')
                ->tooltip('Synchroniser les utilisateurs depuis l\'application tierce (nouveaux comptes créés en admin).'),
            Actions\CreateAction::make(),
        ];
    }
}

