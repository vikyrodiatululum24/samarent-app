<?php

namespace App\Exports;

use App\Filament\Resources\PraPengajuanResource;
use App\Models\PraPengajuan;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class PraPengajuanExport implements FromCollection, WithHeadings
{
    public function __construct(
        private readonly ?string $up = null,
    ) {
    }

    public function collection()
    {
        return PraPengajuan::query()
            ->with('unit')
            ->when($this->up, fn ($query) => $query->where('up', $this->up))
            ->orderByDesc('id')
            ->get()
            ->map(function (PraPengajuan $row) {
                return [
                    'No.' => $iteration = isset($iteration) ? $iteration + 1 : 1,
                    'Tanggal' => optional($row->tanggal)->format('Y-m-d') ?? '',
                    'Nama PIC' => $row->nama_pic,
                    'No. WhatsApp' => $row->no_wa,
                    'Project' => $row->project,
                    'UP' => $row->up,
                    'UP Lainnya' => $row->up_lainnya,
                    'Provinsi' => $row->provinsi,
                    'Kota' => $row->kota,
                    'Nopol Unit' => $row->unit?->nopol,
                    'Merk Unit' => $row->unit?->merk,
                    'Type Unit' => $row->unit?->type,
                    'Service' => PraPengajuanResource::formatServices($row->service),
                    'Status' => $row->status,
                    'Tanggal Input User' => optional($row->tanggal_input_user)->format('Y-m-d') ?? '',
                    'Tanggal Masuk Finance' => optional($row->tanggal_masuk_finance)->format('Y-m-d') ?? '',
                    'Tanggal Otorisasi' => optional($row->tanggal_otorisasi)->format('Y-m-d') ?? '',
                    'Tanggal Pengerjaan' => optional($row->tanggal_pengerjaan)->format('Y-m-d') ?? '',
                    'Created At' => optional($row->created_at)->format('Y-m-d H:i:s') ?? '',
                ];
            });
    }

    public function headings(): array
    {
        return [
            'No.',
            'Tanggal',
            'Nama PIC',
            'No. WhatsApp',
            'Project',
            'UP',
            'UP Lainnya',
            'Provinsi',
            'Kota',
            'Nopol Unit',
            'Merk Unit',
            'Type Unit',
            'Service',
            'Status',
            'Tanggal Input User',
            'Tanggal Masuk Finance',
            'Tanggal Otorisasi',
            'Tanggal Pengerjaan',
            'Created At',
        ];
    }
}
