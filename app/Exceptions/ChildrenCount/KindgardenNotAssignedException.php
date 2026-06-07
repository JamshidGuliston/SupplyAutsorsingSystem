<?php

namespace App\Exceptions\ChildrenCount;

class KindgardenNotAssignedException extends ChildrenCountException
{
    public function __construct()
    {
        parent::__construct('Sizning hisobingizga bog\'cha biriktirilmagan');
    }

    public function errorCode(): string
    {
        return 'kindgarden_not_assigned';
    }
}
