<?php

namespace App\Filament\Finance\Resources\PengajuanResource\Pages;

use App\Filament\Finance\Resources\PengajuanResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewPengajuan extends ViewRecord
{
    protected static string $resource = PengajuanResource::class;

    public static function getNavigationLabel(): string
    {
        return 'Detail'; // Ubah label tombol navigasi menjadi "Proses"
    }
}
