<?php

namespace App\Filament\Exports;

use App\Models\Gs;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class GsExporter extends Exporter
{
    protected static ?string $model = Gs::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')->label('ID'),
            ExportColumn::make('driver.user.name')->label('Driver'),
            ExportColumn::make('no_hp')->label('No. HP Driver'),
            ExportColumn::make('alasan')->label('Alasan'),
            ExportColumn::make('project')->label('Project'),
            ExportColumn::make('user')->label('User'),
            ExportColumn::make('no_hp_user')->label('No. HP User'),
            ExportColumn::make('lokasi')->label('Lokasi'),
            ExportColumn::make('unit.nopol')->label('Unit'),
            ExportColumn::make('jam_standby_mulai')->label('Jam Standby Mulai'),
            ExportColumn::make('jam_standby_selesai')->label('Jam Standby Selesai'),
            ExportColumn::make('tanggal_mulai')->label('Tanggal Mulai')->state(function (Gs $record) {
                return $record->tanggal_mulai ? $record->tanggal_mulai->format('d M Y') : null;
            }),
            ExportColumn::make('tanggal_selesai')->label('Tanggal Selesai')->state(function (Gs $record) {
                return $record->tanggal_selesai ? $record->tanggal_selesai->format('d M Y') : null;
            }),
            ExportColumn::make('kunci_unit')->label('Kunci Unit'),
            ExportColumn::make('keterangan')->label('Keterangan'),
            ExportColumn::make('driver_pengganti')->label('Driver Pengganti'),
            ExportColumn::make('no_hp_pengganti')->label('No. HP Pengganti'),
            ExportColumn::make('status_progres')->label('Status Progres'),
            ExportColumn::make('status_pembayaran')->label('Status Pembayaran')
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Export data GS telah selesai dan ' . number_format($export->successful_rows) . ' baris berhasil di-export.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' baris gagal di-export.';
        }

        return $body;
    }
}
