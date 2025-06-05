<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Forms\Components\Actions\ButtonAction;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class PrintDocumentations extends Page
{
    protected static ?string $title = 'Print Documentations';
    protected static ?string $navigationLabel = 'Print Documentations';
    protected static ?string $slug = 'print-documentations';
    protected static ?string $navigationGroup = 'Unit';

    protected static string $view = 'filament.pages.print-documentations';
}
