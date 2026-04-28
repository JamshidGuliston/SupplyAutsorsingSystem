<?php

namespace App\Services\Attendance;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SelfieStorage
{
    private const DISK = 'local';

    public function store(UploadedFile $file, int $userId, string $type, string $date): string
    {
        $dir = "attendance/{$date}";
        $name = sprintf('%d_%s_%s_%s.%s', $userId, $type, time(), Str::random(8), $file->extension() ?: 'jpg');
        $stored = Storage::disk(self::DISK)->putFileAs($dir, $file, $name);
        return $stored;
    }

    public function delete(?string $path): void
    {
        if (!$path) {
            return;
        }
        if (Storage::disk(self::DISK)->exists($path)) {
            Storage::disk(self::DISK)->delete($path);
        }
    }
}
