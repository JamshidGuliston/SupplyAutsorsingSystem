<?php

namespace App\Services\ChildrenCount;

use Carbon\Carbon;

/**
 * Pure helper for children-count submission time window and cooldown logic.
 *
 * Allowed window: hour in [OPEN_HOUR, CLOSE_HOUR) in Asia/Tashkent.
 * Cooldown: last submission must be at least COOLDOWN_HOURS ago.
 */
class ChildrenCountWindow
{
    public const TIMEZONE = 'Asia/Tashkent';
    public const OPEN_HOUR = 3;
    public const CLOSE_HOUR = 21;
    public const COOLDOWN_HOURS = 12;

    /**
     * True when current hour is within the allowed submission window.
     */
    public function isAllowedNow(Carbon $now): bool
    {
        $hour = (int) $now->copy()->setTimezone(self::TIMEZONE)->format('H');

        return $hour >= self::OPEN_HOUR && $hour < self::CLOSE_HOUR;
    }

    /**
     * Returns the next moment the window opens, in Asia/Tashkent.
     * - If $now is before today's OPEN_HOUR, returns today's OPEN_HOUR:00.
     * - Otherwise (during or after the window) returns tomorrow's OPEN_HOUR:00.
     */
    public function nextOpensAt(Carbon $now): Carbon
    {
        $local = $now->copy()->setTimezone(self::TIMEZONE);
        $todayOpen = $local->copy()->setTime(self::OPEN_HOUR, 0, 0);

        if ($local->lt($todayOpen)) {
            return $todayOpen;
        }

        return $todayOpen->copy()->addDay();
    }

    /**
     * True when last submission was less than COOLDOWN_HOURS ago.
     * Null lastSubmit (never submitted) returns false.
     */
    public function isInCooldown(?Carbon $lastSubmit, Carbon $now): bool
    {
        if ($lastSubmit === null) {
            return false;
        }

        return $lastSubmit->copy()->addHours(self::COOLDOWN_HOURS)->gt($now);
    }
}
