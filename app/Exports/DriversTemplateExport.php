<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class DriversTemplateExport implements FromArray, WithHeadings
{
    public function headings(): array
    {
        return ['name', 'email', 'project', 'password', 'alamat', 'no_wa', 'jenis_kelamin'];
    }

    public function array(): array
    {
        return [
            ['Budi Santoso', 'budisantoso@gmail.com', 'Project A', 'password123', 'Jl. Sudirman No. 123', '081234567890', 'laki-laki']
        ];
    }
}
