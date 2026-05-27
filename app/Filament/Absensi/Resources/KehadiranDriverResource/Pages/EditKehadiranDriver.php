<?php

namespace App\Filament\Absensi\Resources\KehadiranDriverResource\Pages;

use App\Filament\Absensi\Resources\KehadiranDriverResource;
use App\Helpers\PayrollHelpers;
use Filament\Actions;
use App\Models\Driver;
use Filament\Resources\Pages\EditRecord;

class EditKehadiranDriver extends EditRecord
{
    protected static string $resource = KehadiranDriverResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // When editing, the form `user_id` holds the selected driver id.
        if (! empty($data['user_id'])) {
            $driver = Driver::where('user_id', $data['user_id'])->first();
            if ($driver) {
                $data['driver_id'] = $driver->id;
                $data['user_id'] = $driver->user_id;
            }
        }
        return $data;
    }

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
