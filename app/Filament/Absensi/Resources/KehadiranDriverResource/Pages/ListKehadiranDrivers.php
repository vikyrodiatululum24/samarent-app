<?php

namespace App\Filament\Absensi\Resources\KehadiranDriverResource\Pages;

use App\Filament\Imports\DriverAttendanceImporter;
use App\Filament\Absensi\Resources\KehadiranDriverResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListKehadiranDrivers extends ListRecords
{
    protected static string $resource = KehadiranDriverResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ImportAction::make()
                ->label('Import Excel')
                ->importer(DriverAttendanceImporter::class),
            Actions\CreateAction::make(),
        ];
    }


}
