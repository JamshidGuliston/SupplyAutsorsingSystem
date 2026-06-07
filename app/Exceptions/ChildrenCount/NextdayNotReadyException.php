<?php

namespace App\Exceptions\ChildrenCount;

class NextdayNotReadyException extends ChildrenCountException
{
    public function __construct()
    {
        parent::__construct('Texnolog kelajak kun uchun menyu sozlamagan. Texnologga murojaat qiling.');
    }

    public function errorCode(): string
    {
        return 'nextday_not_ready';
    }
}
