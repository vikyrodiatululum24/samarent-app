<div class="flex flex-col items-center gap-2">
    @if($getState())
    <p class="mb-2">Foto Nota</p>
        <img src="{{ asset('storage/' . $getState()) }}" class="object-cover rounded shadow" alt="Foto Nota">
        <a href="{{ asset('storage/' . $getState()) }}" download class="px-3 py-1 bg-blue-600 text-white text-sm rounded hover:bg-blue-700 transition">
            Download
        </a>
    @else
        <p class="text-gray-500">Tidak ada foto yang tersedia.</p>
    @endif
</div>
