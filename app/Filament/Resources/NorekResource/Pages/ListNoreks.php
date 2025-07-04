<?php

namespace App\Filament\Resources\NorekResource\Pages;

use App\Filament\Resources\NorekResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListNoreks extends ListRecords
{
    protected static string $resource = NorekResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
