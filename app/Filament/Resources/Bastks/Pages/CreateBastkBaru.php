<?php

namespace App\Filament\Resources\Bastks\Pages;

use App\Filament\Resources\Bastks\BastkBaruResource;
use Filament\Resources\Pages\CreateRecord;

class CreateBastkBaru extends CreateRecord
{
    protected static string $resource = BastkBaruResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['jenis_bastk'] = 'new';

        return $data;
    }
}
