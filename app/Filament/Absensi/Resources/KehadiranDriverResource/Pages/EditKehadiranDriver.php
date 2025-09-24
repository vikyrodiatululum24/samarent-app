<?php

namespace App\Filament\Absensi\Resources\KehadiranDriverResource\Pages;

use App\Filament\Absensi\Resources\KehadiranDriverResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditKehadiranDriver extends EditRecord
{
    protected static string $resource = KehadiranDriverResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
