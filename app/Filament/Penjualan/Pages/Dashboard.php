<?php

namespace App\Filament\Penjualan\Pages;

use App\Filament\Widgets\CalendarWidget;
use App\Filament\Widgets\EventHolidayListWidget;
use BackedEnum;
use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-home';

    public function getWidgets(): array
    {
        return [
            CalendarWidget::class,
            EventHolidayListWidget::class,
        ];
    }

    public function getColumns(): array|int
    {
        return 1;
    }
}
