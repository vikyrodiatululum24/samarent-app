<?php

namespace App\Filament\Resources\ReimbursementResource\Pages;

use App\Filament\Resources\ReimbursementResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Storage;

class EditReimbursement extends EditRecord
{
    protected static string $resource = ReimbursementResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Handle foto_odometer_awal
        if (isset($data['foto_odometer_awal']) && $data['foto_odometer_awal'] !== $this->record->foto_odometer_awal) {
            // Delete old file if exists
            if ($this->record->foto_odometer_awal && Storage::disk('public')->exists($this->record->foto_odometer_awal)) {
                Storage::disk('public')->delete($this->record->foto_odometer_awal);
            }
        }

        // Handle foto_odometer_akhir
        if (isset($data['foto_odometer_akhir']) && $data['foto_odometer_akhir'] !== $this->record->foto_odometer_akhir) {
            // Delete old file if exists
            if ($this->record->foto_odometer_akhir && Storage::disk('public')->exists($this->record->foto_odometer_akhir)) {
                Storage::disk('public')->delete($this->record->foto_odometer_akhir);
            }
        }

        return $data;
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make()
                ->after(function () {
                    // Delete foto_odometer_awal if exists
                    if ($this->record->foto_odometer_awal && Storage::disk('public')->exists($this->record->foto_odometer_awal)) {
                        Storage::disk('public')->delete($this->record->foto_odometer_awal);
                    }
                    // Delete foto_odometer_akhir if exists
                    if ($this->record->foto_odometer_akhir && Storage::disk('public')->exists($this->record->foto_odometer_akhir)) {
                        Storage::disk('public')->delete($this->record->foto_odometer_akhir);
                    }
                }),
        ];
    }
}
