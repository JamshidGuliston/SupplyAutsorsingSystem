<?php

namespace App\Exceptions\Attendance;

class AlreadyCheckedInException extends AttendanceException
{
    public function __construct()
    {
        parent::__construct("Bugun allaqachon kelgansiz. \"Qayta yuborish\" tugmasidan foydalaning.");
    }

    public function errorCode(): string
    {
        return 'already_checked_in';
    }
}
