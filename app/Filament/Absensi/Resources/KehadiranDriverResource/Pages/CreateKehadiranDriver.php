<?php

namespace App\Filament\Absensi\Resources\KehadiranDriverResource\Pages;

use App\Filament\Absensi\Resources\KehadiranDriverResource;
use App\Helpers\PayrollHelpers;
use App\Models\Driver;
use Filament\Resources\Pages\CreateRecord;

class CreateKehadiranDriver extends CreateRecord
{
    protected static string $resource = KehadiranDriverResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (! empty($data['user_id'])) {
            $driver = Driver::where('user_id', $data['user_id'])->first();
            if ($driver) {
                $data['driver_id'] = $driver->id;
                $data['user_id'] = $driver->user_id;
            }
        }

        return $data;
    }


    protected function afterCreate(): void
    {
        if ($this->record->is_complete) {
            PayrollHelpers::calculateOvertimePay($this->record);
        }
    }
}
