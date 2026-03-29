@inject('geo', \App\Services\GeocodingService::class)
@php
    $attendance_id = $getState();
    $checks = $attendance_id ? \App\Models\DriverCheck::where('attendance_id', $attendance_id)->get() : collect();
@endphp

@if($checks->isEmpty())
    <div>-</div>
@else
    <div class="space-y-3">
        @foreach ($checks as $item)
            @php
                [$lat, $lng] = explode(',', $item->location);
                $address = $geo->getAddressFromCoordinates($lat, $lng);
            @endphp
            <div class="p-3 border rounded-lg">
                <div>🕒 {{ $item->created_at ? $item->created_at->format('H:i:s') : '-' }}</div>
                <div>📍 {{ $address }}</div>
                <div class="text-sm text-gray-500">
                    ({{ $item->location }})
                </div>
                <img src="{{ asset($item->photo) }}" alt="Foto Check" class="mt-2 w-32 h-auto rounded">
            </div>
        @endforeach
    </div>
@endif
