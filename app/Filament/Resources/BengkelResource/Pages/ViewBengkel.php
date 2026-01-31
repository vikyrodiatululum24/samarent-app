<?php

namespace App\Filament\Resources\BengkelResource\Pages;

use App\Filament\Resources\BengkelResource;
use Filament\Actions;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;

class ViewBengkel extends ViewRecord
{
    protected static string $resource = BengkelResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\DeleteAction::make(),
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Informasi Bengkel')
                    ->schema([
                        Infolists\Components\TextEntry::make('nama')
                            ->label('Nama Bengkel')
                            ->size(Infolists\Components\TextEntry\TextEntrySize::Large)
                            ->weight('bold'),

                        Infolists\Components\TextEntry::make('keterangan')
                            ->label('Keterangan')
                            ->placeholder('Tidak ada keterangan')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Infolists\Components\Section::make('Alamat Lengkap')
                    ->schema([
                        Infolists\Components\TextEntry::make('provinsi')
                            ->label('Provinsi')
                            ->icon('heroicon-m-map')
                            ->placeholder('-'),

                        Infolists\Components\TextEntry::make('kab_kota')
                            ->label('Kabupaten/Kota')
                            ->icon('heroicon-m-building-office-2')
                            ->placeholder('-'),

                        Infolists\Components\TextEntry::make('kecamatan')
                            ->label('Kecamatan')
                            ->icon('heroicon-m-building-office')
                            ->placeholder('-'),

                        Infolists\Components\TextEntry::make('desa')
                            ->label('Desa/Kelurahan')
                            ->icon('heroicon-m-home')
                            ->placeholder('-'),

                        Infolists\Components\TextEntry::make('alamat')
                            ->label('Alamat Detail')
                            ->icon('heroicon-m-map-pin')
                            ->columnSpanFull()
                            ->placeholder('-'),

                        Infolists\Components\TextEntry::make('g_maps')
                            ->label('Google Maps')
                            ->icon('heroicon-m-globe-alt')
                            ->url(fn ($record) => $record->g_maps)
                            ->openUrlInNewTab()
                            ->placeholder('Tidak ada link')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Infolists\Components\Section::make('Kontak Bengkel')
                    ->schema([
                        Infolists\Components\RepeatableEntry::make('kontakBengkels')
                            ->schema([
                                Infolists\Components\TextEntry::make('nama')
                                    ->label('Nama Kontak')
                                    ->icon('heroicon-m-user'),

                                Infolists\Components\TextEntry::make('no_telp')
                                    ->label('Nomor Telepon')
                                    ->icon('heroicon-m-phone')
                                    ->copyable(),
                            ])
                            ->columns(2)
                            ->columnSpanFull()
                            ->placeholder('Tidak ada kontak'),
                    ])
                    ->collapsed()
                    ->collapsible(),
            ]);
    }
}
