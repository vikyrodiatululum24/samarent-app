@php
    $buktiPembayaran = $getState() ?? [];
@endphp

<div class="flex gap-4 items-start">
@forelse ($buktiPembayaran as $index => $bukti)
    <a href="{{ \Storage::disk('public')->url($bukti) }}" target="_blank" class="mb-6">
        <h2 class="font-bold text-lg mb-2">Bukti Pembayaran {{ $index + 1 }}</h2>
        <img
        src="{{ \Storage::disk('public')->url($bukti) }}"
        alt="Bukti Pembayaran {{ $index + 1 }}" style="width: 200px;"
        class="rounded-lg border border-gray-300"
        />
    </a>
    @empty
</div>
    <p class="text-gray-500">Tidak ada bukti pembayaran yang tersedia.</p>
@endforelse
