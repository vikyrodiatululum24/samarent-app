<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Storage;

class FileStorageHelper
{
    /**
     * Hapus satu atau banyak file.
     *
     * @param string|array|null $files
     * @param string $disk
     * @return void
     */
    public static function delete(string|array|null $files, string $disk = 'public'): void
    {
        if (empty($files)) {
            return;
        }

        foreach ((array) $files as $file) {
            if (blank($file)) {
                continue;
            }

            if (Storage::disk($disk)->exists($file)) {
                Storage::disk($disk)->delete($file);
            }
        }
    }

    /**
     * Hapus file lama jika field berubah.
     *
     * @param mixed $model
     * @param array $fields
     * @param string $disk
     * @return void
     */
    public static function deleteOldFiles($model, array $fields, string $disk = 'public'): void
    {
        foreach ($fields as $field) {
            if (!$model->isDirty($field)) {
                continue;
            }

            $old = $model->getOriginal($field);
            $new = $model->{$field};

            // Jika array (Multiple File Upload)
            if (is_array($old)) {
                $deleted = array_diff($old, (array) $new);

                static::delete($deleted, $disk);

                continue;
            }

            // Single File Upload
            static::delete($old, $disk);
        }
    }

    /**
     * Hapus semua file dari model.
     *
     * @param mixed $model
     * @param array $fields
     * @param string $disk
     * @return void
     */
    public static function deleteModelFiles($model, array $fields, string $disk = 'public'): void
    {
        foreach ($fields as $field) {
            static::delete($model->{$field}, $disk);
        }
    }
}
