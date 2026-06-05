<?php

namespace Tests\Feature;

use App\Constants\Roles;
use App\Models\ChefDevice;
use App\Models\User;
use App\Services\Push\PushService;
use Database\Seeders\AddelkadirRoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

class SendMorningRemindersTest extends TestCase
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

    public function test_command_invokes_push_to_chef_role(): void
    {
        $chef = User::create(['name' => 'C', 'email' => 'c@t.lo', 'password' => bcrypt('x'), 'role_id' => Roles::CHEF]);
        ChefDevice::create(['user_id' => $chef->id, 'platform' => 'android', 'fcm_token' => 'tok', 'app_version' => '1.0']);

        $mock = Mockery::mock(PushService::class);
        $mock->shouldReceive('sendToRole')
            ->once()
            ->with(Roles::CHEF, Mockery::type('string'), Mockery::type('string'), Mockery::on(fn ($d) => ($d['kind'] ?? null) === 'morning_reminder'))
            ->andReturn(['success_count' => 1, 'failure_count' => 0, 'invalid_tokens' => []]);
        $this->app->instance(PushService::class, $mock);

        $this->artisan('chef:morning-reminder')->assertExitCode(0);
    }
}
