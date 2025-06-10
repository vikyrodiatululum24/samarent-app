{{-- @props(['record']) --}}

@php
    $pengajuan_id = $getState();
    $page = request()->get('unit_page', 1);
    $perPage = 3;
    $serviceUnitsQuery = \App\Models\ServiceUnit::with('unit')->where('pengajuan_id', $pengajuan_id);

    $total = $serviceUnitsQuery->count();
    $serviceUnits = $serviceUnitsQuery->forPage($page, $perPage)->get();

    $lastPage = ceil($total / $perPage);
@endphp

@forelse ($serviceUnits as $serviceUnit)
    @php
        $dataUnit = $serviceUnit->unit;
    @endphp
    <h2 class="font-bold text-lg mb-2">Unit {{ $loop->iteration + ($page - 1) * $perPage }}</h2>
    <div class="flex items-center">
        <span class="font-semibold w-56 text-gray-700 dark:text-gray-200">Nomor Polisi : </span>
        <span class="text-gray-900 dark:text-white">{{ $dataUnit->nopol ?? '-' }}</span>
    </div>
    <div class="flex items-center">
        <span class="font-semibold w-56 text-gray-700 dark:text-gray-200">Jenis Kendaraan : </span>
        <span class="text-gray-900 dark:text-white">{{ $dataUnit->jenis ?? '-' }}</span>
    </div>
    <div class="flex items-center">
        <span class="font-semibold w-56 text-gray-700 dark:text-gray-200">Tipe Unit : </span>
        <span class="text-gray-900 dark:text-white">{{ $dataUnit->type ?? '-' }}</span>
    </div>
    <div class="flex items-center">
        <span class="font-semibold w-56 text-gray-700 dark:text-gray-200">Odometer : </span>
        <span class="text-gray-900 dark:text-white">{{ $serviceUnit->odometer ?? '-' }}</span>
    </div>
    <div class="flex items-center">
        <span class="font-semibold w-56 text-gray-700 dark:text-gray-200">Jenis Permintaan Service : </span>
        <span class="text-gray-900 dark:text-white">{{ $serviceUnit->service ?? '-' }}</span>
    </div>
    <div class="flex flex-wrap gap-4 mt-4">
        <div class="mt-6">
            @php
                $fotoOdometer = $serviceUnit->foto_odometer ?? null;
            @endphp
            @include('filament.components.foto-odometer', ['getState' => fn() => $fotoOdometer])
        </div>
        <div class="mt-6">
            @php
                $fotoUnit = $serviceUnit->foto_unit ?? null;
            @endphp
            @include('filament.components.foto-unit', ['getState' => fn() => $fotoUnit])
        </div>
        {{-- <div class="mt-6">
            @php
                $fotoPengerjaan = $serviceUnit->foto_pengerjaan_bengkel ?? null;
            @endphp
            @include('filament.components.foto-pengerjaan-bengkel', [
                'getState' => fn() => $fotoPengerjaan,
            ])
        </div> --}}
    </div>
    <div class="mt-6">
        @php
            $fotoKondisi = is_array($serviceUnit->foto_kondisi ?? null) ? $serviceUnit->foto_kondisi : [];
        @endphp
        @include('filament.components.foto-kondisi', ['getState' => fn() => $fotoKondisi])
    </div>
@empty
    <p>Tidak ada data unit yang terhubung.</p>
@endforelse

@if ($lastPage > 1)
    <div class="flex justify-center mt-6 space-x-2 gap-2">
        <a href="{{ $page > 1 ? request()->fullUrlWithQuery(['unit_page' => $page - 1]) : '#' }}"
            class="px-3 py-1 rounded border transition-colors duration-150
                {{ $page == 1 ? 'bg-gray-200 dark:bg-gray-700 text-gray-400 cursor-not-allowed' : 'bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-200 dark:hover:text-white' }}"
            @if($page == 1) aria-disabled="true" tabindex="-1" @endif>
            Prev
        </a>
        <a href="{{ $page < $lastPage ? request()->fullUrlWithQuery(['unit_page' => $page + 1]) : '#' }}"
            class="px-3 py-1 rounded border transition-colors duration-150
                {{ $page == $lastPage ? 'bg-gray-200 dark:bg-gray-700 text-gray-400 cursor-not-allowed' : 'bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-200 dark:hover:text-white' }}"
            @if($page == $lastPage) aria-disabled="true" tabindex="-1" @endif>
            Next
        </a>
    </div>
@endif
