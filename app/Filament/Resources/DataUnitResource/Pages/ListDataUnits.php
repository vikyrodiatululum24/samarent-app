<?php

namespace App\Filament\Resources\DataUnitResource\Pages;

use App\Filament\Resources\DataUnitResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDataUnits extends ListRecords
{
    protected static string $resource = DataUnitResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
