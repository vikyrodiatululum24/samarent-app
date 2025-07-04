<?php

namespace App\Filament\Finance\Resources\NorekResource\Pages;

use App\Filament\Finance\Resources\NorekResource;
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
