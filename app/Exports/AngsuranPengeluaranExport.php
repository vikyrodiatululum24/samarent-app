<?php

namespace App\Exports;

use App\Models\AngsuranPengeluaran;
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

class AngsuranPengeluaranExport implements FromCollection, WithHeadings, ShouldAutoSize, WithStyles, WithTitle
{
    protected $angsuranKendaraanId;
    protected $recordIds;
    protected $startDate;
    protected $endDate;

    public function __construct($angsuranKendaraanId = null, $recordIds = null, $startDate = null, $endDate = null)
    {
        $this->angsuranKendaraanId = $angsuranKendaraanId;
        $this->recordIds = $recordIds;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function title(): string
    {
        return 'Biaya & Service Kendaraan';
    }

    public function collection()
    {
        $query = AngsuranPengeluaran::with(['angsuranKendaraan.driver.user', 'angsuranKendaraan.unit']);

        if ($this->angsuranKendaraanId) {
            $query->where('angsuran_kendaraan_id', $this->angsuranKendaraanId);
        }

        if ($this->recordIds) {
            $query->whereIn('id', (array) $this->recordIds);
        }

        if ($this->startDate) {
            $query->whereDate('tgl_pengeluaran', '>=', $this->startDate);
        }

        if ($this->endDate) {
            $query->whereDate('tgl_pengeluaran', '<=', $this->endDate);
        }

        $records = $query->orderBy('tgl_pengeluaran', 'desc')->get();

        return $records->map(function ($row, $index) {
            $statusLabel = match ($row->status) {
                'diajukan' => 'Diajukan',
                'disetujui' => 'Disetujui',
                'dikompensasi_sebagian' => 'Sebagian Terkompensasi',
                'sudah_dikompensasi' => 'Lunas Dikompensasi',
                'ditolak' => 'Ditolak',
                default => ucfirst($row->status),
            };

            return [
                'No' => $index + 1,
                'Nama Penyewa' => $row->angsuranKendaraan?->driver?->user?->name ?? '-',
                'No Pol' => $row->angsuranKendaraan?->unit?->nopol ?? '-',
                'Tanggal Pengeluaran' => $row->tgl_pengeluaran ? Carbon::parse($row->tgl_pengeluaran)->format('d/m/Y') : '-',
                'Jenis Perbaikan / Service' => $row->jenis_pengeluaran ?? '-',
                'Total Biaya (Rp)' => (float) $row->nominal,
                'Total Terkompensasi (Rp)' => (float) $row->total_terkompensasi,
                'Sisa Saldo Nota (Rp)' => (float) $row->sisa_kompensasi,
                'Status Klaim' => $statusLabel,
                'Keterangan' => $row->keterangan ?? '-',
            ];
        });
    }

    public function headings(): array
    {
        return [
            'No',
            'Nama Penyewa',
            'No Pol',
            'Tanggal Pengeluaran',
            'Jenis Perbaikan / Service',
            'Total Biaya (Rp)',
            'Total Terkompensasi (Rp)',
            'Sisa Saldo Nota (Rp)',
            'Status Klaim',
            'Keterangan',
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
                'color' => ['rgb' => 'B45309'], // Amber / Dark Orange
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        $sheet->getRowDimension(1)->setRowHeight(28);

        // Border & Alignment
        if ($lastRow > 1) {
            $sheet->getStyle("A1:{$lastColumn}{$lastRow}")->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => 'CBD5E1'],
                    ],
                ],
            ]);

            $sheet->getStyle("A2:A{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("C2:D{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("I2:I{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            // Format Currency
            $sheet->getStyle("F2:H{$lastRow}")->getNumberFormat()->setFormatCode('#,##0');
        }

        return [];
    }
}
