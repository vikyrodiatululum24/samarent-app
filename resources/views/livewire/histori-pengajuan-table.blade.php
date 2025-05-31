<div class="w-full min-h-screen bg-gray-900 dark:bg-gray-900 px-0 py-8">
    <div class="mb-4 flex flex-wrap justify-end gap-2 items-center px-6">
        <input type="text" wire:model.debounce.300ms="search" placeholder="Cari Nopol / Type..."
            class="px-3 py-2 rounded border border-gray-400 focus:outline-none focus:ring focus:border-blue-300 text-gray-900 dark:text-gray-100 dark:bg-gray-800 dark:border-gray-600 placeholder-gray-500 dark:placeholder-gray-400 w-64" />
    </div>
    <div class="overflow-x-auto w-full">
        <table class="w-full min-w-full divide-y divide-gray-700 dark:divide-gray-700 bg-white dark:bg-gray-800">
            <thead>
                <tr class="bg-gradient-to-r from-gray-800 to-gray-700 dark:from-gray-900 dark:to-gray-800">
                    <th
                        class="px-4 py-3 text-left text-xs font-semibold text-gray-300 dark:text-blue-200 uppercase tracking-wider">
                        No</th>
                    <th
                        class="px-4 py-3 text-left text-xs font-semibold text-gray-300 dark:text-blue-200 uppercase tracking-wider">
                        No Polisi</th>
                    <th
                        class="px-4 py-3 text-left text-xs font-semibold text-gray-300 dark:text-blue-200 uppercase tracking-wider">
                        Type</th>
                    <th
                        class="px-4 py-3 text-center text-xs font-semibold text-gray-300 dark:text-blue-200 uppercase tracking-wider">
                        Total Pengajuan</th>
                    <th
                        class="px-4 py-3 text-center text-xs font-semibold text-gray-300 dark:text-blue-200 uppercase tracking-wider">
                        Pengajuan Bulan Ini</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-700 dark:divide-gray-700">
                @php
                    $page = $currentPage ?? 1;
                    $perPage = $perPage ?? 10;
                    $total = $units->count();
                    $start = ($page - 1) * $perPage;
                    $paginated = $units->slice($start, $perPage);
                @endphp
                @forelse($paginated as $index => $unit)
                    <tr class="hover:bg-gray-800 dark:hover:bg-gray-700 transition-colors">
                        <td class="px-4 py-3 text-gray-200 dark:text-blue-100 font-semibold">
                            {{ $start + $index + 1 }}
                        </td>
                        <td class="px-4 py-3 text-gray-200 dark:text-blue-100 font-semibold">
                            {{ $unit['nopol'] }}
                        </td>
                        <td class="px-4 py-3 text-gray-200 dark:text-blue-100 font-semibold">
                            {{ $unit['type'] }}
                        </td>
                        <td class="px-4 py-3 text-center text-gray-200 dark:text-blue-100 font-semibold">
                            {{ $unit['total_pengajuan'] }}
                        </td>
                        <td class="px-4 py-3 text-center">
                            @if ($unit['pengajuan_bulan_ini'])
                                <span class="text-green-400 dark:text-green-400 font-bold">✅</span>
                            @else
                                <span class="text-red-400 dark:text-red-400 font-bold">❌</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-center text-gray-400 dark:text-gray-300">Tidak ada data
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    {{-- Pagination --}}
    @php
        $totalPages = ceil($units->count() / $perPage);
        $maxButtons = 5;
        $currentPage = $currentPage ?? 1;
        $startBtn = max(1, $currentPage - floor($maxButtons / 2));
        $endBtn = min($totalPages, $startBtn + $maxButtons - 1);
        if ($endBtn - $startBtn + 1 < $maxButtons) {
            $startBtn = max(1, $endBtn - $maxButtons + 1);
        }
    @endphp
    @if ($totalPages > 1)
        <div class="flex justify-between items-center mt-6 px-6">
            <div class="text-gray-300 dark:text-gray-200 text-sm">
                Menampilkan {{ $units->count() ? $start + 1 : 0 }} - {{ $start + $paginated->count() }} dari
                {{ $units->count() }} data
            </div>
            <div class="flex gap-2">
                @if ($currentPage > 1)
                    <button wire:click="$set('currentPage', {{ $currentPage - 1 }})"
                        class="px-3 py-1 rounded border bg-white text-blue-700 hover:bg-blue-100 dark:bg-gray-800 dark:text-blue-200 dark:border-gray-600 dark:hover:bg-gray-700">
                        &laquo;
                    </button>
                @endif
                @for ($i = $startBtn; $i <= $endBtn; $i++)
                    <button wire:click="$set('currentPage', {{ $i }})"
                        class="px-3 py-1 rounded border {{ $i == $currentPage ? 'bg-blue-600 text-white dark:bg-blue-700 dark:text-white font-bold' : 'bg-white text-blue-700 hover:bg-blue-100 dark:bg-gray-800 dark:text-blue-200 dark:border-gray-600 dark:hover:bg-gray-700' }}">
                        {{ $i }}
                    </button>
                @endfor
                @if ($currentPage < $totalPages)
                    <button wire:click="$set('currentPage', {{ $currentPage + 1 }})"
                        class="px-3 py-1 rounded border bg-white text-blue-700 hover:bg-blue-100 dark:bg-gray-800 dark:text-blue-200 dark:border-gray-600 dark:hover:bg-gray-700">
                        &raquo;
                    </button>
                @endif
            </div>
        </div>
    @endif
</div>
