<?php

namespace App\Filament\Resources\LaporanKeuanganServiceResource\Pages;

use App\Filament\Resources\LaporanKeuanganServiceResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateLaporanKeuanganService extends CreateRecord
{
    protected static string $resource = LaporanKeuanganServiceResource::class;

    protected static string $label = 'Buat Laporan Keuangan Service';

    protected function getRedirectUrl(): string
    {
        return LaporanKeuanganServiceResource::getUrl('index');
    }

}
