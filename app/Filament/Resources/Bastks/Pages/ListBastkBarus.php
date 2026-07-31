<?php

namespace App\Filament\Resources\Bastks\Pages;

use App\Filament\Resources\Bastks\BastkBaruResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListBastkBarus extends ListRecords
{
    protected static string $resource = BastkBaruResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
