<?php

namespace App\Http\Controllers;

use App\Helpers\BastkHelper;
use App\Http\Controllers\Controller;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Http\Request;

class PublicBastkController extends Controller
{
    public function create()
    {
        return view('public.bastk.create',[
            'users'=>User::where('role', 'admin')->orderBy('name')->get(),
            'units'=>Unit::orderBy('nopol')->get(),
            'kode'=>BastkHelper::kode(),
            'kondisi'=>BastkHelper::kondisi(),
            'kelengkapan'=>BastkHelper::kelengkapan(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'created_by' => 'required|exists:users,id',
            'type_bastk' => 'required|in:serah,terima',
            'kode' => 'required|string',
            'unit_id' => 'required|exists:data_units,id',
            'kepada' => 'required|string|max:255',
            'no_hp' => 'nullable|string|max:255',
            'alamat' => 'nullable|string',
            'tgl_serah' => 'nullable|date',
            'tgl_kembali' => 'nullable|date',
            'nama_penyerah' => 'nullable|string|max:255',
            'nama_penerima' => 'nullable|string|max:255',
            'kondisi_unit' => 'required|array',
            'exchange' => 'nullable|required_if:kondisi_unit,exchange|string',
            'keterangan' => 'nullable|string',
            'items' => 'nullable|array',
            'dokumentasi' => 'nullable|array',
        ]);

        try {
            \DB::beginTransaction();

            // 1. Simpan data utama BASTK
            $bastk = \App\Models\Bastk::create([
                'created_by' => $validated['created_by'],
                'type_bastk' => $validated['type_bastk'],
                'kode' => $validated['kode'],
                'unit_id' => $validated['unit_id'],
                'kepada' => $validated['kepada'],
                'no_hp' => $validated['no_hp'] ?? null,
                'alamat' => $validated['alamat'] ?? null,
                'tgl_serah' => $validated['tgl_serah'] ?? null,
                'tgl_kembali' => $validated['tgl_kembali'] ?? null,
                'nama_penyerah' => $validated['nama_penyerah'] ?? null,
                'nama_penerima' => $validated['nama_penerima'] ?? null,
                'kondisi_unit' => $validated['kondisi_unit'],
                'exchange' => $validated['exchange'] ?? null,
                'keterangan' => $validated['keterangan'] ?? null,
                'jenis_bastk' => 'new',
            ]); 

            // 2. Simpan items (kelengkapan & kondisi)
            if ($request->has('items') && is_array($request->items)) {
                foreach ($request->items as $itemData) {
                    if (empty($itemData['kelengkapan'])) {
                        continue;
                    }

                    $bastk->items()->create([
                        'kelengkapan' => $itemData['kelengkapan'],
                        'baik' => isset($itemData['baik']) && $itemData['baik'] == '1',
                        'rusak' => isset($itemData['rusak']) && $itemData['rusak'] == '1',
                        'tidak_ada' => isset($itemData['tidak_ada']) && $itemData['tidak_ada'] == '1',
                        'jenis_bbm' => $itemData['jenis_bbm'] ?? null,
                        'bbm' => !empty($itemData['bbm']) ? (int) $itemData['bbm'] : null,
                        'km' => !empty($itemData['km']) ? (int) $itemData['km'] : null,
                        'keterangan' => $itemData['keterangan'] ?? null,
                    ]);
                }
            }

            // 3. Handle upload & simpan dokumentasi foto
            if ($request->hasFile('dokumentasi')) {
                $dokData = ['bastk_id' => $bastk->id];

                $singleFields = [
                    'unit_depan', 'unit_belakang', 'unit_samping_kanan', 'unit_samping_kiri',
                    'kabin_depan', 'kabin_tengah', 'kabin_belakang', 'dashboard',
                    'odometer', 'buku_service', 'manual_book', 'ban_serep',
                    'stnk_depan', 'stnk_belakang', 'bastk',
                ];

                foreach ($singleFields as $field) {
                    if ($request->hasFile("dokumentasi.{$field}")) {
                        $path = $request->file("dokumentasi.{$field}")->store("bastk/dokumentasi/{$field}", 'public');
                        $dokData[$field] = $path;
                    }
                }

                // Multiple uploads for kerusakan & tools
                if ($request->hasFile('dokumentasi.kerusakan')) {
                    $kerusakanPaths = [];
                    foreach ($request->file('dokumentasi.kerusakan') as $file) {
                        $kerusakanPaths[] = $file->store('bastk/dokumentasi/kerusakan', 'public');
                    }
                    $dokData['kerusakan'] = $kerusakanPaths;
                }

                if ($request->hasFile('dokumentasi.tools')) {
                    $toolsPaths = [];
                    foreach ($request->file('dokumentasi.tools') as $file) {
                        $toolsPaths[] = $file->store('bastk/dokumentasi/tools', 'public');
                    }
                    $dokData['tools'] = $toolsPaths;
                }

                \App\Models\BastkDokumentasi::create($dokData);
            }

            \DB::commit();

            return redirect()->back()->with('success', 'Berita Acara Serah Terima Kendaraan (BASTK) berhasil disimpan.');
        } catch (\Exception $e) {
            \DB::rollBack();
            return redirect()->back()->withInput()->with('error', 'Gagal menyimpan BASTK: ' . $e->getMessage());
        }
    }
}
