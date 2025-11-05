<?php

namespace App\Filament\Penjualan\Pages;

use Filament\Pages\Dashboard as BaseDashboard;
use App\Filament\Penjualan\Resources\PenawarResource\Widgets\Penawar;

class Dashboard extends BaseDashboard
{
    protected static ?string $navigationIcon = 'heroicon-o-home';

    public function getWidgets(): array
    {
        return [
            Penawar::class,
        ];
    }

    public function getColumns(): int | string | array
    {
        return 1;
    }
}
