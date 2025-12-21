<?php

namespace App\Filament\User\Resources\FormTugasResource\Pages;

use App\Filament\User\Resources\FormTugasResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;

class EditFormTugas extends EditRecord
{
    protected static string $resource = FormTugasResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make()
                ->color('info'),
            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getSavedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Form Tugas Diupdate')
            ->body('Form tugas berhasil diperbarui.');
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
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
}
