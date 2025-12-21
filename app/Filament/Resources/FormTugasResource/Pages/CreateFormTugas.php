<?php

namespace App\Filament\Resources\FormTugasResource\Pages;

use App\Filament\Resources\FormTugasResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;

class CreateFormTugas extends CreateRecord
{
    protected static string $resource = FormTugasResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Form Tugas Dibuat')
            ->body('Form tugas berhasil dibuat dengan lengkap.');
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Auto-fill user_id jika belum ada
        if (empty($data['user_id'])) {
            $data['user_id'] = auth()->id();
        }

        // Auto-generate no_form jika belum ada
        if (empty($data['no_form'])) {
            $data['no_form'] = $this->generateNoForm();
        }

        // Auto calculate total if not set
        if (!isset($data['total']) || $data['total'] == 0) {
            $data['total'] =
                ($data['bbm'] ?? 0) +
                ($data['toll'] ?? 0) +
                ($data['penginapan'] ?? 0) +
                ($data['uang_dinas'] ?? 0) +
                ($data['entertaint_customer'] ?? 0);
        }

        return $data;
    }

    protected function generateNoForm(): string
    {
        $date = now()->format('Ymd');
        $lastForm = \App\Models\FormTugas::whereDate('created_at', today())
            ->orderBy('id', 'desc')
            ->first();

        if ($lastForm && str_starts_with($lastForm->no_form, 'FT-' . $date)) {
            $lastNumber = (int) substr($lastForm->no_form, -4);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return 'FT-' . $date . '-' . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }
}
