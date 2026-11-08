<?php

namespace App\Support;

class GalleryImages
{
    public static function normalize(mixed $images): array
    {
        if (is_string($images)) {
            $images = json_decode($images, true);
        }

        if (! is_array($images)) {
            return [];
        }

        return array_values(array_filter($images, fn ($image) => is_string($image) && trim($image) !== ''));
    }

    public static function remove(array $images, string $image): ?array
    {
        $target = basename($image);
        $found = false;
        $remaining = [];

        foreach ($images as $current) {
            if (! $found && basename((string) $current) === $target) {
                $found = true;
                continue;
            }
            $remaining[] = $current;
        }

        return $found ? array_values($remaining) : null;
    }

    public static function deleteFile(string $category, string $image): void
    {
        $file = public_path('uploads/'.$category.'/'.basename($image));
        if (is_file($file)) {
            @unlink($file);
        }
    }
}
