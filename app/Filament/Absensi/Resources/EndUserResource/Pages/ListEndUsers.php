<?php

namespace App\Filament\Absensi\Resources\EndUserResource\Pages;

use App\Filament\Absensi\Resources\EndUserResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListEndUsers extends ListRecords
{
    protected static string $resource = EndUserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
