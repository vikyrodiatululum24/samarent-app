<?php

namespace App\Http\Controllers;

use App\Models\Cetak;
use App\Models\Pengajuan;
use App\Models\Asuransi;
use App\Models\KeuanganService;
use PDF;
use Illuminate\Http\Request;

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

    public function keuanganPdf(Request $request)
    {
        // dd($request->all());
        $dari_tanggal = $request->query('dari_tanggal');
        $sampai_tanggal = $request->query('sampai_tanggal');

        $query = KeuanganService::query();

        if ($dari_tanggal) {
            $query->whereDate('created_at', '>=', $dari_tanggal);
        }
        if ($sampai_tanggal) {
            $query->whereDate('created_at', '<=', $sampai_tanggal);
        }

        $data = $query->with([
            'pengajuan',
            'pengajuan.complete'
        ])->get();

        // Hitung total
        $totalFinance = $data->sum(fn($item) => $item->pengajuan->complete->nominal_tf_finance ?? 0);
        $totalBengkel = $data->sum(fn($item) => $item->pengajuan->complete->nominal_tf_bengkel ?? 0);
        $totalSelisih = $data->sum(fn($item) => $item->pengajuan->complete->selisih_tf ?? 0);

        // Render PDF
        $pdf = \PDF::loadView('filament.laporan-keuangan.pdf', [
            'data' => $data,
            'totalFinance' => $totalFinance,
            'totalBengkel' => $totalBengkel,
            'totalSelisih' => $totalSelisih,
            'dari_tanggal' => $dari_tanggal,
            'sampai_tanggal' => $sampai_tanggal,
            'tanggal' => now()->format('d/m/Y'),
        ]);
        $pdf->setPaper('a4', 'landscape');

        return $pdf->stream('laporan_keuangan_service_' . date('Y-m-d') . '.pdf');
    }
}
