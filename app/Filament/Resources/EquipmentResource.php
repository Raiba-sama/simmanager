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
                            ->default('available')
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set) {
                                if ($state !== 'assigned') {
                                    $set('assigned_to_user_id', null);
                                    $set('create_new_user', false);
                                }
                            }),
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
                Forms\Components\Section::make('Attribution')
                    ->schema([
                        Forms\Components\Toggle::make('create_new_user')
                            ->label('Créer un nouvel utilisateur')
                            ->reactive()
                            ->visible(fn ($get) => $get('status') === 'assigned')
                            ->afterStateUpdated(function ($state, callable $set) {
                                if ($state) {
                                    $set('assigned_to_user_id', null);
                                }
                            }),
                        Forms\Components\Select::make('assigned_to_user_id')
                            ->label('Bénéficiaire')
                            ->options(\App\Models\User::orderBy('name')->get()->mapWithKeys(fn ($user) => [$user->id => "{$user->name} ({$user->matricule})"]))
                            ->searchable()
                            ->getSearchResultsUsing(fn (string $search) => \App\Models\User::where('name', 'like', "%{$search}%")
                                ->orWhere('matricule', 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%")
                                ->limit(50)
                                ->get()
                                ->mapWithKeys(fn ($user) => [$user->id => "{$user->name} ({$user->matricule})"]))
                            ->getOptionLabelUsing(fn ($value): ?string => \App\Models\User::find($value)?->name . ' (' . \App\Models\User::find($value)?->matricule . ')')
                            ->preload()
                            ->visible(fn ($get) => $get('status') === 'assigned' && !$get('create_new_user'))
                            ->required(fn ($get) => $get('status') === 'assigned' && !$get('create_new_user')),
                        Forms\Components\TextInput::make('new_user_matricule')
                            ->label('Matricule')
                            ->required(fn ($get) => $get('status') === 'assigned' && $get('create_new_user'))
                            ->visible(fn ($get) => $get('status') === 'assigned' && $get('create_new_user'))
                            ->unique('users', 'matricule')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('new_user_name')
                            ->label('Nom')
                            ->required(fn ($get) => $get('status') === 'assigned' && $get('create_new_user'))
                            ->visible(fn ($get) => $get('status') === 'assigned' && $get('create_new_user'))
                            ->maxLength(255),
                        Forms\Components\TextInput::make('new_user_first_name')
                            ->label('Prénom')
                            ->visible(fn ($get) => $get('status') === 'assigned' && $get('create_new_user'))
                            ->maxLength(255),
                        Forms\Components\TextInput::make('new_user_email')
                            ->label('Email')
                            ->email()
                            ->required(fn ($get) => $get('status') === 'assigned' && $get('create_new_user'))
                            ->visible(fn ($get) => $get('status') === 'assigned' && $get('create_new_user'))
                            ->unique('users', 'email')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('new_user_fonction')
                            ->label('Fonction')
                            ->visible(fn ($get) => $get('status') === 'assigned' && $get('create_new_user'))
                            ->maxLength(255),
                        Forms\Components\DatePicker::make('assignment_date')
                            ->label('Date d\'attribution')
                            ->default(now())
                            ->displayFormat('d/m/Y')
                            ->visible(fn ($get) => $get('status') === 'assigned'),
                        Forms\Components\Textarea::make('assignment_notes')
                            ->label('Notes d\'attribution')
                            ->rows(2)
                            ->visible(fn ($get) => $get('status') === 'assigned'),
                    ])
                    ->visible(fn ($get) => $get('status') === 'assigned')
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
                Tables\Actions\Action::make('downloadTransmissionSheet')
                    ->label('Bordereau')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('success')
                    ->visible(fn (Equipment $record) => $record->currentAssignment && $record->currentAssignment->transmissionSheet)
                    ->action(function (Equipment $record) {
                        $assignment = $record->currentAssignment;
                        if (!$assignment || !$assignment->transmissionSheet) {
                            \Filament\Notifications\Notification::make()
                                ->title('Aucun bordereau trouvé')
                                ->warning()
                                ->send();
                            return;
                        }
                        
                        $transmissionSheet = $assignment->transmissionSheet->load(['toUser', 'fromUser', 'toAgency', 'fromAgency', 'creator', 'items.equipment.equipmentType']);
                        
                        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('transmission-sheets.pdf', compact('transmissionSheet'));
                        return $pdf->download('bordereau_' . $transmissionSheet->sheet_number . '.pdf');
                    }),
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
