<p class="mb-2">Foto Unit</p>
<div class="flex flex-col items-center gap-2">
    @if ($getState())
        <img src="{{ asset('storage/' . $getState()) }}" class="object-cover rounded shadow"
            alt="Foto Unit" style="max-width: 300px; max-height: 200px;">
        <br>
        <a href="{{ asset('storage/' . $getState()) }}" download
            class="px-3 py-1 bg-blue-600 text-gray-800 dark:text-white text-sm rounded hover:bg-blue-700 transition">
            Download
        </a>
    @else
        <p class="text-gray-500">Tidak ada foto yang tersedia.</p>
    @endif
</div>
