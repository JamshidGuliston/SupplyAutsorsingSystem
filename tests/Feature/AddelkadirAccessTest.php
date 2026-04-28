<?php

namespace Tests\Feature;

use App\Constants\Roles;
use App\Models\User;
use Database\Seeders\AddelkadirRoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AddelkadirAccessTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(AddelkadirRoleSeeder::class);
    }

    public function test_chef_cannot_access_addelkadir_home(): void
    {
        $u = User::create([
            'role_id' => Roles::CHEF, 'name' => 'C',
            'email' => 'c@t.l', 'password' => bcrypt('x'),
        ]);
        $resp = $this->actingAs($u)->get('/addelkadir/home');
        $resp->assertRedirect(route('login'));
    }

    public function test_addelkadir_user_can_access_home(): void
    {
        $u = User::create([
            'role_id' => Roles::ADDELKADIR, 'name' => 'A',
            'email' => 'a@t.l', 'password' => bcrypt('x'),
        ]);
        $resp = $this->actingAs($u)->get('/addelkadir/home');
        $resp->assertOk();
    }
}
