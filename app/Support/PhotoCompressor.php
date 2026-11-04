<?php

namespace App\Support;

use Illuminate\Http\UploadedFile;

class PhotoCompressor
{
    public const MAX_BYTES = 2_097_152; // 2 MiB

    private const MAX_DIMENSION = 4096;

    private const QUALITY_START = 90;

    private const QUALITY_MIN = 30;

    /**
     * Kompres foto upload menjadi JPG maksimal 2MB.
     * Mengembalikan path relatif di disk public, atau null kalau gagal decode.
     */
    public static function compressToPublic(UploadedFile $file, string $directory): ?string
    {
        $binary = file_get_contents($file->getRealPath());
        if ($binary === false) {
            return null;
        }

        $source = @imagecreatefromstring($binary);
        if ($source === false) {
            return null;
        }

        $source = self::autoOrient($source, $file->getRealPath());

        $width = imagesx($source);
        $height = imagesy($source);
        $scale = min(1.0, self::MAX_DIMENSION / max($width, $height));
        if ($scale < 1.0) {
            $newW = max(1, (int) round($width * $scale));
            $newH = max(1, (int) round($height * $scale));
            $resized = imagecreatetruecolor($newW, $newH);
            imagecopyresampled($resized, $source, 0, 0, 0, 0, $newW, $newH, $width, $height);
            imagedestroy($source);
            $source = $resized;
            $width = $newW;
            $height = $newH;
        }

        $quality = self::QUALITY_START;
        $temp = tempnam(sys_get_temp_dir(), 'proof_');
        if ($temp === false) {
            imagedestroy($source);

            return null;
        }

        // Turunkan quality sampe muat 2MB (atau mentok di QUALITY_MIN)
        while ($quality >= self::QUALITY_MIN) {
            imagejpeg($source, $temp, $quality);
            if (filesize($temp) <= self::MAX_BYTES) {
                break;
            }
            $quality -= 10;
        }

        // Masih kegedean? Perkecil dimensi lagi (kualitas terendah)
        while (filesize($temp) > self::MAX_BYTES && $width > 800 && $height > 800) {
            $width = (int) max(800, $width * 0.75);
            $height = (int) max(800, $height * 0.75);
            $resized = imagecreatetruecolor($width, $height);
            imagecopyresampled($resized, $source, 0, 0, 0, 0, $width, $height, imagesx($source), imagesy($source));
            imagedestroy($source);
            $source = $resized;
            imagejpeg($source, $temp, self::QUALITY_MIN);
        }

        imagedestroy($source);

        $filename = bin2hex(random_bytes(16)).'.jpg';
        $relative = trim($directory, '/').'/'.$filename;
        $absolute = \Storage::disk('public')->path($relative);

        $dir = dirname($absolute);
        if (! is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        if (! rename($temp, $absolute) && ! copy($temp, $absolute)) {
            @unlink($temp);

            return null;
        }
        @unlink($temp);

        return $relative;
    }

    /** Putar foto sesuai EXIF Orientation supaya tidak miring. */
    private static function autoOrient(\GdImage $image, string $path): \GdImage
    {
        if (! function_exists('exif_read_data')) {
            return $image;
        }

        $exif = @exif_read_data($path);
        $orientation = $exif['Orientation'] ?? 1;

        switch ($orientation) {
            case 3:
                $rotated = imagerotate($image, 180, 0);
                break;
            case 6:
                $rotated = imagerotate($image, -90, 0);
                break;
            case 8:
                $rotated = imagerotate($image, 90, 0);
                break;
            default:
                return $image;
        }

        if ($rotated === false) {
            return $image;
        }
        imagedestroy($image);

        return $rotated;
    }
}
