<?php

namespace Tests\Feature;

use App\Constants\Roles;
use App\Models\Kindgarden;
use App\Models\User;
use App\Services\Push\PushService;
use Carbon\Carbon;
use Database\Seeders\AddelkadirRoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Laravel\Sanctum\Sanctum;
use Mockery;
use Tests\TestCase;

class LocationEventExitPushTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(AddelkadirRoleSeeder::class);
        Cache::flush();
    }

    protected function tearDown(): void
    {
        Mockery::close();
        Carbon::setTestNow(null);
        parent::tearDown();
    }

    public function test_exit_during_work_hours_triggers_push(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-06-05 12:00:00', 'Asia/Tashkent'));

        $chef = User::create(['name' => 'C', 'email' => 'c@t.lo', 'password' => bcrypt('x'), 'role_id' => Roles::CHEF]);
        $kg = Kindgarden::create(['kingar_name' => 'KG', 'lat' => 41.31, 'lng' => 69.27, 'geofence_radius' => 200]);
        \DB::table('user_kindgardens')->insert(['user_id' => $chef->id, 'kindgarden_id' => $kg->id]);

        $mock = Mockery::mock(PushService::class);
        $mock->shouldReceive('sendToRole')
            ->once()
            ->withArgs(fn ($role, $t, $b, $d) => $role === Roles::ADDELKADIR && ($d['kind'] ?? null) === 'exit')
            ->andReturn(['success_count' => 0, 'failure_count' => 0, 'invalid_tokens' => []]);
        $this->app->instance(PushService::class, $mock);

        Sanctum::actingAs($chef);
        $this->postJson('/api/v1/chef/location-events', [
            'events' => [
                ['event_type' => 'exit', 'lat' => 41.32, 'lng' => 69.28, 'happened_at' => '2026-06-05T07:00:00Z', 'is_mock' => false],
            ],
        ])->assertOk();
    }

    public function test_exit_outside_work_hours_does_not_push(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-06-05 20:00:00', 'Asia/Tashkent'));

        $chef = User::create(['name' => 'C', 'email' => 'c@t.lo', 'password' => bcrypt('x'), 'role_id' => Roles::CHEF]);
        $kg = Kindgarden::create(['kingar_name' => 'KG', 'lat' => 41.31, 'lng' => 69.27, 'geofence_radius' => 200]);
        \DB::table('user_kindgardens')->insert(['user_id' => $chef->id, 'kindgarden_id' => $kg->id]);

        $mock = Mockery::mock(PushService::class);
        $mock->shouldNotReceive('sendToRole');
        $this->app->instance(PushService::class, $mock);

        Sanctum::actingAs($chef);
        $this->postJson('/api/v1/chef/location-events', [
            'events' => [
                ['event_type' => 'exit', 'lat' => 41.32, 'lng' => 69.28, 'happened_at' => '2026-06-05T15:00:00Z', 'is_mock' => false],
            ],
        ])->assertOk();
    }

    public function test_second_exit_within_5_minutes_is_suppressed_by_cache_cooldown(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-06-05 12:00:00', 'Asia/Tashkent'));

        $chef = User::create(['name' => 'C', 'email' => 'c@t.lo', 'password' => bcrypt('x'), 'role_id' => Roles::CHEF]);
        $kg = Kindgarden::create(['kingar_name' => 'KG', 'lat' => 41.31, 'lng' => 69.27, 'geofence_radius' => 200]);
        \DB::table('user_kindgardens')->insert(['user_id' => $chef->id, 'kindgarden_id' => $kg->id]);

        $mock = Mockery::mock(PushService::class);
        $mock->shouldReceive('sendToRole')->once()->andReturn(['success_count' => 0, 'failure_count' => 0, 'invalid_tokens' => []]);
        $this->app->instance(PushService::class, $mock);

        Sanctum::actingAs($chef);
        $payload = ['events' => [
            ['event_type' => 'exit', 'lat' => 41.32, 'lng' => 69.28, 'happened_at' => '2026-06-05T07:00:00Z', 'is_mock' => false],
        ]];
        $this->postJson('/api/v1/chef/location-events', $payload)->assertOk();
        $this->postJson('/api/v1/chef/location-events', $payload)->assertOk();
    }
}
