<?php

namespace App\Filament\User\Resources\PengajuanResource\Pages;

use App\Filament\User\Resources\PengajuanResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPengajuans extends ListRecords
{
    protected static string $resource = PengajuanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
