<?php

namespace App\Exports;

use App\Models\ServiceUnit;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ServiceUnitExportManager implements FromCollection, WithHeadings
{
    protected $fromDate;
    protected $toDate;
    protected $project;
    protected $up;

    public function __construct($fromDate = null, $toDate = null, $project = null, $up = null)
    {
        $this->fromDate = $fromDate;
        $this->toDate = $toDate;
        $this->project = $project;
        $this->up = $up;
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

        if($this->project){
            $query->whereHas('pengajuan', function ($q) {
                $q->where('project', $this->project);
            });
        }

        if($this->up){
            $query->whereHas('pengajuan', function ($q) {
                $q->where('up', $this->up);
            });
        }

        $data = $query->get()->map(function ($row, $iteration) {
            return [
                'No' => $iteration + 1,
                'Tanggal Pengajuan' => $row->pengajuan->created_at->format('Y-m-d H:i:s'),
                'No Pengajuan' => $row->pengajuan->no_pengajuan ?? '',
                'Nama PIC' => $row->pengajuan->nama,
                'Pengajuan/Service' => $row->service ?? '',
                'Nomor Polisi' => $row->unit->nopol ?? '',
                'Project' => $row->pengajuan->project ?? '',
            ];
        }); 

        return $data;
    }

    public function headings(): array
    {
        return [
            'No',
            'Tanggal Pengajuan',
            'No Pengajuan',
            'Nama PIC',
            'Pengajuan/Service',
            'Nomor Polisi',
            'Project',
        ];
    }
}
