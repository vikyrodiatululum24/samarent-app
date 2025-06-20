<?php

namespace App\Http\Controllers;

use App\Models\Cetak;
use App\Models\Pengajuan;
use PDF;

class PrintController extends Controller
{
    public function preview($id)
    {
        $pengajuan = Pengajuan::with('complete')->findOrFail($id);
        $namaFile = $pengajuan->nopol . '-' . $pengajuan->no_pengajuan;
        $namaFile = str_replace(['/', '\\'], '-', $namaFile);
        $pdf = PDF::loadView('prints.spk', ['pengajuan' => $pengajuan]);
        return $pdf->stream("$namaFile.pdf");
    }
    public function printSpk($id)
    {
        $pengajuan = Pengajuan::with('complete')->findOrFail($id);
        $namaFile = $pengajuan->no_pengajuan;
        $namaFile = str_replace(['/', '\\'], '-', $namaFile);
        Cetak::create(['pengajuan_id' => $pengajuan->id]);
        $pdf = PDF::loadView('prints.spk', [
            'pengajuan' => $pengajuan,
            'autoPrint' => true // kirim flag ke view
        ]);
        return $pdf->stream("$namaFile.pdf");
    }
}
