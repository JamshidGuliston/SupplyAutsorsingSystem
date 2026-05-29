<?php

namespace Tests\Unit\Services\Attendance;

use App\Services\Attendance\LocationEventSummary;
use Tests\TestCase;

class LocationEventSummaryTest extends TestCase
{
    public function test_is_inside_true_when_distance_within_radius(): void
    {
        $summary = new LocationEventSummary();
        $this->assertTrue($summary->isInside(150, 200));
        $this->assertTrue($summary->isInside(200, 200));
    }

    public function test_is_inside_false_when_distance_exceeds_radius(): void
    {
        $summary = new LocationEventSummary();
        $this->assertFalse($summary->isInside(201, 200));
        $this->assertFalse($summary->isInside(500, 200));
    }
}
