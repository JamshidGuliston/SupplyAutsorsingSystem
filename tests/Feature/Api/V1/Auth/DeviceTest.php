<?php

namespace Tests\Feature\Api\V1\Auth;

use App\Constants\Roles;
use App\Models\ChefDevice;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class DeviceTest extends TestCase
{
    use RefreshDatabase;

    private function chef(): User
    {
        return User::create([
            'role_id' => Roles::CHEF,
            'name' => 'A', 'email' => 'a@t.l', 'password' => bcrypt('x'),
        ]);
    }

    public function test_register_device_creates_record(): void
    {
        Sanctum::actingAs($this->chef());

        $resp = $this->postJson('/api/v1/auth/device', [
            'platform' => 'android',
            'fcm_token' => 'fcm-abc-123',
            'device_model' => 'Samsung A52',
            'app_version' => '1.0.0',
        ]);

        $resp->assertOk();
        $this->assertDatabaseHas('chef_devices', ['fcm_token' => 'fcm-abc-123']);
    }

    public function test_register_new_device_revokes_old_tokens_and_replaces_device_row(): void
    {
        $user = $this->chef();
        Sanctum::actingAs($user);

        $this->postJson('/api/v1/auth/device', [
            'platform' => 'android', 'fcm_token' => 'old-fcm',
        ])->assertOk();

        $oldToken = $user->createToken('mobile')->plainTextToken;

        Sanctum::actingAs($user);
        $this->postJson('/api/v1/auth/device', [
            'platform' => 'android', 'fcm_token' => 'new-fcm',
        ])->assertOk();

        $this->assertDatabaseMissing('chef_devices', ['fcm_token' => 'old-fcm']);
        $this->assertDatabaseHas('chef_devices', ['fcm_token' => 'new-fcm']);
    }

    public function test_register_validates_platform(): void
    {
        Sanctum::actingAs($this->chef());
        $resp = $this->postJson('/api/v1/auth/device', [
            'platform' => 'windows', 'fcm_token' => 'x',
        ]);
        $resp->assertStatus(422);
    }
}
