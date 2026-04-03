<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeocodingService
{
    public function getAddressFromCoordinates($lat, $lng)
    {
        if (!$lat || !$lng) return '-';

        $cacheKey = "geo_" . md5($lat . ',' . $lng);

        return Cache::remember($cacheKey, now()->addDays(7), function () use ($lat, $lng) {

            $response = Http::withHeaders([
                // WAJIB untuk Nominatim (biar tidak diblok)
                'User-Agent' => 'LaravelApp/1.0 (your@email.com)'
            ])->timeout(10)
              ->get("https://nominatim.openstreetmap.org/reverse", [
                  'lat' => $lat,
                  'lon' => $lng,
                  'format' => 'json',
              ]);

            if ($response->successful()) {
                return $response['display_name'] ?? 'Alamat tidak ditemukan';
            }
            return 'Gagal mengambil alamat';
        });
    }
}
