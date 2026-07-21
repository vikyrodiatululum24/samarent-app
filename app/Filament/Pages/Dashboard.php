<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\CalendarWidget;
use App\Filament\Widgets\EventHolidayListWidget;
use BackedEnum;

class Dashboard extends \Filament\Pages\Dashboard
{

    public function getHeaderWidgets(): array
    {
        return [
            CalendarWidget::class,
            EventHolidayListWidget::class,
        ];
    }
}
