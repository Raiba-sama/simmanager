<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SimResource\Pages;
use App\Filament\Resources\SimResource\RelationManagers\HistoriesRelationManager;
use App\Models\Sim;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class SimResource extends Resource
{
    protected static ?string $model = Sim::class;
    protected static ?string $navigationIcon = 'heroicon-o-device-phone-mobile';
    protected static ?string $navigationGroup = 'Gestion';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('iccid')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255)
                    ->label('ICCID'),
                Forms\Components\TextInput::make('phone_number')
                    ->maxLength(255)
                    ->label('Numéro de téléphone'),
                Forms\Components\Select::make('status')
                    ->options([
                        'libre' => 'Libre',
                        'attribue' => 'Attribuée',
                        'suspendu' => 'Suspendue',
                    ])
                    ->required(),
                Forms\Components\TextInput::make('operator')
                    ->maxLength(255)
                    ->label('Opérateur'),
                Forms\Components\TextInput::make('plan_type')
                    ->maxLength(255)
                    ->label('Type de plan'),
                Forms\Components\TextInput::make('monthly_cost')
                    ->label('Coût mensuel')
                    ->prefix('XOF')
                    ->numeric(),
                Forms\Components\Select::make('assigned_to')
                    ->relationship('assignedUser', 'name')
                    ->searchable()
                    ->preload()
                    ->label('Assignée à'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('iccid')
                    ->searchable()
                    ->sortable()
                    ->label('ICCID'),
                Tables\Columns\TextColumn::make('phone_number')
                    ->searchable()
                    ->label('Téléphone'),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'libre' => 'success',
                        'attribue' => 'info',
                        'attribué' => 'info',
                        'suspendu' => 'danger',
                        'suspendue' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('operator')
                    ->searchable(),
                Tables\Columns\TextColumn::make('assignedUser.name')
                    ->label('Assignée à')
                    ->sortable(),
                Tables\Columns\TextColumn::make('assigned_at')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'libre' => 'Libre',
                        'attribue' => 'Attribuée',
                        'suspendu' => 'Suspendue',
                    ]),
            ])
            ->actions([
                Tables\Actions\Action::make('assign')
                    ->label('Attribuer')
                    ->icon('heroicon-o-user-plus')
                    ->form([
                        Forms\Components\Select::make('user_id')
                            ->label('Utilisateur')
                            ->relationship('assignedUser', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                    ])
                    ->action(function (Sim $record, array $data) {
                        if (!$record->isLibre()) {
                            throw new \Exception('Cette SIM n\'est pas libre.');
                        }
                        $user = User::findOrFail($data['user_id']);
                        $oldData = $record->toArray();
                        $record->update([
                            'status' => 'attribue',
                            'assigned_to' => $user->id,
                            'assigned_to_matricule' => $user->matricule,
                            'assigned_at' => now(),
                        ]);
                        $record->histories()->create([
                            'action' => 'assigned',
                            'user_id' => auth()->id(),
                            'user_matricule' => auth()->user()->matricule,
                            'old_data' => $oldData,
                            'new_data' => $record->toArray(),
                        ]);
                    })
                    ->visible(fn (Sim $record) => $record->isLibre()),
                Tables\Actions\Action::make('unassign')
                    ->label('Libérer')
                    ->icon('heroicon-o-user-minus')
                    ->action(function (Sim $record) {
                        if (!$record->isAttribue()) {
                            throw new \Exception('Cette SIM n\'est pas attribuée.');
                        }
                        $oldData = $record->toArray();
                        $record->update([
                            'status' => 'libre',
                            'assigned_to' => null,
                            'assigned_to_matricule' => null,
                            'assigned_at' => null,
                        ]);
                        $record->histories()->create([
                            'action' => 'unassigned',
                            'user_id' => auth()->id(),
                            'user_matricule' => auth()->user()->matricule,
                            'old_data' => $oldData,
                            'new_data' => $record->toArray(),
                        ]);
                    })
                    ->requiresConfirmation()
                    ->visible(fn (Sim $record) => $record->isAttribue()),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('bulkAssign')
                        ->label('Attribuer en masse')
                        ->icon('heroicon-o-user-plus')
                        ->form([
                            Forms\Components\Select::make('user_id')
                                ->label('Utilisateur')
                                ->relationship('assignedUser', 'name', fn (Builder $query) => $query->where('active', true))
                                ->searchable()
                                ->preload()
                                ->required(),
                        ])
                        ->action(function ($records, array $data) {
                            $user = User::findOrFail($data['user_id']);
                            DB::transaction(function () use ($records, $user) {
                                foreach ($records as $sim) {
                                    if ($sim->isLibre()) {
                                        $oldData = $sim->toArray();
                                        $sim->update([
                                            'status' => 'attribue',
                                            'assigned_to' => $user->id,
                                            'assigned_to_matricule' => $user->matricule,
                                            'assigned_at' => now(),
                                        ]);
                                        if ($sim->exists) {
                                            $sim->histories()->create([
                                                'action' => 'assigned',
                                                'user_id' => auth()->id(),
                                                'user_matricule' => auth()->user()->matricule,
                                                'old_data' => $oldData,
                                                'new_data' => $sim->toArray(),
                                            ]);
                                        }
                                    }
                                }
                            });
                        })
                        ->requiresConfirmation(),
                    Tables\Actions\BulkAction::make('bulkUnassign')
                        ->label('Libérer en masse')
                        ->icon('heroicon-o-user-minus')
                        ->action(function ($records) {
                            DB::transaction(function () use ($records) {
                                foreach ($records as $sim) {
                                    if ($sim->isAttribue()) {
                                        $oldData = $sim->toArray();
                                        $sim->update([
                                            'status' => 'libre',
                                            'assigned_to' => null,
                                            'assigned_to_matricule' => null,
                                            'assigned_at' => null,
                                        ]);
                                        if ($sim->exists) {
                                            $sim->histories()->create([
                                                'action' => 'unassigned',
                                                'user_id' => auth()->id(),
                                                'user_matricule' => auth()->user()->matricule,
                                                'old_data' => $oldData,
                                                'new_data' => $sim->toArray(),
                                            ]);
                                        }
                                    }
                                }
                            });
                        })
                        ->requiresConfirmation(),
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            HistoriesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSims::route('/'),
            'create' => Pages\CreateSim::route('/create'),
            'edit' => Pages\EditSim::route('/{record}/edit'),
        ];
    }
}

