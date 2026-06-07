<?php

namespace App\Exceptions\ChildrenCount;

class IncompleteSubmissionException extends ChildrenCountException
{
    /**
     * @param array<int> $missingAgeIds
     */
    public function __construct(private array $missingAgeIds)
    {
        parent::__construct('Barcha yosh toifalari uchun son kiriting. Yetishmayotgan: ' . implode(', ', $missingAgeIds));
    }

    public function errorCode(): string
    {
        return 'incomplete_submission';
    }

    public function context(): array
    {
        return ['missing_age_ids' => $this->missingAgeIds];
    }
}
