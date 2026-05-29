<?php

namespace App\Services\Attendance;

use Carbon\Carbon;

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

    /**
     * Sum of minutes spent outside, pairing each `exit` with the next `enter`.
     * Events must be ordered by happened_at ascending. Unclosed exits add 0.
     * If two consecutive `exit` events appear before an `enter`, the later exit wins (the outside period restarts from it).
     *
     * @param iterable<array{event_type:string, happened_at:string}|\App\Models\ChefLocationEvent> $events
     */
    public function totalMinutesOutside(iterable $events): int
    {
        $minutes = 0;
        $openExitAt = null;
        foreach ($events as $e) {
            $type = is_array($e) ? ($e['event_type'] ?? null) : $e->event_type;
            $at = is_array($e) ? ($e['happened_at'] ?? null) : $e->happened_at;
            if ($type === 'exit' && $at !== null) {
                $openExitAt = Carbon::parse($at);
            } elseif ($type === 'enter' && $at !== null && $openExitAt !== null) {
                $minutes += $openExitAt->diffInMinutes(Carbon::parse($at));
                $openExitAt = null;
            }
        }
        return $minutes;
    }
}
