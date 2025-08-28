<?php

namespace App\Filament\Resources\LaporanKeuanganServiceResource\Pages;

use App\Filament\Resources\LaporanKeuanganServiceResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListLaporanKeuanganServices extends ListRecords
{
    protected static string $resource = LaporanKeuanganServiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
