<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;


class CompressImage
{
    public function compressAndStore($file, $directory)
    {
        // Generate unique filename
        $filename = Str::uuid() . '.jpg';
        $path = $directory . '/' . $filename;

        // Get original image
        $imageData = file_get_contents($file->getRealPath());
        $image = imagecreatefromstring($imageData);

        if ($image === false) {
            throw new \Exception('Failed to create image from uploaded file');
        }

        // Get original dimensions
        $originalWidth = imagesx($image);
        $originalHeight = imagesy($image);

        // Set max width/height (compress jika lebih besar)
        $maxWidth = 1920;
        $maxHeight = 1920;

        // Calculate new dimensions
        $ratio = min($maxWidth / $originalWidth, $maxHeight / $originalHeight);

        // Only resize if image is larger than max dimensions
        if ($ratio < 1) {
            $newWidth = (int)($originalWidth * $ratio);
            $newHeight = (int)($originalHeight * $ratio);
        } else {
            $newWidth = $originalWidth;
            $newHeight = $originalHeight;
        }

        // Create new image with calculated dimensions
        $compressedImage = imagecreatetruecolor($newWidth, $newHeight);

        // Preserve transparency for PNG
        imagealphablending($compressedImage, false);
        imagesavealpha($compressedImage, true);

        // Resize image
        imagecopyresampled(
            $compressedImage,
            $image,
            0, 0, 0, 0,
            $newWidth,
            $newHeight,
            $originalWidth,
            $originalHeight
        );

        // Save compressed image to temporary file
        $tempPath = sys_get_temp_dir() . '/' . $filename;
        imagejpeg($compressedImage, $tempPath, 80); // Quality 80 (0-100)

        // Store to storage
        $stored = Storage::disk('public')->put($path, file_get_contents($tempPath));

        // Clean up
        imagedestroy($image);
        imagedestroy($compressedImage);
        unlink($tempPath);

        if (!$stored) {
            throw new \Exception('Failed to store compressed image');
        }

        return $path;
    }

}
