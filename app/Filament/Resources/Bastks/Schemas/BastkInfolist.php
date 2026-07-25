<?php

namespace App\Filament\Resources\Bastks\Schemas;

use Filament\Infolists;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\HtmlString;

class BastkInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Utama BASTK')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                Infolists\Components\TextEntry::make('kode')
                                    ->label('Kode BASTK')
                                    ->weight('bold')
                                    ->copyable(),
                                Infolists\Components\TextEntry::make('unit.nopol')
                                    ->label('No. Polisi Unit')
                                    ->weight('bold')
                                    ->formatStateUsing(fn ($record) => $record->unit ? "{$record->unit->type} - {$record->unit->nopol}" : '-'),
                                Infolists\Components\TextEntry::make('kepada')
                                    ->label('Kepada'),
                                Infolists\Components\TextEntry::make('no_hp')
                                    ->label('No. HP'),
                                Infolists\Components\TextEntry::make('tgl_serah')
                                    ->label('Tanggal Serah')
                                    ->date(),
                                Infolists\Components\TextEntry::make('tgl_kembali')
                                    ->label('Tanggal Kembali')
                                    ->date(),
                                Infolists\Components\TextEntry::make('nama_penyerah')
                                    ->label('Nama Penyerah'),
                                Infolists\Components\TextEntry::make('nama_penerima')
                                    ->label('Nama Penerima'),
                                Infolists\Components\TextEntry::make('alamat')
                                    ->label('Alamat')
                                    ->columnSpanFull(),
                                Infolists\Components\TextEntry::make('kondisi_unit')
                                    ->label('Kondisi Unit')
                                    ->columnSpanFull(),
                                Infolists\Components\TextEntry::make('exchange')
                                    ->label('Exchange')
                                    ->columnSpanFull()
                                    ->visible(fn($record) => in_array('exchange', $record->kondisi_unit ?? [])),
                                Infolists\Components\TextEntry::make('keterangan')
                                    ->label('Keterangan')
                                    ->columnSpanFull(),
                            ]),
                    ])
                    ->collapsible(),

                Section::make('Kelengkapan & Kondisi Kendaraan')
                    ->schema([
                        Infolists\Components\RepeatableEntry::make('items')
                            ->label('')
                            ->schema([
                                Grid::make(4)
                                    ->schema([
                                        Infolists\Components\TextEntry::make('kelengkapan')
                                            ->hiddenLabel()
                                            ->weight('bold')
                                            ->visible(fn($record) => $record->kelengkapan != "BBM & KM"),
                                        Infolists\Components\TextEntry::make('status_kondisi')
                                            ->label('Kondisi')
                                            ->badge()
                                            ->state(function ($record) {
                                                if ($record->baik) {
                                                    return match ($record->kelengkapan) {
                                                        'Velg Ban' => 'Original',
                                                        'Tutup Dop', 'Apar' => 'Ada',
                                                        default => 'Baik',
                                                    };
                                                }
                                                if ($record->rusak) return $record->kelengkapan === 'Velg Ban' ? 'Racing' : 'Rusak';
                                                if ($record->tidak_ada) return 'Tidak Ada';
                                                return 'N/A';
                                            })
                                            ->color(fn ($state) => match ($state) {
                                                'Baik', 'Original', 'Ada' => 'success',
                                                'Rusak', 'Racing' => 'danger',
                                                'Tidak Ada' => 'warning',
                                                default => 'gray',
                                            })
                                            ->visible(fn($record) => $record->kelengkapan != "BBM & KM"),

                                        // BBM & KM row (custom indicator)
                                        Infolists\Components\TextEntry::make('bbm_indicator')
                                            ->hiddenLabel()
                                            ->state(function ($record) {
                                                if ($record->kelengkapan !== 'BBM & KM') return null;
                                                $bars = (int) $record->bbm;
                                                $jenisBbm = $record->jenis_bbm ?? '-';
                                                $km = number_format((int) $record->km) . ' km';

                                                $html = '<div style="display: flex; align-items: center; gap: 12px; flex-wrap: wrap;">';

                                                // Fuel bar indicator
                                                $html .= '<div style="display: flex; align-items: flex-end; gap: 3px;">';
                                                $html .= '<span style="font-size: 11px; color: #6b7280; margin-right: 4px; font-weight: 600;">E</span>';
                                                for ($i = 1; $i <= 8; $i++) {
                                                    $active = $i <= $bars;
                                                    $color = $active ? '#22c55e' : '#e5e7eb';
                                                    $border = $active ? '#16a34a' : '#d1d5db';
                                                    // Progressive height for fuel bar effect
                                                    $height = 14 + ($i * 2);
                                                    $html .= "<div style='width: 14px; height: {$height}px; background-color: {$color}; border: 1px solid {$border}; border-radius: 2px;' title='Bar {$i}'></div>";
                                                }
                                                $html .= '<span style="font-size: 11px; color: #6b7280; margin-left: 4px; font-weight: 600;">F</span>';
                                                $html .= '</div>';

                                                // BBM count + Jenis BBM
                                                $html .= "<div style='display: flex; flex-direction: column; gap: 2px;'>";
                                                $html .= "<span style='font-size: 13px; font-weight: bold; color: #111827;'>{$bars} / 8 Bar</span>";
                                                $html .= "<span style='font-size: 11px; color: #6b7280;'>{$jenisBbm}</span>";
                                                $html .= "</div>";

                                                $html .= "</div>";

                                                return new HtmlString($html);
                                            })
                                            ->html()
                                            ->visible(fn($record) => $record->kelengkapan == "BBM & KM")
                                            ->columnSpan(2),

                                        Infolists\Components\TextEntry::make('km')
                                            ->label('KM Kendaraan')
                                            ->formatStateUsing(fn ($state) => number_format((int) $state) . ' km')
                                            ->badge()
                                            ->color('info')
                                            ->visible(fn($record) => $record->kelengkapan == "BBM & KM"),

                                        Infolists\Components\TextEntry::make('keterangan')
                                            ->label('Keterangan')
                                            ->visible(fn($record) => $record->kelengkapan != "BBM & KM"),
                                    ]),
                            ]),
                    ])
                    ->collapsible(),

                Section::make('Dokumentasi Foto')
                    ->schema([
                        Grid::make(4)
                            ->schema([
                                Infolists\Components\ImageEntry::make('dokumentasi.unit_depan')
                                    ->label('Unit Depan')
                                    ->disk('public')
                                    ->height(120)
                                    ->extraImgAttributes(['style' => 'cursor: zoom-in; border-radius: 6px;'])
                                    ->url(fn ($record) => $record->dokumentasi?->unit_depan ? asset('storage/' . $record->dokumentasi->unit_depan) : null)
                                    ->openUrlInNewTab(),
                                Infolists\Components\ImageEntry::make('dokumentasi.unit_belakang')
                                    ->label('Unit Belakang')
                                    ->disk('public')
                                    ->height(120)
                                    ->extraImgAttributes(['style' => 'cursor: zoom-in; border-radius: 6px;'])
                                    ->url(fn ($record) => $record->dokumentasi?->unit_belakang ? asset('storage/' . $record->dokumentasi->unit_belakang) : null)
                                    ->openUrlInNewTab(),
                                Infolists\Components\ImageEntry::make('dokumentasi.unit_samping_kanan')
                                    ->label('Samping Kanan')
                                    ->disk('public')
                                    ->height(120)
                                    ->extraImgAttributes(['style' => 'cursor: zoom-in; border-radius: 6px;'])
                                    ->url(fn ($record) => $record->dokumentasi?->unit_samping_kanan ? asset('storage/' . $record->dokumentasi->unit_samping_kanan) : null)
                                    ->openUrlInNewTab(),
                                Infolists\Components\ImageEntry::make('dokumentasi.unit_samping_kiri')
                                    ->label('Samping Kiri')
                                    ->disk('public')
                                    ->height(120)
                                    ->extraImgAttributes(['style' => 'cursor: zoom-in; border-radius: 6px;'])
                                    ->url(fn ($record) => $record->dokumentasi?->unit_samping_kiri ? asset('storage/' . $record->dokumentasi->unit_samping_kiri) : null)
                                    ->openUrlInNewTab(),
                                Infolists\Components\ImageEntry::make('dokumentasi.kabin_depan')
                                    ->label('Kabin Depan')
                                    ->disk('public')
                                    ->height(120)
                                    ->extraImgAttributes(['style' => 'cursor: zoom-in; border-radius: 6px;'])
                                    ->url(fn ($record) => $record->dokumentasi?->kabin_depan ? asset('storage/' . $record->dokumentasi->kabin_depan) : null)
                                    ->openUrlInNewTab(),
                                Infolists\Components\ImageEntry::make('dokumentasi.kabin_tengah')
                                    ->label('Kabin Tengah')
                                    ->disk('public')
                                    ->height(120)
                                    ->extraImgAttributes(['style' => 'cursor: zoom-in; border-radius: 6px;'])
                                    ->url(fn ($record) => $record->dokumentasi?->kabin_tengah ? asset('storage/' . $record->dokumentasi->kabin_tengah) : null)
                                    ->openUrlInNewTab(),
                                Infolists\Components\ImageEntry::make('dokumentasi.kabin_belakang')
                                    ->label('Kabin Belakang')
                                    ->disk('public')
                                    ->height(120)
                                    ->extraImgAttributes(['style' => 'cursor: zoom-in; border-radius: 6px;'])
                                    ->url(fn ($record) => $record->dokumentasi?->kabin_belakang ? asset('storage/' . $record->dokumentasi->kabin_belakang) : null)
                                    ->openUrlInNewTab(),
                                Infolists\Components\ImageEntry::make('dokumentasi.dashboard')
                                    ->label('Dashboard')
                                    ->disk('public')
                                    ->height(120)
                                    ->extraImgAttributes(['style' => 'cursor: zoom-in; border-radius: 6px;'])
                                    ->url(fn ($record) => $record->dokumentasi?->dashboard ? asset('storage/' . $record->dokumentasi->dashboard) : null)
                                    ->openUrlInNewTab(),
                                Infolists\Components\ImageEntry::make('dokumentasi.odometer')
                                    ->label('Odometer')
                                    ->disk('public')
                                    ->height(120)
                                    ->extraImgAttributes(['style' => 'cursor: zoom-in; border-radius: 6px;'])
                                    ->url(fn ($record) => $record->dokumentasi?->odometer ? asset('storage/' . $record->dokumentasi->odometer) : null)
                                    ->openUrlInNewTab(),
                                Infolists\Components\ImageEntry::make('dokumentasi.buku_service')
                                    ->label('Buku Service')
                                    ->disk('public')
                                    ->height(120)
                                    ->extraImgAttributes(['style' => 'cursor: zoom-in; border-radius: 6px;'])
                                    ->url(fn ($record) => $record->dokumentasi?->buku_service ? asset('storage/' . $record->dokumentasi->buku_service) : null)
                                    ->openUrlInNewTab(),
                                Infolists\Components\ImageEntry::make('dokumentasi.manual_book')
                                    ->label('Manual Book')
                                    ->disk('public')
                                    ->height(120)
                                    ->extraImgAttributes(['style' => 'cursor: zoom-in; border-radius: 6px;'])
                                    ->url(fn ($record) => $record->dokumentasi?->manual_book ? asset('storage/' . $record->dokumentasi->manual_book) : null)
                                    ->openUrlInNewTab(),
                                Infolists\Components\ImageEntry::make('dokumentasi.ban_serep')
                                    ->label('Ban Serep')
                                    ->disk('public')
                                    ->height(120)
                                    ->extraImgAttributes(['style' => 'cursor: zoom-in; border-radius: 6px;'])
                                    ->url(fn ($record) => $record->dokumentasi?->ban_serep ? asset('storage/' . $record->dokumentasi->ban_serep) : null)
                                    ->openUrlInNewTab(),
                                Infolists\Components\ImageEntry::make('dokumentasi.stnk_depan')
                                    ->label('STNK Depan')
                                    ->disk('public')
                                    ->height(120)
                                    ->extraImgAttributes(['style' => 'cursor: zoom-in; border-radius: 6px;'])
                                    ->url(fn ($record) => $record->dokumentasi?->stnk_depan ? asset('storage/' . $record->dokumentasi->stnk_depan) : null)
                                    ->openUrlInNewTab(),
                                Infolists\Components\ImageEntry::make('dokumentasi.stnk_belakang')
                                    ->label('STNK Belakang')
                                    ->disk('public')
                                    ->height(120)
                                    ->extraImgAttributes(['style' => 'cursor: zoom-in; border-radius: 6px;'])
                                    ->url(fn ($record) => $record->dokumentasi?->stnk_belakang ? asset('storage/' . $record->dokumentasi->stnk_belakang) : null)
                                    ->openUrlInNewTab(),
                                Infolists\Components\ImageEntry::make('dokumentasi.bastk')
                                    ->label('File Scan BASTK')
                                    ->disk('public')
                                    ->height(120)
                                    ->extraImgAttributes(['style' => 'cursor: zoom-in; border-radius: 6px;'])
                                    ->url(fn ($record) => $record->dokumentasi?->bastk ? asset('storage/' . $record->dokumentasi->bastk) : null)
                                    ->openUrlInNewTab(),
                            ]),
                        Grid::make(2)
                            ->schema([
                                Infolists\Components\ImageEntry::make('dokumentasi.kerusakan')
                                    ->label('Foto Kerusakan')
                                    ->disk('public')
                                    ->height(120)
                                    ->extraImgAttributes(['style' => 'cursor: zoom-in; border-radius: 6px;']),
                                Infolists\Components\ImageEntry::make('dokumentasi.tools')
                                    ->label('Foto Tools / Peralatan')
                                    ->disk('public')
                                    ->height(120)
                                    ->extraImgAttributes(['style' => 'cursor: zoom-in; border-radius: 6px;']),
                            ]),
                    ])
                    ->collapsible(),
            ])
            ->columns(1);
    }
}
