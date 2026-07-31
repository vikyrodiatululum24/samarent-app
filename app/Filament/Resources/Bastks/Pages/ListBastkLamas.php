<?php

namespace App\Filament\Resources\Bastks\Pages;

use App\Filament\Resources\Bastks\BastkLamaResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListBastkLamas extends ListRecords
{
    protected static string $resource = BastkLamaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
