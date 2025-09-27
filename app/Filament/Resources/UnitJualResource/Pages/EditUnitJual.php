<?php

namespace App\Filament\Resources\UnitJualResource\Pages;

use App\Filament\Resources\UnitJualResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditUnitJual extends EditRecord
{
    protected static string $resource = UnitJualResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->before(function ($record) {
                    // List of image fields to delete
                    $imageFields = [
                        'foto_depan',
                        'foto_belakang',
                        'foto_kiri',
                        'foto_kanan',
                        'foto_interior',
                        'foto_odometer'
                    ];

                    foreach ($imageFields as $field) {
                        if ($record->$field && \Storage::disk('public')->exists($record->$field)) {
                            \Storage::disk('public')->delete($record->$field);
                        }
                    }
                }),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // List of image fields to check
        $imageFields = [
            'foto_depan',
            'foto_belakang',
            'foto_kiri',
            'foto_kanan',
            'foto_interior',
            'foto_odometer'
        ];

        foreach ($imageFields as $field) {
            // Check if the image field exists and has been changed
            if (isset($data[$field]) && $this->record->$field !== $data[$field]) {
            // Delete old image from storage if it exists
            if ($this->record->$field && \Storage::disk('public')->exists($this->record->$field)) {
                \Storage::disk('public')->delete($this->record->$field);
            }
            }

            // If image field is empty but record had an image, delete the old image
            if (empty($data[$field]) && $this->record->$field) {
            if (\Storage::disk('public')->exists($this->record->$field)) {
                \Storage::disk('public')->delete($this->record->$field);
            }
            }
        }
        return $data;
    }
}
