<?php

namespace App\Http\Controllers;

use App\Models\PraPengajuan;
use App\Models\Project;
use App\Models\Unit;
use App\Services\CompressImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PublicPraPengajuanController extends Controller
{
    protected $compressImage;

    public function __construct(CompressImage $compressImage)
    {
        $this->compressImage = $compressImage;
    }

    public function create()
    {
        $units = Unit::query()
            ->select(['id', 'nopol', 'merk', 'type'])
            ->orderBy('nopol')
            ->get();

        $projects = Project::query()
            ->select(['id', 'name'])
            ->orderBy('name')
            ->get();

        return view('pra-pengajuan.create', compact('units', 'projects'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_pic' => 'required|string|max:100',
            'no_wa' => 'required|string|max:20',
            'project' => 'required|exists:projects,id',
            'up' => 'required|string',
            'up_lainnya' => 'nullable|string|max:255',
            'provinsi' => 'required|string',
            'kota' => 'required|string',

            // 🔥 service units
            'service_units' => 'required|array|min:1',
            'service_units.*.unit_id' => 'required|exists:data_units,id',
            'service_units.*.odometer' => 'required|string|max:20',
            'service_units.*.service' => 'required|array|min:1',

            // foto
            'service_units.*.foto_unit' => 'nullable|image|mimes:jpg,jpeg,png',
            'service_units.*.foto_odometer' => 'nullable|image|mimes:jpg,jpeg,png',

            // multiple foto kondisi
            'service_units.*.foto_kondisi' => 'array|max:3',
            'service_units.*.foto_kondisi.*' => 'nullable|image|mimes:jpg,jpeg,png',
        ], [
            'service_units.required' => 'Tambahkan minimal 1 unit untuk diservice.',
            'service_units.*.unit_id.required' => 'Pilih unit yang akan diservice.',
            'service_units.*.unit_id.exists' => 'Unit yang dipilih tidak valid.',
            'service_units.*.odometer.required' => 'Odometer wajib diisi.',
            'service_units.*.service.required' => 'Pilih minimal 1 service untuk unit ini.',
            'service_units.*.foto_unit.image' => 'Foto unit harus berupa gambar.',
            'service_units.*.foto_unit.mimes' => 'Foto unit harus berformat jpg, jpeg, atau png.',
            'service_units.*.foto_odometer.image' => 'Foto odometer harus berupa gambar.',
            'service_units.*.foto_odometer.mimes' => 'Foto odometer harus berformat jpg, jpeg, atau png.',
            'service_units.*.foto_kondisi.array' => 'Foto kondisi unit harus berupa array.',
            'service_units.*.foto_kondisi.max' => 'Unggah maksimal 3 foto kondisi unit.',
            'service_units.*.foto_kondisi.*.image' => 'Setiap foto kondisi unit harus berupa gambar.',
            'service_units.*.foto_kondisi.*.mimes' => 'Setiap foto kondisi unit harus berformat jpg, jpeg, atau png.',
        ]);

        if (($validated['up'] ?? null) !== 'Lainnya') {
            $validated['up_lainnya'] = null;
        }

        $project = Project::find($validated['project']);
        $validated['project'] = $project->name;

        DB::beginTransaction();

        try {
            $praPengajuan = PraPengajuan::create([
                'nama_pic' => $validated['nama_pic'],
                'no_wa' => $validated['no_wa'],
                'project' => $validated['project'],
                'up' => $validated['up'],
                'up_lainnya' => $validated['up_lainnya'] ?? null,
                'provinsi' => $validated['provinsi'],
                'kota' => $validated['kota'],
                'status' => 'pending',
            ]);

            foreach ($validated['service_units'] as $index => $unitData) {

                $fotoUnitPath = null;
                $fotoOdometerPath = null;
                $fotoKondisiPaths = [];

                $unit = Unit::find($unitData['unit_id']);

                if (!$unit) {
                    // skip if unit not found, should not happen due to validation
                    continue;
                }

                if ($request->hasFile("service_units.{$index}.foto_unit")) {
                     $fotoUnitPath = $this->compressImage->compressAndStore(
                        $unitData['foto_unit'],
                        'foto_unit'
                    );
                }

                if ($request->hasFile("service_units.{$index}.foto_odometer")) {
                    $fotoOdometerPath = $this->compressImage->compressAndStore(
                        $unitData['foto_odometer'],
                        'foto_odometer'
                    );
                }

                if ($request->hasFile("service_units.{$index}.foto_kondisi")) {
                    $fotoKondisiPaths = [];
                    foreach ($unitData['foto_kondisi'] as $index => $kondisiFile) {
                        $fotoKondisiPaths[] = $this->compressImage->compressAndStore(
                            $kondisiFile,
                            "foto_kondisi"
                        );
                    }
                }

                $services = array_values(array_filter($unitData['service'], fn($item) => $item !== 'Lainnya'));

                $praPengajuan->service_units()->create([
                    'pra_pengajuan_id' => $praPengajuan->id,
                    'unit_id' => $unit->id,
                    'odometer' => $unitData['odometer'],
                    'service' => implode(',', $services),
                    'foto_unit' => $fotoUnitPath,
                    'foto_odometer' => $fotoOdometerPath,
                    'foto_kondisi' => $fotoKondisiPaths,
                ]);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->withErrors(['error' => 'Terjadi kesalahan saat menyimpan data. Silakan coba lagi.']);
        }

        return redirect()->route('public.pra-pengajuan.success')->with('success', 'Form pra pengajuan berhasil dikirim.');
    }

    public function success()
    {
        return view('pra-pengajuan.success');
    }
}
