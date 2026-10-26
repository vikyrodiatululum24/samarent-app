<?php

namespace App\Filament\Exports;

use App\Models\AngsuranKendaraan;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class AngsuranKendaraanExporter extends Exporter
{
    protected static ?string $model = AngsuranKendaraan::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')
                ->label('No'),
            ExportColumn::make('driver.user.name')
                ->label('Nama'),
            ExportColumn::make('unit.nopol')
                ->label('No Pol'),
            ExportColumn::make('tgl_serah_terima')
                ->label('Tgl Serah Terima'),
            ExportColumn::make('tgl_jatuh_tempo_akhir')
                ->label('Masa Akhir Kontrak Sewa'),
            ExportColumn::make('jangka_waktu_tahun')
                ->label('Jangka Waktu / Tahun'),
            ExportColumn::make('dp')
                ->label('DP'),
            ExportColumn::make('tgl_dp')
                ->label('Tgl DP'),
            ExportColumn::make('frekuensi_per_bulan')
                ->label('Frekuensi perbulan'),
            ExportColumn::make('angsuran_per_hari')
                ->label('Angs perhari'),
            ExportColumn::make('normal_angsuran')
                ->label('Normal Angsuran'),
            ExportColumn::make('nilai_kontrak')
                ->label('Nilai Kontrak'),
            ExportColumn::make('deposit')
                ->label('Deposit (3x Angsuran)'),
            ExportColumn::make('tgl_deposit')
                ->label('Tgl Deposit'),
            ExportColumn::make('total_setoran_keseluruhan')
                ->label('Total Setoran Terkumpul'),
            ExportColumn::make('status')
                ->label('Status'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Ekspor data angsuran kendaraan selesai. Sebanyak ' . number_format($export->successful_rows) . ' baris berhasil diekspor.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' baris gagal diekspor.';
        }

        return $body;
    }
}
