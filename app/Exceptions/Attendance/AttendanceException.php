<?php

namespace App\Exceptions\Attendance;

use RuntimeException;

abstract class AttendanceException extends RuntimeException
{
    /** Stable error code returned to API clients (e.g., outside_geofence). */
    abstract public function errorCode(): string;

    /** Extra context returned in JSON body (e.g., distance_m, max_radius_m). */
    public function context(): array
    {
        return [];
    }

    public function httpStatus(): int
    {
        return 422;
    }
}
