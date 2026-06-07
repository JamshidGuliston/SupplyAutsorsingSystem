<?php

namespace Tests\Feature\Api\V1\Chef;

use App\Constants\Roles;
use App\Models\Age_range;
use App\Models\ChildrenCountHistory;
use App\Models\Kindgarden;
use App\Models\Nextday_namber;
use App\Models\User;
use Carbon\Carbon;
use Database\Seeders\AddelkadirRoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ChildrenCountTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(AddelkadirRoleSeeder::class);
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow(null);
        parent::tearDown();
    }

    private function chefWithKindgarden(): array
    {
        $chef = User::create([
            'name' => 'Test Chef', 'email' => 'c@t.lo',
            'password' => bcrypt('x'), 'role_id' => Roles::CHEF,
        ]);
        $kg = Kindgarden::create([
            'kingar_name' => 'Bog\'cha 1',
            'lat' => 41.31, 'lng' => 69.27, 'geofence_radius' => 200,
        ]);
        \DB::table('user_kindgardens')->insert(['user_id' => $chef->id, 'kindgarden_id' => $kg->id]);

        $age1 = Age_range::create(['age_name' => '3-4 yosh']);
        $age2 = Age_range::create(['age_name' => '5-6 yosh']);
        \DB::table('age_range_kindgarden')->insert([
            ['age_range_id' => $age1->id, 'kindgarden_id' => $kg->id],
            ['age_range_id' => $age2->id, 'kindgarden_id' => $kg->id],
        ]);

        Nextday_namber::create([
            'kingar_name_id' => $kg->id,
            'king_age_name_id' => $age1->id,
            'kingar_children_number' => 10,
            'workers_count' => 5,
        ]);
        Nextday_namber::create([
            'kingar_name_id' => $kg->id,
            'king_age_name_id' => $age2->id,
            'kingar_children_number' => 12,
            'workers_count' => 5,
        ]);

        return [$chef, $kg, $age1, $age2];
    }

    public function test_today_returns_fresh_state(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-06-01 10:00:00', 'Asia/Tashkent'));
        [$chef, $kg, $age1, $age2] = $this->chefWithKindgarden();

        Sanctum::actingAs($chef);
        $r = $this->getJson('/api/v1/chef/children-count/today');

        $r->assertOk();
        $r->assertJsonPath('kindgarden.id', $kg->id);
        $r->assertJsonPath('submitted', false);
        $r->assertJsonPath('time_window.allowed', true);
        $r->assertJsonPath('nextday_ready', true);
        $r->assertJsonCount(2, 'age_ranges');
    }

    public function test_submit_happy_path_writes_history_and_updates_nextday(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-06-01 10:00:00', 'Asia/Tashkent'));
        [$chef, $kg, $age1, $age2] = $this->chefWithKindgarden();

        Sanctum::actingAs($chef);
        $r = $this->postJson('/api/v1/chef/children-count', [
            'counts' => [(string) $age1->id => 30, (string) $age2->id => 25],
        ]);

        $r->assertOk();
        $r->assertJsonPath('submitted', true);
        $r->assertJsonPath('today_counts.' . $age1->id, 30);
        $r->assertJsonPath('today_counts.' . $age2->id, 25);

        $this->assertSame(2, ChildrenCountHistory::count());
        $this->assertSame(30, (int) Nextday_namber::where('king_age_name_id', $age1->id)->value('kingar_children_number'));
        $this->assertSame(25, (int) Nextday_namber::where('king_age_name_id', $age2->id)->value('kingar_children_number'));
    }

    public function test_submit_blocked_outside_window(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-06-01 22:00:00', 'Asia/Tashkent'));
        [$chef, $kg, $age1, $age2] = $this->chefWithKindgarden();

        Sanctum::actingAs($chef);
        $r = $this->postJson('/api/v1/chef/children-count', [
            'counts' => [(string) $age1->id => 30],
        ]);

        $r->assertStatus(422);
        $r->assertJsonPath('error', 'time_window_closed');
    }

    public function test_submit_blocked_during_cooldown(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-06-01 10:00:00', 'Asia/Tashkent'));
        [$chef, $kg, $age1, $age2] = $this->chefWithKindgarden();

        Sanctum::actingAs($chef);
        $this->postJson('/api/v1/chef/children-count', [
            'counts' => [(string) $age1->id => 30],
        ])->assertOk();

        $r = $this->postJson('/api/v1/chef/children-count', [
            'counts' => [(string) $age1->id => 31],
        ]);
        $r->assertStatus(422);
        $r->assertJsonPath('error', 'already_submitted_today');
    }

    public function test_submit_rejects_age_not_in_kindgarden(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-06-01 10:00:00', 'Asia/Tashkent'));
        [$chef, $kg, $age1, $age2] = $this->chefWithKindgarden();
        $foreignAge = Age_range::create(['age_name' => '7-8 yosh']);

        Sanctum::actingAs($chef);
        $r = $this->postJson('/api/v1/chef/children-count', [
            'counts' => [(string) $foreignAge->id => 99],
        ]);

        $r->assertStatus(422);
        $r->assertJsonPath('error', 'invalid_age_for_kindgarden');
    }

    public function test_nextday_not_ready_when_no_rows(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-06-01 10:00:00', 'Asia/Tashkent'));
        $chef = User::create([
            'name' => 'Test Chef', 'email' => 'c@t.lo',
            'password' => bcrypt('x'), 'role_id' => Roles::CHEF,
        ]);
        $kg = Kindgarden::create([
            'kingar_name' => 'Bog\'cha 1',
            'lat' => 41.31, 'lng' => 69.27, 'geofence_radius' => 200,
        ]);
        \DB::table('user_kindgardens')->insert(['user_id' => $chef->id, 'kindgarden_id' => $kg->id]);
        $age1 = Age_range::create(['age_name' => '3-4 yosh']);
        \DB::table('age_range_kindgarden')->insert([
            'age_range_id' => $age1->id, 'kindgarden_id' => $kg->id,
        ]);

        Sanctum::actingAs($chef);
        $r = $this->getJson('/api/v1/chef/children-count/today');
        $r->assertJsonPath('nextday_ready', false);

        $r = $this->postJson('/api/v1/chef/children-count', [
            'counts' => [(string) $age1->id => 30],
        ]);
        $r->assertStatus(422);
        $r->assertJsonPath('error', 'nextday_not_ready');
    }
}
