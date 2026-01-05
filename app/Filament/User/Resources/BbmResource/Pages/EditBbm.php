<?php

namespace App\Filament\User\Resources\BbmResource\Pages;

use App\Filament\User\Resources\BbmResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBbm extends EditRecord
{
    protected static string $resource = BbmResource::class;

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
}
