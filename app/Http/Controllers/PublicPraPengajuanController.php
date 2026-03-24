<?php

namespace App\Http\Controllers;

use App\Models\PraPengajuan;
use App\Models\Unit;
use Illuminate\Http\Request;

class PublicPraPengajuanController extends Controller
{
    private const SERVICE_OPTIONS = [
        'Service Ganti Oli',
        'Rem Depan',
        'Rem Belakang',
        'Lampu Depan',
        'Lampu Belakang',
        'Ban Depan',
        'Ban Belakang',
        'Gear Set',
        'Kampas Kopling',
        'Fikter Udara',
        'Filter Oli',
        'Busi',
        'Ban Dalam',
        'Spion',
        'Lampu Stop',
        'Lampu Sein depan',
        'Lampu Sein Belakang',
        'Bearing Depan',
        'Bearung Belakang',
        'Accu',
        'Lainnya',
    ];

    public function create()
    {
        $units = Unit::query()
            ->select(['id', 'nopol', 'merk', 'type'])
            ->orderBy('nopol')
            ->get();

        return view('pra-pengajuan.create', compact('units'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_pic' => ['required', 'string', 'max:255'],
            'no_wa' => ['required', 'string', 'max:20'],
            'project' => ['required', 'string', 'max:255'],
            'up' => ['required', 'string', 'max:255'],
            'up_lainnya' => ['nullable', 'string', 'max:255'],
            'provinsi' => ['required', 'string', 'max:255'],
            'kota' => ['required', 'string', 'max:255'],
            'unitId' => ['required', 'exists:data_units,id'],
            'service' => ['required', 'array', 'min:1'],
            'service.*' => ['required', 'string', 'max:255'],
            'service_lainnya' => ['nullable', 'string', 'max:255'],
            'tanggal' => ['nullable', 'date'],
            'status' => ['nullable', 'string', 'max:100'],
        ]);

        if (($validated['up'] ?? null) !== 'Lainnya') {
            $validated['up_lainnya'] = null;
        }

        $serviceValues = collect($validated['service'] ?? [])
            ->map(fn (string $value) => trim(str_replace(',', ' ', $value)))
            ->filter()
            ->unique()
            ->values()
            ->all();

        if (empty($serviceValues)) {
            return back()->withInput()->withErrors(['service' => 'Pilih minimal 1 service.']);
        }

        $validated['service'] = implode(',', $serviceValues);
        unset($validated['service_lainnya']);

        $validated['tanggal'] = $validated['tanggal'] ?? now()->toDateString();
        $validated['status'] = $validated['status'] ?? 'pending';

        $validated['unitId'] = (string) $validated['unitId'];

        PraPengajuan::create($validated);

        return redirect()->route('public.pra-pengajuan.success')->with('success', 'Form pra pengajuan berhasil dikirim.');
    }

    public function success()
    {
        return view('pra-pengajuan.success');
    }
}
