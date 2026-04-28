<?php

namespace App\Exceptions\Attendance;

class KindgardenCoordsNotSetException extends AttendanceException
{
    public function __construct()
    {
        parent::__construct("Bog'cha koordinatalari sozlanmagan. Addelkadirga murojaat qiling.");
    }

    public function errorCode(): string
    {
        return 'kindgarden_coords_not_set';
    }
}
