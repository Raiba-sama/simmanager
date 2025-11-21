<?php

namespace App\Filament\Resources\SimResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class HistoriesRelationManager extends RelationManager
{
    protected static string $relationship = 'histories';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('action')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('action')
            ->columns([
                Tables\Columns\TextColumn::make('action')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'assigned' => 'success',
                        'unassigned' => 'warning',
                        'suspended' => 'danger',
                        default => 'info',
                    }),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Utilisateur'),
                Tables\Columns\TextColumn::make('user_matricule')
                    ->label('Matricule'),
                Tables\Columns\TextColumn::make('notes')
                    ->limit(50),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                //
            ])
            ->actions([
                //
            ])
            ->bulkActions([
                //
            ])
            ->defaultSort('created_at', 'desc');
    }
}

