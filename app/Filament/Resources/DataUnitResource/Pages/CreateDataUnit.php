<?php

namespace App\Filament\Resources\DataUnitResource\Pages;

use App\Filament\Resources\DataUnitResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateDataUnit extends CreateRecord
{
    protected static string $resource = DataUnitResource::class;
    protected static ?string $title = 'Tambah Data Unit';
}
