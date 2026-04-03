<?php

namespace App\Http\Controllers;

use App\Models\Pengajuan;
use App\Models\PraPengajuan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PraPengajuanController extends Controller
{
    public function ajukanPraPengajuan($id)
    {
        $praPengajuan = PraPengajuan::findOrFail($id);

        DB::beginTransaction();
        try {
            $pengajuan = new Pengajuan();
            $pengajuan->user_id = auth()->id();
            $pengajuan->nama = $praPengajuan->nama_pic;
            $pengajuan->no_wa = $praPengajuan->no_wa;
            $pengajuan->project = $praPengajuan->project;
            $pengajuan->up = $praPengajuan->up;
            $pengajuan->up_lainnya = $praPengajuan->up_lainnya;
            $pengajuan->provinsi = $praPengajuan->provinsi;
            $pengajuan->kota = $praPengajuan->kota;
            $pengajuan->keterangan = 'PraPengajuan';
            $pengajuan->keterangan_proses = 'cs';
            $pengajuan->save();

            foreach ($praPengajuan->service_units as $serviceUnit) {
                $serviceUnit->pengajuan_id = $pengajuan->id;
                $serviceUnit->pra_pengajuan_id = null;
                $serviceUnit->save();
            }

            $praPengajuan->delete();
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->withErrors(['error' => 'Terjadi kesalahan saat mengajukan Pra Pengajuan. Silakan coba lagi.']);
        }

        return redirect()->route('filament.admin.resources.pra-pengajuans.index')->with('success', 'Pra Pengajuan berhasil diajukan sebagai Pengajuan.');
    }

    public function ajukanMultiplePraPengajuan(Request $request)
    {
        $ids = explode(',', $request->input('ids', []));

        foreach ($ids as $id) {
            $this->ajukanPraPengajuan($id);
        }

        return redirect()->route('filament.admin.resources.pra-pengajuans.index')->with('success', 'Pra Pengajuan berhasil diajukan sebagai Pengajuan.');
    }
}
