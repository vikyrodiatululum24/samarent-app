<?php

namespace App\Filament\Resources\KunciSereps\Pages;

use App\Filament\Resources\KunciSereps\KunciSerepResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditKunciSerep extends EditRecord
{
    protected static string $resource = KunciSerepResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
    
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (isset($data['status_kunci']) && $data['status_kunci'] === 'tersedia') {
            $data['tanggal_keluar'] = null;
            $data['diambil_oleh'] = null;
        }

        return $data;
    }
}
