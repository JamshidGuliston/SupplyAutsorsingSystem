<?php

namespace Tests\Unit\Services\Attendance;

use App\Services\Attendance\SelfieStorage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SelfieStorageTest extends TestCase
{
    public function test_store_writes_to_attendance_subdir_with_user_and_type_prefix(): void
    {
        Storage::fake('local');
        $svc = new SelfieStorage();
        $file = UploadedFile::fake()->image('selfie.jpg');

        $path = $svc->store($file, userId: 42, type: 'check_in', date: '2026-04-28');

        $this->assertStringStartsWith('attendance/2026-04-28/42_check_in_', $path);
        $this->assertStringEndsWith('.jpg', $path);
        Storage::disk('local')->assertExists($path);
    }

    public function test_delete_removes_file(): void
    {
        Storage::fake('local');
        $svc = new SelfieStorage();
        $file = UploadedFile::fake()->image('selfie.jpg');
        $path = $svc->store($file, 42, 'check_in', '2026-04-28');

        $svc->delete($path);

        Storage::disk('local')->assertMissing($path);
    }

    public function test_delete_silently_ignores_missing_file(): void
    {
        Storage::fake('local');
        $svc = new SelfieStorage();
        $svc->delete('attendance/2026-04-28/missing.jpg'); // must not throw
        $this->assertTrue(true);
    }
}
