<?php

namespace App\Filament\Resources\ArchivePengajuanResource\Pages;

use App\Filament\Resources\ArchivePengajuanResource;
use App\Models\Pengajuan;
use Filament\Actions;
use Filament\Forms\Components\Select;
use Filament\Resources\Pages\ListRecords;

class ListArchivePengajuans extends ListRecords
{
    protected static string $resource = ArchivePengajuanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('tambah_ke_archive')
                ->label('Tambah SPK ke Archive')
                ->icon('heroicon-o-plus')
                ->form([
                    Select::make('pengajuan_ids')
                        ->label('Pilih No. SPK')
                        ->multiple()
                        ->searchable()
                        ->options(function () {
                            return Pengajuan::whereNull('hidden_at')
                                ->orderBy('id', 'desc')
                                ->pluck('no_pengajuan', 'id');
                        })
                        ->required(),
                ])
                ->action(function (array $data) {
                    Pengajuan::whereIn('id', $data['pengajuan_ids'])->update(['hidden_at' => now()]);
                }),
        ];
    }
}
