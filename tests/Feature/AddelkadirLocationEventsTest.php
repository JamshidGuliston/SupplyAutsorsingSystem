<?php

namespace Tests\Feature;

use App\Constants\Roles;
use App\Models\ChefLocationEvent;
use App\Models\Kindgarden;
use App\Models\User;
use Database\Seeders\AddelkadirRoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AddelkadirLocationEventsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(AddelkadirRoleSeeder::class);
    }

    public function test_addelkadir_can_view_location_events_for_a_date(): void
    {
        $addelkadir = User::create([
            'role_id' => Roles::ADDELKADIR, 'name' => 'A',
            'email' => 'a@t.l', 'password' => bcrypt('x'),
        ]);
        $chef = User::create([
            'role_id' => Roles::CHEF, 'name' => 'Test Chef',
            'email' => 'chef@t.l', 'password' => bcrypt('x'),
        ]);
        $kg = Kindgarden::create([
            'kingar_name' => 'Test KG', 'lat' => 41.3111, 'lng' => 69.2797,
            'geofence_radius' => 200,
        ]);

        ChefLocationEvent::create([
            'user_id' => $chef->id, 'kindgarden_id' => $kg->id,
            'event_type' => 'exit', 'happened_at' => '2026-05-22 13:00:00',
            'lat' => 41.3200, 'lng' => 69.2797, 'distance_m' => 250, 'is_mock' => false,
        ]);
        ChefLocationEvent::create([
            'user_id' => $chef->id, 'kindgarden_id' => $kg->id,
            'event_type' => 'enter', 'happened_at' => '2026-05-22 13:20:00',
            'lat' => 41.3112, 'lng' => 69.2797, 'distance_m' => 30, 'is_mock' => false,
        ]);

        $resp = $this->actingAs($addelkadir)
            ->get('/addelkadir/location-events?date=2026-05-22');

        $resp->assertOk();
        $resp->assertSee('Test Chef');
        $resp->assertSee('Chiqish');
        $resp->assertSee('Tashqarida');
        // 13:00 exit -> 13:20 enter = 20 minutes outside.
        // View renders "<strong>20</strong> daq", so assert the parts separately.
        $resp->assertSee('20');
        $resp->assertSee('daq');
    }

    public function test_non_addelkadir_chef_is_blocked(): void
    {
        $chef = User::create([
            'role_id' => Roles::CHEF, 'name' => 'C',
            'email' => 'c@t.l', 'password' => bcrypt('x'),
        ]);

        // isAddelkadirMiddleware redirects unauthorized users to the login route.
        $resp = $this->actingAs($chef)->get('/addelkadir/location-events');
        $resp->assertRedirect(route('login'));
    }

    public function test_chef_filter_narrows_results(): void
    {
        $addelkadir = User::create([
            'role_id' => Roles::ADDELKADIR, 'name' => 'A',
            'email' => 'a@t.l', 'password' => bcrypt('x'),
        ]);
        // Distinct kindergarten names per chef so we can assert on table rows.
        // (Chef names always appear in the page's chef dropdown, so we key the
        // "does not see Beta" assertion on Beta's kindergarten name, which only
        // renders inside the filtered table rows.)
        $kgAlpha = Kindgarden::create([
            'kingar_name' => 'Alpha Kindergarten', 'lat' => 41.3111,
            'lng' => 69.2797, 'geofence_radius' => 200,
        ]);
        $kgBeta = Kindgarden::create([
            'kingar_name' => 'Beta Kindergarten', 'lat' => 41.3111,
            'lng' => 69.2797, 'geofence_radius' => 200,
        ]);
        $alpha = User::create([
            'role_id' => Roles::CHEF, 'name' => 'Chef Alpha',
            'email' => 'alpha@t.l', 'password' => bcrypt('x'),
        ]);
        $beta = User::create([
            'role_id' => Roles::CHEF, 'name' => 'Chef Beta',
            'email' => 'beta@t.l', 'password' => bcrypt('x'),
        ]);

        ChefLocationEvent::create([
            'user_id' => $alpha->id, 'kindgarden_id' => $kgAlpha->id,
            'event_type' => 'beacon', 'happened_at' => '2026-05-22 13:00:00',
            'lat' => 41.3112, 'lng' => 69.2797, 'distance_m' => 30, 'is_mock' => false,
        ]);
        ChefLocationEvent::create([
            'user_id' => $beta->id, 'kindgarden_id' => $kgBeta->id,
            'event_type' => 'beacon', 'happened_at' => '2026-05-22 13:05:00',
            'lat' => 41.3112, 'lng' => 69.2797, 'distance_m' => 30, 'is_mock' => false,
        ]);

        $resp = $this->actingAs($addelkadir)
            ->get('/addelkadir/location-events?date=2026-05-22&chef_id=' . $alpha->id);

        $resp->assertOk();
        $resp->assertSee('Chef Alpha');
        // Alpha's row is present, Beta's row (and its kindergarten) is filtered out.
        $resp->assertSee('Alpha Kindergarten');
        $resp->assertDontSee('Beta Kindergarten');
    }
}
