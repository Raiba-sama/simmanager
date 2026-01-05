<?php

namespace App\Filament\Resources\EquipmentResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class HistoryRelationManager extends RelationManager
{
    protected static string $relationship = 'history';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('action')
                    ->label('Action')
                    ->options([
                        'created' => 'Créé',
                        'assigned' => 'Attribué',
                        'returned' => 'Retourné',
                        'transferred' => 'Transféré',
                        'status_changed' => 'Statut modifié',
                        'maintenance' => 'Maintenance',
                        'updated' => 'Modifié',
                    ])
                    ->required(),
                Forms\Components\Textarea::make('notes')
                    ->label('Notes')
                    ->rows(3),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                Tables\Columns\TextColumn::make('action')
                    ->label('Action')
                    ->formatStateUsing(fn (string $state): string => match($state) {
                        'created' => 'Créé',
                        'assigned' => 'Attribué',
                        'returned' => 'Retourné',
                        'transferred' => 'Transféré',
                        'status_changed' => 'Statut modifié',
                        'maintenance' => 'Maintenance',
                        'updated' => 'Modifié',
                        default => $state,
                    })
                    ->badge()
                    ->color(fn (string $state): string => match($state) {
                        'created' => 'success',
                        'assigned' => 'info',
                        'returned' => 'warning',
                        'transferred' => 'primary',
                        'status_changed' => 'gray',
                        'maintenance' => 'warning',
                        'updated' => 'gray',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('performer.name')
                    ->label('Effectué par')
                    ->searchable(),
                Tables\Columns\TextColumn::make('notes')
                    ->label('Notes')
                    ->limit(50)
                    ->toggleable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Date')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('action')
                    ->label('Action')
                    ->options([
                        'created' => 'Créé',
                        'assigned' => 'Attribué',
                        'returned' => 'Retourné',
                        'transferred' => 'Transféré',
                        'status_changed' => 'Statut modifié',
                        'maintenance' => 'Maintenance',
                        'updated' => 'Modifié',
                    ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->headerActions([
                //
            ])
            ->actions([
                //
            ])
            ->bulkActions([
                //
            ]);
    }
}

