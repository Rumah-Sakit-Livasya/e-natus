<?php

namespace App\Support;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class StoragePublicUrl
{
    public static function fromPath(?string $path): ?string
    {
        if (blank($path)) {
            return null;
        }

        $normalized = trim((string) $path);

        if (Str::startsWith($normalized, ['http://', 'https://', 'data:', 'blob:'])) {
            return $normalized;
        }

        if (Str::startsWith($normalized, '/storage/')) {
            return $normalized;
        }

        if (Str::startsWith($normalized, 'storage/')) {
            return asset($normalized);
        }

        $normalized = preg_replace('#^/?public/#', '', $normalized) ?? $normalized;
        $normalized = preg_replace('#^/?storage/app/public/#', '', $normalized) ?? $normalized;
        $normalized = preg_replace('#^/?app/public/#', '', $normalized) ?? $normalized;
        $normalized = ltrim($normalized, '/');

        if ($normalized === '') {
            return null;
        }

        return Storage::disk('public')->url($normalized);
    }
}
