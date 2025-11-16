<?php

namespace App\Filament\Penjualan\Resources\OpenHouseResource\Pages;

use App\Filament\Penjualan\Resources\OpenHouseResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditOpenHouse extends EditRecord
{
    protected static string $resource = OpenHouseResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\DeleteAction::make()];
    }

    protected function beforeValidate(): void
    {
        $model = OpenHouseResource::getModel();
        $existingActiveEvent = $model::where('is_active', true)->first();

        if ($this->data['tanggal_event'] < date('Y-m-d')) {
            \Filament\Notifications\Notification::make()->danger()->title('Gagal')->body('Tanggal event tidak boleh kurang dari tanggal hari ini.')->persistent()->send();

            $this->halt();
        }

        if ($this->data['is_active'] && $existingActiveEvent && $existingActiveEvent->getKey() !== $this->record->getKey()) {
            \Filament\Notifications\Notification::make()
            ->danger()
            ->title('Gagal')
            ->body('Hanya boleh ada satu event aktif pada satu waktu. Nonaktifkan event aktif yang ada sebelum membuat event baru yang aktif.')
            ->persistent()
            ->send();

            $this->halt();
        }
    }
}
