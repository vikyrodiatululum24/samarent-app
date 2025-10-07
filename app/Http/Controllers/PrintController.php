<?php

namespace App\Http\Controllers;

use PDF;
use App\Models\Cetak;
use App\Models\Driver;
use App\Models\Asuransi;
use App\Models\Pengajuan;
use Illuminate\Http\Request;
use App\Models\KeuanganService;

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

    public function jualunit($id)
    {
        $unitJual = \App\Models\UnitJual::with('unit', 'penawars')->findOrFail($id);
        $namaFile = 'Laporan-Jual-Unit-' . ($unitJual->unit->nopol ?? 'unit');
        $namaFile = str_replace(['/', '\\'], '-', $namaFile);
        $pdf = PDF::loadView('prints.jualunit', [
            'jualunit' => $unitJual,
        ]);
        return $pdf->stream("$namaFile.pdf");
    }

    public function absensi(Request $request, $driver_id)
    {
        // Ambil bulan dari query string (?month=10)
        $month = $request->get('month', date('m'));
        // Konversi nomor bulan ke nama bulan dalam bahasa Indonesia
        $monthName = match($month) {
            '01' => 'Januari',
            '02' => 'Februari', 
            '03' => 'Maret',
            '04' => 'April',
            '05' => 'Mei',
            '06' => 'Juni',
            '07' => 'Juli',
            '08' => 'Agustus',
            '09' => 'September',
            '10' => 'Oktober',
            '11' => 'November',
            '12' => 'Desember',
            default => ''
        };

        // Ambil driver
        $driver = Driver::with(['user', 'driverAttendences' => function ($query) use ($month) {
            $query->whereMonth('date', $month);
        }])->findOrFail($driver_id);

        // Ambil daftar absensinya
        $attendences = $driver->driverAttendences;
        // Buat nama file yang aman untuk disimpan

        $namaFile = 'Laporan-Absensi-' . ($driver->user->name ?? 'driver');
        $namaFile = str_replace(['/', '\\'], '-', $namaFile);

        $pdf = PDF::loadView('prints.absensi', [
            'driver' => $driver,
            'attendences' => $attendences->load('project', 'endUser', 'unit'),
            'month' => $monthName,
        ]);
        return $pdf->stream("$namaFile.pdf");
    }
}
