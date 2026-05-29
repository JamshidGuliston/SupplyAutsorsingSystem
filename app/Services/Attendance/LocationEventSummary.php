<?php

namespace App\Services\Attendance;

class LocationEventSummary
{
    public function isInside(int $distanceM, int $radiusM): bool
    {
        return $distanceM <= $radiusM;
    }
}
