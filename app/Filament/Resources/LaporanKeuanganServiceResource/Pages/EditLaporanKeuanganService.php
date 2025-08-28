<?php

namespace App\Filament\Resources\LaporanKeuanganServiceResource\Pages;

use App\Filament\Resources\LaporanKeuanganServiceResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditLaporanKeuanganService extends EditRecord
{
    protected static string $resource = LaporanKeuanganServiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
