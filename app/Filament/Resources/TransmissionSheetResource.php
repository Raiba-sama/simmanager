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
                            ->label('Utilisateur')
                            ->relationship('toUser', 'name')
                            ->searchable()
                            ->preload()
                            ->visible(fn (Forms\Get $get) => $get('type') !== 'return'),
                        Forms\Components\Select::make('to_agency_id')
                            ->label('Agence')
                            ->relationship('toAgency', 'name')
                            ->searchable()
                            ->preload()
                            ->visible(fn (Forms\Get $get) => $get('type') !== 'return'),
                    ])
                    ->columns(2),
                Forms\Components\Section::make('Signature')
                    ->schema([
                        Forms\Components\Toggle::make('signed_by_recipient')
                            ->label('Signé par le destinataire')
                            ->default(false),
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
                Tables\Columns\TextColumn::make('toUser.name')
                    ->label('Vers (Utilisateur)')
                    ->default('-')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('toAgency.name')
                    ->label('Vers (Agence)')
                    ->default('-')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
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
