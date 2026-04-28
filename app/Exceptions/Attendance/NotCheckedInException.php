<?php

namespace App\Exceptions\Attendance;

class NotCheckedInException extends AttendanceException
{
    public function __construct()
    {
        parent::__construct("Avval \"Keldim\" tugmasini bosing.");
    }

    public function errorCode(): string
    {
        return 'not_checked_in';
    }
}
