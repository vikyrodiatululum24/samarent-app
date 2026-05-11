<?php

namespace App\Filament\Pages;

use BackedEnum;

class Dashboard extends \Filament\Pages\Dashboard
{
    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-circle-stack';
}
