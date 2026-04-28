<?php

namespace Tests\Unit\Services\Attendance;

use App\Constants\Roles;
use App\Exceptions\Attendance\AlreadyCheckedInException;
use App\Exceptions\Attendance\AlreadyCheckedOutException;
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

    public function test_check_out_happy_path(): void
    {
        $this->svc->checkIn($this->chef, UploadedFile::fake()->image('in.jpg'),
            41.3112, 69.2797, now(), false);

        Carbon::setTestNow('2026-04-28 17:00:00', 'Asia/Tashkent');

        $att = $this->svc->checkOut($this->chef, UploadedFile::fake()->image('out.jpg'),
            41.3112, 69.2797, now(), false);

        $this->assertNotNull($att->check_out_at);
        Storage::disk('local')->assertExists($att->check_out_selfie_path);
    }

    public function test_check_out_rejects_when_not_checked_in(): void
    {
        $this->expectException(\App\Exceptions\Attendance\AttendanceException::class);
        $this->svc->checkOut($this->chef, UploadedFile::fake()->image('out.jpg'),
            41.3112, 69.2797, now(), false);
    }

    public function test_check_out_rejects_when_already_checked_out(): void
    {
        $this->svc->checkIn($this->chef, UploadedFile::fake()->image('in.jpg'),
            41.3112, 69.2797, now(), false);
        Carbon::setTestNow('2026-04-28 17:00:00', 'Asia/Tashkent');
        $this->svc->checkOut($this->chef, UploadedFile::fake()->image('out.jpg'),
            41.3112, 69.2797, now(), false);

        $this->expectException(AlreadyCheckedOutException::class);
        $this->svc->checkOut($this->chef, UploadedFile::fake()->image('out2.jpg'),
            41.3112, 69.2797, now(), false);
    }

    public function test_replace_check_in_replaces_existing_and_increments_counter(): void
    {
        $first = $this->svc->checkIn($this->chef, UploadedFile::fake()->image('in1.jpg'),
            41.3112, 69.2797, now(), false);
        $oldPath = $first->check_in_selfie_path;

        Carbon::setTestNow('2026-04-28 09:30:00', 'Asia/Tashkent');
        $second = $this->svc->replace($this->chef, 'check_in',
            UploadedFile::fake()->image('in2.jpg'),
            41.3112, 69.2797, now(), false);

        $this->assertSame(1, $second->check_in_replaced_count);
        $this->assertNotSame($oldPath, $second->check_in_selfie_path);
        $this->assertFalse($second->check_in_is_late);
        Storage::disk('local')->assertMissing($oldPath);
    }

    public function test_replace_check_in_as_late_entry_when_no_existing(): void
    {
        Carbon::setTestNow('2026-04-28 11:00:00', 'Asia/Tashkent');

        $att = $this->svc->replace($this->chef, 'check_in',
            UploadedFile::fake()->image('late.jpg'),
            41.3112, 69.2797, now(), false);

        $this->assertNotNull($att->check_in_at);
        $this->assertTrue($att->check_in_is_late);
        $this->assertSame(0, $att->check_in_replaced_count);
    }

    public function test_replace_check_out_replaces_existing(): void
    {
        $this->svc->checkIn($this->chef, UploadedFile::fake()->image('in.jpg'),
            41.3112, 69.2797, now(), false);
        Carbon::setTestNow('2026-04-28 17:00:00', 'Asia/Tashkent');
        $first = $this->svc->checkOut($this->chef, UploadedFile::fake()->image('out1.jpg'),
            41.3112, 69.2797, now(), false);
        $oldPath = $first->check_out_selfie_path;

        Carbon::setTestNow('2026-04-28 17:30:00', 'Asia/Tashkent');
        $second = $this->svc->replace($this->chef, 'check_out',
            UploadedFile::fake()->image('out2.jpg'),
            41.3112, 69.2797, now(), false);

        $this->assertSame(1, $second->check_out_replaced_count);
        $this->assertNotSame($oldPath, $second->check_out_selfie_path);
    }

    public function test_replace_check_out_rejects_when_not_checked_in_yet(): void
    {
        $this->expectException(\App\Exceptions\Attendance\AttendanceException::class);
        $this->svc->replace($this->chef, 'check_out',
            UploadedFile::fake()->image('o.jpg'),
            41.3112, 69.2797, now(), false);
    }

    public function test_replace_invalid_type_throws(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->svc->replace($this->chef, 'lunch',
            UploadedFile::fake()->image('o.jpg'),
            41.3112, 69.2797, now(), false);
    }

    public function test_record_location_events_inserts_all_with_distance(): void
    {
        $events = [
            ['event_type' => 'exit', 'lat' => 41.3200, 'lng' => 69.2797,
             'happened_at' => now()->toIso8601String(), 'is_mock' => false],
            ['event_type' => 'enter', 'lat' => 41.3112, 'lng' => 69.2797,
             'happened_at' => now()->addMinutes(5)->toIso8601String(), 'is_mock' => false],
        ];

        $count = $this->svc->recordLocationEvents($this->chef, $events);

        $this->assertSame(2, $count);
        $this->assertSame(2, \App\Models\ChefLocationEvent::count());
        $first = \App\Models\ChefLocationEvent::orderBy('id')->first();
        $this->assertSame('exit', $first->event_type);
        $this->assertGreaterThan(800, $first->distance_m);
    }

    public function test_record_location_events_throws_when_kindgarden_coords_missing(): void
    {
        $this->kg->update(['lat' => null, 'lng' => null]);
        $this->expectException(KindgardenCoordsNotSetException::class);
        $this->svc->recordLocationEvents($this->chef, [
            ['event_type' => 'exit', 'lat' => 41.32, 'lng' => 69.28,
             'happened_at' => now()->toIso8601String(), 'is_mock' => false],
        ]);
    }

    public function test_record_location_events_rejects_invalid_event_type(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->svc->recordLocationEvents($this->chef, [
            ['event_type' => 'lunch', 'lat' => 41.31, 'lng' => 69.27,
             'happened_at' => now()->toIso8601String(), 'is_mock' => false],
        ]);
    }
}
