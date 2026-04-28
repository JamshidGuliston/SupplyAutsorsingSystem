<?php

namespace Tests\Unit\Services\Attendance;

use App\Services\Attendance\DistanceCalculator;
use Tests\TestCase;

class DistanceCalculatorTest extends TestCase
{
    private DistanceCalculator $calc;

    protected function setUp(): void
    {
        parent::setUp();
        $this->calc = new DistanceCalculator();
    }

    public function test_distance_to_self_is_zero(): void
    {
        $this->assertSame(0, $this->calc->meters(41.3111, 69.2797, 41.3111, 69.2797));
    }

    public function test_short_distance_about_100m(): void
    {
        // ~100 m to the north: lat += 0.0009 (1° ≈ 111 km, so 0.0009° ≈ 100m)
        $d = $this->calc->meters(41.3111, 69.2797, 41.3120, 69.2797);
        $this->assertGreaterThanOrEqual(95, $d);
        $this->assertLessThanOrEqual(105, $d);
    }

    public function test_known_distance_tashkent_samarkand(): void
    {
        // Tashkent (41.3111, 69.2797) to Samarkand (39.6542, 66.9750) ~270km
        $d = $this->calc->meters(41.3111, 69.2797, 39.6542, 66.9750);
        $this->assertGreaterThan(260000, $d);
        $this->assertLessThan(280000, $d);
    }
}
