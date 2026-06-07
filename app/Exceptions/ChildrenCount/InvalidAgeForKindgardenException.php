<?php

namespace App\Exceptions\ChildrenCount;

class InvalidAgeForKindgardenException extends ChildrenCountException
{
    public function __construct(private int $ageId)
    {
        parent::__construct('Yosh toifasi (id=' . $ageId . ') bog\'chaga tegishli emas');
    }

    public function errorCode(): string
    {
        return 'invalid_age_for_kindgarden';
    }

    /** @return array<string, mixed> */
    public function context(): array
    {
        return ['age_id' => $this->ageId];
    }
}
