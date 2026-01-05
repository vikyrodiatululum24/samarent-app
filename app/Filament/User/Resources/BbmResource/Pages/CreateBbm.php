<?php

namespace App\Filament\User\Resources\BbmResource\Pages;

use App\Filament\User\Resources\BbmResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateBbm extends CreateRecord
{
    protected static string $resource = BbmResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
