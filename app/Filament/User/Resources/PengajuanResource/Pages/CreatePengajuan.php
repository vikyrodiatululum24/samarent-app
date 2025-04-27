<?php

namespace App\Filament\User\Resources\PengajuanResource\Pages;

use App\Filament\User\Resources\PengajuanResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreatePengajuan extends CreateRecord
{
    protected static string $resource = PengajuanResource::class;

    public function getTitle(): string
    {
        return 'Buat Pengajuan Baru';
    }
}
