<?php

namespace App\Filament\Absensi\Resources\Gs\Pages;

use App\Filament\Absensi\Resources\Gs\GsResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListGs extends ListRecords
{
    protected static string $resource = GsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
