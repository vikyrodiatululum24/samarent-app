<?php

namespace App\Filament\Exports;

use App\Models\Pengajuan;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class PengajuanExporter extends Exporter
{
    protected static ?string $model = Pengajuan::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')->label('ID'),
            ExportColumn::make('created_at')->label('Tanggal Pengajuan'),
            ExportColumn::make('no_pengajuan')->label('No Pengajuan'),
            ExportColumn::make('nama')->label('Nama PIC'),
            ExportColumn::make('no_wa')->label('No WA'),
            ExportColumn::make('jenis'),
            ExportColumn::make('type'),
            ExportColumn::make('nopol')->label('Nomor Polisi'),
            ExportColumn::make('odometer'),
            ExportColumn::make('service')->label('Pengajuan/Service'),
            ExportColumn::make('complete.bengkel_estimasi')->label('Bengkel Estimasi'),
            ExportColumn::make('complete.no_telp_bengkel')->label('No Telp Bengkel'),
            ExportColumn::make('complete.nominal_estimasi')->label('Nominal Estimasi'),
            ExportColumn::make('project')->label('Project'),
            ExportColumn::make('up')->label('UP'),
            ExportColumn::make('up_lainnya')->label('UP Lainnya'),
            ExportColumn::make('provinsi')->label('Provinsi'),
            ExportColumn::make('kota')->label('Kota'),
            ExportColumn::make('complete.kode')->label('Kode'),
            ExportColumn::make('complete.tanggal_masuk_finance')->label('Tanggal Masuk Finance'),
            ExportColumn::make('complete.tanggal_tf_finance')->label('Tanggal Transfer Finance'),
            ExportColumn::make('keterangan')->label('Jenis Pengajuan'),
            ExportColumn::make('complete.nominal_tf_finance')->label('Nominal Transfer Finance'),
            ExportColumn::make('payment_1'),
            ExportColumn::make('bank_1')->label('Nama Bank'),
            ExportColumn::make('norek_1')->label('NoRek'),
            ExportColumn::make('complete.payment_2'),
            ExportColumn::make('complete.bank_2')->label('Nama Bank'),
            ExportColumn::make('complete.norek_2')->label('NoRek'),
            ExportColumn::make('complete.nominal_tf_bengkel')->label('Nominal Transfer ke Bengkel'),
            ExportColumn::make('complete.selesih_tf')->label('Selisih Transfer'),
            ExportColumn::make('complete.tanggal_pengerjaan')->label('Tanggal  Pengerjaan'),
            ExportColumn::make('complete.tanggal_tf_bengkel')->label('Tanggal Transfer ke Bengkel'),
            ExportColumn::make('keterangan_proses')->label('Keterangan Proses Pengajuan'),
            ExportColumn::make('complete.status_finance')->label('Status Finance'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your pengajuan export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
