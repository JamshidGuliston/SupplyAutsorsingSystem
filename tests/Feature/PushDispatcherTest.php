<?php

namespace Tests\Feature;

use App\Constants\Roles;
use App\Models\ChefDevice;
use App\Models\User;
use App\Services\Push\MulticastResultParser;
use App\Services\Push\PushService;
use Database\Seeders\AddelkadirRoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Kreait\Firebase\Contract\Messaging;
use Kreait\Firebase\Messaging\MulticastSendReport;
use Mockery;
use Tests\TestCase;

class PushDispatcherTest extends TestCase
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

    public function test_send_to_user_fetches_user_devices_and_sends(): void
    {
        $user = User::create(['name' => 'Chef X', 'email' => 'cx@test.lo', 'password' => bcrypt('x'), 'role_id' => Roles::CHEF]);
        ChefDevice::create(['user_id' => $user->id, 'platform' => 'android', 'fcm_token' => 'tok-A', 'app_version' => '1.0']);
        ChefDevice::create(['user_id' => $user->id, 'platform' => 'android', 'fcm_token' => 'tok-B', 'app_version' => '1.0']);

        $messaging = Mockery::mock(Messaging::class);
        $messaging->shouldReceive('sendMulticast')
            ->once()
            ->withArgs(fn ($msg, $tokens) => count($tokens) === 2 && in_array('tok-A', $tokens, true) && in_array('tok-B', $tokens, true))
            ->andReturn(MulticastSendReport::withItems([]));

        $service = new PushService($messaging, new MulticastResultParser());
        $service->sendToUser($user->id, 'Hi', 'Body');
    }

    public function test_send_to_role_only_includes_devices_of_that_role(): void
    {
        $chef = User::create(['name' => 'C', 'email' => 'c@t.lo', 'password' => bcrypt('x'), 'role_id' => Roles::CHEF]);
        $boss = User::create(['name' => 'B', 'email' => 'b@t.lo', 'password' => bcrypt('x'), 'role_id' => Roles::BOSS]);
        ChefDevice::create(['user_id' => $chef->id, 'platform' => 'android', 'fcm_token' => 'chef-tok', 'app_version' => '1.0']);
        ChefDevice::create(['user_id' => $boss->id, 'platform' => 'android', 'fcm_token' => 'boss-tok', 'app_version' => '1.0']);

        $messaging = Mockery::mock(Messaging::class);
        $messaging->shouldReceive('sendMulticast')
            ->once()
            ->withArgs(fn ($msg, $tokens) => $tokens === ['chef-tok'])
            ->andReturn(MulticastSendReport::withItems([]));

        $service = new PushService($messaging, new MulticastResultParser());
        $service->sendToRole(Roles::CHEF, 'Hi', 'Body');
    }

    public function test_no_devices_short_circuits_without_calling_messaging(): void
    {
        $user = User::create(['name' => 'No Device', 'email' => 'nd@t.lo', 'password' => bcrypt('x'), 'role_id' => Roles::CHEF]);

        $messaging = Mockery::mock(Messaging::class);
        $messaging->shouldNotReceive('sendMulticast');

        $service = new PushService($messaging, new MulticastResultParser());
        $result = $service->sendToUser($user->id, 'Hi', 'Body');

        $this->assertSame(0, $result['success_count']);
    }
}
