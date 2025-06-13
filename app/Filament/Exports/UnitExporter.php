<?php

namespace App\Filament\Exports;

use App\Models\Unit;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class UnitExporter extends Exporter
{
    protected static ?string $model = Unit::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')
                ->label('ID'),
            ExportColumn::make('no_rks'),
            ExportColumn::make('penyerahan_unit'),
            ExportColumn::make('jenis'),
            ExportColumn::make('merk'),
            ExportColumn::make('type'),
            ExportColumn::make('nopol'),
            ExportColumn::make('no_rangka'),
            ExportColumn::make('no_mesin'),
            ExportColumn::make('tgl_pajak'),
            ExportColumn::make('regional'),
            ExportColumn::make('created_at'),
            ExportColumn::make('updated_at'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your unit export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
