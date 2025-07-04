<?php

namespace App\Filament\Resources\NorekResource\Pages;

use App\Filament\Resources\NorekResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditNorek extends EditRecord
{
    protected static string $resource = NorekResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
