<?php

namespace App\Filament\Finance\Resources\PengajuanResource\Pages;

use App\Filament\Finance\Resources\PengajuanResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewPengajuan extends ViewRecord
{
    protected static string $resource = PengajuanResource::class;

    // tambahkan action untuk menampilkan tombol "Proses" di halaman detail
    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\Action::make('proses')
                ->label('Proses')
                ->url(fn ($record) => route('filament.finance.resources.pengajuans.proses', ['record' => $record->id]))
                ->color('primary'),
        ];
    }

    public static function getNavigationLabel(): string
    {
        return 'Detail'; // Ubah label tombol navigasi menjadi "Proses"
    }
}
