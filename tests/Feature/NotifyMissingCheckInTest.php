<?php

namespace Tests\Feature;

use App\Constants\Roles;
use App\Models\ChefAttendance;
use App\Models\User;
use App\Services\Push\PushService;
use Database\Seeders\AddelkadirRoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

class NotifyMissingCheckInTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(AddelkadirRoleSeeder::class);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_notifies_addelkadirs_when_chefs_missing(): void
    {
        $checkedIn = User::create(['name' => 'In Chef', 'email' => 'ic@t.lo', 'password' => bcrypt('x'), 'role_id' => Roles::CHEF]);
        $missing1 = User::create(['name' => 'Out Chef A', 'email' => 'oa@t.lo', 'password' => bcrypt('x'), 'role_id' => Roles::CHEF]);
        $missing2 = User::create(['name' => 'Out Chef B', 'email' => 'ob@t.lo', 'password' => bcrypt('x'), 'role_id' => Roles::CHEF]);

        $today = now()->setTimezone('Asia/Tashkent')->toDateString();
        ChefAttendance::create([
            'user_id' => $checkedIn->id,
            'kindgarden_id' => 1,
            'date' => $today,
            'check_in_at' => now(),
            'check_in_lat' => 41.31, 'check_in_lng' => 69.27,
            'check_in_distance_m' => 10, 'check_in_is_late' => false, 'check_in_replaced_count' => 0,
            'check_out_replaced_count' => 0, 'check_out_undo_count' => 0,
        ]);

        $mock = Mockery::mock(PushService::class);
        $mock->shouldReceive('sendToRole')
            ->once()
            ->withArgs(function ($role, $title, $body, $data) {
                return $role === Roles::ADDELKADIR
                    && str_contains($body, 'Out Chef A')
                    && str_contains($body, 'Out Chef B')
                    && !str_contains($body, 'In Chef')
                    && ($data['kind'] ?? null) === 'missing_checkin';
            })
            ->andReturn(['success_count' => 1, 'failure_count' => 0, 'invalid_tokens' => []]);
        $this->app->instance(PushService::class, $mock);

        $this->artisan('chef:notify-missing-checkin')->assertExitCode(0);
    }

    public function test_no_op_when_all_chefs_checked_in(): void
    {
        $chef = User::create(['name' => 'A', 'email' => 'a@t.lo', 'password' => bcrypt('x'), 'role_id' => Roles::CHEF]);
        $today = now()->setTimezone('Asia/Tashkent')->toDateString();
        ChefAttendance::create([
            'user_id' => $chef->id, 'kindgarden_id' => 1, 'date' => $today,
            'check_in_at' => now(), 'check_in_lat' => 41.31, 'check_in_lng' => 69.27,
            'check_in_distance_m' => 10, 'check_in_is_late' => false, 'check_in_replaced_count' => 0,
            'check_out_replaced_count' => 0, 'check_out_undo_count' => 0,
        ]);

        $mock = Mockery::mock(PushService::class);
        $mock->shouldNotReceive('sendToRole');
        $this->app->instance(PushService::class, $mock);

        $this->artisan('chef:notify-missing-checkin')->assertExitCode(0);
    }
}
