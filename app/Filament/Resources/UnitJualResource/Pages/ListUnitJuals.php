<?php

namespace App\Filament\Resources\UnitJualResource\Pages;

use App\Filament\Resources\UnitJualResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListUnitJuals extends ListRecords
{
    protected static string $resource = UnitJualResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
