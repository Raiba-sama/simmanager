<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Hash;

class UserResource extends Resource
{
    protected static ?string $model = User::class;
    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationGroup = 'Administration';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('matricule')
                    ->label('MLE')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),
                Forms\Components\TextInput::make('name')
                    ->label('Nom')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('first_name')
                    ->label('Prénom')
                    ->maxLength(255),
                Forms\Components\TextInput::make('fonction')
                    ->label('Poste')
                    ->maxLength(255),
                Forms\Components\TextInput::make('lieu_affectation')
                    ->label('Lieu d\'affectation')
                    ->maxLength(255),
                Forms\Components\TextInput::make('zone_affectation')
                    ->label('Zone d\'affectation')
                    ->maxLength(255),
                Forms\Components\TextInput::make('direction')
                    ->label('Direction')
                    ->maxLength(255),
                Forms\Components\TextInput::make('numero_flotte')
                    ->label('Numéro flotte 1')
                    ->maxLength(255),
                Forms\Components\TextInput::make('numero_flotte_2')
                    ->label('Numéro flotte 2')
                    ->maxLength(255),
                Forms\Components\TextInput::make('email')
                    ->email()
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),
                Forms\Components\TextInput::make('password')
                    ->password()
                    ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                    ->dehydrated(fn ($state) => filled($state))
                    ->required(fn (string $context): bool => $context === 'create')
                    ->maxLength(255),
                Forms\Components\Select::make('role')
                    ->options([
                        'admin' => 'Admin',
                        'validator' => 'Validateur',
                        'user' => 'Utilisateur',
                    ])
                    ->required(),
                Forms\Components\Toggle::make('active')
                    ->default(true),
                Forms\Components\TextInput::make('avatar_url')
                    ->url()
                    ->maxLength(500),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('matricule')
                    ->label('MLE')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Nom')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('first_name')
                    ->label('Prénom')
                    ->searchable(),
                Tables\Columns\TextColumn::make('fonction')
                    ->label('Poste')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('lieu_affectation')
                    ->label('Lieu d\'affectation')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('zone_affectation')
                    ->label('Zone d\'affectation')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('direction')
                    ->label('Direction')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('numero_flotte')
                    ->label('N° Flotte 1')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('numero_flotte_2')
                    ->label('N° Flotte 2')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('role')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'admin' => 'danger',
                        'validator' => 'warning',
                        'user' => 'info',
                    }),
                Tables\Columns\IconColumn::make('active')
                    ->boolean()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('role')
                    ->options([
                        'admin' => 'Admin',
                        'validator' => 'Validateur',
                        'user' => 'Utilisateur',
                    ]),
                Tables\Filters\TernaryFilter::make('active')
                    ->label('Actif')
                    ->placeholder('Tous')
                    ->trueLabel('Actifs uniquement')
                    ->falseLabel('Inactifs uniquement'),
                Tables\Filters\TernaryFilter::make('has_sim')
                    ->label('Carte SIM')
                    ->placeholder('Tous')
                    ->trueLabel('Avec SIM')
                    ->falseLabel('Sans SIM')
                    ->queries(
                        true: fn (\Illuminate\Database\Eloquent\Builder $q) => $q->whereHas('assignedSims'),
                        false: fn (\Illuminate\Database\Eloquent\Builder $q) => $q->whereDoesntHave('assignedSims'),
                    ),
            ])
            ->actions([
                Tables\Actions\Action::make('viewSims')
                    ->label('Détails SIM')
                    ->icon('heroicon-o-device-phone-mobile')
                    ->color('info')
                    ->modalHeading(fn (User $record) => 'Cartes SIM — ' . $record->full_name)
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Fermer')
                    ->modalWidth('xl')
                    ->modalContent(function (User $record) {
                        $directIds = $record->assignedSims()->pluck('id');

                        $requestSimIds = \App\Models\SimRequest::where(function ($q) use ($record) {
                                $q->where('user_id', $record->id);
                                if ($record->matricule) {
                                    $q->orWhere('collaborator_matricule', $record->matricule)
                                      ->orWhere('beneficiary_matricule', $record->matricule);
                                }
                            })
                            ->whereNotNull('sim_id')
                            ->pluck('sim_id');

                        $allIds = $directIds->merge($requestSimIds)->unique();

                        $sims = \App\Models\Sim::whereIn('id', $allIds)
                            ->orderByDesc('assigned_at')
                            ->get();

                        return view('filament.modals.user-sims-detail', [
                            'user' => $record,
                            'sims' => $sims,
                        ]);
                    }),
                Tables\Actions\Action::make('resetPassword')
                    ->label('Réinitialiser mot de passe')
                    ->icon('heroicon-o-key')
                    ->form([
                        Forms\Components\TextInput::make('password')
                            ->password()
                            ->required()
                            ->confirmed()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('password_confirmation')
                            ->password()
                            ->required()
                            ->maxLength(255),
                    ])
                    ->action(function (User $record, array $data) {
                        $record->update(['password' => Hash::make($data['password'])]);
                    })
                    ->requiresConfirmation(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('enable')
                        ->label('Activer')
                        ->icon('heroicon-o-check-circle')
                        ->action(fn ($records) => $records->each->update(['active' => true]))
                        ->requiresConfirmation(),
                    Tables\Actions\BulkAction::make('disable')
                        ->label('Désactiver')
                        ->icon('heroicon-o-x-circle')
                        ->action(fn ($records) => $records->each->update(['active' => false]))
                        ->requiresConfirmation(),
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}

