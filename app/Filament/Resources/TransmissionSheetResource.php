<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TransmissionSheetResource\Pages;
use App\Filament\Resources\TransmissionSheetResource\RelationManagers;
use App\Models\TransmissionSheet;
use App\Mail\TransmissionSheetsEmail;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Collection;

class TransmissionSheetResource extends Resource
{
    protected static ?string $model = TransmissionSheet::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationLabel = 'Bordereaux de transmission';
    protected static ?string $modelLabel = 'Bordereau de transmission';
    protected static ?string $pluralModelLabel = 'Bordereaux de transmission';
    protected static ?string $navigationGroup = 'Parc Informatique';
    protected static ?int $navigationSort = 5;

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
                        Forms\Components\TextInput::make('sheet_number')
                            ->label('Numéro de bordereau')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255)
                            ->default(fn () => 'BT-' . date('Ymd') . '-' . strtoupper(uniqid())),
                        Forms\Components\Select::make('type')
                            ->label('Type')
                            ->options([
                                'assignment' => 'Attribution',
                                'return' => 'Retour',
                                'transfer' => 'Transfert',
                            ])
                            ->required()
                            ->default('assignment')
                            ->reactive(),
                        Forms\Components\DatePicker::make('transmission_date')
                            ->label('Date de transmission')
                            ->required()
                            ->default(now())
                            ->displayFormat('d/m/Y'),
                        Forms\Components\Select::make('status')
                            ->label('Statut')
                            ->options([
                                'draft' => 'Brouillon',
                                'pending' => 'En attente',
                                'completed' => 'Complété',
                                'cancelled' => 'Annulé',
                            ])
                            ->required()
                            ->default('draft'),
                    ])
                    ->columns(2),
                Forms\Components\Section::make('De')
                    ->schema([
                        Forms\Components\Select::make('from_user_id')
                            ->label('Utilisateur')
                            ->relationship('fromUser', 'name')
                            ->searchable()
                            ->preload()
                            ->visible(fn (Forms\Get $get) => $get('type') !== 'assignment'),
                        Forms\Components\Select::make('from_agency_id')
                            ->label('Agence')
                            ->relationship('fromAgency', 'name')
                            ->searchable()
                            ->preload()
                            ->visible(fn (Forms\Get $get) => $get('type') !== 'assignment'),
                    ])
                    ->columns(2),
                Forms\Components\Section::make('Vers')
                    ->schema([
                        Forms\Components\Select::make('to_user_id')
                            ->label('Bénéficiaire (personne)')
                            ->relationship('toUser', 'name')
                            ->searchable()
                            ->preload()
                            ->visible(fn (Forms\Get $get) => $get('type') !== 'return')
                            ->helperText('Sélectionner un utilisateur si l\'attribution est à une personne.')
                            ->reactive(),
                        Forms\Components\Select::make('to_agency_id')
                            ->label('Agence destinataire')
                            ->relationship('toAgency', 'name')
                            ->searchable()
                            ->preload()
                            ->visible(fn (Forms\Get $get) => $get('type') !== 'return')
                            ->helperText('Pour les équipements réseau ou matériel attribué à une agence.')
                            ->reactive(),
                    ])
                    ->columns(2),
                Forms\Components\Section::make('Responsable / Signataire')
                    ->description('Personne qui réceptionnera et signera le bordereau. Obligatoire pour les attributions à une agence.')
                    ->schema([
                        Forms\Components\TextInput::make('recipient_name')
                            ->label('Nom du signataire')
                            ->maxLength(255)
                            ->helperText('Nom de la personne qui signe à réception (responsable agence, chef de zone, etc.).')
                            ->visible(fn (Forms\Get $get) => filled($get('to_agency_id')) && !filled($get('to_user_id'))),
                        Forms\Components\TextInput::make('recipient_fonction')
                            ->label('Fonction du signataire')
                            ->maxLength(255)
                            ->helperText('Ex: Chef d\'agence, Responsable IT, etc.')
                            ->visible(fn (Forms\Get $get) => filled($get('to_agency_id')) && !filled($get('to_user_id'))),
                    ])
                    ->columns(2)
                    ->visible(fn (Forms\Get $get) => $get('type') !== 'return'),
                Forms\Components\Section::make('Équipements')
                    ->description('Sélectionnez les équipements à inclure dans ce bordereau.')
                    ->schema([
                        Forms\Components\Repeater::make('equipment_items')
                            ->label('')
                            ->schema([
                                Forms\Components\Select::make('equipment_id')
                                    ->label('Équipement')
                                    ->options(function () {
                                        return \App\Models\Equipment::query()
                                            ->where('status', '!=', 'retired')
                                            ->with('equipmentType')
                                            ->get()
                                            ->mapWithKeys(fn ($eq) => [
                                                $eq->id => ($eq->asset_tag ?: 'Sans tag') .
                                                    ' — ' . ($eq->equipmentType?->name ?? '') .
                                                    ' ' . ($eq->brand ?? '') .
                                                    ' ' . ($eq->model ?? '') .
                                                    ($eq->serial_number ? " (SN: {$eq->serial_number})" : ''),
                                            ]);
                                    })
                                    ->searchable()
                                    ->required()
                                    ->reactive()
                                    ->afterStateUpdated(function ($state, Forms\Set $set) {
                                        if ($state) {
                                            $eq = \App\Models\Equipment::find($state);
                                            if ($eq) {
                                                $set('condition_at_transmission', $eq->condition ?? 'good');
                                            }
                                        }
                                    })
                                    ->columnSpan(2),
                                Forms\Components\Select::make('condition_at_transmission')
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
                                Forms\Components\TextInput::make('notes')
                                    ->label('Notes')
                                    ->maxLength(255),
                            ])
                            ->columns(4)
                            ->defaultItems(0)
                            ->addActionLabel('Ajouter un équipement')
                            ->reorderable(false)
                            ->afterStateHydrated(function (Forms\Components\Repeater $component, $record) {
                                if ($record && $record->exists) {
                                    $items = $record->items()->with('equipment')->get();
                                    $component->state(
                                        $items->map(fn ($item) => [
                                            'equipment_id' => $item->equipment_id,
                                            'condition_at_transmission' => $item->condition_at_transmission,
                                            'notes' => $item->notes,
                                        ])->toArray()
                                    );
                                }
                            }),
                    ])
                    ->collapsible(),
                Forms\Components\Section::make('Signature')
                    ->schema([
                        Forms\Components\Toggle::make('signed_by_recipient')
                            ->label('Signé par le destinataire')
                            ->default(false)
                            ->reactive(),
                        Forms\Components\DateTimePicker::make('signed_at')
                            ->label('Date de signature')
                            ->displayFormat('d/m/Y H:i')
                            ->visible(fn (Forms\Get $get) => $get('signed_by_recipient')),
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
                Tables\Columns\TextColumn::make('sheet_number')
                    ->label('N° bordereau')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('type')
                    ->label('Type')
                    ->formatStateUsing(fn (string $state): string => match($state) {
                        'assignment' => 'Attribution',
                        'return' => 'Retour',
                        'transfer' => 'Transfert',
                        default => $state,
                    })
                    ->badge()
                    ->color(fn (string $state): string => match($state) {
                        'assignment' => 'success',
                        'return' => 'warning',
                        'transfer' => 'info',
                        default => 'gray',
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('fromUser.name')
                    ->label('De (Utilisateur)')
                    ->default('-')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('fromAgency.name')
                    ->label('De (Agence)')
                    ->default('-')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('recipient_display')
                    ->label('Destinataire')
                    ->getStateUsing(function (TransmissionSheet $record) {
                        if ($record->toUser) {
                            return $record->toUser->full_name;
                        }
                        if ($record->toAgency) {
                            $label = $record->toAgency->name;
                            if ($record->recipient_name) {
                                $label .= ' — ' . $record->recipient_name;
                            }
                            return $label;
                        }
                        return '-';
                    })
                    ->description(function (TransmissionSheet $record) {
                        if ($record->toUser?->fonction) {
                            return $record->toUser->fonction;
                        }
                        if ($record->recipient_fonction) {
                            return $record->recipient_fonction;
                        }
                        return null;
                    })
                    ->searchable(query: function (Builder $query, string $search) {
                        return $query->where(function ($q) use ($search) {
                            $q->whereHas('toUser', fn ($u) => $u->where('name', 'like', "%{$search}%")->orWhere('first_name', 'like', "%{$search}%"))
                              ->orWhereHas('toAgency', fn ($a) => $a->where('name', 'like', "%{$search}%"))
                              ->orWhere('recipient_name', 'like', "%{$search}%");
                        });
                    })
                    ->icon(function (TransmissionSheet $record) {
                        return $record->toAgency && !$record->toUser
                            ? 'heroicon-m-building-office-2'
                            : 'heroicon-m-user';
                    }),
                Tables\Columns\TextColumn::make('transmission_date')
                    ->label('Date')
                    ->date('d/m/Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Statut')
                    ->formatStateUsing(fn (string $state): string => match($state) {
                        'draft' => 'Brouillon',
                        'pending' => 'En attente',
                        'completed' => 'Complété',
                        'cancelled' => 'Annulé',
                        default => $state,
                    })
                    ->badge()
                    ->color(fn (string $state): string => match($state) {
                        'draft' => 'gray',
                        'pending' => 'warning',
                        'completed' => 'success',
                        'cancelled' => 'danger',
                        default => 'gray',
                    })
                    ->sortable(),
                Tables\Columns\IconColumn::make('signed_by_recipient')
                    ->label('Signé')
                    ->boolean()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('items_count')
                    ->label('Nb équipements')
                    ->counts('items')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->label('Type')
                    ->options([
                        'assignment' => 'Attribution',
                        'return' => 'Retour',
                        'transfer' => 'Transfert',
                    ]),
                Tables\Filters\SelectFilter::make('status')
                    ->label('Statut')
                    ->options([
                        'draft' => 'Brouillon',
                        'pending' => 'En attente',
                        'completed' => 'Complété',
                        'cancelled' => 'Annulé',
                    ]),
                Tables\Filters\TernaryFilter::make('signed_by_recipient')
                    ->label('Signé'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('send_email')
                        ->label('Envoyer par email')
                        ->icon('heroicon-o-paper-airplane')
                        ->color('success')
                        ->form([
                            Forms\Components\TextInput::make('recipient_email')
                                ->label('Email du destinataire')
                                ->email()
                                ->required()
                                ->default(fn () => auth()->user()->email)
                                ->helperText('L\'email où envoyer les bordereaux'),
                            Forms\Components\Textarea::make('message')
                                ->label('Message (optionnel)')
                                ->rows(3)
                                ->placeholder('Message à inclure dans l\'email...'),
                        ])
                        ->action(function (Collection $records, array $data) {
                            try {
                                // Augmenter le temps d'exécution pour la génération des PDFs
                                set_time_limit(300); // 5 minutes
                                
                                $transmissionSheets = $records->load([
                                    'toUser',
                                    'fromUser',
                                    'toAgency',
                                    'fromAgency',
                                    'creator',
                                    'items.equipment.equipmentType'
                                ]);

                                Mail::to($data['recipient_email'])
                                    ->send(new TransmissionSheetsEmail(
                                        $transmissionSheets,
                                        $data['recipient_email'],
                                        $data['message'] ?? null
                                    ));

                                \Filament\Notifications\Notification::make()
                                    ->title('Email envoyé avec succès')
                                    ->body(count($transmissionSheets) . ' bordereau(x) envoyé(s) à ' . $data['recipient_email'])
                                    ->success()
                                    ->send();
                            } catch (\Illuminate\Mail\SendFailedException $e) {
                                $errorMessage = 'Erreur SMTP: ' . $e->getMessage();
                                if ($e->getPrevious()) {
                                    $errorMessage .= ' (' . $e->getPrevious()->getMessage() . ')';
                                }
                                
                                \Filament\Notifications\Notification::make()
                                    ->title('Erreur lors de l\'envoi de l\'email')
                                    ->body($errorMessage)
                                    ->danger()
                                    ->send();

                                \Log::error('Erreur envoi email bordereaux (SMTP)', [
                                    'error' => $e->getMessage(),
                                    'previous_error' => $e->getPrevious()?->getMessage(),
                                    'trace' => $e->getTraceAsString(),
                                    'recipient' => $data['recipient_email'] ?? null,
                                    'count' => $records->count(),
                                    'mail_config' => [
                                        'host' => config('mail.mailers.smtp.host'),
                                        'port' => config('mail.mailers.smtp.port'),
                                        'encryption' => config('mail.mailers.smtp.encryption'),
                                        'username' => config('mail.mailers.smtp.username') ? '***' : null,
                                    ],
                                ]);
                            } catch (\Exception $e) {
                                \Filament\Notifications\Notification::make()
                                    ->title('Erreur lors de l\'envoi de l\'email')
                                    ->body('Erreur: ' . $e->getMessage())
                                    ->danger()
                                    ->send();

                                \Log::error('Erreur envoi email bordereaux', [
                                    'error' => $e->getMessage(),
                                    'trace' => $e->getTraceAsString(),
                                    'recipient' => $data['recipient_email'] ?? null,
                                    'count' => $records->count(),
                                ]);
                            }
                        })
                        ->requiresConfirmation()
                        ->deselectRecordsAfterCompletion(),
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
            RelationManagers\ItemsRelationManager::class,
        ];
    }
    
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTransmissionSheets::route('/'),
            'create' => Pages\CreateTransmissionSheet::route('/create'),
            'edit' => Pages\EditTransmissionSheet::route('/{record}/edit'),
        ];
    }    
}
