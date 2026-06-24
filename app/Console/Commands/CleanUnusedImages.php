<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use App\Models\Complete;
use App\Models\DriverAttendence;
use App\Models\DriverCheck;
use App\Models\Finance;
use App\Models\Reimbursement;
use App\Models\ServiceUnit;

class CleanUnusedImages extends Command
{
    protected $signature = 'images:clean';
    protected $description = 'Hapus file gambar yang tidak ada di database';

    /**
     * Definisi model dan kolom gambar yang akan dibersihkan.
     *
     * Format setiap field:
     *   'nama_field' => ['type' => 'string|array', 'folder' => 'nama-folder-di-storage']
     *
     * Jika 'folder' tidak diisi, maka folder dianggap sama dengan nama field.
     */
    private array $modelConfig = [
        Complete::class => [
            'foto_nota'    => ['type' => 'array'],
        ],
        ServiceUnit::class => [
            'foto_unit'                => ['type' => 'string'],
            'foto_odometer'            => ['type' => 'string'],
            'foto_kondisi'             => ['type' => 'array'],
            'foto_pengerjaan_bengkel'  => ['type' => 'string'],
            'foto_tambahan'            => ['type' => 'array'],
        ],
        Finance::class => [
            'bukti_transaksi' => ['type' => 'string'],
        ],
        DriverCheck::class => [
            // Field di DB: 'photo_check', tapi folder di storage: 'absen'
            'photo' => ['type' => 'string', 'folder' => 'absen/photo_check'],
        ],

        Reimbursement::class => [
            'foto_odometer_awal' => ['type' => 'string', 'folder' => 'reimbursement/odometer_awal'],
            'foto_odometer_akhir' => ['type' => 'string', 'folder' => 'reimbursement/odometer_akhir'],
            'nota' => ['type' => 'string', 'folder' => 'reimbursement/nota'],
        ],

        DriverAttendence::class => [
            'photo_in' => ['type' => 'string', 'folder' => 'absen/photo_in'],
            'photo_out' => ['type' => 'string', 'folder' => 'absen/photo_out'],
        ],

        
    ];

    public function handle()
    {
        $this->info('Mulai membersihkan file tidak terpakai...');
        $totalDeleted = 0;

        // Proses setiap model
        foreach ($this->modelConfig as $modelClass => $fields) {
            $this->info("\nMemproses model: " . class_basename($modelClass));

            // Proses setiap field foto dalam model
            foreach ($fields as $field => $config) {
                $type   = $config['type'];
                // Jika 'folder' tidak didefinisikan, gunakan nama field sebagai nama folder
                $folder = $config['folder'] ?? $field;

                $deletedCount = $this->cleanupFieldFiles($modelClass, $field, $type, $folder);
                $totalDeleted += $deletedCount;
            }
        }

        $this->info("\nProses selesai! Total file yang dihapus: {$totalDeleted}");
    }

    /**
     * Membersihkan file tidak terpakai untuk satu field.
     *
     * @param string $modelClass  Nama class model
     * @param string $field       Nama kolom di database
     * @param string $type        Tipe data: 'string' atau 'array'
     * @param string $folder      Nama folder di storage (bisa berbeda dengan $field)
     */
    private function cleanupFieldFiles(string $modelClass, string $field, string $type, string $folder): int
    {
        $this->info("\nMemeriksa field: {$field} (folder: {$folder})");

        // 1. Dapatkan semua file dari storage berdasarkan nama FOLDER
        $allFiles = Storage::disk('public')->files($folder);
        if (empty($allFiles)) {
            $this->info("Tidak ada file di folder {$folder}");
            return 0;
        }
        $this->info("Ditemukan " . count($allFiles) . " file di storage");

        // 2. Dapatkan file yang digunakan dari database berdasarkan nama FIELD
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
            $this->info("Berhasil menghapus {$deletedCount} file tidak terpakai dari folder {$folder}");
        } else {
            $this->info("Tidak ada file yang perlu dihapus di folder {$folder}");
        }

        return $deletedCount;
    }
}
