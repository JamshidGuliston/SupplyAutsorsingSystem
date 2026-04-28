<?php

namespace App\Exceptions\Attendance;

class StaleCaptureException extends AttendanceException
{
    public function __construct()
    {
        parent::__construct("Yuborilgan vaqt server vaqtidan ko'p farq qiladi. Qayta urinib ko'ring.");
    }

    public function errorCode(): string
    {
        return 'stale_capture';
    }
}
