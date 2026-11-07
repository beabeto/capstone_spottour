<?php

namespace App\Support;

use Illuminate\Support\Str;

class ListingImage
{
    public static function url(?string $value, string $category): string
    {
        $fallback = asset('images/listing-placeholder.svg');
        $value = trim((string) $value);

        if ($value === '') {
            return $fallback;
        }

        if (preg_match('~^data:image/~i', $value)) {
            return $value;
        }

        $isUrl = (bool) preg_match('~^(https?:)?//~i', $value);
        $path = str_replace('\\', '/', $isUrl ? (parse_url($value, PHP_URL_PATH) ?: '') : $value);
        if ($isUrl && stripos($path, 'uploads/'.$category.'/') === false) {
            return $value;
        }
        $path = ltrim($path, '/');
        $path = preg_replace('~^(?:public/)+~i', '', $path);

        $uploadsPosition = stripos($path, 'uploads/');
        if ($uploadsPosition !== false) {
            $relative = substr($path, $uploadsPosition);
        } elseif (Str::startsWith(strtolower($path), strtolower($category).'/')) {
            $relative = 'uploads/'.$path;
        } else {
            $relative = 'uploads/'.$category.'/'.$path;
        }

        $relative = preg_replace('~/{2,}~', '/', $relative);
        $file = public_path($relative);

        return is_file($file) ? asset($relative) : $fallback;
    }
}
