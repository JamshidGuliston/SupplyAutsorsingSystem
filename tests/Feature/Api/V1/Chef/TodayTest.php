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

class TodayTest extends TestCase
{
    use RefreshDatabase;

    public function test_today_returns_null_when_no_attendance(): void
    {
        Carbon::setTestNow('2026-04-28 08:00:00', 'Asia/Tashkent');
        $kg = Kindgarden::create(['kingar_name' => 'T', 'lat' => 41.31, 'lng' => 69.27, 'geofence_radius' => 200]);
        $chef = User::create(['role_id' => Roles::CHEF, 'name' => 'A', 'email' => 'a@t.l', 'password' => bcrypt('x')]);
        $chef->kindgarden()->attach($kg->id);
        Sanctum::actingAs($chef);

        $resp = $this->getJson('/api/v1/chef/attendance/today');
        $resp->assertOk();
        $resp->assertJson(['attendance' => null]);
        $resp->assertJsonStructure(['kindgarden' => ['id', 'lat', 'lng', 'geofence_radius']]);
    }

    public function test_today_returns_existing_row(): void
    {
        Storage::fake('local');
        Carbon::setTestNow('2026-04-28 08:00:00', 'Asia/Tashkent');
        $kg = Kindgarden::create(['kingar_name' => 'T', 'lat' => 41.3111, 'lng' => 69.2797, 'geofence_radius' => 200]);
        $chef = User::create(['role_id' => Roles::CHEF, 'name' => 'A', 'email' => 'a@t.l', 'password' => bcrypt('x')]);
        $chef->kindgarden()->attach($kg->id);
        Sanctum::actingAs($chef);

        $this->postJson('/api/v1/chef/attendance/check-in', [
            'lat' => 41.3112, 'lng' => 69.2797,
            'captured_at' => now()->toIso8601String(), 'is_mock' => false,
            'photo' => UploadedFile::fake()->image('a.jpg'),
        ])->assertOk();

        $resp = $this->getJson('/api/v1/chef/attendance/today');
        $resp->assertOk();
        $this->assertNotNull($resp->json('attendance.check_in_at'));
        $this->assertNull($resp->json('attendance.check_out_at'));
    }
}
