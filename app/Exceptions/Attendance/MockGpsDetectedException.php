<?php

namespace App\Exceptions\Attendance;

class MockGpsDetectedException extends AttendanceException
{
    public function __construct()
    {
        parent::__construct("Soxta GPS aniqlandi. GPS Joystick va shu kabi ilovalarni o'chiring.");
    }

    public function errorCode(): string
    {
        return 'mock_gps_detected';
    }
}
