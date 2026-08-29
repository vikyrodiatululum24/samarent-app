<?php

namespace App\Filament\Resources\KunciSereps\Pages;

use App\Filament\Resources\KunciSereps\KunciSerepResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListKunciSereps extends ListRecords
{
    protected static string $resource = KunciSerepResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
