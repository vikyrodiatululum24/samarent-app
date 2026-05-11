<?php

namespace App\Filament\Penjualan\Pages;

use BackedEnum;
use Filament\Pages\Dashboard as BaseDashboard;
use App\Filament\Penjualan\Resources\PenawarResource\Widgets\Penawar;

class Dashboard extends BaseDashboard
{
    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-home';

    public function getWidgets(): array
    {
        return [
            Penawar::class,
        ];
    }

    public function getColumns(): array|int
    {
        return 1;
    }
}
