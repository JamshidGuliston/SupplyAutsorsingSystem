<?php

namespace App\Exceptions\Attendance;

class NotCheckedOutException extends AttendanceException
{
    public function __construct()
    {
        parent::__construct("Bugun \"Ketdim\" yozuvi yo'q — bekor qilishga narsa yo'q.");
    }

    public function errorCode(): string
    {
        return 'not_checked_out';
    }
}
