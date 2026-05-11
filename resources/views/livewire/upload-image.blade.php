<!-- resources/views/livewire/upload-image.blade.php -->

<div class="space-y-6">
    @if (session()->has('error'))
        <x-filament::section>
            <div class="text-sm font-medium text-danger-600">
                {{ session('error') }}
            </div>
        </x-filament::section>
    @endif

    <form wire:submit.prevent="printImages" class="space-y-6">
        <x-filament::section>
            <div class="flex flex-col items-center gap-4 text-center">

                <label for="File" class="inline-flex cursor-pointer">
                    <x-filament::button tag="span" color="gray">
                        Browse files
                    </x-filament::button>
                </label>

                <input type="file" wire:model="images" multiple accept="image/*" id="File" class="sr-only">
            </div>
        </x-filament::section>

        @if ($images)
            <x-filament::section>
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <h3 class="text-sm font-medium text-gray-950 dark:text-white">
                        Preview Gambar
                    </h3>

                    <div class="text-sm text-gray-500 dark:text-gray-400">
                        Total gambar: {{ count($images) }} / 27
                    </div>
                </div>

                <div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-3 lg:grid-cols-6">
                    @foreach ($this->getTempImageUrls() as $url)
                        <div class="overflow-hidden">
                            <img src="{{ $url }}" alt="Preview gambar" class="h-auto w-full object-cover">
                        </div>
                    @endforeach
                </div>

                <div class="mt-6">
                    <x-filament::button type="submit" color="primary">
                        Download PDF
                    </x-filament::button>
                </div>
            </x-filament::section>
        @endif
    </form>
</div>
