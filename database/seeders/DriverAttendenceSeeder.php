<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DriverAttendenceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        for ($i = 1; $i <= 30; $i++) {
            \App\Models\DriverAttendence::create([
                'user_id' => 20,
                'project_id' => 1,
                'end_user_id' => 1,
                'unit_id' => 11,
                'date' => \Carbon\Carbon::create(2024, 10, 1)->addDays($i - 1)->toDateString(),
                'time_in' => '08:00:00',
                'start_km' => 1000 + ($i * 10),
                'note' => 'Driver hadir tepat waktu',
                'location_in' => 'Lokasi Masuk ' . $i,
                'photo_in' => 'storage/driver_attendences/photo_in_' . $i . '.jpg',
                'location_check' => 'Lokasi Cek ' . $i,
                'photo_check' => 'storage/driver_attendences/photo_check_' . $i . '.jpg',
                'time_check' => '12:00:00',
                'end_km' => 1000 + ($i * 10) + 100,
                'time_out' => '17:00:00',
                'location_out' => 'Lokasi Keluar ' . $i,
                'photo_out' => 'storage/driver_attendences/photo_out_' . $i . '.jpg',
                'is_complete' => true,
            ]);
        }
    }
}
