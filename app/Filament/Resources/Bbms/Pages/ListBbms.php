<?php

namespace App\Filament\Resources\Bbms\Pages;

use Filament\Actions\CreateAction;
use App\Filament\Resources\Bbms\BbmResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBbms extends ListRecords
{
    protected static string $resource = BbmResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}

