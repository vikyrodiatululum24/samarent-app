<?php

namespace App\Filament\User\Pages;

use App\Filament\Widgets\CalendarWidget;
use App\Filament\Widgets\EventHolidayListWidget;
use BackedEnum;
use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
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
