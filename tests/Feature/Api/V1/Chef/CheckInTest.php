<?php

namespace Tests\Feature\Api\V1\Chef;

use App\Constants\Roles;
use App\Models\Kindgarden;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class CheckInTest extends TestCase
{
    use RefreshDatabase;

    private User $chef;
    private Kindgarden $kg;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('local');
        Carbon::setTestNow('2026-04-28 08:00:00', 'Asia/Tashkent');

        $this->kg = Kindgarden::create([
            'kingar_name' => 'Test', 'lat' => 41.3111, 'lng' => 69.2797,
            'geofence_radius' => 200,
        ]);
        $this->chef = User::create([
            'role_id' => Roles::CHEF, 'name' => 'A', 'email' => 'a@t.l',
            'password' => bcrypt('x'),
        ]);
        $this->chef->kindgarden()->attach($this->kg->id);

        Sanctum::actingAs($this->chef);
    }

    public function test_check_in_success(): void
    {
        $resp = $this->postJson('/api/v1/chef/attendance/check-in', [
            'lat' => 41.3112,
            'lng' => 69.2797,
            'captured_at' => now()->toIso8601String(),
            'is_mock' => false,
            'photo' => UploadedFile::fake()->image('s.jpg'),
        ]);

        $resp->assertOk();
        $resp->assertJsonStructure(['attendance' => ['id', 'check_in_at', 'check_in_distance_m']]);
        $this->assertDatabaseHas('chef_attendances', ['user_id' => $this->chef->id]);
    }

    public function test_check_in_outside_geofence_returns_422(): void
    {
        $resp = $this->postJson('/api/v1/chef/attendance/check-in', [
            'lat' => 41.3200, 'lng' => 69.2797,
            'captured_at' => now()->toIso8601String(),
            'is_mock' => false,
            'photo' => UploadedFile::fake()->image('s.jpg'),
        ]);
        $resp->assertStatus(422);
        $resp->assertJson(['error' => 'outside_geofence']);
        $resp->assertJsonStructure(['distance_m', 'max_radius_m']);
    }

    public function test_check_in_mock_gps_returns_422(): void
    {
        $resp = $this->postJson('/api/v1/chef/attendance/check-in', [
            'lat' => 41.3112, 'lng' => 69.2797,
            'captured_at' => now()->toIso8601String(),
            'is_mock' => true,
            'photo' => UploadedFile::fake()->image('s.jpg'),
        ]);
        $resp->assertStatus(422);
        $resp->assertJson(['error' => 'mock_gps_detected']);
    }

    public function test_check_in_already_done_returns_422(): void
    {
        $payload = [
            'lat' => 41.3112, 'lng' => 69.2797,
            'captured_at' => now()->toIso8601String(),
            'is_mock' => false,
        ];
        $this->postJson('/api/v1/chef/attendance/check-in',
            $payload + ['photo' => UploadedFile::fake()->image('a.jpg')])->assertOk();

        $resp = $this->postJson('/api/v1/chef/attendance/check-in',
            $payload + ['photo' => UploadedFile::fake()->image('b.jpg')]);
        $resp->assertStatus(422);
        $resp->assertJson(['error' => 'already_checked_in']);
    }

    public function test_check_in_validation_required_fields(): void
    {
        $resp = $this->postJson('/api/v1/chef/attendance/check-in', []);
        $resp->assertStatus(422);
    }

    public function test_check_in_unauthenticated_returns_401(): void
    {
        $this->app['auth']->forgetGuards();
        $resp = $this->withHeader('Accept', 'application/json')
            ->post('/api/v1/chef/attendance/check-in', []);
        $resp->assertStatus(401);
    }
}
