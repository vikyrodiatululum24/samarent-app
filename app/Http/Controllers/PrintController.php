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
        return view('prints.preview', compact('pengajuan'));
    }
    public function printSpk($id)
    {   
        // $data = ['title' => 'domPDF in Laravel 10'];
        $pengajuan = Pengajuan::with('complete')->findOrFail($id);
        $namaFile = $pengajuan->nopol. '-' . $pengajuan->no_pengajuan;
        $namaFile = str_replace(['/', '\\'], '-' , $namaFile);
        $pdf = PDF::loadView('prints.spk', ['pengajuan' => $pengajuan]);
        Cetak::create(['pengajuan_id' => $pengajuan->no_pengajuan]);
        return $pdf->stream("$namaFile.pdf");
    }
}
