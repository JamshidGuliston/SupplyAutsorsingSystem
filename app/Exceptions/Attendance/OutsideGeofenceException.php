<?php

namespace App\Exceptions\Attendance;

class OutsideGeofenceException extends AttendanceException
{
    public function __construct(private int $distanceM, private int $maxRadiusM)
    {
        parent::__construct("Bog'chadan {$distanceM}m uzoqdasiz.");
    }

    public function errorCode(): string
    {
        return 'outside_geofence';
    }

    public function context(): array
    {
        return [
            'distance_m' => $this->distanceM,
            'max_radius_m' => $this->maxRadiusM,
        ];
    }
}
