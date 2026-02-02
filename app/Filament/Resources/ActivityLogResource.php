<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ActivityLogResource\Pages;
use App\Models\ActivityLog;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class ActivityLogResource extends Resource
{
    protected static ?string $model = ActivityLog::class;
    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';
    protected static ?string $navigationGroup = 'Administration';
    protected static ?int $navigationSort = 2;
    protected static ?string $navigationLabel = 'Logs d\'activité';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('user_identifier')
                    ->disabled(),
                Forms\Components\TextInput::make('action')
                    ->disabled(),
                Forms\Components\TextInput::make('table_name')
                    ->disabled(),
                Forms\Components\Textarea::make('description')
                    ->disabled()
                    ->rows(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user_identifier')
                    ->searchable()
                    ->sortable()
                    ->label('Utilisateur'),
                Tables\Columns\TextColumn::make('action')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('info'),
                Tables\Columns\TextColumn::make('table_name')
                    ->searchable()
                    ->sortable()
                    ->label('Table'),
                Tables\Columns\TextColumn::make('record_id')
                    ->label('ID Enregistrement'),
                Tables\Columns\TextColumn::make('description')
                    ->limit(50)
                    ->tooltip(fn ($record) => $record->description),
                Tables\Columns\TextColumn::make('ip_address')
                    ->label('IP'),
                Tables\Columns\TextColumn::make('user_agent')
                    ->limit(30)
                    ->tooltip(fn ($record) => Str::limit($record->user_agent ?? '', 200)),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->label('Date'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('action')
                    ->options(function () {
                        return ActivityLog::distinct('action')
                            ->pluck('action', 'action')
                            ->toArray();
                    }),
                Tables\Filters\SelectFilter::make('table_name')
                    ->options(function () {
                        return ActivityLog::distinct('table_name')
                            ->whereNotNull('table_name')
                            ->pluck('table_name', 'table_name')
                            ->toArray();
                    }),
                Tables\Filters\Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('created_from')
                            ->label('Du'),
                        Forms\Components\DatePicker::make('created_until')
                            ->label('Au'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn ($query, $date) => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn ($query, $date) => $query->whereDate('created_at', '<=', $date),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkAction::make('export')
                    ->label('Exporter CSV')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->action(function ($records) {
                        $filename = 'activity_logs_' . now()->format('Y-m-d_His') . '.csv';
                        $headers = ['ID', 'Utilisateur', 'Action', 'Table', 'ID Enregistrement', 'Description', 'IP', 'Date'];
                        
                        $file = fopen('php://temp', 'r+');
                        fputcsv($file, $headers);
                        
                        foreach ($records as $log) {
                            fputcsv($file, [
                                $log->id,
                                $log->user_identifier,
                                $log->action,
                                $log->table_name ?? '',
                                $log->record_id ?? '',
                                $log->description ?? '',
                                $log->ip_address ?? '',
                                $log->created_at->format('Y-m-d H:i:s'),
                            ]);
                        }
                        
                        rewind($file);
                        $content = stream_get_contents($file);
                        fclose($file);
                        
                        return response()->streamDownload(function () use ($content) {
                            echo $content;
                        }, $filename, [
                            'Content-Type' => 'text/csv',
                        ]);
                    }),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListActivityLogs::route('/'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit($record): bool
    {
        return false;
    }

    public static function canDelete($record): bool
    {
        return false;
    }
}

