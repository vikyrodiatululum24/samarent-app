<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use App\Models\Complete;
use App\Models\ServiceUnit;
use App\Models\Finance;

class CleanUnusedImages extends Command
{
    protected $signature = 'images:clean';
    protected $description = 'Hapus file gambar yang tidak ada di database';

    /**
     * Definisi model dan kolom gambar yang akan dibersihkan
     */
    private array $modelConfig = [
        Complete::class => [
            'foto_nota' => 'array',
        ],
        ServiceUnit::class => [
            'foto_unit' => 'string',
            'foto_odometer' => 'string',
            'foto_kondisi' => 'array',
            'foto_pengerjaan_bengkel' => 'string',
            'foto_tambahan' => 'array'
        ],
        Finance::class => [
            'bukti_transaksi' => 'string'
        ]
    ];

    public function handle()
    {
        $this->info('Mulai membersihkan file tidak terpakai...');
        $totalDeleted = 0;

        // Proses setiap model
        foreach ($this->modelConfig as $modelClass => $fields) {
            $this->info("\nMemproses model: " . class_basename($modelClass));

            // Proses setiap field foto dalam model
            foreach ($fields as $field => $type) {
                $deletedCount = $this->cleanupFieldFiles($modelClass, $field, $type);
                $totalDeleted += $deletedCount;
            }
        }

        $this->info("\nProses selesai! Total file yang dihapus: {$totalDeleted}");
    }

    /**
     * Membersihkan file tidak terpakai untuk satu field
     */
    private function cleanupFieldFiles(string $modelClass, string $field, string $type): int
    {
        $this->info("\nMemeriksa field: {$field}");

        // 1. Dapatkan semua file dari storage
        $allFiles = Storage::disk('public')->files($field);
        if (empty($allFiles)) {
            $this->info("Tidak ada file di folder {$field}");
            return 0;
        }
        $this->info("Ditemukan " . count($allFiles) . " file di storage");

        // 2. Dapatkan file yang digunakan dari database
        $usedFiles = [];
        $records = $modelClass::whereNotNull($field)->get([$field]);

        foreach ($records as $record) {
            $value = $record->$field;

            if ($type === 'array' && is_array($value)) {
                // Untuk field array seperti foto_nota
                foreach ($value as $path) {
                    if ($path) {
                        $usedFiles[] = $path;
                    }
                }
            } elseif ($type === 'string' && $value) {
                // Untuk field string seperti bukti_transaksi
                $usedFiles[] = $value;
            }
        }

        // 3. Hilangkan duplikat
        $usedFiles = array_unique($usedFiles);
        $this->info("Ditemukan " . count($usedFiles) . " file yang digunakan di database");

        // 4. Hapus file yang tidak digunakan
        $deletedCount = 0;
        foreach ($allFiles as $file) {
            if (!in_array(basename($file), array_map('basename', $usedFiles))) {
                Storage::disk('public')->delete($file);
                $this->line("Dihapus: {$file}");
                $deletedCount++;
            }
        }

        if ($deletedCount > 0) {
            $this->info("Berhasil menghapus {$deletedCount} file tidak terpakai dari {$field}");
        } else {
            $this->info("Tidak ada file yang perlu dihapus di {$field}");
        }

        return $deletedCount;
    }
}
