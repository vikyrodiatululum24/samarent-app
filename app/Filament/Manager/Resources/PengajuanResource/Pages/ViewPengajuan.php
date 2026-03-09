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
                Components\Section::make('Pembayaran')
                    ->schema([
                        Components\Grid::make(3)
                            ->schema([
                                Components\TextEntry::make('payment_1')
                                    ->label('Nama Rekening')
                                    ->getStateUsing(fn($record) => strtoupper($record->payment_1)),
                                Components\TextEntry::make('bank_1')
                                    ->label('Nama Bank')
                                    ->getStateUsing(fn($record) => strtoupper($record->bank_1)),
                                Components\TextEntry::make('norek_1')
                                    ->label('Nomor Rekening')
                                    ->getStateUsing(fn($record) => strtoupper($record->norek_1)),
                            ]),
                    ]),
                Components\Section::make('Detail Kendaraan')
                    ->schema([
                        ViewEntry::make('service_unit.pengajuan_id')
                            ->label('Detail Kendaraan')
                            ->view('filament.resources.pages.pengajuan.detail-kendaraan')
                            ->columnSpanFull(),
                    ]),
                Components\Section::make('Informasi Complete')
                    ->schema([
                        Components\Grid::make(3)
                            ->schema([
                                Components\Group::make([
                                    Components\TextEntry::make('complete.kode')
                                        ->label('Kode')
                                        ->visible(fn($record) => !empty($record->complete?->kode))
                                        ->badge()
                                        ->formatStateUsing(fn($state) => strtoupper($state)),
                                ]),
                                Components\TextEntry::make('complete.bengkel_invoice')
                                    ->label('Bengkel Invoice')
                                    ->visible(fn($record) => !empty($record->complete?->bengkel_invoice))
                                    ->columnSpan(2),

                            ]),
                        Components\Grid::make(3)
                            ->schema([
                                Components\TextEntry::make('complete.bengkel_estimasi')
                                    ->label('Bengkel Estimasi'),
                                Components\TextEntry::make('complete.no_telp_bengkel')
                                    ->label('No. Telp Bengkel'),
                                Components\TextEntry::make('complete.nominal_estimasi')
                                    ->label('Nominal Estimasi'),
                            ])
                            ->label('Informasi Bengkel'),
                        Components\Grid::make(3)
                            ->schema([
                                Components\TextEntry::make('complete.tanggal_masuk_finance')
                                    ->label('Tanggal Masuk Finance')
                                    ->date()
                                    ,
                                Components\TextEntry::make('complete.tanggal_tf_finance')
                                    ->label('Tanggal Transfer Finance')
                                    ->date(),
                                Components\TextEntry::make('complete.nominal_tf_finance')
                                    ->label('Nominal Transfer Finance'),
                            ]),
                        Components\Grid::make(1)
                            ->schema([
                                Components\Group::make([
                                    Components\TextEntry::make('complete.payment_2')
                                        ->label('Nama Rekening'),
                                ]),
                            ]),
                        Components\Grid::make(3)
                            ->schema([
                                Components\TextEntry::make('complete.bank_2')
                                    ->label('Bank'),
                                Components\TextEntry::make('complete.norek_2')
                                    ->label('No. Rekening'),
                                Components\TextEntry::make('complete.status_finance')
                                    ->label('Status Finance')
                                     ->getStateUsing(function ($record) {
                                        return match ($record->complete?->status_finance) {
                                            'paid' => 'PAID',
                                            'unpaid' => 'UNPAID',
                                            default => 'Tidak Diketahui',
                                        };
                                    })
                                     ->color(fn(string $state) => match ($state) {
                                        'PAID' => 'success',
                                        'UNPAID' => 'danger',
                                         default => 'gray',
                                     }),
                            ]),
                    ])
                    ->visible(fn($record) => !empty($record->complete)), // Only show when complete data is filled
                    Components\Section::make('Informasi Bengkel')
                    ->schema([
                        Components\Grid::make(3)
                            ->schema([
                                Components\TextEntry::make('complete.rek_bengkel')
                                    ->label('No. Rekening Bengkel'),
                                Components\TextEntry::make('complete.nama_rek_bengkel')
                                    ->label('Nama Rekening Bengkel')
                                    ->getStateUsing(fn($record) => strtoupper($record->complete?->nama_rek_bengkel)),
                                Components\TextEntry::make('complete.bank_bengkel')
                                    ->label('Bank Bengkel')
                                    ->getStateUsing(fn($record) => strtoupper($record->complete?->bank_bengkel)),
                                Components\TextEntry::make('complete.nominal_tf_bengkel')
                                    ->label('Nominal Transfer Bengkel'),
                                Components\TextEntry::make('complete.selisih_tf')
                                    ->label('Selisih Transfer'),
                                Components\TextEntry::make('complete.tanggal_tf_bengkel')
                                    ->label('Tanggal Transfer Bengkel')
                                    ->date(),
                                Components\TextEntry::make('complete.tanggal_pengerjaan')
                                    ->label('Tanggal Pengerjaan')
                                    ->date(),
                            ]),
                    ])
                    ->visible(fn($record) => !empty($record->complete)),
                Components\Section::make('Dokumentasi Complete')
                    ->schema([
                        ViewEntry::make('complete.foto_nota')
                            ->label('Foto Nota')
                            ->view('filament.components.foto-nota')
                            ->columnSpan([
                                'default' => 4,
                                'md' => 3,
                            ]),
                        ViewEntry::make('finance.bukti_transaksi')
                            ->label('Bukti Transaksi')
                            ->view('filament.components.bukti_transaksi')
                            ->columnSpan([
                                'default' => 4,
                                'md' => 3,
                            ]),
                    ])
                    ->columns([
                        'default' => 4,
                        'md' => 3,
                    ])
                    ->visible(fn($record) => !empty($record->complete)),
            ]);
    }
}
