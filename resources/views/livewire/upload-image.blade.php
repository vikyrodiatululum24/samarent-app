<!-- resources/views/livewire/upload-image.blade.php -->

<div class="container mt-4">
    @if (session()->has('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <form wire:submit.prevent="printImages">
        <label for="File"
            class="flex flex-col items-center rounded border border-gray-300 p-4 text-gray-900 shadow-sm sm:p-6">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                stroke="currentColor" class="w-5 h-5">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M7.5 7.5h-.75A2.25 2.25 0 0 0 4.5 9.75v7.5a2.25 2.25 0 0 0 2.25 2.25h7.5a2.25 2.25 0 0 0 2.25-2.25v-7.5a2.25 2.25 0 0 0-2.25-2.25h-.75m0-3-3-3m0 0-3 3m3-3v11.25m6-2.25h.75a2.25 2.25 0 0 1 2.25 2.25v7.5a2.25 2.25 0 0 1-2.25 2.25h-7.5a2.25 2.25 0 0 1-2.25-2.25v-.75" />
            </svg>

            <span class="mt-4 font-medium"> Upload your file(s) </span>

            <span
                class="mt-2 inline-block rounded border border-gray-200 bg-gray-50 px-3 py-1.5 text-center text-xs font-medium text-gray-700 shadow-sm hover:bg-gray-100">
                Browse files
            </span>

            <input type="file" wire:model="images" multiple accept="image/*" class="hidden" id="File">
        </label>

        @if ($images)
            <div class="mb-2 text-sm text-gray-700">
                Total gambar: {{ count($images) }} / 27
            </div>
            <div class="flex flex-wrap">
                @foreach ($this->getTempImageUrls() as $url)
                    <div class="w-1/3 p-0.5">
                        <img src="{{ $url }}" class="img-thumbnail"
                            style="width: 200px; height: 200px; object-fit: cover;">
                    </div>
                @endforeach
            </div>
            <button type="submit">
                <span
                    class="mt-2 inline-block rounded border border-gray-200 bg-gray-50 px-3 py-1.5 text-center text-xs font-medium text-gray-700 shadow-sm hover:bg-gray-100">
                    <svg xmlns="http://www.w3.org/2000/svg" class="inline w-4 h-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v12m0 0l-4-4m4 4l4-4m-8 8h8" />
                    </svg>
                    Download PDF
                </span>
            </button>
            @endif
    </form>
</div>
