<?php

namespace Tests\Feature;

use App\Models\Kindgarden;
use App\Models\order_product;
use App\Services\Push\PushService;
use Database\Seeders\AddelkadirRoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

class OrderProductObserverTest extends TestCase
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

    public function test_creating_order_product_triggers_push_to_kindergarten_chefs(): void
    {
        $kg = Kindgarden::create(['kingar_name' => 'KG', 'lat' => 41.31, 'lng' => 69.27, 'geofence_radius' => 200]);

        $mock = Mockery::mock(PushService::class);
        $mock->shouldReceive('sendToChefsOfKindgarden')
            ->once()
            ->withArgs(function ($kid, $title, $body, $data) use ($kg) {
                return $kid === $kg->id
                    && str_contains($title, 'Yangi buyurtma')
                    && ($data['kind'] ?? null) === 'new_order';
            })
            ->andReturn(['success_count' => 0, 'failure_count' => 0, 'invalid_tokens' => []]);
        $this->app->instance(PushService::class, $mock);

        order_product::create([
            'kingar_name_id' => $kg->id,
            'order_title' => 'Test buyurtma',
            'day_id' => 1,
        ]);
    }

    public function test_creating_order_without_kingar_name_id_skips_push(): void
    {
        $mock = Mockery::mock(PushService::class);
        $mock->shouldNotReceive('sendToChefsOfKindgarden');
        $this->app->instance(PushService::class, $mock);

        order_product::create(['order_title' => 'Orphan', 'day_id' => 1]);
    }
}
