<?php

namespace App\Exports;

use App\Models\AngsuranKendaraan;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AngsuranKendaraanExport implements FromCollection, WithHeadings, ShouldAutoSize, WithStyles, WithTitle
{
    protected $recordIds;
    protected $status;
    protected $sheetTitle;

    public function __construct($recordIds = null, $status = null, $sheetTitle = null)
    {
        $this->recordIds = $recordIds;
        $this->status = $status;
        $this->sheetTitle = $sheetTitle;
    }

    public function title(): string
    {
        return $this->sheetTitle ?? 'Kontrak Sewa & Angsuran';
    }

    public function collection()
    {
        $query = AngsuranKendaraan::with(['driver.user', 'unit', 'setorans', 'pengeluarans']);

        if ($this->recordIds) {
            $query->whereIn('id', (array) $this->recordIds);
        }

        if ($this->status) {
            $query->where('status', $this->status);
        }

        $records = $query->orderBy('created_at', 'desc')->get();

        return $records->map(function ($row, $index) {
            return [
                'No' => $index + 1,
                'Nama Penyewa' => $row->driver?->user?->name ?? '-',
                'No HP/WA' => $row->driver?->no_wa ?? '-',
                'No Pol' => $row->unit?->nopol ?? '-',
                'Kendaraan' => ($row->unit?->merk ?? '') . ' ' . ($row->unit?->type ?? ''),
                'Tgl Serah Terima' => $row->tgl_serah_terima ? Carbon::parse($row->tgl_serah_terima)->format('d/m/Y') : '-',
                'Masa Akhir Kontrak' => $row->tgl_jatuh_tempo_akhir ? Carbon::parse($row->tgl_jatuh_tempo_akhir)->format('d/m/Y') : '-',
                'Jangka Waktu (Tahun)' => $row->jangka_waktu_tahun ?? 0,
                'DP (Rp)' => $row->dp ? (float) $row->dp : 0,
                'Tgl DP' => $row->tgl_dp ? Carbon::parse($row->tgl_dp)->format('d/m/Y') : '-',
                'Frekuensi/Bulan' => $row->frekuensi_per_bulan ?? 0,
                'Angsuran/Hari (Rp)' => (float) $row->angsuran_per_hari,
                'Normal Angsuran (Rp)' => (float) $row->normal_angsuran,
                'Nilai Kontrak (Rp)' => (float) $row->nilai_kontrak,
                'Deposit (Rp)' => $row->deposit ? (float) $row->deposit : 0,
                'Tgl Deposit' => $row->tgl_deposit ? Carbon::parse($row->tgl_deposit)->format('d/m/Y') : '-',
                'Total Setoran (Rp)' => (float) $row->total_setoran_keseluruhan,
                'Sisa Kontrak (Rp)' => (float) $row->sisa_kontrak,
                'Total Biaya Service (Rp)' => (float) $row->pengeluarans()->sum('nominal'),
                'Status' => ucfirst($row->status ?? 'Aktif'),
                'Catatan' => $row->catatan ?? '-',
            ];
        });
    }

    public function headings(): array
    {
        return [
            'No',
            'Nama Penyewa',
            'No HP/WA',
            'No Pol',
            'Kendaraan',
            'Tgl Serah Terima',
            'Masa Akhir Kontrak',
            'Jangka Waktu (Tahun)',
            'DP (Rp)',
            'Tgl DP',
            'Frekuensi/Bulan',
            'Angsuran/Hari (Rp)',
            'Normal Angsuran (Rp)',
            'Nilai Kontrak (Rp)',
            'Deposit (Rp)',
            'Tgl Deposit',
            'Total Setoran (Rp)',
            'Sisa Kontrak (Rp)',
            'Total Biaya Service (Rp)',
            'Status',
            'Catatan',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $lastColumn = $sheet->getHighestColumn();
        $lastRow = $sheet->getHighestRow();

        // Style Header
        $sheet->getStyle("A1:{$lastColumn}1")->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size' => 11,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'color' => ['rgb' => '1E3A8A'], // Navy
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        $sheet->getRowDimension(1)->setRowHeight(28);

        // Border & Alignment untuk seluruh tabel
        if ($lastRow > 1) {
            $sheet->getStyle("A1:{$lastColumn}{$lastRow}")->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => 'CBD5E1'],
                    ],
                ],
            ]);

            // Center kolom No & Tanggal
            $sheet->getStyle("A2:A{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("F2:G{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("H2:H{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("J2:J{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("K2:K{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("P2:P{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("T2:T{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            // Format Angka / Currency untuk kolom uang
            $currencyFormat = '#,##0';
            $sheet->getStyle("I2:I{$lastRow}")->getNumberFormat()->setFormatCode($currencyFormat);
            $sheet->getStyle("L2:O{$lastRow}")->getNumberFormat()->setFormatCode($currencyFormat);
            $sheet->getStyle("Q2:S{$lastRow}")->getNumberFormat()->setFormatCode($currencyFormat);
        }

        return [];
    }
}
