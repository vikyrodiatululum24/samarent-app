<?php

namespace App\Filament\Resources\FormTugasResource\Pages;

use App\Filament\Resources\FormTugasResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Support\Enums\FontWeight;

class ViewFormTugas extends ViewRecord
{
    protected static string $resource = FormTugasResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ActionGroup::make([
                Actions\Action::make('preview_form_tugas')
                    ->label('Preview')
                    ->icon('heroicon-o-eye')
                    ->url(fn($record) => route('preview.form-tugas', $record->id))
                    ->openUrlInNewTab(),
                Actions\Action::make('print_form_tugas')
                    ->label('Print')
                    ->icon('heroicon-o-printer')
                    ->url(fn($record) => route('print.form-tugas', $record->id))
                    ->openUrlInNewTab()
                    ->badge(fn($record) => \App\Models\Cetak::where('form_tugas_id', $record->id)->exists() ? 'Sudah di-print' : null)
            ])
                ->label('Print')
                ->icon('heroicon-o-printer')
                ->color('primary'),
            Actions\EditAction::make()
                ->color('warning'),
            Actions\DeleteAction::make(),
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Informasi Form Tugas')
                    ->icon('heroicon-o-document-text')
                    ->schema([
                        TextEntry::make('no_form')
                            ->label('No. Form')
                            ->weight(FontWeight::Bold)
                            ->copyable()
                            ->color('primary')
                            ->size(TextEntry\TextEntrySize::Large),

                        // TextEntry::make('user.name')
                        //     ->label('User')
                        //     ->badge()
                        //     ->color('success'),

                        TextEntry::make('nama_atasan')
                            ->label('Nama Atasan')
                            ->icon('heroicon-o-user'),

                        TextEntry::make('pemohon')
                            ->label('Pemohon')
                            ->icon('heroicon-o-user'),

                        TextEntry::make('penerima_tugas')
                            ->label('Penerima Tugas')
                            ->badge()
                            ->color('info')
                            ->columnSpanFull(),
                    ])
                    ->columns(2)
                    ->collapsible(),

                Section::make('Periode & Deskripsi')
                    ->icon('heroicon-o-calendar-days')
                    ->schema([
                        TextEntry::make('tanggal_mulai')
                            ->label('Tanggal Mulai')
                            ->dateTime('d F Y')
                            ->icon('heroicon-o-calendar')
                            ->color('success'),

                        TextEntry::make('tanggal_selesai')
                            ->label('Tanggal Selesai')
                            ->dateTime('d F Y')
                            ->icon('heroicon-o-calendar')
                            ->color('danger'),

                        TextEntry::make('durasi')
                            ->label('Durasi')
                            ->state(function ($record) {
                                if ($record->tanggal_mulai && $record->tanggal_selesai) {
                                    $diff = $record->tanggal_mulai->diffInDays($record->tanggal_selesai) + 1;
                                    $hours = $record->tanggal_mulai->diffInHours($record->tanggal_selesai) % 24;

                                    $result = '';
                                    if ($diff > 0) {
                                        $result .= $diff . ' hari';
                                    }
                                    if ($hours > 0) {
                                        $result .= ($result ? ' ' : '') . $hours . ' jam';
                                    }
                                    if (empty($result)) {
                                        $minutes = $record->tanggal_mulai->diffInMinutes($record->tanggal_selesai);
                                        $result = $minutes . ' menit';
                                    }

                                    return $result;
                                }
                                return '-';
                            })
                            ->icon('heroicon-o-clock')
                            ->badge()
                            ->color('info')
                            ->weight(FontWeight::Bold),

                        TextEntry::make('deskripsi')
                            ->label('Deskripsi Tugas')
                            ->markdown()
                            ->columnSpanFull(),
                    ])
                    ->columns(3)
                    ->collapsible(),

                Section::make('Unit Kendaraan')
                    ->icon('heroicon-o-truck')
                    ->schema([
                        TextEntry::make('unit.nopol')
                            ->label('Nomor Polisi')
                            ->badge()
                            ->color('warning')
                            ->size(TextEntry\TextEntrySize::Large)
                            ->weight(FontWeight::Bold),

                        TextEntry::make('unit.merk')
                            ->label('Merk'),

                        TextEntry::make('unit.type')
                            ->label('Type'),

                        TextEntry::make('lainnya')
                            ->label('Informasi Lainnya')
                            ->placeholder('Tidak ada')
                            ->columnSpanFull(),
                    ])
                    ->columns(4)
                    ->collapsible(),

                Section::make('Rincian Biaya')
                    ->icon('heroicon-o-currency-dollar')
                    ->schema([
                        TextEntry::make('bbm')
                            ->label('BBM')
                            ->money('IDR')
                            ->color('success'),

                        TextEntry::make('toll')
                            ->label('Toll')
                            ->money('IDR')
                            ->color('info'),

                        TextEntry::make('penginapan')
                            ->label('Penginapan')
                            ->money('IDR')
                            ->color('warning'),

                        TextEntry::make('uang_dinas')
                            ->label('Uang Dinas')
                            ->money('IDR')
                            ->color('primary'),

                        TextEntry::make('entertaint_customer')
                            ->label('Entertaint Customer')
                            ->money('IDR')
                            ->color('secondary'),

                        TextEntry::make('total')
                            ->label('Total Biaya')
                            ->money('IDR')
                            ->weight(FontWeight::Bold)
                            ->size(TextEntry\TextEntrySize::Large)
                            ->color('danger'),
                    ])
                    ->columns(3)
                    ->collapsible(),

                Section::make('Daftar Tujuan Tugas')
                    ->icon('heroicon-o-map-pin')
                    ->schema([
                        RepeatableEntry::make('tujuanTugas')
                            ->label('')
                            ->schema([
                                TextEntry::make('tanggal')
                                    ->label('Tanggal')
                                    ->date('d F Y')
                                    ->icon('heroicon-o-calendar')
                                    ->color('primary'),

                                TextEntry::make('tempat')
                                    ->label('Tempat')
                                    ->weight(FontWeight::Bold)
                                    ->icon('heroicon-o-building-office-2'),

                                TextEntry::make('location')
                                    ->label('Lokasi')
                                    ->icon('heroicon-o-map-pin')
                                    ->placeholder('Tidak ada'),

                                TextEntry::make('keterangan')
                                    ->label('Keterangan')
                                    ->placeholder('Tidak ada keterangan')
                                    ->columnSpanFull(),
                            ])
                            ->columns(3)
                            ->columnSpanFull(),
                    ])
                    ->collapsible(),
            ]);
    }
}
