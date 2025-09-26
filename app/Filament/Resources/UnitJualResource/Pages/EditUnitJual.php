<?php

namespace App\Filament\Resources\UnitJualResource\Pages;

use App\Filament\Resources\UnitJualResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditUnitJual extends EditRecord
{
    protected static string $resource = UnitJualResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
