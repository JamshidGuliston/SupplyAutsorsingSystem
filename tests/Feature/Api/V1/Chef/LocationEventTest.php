<?php

namespace Tests\Feature\Api\V1\Chef;

use App\Constants\Roles;
use App\Models\ChefLocationEvent;
use App\Models\Kindgarden;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class LocationEventTest extends TestCase
{
    use RefreshDatabase;

    private User $chef;

    protected function setUp(): void
    {
        parent::setUp();
        Carbon::setTestNow('2026-04-28 08:00:00', 'Asia/Tashkent');
        $kg = Kindgarden::create(['kingar_name' => 'T',
            'lat' => 41.3111, 'lng' => 69.2797, 'geofence_radius' => 200]);
        $this->chef = User::create(['role_id' => Roles::CHEF,
            'name' => 'A', 'email' => 'a@t.l', 'password' => bcrypt('x')]);
        $this->chef->kindgarden()->attach($kg->id);
        Sanctum::actingAs($this->chef);
    }

    public function test_batch_inserts_events(): void
    {
        $resp = $this->postJson('/api/v1/chef/location-events', [
            'events' => [
                ['event_type' => 'exit', 'lat' => 41.3200, 'lng' => 69.2797,
                 'happened_at' => now()->toIso8601String(), 'is_mock' => false],
                ['event_type' => 'enter', 'lat' => 41.3112, 'lng' => 69.2797,
                 'happened_at' => now()->addMinutes(3)->toIso8601String(), 'is_mock' => false],
            ],
        ]);

        $resp->assertOk();
        $resp->assertJson(['inserted' => 2]);
        $this->assertSame(2, ChefLocationEvent::count());
    }

    public function test_invalid_event_type_returns_422(): void
    {
        $resp = $this->postJson('/api/v1/chef/location-events', [
            'events' => [
                ['event_type' => 'lunch', 'lat' => 41.31, 'lng' => 69.27,
                 'happened_at' => now()->toIso8601String(), 'is_mock' => false],
            ],
        ]);
        $resp->assertStatus(422);
    }

    public function test_empty_events_array_returns_zero(): void
    {
        $resp = $this->postJson('/api/v1/chef/location-events', ['events' => []]);
        $resp->assertOk();
        $resp->assertJson(['inserted' => 0]);
    }
}
