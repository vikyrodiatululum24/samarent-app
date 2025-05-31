<?php

namespace App\Filament\Imports;

use App\Models\Unit;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;

class DataUnitImporter extends Importer
{
    protected static ?string $model = Unit::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('no_rks')
                ->requiredMapping()
                ->rules(['max:255']),
            ImportColumn::make('penyerahan_unit'),
            ImportColumn::make('jenis')
                ->requiredMapping()
                ->rules(['required', 'max:255']),
            ImportColumn::make('merk')
                ->requiredMapping()
                ->rules(['required', 'max:255']),
            ImportColumn::make('type')
                ->requiredMapping()
                ->rules(['required', 'max:255']),
            ImportColumn::make('nopol')
                ->requiredMapping()
                ->rules(['required', 'max:255', 'unique:data_units,nopol']),
            ImportColumn::make('no_rangka'),
            ImportColumn::make('no_mesin'),
            ImportColumn::make('tgl_pajak'),
            ImportColumn::make('regional'),
        ];
    }

    public function resolveRecord(): ?Unit
    {
        return Unit::firstOrNew([
            // Update existing records, matching them by `$this->data['column_name']`
            'nopol' => $this->data['nopol'],
        ]);

        return new Unit();
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your data unit import has completed and ' . number_format($import->successful_rows) . ' ' . str('row')->plural($import->successful_rows) . ' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to import.';
        }

        return $body;
    }
}
