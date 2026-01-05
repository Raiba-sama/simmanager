<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EquipmentTypeResource\Pages;
use App\Filament\Resources\EquipmentTypeResource\RelationManagers;
use App\Models\EquipmentType;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class EquipmentTypeResource extends Resource
{
    protected static ?string $model = EquipmentType::class;

    protected static ?string $navigationIcon = 'heroicon-o-tag';
    protected static ?string $navigationLabel = 'Types d\'équipements';
    protected static ?string $modelLabel = 'Type d\'équipement';
    protected static ?string $pluralModelLabel = 'Types d\'équipements';
    protected static ?string $navigationGroup = 'Parc Informatique';
    protected static ?int $navigationSort = 1;

    public static function canAccess(): bool
    {
        return auth()->user()?->isAdmin() ?? false;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Nom')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('category')
                    ->label('Catégorie')
                    ->options([
                        'computer' => 'Ordinateur',
                        'peripheral' => 'Périphérique',
                        'network' => 'Réseau',
                        'accessory' => 'Accessoire',
                    ])
                    ->required()
                    ->default('computer'),
                Forms\Components\Textarea::make('description')
                    ->label('Description')
                    ->rows(3),
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
                Tables\Columns\TextColumn::make('category')
                    ->label('Catégorie')
                    ->formatStateUsing(fn (string $state): string => match($state) {
                        'computer' => 'Ordinateur',
                        'peripheral' => 'Périphérique',
                        'network' => 'Réseau',
                        'accessory' => 'Accessoire',
                        default => $state,
                    })
                    ->badge()
                    ->color(fn (string $state): string => match($state) {
                        'computer' => 'primary',
                        'peripheral' => 'success',
                        'network' => 'info',
                        'accessory' => 'warning',
                        default => 'gray',
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('equipment_count')
                    ->label('Nb équipements')
                    ->counts('equipment')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Créé le')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category')
                    ->label('Catégorie')
                    ->options([
                        'computer' => 'Ordinateur',
                        'peripheral' => 'Périphérique',
                        'network' => 'Réseau',
                        'accessory' => 'Accessoire',
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
            'index' => Pages\ListEquipmentTypes::route('/'),
            'create' => Pages\CreateEquipmentType::route('/create'),
            'edit' => Pages\EditEquipmentType::route('/{record}/edit'),
        ];
    }    
}
