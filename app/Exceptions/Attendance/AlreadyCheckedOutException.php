<?php

namespace App\Exceptions\Attendance;

class AlreadyCheckedOutException extends AttendanceException
{
    public function __construct()
    {
        parent::__construct("Bugun allaqachon ketgansiz. \"Qayta yuborish\" tugmasidan foydalaning.");
    }

    public function errorCode(): string
    {
        return 'already_checked_out';
    }
}
