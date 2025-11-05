<?php

namespace App\Filament\Penjualan\Resources\JualUnitResource\Pages;

use App\Filament\Penjualan\Resources\JualUnitResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditJualUnit extends EditRecord
{
    protected static string $resource = JualUnitResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
