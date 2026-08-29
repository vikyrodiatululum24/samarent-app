<?php

namespace App\Filament\Resources\KunciSereps\Pages;

use App\Filament\Resources\KunciSereps\KunciSerepResource;
use Filament\Resources\Pages\CreateRecord;

class CreateKunciSerep extends CreateRecord
{
    protected static string $resource = KunciSerepResource::class;
    
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (isset($data['status_kunci']) && $data['status_kunci'] === 'tersedia') {
            $data['tanggal_keluar'] = null;
            $data['diambil_oleh'] = null;
        }

        return $data;
    }
}
