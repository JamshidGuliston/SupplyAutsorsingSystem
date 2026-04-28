<?php

namespace Tests\Feature\Api\V1\Auth;

use App\Constants\Roles;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_with_valid_credentials_returns_token_and_user(): void
    {
        User::create([
            'role_id' => Roles::CHEF,
            'name' => 'Akmal',
            'email' => 'a@test.local',
            'password' => bcrypt('secret123'),
        ]);

        $resp = $this->postJson('/api/v1/auth/login', [
            'email' => 'a@test.local',
            'password' => 'secret123',
        ]);

        $resp->assertOk();
        $resp->assertJsonStructure(['token', 'user' => ['id', 'name', 'email', 'role_id']]);
        $this->assertNotEmpty($resp->json('token'));
    }

    public function test_login_with_wrong_password_returns_401(): void
    {
        User::create([
            'role_id' => Roles::CHEF,
            'name' => 'A', 'email' => 'a@test.local',
            'password' => bcrypt('secret123'),
        ]);

        $resp = $this->postJson('/api/v1/auth/login', [
            'email' => 'a@test.local',
            'password' => 'wrong',
        ]);

        $resp->assertStatus(401);
        $resp->assertJson(['error' => 'invalid_credentials']);
    }

    public function test_login_with_non_chef_role_is_rejected(): void
    {
        User::create([
            'role_id' => Roles::TECHNOLOG,
            'name' => 'T', 'email' => 't@test.local',
            'password' => bcrypt('secret123'),
        ]);

        $resp = $this->postJson('/api/v1/auth/login', [
            'email' => 't@test.local',
            'password' => 'secret123',
        ]);

        $resp->assertStatus(403);
        $resp->assertJson(['error' => 'role_not_allowed']);
    }

    public function test_login_validates_required_fields(): void
    {
        $resp = $this->postJson('/api/v1/auth/login', []);
        $resp->assertStatus(422);
    }
}
