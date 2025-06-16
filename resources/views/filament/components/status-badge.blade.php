{{-- resources/views/filament/components/status-badge.blade.php --}}
@php
    $status = $record->keterangan_proses ?? 'cs';
    $badgeClass = match ($status) {
        'cs' => 'bg-black text-white', // Hitam
        'checker' => 'bg-red-600 text-white', // Merah
        'pengajuan finance' => 'bg-blue-600 text-white', // Biru
        'finance' => 'bg-yellow-900 text-white', // Coklat
        'otorisasi' => 'bg-yellow-400 text-black', // Kuning
        'done' => 'bg-green-600 text-white', // Hijau
        default => 'bg-gray-400 text-white',
    };
@endphp
<span class="px-2 py-1 rounded text-xs font-semibold {{ $badgeClass }}">
    {{ match ($status) {
        'cs' => 'Customer Service',
        'checker' => 'Checker',
        'pengajuan finance' => 'Pengajuan Finance',
        'finance' => 'Input Finance',
        'otorisasi' => 'Otorisasi',
        'done' => 'Selesai',
        default => 'Tidak Diketahui',
    } }}
</span>
