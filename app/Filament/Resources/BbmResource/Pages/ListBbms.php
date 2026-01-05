<?php

namespace App\Filament\Resources\BbmResource\Pages;

use App\Filament\Resources\BbmResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBbms extends ListRecords
{
    protected static string $resource = BbmResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
