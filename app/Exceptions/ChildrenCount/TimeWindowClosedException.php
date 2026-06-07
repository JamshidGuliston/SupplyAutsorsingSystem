<?php

namespace App\Exceptions\ChildrenCount;

class TimeWindowClosedException extends ChildrenCountException
{
    public function __construct()
    {
        parent::__construct('Hozir yuborish vaqti emas (03:00 - 21:00 oralig\'ida bo\'lishi kerak)');
    }

    public function errorCode(): string
    {
        return 'time_window_closed';
    }
}
