<?php

namespace Tests\Unit\Services\Attendance;

use App\Constants\Roles;
use App\Exceptions\Attendance\AlreadyCheckedInException;
use App\Exceptions\Attendance\KindgardenCoordsNotSetException;
use App\Exceptions\Attendance\MockGpsDetectedException;
use App\Exceptions\Attendance\OutsideGeofenceException;
use App\Exceptions\Attendance\StaleCaptureException;
use App\Models\ChefAttendance;
use App\Models\Kindgarden;
use App\Models\User;
use App\Services\Attendance\AttendanceService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AttendanceServiceTest extends TestCase
{
    use RefreshDatabase;

    private AttendanceService $svc;
    private User $chef;
    private Kindgarden $kg;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('local');
        Carbon::setTestNow('2026-04-28 08:00:00', 'Asia/Tashkent');

        $this->kg = Kindgarden::create([
            'kingar_name' => 'Test bog\'cha',
            'lat' => 41.3111,
            'lng' => 69.2797,
            'geofence_radius' => 200,
        ]);
        $this->chef = User::create([
            'role_id' => Roles::CHEF,
            'name' => 'Test Chef',
            'email' => 'chef@test.local',
            'password' => bcrypt('secret'),
        ]);
        $this->chef->kindgarden()->attach($this->kg->id);

        $this->svc = $this->app->make(AttendanceService::class);
    }

    public function test_check_in_happy_path_creates_row_and_stores_selfie(): void
    {
        $photo = UploadedFile::fake()->image('selfie.jpg');

        $att = $this->svc->checkIn(
            user: $this->chef,
            photo: $photo,
            lat: 41.3112,
            lng: 69.2797,
            capturedAt: now(),
            isMock: false,
        );

        $this->assertInstanceOf(ChefAttendance::class, $att);
        $this->assertNotNull($att->check_in_at);
        $this->assertSame($this->chef->id, $att->user_id);
        $this->assertSame($this->kg->id, $att->kindgarden_id);
        $this->assertLessThanOrEqual(15, $att->check_in_distance_m);
        Storage::disk('local')->assertExists($att->check_in_selfie_path);
    }

    public function test_check_in_rejects_mock_gps(): void
    {
        $this->expectException(MockGpsDetectedException::class);
        $this->svc->checkIn(
            $this->chef,
            UploadedFile::fake()->image('s.jpg'),
            41.3111, 69.2797, now(), true,
        );
    }

    public function test_check_in_rejects_outside_geofence(): void
    {
        $this->expectException(OutsideGeofenceException::class);
        $this->svc->checkIn(
            $this->chef,
            UploadedFile::fake()->image('s.jpg'),
            41.3200, 69.2797, now(), false,
        );
    }

    public function test_check_in_rejects_when_already_checked_in_today(): void
    {
        $this->svc->checkIn($this->chef, UploadedFile::fake()->image('a.jpg'),
            41.3112, 69.2797, now(), false);

        $this->expectException(AlreadyCheckedInException::class);
        $this->svc->checkIn($this->chef, UploadedFile::fake()->image('b.jpg'),
            41.3112, 69.2797, now(), false);
    }

    public function test_check_in_rejects_when_kindgarden_coords_not_set(): void
    {
        $this->kg->update(['lat' => null, 'lng' => null]);

        $this->expectException(KindgardenCoordsNotSetException::class);
        $this->svc->checkIn($this->chef, UploadedFile::fake()->image('a.jpg'),
            41.3112, 69.2797, now(), false);
    }

    public function test_check_in_rejects_stale_captured_at(): void
    {
        $this->expectException(StaleCaptureException::class);
        $this->svc->checkIn($this->chef, UploadedFile::fake()->image('a.jpg'),
            41.3112, 69.2797, now()->subMinutes(10), false);
    }
}
