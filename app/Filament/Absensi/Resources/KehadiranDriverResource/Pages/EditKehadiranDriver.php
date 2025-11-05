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

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if ($data['is_complete'] == true) {
            PayrollHelpers::calculateOvertimePay($this->record);
        }

        return $data;
    }

    public function getRelationManagers(): array
    {
        return [
            //
        ];
    }
}
