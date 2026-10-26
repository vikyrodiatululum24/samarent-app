<?php

namespace App\Exports;

use App\Models\AngsuranSetoran;
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

class AngsuranSetoranExport implements FromCollection, WithHeadings, ShouldAutoSize, WithStyles, WithTitle
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
        return 'Riwayat Setoran Harian';
    }

    public function collection()
    {
        $query = AngsuranSetoran::with(['angsuranKendaraan.driver.user', 'angsuranKendaraan.unit', 'pengeluaran']);

        if ($this->angsuranKendaraanId) {
            $query->where('angsuran_kendaraan_id', $this->angsuranKendaraanId);
        }

        if ($this->recordIds) {
            $query->whereIn('id', (array) $this->recordIds);
        }

        if ($this->startDate) {
            $query->whereDate('tgl_setor', '>=', $this->startDate);
        }

        if ($this->endDate) {
            $query->whereDate('tgl_setor', '<=', $this->endDate);
        }

        $records = $query->orderBy('tgl_setor', 'desc')->get();

        return $records->map(function ($row, $index) {
            $metodeLabel = match ($row->metode_setor) {
                'transfer' => 'Transfer Bank',
                'tunai' => 'Tunai',
                'kompensasi_biaya' => 'Kompensasi Biaya / Service',
                default => ucfirst($row->metode_setor),
            };

            $periode = Carbon::create()->month($row->periode_bulan)->locale('id')->translatedFormat('F') . ' ' . $row->periode_tahun;

            return [
                'No' => $index + 1,
                'Nama Penyewa' => $row->angsuranKendaraan?->driver?->user?->name ?? '-',
                'No Pol' => $row->angsuranKendaraan?->unit?->nopol ?? '-',
                'Tanggal Setor' => $row->tgl_setor ? Carbon::parse($row->tgl_setor)->format('d/m/Y') : '-',
                'Periode' => $periode,
                'Nominal Setor (Rp)' => (float) $row->nominal,
                'Metode Pembayaran' => $metodeLabel,
                'Kompensasi Dari Nota' => $row->pengeluaran ? "{$row->pengeluaran->jenis_pengeluaran} (Tgl {$row->pengeluaran->tgl_pengeluaran->format('d/m/Y')})" : '-',
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
            'Tanggal Setor',
            'Periode',
            'Nominal Setor (Rp)',
            'Metode Pembayaran',
            'Kompensasi Dari Nota',
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
                'color' => ['rgb' => '047857'], // Emerald Green
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
            $sheet->getStyle("C2:E{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            // Format Currency
            $sheet->getStyle("F2:F{$lastRow}")->getNumberFormat()->setFormatCode('#,##0');
        }

        return [];
    }
}
