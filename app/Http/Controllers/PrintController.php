<?php

namespace App\Http\Controllers;

use App\Models\Pengajuan;
use Illuminate\Http\Request;
use Mpdf\Mpdf;

class PrintController extends Controller
{
    public function printSpk($id)
    {
        $mpdf = new Mpdf();
        $pengajuan = Pengajuan::with('complete')->findOrFail($id);
        $mpdf->WriteHTML(view('prints.spk', compact('pengajuan')));
        $mpdf->Output('spk.pdf', 'I');
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
