<?php

namespace App\Filament\Resources\UnitJualResource\Pages;

use App\Filament\Resources\UnitJualResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateUnitJual extends CreateRecord
{
    protected static string $resource = UnitJualResource::class;
    protected static ?string $title = 'Tambah Unit Jual';
}
