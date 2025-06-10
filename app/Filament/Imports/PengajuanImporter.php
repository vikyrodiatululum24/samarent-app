<?php

namespace App\Filament\Imports;

use App\Models\Pengajuan;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;

class PengajuanImporter extends Importer
{
    protected static ?string $model = Pengajuan::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('no_pengajuan')
                ->requiredMapping()
                ->rules(['required', 'max:255']),
            ImportColumn::make('nama')
                ->requiredMapping()
                ->rules(['required', 'max:255']),
            ImportColumn::make('no_wa')
                ->requiredMapping()
                ->rules(['required', 'max:255']),
            ImportColumn::make('project')
                ->requiredMapping()
                ->rules(['required', 'max:255']),
            ImportColumn::make('up')
                ->requiredMapping()
                ->rules(['required', 'max:255']),
            ImportColumn::make('up_lainnya')
                ->rules(['max:255']),
            ImportColumn::make('provinsi')
                ->requiredMapping()
                ->rules(['required', 'max:255']),
            ImportColumn::make('kota')
                ->requiredMapping()
                ->rules(['required', 'max:255']),
            ImportColumn::make('keterangan')
                ->requiredMapping()
                ->rules(['required']),
            ImportColumn::make('payment_1')
                ->rules(['max:255']),
            ImportColumn::make('bank_1')
                ->rules(['max:255']),
            ImportColumn::make('norek_1')
                ->rules(['max:255']),
            ImportColumn::make('keterangan_proses')
                ->requiredMapping()
                ->rules(['required', 'max:255']),
        ];
    }

    public function resolveRecord(): ?Pengajuan
    {
        return Pengajuan::firstOrNew([
            // Update existing records, matching them by `$this->data['column_name']`
            'no_pengajuan' => $this->data['no_pengajuan'],
        ]);

        return new Pengajuan();
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your pengajuan import has completed and ' . number_format($import->successful_rows) . ' ' . str('row')->plural($import->successful_rows) . ' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to import.';
        }

        return $body;
    }
}
