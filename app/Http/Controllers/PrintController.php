<?php

namespace App\Http\Controllers;

use App\Models\Pengajuan;
use PDF;
use Illuminate\Http\Request;

class PrintController extends Controller
{
    public function viewSpk($id)
    {
        $pengajuan = Pengajuan::with('complete')->findOrFail($id);
        return view('prints.spk', compact('pengajuan'));
    }
    public function printSpk($id)
    {   
        // $data = ['title' => 'domPDF in Laravel 10'];
        $pengajuan = Pengajuan::with('complete')->findOrFail($id);
        $namaFile = $pengajuan->nopol. '-' . $pengajuan->no_pengajuan;
        $namaFile = str_replace(['/', '\\'], '-' , $namaFile);
        $pdf = PDF::loadView('prints.spk', ['pengajuan' => $pengajuan]);
        return $pdf->stream("$namaFile.pdf");
    }

    public function printSjp($id)
    {
        $pengajuan = Pengajuan::findOrFail($id);
        return view('prints.sjp', compact('pengajuan'));
    }

    public function printLampiran($id)
    {
        $pengajuan = Pengajuan::findOrFail($id);
        return view('prints.lampiran', compact('pengajuan'));
    }

    public function printLampiran2($id)
    {
        $pengajuan = Pengajuan::findOrFail($id);
        return view('prints.lampiran2', compact('pengajuan'));
    }
}
