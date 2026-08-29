<?php

namespace App\Imports;

use App\Models\KunciSerep;
use App\Models\Unit;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;

class KunciSerepImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnFailure
{
    use SkipsFailures;

    public $successCount = 0;

    public function model(array $row)
    {
        // Try getting Nopol from 'unit' or 'nopol' column in CSV
        $nopol = $row['unit'] ?? $row['nopol'] ?? null;
        $unit = Unit::where('nopol', $nopol)->first();

        if (!$unit) {
            return null; // Safety fallback
        }

        $this->successCount++;

        return KunciSerep::updateOrCreate(
            ['unit_id' => $unit->id],
            [
                'no_kunci' => $row['no_kunci'] ?? null,
                'status_kunci' => $row['status_kunci'] ?? 'tersedia',
                'lokasi' => $row['lokasi'] ?? null,
                'tanggal_masuk' => !empty($row['tanggal_masuk']) ? \Carbon\Carbon::parse($row['tanggal_masuk'])->format('Y-m-d') : null,
                'tanggal_keluar' => !empty($row['tanggal_keluar']) ? \Carbon\Carbon::parse($row['tanggal_keluar'])->format('Y-m-d') : null,
                'diambil_oleh' => $row['diambil_oleh'] ?? null,
                'keterangan' => $row['keterangan'] ?? null,
            ]
        );
    }

    public function rules(): array
    {
        return [
            // Validasi ini memastikan Nopol wajib terdaftar di tabel units
            '*.unit' => ['required_without:*.nopol', 'exists:data_units,nopol'],
            '*.nopol' => ['required_without:*.unit', 'exists:data_units,nopol'],
            '*.status_kunci' => ['nullable', 'in:tersedia,diambil'],
        ];
    }
}
