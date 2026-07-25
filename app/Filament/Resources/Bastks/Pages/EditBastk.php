<?php

namespace App\Filament\Resources\Bastks\Pages;

use App\Filament\Resources\Bastks\BastkResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditBastk extends EditRecord
{
    protected static string $resource = BastkResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
