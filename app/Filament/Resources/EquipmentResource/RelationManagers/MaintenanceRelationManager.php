<?php

namespace App\Filament\Resources\EquipmentResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class MaintenanceRelationManager extends RelationManager
{
    protected static string $relationship = 'maintenance';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('maintenance_type')
                    ->label('Type de maintenance')
                    ->options([
                        'repair' => 'Réparation',
                        'upgrade' => 'Mise à niveau',
                        'cleaning' => 'Nettoyage',
                        'inspection' => 'Inspection',
                    ])
                    ->required()
                    ->default('repair'),
                Forms\Components\Textarea::make('description')
                    ->label('Description')
                    ->required()
                    ->rows(3),
                Forms\Components\TextInput::make('cost')
                    ->label('Coût')
                    ->numeric()
                    ->prefix('Ar')
                    ->step(0.01),
                Forms\Components\TextInput::make('performed_by')
                    ->label('Effectué par')
                    ->maxLength(255),
                Forms\Components\DateTimePicker::make('performed_at')
                    ->label('Date d\'intervention')
                    ->required()
                    ->default(now()),
                Forms\Components\DatePicker::make('next_maintenance_due')
                    ->label('Prochaine maintenance prévue')
                    ->displayFormat('d/m/Y'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                Tables\Columns\TextColumn::make('maintenance_type')
                    ->label('Type')
                    ->formatStateUsing(fn (string $state): string => match($state) {
                        'repair' => 'Réparation',
                        'upgrade' => 'Mise à niveau',
                        'cleaning' => 'Nettoyage',
                        'inspection' => 'Inspection',
                        default => $state,
                    })
                    ->badge(),
                Tables\Columns\TextColumn::make('description')
                    ->label('Description')
                    ->limit(50),
                Tables\Columns\TextColumn::make('cost')
                    ->label('Coût')
                    ->formatStateUsing(fn ($state) => $state ? number_format((float) $state, 0, ',', ' ') . ' Ar' : '-')
                    ->sortable(),
                Tables\Columns\TextColumn::make('performed_by')
                    ->label('Effectué par')
                    ->searchable(),
                Tables\Columns\TextColumn::make('performed_at')
                    ->label('Date')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('maintenance_type')
                    ->label('Type')
                    ->options([
                        'repair' => 'Réparation',
                        'upgrade' => 'Mise à niveau',
                        'cleaning' => 'Nettoyage',
                        'inspection' => 'Inspection',
                    ]),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}

