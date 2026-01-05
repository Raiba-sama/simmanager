<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EquipmentResource\Pages;
use App\Filament\Resources\EquipmentResource\RelationManagers;
use App\Models\Equipment;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class EquipmentResource extends Resource
{
    protected static ?string $model = Equipment::class;

    protected static ?string $navigationIcon = 'heroicon-o-computer-desktop';
    protected static ?string $navigationLabel = 'Équipements';
    protected static ?string $modelLabel = 'Équipement';
    protected static ?string $pluralModelLabel = 'Équipements';
    protected static ?string $navigationGroup = 'Parc Informatique';
    protected static ?int $navigationSort = 4;

    public static function canAccess(): bool
    {
        return auth()->user()?->isAdmin() ?? false;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informations générales')
                    ->schema([
                        Forms\Components\Select::make('equipment_type_id')
                            ->label('Type d\'équipement')
                            ->relationship('equipmentType', 'name')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->reactive(),
                        Forms\Components\TextInput::make('brand')
                            ->label('Marque')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('model')
                            ->label('Modèle')
                            ->maxLength(255),
                    ])
                    ->columns(3),
                Forms\Components\Section::make('Identifiants')
                    ->schema([
                        Forms\Components\TextInput::make('serial_number')
                            ->label('Numéro de série')
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),
                        Forms\Components\TextInput::make('asset_tag')
                            ->label('Tag d\'actif')
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),
                        Forms\Components\TextInput::make('mac_address')
                            ->label('Adresse MAC')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('ip_address')
                            ->label('Adresse IP')
                            ->ip()
                            ->maxLength(255),
                    ])
                    ->columns(2),
                Forms\Components\Section::make('Achat et garantie')
                    ->schema([
                        Forms\Components\DatePicker::make('purchase_date')
                            ->label('Date d\'achat')
                            ->displayFormat('d/m/Y'),
                        Forms\Components\TextInput::make('purchase_price')
                            ->label('Prix d\'achat')
                            ->numeric()
                            ->prefix('Ar')
                            ->step(0.01),
                        Forms\Components\DatePicker::make('warranty_expires_at')
                            ->label('Garantie expire le')
                            ->displayFormat('d/m/Y'),
                    ])
                    ->columns(3),
                Forms\Components\Section::make('État')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->label('Statut')
                            ->options([
                                'available' => 'Disponible',
                                'assigned' => 'Attribué',
                                'maintenance' => 'En maintenance',
                                'retired' => 'Retiré',
                                'lost' => 'Perdu',
                                'damaged' => 'Endommagé',
                            ])
                            ->required()
                            ->default('available'),
                        Forms\Components\Select::make('condition')
                            ->label('Condition')
                            ->options([
                                'new' => 'Neuf',
                                'excellent' => 'Excellent',
                                'good' => 'Bon',
                                'fair' => 'Moyen',
                                'poor' => 'Mauvais',
                            ])
                            ->required()
                            ->default('good'),
                    ])
                    ->columns(2),
                Forms\Components\Section::make('Spécifications')
                    ->schema([
                        Forms\Components\KeyValue::make('specifications')
                            ->label('Spécifications')
                            ->keyLabel('Clé')
                            ->valueLabel('Valeur'),
                    ])
                    ->collapsible(),
                Forms\Components\Textarea::make('notes')
                    ->label('Notes')
                    ->rows(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('asset_tag')
                    ->label('Tag')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('equipmentType.name')
                    ->label('Type')
                    ->sortable(),
                Tables\Columns\TextColumn::make('brand')
                    ->label('Marque')
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('model')
                    ->label('Modèle')
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('serial_number')
                    ->label('N° série')
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Statut')
                    ->formatStateUsing(fn (string $state): string => match($state) {
                        'available' => 'Disponible',
                        'assigned' => 'Attribué',
                        'maintenance' => 'En maintenance',
                        'retired' => 'Retiré',
                        'lost' => 'Perdu',
                        'damaged' => 'Endommagé',
                        default => $state,
                    })
                    ->badge()
                    ->color(fn (string $state): string => match($state) {
                        'available' => 'success',
                        'assigned' => 'info',
                        'maintenance' => 'warning',
                        'retired' => 'gray',
                        'lost' => 'danger',
                        'damaged' => 'danger',
                        default => 'gray',
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('condition')
                    ->label('Condition')
                    ->formatStateUsing(fn (string $state): string => match($state) {
                        'new' => 'Neuf',
                        'excellent' => 'Excellent',
                        'good' => 'Bon',
                        'fair' => 'Moyen',
                        'poor' => 'Mauvais',
                        default => $state,
                    })
                    ->badge()
                    ->toggleable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('equipment_type_id')
                    ->label('Type')
                    ->relationship('equipmentType', 'name'),
                Tables\Filters\SelectFilter::make('status')
                    ->label('Statut')
                    ->options([
                        'available' => 'Disponible',
                        'assigned' => 'Attribué',
                        'maintenance' => 'En maintenance',
                        'retired' => 'Retiré',
                        'lost' => 'Perdu',
                        'damaged' => 'Endommagé',
                    ]),
                Tables\Filters\SelectFilter::make('condition')
                    ->label('Condition')
                    ->options([
                        'new' => 'Neuf',
                        'excellent' => 'Excellent',
                        'good' => 'Bon',
                        'fair' => 'Moyen',
                        'poor' => 'Mauvais',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
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
            RelationManagers\AssignmentsRelationManager::class,
            RelationManagers\MaintenanceRelationManager::class,
            RelationManagers\HistoryRelationManager::class,
        ];
    }
    
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEquipment::route('/'),
            'create' => Pages\CreateEquipment::route('/create'),
            'edit' => Pages\EditEquipment::route('/{record}/edit'),
        ];
    }    
}
