<?php

namespace App\Filament\Resources\KunciSereps\Schemas;

use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;

class KunciSerepForm
{
    public static function getLokasiOptions(): array
    {
        $options = [];
        for ($i = 1; $i <= 6; $i++) {
            foreach (['a', 'b', 'c', 'd'] as $char) {
                $options["box $i-$char"] = strtoupper("box $i-$char");
            }
        }
        return $options;
    }

    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make('Data Kunci')
                    ->schema([
                        Forms\Components\Select::make('unit_id')
                            ->relationship('unit', 'nopol')
                            ->label('Unit')
                            ->searchable()
                            ->required()
                            ->unique(ignoreRecord: true),
                        Forms\Components\TextInput::make('no_kunci')
                            ->label('No. Kunci')
                            ->maxLength(255),
                        Forms\Components\Select::make('status_kunci')
                            ->label('Status Kunci')
                            ->options([
                                'tersedia' => 'Tersedia',
                                'diambil' => 'Diambil',
                            ])
                            ->default('tersedia')
                            ->required()
                            ->live()
                            ->afterStateUpdated(function (Set $set, $state) {
                                if ($state === 'tersedia') {
                                    $set('tanggal_keluar', null);
                                    $set('diambil_oleh', null);
                                }
                            }),
                        Forms\Components\Select::make('lokasi')
                            ->label('Lokasi')
                            ->options(self::getLokasiOptions())
                            ->searchable()
                            ->afterStateHydrated(fn (Set $set, ?string $state) => $state ? $set('lokasi', strtolower($state)) : null)
                            ->disabled(fn (Get $get) => $get('status_kunci') === 'diambil')
                            ->dehydrated()
                            ->required(fn (Get $get) => $get('status_kunci') === 'tersedia'),
                    ])->columns(2),

                Section::make('Riwayat & Keterangan')
                    ->schema([
                        Forms\Components\DatePicker::make('tanggal_masuk')
                            ->label('Tanggal Masuk')
                            ->default(now()),
                        Forms\Components\DatePicker::make('tanggal_keluar')
                            ->label('Tanggal Keluar')
                            ->hidden(fn (Get $get) => $get('status_kunci') === 'tersedia')
                            ->dehydrated()
                            ->required(fn (Get $get) => $get('status_kunci') === 'diambil'),
                        Forms\Components\TextInput::make('diambil_oleh')
                            ->label('Diambil Oleh')
                            ->maxLength(255)
                            ->hidden(fn (Get $get) => $get('status_kunci') === 'tersedia')
                            ->dehydrated()
                            ->required(fn (Get $get) => $get('status_kunci') === 'diambil'),
                        Forms\Components\Textarea::make('keterangan')
                            ->label('Keterangan')
                            ->columnSpanFull(),
                    ])->columns(2),
            ]);
    }
}
