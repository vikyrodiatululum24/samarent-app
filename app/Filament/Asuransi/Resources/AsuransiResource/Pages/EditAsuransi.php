<?php

namespace App\Filament\Asuransi\Resources\AsuransiResource\Pages;

use App\Filament\Asuransi\Resources\AsuransiResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Storage;

class EditAsuransi extends EditRecord
{
    protected static string $resource = AsuransiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->before(function ($record) {
                    $this->deleteRelatedFiles($record);
                }),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Ambil data lama untuk perbandingan
        $original = $this->record->getOriginal();

        // Daftar field foto yang perlu dicek
        $imageFields = [
            'foto_ktp',
            'foto_sim',
            'foto_sntk',
            'foto_bpkb',
            'foto_polis_asuransi',
            'foto_ba',
            'foto_keterangan_bengkel',
            'foto_npwp_pt',
            'foto_unit'
        ];

        foreach ($imageFields as $field) {
            // Jika field foto_unit (multiple)
            if ($field === 'foto_unit') {
                $oldFiles = $original[$field] ? (is_array($original[$field]) ? $original[$field] : json_decode($original[$field], true)) : [];
                $newFiles = $data[$field] ?? [];

                // Hapus file yang tidak ada di data baru
                $filesToDelete = array_diff($oldFiles, $newFiles);
                foreach ($filesToDelete as $file) {
                    if ($file && Storage::disk('public')->exists($file)) {
                        Storage::disk('public')->delete($file);
                    }
                }
            } else {
                // Untuk single file
                $oldFile = $original[$field] ?? null;
                $newFile = $data[$field] ?? null;

                // Jika file lama ada dan berbeda dengan file baru, hapus file lama
                if ($oldFile && $oldFile !== $newFile && Storage::disk('public')->exists($oldFile)) {
                    Storage::disk('public')->delete($oldFile);
                }
            }
        }

        return $data;
    }

    protected function deleteRelatedFiles($record): void
    {
        // Daftar field foto yang perlu dihapus
        $imageFields = [
            'foto_ktp',
            'foto_sim',
            'foto_sntk',
            'foto_bpkb',
            'foto_polis_asuransi',
            'foto_ba',
            'foto_keterangan_bengkel',
            'foto_npwp_pt',
            'foto_unit'
        ];

        foreach ($imageFields as $field) {
            $files = $record->{$field};

            if ($files) {
                // Jika foto_unit (multiple files)
                if ($field === 'foto_unit') {
                    $fileArray = is_array($files) ? $files : json_decode($files, true);
                    if (is_array($fileArray)) {
                        foreach ($fileArray as $file) {
                            if (Storage::disk('public')->exists($file)) {
                                Storage::disk('public')->delete($file);
                            }
                        }
                    }
                } else {
                    // Single file
                    if (Storage::disk('public')->exists($files)) {
                        Storage::disk('public')->delete($files);
                    }
                }
            }
        }
    }
}
