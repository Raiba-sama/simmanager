<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class DocumentationPage extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-book-open';
    protected static string $view = 'filament.pages.documentation';
    protected static ?string $navigationLabel = 'Documentation';
    protected static ?string $title = 'Documentation Technique';
    protected static ?string $navigationGroup = 'Aide';
    protected static ?int $navigationSort = 1;

    public static function canAccess(): bool
    {
        return auth()->user()?->isValidator() ?? false;
    }
}
