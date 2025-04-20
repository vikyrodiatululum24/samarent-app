@if (!empty($getState()) && is_iterable($getState()))
    <p class="label mb-2">Foto Tambahan</p>
    <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
        @foreach ($getState() as $image)
            <div class="flex flex-col items-center gap-2 mb-2">
                <img src="{{ asset('storage/' . $image) }}" class="object-cover rounded-lg shadow-md" alt="Foto Tambahan" />
                <a href="{{ asset('storage/' . $image()) }}" download class="px-3 py-1 bg-blue-600 text-white text-sm rounded hover:bg-blue-700 transition">
                    Download
                </a>
            </div>
        @endforeach
    </div>
@else
    <p class="text-gray-500">Tidak ada foto tambahan.</p>
@endif
