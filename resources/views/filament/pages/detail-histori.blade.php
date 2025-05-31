<x-filament::page>
    <div class="space-y-6">
        <h2 class="text-xl font-bold">Detail Unit: {{ $unit->jenis }} - {{ $unit->nopol }}</h2>

        <div class="grid grid-cols-2 gap-4">
            <div><strong>Jenis:</strong> {{ $unit->jenis }}</div>
            <div><strong>Merk:</strong> {{ $unit->merk }}</div>
            <div><strong>Nomor Polisi:</strong> {{ $unit->nopol }}</div>
        </div>

        <hr>

        <h3 class="text-lg font-semibold mt-4">Riwayat Service</h3>

        @if ($unit->serviceUnit->count())
            <ul class="list-disc ml-5">
                @foreach ($unit->serviceUnit as $service)
                    <li>
                        Tanggal: {{ \Carbon\Carbon::parse($service->created_at)->format('d M Y') }},
                        Keterangan: {{ $service->keterangan ?? '-' }}
                    </li>
                @endforeach
            </ul>
        @else
            <p class="text-gray-500 italic">Tidak ada riwayat service.</p>
        @endif

        {{-- <x-filament::button tag="a" href="{{ route('filament.pages.histori-pengajuan') }}">
            Kembali
        </x-filament::button> --}}
    </div>
</x-filament::page>

