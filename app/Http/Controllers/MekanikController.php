<?php

namespace App\Http\Controllers;

use App\Models\Complete;
use App\Models\Pengajuan;
use App\Models\ServiceUnit;
use App\Models\Unit;
use App\Services\CompressImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class MekanikController extends Controller
{
    protected CompressImage $compressImage;

    public function __construct(CompressImage $compressImage)
    {
        $this->compressImage = $compressImage;
    }

    public function create()
    {
        $pengajuans = Pengajuan::get(['id', 'no_pengajuan']);

        return view('mekanik.create', compact('pengajuans'));
    }

    public function fetchPengajuan($id)
    {
        try {
            $complete = Complete::where('pengajuan_id', $id)
                ->select('id', 'pengajuan_id', 'foto_nota')
                ->first();
            $serviceUnits = ServiceUnit::with('unit:id,nopol,merk,type')->where('pengajuan_id', $id)
                ->select('id', 'pengajuan_id', 'unit_id', 'foto_unit', 'foto_odometer', 'foto_kondisi', 'foto_pengerjaan_bengkel', 'foto_tambahan')
                ->get();

            return response()->json([
                'complete' => $complete,
                'service_units' => $serviceUnits
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'error' => 'Pengajuan not found',
                'message' => $e->getMessage()
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        $pengajuan = Pengajuan::findOrFail($id);

        $validated = $request->validate([
            'complete.id' => 'nullable|exists:completes,id',
            'complete.existing_foto_nota' => 'nullable|array',
            'complete.existing_foto_nota.*' => 'string',
            'complete.foto_nota' => 'nullable|array|max:3',
            'complete.foto_nota.*' => 'nullable|image|mimes:jpg,jpeg,png|max:10240',
            'service_units' => 'nullable|array',
            'service_units.*.id' => 'required|exists:service_units,id',
            'service_units.*.foto_unit' => 'nullable|image|mimes:jpg,jpeg,png|max:10240',
            'service_units.*.foto_odometer' => 'nullable|image|mimes:jpg,jpeg,png|max:10240',
            'service_units.*.foto_pengerjaan_bengkel' => 'nullable|image|mimes:jpg,jpeg,png|max:10240',
            'service_units.*.existing_foto_kondisi' => 'nullable|array',
            'service_units.*.existing_foto_kondisi.*' => 'string',
            'service_units.*.foto_kondisi' => 'nullable|array|max:3',
            'service_units.*.foto_kondisi.*' => 'nullable|image|mimes:jpg,jpeg,png|max:10240',
            'service_units.*.existing_foto_tambahan' => 'nullable|array',
            'service_units.*.existing_foto_tambahan.*' => 'string',
            'service_units.*.foto_tambahan' => 'nullable|array|max:3',
            'service_units.*.foto_tambahan.*' => 'nullable|image|mimes:jpg,jpeg,png|max:10240',
        ]);

        DB::beginTransaction();

        try {
            $completePayload = $validated['complete'] ?? [];
            $complete = null;

            if (!empty($completePayload['id'])) {
                $complete = Complete::where('id', $completePayload['id'])
                    ->where('pengajuan_id', $pengajuan->id)
                    ->first();
            }

            if (!$complete) {
                $complete = Complete::firstOrCreate([
                    'pengajuan_id' => $pengajuan->id,
                ]);
            }

            $existingNota = $this->normalizeArrayField($complete->foto_nota);
            $keptNota = $this->intersectKeptPaths($existingNota, $completePayload['existing_foto_nota'] ?? []);
            $newNota = [];

            if ($request->hasFile('complete.foto_nota')) {
                foreach ($request->file('complete.foto_nota') as $file) {
                    $newNota[] = $this->compressImage->compressAndStore($file, 'foto_nota');
                }
            }

            $finalNota = array_values(array_unique(array_merge($keptNota, $newNota)));
            if (count($finalNota) > 3) {
                throw new \RuntimeException('Foto nota maksimal 3 gambar.');
            }

            $deletedNota = array_diff($existingNota, $finalNota);
            $this->deleteFiles($deletedNota);

            $complete->foto_nota = $finalNota;
            $complete->save();

            $unitsPayload = $validated['service_units'] ?? [];

            foreach ($unitsPayload as $index => $unitPayload) {
                $serviceUnit = ServiceUnit::where('id', $unitPayload['id'])
                    ->where('pengajuan_id', $pengajuan->id)
                    ->first();

                if (!$serviceUnit) {
                    continue;
                }

                $updates = [];

                if ($request->hasFile("service_units.{$index}.foto_unit")) {
                    $newPath = $this->compressImage->compressAndStore(
                        $request->file("service_units.{$index}.foto_unit"),
                        'foto_unit'
                    );

                    if (!empty($serviceUnit->foto_unit) && $serviceUnit->foto_unit !== $newPath) {
                        $this->deleteFiles([$serviceUnit->foto_unit]);
                    }

                    $updates['foto_unit'] = $newPath;
                }

                if ($request->hasFile("service_units.{$index}.foto_odometer")) {
                    $newPath = $this->compressImage->compressAndStore(
                        $request->file("service_units.{$index}.foto_odometer"),
                        'foto_odometer'
                    );

                    if (!empty($serviceUnit->foto_odometer) && $serviceUnit->foto_odometer !== $newPath) {
                        $this->deleteFiles([$serviceUnit->foto_odometer]);
                    }

                    $updates['foto_odometer'] = $newPath;
                }

                if ($request->hasFile("service_units.{$index}.foto_pengerjaan_bengkel")) {
                    $newPath = $this->compressImage->compressAndStore(
                        $request->file("service_units.{$index}.foto_pengerjaan_bengkel"),
                        'foto_pengerjaan_bengkel'
                    );

                    if (!empty($serviceUnit->foto_pengerjaan_bengkel) && $serviceUnit->foto_pengerjaan_bengkel !== $newPath) {
                        $this->deleteFiles([$serviceUnit->foto_pengerjaan_bengkel]);
                    }

                    $updates['foto_pengerjaan_bengkel'] = $newPath;
                }

                $existingKondisi = $this->normalizeArrayField($serviceUnit->foto_kondisi);
                $keptKondisi = $this->intersectKeptPaths($existingKondisi, $unitPayload['existing_foto_kondisi'] ?? []);
                $newKondisi = [];

                if ($request->hasFile("service_units.{$index}.foto_kondisi")) {
                    foreach ($request->file("service_units.{$index}.foto_kondisi") as $file) {
                        $newKondisi[] = $this->compressImage->compressAndStore($file, 'foto_kondisi');
                    }
                }

                $finalKondisi = array_values(array_unique(array_merge($keptKondisi, $newKondisi)));
                if (count($finalKondisi) > 3) {
                    throw new \RuntimeException('Foto kondisi maksimal 3 gambar per unit.');
                }

                $deletedKondisi = array_diff($existingKondisi, $finalKondisi);
                $this->deleteFiles($deletedKondisi);
                $updates['foto_kondisi'] = $finalKondisi;

                $existingTambahan = $this->normalizeArrayField($serviceUnit->foto_tambahan);
                $keptTambahan = $this->intersectKeptPaths($existingTambahan, $unitPayload['existing_foto_tambahan'] ?? []);
                $newTambahan = [];

                if ($request->hasFile("service_units.{$index}.foto_tambahan")) {
                    foreach ($request->file("service_units.{$index}.foto_tambahan") as $file) {
                        $newTambahan[] = $this->compressImage->compressAndStore($file, 'foto_tambahan');
                    }
                }

                $finalTambahan = array_values(array_unique(array_merge($keptTambahan, $newTambahan)));
                if (count($finalTambahan) > 3) {
                    throw new \RuntimeException('Foto tambahan maksimal 3 gambar per unit.');
                }

                $deletedTambahan = array_diff($existingTambahan, $finalTambahan);
                $this->deleteFiles($deletedTambahan);
                $updates['foto_tambahan'] = $finalTambahan;

                $serviceUnit->update($updates);
            }

            DB::commit();

            return redirect()
                ->route('mekanik.create')
                ->with('success', 'Dokumentasi berhasil diperbarui.');
        } catch (\Throwable $e) {
            DB::rollBack();

            return back()
                ->withErrors(['error' => 'Gagal memperbarui dokumentasi: ' . $e->getMessage()])
                ->withInput();
        }
    }

    private function normalizeArrayField($value): array
    {
        if (is_array($value)) {
            return array_values(array_filter($value));
        }

        if (empty($value)) {
            return [];
        }

        return [$value];
    }

    private function intersectKeptPaths(array $existing, array $incoming): array
    {
        $incomingClean = array_values(array_filter($incoming));
        return array_values(array_intersect($existing, $incomingClean));
    }

    private function deleteFiles(array $paths): void
    {
        foreach ($paths as $path) {
            if (!empty($path) && Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
            }
        }
    }
}
