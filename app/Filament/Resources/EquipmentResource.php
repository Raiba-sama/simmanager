<?php

namespace App\Filament\Resources;

use App\Exports\EquipmentInventoryExport;
use App\Filament\Resources\EquipmentResource\Pages;
use App\Filament\Resources\EquipmentResource\RelationManagers;
use App\Models\Equipment;
use App\Models\EquipmentDischarge;
use App\Models\User;
use Maatwebsite\Excel\Facades\Excel;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;

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
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Date de création')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
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
                        
                        try {
                            // Utiliser loadView directement avec options DomPDF
                            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('transmission-sheets.pdf', compact('transmissionSheet'));
                            $pdf->setOption('encoding', 'utf-8');
                            $pdf->setOption('defaultFont', 'DejaVu Sans');
                            $pdf->setOption('enable-remote', true);
                            $pdf->setPaper('a4', 'portrait');
                            
                            $filename = 'bordereau_' . preg_replace('/[^a-zA-Z0-9_-]/', '_', $transmissionSheet->sheet_number ?? '') . '.pdf';
                            
                            return response()->streamDownload(function () use ($pdf) {
                                echo $pdf->output();
                            }, $filename, [
                                'Content-Type' => 'application/pdf',
                            ]);
                        } catch (\Exception $e) {
                            \Filament\Notifications\Notification::make()
                                ->title('Erreur lors de la génération du PDF')
                                ->body('Erreur: ' . $e->getMessage())
                                ->danger()
                                ->send();
                            
                            \Log::error('Erreur génération PDF bordereau', [
                                'error' => $e->getMessage(),
                                'trace' => $e->getTraceAsString(),
                                'transmission_sheet_id' => $transmissionSheet->id ?? null,
                            ]);
                            
                            return null;
                        }
                    }),
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkAction::make('dischargePdf')
                    ->label('Décharge (PDF)')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('primary')
                    ->form([
                        Forms\Components\Select::make('user_id')
                            ->label('Collaborateur')
                            ->options(fn () => User::query()->where('active', true)->orderBy('name')->pluck('name', 'id'))
                            ->searchable()
                            ->preload()
                            ->required(),
                        Forms\Components\Select::make('reason')
                            ->label('Motif')
                            ->options([
                                'depart' => 'Départ / fin de contrat',
                                'remplacement' => 'Remplacement',
                                'nouvelle_attribution' => 'Nouvelle attribution',
                            ])
                            ->required(),
                        Forms\Components\DatePicker::make('effective_date')
                            ->label('Date effective')
                            ->default(now())
                            ->displayFormat('d/m/Y'),
                        Forms\Components\Textarea::make('notes')
                            ->label('Observations')
                            ->rows(3),
                    ])
                    ->action(function (Tables\Contracts\HasTable $livewire, array $records, array $data) {
                        $equipment = Equipment::query()
                            ->whereIn('id', $records)
                            ->with([
                                'equipmentType',
                                'currentAssignment.assignedToUser',
                                'currentAssignment.assignedToAgency',
                            ])
                            ->orderBy('id')
                            ->get();

                        $user = User::find($data['user_id']);
                        $generatedBy = auth()->user();
                        $reasonLabel = [
                            'depart' => 'Départ / fin de contrat',
                            'remplacement' => 'Remplacement',
                            'nouvelle_attribution' => 'Nouvelle attribution',
                        ][$data['reason']] ?? $data['reason'];

                        $equipmentItems = $equipment->map(function (Equipment $e) {
                            return [
                                'id' => $e->id,
                                'asset_tag' => $e->asset_tag,
                                'type' => $e->equipmentType?->name,
                                'brand' => $e->brand,
                                'model' => $e->model,
                                'serial_number' => $e->serial_number,
                                'condition' => $e->condition_label ?? $e->condition,
                                'status' => $e->status_label ?? $e->status,
                                'assignee' => $e->currentAssignment?->assignee?->full_name
                                    ?? $e->currentAssignment?->assignee?->name
                                    ?? null,
                            ];
                        })->values()->all();

                        $dischargeNumber = 'DEC-' . now()->format('Ymd') . '-' . strtoupper(Str::random(6));

                        // Tenter d'archiver en base (si DB indisponible, on génère quand même le PDF)
                        try {
                            EquipmentDischarge::create([
                                'discharge_number' => $dischargeNumber,
                                'user_id' => $user?->id,
                                'generated_by' => $generatedBy?->id,
                                'reason' => $data['reason'],
                                'effective_date' => $data['effective_date'] ?? null,
                                'notes' => $data['notes'] ?? null,
                                'equipment_snapshot' => [
                                    'equipment_ids' => $equipment->pluck('id')->values()->all(),
                                    'items' => $equipmentItems,
                                ],
                            ]);
                        } catch (\Throwable $e) {
                            \Log::warning('Décharge: archivage DB impossible, PDF généré sans archive', [
                                'error' => $e->getMessage(),
                            ]);
                        }

                        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('equipment.discharge-pdf', [
                            'dischargeNumber' => $dischargeNumber,
                            'generatedAt' => now(),
                            'effectiveDate' => $data['effective_date'] ?? null,
                            'reasonLabel' => $reasonLabel,
                            'notes' => $data['notes'] ?? null,
                            'user' => $user,
                            'generatedBy' => $generatedBy,
                            'equipmentItems' => $equipmentItems,
                        ]);
                        $pdf->setOption('encoding', 'utf-8');
                        $pdf->setOption('defaultFont', 'DejaVu Sans');
                        $pdf->setPaper('a4', 'portrait');

                        $filename = 'decharge_' . $dischargeNumber . '.pdf';
                        return response()->streamDownload(function () use ($pdf) {
                            echo $pdf->output();
                        }, $filename, ['Content-Type' => 'application/pdf']);
                    })
                    ->successNotificationTitle('Décharge générée.'),
                Tables\Actions\BulkAction::make('exportSelected')
                    ->label('Exporter la sélection')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success')
                    ->action(function (Tables\Contracts\HasTable $livewire, array $records): \Symfony\Component\HttpFoundation\BinaryFileResponse {
                        $equipment = Equipment::query()
                            ->whereIn('id', $records)
                            ->with([
                                'equipmentType',
                                'assignments' => fn ($q) => $q->whereNull('returned_at')->with(['assignedToUser', 'assignedToAgency']),
                            ])
                            ->orderBy('id')
                            ->get();
                        $filename = 'equipements_selection_' . now()->format('Y-m-d_His') . '.xlsx';
                        return Excel::download(new EquipmentInventoryExport($equipment), $filename);
                    })
                    ->successNotificationTitle('Export Excel généré.'),
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
            RelationManagers\RepairSendoutsRelationManager::class,
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
    
    /**
     * Nettoie une chaîne UTF-8 en supprimant les caractères malformés
     */
    protected static function cleanUtf8String(?string $string): string
    {
        if (empty($string)) {
            return '';
        }
        
        // D'abord, essayer de réparer avec iconv (ignore les caractères invalides)
        $string = @iconv('UTF-8', 'UTF-8//IGNORE//TRANSLIT', $string);
        if ($string === false) {
            $string = '';
        }
        
        // Nettoyer les caractères de contrôle
        $string = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u', '', $string);
        
        // Supprimer les séquences UTF-8 invalides
        $string = preg_replace('/[\x{FFFE}\x{FFFF}]/u', '', $string);
        
        // Vérifier et réparer l'encodage
        if (!mb_check_encoding($string, 'UTF-8')) {
            // Essayer de convertir depuis différents encodages
            $encodings = ['ISO-8859-1', 'Windows-1252', 'Windows-1251'];
            foreach ($encodings as $encoding) {
                $converted = @mb_convert_encoding($string, 'UTF-8', $encoding);
                if ($converted !== false && mb_check_encoding($converted, 'UTF-8')) {
                    $string = $converted;
                    break;
                }
            }
            
            // Si toujours invalide, utiliser iconv avec IGNORE
            if (!mb_check_encoding($string, 'UTF-8')) {
                $string = @iconv('UTF-8', 'UTF-8//IGNORE', $string);
                if ($string === false) {
                    $string = '';
                }
            }
        }
        
        return $string;
    }
    
    /**
     * Nettoie récursivement les données d'un modèle pour l'encodage UTF-8
     */
    protected static function cleanUtf8Data($data)
    {
        if (is_string($data)) {
            return static::cleanUtf8String($data);
        }
        
        if (is_array($data)) {
            return array_map([static::class, 'cleanUtf8Data'], $data);
        }
        
        if (is_object($data)) {
            // Si c'est un modèle Eloquent, nettoyer les attributs
            if (method_exists($data, 'getAttributes')) {
                $attributes = $data->getAttributes();
                foreach ($attributes as $key => $value) {
                    if (is_string($value)) {
                        $data->setAttribute($key, static::cleanUtf8String($value));
                    } elseif (is_array($value)) {
                        $data->setAttribute($key, static::cleanUtf8Data($value));
                    }
                }
                
                // Nettoyer aussi les relations chargées
                foreach ($data->getRelations() as $relationName => $relation) {
                    if (is_object($relation)) {
                        $data->setRelation($relationName, static::cleanUtf8Data($relation));
                    } elseif (is_array($relation) || $relation instanceof \Illuminate\Support\Collection) {
                        $cleaned = [];
                        foreach ($relation as $item) {
                            $cleaned[] = static::cleanUtf8Data($item);
                        }
                        $data->setRelation($relationName, is_array($relation) ? $cleaned : collect($cleaned));
                    }
                }
            }
            
            return $data;
        }
        
        return $data;
    }
}
