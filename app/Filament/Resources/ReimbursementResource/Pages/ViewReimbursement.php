<?php

namespace App\Filament\Resources\ReimbursementResource\Pages;

use App\Filament\Resources\ReimbursementResource;
use App\Models\Reimbursement;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components;

class ViewReimbursement extends ViewRecord
{
    protected static string $resource = ReimbursementResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Components\Section::make('Informasi User')
                    ->schema([
                        Components\TextEntry::make('user.name')
                            ->label('Nama User')
                            ->badge()
                            ->color('info'),

                        Components\TextEntry::make('created_at')
                            ->label('Tanggal Dibuat')
                            ->date('d F Y, H:i:s'),
                    ])
                    ->columns(2),

                Components\Section::make('Data Odometer Awal')
                    ->schema([
                        Components\TextEntry::make('km_awal')
                            ->label('KM Awal')
                            ->formatStateUsing(fn ($state) => number_format($state) . ' KM')
                            ->badge()
                            ->color('success'),

                        Components\ImageEntry::make('foto_odometer_awal')
                            ->label('Foto Odometer Awal')
                            ->width('100%')
                            ->height('auto')
                            ->extraImgAttributes([
                                'class' => 'rounded-lg shadow-lg',
                                'style' => 'max-height: 500px; object-fit: contain;'
                            ])
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Components\Section::make('Data Odometer Akhir')
                    ->schema([
                        Components\TextEntry::make('km_akhir')
                            ->label('KM Akhir')
                            ->formatStateUsing(fn ($state) => $state ? number_format($state) . ' KM' : '-')
                            ->badge()
                            ->color('warning')
                            ->placeholder('-'),

                        Components\TextEntry::make('jarak_tempuh')
                            ->label('Jarak Tempuh')
                            ->state(function (Reimbursement $record) {
                                if ($record->km_akhir && $record->km_awal) {
                                    return number_format($record->km_akhir - $record->km_awal) . ' KM';
                                }
                                return '-';
                            })
                            ->badge()
                            ->color('info'),

                        Components\ImageEntry::make('foto_odometer_akhir')
                            ->label('Foto Odometer Akhir')
                            ->width('100%')
                            ->height('auto')
                            ->extraImgAttributes([
                                'class' => 'rounded-lg shadow-lg',
                                'style' => 'max-height: 500px; object-fit: contain;'
                            ])
                            ->placeholder('-')
                            ->columnSpanFull(),
                    ])
                    ->columns(2)
                    ->collapsible(),

                Components\Section::make('Detail Perjalanan')
                    ->schema([
                        Components\TextEntry::make('tujuan_perjalanan')
                            ->label('Tujuan Perjalanan')
                            ->placeholder('-'),

                        Components\TextEntry::make('keterangan')
                            ->label('Keterangan')
                            ->placeholder('-')
                            ->columnSpanFull(),
                        Components\ImageEntry::make('nota')
                            ->label('Foto Nota')
                            ->width('100%')
                            ->height('auto')
                            ->extraImgAttributes([
                                'class' => 'rounded-lg shadow-lg',
                                'style' => 'max-height: 500px; object-fit: contain;'
                            ])
                            ->placeholder('-')
                            ->columnSpanFull(),
                    ])
                    ->columns(2)
                    ->collapsible(),

                Components\Section::make('Informasi Dana')
                    ->schema([
                        Components\TextEntry::make('dana_masuk')
                            ->label('Dana Masuk')
                            ->money('IDR')
                            ->placeholder('-'),

                        Components\TextEntry::make('dana_keluar')
                            ->label('Dana Keluar')
                            ->money('IDR')
                            ->placeholder('-'),

                        Components\TextEntry::make('saldo')
                            ->label('Saldo')
                            ->state(function (Reimbursement $record) {
                                $masuk = $record->dana_masuk ?? 0;
                                $keluar = $record->dana_keluar ?? 0;
                                $saldo = $masuk - $keluar;
                                return 'Rp ' . number_format($saldo, 0, ',', '.');
                            })
                            ->badge()
                            ->color(function (Reimbursement $record) {
                                $masuk = $record->dana_masuk ?? 0;
                                $keluar = $record->dana_keluar ?? 0;
                                $saldo = $masuk - $keluar;
                                if ($saldo > 0) return 'success';
                                if ($saldo < 0) return 'danger';
                                return 'warning';
                            }),
                    ])
                    ->columns(3)
                    ->collapsible(),

                Components\Section::make('Informasi Tambahan')
                    ->schema([
                        Components\TextEntry::make('created_at')
                            ->label('Dibuat Pada')
                            ->dateTime('d F Y, H:i:s'),

                        Components\TextEntry::make('updated_at')
                            ->label('Diperbarui Pada')
                            ->dateTime('d F Y, H:i:s'),
                    ])
                    ->columns(2)
                    ->collapsed(),
            ]);
    }
}
