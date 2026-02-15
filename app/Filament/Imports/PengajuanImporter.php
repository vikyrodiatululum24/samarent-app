<?php

namespace App\Filament\Imports;

use App\Models\Pengajuan;
use App\Models\Unit;
use Filament\Actions\Imports\Exceptions\RowImportFailedException;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Support\Facades\Log;

class PengajuanImporter extends Importer
{
    protected static ?string $model = Pengajuan::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('nama')
                ->requiredMapping()
                ->example('John Doe')
                ->rules(['required', 'max:255']),
            ImportColumn::make('no_wa')
                ->requiredMapping()
                ->example('081234567890')
                ->rules(['required', 'max:255']),
            ImportColumn::make('project')
                ->requiredMapping()
                ->example('Samarent')
                ->rules(['required', 'max:255']),
            ImportColumn::make('up')
                ->requiredMapping()
                ->example('masukan pilihan "up1" atau "manual" jika up tidak ada di pilihan')
                ->rules(['required', 'max:255']),
            ImportColumn::make('up_lainnya')
                ->example('isi jika up memilih "manual"')
                ->rules(['nullable', 'max:255']),
            ImportColumn::make('provinsi')
                ->requiredMapping()
                ->example('Jawa Barat')
                ->rules(['required', 'max:255']),
            ImportColumn::make('kota')
                ->requiredMapping()
                ->example('Bandung')
                ->rules(['required', 'max:255']),
            ImportColumn::make('keterangan')
                ->requiredMapping()
                ->example('REIMBURSE')
                ->rules(['required']),
            ImportColumn::make('payment_1')
                ->example('Cecep')
                ->rules(['nullable', 'max:255']),
            ImportColumn::make('bank_1')
                ->example('BRI')
                ->rules(['nullable', 'max:255']),
            ImportColumn::make('norek_1')
                ->example('1234567890')
                ->rules(['nullable', 'max:255']),

            // Kolom untuk service units (JANGAN tambahkan ke fillable model!)
            ImportColumn::make('nopol')
                ->label('Nopol (pisahkan dengan | untuk multiple)')
                ->example('jika lebih dari 1 unit, pisahkan dengan |, contoh: B1234ABC|B5678DEF')
                ->rules(['nullable'])
                ->guess(['nopol', 'no_pol', 'nomor_polisi']),
            ImportColumn::make('odometer')
                ->label('Odometer (pisahkan dengan | untuk multiple)')
                ->example('jika lebih dari 1 unit, pisahkan dengan |, contoh: 10000|20000')
                ->rules(['nullable'])
                ->guess(['odometer', 'km']),
            ImportColumn::make('service')
                ->label('Service (pisahkan dengan | untuk multiple)')
                ->example('jika lebih dari 1 unit, pisahkan dengan |, contoh: Ganti Oli|Tune Up')
                ->rules(['nullable'])
                ->guess(['service', 'keterangan_service']),
        ];
    }

    public function resolveRecord(): ?Pengajuan
    {
        $serviceUnitsData = [
            'nopol' => $this->data['nopol'] ?? null,
            'odometer' => $this->data['odometer'] ?? null,
            'service' => $this->data['service'] ?? null,
        ];

        // Memastika jumlah kolom header dan data sama agar tidak ada kesalahan kolom
        $expectedColumns = [
            'nama', 'no_wa', 'project', 'up', 'up_lainnya', 'provinsi', 'kota', 'keterangan',
            'payment_1', 'bank_1', 'norek_1',
            'nopol', 'odometer', 'service',
        ];
        foreach ($expectedColumns as $column) {
            if (!array_key_exists($column, $this->data)) {
                throw new RowImportFailedException("Kolom '{$column}' tidak ditemukan. Pastikan file memiliki header yang benar.");
            }
        }

        // Validasi data service unit
        $nopols = !empty($serviceUnitsData['nopol']) ? array_filter(array_map('trim', explode('|', $serviceUnitsData['nopol']))) : [];
        $odometers = !empty($serviceUnitsData['odometer']) ? array_map('trim', explode('|', $serviceUnitsData['odometer'])) : [];
        $services = !empty($serviceUnitsData['service']) ? array_map('trim', explode('|', $serviceUnitsData['service'])) : [];

        if (!empty($nopols) || !empty($odometers) || !empty($services)) {
            $max = max(count($nopols), count($odometers), count($services));
            if (count($nopols) !== $max || count($odometers) !== $max || count($services) !== $max) {
                throw new RowImportFailedException('Jumlah nopol, odometer, dan service tidak sesuai. Pastikan jumlah data dipisahkan dengan | sama banyaknya.');
            }
        }

        try {
            unset($this->data['nopol'], $this->data['odometer'], $this->data['service']);

            Log::info('Membuat pengajuan dengan data:', $this->data);
            $pengajuan = Pengajuan::create([
                'user_id' => auth()->id(),
                'nama' => $this->data['nama'],
                'no_wa' => $this->data['no_wa'],
                'project' => $this->data['project'],
                'up' => $this->data['up'],
                'up_lainnya' => $this->data['up_lainnya'] ?? null,
                'provinsi' => $this->data['provinsi'],
                'kota' => $this->data['kota'],
                'keterangan' => $this->data['keterangan'],
                'payment_1' => $this->data['payment_1'] ?? null,
                'bank_1' => $this->data['bank_1'] ?? null,
                'norek_1' => $this->data['norek_1'] ?? null,
            ]);
        } catch (\Exception $e) {
            Log::error('Gagal membuat pengajuan: ' . $e->getMessage(), ['data' => $this->data]);
            throw new RowImportFailedException('Gagal membuat pengajuan: Periksa kembali data Anda sudah sesuai dengan format yang benar. ');
        }

        if (!empty($serviceUnitsData['nopol']) || !empty($serviceUnitsData['service'])) {
            $this->createServiceUnits($pengajuan, $serviceUnitsData);
        }

        return $pengajuan;
    }

    protected function createServiceUnits(Pengajuan $pengajuan, array $data): void
    {
        $nopols = !empty($data['nopol']) ? array_filter(array_map('trim', explode('|', $data['nopol']))) : [];

        $odometers = !empty($data['odometer']) ? array_map('trim', explode('|', $data['odometer'])) : [];

        $services = !empty($data['service']) ? array_map('trim', explode('|', $data['service'])) : [];

        foreach ($nopols as $index => $nopolValue) {
            $unit = Unit::where('nopol', $nopolValue)->first();

            $pengajuan->service_unit()->create([
                'unit_id' => $unit?->id,
                'odometer' => $odometers[$index] ?? null,
                'service' => $services[$index] ?? null,
            ]);
        }
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Import pengajuan selesai. ' . number_format($import->successful_rows) . ' baris berhasil diimport.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' baris gagal.';
        }

        return $body;
    }
}
