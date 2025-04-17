<?php

namespace App\Filament\Resources\DataUnitResource\Pages;

use App\Filament\Resources\DataUnitResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDataUnit extends EditRecord
{
    protected static string $resource = DataUnitResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
