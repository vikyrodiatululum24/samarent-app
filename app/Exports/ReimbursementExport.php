<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ReimbursementExport implements FromView, WithStyles
{
    /**
    * @return \Illuminate\Support\Collection
    */
    protected $reimbursements;
    protected $dari;
    protected $sampai;
    protected $user;

    public function __construct($reimbursements, $dari, $sampai, $user)
    {
        $this->reimbursements = $reimbursements;
        $this->dari = $dari;
        $this->sampai = $sampai;
        $this->user = $user;
    }

    public function view(): View
    {
        return view('exports.reimbursement', [
            'reimbursements' => $this->reimbursements,
            'dari' => $this->dari,
            'sampai' => $this->sampai,
            'user' => $this->user,
        ]);
    }

    public function styles(Worksheet $sheet)
    {
        foreach (range('A', 'K') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $lastColumn = $sheet->getHighestColumn();
        $lastRow = $sheet->getHighestRow();

        //header

        $sheet->getStyle('A1:K1')->getFont()->setBold(true)->setSize(16)->setName('Arial');
        $sheet->getStyle('A1:K1')->getAlignment()->setHorizontal('center')->setVertical('center');
        $sheet->getStyle('A2:K2')->getAlignment();
        $sheet->getStyle('A3:K3')->getAlignment();
        $sheet->getStyle('A4:K4')->getAlignment();
        $sheet->getStyle('A5:K5')->getAlignment();

        // head table
        $sheet->getStyle('A6:K6')->applyFromArray([
            'font' => ['bold' => true],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4472C4'],
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
            'alignment' => [
                'horizontal' => 'center',
                'vertical' => 'center',
            ],
        ]);

        // all body
        $sheet->getStyle('A6:' . $lastColumn . $lastRow)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

        $footerRow = $lastRow;

        $sheet->getStyle('A' . $footerRow . ':' . $lastColumn . $footerRow)->applyFromArray([
            'font' => ['bold' => true],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
            'alignment' => [
                'horizontal' => 'center',
                'vertical' => 'center',
            ],
        ]);
    }

}
