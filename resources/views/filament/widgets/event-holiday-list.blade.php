<x-filament-widgets::widget>
    <x-filament::section heading="Jadwal & Hari Libur Nasional Bulan Ini">
        <div class="space-y-2">
            @forelse ($this->getListItems() as $item)
                <div class="flex items-center gap-3 rounded-lg border border-gray-200 p-3 dark:border-gray-700">
                    <span
                        class="h-3 w-3 shrink-0 rounded-full"
                        style="background-color: {{ $item['color'] }}"
                    ></span>

                    <span class="w-24 shrink-0 text-sm font-medium text-gray-500 dark:text-gray-400">
                        {{ \Carbon\Carbon::parse($item['date'])->translatedFormat('d M Y') }}
                    </span>

                    <div class="flex-1 text-sm text-gray-900 dark:text-gray-100">
                        {{ $item['title'] }}
                        <p class="flex-1 text-xs text-gray-500 dark:text-gray-400">
                            {{ $item['description'] }}
                        </p>
                    </div>

                    @if ($item['type'] === 'holiday')
                        <span class="rounded-full bg-red-100 px-2 py-0.5 text-xs font-medium text-red-700 dark:bg-red-900 dark:text-red-300">
                            Libur Nasional
                        </span>
                    @endif
                </div>
            @empty
                <p class="text-sm text-gray-500 dark:text-gray-400">Tidak ada jadwal atau libur bulan ini.</p>
            @endforelse
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
