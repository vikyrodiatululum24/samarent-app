<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class AngsuranLengkapExport implements WithMultipleSheets
{
    protected $angsuranKendaraanId;
    protected $status;
    protected $startDate;
    protected $endDate;

    public function __construct($angsuranKendaraanId = null, $status = null, $startDate = null, $endDate = null)
    {
        $this->angsuranKendaraanId = $angsuranKendaraanId;
        $this->status = $status;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function sheets(): array
    {
        $recordIds = $this->angsuranKendaraanId ? [$this->angsuranKendaraanId] : null;

        return [
            new AngsuranKendaraanExport($recordIds, $this->status, 'Ringkasan Kontrak'),
            new AngsuranSetoranExport($this->angsuranKendaraanId, null, $this->startDate, $this->endDate),
            new AngsuranPengeluaranExport($this->angsuranKendaraanId, null, $this->startDate, $this->endDate),
        ];
    }
}
