<?php

namespace App\Filament\Absensi\Resources\Gs\Schemas;

use Carbon\Carbon;
use Dom\Text;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class GsInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Data Driver Original')
                    ->schema([
                        TextEntry::make('driver.user.name')
                            ->label('Driver')
                            ->placeholder('-'),
                        TextEntry::make('no_hp')
                            ->label('No. HP Driver')
                            ->placeholder('-'),
                        TextEntry::make('alasan')
                            ->label('Alasan')
                            ->placeholder('-')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Section::make('Data User')
                    ->schema([
                        TextEntry::make('project')
                            ->label('Project')
                            ->placeholder('-'),
                        TextEntry::make('user')
                            ->label('User')
                            ->placeholder('-'),
                        TextEntry::make('no_hp_user')
                            ->label('No. HP User')
                            ->placeholder('-'),
                    ])
                    ->columns(3),

                Section::make('Detail')
                    ->schema([
                        TextEntry::make('lokasi')
                            ->label('Lokasi')
                            ->placeholder('-')
                            ->columnSpanFull(),
                        TextEntry::make('unit.nopol')
                            ->label('Unit')
                            ->placeholder('-'),
                        TextEntry::make('kunci_unit')
                            ->label('Kunci Unit')
                            ->placeholder('-'),
                        Section::make('')
                            ->schema([

                                TextEntry::make('tanggal_mulai')
                                    ->label('Tanggal Mulai')
                                    ->date()
                                    ->placeholder('-'),
                                TextEntry::make('tanggal_selesai')
                                    ->label('Tanggal Selesai')
                                    ->date()
                                    ->placeholder('-'),
                                TextEntry::make('jumlah_hari')
                                    ->label('Jumlah Hari')
                                    ->state(function ($record) {
                                        if (!$record->tanggal_mulai) {
                                                return '-';
                                            }

                                            if (!$record->tanggal_selesai) {
                                                return '1 hari';
                                            }

                                            $start = Carbon::parse($record->tanggal_mulai);
                                            $end = Carbon::parse($record->tanggal_selesai);

                                            return ($start->diffInDays($end) + 1) . ' hari';
                                    })
                                    ->placeholder('-'),
                            ])
                            ->columns(3)
                            ->columnSpanFull(),

                        TextEntry::make('jam_standby_mulai')
                            ->label('Jam Standby Mulai')
                            ->placeholder('-'),
                        TextEntry::make('jam_standby_selesai')
                            ->label('Jam Standby Selesai')
                            ->placeholder('-'),
                        TextEntry::make('keterangan')
                            ->label('Keterangan')
                            ->placeholder('-')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Section::make('Driver Pengganti')
                    ->schema([
                        TextEntry::make('driver_pengganti')
                            ->label('Nama Driver Pengganti')
                            ->placeholder('-'),
                        TextEntry::make('no_hp_pengganti')
                            ->label('No. HP Pengganti')
                            ->placeholder('-'),
                    ])
                    ->columns(2),
            ]);
    }
}
