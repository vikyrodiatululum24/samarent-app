<?php

namespace App\Filament\Absensi\Resources\KehadiranDriverResource\Pages;

use App\Filament\Absensi\Resources\KehadiranDriverResource;
use App\Helpers\PayrollHelpers;
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

    protected function afterSave(): void
    {
        if ($this->record->is_complete) {
            PayrollHelpers::calculateOvertimePay($this->record);
        }
    }

    public function getRelationManagers(): array
    {
        return [
            //
        ];
    }
}
