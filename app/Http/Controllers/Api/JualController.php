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
        $query = UnitJual::with([
            'unit:id,id,type,merk,tahun,warna'
        ])->select(
            'id',
            'unit_id', // WAJIB ada agar relasi bisa dipetakan
            'harga_jual',
            'foto_depan',
            'foto_belakang',
            'foto_kiri',
            'foto_kanan',
            'foto_interior',
            'foto_odometer',
            'keterangan',
            'odometer'
        ); // field dari tabel data_units

        // 🔍 Filter Search (misalnya berdasarkan merk, nopol, atau type)
        if ($request->search) {
            $query->whereHas('unit', function ($q) use ($request) {
                $q->where('merk', 'like', '%' . $request->search . '%')
                    ->orWhere('type', 'like', '%' . $request->search . '%')
                    ->orWhere('tahun', 'like', '%' . $request->search . '%');
            });
        }

        // Filter merk
        $query->when($request->merk, function ($q, $merk) {
            $q->whereHas('unit', function ($q2) use ($merk) {
                $q2->where('merk', $merk);
            });
        });

        // Filter tahun
        $query->when($request->tahun, function ($q, $tahun) {
            $q->whereHas('unit', function ($q2) use ($tahun) {
                $q2->where('tahun', $tahun);
            });
        });


        // Pagination
        $perPage = $request->get('per_page', 10);
        $units = $query->paginate($perPage);

        // Ambil filter options (seluruh data, bukan per page)
        $filterOptions = [
            'merk'  => UnitJual::with('unit')
                ->get()
                ->pluck('unit.merk')
                ->unique()
                ->values(),

            'tahun' => UnitJual::with('unit')
                ->get()
                ->pluck('unit.tahun')
                ->unique()
                ->values(),
        ];

        return response()->json([
            'data' => $units,
            'filters' => $filterOptions,
        ]);
    }

    public function penawar(Request $request)
    {
        $request->validate([
            'unit_jual_id' => 'required|exists:unit_juals,id',
            'nama' => 'required|string|max:255',
            'no_wa' => 'required|string|max:20',
            'harga_penawaran' => 'required|numeric|min:0',
            'down_payment' => 'nullable|numeric|min:0',
            'catatan' => 'nullable|string'
        ]);

        $penawar = new \App\Models\Penawar();
        $penawar->unit_jual_id = $request->unit_jual_id;
        $penawar->nama = $request->nama;
        $penawar->no_wa = $request->no_wa;
        $penawar->harga_penawaran = $request->harga_penawaran;
        $penawar->down_payment = $request->down_payment;
        $penawar->catatan = $request->catatan;
        $penawar->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Data penawar berhasil disimpan',
            'data' => $penawar
        ], 201);
    }

    public function detail($id)
    {
        $unitJual = UnitJual::with('unit')->find($id);

        if (!$unitJual) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unit jual tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $unitJual
        ]);
    }
}
