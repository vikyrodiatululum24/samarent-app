<?php

namespace App\Filament\Absensi\Resources\DriverResource\Pages;

use App\Models\User;
use Filament\Actions;
use Illuminate\Support\Facades\Hash;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Absensi\Resources\DriverResource;

class CreateDriver extends CreateRecord
{
    protected static string $resource = DriverResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $user = User::create([
            'name' => $data['user']['name'],
            'email' => $data['user']['email'],
            'password' => Hash::make($data['password']),
            'role' => 'driver',
        ]);

        $data['user_id'] = $user->id;
        unset($data['user']);

        return $data;
    }
}
