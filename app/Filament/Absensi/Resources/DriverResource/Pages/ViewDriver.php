<?php

namespace App\Filament\Absensi\Resources\DriverResource\Pages;

use App\Filament\Absensi\Resources\DriverResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use App\Filament\Absensi\Resources\DriverResource\RelationManagers\DriverAttendenceRelationManager;

class ViewDriver extends ViewRecord
{
    protected static string $resource = DriverResource::class;

    protected function getSubNavigationItems(): array
    {
        return $this->getRelationManagersSubNavigation();
    }

    public function getRelations(): array
    {
        return [
            DriverAttendenceRelationManager::class
        ];
    }
}
