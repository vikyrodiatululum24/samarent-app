<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class OvertimePayExport implements FromView, WithStyles
{
    protected $overtimePays;
    protected $driverName;
    protected $month;

    public function __construct($overtimePays, $driverName, $month)
    {
        $this->overtimePays = $overtimePays;
        $this->driverName = $driverName;
        $this->month = $month;
    }

    public function view(): View
    {
        return view('exports.overtimepay', [
            'overtimePays' => $this->overtimePays,
            'driverName' => $this->driverName,
            'month' => $this->month,
        ]);
    }

    public function styles(Worksheet $sheet)
    {
        // mengatur lebar kolom secara otomatis
        foreach (range('A', 'R') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $sheet->getStyle('A1:R3')->getFont()->setBold(true);

        $sheet->getStyle('A5:R5')->applyFromArray([
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

        $lastRow = 5 + $this->overtimePays->count();
        $sheet->getStyle("A6:R{$lastRow}")->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

        $fotterRow = $lastRow + 1;
        $sheet->getStyle("A{$fotterRow}:R{$fotterRow}")->applyFromArray([
            'borders' => [
                'top' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

    }

}
