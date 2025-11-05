<?php

namespace App\Filament\Absensi\Resources\KehadiranDriverResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use App\Filament\Absensi\Resources\KehadiranDriverResource;
use App\Filament\Absensi\Resources\KehadiranDriverResource\RelationManagers\OvertimePayRelationManager;

class ViewKehadiranDriver extends ViewRecord
{
    protected static string $resource = KehadiranDriverResource::class;

    public function getRelations(): array
    {
        return [
            OvertimePayRelationManager::class
        ];
    }
}
