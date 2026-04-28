<?php

namespace Tests\Feature\Api\V1\Auth;

use App\Constants\Roles;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class LogoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_logout_revokes_current_token(): void
    {
        $user = User::create([
            'role_id' => Roles::CHEF,
            'name' => 'A', 'email' => 'a@t.l', 'password' => bcrypt('x'),
        ]);
        Sanctum::actingAs($user);

        $resp = $this->postJson('/api/v1/auth/logout');

        $resp->assertOk();
        $resp->assertJson(['message' => 'logged_out']);
    }

    public function test_logout_without_token_returns_401(): void
    {
        $resp = $this->postJson('/api/v1/auth/logout');
        $resp->assertStatus(401);
    }
}
