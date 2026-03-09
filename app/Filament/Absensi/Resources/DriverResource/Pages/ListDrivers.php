<?php

namespace App\Filament\Absensi\Resources\DriverResource\Pages;

use App\Filament\Absensi\Resources\DriverResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDrivers extends ListRecords
{
    protected static string $resource = DriverResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Actions\Action::make('printForm')
                ->label('Print Form Driver')
                ->url(route('filament.driver.print-form-driver'))
                ->openUrlInNewTab()
                ->icon('heroicon-o-printer')
                ->color('success'),
        ];
    }
}
