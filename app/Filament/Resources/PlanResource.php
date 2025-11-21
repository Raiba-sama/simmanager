<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PlanResource\Pages;
use App\Filament\Resources\PlanResource\RelationManagers;
use App\Models\Plan;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PlanResource extends Resource
{
    protected static ?string $model = Plan::class;

    protected static ?string $navigationIcon = 'heroicon-o-credit-card';
    protected static ?string $navigationGroup = 'Gestion';
    protected static ?string $navigationLabel = 'Forfaits';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Nom du forfait')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Textarea::make('description')
                    ->label('Description')
                    ->maxLength(500)
                    ->rows(2),
                Forms\Components\TextInput::make('limite_credit')
                    ->label('Limite crédit (XOF)')
                    ->required()
                    ->numeric()
                    ->prefix('XOF')
                    ->default(0.00),
                Forms\Components\TextInput::make('limite_data')
                    ->label('Limite data (Go)')
                    ->required()
                    ->numeric()
                    ->suffix('Go')
                    ->default(0.00),
                Forms\Components\TextInput::make('monthly_cost')
                    ->label('Montant flotte (XOF)')
                    ->numeric()
                    ->prefix('XOF'),
                Forms\Components\Select::make('operator')
                    ->label('Opérateur')
                    ->options([
                        'Telma' => 'Telma',
                        'Orange' => 'Orange',
                        'Airtel' => 'Airtel',
                    ])
                    ->searchable(),
                Forms\Components\Toggle::make('active')
                    ->label('Actif')
                    ->default(true)
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nom')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('limite_credit')
                    ->label('Limite crédit')
                    ->formatStateUsing(fn ($state) => number_format((float) $state, 0, ',', ' ') . ' XOF')
                    ->sortable(),
                Tables\Columns\TextColumn::make('limite_data')
                    ->label('Limite data')
                    ->formatStateUsing(fn ($state) => $state > 0 ? number_format((float) $state, 1, ',', ' ') . ' Go' : '0 Go')
                    ->sortable(),
                Tables\Columns\TextColumn::make('monthly_cost')
                    ->label('Montant flotte')
                    ->formatStateUsing(fn ($state) => $state > 0 ? number_format((float) $state, 0, ',', ' ') . ' XOF' : '-')
                    ->sortable(),
                Tables\Columns\TextColumn::make('operator')
                    ->label('Opérateur')
                    ->searchable()
                    ->badge()
                    ->color(fn ($state) => match($state) {
                        'Telma' => 'success',
                        'Orange' => 'warning',
                        'Airtel' => 'info',
                        default => 'secondary',
                    }),
                Tables\Columns\IconColumn::make('active')
                    ->label('Actif')
                    ->boolean()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Créé le')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('active')
                    ->label('Actif')
                    ->placeholder('Tous')
                    ->trueLabel('Actifs uniquement')
                    ->falseLabel('Inactifs uniquement'),
                Tables\Filters\SelectFilter::make('operator')
                    ->label('Opérateur')
                    ->options([
                        'Telma' => 'Telma',
                        'Orange' => 'Orange',
                        'Airtel' => 'Airtel',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make(),
            ]);
    }
    
    public static function getRelations(): array
    {
        return [
            //
        ];
    }
    
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPlans::route('/'),
            'create' => Pages\CreatePlan::route('/create'),
            'edit' => Pages\EditPlan::route('/{record}/edit'),
        ];
    }    
}
