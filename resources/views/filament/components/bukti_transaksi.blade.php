@if ($getState())
    <p class="mb-2">Foto Bukti Transaksi</p>
    @php
        $file = $getState();
        $extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));
    @endphp

    @if ($extension === 'pdf')
        <embed src="{{ asset('storage/' . $file) }}" type="application/pdf" width="300px" height="200px" class="rounded shadow" />

    @else
        <img src="{{ asset('storage/' . $file) }}" class="object-cover rounded shadow"
            style="max-width: 300px; max-height: 200px;" alt="Foto Bukti Transaksi">
    @endif
    <a href="{{ asset('storage/' . $getState()) }}" download
        class="px-3 py-1 bg-blue-600 text-gray-800 dark:text-white text-sm rounded hover:bg-blue-700 transition">
        Download
    </a>
@else
    <p class="text-gray-500">Tidak ada bukti transaksi.</p>
@endif
