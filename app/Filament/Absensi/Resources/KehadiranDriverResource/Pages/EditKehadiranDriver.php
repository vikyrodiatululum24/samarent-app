<?php

namespace App\Filament\Absensi\Resources\KehadiranDriverResource\Pages;

use App\Filament\Absensi\Resources\KehadiranDriverResource;
use App\Helpers\PayrollHelpers;
use Filament\Actions;
use App\Models\Driver;
use Filament\Resources\Pages\EditRecord;

class EditKehadiranDriver extends EditRecord
{
    protected static string $resource = KehadiranDriverResource::class;

    protected function mutateFormDataBeforeFill(array $data): array
    {
        foreach (['photo_in', 'photo_out'] as $field) {
            if (! empty($data[$field])) {
                $data[$field] = str_replace('storage/', '', $data[$field]);
            }
        }

        if (! empty($data['checks']) && is_array($data['checks'])) {
            foreach ($data['checks'] as $index => $check) {
                if (! empty($check['photo'])) {
                    $data['checks'][$index]['photo'] = str_replace('storage/', '', $check['photo']);
                }
            }
        }

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Helper to ensure DB values use the `storage/` prefix when appropriate
        $ensureStoragePrefix = function (?string $path) {
            if (blank($path)) {
                return null;
            }
            $path = (string) $path;
            if (str_starts_with($path, 'http')) {
                return $path;
            }
            if (str_starts_with($path, 'storage/')) {
                return $path;
            }
            return 'storage/' . ltrim($path, '/');
        };

        // photo_in
        if (! array_key_exists('photo_in', $data) || blank($data['photo_in'])) {
            $data['photo_in'] = $ensureStoragePrefix($this->record->photo_in ?? null);
        } else {
            $data['photo_in'] = $ensureStoragePrefix($data['photo_in']);
        }

        // photo_out
        if (! array_key_exists('photo_out', $data) || blank($data['photo_out'])) {
            $data['photo_out'] = $ensureStoragePrefix($this->record->photo_out ?? null);
        } else {
            $data['photo_out'] = $ensureStoragePrefix($data['photo_out']);
        }

        // checks photos
        if (! empty($data['checks'])) {
            foreach ($data['checks'] as $index => $check) {
                $incoming = $check['photo'] ?? null;
                if (blank($incoming)) {
                    $existing = null;
                    if (! empty($check['id'])) {
                        $existing = $this->record->checks()->find($check['id']);
                    }
                    if (! $existing) {
                        $existing = $this->record->checks->get($index);
                    }
                    $data['checks'][$index]['photo'] = $ensureStoragePrefix($existing->photo ?? null);
                } else {
                    $data['checks'][$index]['photo'] = $ensureStoragePrefix($incoming);
                }
            }
        }

        // When editing, the form `user_id` holds the selected driver id.
        if (! empty($data['user_id'])) {
            $driver = Driver::where('user_id', $data['user_id'])->first();
            if ($driver) {
                $data['driver_id'] = $driver->id;
                $data['user_id'] = $driver->user_id;
            }
        }
        return $data;
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function afterSave(): void
    {
        if ($this->record->is_complete) {
            PayrollHelpers::calculateOvertimePay($this->record);
        }
    }

    public function getRelationManagers(): array
    {
        return [
            //
        ];
    }
}
