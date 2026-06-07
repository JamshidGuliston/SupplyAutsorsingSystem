<?php

namespace Tests\Unit\Services\ChildrenCount;

use App\Services\ChildrenCount\ChildrenCountWindow;
use Carbon\Carbon;
use Tests\TestCase;

class ChildrenCountWindowTest extends TestCase
{
    private ChildrenCountWindow $window;

    protected function setUp(): void
    {
        parent::setUp();
        $this->window = new ChildrenCountWindow();
    }

    public function test_is_allowed_at_10am(): void
    {
        $now = Carbon::parse('2026-06-01 10:00:00', 'Asia/Tashkent');
        $this->assertTrue($this->window->isAllowedNow($now));
    }

    public function test_is_allowed_at_03_00_boundary(): void
    {
        $now = Carbon::parse('2026-06-01 03:00:00', 'Asia/Tashkent');
        $this->assertTrue($this->window->isAllowedNow($now));
    }

    public function test_is_allowed_at_20_59(): void
    {
        $now = Carbon::parse('2026-06-01 20:59:00', 'Asia/Tashkent');
        $this->assertTrue($this->window->isAllowedNow($now));
    }

    public function test_is_blocked_at_21_00(): void
    {
        $now = Carbon::parse('2026-06-01 21:00:00', 'Asia/Tashkent');
        $this->assertFalse($this->window->isAllowedNow($now));
    }

    public function test_is_blocked_at_22_00(): void
    {
        $now = Carbon::parse('2026-06-01 22:00:00', 'Asia/Tashkent');
        $this->assertFalse($this->window->isAllowedNow($now));
    }

    public function test_is_blocked_at_02_59(): void
    {
        $now = Carbon::parse('2026-06-01 02:59:00', 'Asia/Tashkent');
        $this->assertFalse($this->window->isAllowedNow($now));
    }

    public function test_is_blocked_at_00_00(): void
    {
        $now = Carbon::parse('2026-06-01 00:00:00', 'Asia/Tashkent');
        $this->assertFalse($this->window->isAllowedNow($now));
    }

    public function test_next_opens_at_returns_today_03_when_before_open(): void
    {
        $now = Carbon::parse('2026-06-01 02:00:00', 'Asia/Tashkent');
        $next = $this->window->nextOpensAt($now);
        $this->assertSame('2026-06-01 03:00:00', $next->format('Y-m-d H:i:s'));
        $this->assertSame('Asia/Tashkent', $next->timezoneName);
    }

    public function test_next_opens_at_returns_tomorrow_03_when_after_close(): void
    {
        $now = Carbon::parse('2026-06-01 22:00:00', 'Asia/Tashkent');
        $next = $this->window->nextOpensAt($now);
        $this->assertSame('2026-06-02 03:00:00', $next->format('Y-m-d H:i:s'));
        $this->assertSame('Asia/Tashkent', $next->timezoneName);
    }

    public function test_in_cooldown_when_5_hours_ago(): void
    {
        $now = Carbon::parse('2026-06-01 15:00:00', 'Asia/Tashkent');
        $last = Carbon::parse('2026-06-01 10:00:00', 'Asia/Tashkent');
        $this->assertTrue($this->window->isInCooldown($last, $now));
    }

    public function test_not_in_cooldown_when_13_hours_ago(): void
    {
        $now = Carbon::parse('2026-06-01 15:00:00', 'Asia/Tashkent');
        $last = Carbon::parse('2026-06-01 02:00:00', 'Asia/Tashkent');
        $this->assertFalse($this->window->isInCooldown($last, $now));
    }

    public function test_not_in_cooldown_when_null(): void
    {
        $now = Carbon::parse('2026-06-01 15:00:00', 'Asia/Tashkent');
        $this->assertFalse($this->window->isInCooldown(null, $now));
    }

    public function test_not_in_cooldown_when_exactly_12_hours_ago(): void
    {
        $now = Carbon::parse('2026-06-01 15:00:00', 'Asia/Tashkent');
        $last = Carbon::parse('2026-06-01 03:00:00', 'Asia/Tashkent');
        // 12 hours = cooldown just ended; submission allowed again
        $this->assertFalse($this->window->isInCooldown($last, $now));
    }
}
