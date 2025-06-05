<?php

namespace App\Filament\Exports;

use App\Models\ServiceUnit;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class ServiceUnitExporter extends Exporter
{
    protected static ?string $model = ServiceUnit::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')->label('ID'),
            ExportColumn::make('pengajuan.created_at')->label('Tanggal Pengajuan'),
            ExportColumn::make('pengajuan.nama')->label('Nama PIC'),
            ExportColumn::make('pengajuan.no_wa')->label('No WA'),
            ExportColumn::make('unit.jenis'),
            ExportColumn::make('unit.type'),
            ExportColumn::make('unit.nopol')->label('Nomor Polisi'),
            ExportColumn::make('odometer'),
            ExportColumn::make('service')->label('Pengajuan/Service'),
            ExportColumn::make('pengajuan.complete.bengkel_estimasi')->label('Bengkel Estimasi'),
            ExportColumn::make('pengajuan.complete.no_telp_bengkel')->label('No Telp Bengkel'),
            ExportColumn::make('pengajuan.complete.nominal_estimasi')->label('Nominal Estimasi'),
            ExportColumn::make('pengajuan.project')->label('Project'),
            ExportColumn::make('pengajuan.up')->label('UP'),
            ExportColumn::make('pengajuan.up_lainnya')->label('UP Lainnya'),
            ExportColumn::make('pengajuan.provinsi')->label('Provinsi'),
            ExportColumn::make('pengajuan.kota')->label('Kota'),
            ExportColumn::make('pengajuan.complete.kode')->label('Kode'),
            ExportColumn::make('pengajuan.no_pengajuan')->label('No Pengajuan'),
            ExportColumn::make('pengajuan.complete.tanggal_masuk_finance')->label('Tanggal Masuk Finance'),
            ExportColumn::make('pengajuan.complete.tanggal_tf_finance')->label('Tanggal Transfer Finance'),
            ExportColumn::make('pengajuan.keterangan')->label('Jenis Pengajuan'),
            ExportColumn::make('pengajuan.complete.nominal_tf_finance')->label('Nominal Transfer Finance'),
            ExportColumn::make('pengajuan.payment_1'),
            ExportColumn::make('pengajuan.bank_1')->label('Nama Bank'),
            ExportColumn::make('pengajuan.norek_1')->label('NoRek'),
            ExportColumn::make('pengajuan.complete.payment_2'),
            ExportColumn::make('pengajuan.complete.bank_2')->label('Nama Bank'),
            ExportColumn::make('pengajuan.complete.norek_2')->label('NoRek'),
            ExportColumn::make('pengajuan.complete.nominal_tf_bengkel')->label('Nominal Transfer ke Bengkel'),
            ExportColumn::make('pengajuan.complete.selesih_tf')->label('Selisih Transfer'),
            ExportColumn::make('pengajuan.complete.tanggal_pengerjaan')->label('Tanggal  Pengerjaan'),
            ExportColumn::make('pengajuan.complete.tanggal_tf_bengkel')->label('Tanggal Transfer ke Bengkel'),
            ExportColumn::make('pengajuan.keterangan_proses')->label('Keterangan Proses Pengajuan'),
            ExportColumn::make('pengajuan.complete.status_finance')->label('Status Finance'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your service unit export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
