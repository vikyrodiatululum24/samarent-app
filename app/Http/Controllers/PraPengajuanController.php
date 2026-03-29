<?php

namespace App\Http\Controllers;

use App\Models\Pengajuan;
use App\Models\PraPengajuan;
use Illuminate\Http\Request;

class PraPengajuanController extends Controller
{
    public function ajukanPraPengajuan($id)
    {
        $praPengajuan = PraPengajuan::findOrFail($id);

        $pengajuan = new Pengajuan();
        $pengajuan->user_id = auth()->id();
        $pengajuan->nama = $praPengajuan->nama_pic;
        $pengajuan->no_wa = $praPengajuan->no_wa;
        $pengajuan->project = $praPengajuan->project;
        $pengajuan->up = $praPengajuan->up;
        $pengajuan->up_lainnya = $praPengajuan->up_lainnya;
        $pengajuan->provinsi = $praPengajuan->provinsi;
        $pengajuan->kota = $praPengajuan->kota;
        $pengajuan->keterangan_proses = 'cs';
        $pengajuan->save();

        $praPengajuan->status = 'cs';
        $praPengajuan->save();

        return redirect()->route('pra-pengajuan.success');
    }
}
