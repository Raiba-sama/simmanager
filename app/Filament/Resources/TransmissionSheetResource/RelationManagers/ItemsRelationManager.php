<?php

namespace App\Filament\Resources\TransmissionSheetResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'items';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('equipment_id')
                    ->label('Équipement')
                    ->relationship('equipment', 'asset_tag', fn (Builder $query) => $query->where('status', '!=', 'retired'))
                    ->searchable(['asset_tag', 'serial_number', 'brand', 'model'])
                    ->preload()
                    ->required()
                    ->reactive()
                    ->afterStateUpdated(function ($state, Forms\Set $set) {
                        if ($state) {
                            $equipment = \App\Models\Equipment::find($state);
                            if ($equipment) {
                                $set('condition_at_transmission', $equipment->condition);
                            }
                        }
                    }),
                Forms\Components\TextInput::make('quantity')
                    ->label('Quantité')
                    ->numeric()
                    ->default(1)
                    ->required()
                    ->minValue(1),
                Forms\Components\Select::make('condition_at_transmission')
                    ->label('Condition lors de la transmission')
                    ->options([
                        'new' => 'Neuf',
                        'excellent' => 'Excellent',
                        'good' => 'Bon',
                        'fair' => 'Moyen',
                        'poor' => 'Mauvais',
                    ])
                    ->required()
                    ->default('good'),
                Forms\Components\Textarea::make('notes')
                    ->label('Notes')
                    ->rows(2),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                Tables\Columns\TextColumn::make('equipment.asset_tag')
                    ->label('Tag équipement')
                    ->searchable(),
                Tables\Columns\TextColumn::make('equipment.equipmentType.name')
                    ->label('Type')
                    ->searchable(),
                Tables\Columns\TextColumn::make('equipment.brand')
                    ->label('Marque')
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('equipment.model')
                    ->label('Modèle')
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('quantity')
                    ->label('Quantité')
                    ->sortable(),
                Tables\Columns\TextColumn::make('condition_at_transmission')
                    ->label('Condition')
                    ->formatStateUsing(fn (string $state): string => match($state) {
                        'new' => 'Neuf',
                        'excellent' => 'Excellent',
                        'good' => 'Bon',
                        'fair' => 'Moyen',
                        'poor' => 'Mauvais',
                        default => $state,
                    })
                    ->badge(),
            ])
            ->filters([
                //
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

