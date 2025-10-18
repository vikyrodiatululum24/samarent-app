<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AbsensiExport implements FromView, WithStyles
{
    protected $attendences;
    protected $driverName;
    protected $month;

    public function __construct($attendences, $driverName, $month)
    {
        $this->attendences = $attendences;
        $this->driverName = $driverName;
        $this->month = $month;
    }

    public function view(): View
    {
        return view('exports.absensi', [
            'attendences' => $this->attendences,
            'driverName' => $this->driverName,
            'month' => $this->month,
        ]);
    }

    public function styles(Worksheet $sheet)
    {
        // mengatur lebar kolom secara otomatis
        foreach (range('A', 'K') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $sheet->getStyle('A1:K3')->getFont()->setBold(true);

        $sheet->getStyle('A5:K5')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'color' => ['rgb' => '4472C4'], // biru
            ],
            'alignment' => [
                'horizontal' => 'center',
                'vertical' => 'center',
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

        $lastRow = 5 + $this->attendences->count();
        $sheet->getStyle("A6:K{$lastRow}")->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);
    }

}
