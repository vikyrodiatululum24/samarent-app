<?php

namespace App\Exports;

use App\Models\KunciSerep;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class KunciSerepExport implements FromView, ShouldAutoSize, WithStyles
{
    protected $ids;

    public function __construct($ids)
    {
        $this->ids = $ids;
    }

    public function view(): View
    {
        return view('exports.kunci_serep', [
            'data' => KunciSerep::with('unit')->whereIn('id', $this->ids)->get()
        ]);
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1    => ['font' => ['bold' => true]],
        ];
    }
}
