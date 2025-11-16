<?php

namespace App\Filament\Penjualan\Resources\OpenHouseResource\Pages;

use App\Filament\Penjualan\Resources\OpenHouseResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListOpenHouses extends ListRecords
{
    protected static string $resource = OpenHouseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
