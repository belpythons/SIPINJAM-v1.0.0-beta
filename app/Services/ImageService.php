<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImageService
{
    /**
     * Center crops an uploaded image to 4:3 ratio, resizes to 800x600,
     * preserves alpha channels (PNG/WebP), and saves to public storage disk.
     *
     * @param UploadedFile $file
     * @param string $folder
     * @return string Path starting with '/storage/folder_name/filename.ext'
     */
    public static function cropAndSave(UploadedFile $file, string $folder): string
    {
        $extension = strtolower($file->getClientOriginalExtension());
        if (!in_array($extension, ['jpg', 'jpeg', 'png', 'webp'])) {
            $extension = 'jpg';
        }
        
        $filename = Str::random(40) . '.' . $extension;
        $targetDir = storage_path('app/public/' . $folder);
        
        if (!file_exists($targetDir)) {
            mkdir($targetDir, 0755, true);
        }

        $targetPath = $targetDir . '/' . $filename;
        $tempPath = $file->getRealPath();

        // 1. Load image using GD
        $image = null;
        switch ($extension) {
            case 'png':
                if (function_exists('imagecreatefrompng')) {
                    $image = @imagecreatefrompng($tempPath);
                }
                break;
            case 'webp':
                if (function_exists('imagecreatefromwebp')) {
                    $image = @imagecreatefromwebp($tempPath);
                }
                break;
            case 'jpg':
            case 'jpeg':
            default:
                if (function_exists('imagecreatefromjpeg')) {
                    $image = @imagecreatefromjpeg($tempPath);
                }
                break;
        }

        // Fallback if GD is disabled or fail
        if (!$image) {
            $path = $file->store($folder, 'public');
            return '/storage/' . $path;
        }

        // 2. Perform 4:3 center crop calculations
        $width = imagesx($image);
        $height = imagesy($image);
        $targetRatio = 4 / 3;
        $currentRatio = $width / $height;

        $cropWidth = $width;
        $cropHeight = $height;
        $xOffset = 0;
        $yOffset = 0;

        if ($currentRatio > $targetRatio) {
            // Original is wider: crop left & right
            $cropWidth = (int)($height * $targetRatio);
            $xOffset = (int)(($width - $cropWidth) / 2);
        } elseif ($currentRatio < $targetRatio) {
            // Original is taller: crop top & bottom
            $cropHeight = (int)($width / $targetRatio);
            $yOffset = (int)(($height - $cropHeight) / 2);
        }

        // 3. Create target true-color canvas (800x600)
        $dstWidth = 800;
        $dstHeight = 600;
        $newImage = imagecreatetruecolor($dstWidth, $dstHeight);

        // Preserve alpha transparency for PNG / WebP
        if ($extension === 'png' || $extension === 'webp') {
            imagealphablending($newImage, false);
            imagesavealpha($newImage, true);
            $transparent = imagecolorallocatealpha($newImage, 255, 255, 255, 127);
            imagefilledrectangle($newImage, 0, 0, $dstWidth, $dstHeight, $transparent);
        }

        // Resample
        imagecopyresampled(
            $newImage,
            $image,
            0, 0,
            $xOffset, $yOffset,
            $dstWidth, $dstHeight,
            $cropWidth, $cropHeight
        );

        // 4. Save
        switch ($extension) {
            case 'png':
                imagepng($newImage, $targetPath, 8);
                break;
            case 'webp':
                imagewebp($newImage, $targetPath, 80);
                break;
            case 'jpg':
            case 'jpeg':
            default:
                imagejpeg($newImage, $targetPath, 85);
                break;
        }

        imagedestroy($image);
        imagedestroy($newImage);

        return '/storage/' . $folder . '/' . $filename;
    }

    /**
     * Safely deletes an old image file from storage.
     *
     * @param string|null $path
     * @return void
     */
    public static function deleteOldImage(?string $path): void
    {
        if (empty($path)) {
            return;
        }

        // Convert '/storage/folder/filename.ext' to 'folder/filename.ext'
        $cleanPath = $path;
        if (str_starts_with($path, '/storage/')) {
            $cleanPath = substr($path, 9);
        } elseif (str_starts_with($path, 'storage/')) {
            $cleanPath = substr($path, 8);
        }

        if (Storage::disk('public')->exists($cleanPath)) {
            Storage::disk('public')->delete($cleanPath);
        }
    }
}
