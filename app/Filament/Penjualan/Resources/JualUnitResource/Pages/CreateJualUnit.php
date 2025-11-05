<?php

namespace App\Filament\Penjualan\Resources\JualUnitResource\Pages;

use App\Filament\Penjualan\Resources\JualUnitResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateJualUnit extends CreateRecord
{
    protected static string $resource = JualUnitResource::class;

    public function getTitle(): string
    {
        return 'Tambah Jual Unit';
    }
}
