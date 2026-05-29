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

    public function test_counts_exits_and_beacons(): void
    {
        $summary = new LocationEventSummary();
        $events = [
            ['event_type' => 'enter'],
            ['event_type' => 'beacon'],
            ['event_type' => 'exit'],
            ['event_type' => 'enter'],
            ['event_type' => 'beacon'],
            ['event_type' => 'exit'],
        ];
        $counts = $summary->countByType($events);
        $this->assertSame(2, $counts['exit']);
        $this->assertSame(2, $counts['enter']);
        $this->assertSame(2, $counts['beacon']);
    }

    public function test_counts_empty_events(): void
    {
        $summary = new LocationEventSummary();
        $counts = $summary->countByType([]);
        $this->assertSame(0, $counts['exit']);
        $this->assertSame(0, $counts['enter']);
        $this->assertSame(0, $counts['beacon']);
    }
}
