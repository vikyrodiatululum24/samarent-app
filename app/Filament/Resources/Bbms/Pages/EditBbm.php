<?php
namespace App\Filament\Resources\Bbms\Pages;

use Filament\Actions\ViewAction;
use Filament\Actions\DeleteAction;
use App\Filament\Resources\Bbms\BbmResource;
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
            ViewAction::make(),
            DeleteAction::make()
                ->after(function () {
                    // Delete barcode_bbm if exists
                    if ($this->record->barcode_bbm && Storage::disk('public')->exists($this->record->barcode_bbm)) {
                        Storage::disk('public')->delete($this->record->barcode_bbm);
                    }
                }),
        ];
    }
}

