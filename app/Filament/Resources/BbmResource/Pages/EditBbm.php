<?php

namespace App\Filament\Resources\BbmResource\Pages;

use App\Filament\Resources\BbmResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Storage;

class EditBbm extends EditRecord
{
    protected static string $resource = BbmResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Handle barcode_bbm
        if (isset($data['barcode_bbm']) && $data['barcode_bbm'] !== $this->record->barcode_bbm) {
            // Delete old file if exists
            if ($this->record->barcode_bbm && Storage::disk('public')->exists($this->record->barcode_bbm)) {
                Storage::disk('public')->delete($this->record->barcode_bbm);
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
                    // Delete barcode_bbm if exists
                    if ($this->record->barcode_bbm && Storage::disk('public')->exists($this->record->barcode_bbm)) {
                        Storage::disk('public')->delete($this->record->barcode_bbm);
                    }
                }),
        ];
    }
}
