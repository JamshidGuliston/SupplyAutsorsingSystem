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

class ReplaceTest extends TestCase
{
    use RefreshDatabase;

    private User $chef;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('local');
        Carbon::setTestNow('2026-04-28 08:00:00', 'Asia/Tashkent');

        $kg = Kindgarden::create(['kingar_name' => 'T',
            'lat' => 41.3111, 'lng' => 69.2797, 'geofence_radius' => 200]);
        $this->chef = User::create(['role_id' => Roles::CHEF,
            'name' => 'A', 'email' => 'a@t.l', 'password' => bcrypt('x')]);
        $this->chef->kindgarden()->attach($kg->id);
        Sanctum::actingAs($this->chef);
    }

    public function test_replace_check_in_after_existing_increments_counter(): void
    {
        $payload = [
            'lat' => 41.3112, 'lng' => 69.2797,
            'captured_at' => now()->toIso8601String(), 'is_mock' => false,
        ];
        $this->postJson('/api/v1/chef/attendance/check-in',
            $payload + ['photo' => UploadedFile::fake()->image('a.jpg')])->assertOk();

        Carbon::setTestNow('2026-04-28 09:30:00', 'Asia/Tashkent');
        $resp = $this->postJson('/api/v1/chef/attendance/replace',
            $payload + ['type' => 'check_in', 'photo' => UploadedFile::fake()->image('b.jpg'),
            'captured_at' => now()->toIso8601String()]);

        $resp->assertOk();
        $this->assertSame(1, $resp->json('attendance.check_in_replaced_count'));
    }

    public function test_replace_check_in_as_late_entry_when_no_existing(): void
    {
        Carbon::setTestNow('2026-04-28 11:00:00', 'Asia/Tashkent');
        $resp = $this->postJson('/api/v1/chef/attendance/replace', [
            'type' => 'check_in',
            'lat' => 41.3112, 'lng' => 69.2797,
            'captured_at' => now()->toIso8601String(), 'is_mock' => false,
            'photo' => UploadedFile::fake()->image('late.jpg'),
        ]);
        $resp->assertOk();
        $this->assertTrue($resp->json('attendance.check_in_is_late'));
    }

    public function test_replace_invalid_type_returns_422(): void
    {
        $resp = $this->postJson('/api/v1/chef/attendance/replace', [
            'type' => 'lunch',
            'lat' => 41.3112, 'lng' => 69.2797,
            'captured_at' => now()->toIso8601String(), 'is_mock' => false,
            'photo' => UploadedFile::fake()->image('x.jpg'),
        ]);
        $resp->assertStatus(422);
    }
}
