<?php

namespace App\Filament\Manager\Resources\DriverAttendenceResource\Pages;

use App\Filament\Manager\Resources\DriverAttendenceResource;
use App\Filament\Manager\Resources\DriverResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewDriverAttendence extends ViewRecord
{
    protected static string $resource = DriverAttendenceResource::class;

    public function getBreadcrumbs(): array
    {
        return [];
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('back')
                ->label('Kembali ke Driver')
                ->url(fn () => DriverResource::getUrl('view', ['record' => $this->record->driver_id]))
                ->color('gray')
                ->icon('heroicon-o-arrow-left'),
        ];
    }
}
