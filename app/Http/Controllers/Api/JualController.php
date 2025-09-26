<?php

namespace App\Http\Controllers\Api;

use App\Models\Unit;
use App\Models\UnitJual;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class JualController extends Controller
{
    public function getunit(Request $request)
    {
        $query = Unit::with([
            'unitJual:id,unit_id,harga_jual,foto_depan,foto_belakang,foto_kiri,foto_kanan,foto_interior,foto_odometer,keterangan'
        ])->select('id', 'type', 'merk', 'tahun', 'warna',); // field dari tabel data_units

        // 🔍 Filter Search (misalnya berdasarkan merk, nopol, atau type)
        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('merk', 'like', '%' . $request->search . '%')
                    ->orWhere('nopol', 'like', '%' . $request->search . '%')
                    ->orWhere('type', 'like', '%' . $request->search . '%');
            });
        }

        // 🔍 Filter jenis
        if ($request->merk) {
            $query->where('merk', $request->jenis);
        }

        // 🔍 Filter tahun
        if ($request->tahun) {
            $query->where('tahun', $request->tahun);
        }

        // 🔍 Filter warna
        if ($request->warna) {
            $query->where('warna', $request->warna);
        }

        // Pagination
        $units = $query->paginate(10);

        return response()->json($units);
    }
}
