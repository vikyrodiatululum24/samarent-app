<?php

namespace App\Http\Controllers;

use App\Models\Cetak;
use App\Models\Pengajuan;
use App\Models\Asuransi;
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
        Cetak::updateOrCreate(['pengajuan_id' => $pengajuan->id]);
        $pdf = PDF::loadView('prints.spk', [
            'pengajuan' => $pengajuan,
        ]);
        return $pdf->stream("$namaFile.pdf");
    }

    public function previewAsuransi($id)
    {
        $asuransi = Asuransi::with('unit')->findOrFail($id);
        $namaFile = $asuransi->unit->nopol ?? 'asuransi';
        $namaFile = str_replace(['/', '\\'], '-', $namaFile);
        $pdf = PDF::loadView('prints.asuransi', [
            'asuransi' => $asuransi,
        ]);
        return $pdf->stream("$namaFile.pdf");
    }
    
    public function printAsuransi($id)
    {
        $asuransi = Asuransi::with('unit')->findOrFail($id);
        $namaFile = $asuransi->unit->nopol ?? 'asuransi';
        $namaFile = str_replace(['/', '\\'], '-', $namaFile);
        Cetak::updateOrCreate(['asuransi_id' => $asuransi->id]);
        $pdf = PDF::loadView('prints.asuransi', [
            'asuransi' => $asuransi,
        ]);
        return $pdf->stream("$namaFile.pdf");
    }
}
