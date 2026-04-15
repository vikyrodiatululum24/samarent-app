<?php

namespace App\Filament\President\Resources\BosJoulmerApprovedResource\Pages;

use App\Filament\President\Resources\BosJoulmerApprovedResource;
use App\Models\BosJoulmer;
use Filament\Actions;
use Filament\Forms;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Form;
use Filament\Resources\Pages\ViewRecord;

class ViewBosJoulmerApproved extends ViewRecord
{
    protected static string $resource = BosJoulmerApprovedResource::class;

    protected static ?string $title = 'Detail Pengajuan Disetujui';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Umum')
                    ->schema([
                        Placeholder::make('no_pengajuan')
                            ->label('No. Pengajuan')
                            ->content(fn () => $this->record->pengajuan?->no_pengajuan ?? '-'),
                        Placeholder::make('no_wa')
                            ->label('Nomor WhatsApp')
                            ->content(fn () => $this->record->pengajuan?->no_wa ?? '-'),
                        Placeholder::make('project')
                            ->label('Project')
                            ->content(fn () => $this->record->pengajuan?->project ?? '-'),
                        Placeholder::make('keterangan')
                            ->label('Keterangan')
                            ->content(fn () => $this->record->pengajuan?->keterangan ?? '-'),
                        Placeholder::make('up')
                            ->label('Unit Pelaksana')
                            ->content(function () {
                                $pengajuan = $this->record->pengajuan;

                                if (! $pengajuan) {
                                    return '-';
                                }

                                if ($pengajuan->up === 'manual') {
                                    return $pengajuan->up_lainnya ?: '-';
                                }

                                return $pengajuan->up ?: '-';
                            }),
                        Placeholder::make('provinsi')
                            ->label('Provinsi')
                            ->content(fn () => $this->record->pengajuan?->provinsi ?? '-'),
                        Placeholder::make('kota')
                            ->label('Kota/Kabupaten')
                            ->content(fn () => $this->record->pengajuan?->kota ?? '-'),
                        Placeholder::make('status_pengajuan')
                            ->label('Status Pengajuan')
                            ->content(function () {
                                return match ($this->record->pengajuan?->keterangan_proses) {
                                    'cs' => 'Customer Service',
                                    'checker' => 'Verifikasi',
                                    'pengajuan atasan' => 'Pengajuan Atasan',
                                    'pengajuan finance' => 'Pengajuan Finance',
                                    'finance' => 'Input Finance',
                                    'otorisasi' => 'Otorisasi',
                                    'done' => 'Selesai',
                                    default => 'Tidak Diketahui',
                                };
                            }),
                    ])
                    ->columns(2),
                Forms\Components\Section::make('Pembayaran')
                    ->schema([
                        Placeholder::make('payment_1')
                            ->label('Nama Rekening')
                            ->content(fn () => $this->record->pengajuan?->payment_1 ?? '-'),
                        Placeholder::make('bank_1')
                            ->label('Bank')
                            ->content(fn () => $this->record->pengajuan?->bank_1 ?? '-'),
                        Placeholder::make('norek_1')
                            ->label('No. Rekening')
                            ->content(fn () => $this->record->pengajuan?->norek_1 ?? '-'),
                    ])
                    ->columns(3),
                Forms\Components\Section::make('Detail Kendaraan')
                    ->schema([
                        Forms\Components\View::make('filament.resources.pages.bos-joulmer.detail-kendaraan')
                            ->viewData([
                                'pengajuanId' => $this->record->pengajuan_id,
                            ])
                            ->columnSpanFull(),
                    ]),
                Forms\Components\Section::make('Informasi Complete')
                    ->schema([
                        Placeholder::make('complete_bengkel_estimasi')
                            ->label('Bengkel Estimasi')
                            ->content(fn () => $this->record->pengajuan?->complete?->bengkel_estimasi ?? '-'),
                        Placeholder::make('complete_nominal_estimasi')
                            ->label('Nominal Estimasi')
                            ->content(fn() => $this->record->pengajuan?->complete?->nominal_estimasi !== null ? 'Rp ' . number_format($this->record->pengajuan->complete->nominal_estimasi, 0, ',', '.') : '-'),
                        Placeholder::make('complete_bengkel_invoice')
                            ->label('Bengkel Invoice')
                            ->content(fn () => $this->record->pengajuan?->complete?->bengkel_invoice ?? '-'),
                        Placeholder::make('complete_payment_2')
                            ->label('Nama Rekening Finance')
                            ->content(fn () => $this->record->pengajuan?->complete?->payment_2 ?? '-'),
                        Placeholder::make('complete_bank_2')
                            ->label('Bank Finance')
                            ->content(fn () => $this->record->pengajuan?->complete?->bank_2 ?? '-'),
                        Placeholder::make('complete_norek_2')
                            ->label('No. Rekening Finance')
                            ->content(fn () => $this->record->pengajuan?->complete?->norek_2 ?? '-'),
                        Placeholder::make('complete_status_finance')
                            ->label('Status Finance')
                            ->content(function () {
                                return match ($this->record->pengajuan?->complete?->status_finance) {
                                    'paid' => 'PAID',
                                    'unpaid' => 'UNPAID',
                                    default => '-',
                                };
                            }),
                    ])
                    ->columns(3)
                    ->visible(fn () => filled($this->record->pengajuan?->complete)),
                Forms\Components\Section::make('Review Atasan')
                    ->schema([
                        Placeholder::make('note')
                            ->label('Catatan')
                            ->content(fn () => $this->record->note ?? '-'),
                        Placeholder::make('updated_at')
                            ->label('Waktu Review')
                            ->content(fn () => $this->record->updated_at?->format('d M Y H:i') ?? '-'),
                    ]),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('kembali')
                ->label('Kembali')
                ->icon('heroicon-o-arrow-left')
                ->url(static::getResource()::getUrl('index')),
        ];
    }
}
