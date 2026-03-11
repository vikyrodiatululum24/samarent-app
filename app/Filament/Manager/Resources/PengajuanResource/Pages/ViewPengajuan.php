<?php

namespace App\Filament\Manager\Resources\PengajuanResource\Pages;

use App\Filament\Manager\Resources\PengajuanResource;
use Filament\Actions;
use Filament\Infolists\Components;
use Filament\Infolists\Components\ViewEntry;
use Filament\Resources\Pages\ViewRecord;

class ViewPengajuan extends ViewRecord
{
    protected static string $resource = PengajuanResource::class;

    public function infolist(\Filament\Infolists\Infolist $infolist): \Filament\Infolists\Infolist
    {
        return $infolist
            ->schema([
                Components\Section::make('Informasi Umum')
                    ->schema([
                        Components\TextEntry::make('nama')
                            ->label('Nama PIC')
                            ->getStateUsing(fn($record) => strtoupper($record->nama)),
                        Components\TextEntry::make('no_wa')
                            ->label('Nomor WhatsApp')
                            ->getStateUsing(fn($record) => strtoupper($record->no_wa)),
                        Components\TextEntry::make('project')
                            ->label('Project')
                            ->getStateUsing(fn($record) => strtoupper($record->project)),
                        Components\TextEntry::make('keterangan')
                            ->label('Keterangan')
                            ->getStateUsing(fn($record) => strtoupper($record->keterangan)),
                        Components\TextEntry::make('up')
                            ->label('Unit Pelaksana')
                            ->getStateUsing(function ($record) {
                                if ($record->up === 'manual') {
                                    return $record->up_lainnya ?? 'Lainnya';
                                }
                                return $record->up;
                            })
                            ->badge()
                            ->formatStateUsing(fn($state) => strtoupper($state)),
                        Components\TextEntry::make('provinsi')
                            ->label('Provinsi')
                            ->getStateUsing(fn($record) => strtoupper($record->provinsi)),
                        Components\TextEntry::make('kota')
                            ->label('Kota')
                            ->getStateUsing(fn($record) => strtoupper($record->kota)),
                        Components\TextEntry::make('keterangan_proses')
                            ->label('Status Proses')
                            ->badge()
                            ->getStateUsing(function ($record) {
                                return match ($record->keterangan_proses) {
                                    'cs' => 'Customer Service',
                                    'checker' => 'Verifikasi',
                                    'pengajuan finance' => 'Pengajuan Finance',
                                    'finance' => 'Input Finance',
                                    'otorisasi' => 'Otorisasi',
                                    'done' => 'Selesai',
                                    default => 'Tidak Diketahui',
                                };
                            })
                            ->color(fn(string $state) => match ($state) {
                                'Customer Service' => 'black',
                                'Verifikasi' => 'danger',
                                'Pengajuan Finance' => 'primary',
                                'Input Finance' => 'brown',
                                'Otorisasi' => 'yellow',
                                'Selesai' => 'success',
                                default => 'gray',
                            }),
                        Components\TextEntry::make('created_at')
                            ->label('Tanggal Pengajuan')
                            ->dateTime()
                            ->getStateUsing(fn($record) => $record->created_at->format('d M Y H:i:s')),

                        Components\TextEntry::make('logUpdateStatus')
                            ->label('History Update Status')
                            ->getStateUsing(fn($record) =>
                                $record->logUpdateStatusPengajuans()
                                    ->orderBy('created_at', 'desc')
                                    ->get()
                                    ->map(fn($log) => "{$log->status_baru} - " . $log->created_at->format('d M Y H:i:s'))
                                    ->implode('<br>')
                            )
                            ->html(),
                    ])
                    ->columns(2),

                Components\Section::make('Detail Kendaraan')
                    ->schema([
                        ViewEntry::make('service_unit.pengajuan_id')
                            ->label('Detail Kendaraan')
                            ->view('filament.resources.pages.pengajuan.detail-kendaraan')
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
