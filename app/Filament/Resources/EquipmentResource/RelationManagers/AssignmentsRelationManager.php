<?php

namespace App\Filament\Resources\EquipmentResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AssignmentsRelationManager extends RelationManager
{
    protected static string $relationship = 'assignments';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('assigned_to_user_id')
                    ->label('Utilisateur')
                    ->relationship('assignedToUser', 'name')
                    ->searchable()
                    ->preload(),
                Forms\Components\Select::make('assigned_to_agency_id')
                    ->label('Agence')
                    ->relationship('assignedToAgency', 'name')
                    ->searchable()
                    ->preload(),
                Forms\Components\DateTimePicker::make('assigned_at')
                    ->label('Date d\'attribution')
                    ->required()
                    ->default(now()),
                Forms\Components\DateTimePicker::make('returned_at')
                    ->label('Date de retour'),
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
                Tables\Columns\TextColumn::make('assignedToUser.name')
                    ->label('Utilisateur')
                    ->default('-'),
                Tables\Columns\TextColumn::make('assignedToAgency.name')
                    ->label('Agence')
                    ->default('-'),
                Tables\Columns\TextColumn::make('assigned_at')
                    ->label('Attribué le')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
                Tables\Columns\TextColumn::make('returned_at')
                    ->label('Retourné le')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
                Tables\Columns\IconColumn::make('isActive')
                    ->label('Actif')
                    ->boolean()
                    ->getStateUsing(fn ($record) => is_null($record->returned_at)),
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

