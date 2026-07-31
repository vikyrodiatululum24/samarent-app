<?php

namespace App\Filament\Resources\Bastks\Pages;

use App\Filament\Resources\Bastks\BastkLamaResource;
use Filament\Resources\Pages\CreateRecord;

class CreateBastkLama extends CreateRecord
{
    protected static string $resource = BastkLamaResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['jenis_bastk'] = 'old';

        return $data;
    }
}
