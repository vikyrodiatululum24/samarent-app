<?php

namespace App\Filament\Resources\KunciSereps\Schemas;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Infolists\Components\TextEntry;

class KunciSerepInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Data Kunci')
                    ->schema([
                        TextEntry::make('unit.nopol')
                            ->label('Unit')
                            ->placeholder('-'),
                        TextEntry::make('no_kunci')
                            ->label('No. Kunci')
                            ->placeholder('-'),
                        TextEntry::make('lokasi')
                            ->label('Lokasi')
                            ->placeholder('-'),
                        TextEntry::make('status_kunci')
                            ->label('Status Kunci')
                            ->badge()
                            ->formatStateUsing(fn (string $state): string => strtoupper($state))
                            ->color(fn (string $state): string => match ($state) {
                                'tersedia' => 'success',
                                'diambil' => 'warning',
                                default => 'gray',
                            }),
                    ])->columns(2),

                Section::make('Riwayat & Keterangan')
                    ->schema([
                        TextEntry::make('tanggal_masuk')
                            ->label('Tanggal Masuk')
                            ->date()
                            ->placeholder('-'),
                        TextEntry::make('tanggal_keluar')
                            ->label('Tanggal Keluar')
                            ->date()
                            ->placeholder('-'),
                        TextEntry::make('diambil_oleh')
                            ->label('Diambil Oleh')
                            ->placeholder('-'),
                        TextEntry::make('keterangan')
                            ->label('Keterangan')
                            ->columnSpanFull()
                            ->placeholder('-'),
                    ])->columns(2),
            ]);
    }
}
