<?php

namespace App\Filament\Resources\BbmResource\Pages;

use App\Filament\Resources\BbmResource;
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
}
