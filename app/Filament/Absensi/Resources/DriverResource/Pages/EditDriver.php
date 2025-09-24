<?php

namespace App\Filament\Absensi\Resources\DriverResource\Pages;

use App\Filament\Absensi\Resources\DriverResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDriver extends EditRecord
{
    protected static string $resource = DriverResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['user'] = [
            'name' => $this->record->user->name,
            'email' => $this->record->user->email,
        ];

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (isset($data['user'])) {
            $this->record->user->update([
                'name' => $data['user']['name'],
                'email' => $data['user']['email'],
                'password' => isset($data['password']) ? \Illuminate\Support\Facades\Hash::make($data['password']) : $this->record->user->password,
            ]);
            unset($data['user']);
        }

        return $data;
    }
}
