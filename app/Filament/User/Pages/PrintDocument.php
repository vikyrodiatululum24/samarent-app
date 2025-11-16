<?php

namespace App\Filament\User\Pages;

use Filament\Pages\Page;

class PrintDocument extends Page
{
    protected static ?string $title = 'Print Documentations';
    protected static ?string $navigationLabel = 'Print Documentations';
    protected static ?string $slug = 'print-documentations';

    protected static string $view = 'filament.pages.print-documentations';
}
