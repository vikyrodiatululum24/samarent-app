<x-filament::page>
    {{-- <div class="w-full bg-gray-900 dark:bg-gray-900 px-0 py-8 rounded-md">
        <form method="GET" class="mb-4 flex flex-wrap justify-end gap-2 items-center px-6">
            <input type="text" name="search" value="{{ request('search', '') }}" placeholder="Cari Nopol / Type..."
                class="px-3 py-2 rounded border border-gray-400 focus:outline-none focus:ring focus:border-blue-300 text-gray-900 dark:text-gray-100 dark:bg-gray-800 dark:border-gray-600 placeholder-gray-500 dark:placeholder-gray-400 w-64" />
            <input type="date" name="start_date" value="{{ request('start_date', '') }}"
                class="px-3 py-2 rounded border border-gray-400 focus:outline-none focus:ring focus:border-blue-300 text-gray-900 dark:text-gray-100 dark:bg-gray-800 dark:border-gray-600" />
            <span class="text-gray-700 dark:text-gray-300">s/d</span>
            <input type="date" name="end_date" value="{{ request('end_date', '') }}"
                class="px-3 py-2 rounded border border-gray-400 focus:outline-none focus:ring focus:border-blue-300 text-gray-900 dark:text-gray-100 dark:bg-gray-800 dark:border-gray-600" />
            <button type="submit"
                class="px-4 py-2 rounded-sm bg-gray-700 text-gray-700 dark:text-white hover:bg-gray-600">Cari</button>
        </form>
        <div class="overflow-x-auto w-full">
            <table
                class="w-full min-w-full divide-y divide-gray-700 dark:divide-gray-700 bg-white dark:bg-gray-800 rounded-sm">
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
                        $perPage = request('perPage', $perPage ?? 10);
                        $start = ($page - 1) * $perPage;
                    @endphp
                    @forelse($units as $index => $unit)
                        <tr class="hover:bg-gray-800 dark:hover:bg-gray-700 transition-colors">
                            <td class="px-4 py-3 text-gray-700 dark:text-white font-semibold">
                                <a href="{{ route('filament.admin.pages.detail-histori', ['unit' => 1]) }}"
                                    class="block w-full">
                                    {{ $start + $index + 1 }}
                                </a>
                            </td>
                            <td class="px-4 py-3 text-gray-700 dark:text-white font-semibold">
                                <a href="{{ route('filament.admin.pages.detail-histori', 'id') }}"
                                    class="block w-full">
                                    {{ $unit['id'] }}
                                </a>
                            </td>
                            <td class="px-4 py-3 text-gray-700 dark:text-white font-semibold">
                                <a href="{{ route('filament.admin.pages.detail-histori', 'id') }}"
                                    class="block w-full">
                                    {{ $unit['nopol'] }}
                                </a>
                            </td>
                            <td class="px-4 py-3 text-gray-700 dark:text-white font-semibold">
                                <a href="{{ route('filament.admin.pages.detail-histori', 'id') }}"
                                    class="block w-full">
                                    {{ $unit['type'] }}
                                </a>
                            </td>
                            <td class="px-4 py-3 text-center text-gray-700 dark:text-white font-semibold">
                                <a href="{{ route('filament.admin.pages.detail-histori', 'id') }}"
                                    class="block w-full">
                                    {{ $unit['total_pengajuan'] }}
                                </a>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <a href="{{ route('filament.admin.pages.detail-histori', 'id') }}"
                                    class="block w-full">
                                    @if ($unit['pengajuan_bulan_ini'])
                                        <span class="text-green-400 dark:text-green-400 font-bold">✅</span>
                                    @else
                                        <span class="text-red-400 dark:text-red-400 font-bold">❌</span>
                                    @endif
                                </a>
                            </td>
                            
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-4 text-center text-gray-400 dark:text-gray-300">Tidak ada
                                data</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{-- Pagination --}}
        {{-- @if ($totalPages > 1)
            @php
                $maxButtons = 5;
                $startBtn = max(1, $currentPage - floor($maxButtons / 2));
                $endBtn = min($totalPages, $startBtn + $maxButtons - 1);
                if ($endBtn - $startBtn + 1 < $maxButtons) {
                    $startBtn = max(1, $endBtn - $maxButtons + 1);
                }
            @endphp
            <div class="flex flex-wrap justify-between items-center mt-6 px-6 gap-2">
                <div class="text-gray-300 dark:text-gray-200 text-sm">
                    Menampilkan {{ $units ? $start + 1 : 0 }} - {{ $start + count($units) }} dari {{ $total }}
                    data
                </div>
                <form method="GET" class="flex items-center">
                    <div class="flex items-center border-r border-gray-400 dark:border-gray-600 gap-2">
                        <label for="perPage" class="text-gray-300 dark:text-gray-200 mr-2">Per Page </label>
                        @foreach (request()->except(['page', 'perPage']) as $key => $val)
                            <input type="hidden" name="{{ $key }}" value="{{ $val }}">
                        @endforeach
                        <select name="perPage" onchange="this.form.submit()"
                            class="py-1 rounded border border-gray-400 bg-white dark:bg-gray-800 dark:text-gray-100 dark:border-gray-600">
                            @foreach ([10, 25, 50, 100] as $option)
                                <option value="{{ $option }}"
                                    {{ request('perPage', $perPage ?? 10) == $option ? 'selected' : '' }}>
                                    {{ $option }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </form>
                <div class="flex gap-2">
                    @if ($currentPage > 1)
                        <a href="{{ request()->fullUrlWithQuery(['page' => $currentPage - 1, 'perPage' => $perPage]) }}"
                            class="px-3 py-1 rounded border bg-white text-blue-700 hover:bg-blue-100 dark:bg-gray-800 dark:text-blue-200 dark:border-gray-600 dark:hover:bg-gray-700">
                            &laquo;
                        </a>
                    @endif
                    @for ($i = $startBtn; $i <= $endBtn; $i++)
                        <a href="{{ request()->fullUrlWithQuery(['page' => $i, 'perPage' => $perPage]) }}"
                            class="px-3 py-1 rounded border {{ $i == $currentPage ? 'bg-blue-600 text-gray-700 dark:bg-blue-700 dark:text-white font-bold' : 'bg-white text-blue-700 hover:bg-blue-100 dark:bg-gray-800 dark:text-blue-200 dark:border-gray-600 dark:hover:bg-gray-700' }}">
                            {{ $i }}
                        </a>
                    @endfor
                    @if ($currentPage < $totalPages)
                        <a href="{{ request()->fullUrlWithQuery(['page' => $currentPage + 1, 'perPage' => $perPage]) }}"
                            class="px-3 py-1 rounded border bg-white text-blue-700 hover:bg-blue-100 dark:bg-gray-800 dark:text-blue-200 dark:border-gray-600 dark:hover:bg-gray-700">
                            &raquo;
                        </a>
                    @endif
                </div>
            </div>
        @endif
    </div> --}}
    {{ $this->form }}

    
    {{-- Spacer --}}
    <div class="my-6 border-t"></div>

    {{-- Tabel Data --}}
    {{ $this->table }}
</x-filament::page>
