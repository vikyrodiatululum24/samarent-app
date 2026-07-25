<?php

namespace App\Filament\Resources\Bastks\Pages;

use App\Filament\Resources\Bastks\BastkResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListBastks extends ListRecords
{
    protected static string $resource = BastkResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
