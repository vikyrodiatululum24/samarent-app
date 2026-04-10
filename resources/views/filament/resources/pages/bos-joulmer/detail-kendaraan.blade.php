@php
    $serviceUnits = \App\Models\ServiceUnit::with('unit')
        ->where('pengajuan_id', $pengajuanId)
        ->get();
@endphp

<div class="space-y-4">
    @forelse ($serviceUnits as $serviceUnit)
        <div class="rounded-lg border border-gray-200 p-4 dark:border-gray-700">
            <h3 class="mb-2 text-sm font-semibold text-gray-900 dark:text-gray-100">
                Unit {{ $loop->iteration }}
            </h3>

            <div class="grid gap-2 md:grid-cols-2">
                <div><span class="font-medium">Nomor Polisi:</span> {{ $serviceUnit->unit?->nopol ?? '-' }}</div>
                <div><span class="font-medium">Jenis Kendaraan:</span> {{ $serviceUnit->unit?->jenis ?? '-' }}</div>
                <div><span class="font-medium">Tipe Unit:</span> {{ $serviceUnit->unit?->type ?? '-' }}</div>
                <div><span class="font-medium">Odometer:</span> {{ $serviceUnit->odometer ?? '-' }}</div>
                <div class="md:col-span-2"><span class="font-medium">Jenis Permintaan Service:</span> {{ $serviceUnit->service ?? '-' }}</div>
            </div>

            <div class="mt-4 flex flex-wrap gap-4">
                <div class="mt-2">
                    @php
                        $fotoOdometer = $serviceUnit->foto_odometer ?? null;
                    @endphp
                    @include('filament.components.foto-odometer', ['getState' => fn() => $fotoOdometer])
                </div>

                <div class="mt-2">
                    @php
                        $fotoUnit = $serviceUnit->foto_unit ?? null;
                    @endphp
                    @include('filament.components.foto-unit', ['getState' => fn() => $fotoUnit])
                </div>

                <div class="mt-2">
                    @php
                        $fotoPengerjaan = $serviceUnit->foto_pengerjaan_bengkel ?? null;
                    @endphp
                    @include('filament.components.foto-pengerjaan-bengkel', [
                        'getState' => fn() => $fotoPengerjaan,
                    ])
                </div>
            </div>

            <div class="mt-6">
                @php
                    $fotoKondisi = is_array($serviceUnit->foto_kondisi ?? null) ? $serviceUnit->foto_kondisi : [];
                @endphp
                @include('filament.components.foto-kondisi', ['getState' => fn() => $fotoKondisi])
            </div>
        </div>
    @empty
        <div class="text-sm text-gray-500 dark:text-gray-400">Tidak ada data unit yang terhubung.</div>
    @endforelse
</div>
