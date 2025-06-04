<x-filament::page>
    <div class="space-y-6">
        <h2 class="text-xl font-bold">Detail Unit: {{ $unit->jenis }} - {{ $unit->nopol }}</h2>

        <div class="grid grid-cols-2 gap-4">
            <div><strong>type:</strong> {{ $unit->type }}</div>
            <div><strong>Merk:</strong> {{ $unit->merk }}</div>
            <div><strong>Nomor Polisi:</strong> {{ $unit->nopol }}</div>
        </div>

        <hr>

        <h3 class="text-lg font-semibold mt-4">Riwayat Service</h3>
    </div>
    {{ $this->table }}
</x-filament::page>
