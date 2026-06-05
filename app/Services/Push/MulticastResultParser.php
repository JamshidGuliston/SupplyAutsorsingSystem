<?php

namespace App\Services\Push;

class MulticastResultParser
{
    private const PERMANENT_FAILURE_CODES = [
        'messaging/registration-token-not-registered',
        'messaging/invalid-registration-token',
        'messaging/invalid-argument',
    ];

    /**
     * @param array<int, array{token:string, success:bool, errorCode:?string}> $reports
     * @param array<int, string> $allTokens
     * @return array{success_count:int, failure_count:int, invalid_tokens:array<int, string>}
     */
    public function classify(array $reports, array $allTokens): array
    {
        $success = 0;
        $failure = 0;
        $invalid = [];
        foreach ($reports as $r) {
            if (!empty($r['success'])) {
                $success++;
                continue;
            }
            $failure++;
            if (in_array($r['errorCode'] ?? '', self::PERMANENT_FAILURE_CODES, true)) {
                $invalid[] = $r['token'];
            }
        }
        return [
            'success_count' => $success,
            'failure_count' => $failure,
            'invalid_tokens' => $invalid,
        ];
    }
}
