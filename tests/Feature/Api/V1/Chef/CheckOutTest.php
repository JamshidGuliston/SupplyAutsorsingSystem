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

class CheckOutTest extends TestCase
{
    use RefreshDatabase;

    private User $chef;
    private Kindgarden $kg;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('local');
        Carbon::setTestNow('2026-04-28 08:00:00', 'Asia/Tashkent');

        $this->kg = Kindgarden::create(['kingar_name' => 'T',
            'lat' => 41.3111, 'lng' => 69.2797, 'geofence_radius' => 200]);
        $this->chef = User::create([
            'role_id' => Roles::CHEF, 'name' => 'A', 'email' => 'a@t.l',
            'password' => bcrypt('x'),
        ]);
        $this->chef->kindgarden()->attach($this->kg->id);
        Sanctum::actingAs($this->chef);

        $this->postJson('/api/v1/chef/attendance/check-in', [
            'lat' => 41.3112, 'lng' => 69.2797,
            'captured_at' => now()->toIso8601String(), 'is_mock' => false,
            'photo' => UploadedFile::fake()->image('in.jpg'),
        ])->assertOk();
    }

    public function test_check_out_success(): void
    {
        Carbon::setTestNow('2026-04-28 17:00:00', 'Asia/Tashkent');
        $resp = $this->postJson('/api/v1/chef/attendance/check-out', [
            'lat' => 41.3112, 'lng' => 69.2797,
            'captured_at' => now()->toIso8601String(), 'is_mock' => false,
            'photo' => UploadedFile::fake()->image('out.jpg'),
        ]);
        $resp->assertOk();
        $resp->assertJsonStructure(['attendance' => ['check_out_at']]);
    }

    public function test_check_out_without_check_in_returns_422(): void
    {
        $other = User::create(['role_id' => Roles::CHEF, 'name' => 'B',
            'email' => 'b@t.l', 'password' => bcrypt('x')]);
        $other->kindgarden()->attach($this->kg->id);
        Sanctum::actingAs($other);

        $resp = $this->postJson('/api/v1/chef/attendance/check-out', [
            'lat' => 41.3112, 'lng' => 69.2797,
            'captured_at' => now()->toIso8601String(), 'is_mock' => false,
            'photo' => UploadedFile::fake()->image('out.jpg'),
        ]);
        $resp->assertStatus(422);
        $resp->assertJson(['error' => 'not_checked_in']);
    }
}
