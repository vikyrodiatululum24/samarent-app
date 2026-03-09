<?php

namespace App\Filament\Manager\Resources\DriverAttendenceResource\Pages;

use App\Filament\Manager\Resources\DriverAttendenceResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDriverAttendences extends ListRecords
{
    protected static string $resource = DriverAttendenceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            //
        ];
    }
}
