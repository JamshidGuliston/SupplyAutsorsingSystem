<?php

namespace App\Services\Attendance;

class LocationEventSummary
{
    public function isInside(int $distanceM, int $radiusM): bool
    {
        return $distanceM <= $radiusM;
    }

    /**
     * @param iterable<array{event_type:string}|\App\Models\ChefLocationEvent> $events
     * @return array{exit:int, enter:int, beacon:int}
     */
    public function countByType(iterable $events): array
    {
        $counts = ['exit' => 0, 'enter' => 0, 'beacon' => 0];
        foreach ($events as $e) {
            $type = is_array($e) ? ($e['event_type'] ?? null) : $e->event_type;
            if (isset($counts[$type])) {
                $counts[$type]++;
            }
        }
        return $counts;
    }
}
