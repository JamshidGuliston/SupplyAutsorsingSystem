<?php

namespace App\Exceptions\ChildrenCount;

class AlreadySubmittedTodayException extends ChildrenCountException
{
    public function __construct()
    {
        parent::__construct('Bugungi son allaqachon yuborilgan. O\'zgartirish kerak bo\'lsa texnologga murojaat qiling.');
    }

    public function errorCode(): string
    {
        return 'already_submitted_today';
    }
}
