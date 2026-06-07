<?php

namespace App\Exceptions\ChildrenCount;

use Exception;

abstract class ChildrenCountException extends Exception
{
    abstract public function errorCode(): string;

    public function httpStatus(): int
    {
        return 422;
    }

    /** @return array<string, mixed> */
    public function context(): array
    {
        return [];
    }
}
