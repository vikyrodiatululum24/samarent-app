<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class HolidayDates
{
    public static function getHolidayDates($year)
    {
        $cacheKey = 'holidays_' . $year;
        Log::info('Fetching from helper: ' . $year);

        $data = Cache::remember(
            $cacheKey,
            now()->addDays(30),
            function () use ($year) {
                $response = Http::timeout(5)->get(
                    "https://libur.deno.dev/api?tahun={$year}"
                );

                if ($response->successful()) {
                    return collect(
                        json_decode($response->body(), true)
                    );
                }

                return collect();
            }
        );
        Log::info('Fetched data: ', [
            'count' => $data->count(),
            'year' => $year,
        ]);
        return $data;
    }
}
