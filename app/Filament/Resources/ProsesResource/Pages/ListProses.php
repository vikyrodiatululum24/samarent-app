<?php

namespace App\Filament\Resources\ProsesResource\Pages;

use App\Filament\Resources\ProsesResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListProses extends ListRecords
{
    protected static string $resource = ProsesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
