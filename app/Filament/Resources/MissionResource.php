<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MissionResource\Pages;
use App\Models\Mission;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class MissionResource extends Resource
{
    protected static ?string $model = Mission::class;

    protected static ?string $navigationIcon = 'heroicon-o-map-pin';
    protected static ?string $navigationLabel = 'Missions';
    protected static ?string $modelLabel = 'Mission';
    protected static ?string $pluralModelLabel = 'Missions';
    protected static ?string $navigationGroup = 'Gestion';
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
                        Forms\Components\TextInput::make('title')
                            ->label('Objet / Intitulé')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),
                        Forms\Components\Select::make('type')
                            ->label('Type de mission')
                            ->options(Mission::TYPES)
                            ->required()
                            ->native(false),
                        Forms\Components\Select::make('agency_id')
                            ->label('Agence')
                            ->relationship(
                                'agency',
                                'name',
                                fn ($query) => $query->where('active', true)->orderBy('code')
                            )
                            ->getOptionLabelFromRecordUsing(fn ($record) => $record->code . ' - ' . $record->name)
                            ->searchable(['code', 'name'])
                            ->preload()
                            ->required()
                            ->native(false),
                        Forms\Components\Select::make('status')
                            ->label('Statut')
                            ->options(Mission::STATUSES)
                            ->default('planned')
                            ->required()
                            ->native(false),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Dates et durée')
                    ->description('Renseignez la date de début et de fin, ou la date de début et le nombre de jours. Le nombre de jours est calculé automatiquement si début et fin sont renseignés.')
                    ->schema([
                        Forms\Components\DatePicker::make('start_date')
                            ->label('Date de début')
                            ->required()
                            ->native(false),
                        Forms\Components\DatePicker::make('end_date')
                            ->label('Date de fin')
                            ->native(false),
                        Forms\Components\TextInput::make('number_of_days')
                            ->label('Nombre de jours')
                            ->numeric()
                            ->minValue(1),
                    ])
                    ->columns(3),

                Forms\Components\Section::make('Participants (admins)')
                    ->schema([
                        Forms\Components\Select::make('users')
                            ->label('Admins concernés')
                            ->relationship(
                                'users',
                                'name',
                                fn ($query) => $query->where('role', 'admin')->where('active', true)->orderBy('name')
                            )
                            ->getOptionLabelFromRecordUsing(fn ($record) => $record->full_name . ' (' . $record->matricule . ')')
                            ->searchable(['name', 'first_name', 'matricule'])
                            ->preload()
                            ->multiple()
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Section::make('Description')
                    ->schema([
                        Forms\Components\Textarea::make('description')
                            ->label('Notes / Description')
                            ->rows(4)
                            ->columnSpanFull(),
                    ])
                    ->collapsed(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('Objet')
                    ->searchable()
                    ->sortable()
                    ->limit(40),
                Tables\Columns\TextColumn::make('type')
                    ->label('Type')
                    ->formatStateUsing(fn (string $state) => Mission::TYPES[$state] ?? $state)
                    ->badge()
                    ->sortable(),
                Tables\Columns\TextColumn::make('agency.name')
                    ->label('Agence')
                    ->formatStateUsing(fn ($state, $record) => $record->agency ? $record->agency->code . ' - ' . $record->agency->name : '-')
                    ->searchable(['agencies.code', 'agencies.name'])
                    ->sortable(),
                Tables\Columns\TextColumn::make('start_date')
                    ->label('Début')
                    ->date('d/m/Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('end_date')
                    ->label('Fin')
                    ->date('d/m/Y')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('number_of_days')
                    ->label('Jours')
                    ->sortable()
                    ->alignCenter(),
                Tables\Columns\TextColumn::make('users_count')
                    ->label('Participants')
                    ->counts('users')
                    ->alignCenter()
                    ->badge(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Statut')
                    ->formatStateUsing(fn (string $state) => Mission::STATUSES[$state] ?? $state)
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'planned' => 'info',
                        'in_progress' => 'warning',
                        'completed' => 'success',
                        'cancelled' => 'danger',
                        default => 'gray',
                    })
                    ->sortable(),
            ])
            ->defaultSort('start_date', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->label('Type')
                    ->options(Mission::TYPES),
                Tables\Filters\SelectFilter::make('agency_id')
                    ->label('Agence')
                    ->relationship('agency', 'name', fn ($query) => $query->orderBy('code'))
                    ->searchable()
                    ->preload(),
                Tables\Filters\SelectFilter::make('status')
                    ->label('Statut')
                    ->options(Mission::STATUSES),
                Tables\Filters\Filter::make('upcoming')
                    ->label('À venir')
                    ->query(fn (Builder $query) => $query->where('start_date', '>=', now()->toDateString())->whereIn('status', ['planned', 'in_progress']))
                    ->toggle(),
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
        return [];
    }

    public static function getWidgets(): array
    {
        return [
            \App\Filament\Widgets\MissionStatsWidget::class,
            \App\Filament\Widgets\MissionsPerMonthChartWidget::class,
            \App\Filament\Widgets\MissionsByTypeChartWidget::class,
            \App\Filament\Widgets\MissionsByAgencyChartWidget::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMissions::route('/'),
            'create' => Pages\CreateMission::route('/create'),
            'edit' => Pages\EditMission::route('/{record}/edit'),
        ];
    }
}
