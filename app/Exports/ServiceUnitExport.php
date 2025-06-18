<?php

namespace App\Exports;

use App\Models\ServiceUnit;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ServiceUnitExport implements FromCollection, WithHeadings
{
    protected $fromDate;
    protected $toDate;

    public function __construct($fromDate = null, $toDate = null)
    {
        $this->fromDate = $fromDate;
        $this->toDate = $toDate;
    }

    public function collection()
    {
        // Ambil dan olah data sesuai kebutuhan
        $query = ServiceUnit::with(['pengajuan', 'pengajuan.complete', 'unit']);

        if ($this->fromDate && $this->toDate) {
            $query->whereHas('pengajuan', function ($q) {
                $q->whereBetween('created_at', [$this->fromDate, $this->toDate]);
            });
        }

        $data = $query->get()->map(function ($row) {
            return [
                'ID' => $row->id,
                'Tanggal Pengajuan' => $row->pengajuan->created_at->format('Y-m-d H:i:s'),
                'Nama PIC' => $row->pengajuan->nama,
                'No WA' => $row->pengajuan->no_wa ?? '',
                'Jenis' => $row->unit->jenis ?? '',
                'Type' => $row->unit->type ?? '',
                'Nomor Polisi' => $row->unit->nopol ?? '',
                'Odometer' => $row->odometer ?? '',
                'Pengajuan/Service' => $row->service ?? '',
                'Bengkel Estimasi' => $row->pengajuan->complete->bengkel_estimasi ?? '',
                'No Telp Bengkel' => $row->pengajuan->complete->no_telp_bengkel ?? '',
                'Nominal Estimasi' => $row->pengajuan->complete->nominal_estimasi ?? '',
                'Project' => $row->pengajuan->project ?? '',
                'UP' => $row->pengajuan->up ?? '',
                'UP Lainnya' => $row->pengajuan->up_lainnya ?? '',
                'Provinsi' => $row->pengajuan->provinsi ?? '',
                'Kota' => $row->pengajuan->kota ?? '',
                'Kode' => $row->pengajuan->complete->kode ?? '',
                'No Pengajuan' => $row->pengajuan->no_pengajuan ?? '',
                'Tanggal Masuk Finance' => $row->pengajuan->complete->tanggal_masuk_finance ?? '',
                'Tanggal Transfer Finance' => $row->pengajuan->complete->tanggal_tf_finance ?? '',
                'Jenis Pengajuan' => $row->pengajuan->keterangan ?? '',
                'Nominal Transfer Finance' => $row->pengajuan->complete->nominal_tf_finance ?? '',
                'Payment 1' => $row->pengajuan->payment_1 ?? '',
                'Nama Bank 1' => $row->pengajuan->bank_1 ?? '',
                'NoRek 1' => $row->pengajuan->norek_1 ?? '',
                'Payment 2' => $row->pengajuan->complete->payment_2 ?? '',
                'Nama Bank 2' => $row->pengajuan->complete->bank_2 ?? '',
                'NoRek 2' => $row->pengajuan->complete->norek_2 ?? '',
                'Nominal Transfer ke Bengkel' => $row->pengajuan->complete->nominal_tf_bengkel ?? '',
                'Selisih Transfer' => $row->pengajuan->complete->selisih_tf ?? '',
                'Tanggal Pengerjaan' => $row->pengajuan->complete->tanggal_pengerjaan ?? '',
                'Tanggal Transfer ke Bengkel' => $row->pengajuan->complete->tanggal_tf_bengkel ?? '',
                'Keterangan Proses Pengajuan' => $row->pengajuan->keterangan_proses ?? '',
                'Status Finance' => $row->pengajuan->complete->status_finance ?? '',
            ];
        }); 

        return $data;
    }

    public function headings(): array
    {
        return [
            'ID',
            'Tanggal Pengajuan',
            'Nama PIC',
            'No WA',
            'Jenis',
            'Type',
            'Nomor Polisi',
            'Odometer',
            'Pengajuan/Service',
            'Bengkel Estimasi',
            'No Telp Bengkel',
            'Nominal Estimasi',
            'Project',
            'UP',
            'UP Lainnya',
            'Provinsi',
            'Kota',
            'Kode',
            'No Pengajuan',
            'Tanggal Masuk Finance',
            'Tanggal Transfer Finance',
            'Jenis Pengajuan',
            'Nominal Transfer Finance',
            'Payment 1',
            'Nama Bank 1',
            'NoRek 1',
            'Payment 2',
            'Nama Bank 2',
            'NoRek 2',
            'Nominal Transfer ke Bengkel',
            'Selisih Transfer',
            'Tanggal Pengerjaan',
            'Tanggal Transfer ke Bengkel',
            'Keterangan Proses Pengajuan',
            'Status Finance',
        ];
    }
}
