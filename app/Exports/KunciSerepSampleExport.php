<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class KunciSerepSampleExport implements FromArray, ShouldAutoSize, WithStyles
{
    public function array(): array
    {
        return [
            // Header baris pertama
            ['nopol', 'no_kunci', 'status_kunci', 'lokasi', 'tanggal_masuk', 'tanggal_keluar', 'diambil_oleh', 'keterangan'],
            
            // Contoh pengisian 1 (Tersedia)
            ['B1234CD', 'K-001', 'tersedia', 'BOX 1-A', '2023-08-01', '', '', 'Kunci cadangan utama'],
            
            // Contoh pengisian 2 (Diambil)
            ['B5678EF', 'K-002', 'diambil', '', '2023-08-01', '2023-08-05', 'Budi Santoso', 'Dipinjam untuk operasional'],
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1    => ['font' => ['bold' => true]], // Bold untuk header
        ];
    }
}
