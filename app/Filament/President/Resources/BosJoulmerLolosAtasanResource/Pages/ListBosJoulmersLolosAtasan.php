<?php

namespace App\Filament\President\Resources\BosJoulmerLolosAtasanResource\Pages;

use App\Filament\President\Resources\BosJoulmerLolosAtasanResource;
use Filament\Resources\Pages\ListRecords;

class ListBosJoulmersLolosAtasan extends ListRecords
{
    protected static string $resource = BosJoulmerLolosAtasanResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
