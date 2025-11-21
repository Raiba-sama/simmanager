<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SimRequestResource\Pages;
use App\Models\SimRequest;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\DB;

class SimRequestResource extends Resource
{
    protected static ?string $model = SimRequest::class;
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationGroup = 'Gestion';
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('request_number')
                    ->disabled()
                    ->dehydrated(false),
                Forms\Components\Select::make('user_id')
                    ->relationship('user', 'name')
                    ->required()
                    ->searchable()
                    ->preload(),
                Forms\Components\Select::make('sim_id')
                    ->relationship('sim', 'iccid')
                    ->searchable()
                    ->preload(),
                Forms\Components\TextInput::make('requested_iccid')
                    ->maxLength(255),
                Forms\Components\Select::make('request_type')
                    ->options([
                        'attribution' => 'Attribution',
                        'suspension' => 'Suspension',
                        'reactivation' => 'Réactivation',
                        'retour' => 'Retour',
                    ])
                    ->required(),
                Forms\Components\Textarea::make('motif')
                    ->maxLength(500),
                Forms\Components\Textarea::make('justification')
                    ->required()
                    ->maxLength(1000),
                Forms\Components\Select::make('priority')
                    ->options([
                        'low' => 'Basse',
                        'normal' => 'Normale',
                        'high' => 'Haute',
                        'urgent' => 'Urgente',
                    ])
                    ->default('normal'),
                Forms\Components\Select::make('status')
                    ->options([
                        'en_attente' => 'En attente',
                        'validee' => 'Validée',
                        'rejetee' => 'Rejetée',
                    ])
                    ->required(),
                Forms\Components\Select::make('validator_id')
                    ->relationship('validator', 'name')
                    ->searchable()
                    ->preload(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('request_number')
                    ->searchable()
                    ->sortable()
                    ->label('N° Demande'),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Utilisateur')
                    ->sortable(),
                Tables\Columns\TextColumn::make('request_type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'attribution' => 'success',
                        'suspension' => 'warning',
                        'reactivation' => 'info',
                        'retour' => 'danger',
                    }),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'en_attente' => 'warning',
                        'validee' => 'success',
                        'rejetee' => 'danger',
                    }),
                Tables\Columns\TextColumn::make('priority')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'urgent' => 'danger',
                        'high' => 'warning',
                        'normal' => 'info',
                        'low' => 'gray',
                    }),
                Tables\Columns\TextColumn::make('validator.name')
                    ->label('Validateur'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'en_attente' => 'En attente',
                        'validee' => 'Validée',
                        'rejetee' => 'Rejetée',
                    ]),
                Tables\Filters\SelectFilter::make('request_type')
                    ->options([
                        'attribution' => 'Attribution',
                        'suspension' => 'Suspension',
                        'reactivation' => 'Réactivation',
                        'retour' => 'Retour',
                    ]),
            ])
            ->actions([
                Tables\Actions\Action::make('approve')
                    ->label('Approuver')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->form([
                        Forms\Components\Textarea::make('notes')
                            ->maxLength(500),
                    ])
                    ->action(function (SimRequest $record, array $data) {
                        DB::beginTransaction();
                        try {
                            $record->update([
                                'status' => 'validee',
                                'validator_id' => auth()->id(),
                                'validated_at' => now(),
                                'updated_by' => auth()->id(),
                            ]);

                            if ($record->request_type === 'attribution' && $record->sim_id) {
                                $sim = $record->sim;
                                if ($sim && $sim->isLibre()) {
                                    $oldData = $sim->toArray();
                                    $sim->update([
                                        'status' => 'attribue',
                                        'assigned_to' => $record->user_id,
                                        'assigned_to_matricule' => $record->user->matricule,
                                        'assigned_at' => now(),
                                    ]);
                                    if ($sim->exists) {
                                        $sim->histories()->create([
                                            'action' => 'assigned',
                                            'user_id' => auth()->id(),
                                            'user_matricule' => auth()->user()->matricule,
                                            'request_id' => $record->id,
                                            'old_data' => $oldData,
                                            'new_data' => $sim->toArray(),
                                            'notes' => $data['notes'] ?? null,
                                        ]);
                                    }
                                }
                            }

                            \Filament\Notifications\Notification::make()
                                ->title('Demande approuvée')
                                ->success()
                                ->send();
                            DB::commit();
                        } catch (\Exception $e) {
                            DB::rollBack();
                            throw $e;
                        }
                    })
                    ->requiresConfirmation()
                    ->visible(fn (SimRequest $record) => $record->isEnAttente() && auth()->user()->canValidateRequests()),
                Tables\Actions\Action::make('reject')
                    ->label('Rejeter')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->form([
                        Forms\Components\Textarea::make('rejection_reason')
                            ->required()
                            ->maxLength(500)
                            ->label('Raison du rejet'),
                    ])
                    ->action(function (SimRequest $record, array $data) {
                        DB::beginTransaction();
                        try {
                            $record->update([
                                'status' => 'rejetee',
                                'validator_id' => auth()->id(),
                                'validated_at' => now(),
                                'rejection_reason' => $data['rejection_reason'],
                                'updated_by' => auth()->id(),
                            ]);
                            \Filament\Notifications\Notification::make()
                                ->title('Demande rejetée')
                                ->success()
                                ->send();
                            DB::commit();
                        } catch (\Exception $e) {
                            DB::rollBack();
                            throw $e;
                        }
                    })
                    ->requiresConfirmation()
                    ->visible(fn (SimRequest $record) => $record->isEnAttente() && auth()->user()->canValidateRequests()),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSimRequests::route('/'),
            'create' => Pages\CreateSimRequest::route('/create'),
            'edit' => Pages\EditSimRequest::route('/{record}/edit'),
        ];
    }
}

