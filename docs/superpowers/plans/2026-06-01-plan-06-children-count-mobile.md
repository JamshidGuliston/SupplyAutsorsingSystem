# Plan 6 — Children Count Mobile Feature Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Oshpaz mobil ilovasi orqali har kuni bog'cha bolalar sonini yosh toifa bo'yicha **bir marta** yuborishi. Yuborilgandan keyin tahrirlash mobilda yo'q — texnologga murojaat.

**Architecture:** Backend tomonida sof `ChildrenCountWindow` helper (DB'siz, mahalliy TDD) vaqt chegarasi va 12 soatlik cooldown logikasini olib boradi. `ChildrenCountService` mavjud `Nextday_namber` + `ChildrenCountHistory` + `Notification` zanjirini qayta ishlatadi. 2 ta REST endpoint (GET today, POST submit). Mobile tomonda yangi `ChildrenCountCard` 3 holatli komponent `HomeScreen`'ga mount qilinadi; react-query bilan state yangilanadi.

**Tech Stack:** Laravel 8.83, PHP 8.1+, Sanctum API auth, React Native 0.74.5 + TypeScript, @tanstack/react-query 5.x, axios. Mavjud Eloquent modellari: `Nextday_namber`, `ChildrenCountHistory`, `Age_range`, `Kindgarden`, `User`, `Notification`.

**Spec:** [docs/superpowers/specs/2026-06-01-plan-06-children-count-mobile-design.md](../specs/2026-06-01-plan-06-children-count-mobile-design.md)

---

## Testing Constraint (eslatma)

Plan 5a/5b'dagi qoidalar bu yerda ham amal qiladi:
- **Unit testlar** (DB'siz, masalan `ChildrenCountWindowTest`) — local'da `& "D:\OSPanel\modules\PHP-8.3\PHP\php.exe" artisan test --filter=...` bilan ishlaydi va ishlashi shart.
- **Feature testlar** (DB kerak) — local'da `php -l` syntax check, serverda MySQL bilan ishlaydi.
- Mobile: hozir komponent uchun snapshot testlar yo'q (mavjud pattern emas). `tsc --noEmit` bilan tip tekshiruvi yetarli; manual smoke test telefonda.

Har PHP fayl yarat/o'zgartirilgandan keyin: `& "D:\OSPanel\modules\PHP-8.3\PHP\php.exe" -l <fayl>`.

---

## File Structure

### Backend (Laravel)

| Fayl | Mas'uliyat | Amal |
|---|---|---|
| `app/Services/ChildrenCount/ChildrenCountWindow.php` | Sof helper: vaqt chegarasi (03 ≤ hour < 21) + 12 soat cooldown | Create |
| `tests/Unit/Services/ChildrenCount/ChildrenCountWindowTest.php` | Helper unit testlari (mahalliy) | Create |
| `app/Exceptions/ChildrenCount/ChildrenCountException.php` | Base abstract: `errorCode()`, `httpStatus()=422`, `context()=[]` | Create |
| `app/Exceptions/ChildrenCount/TimeWindowClosedException.php` | error code: `time_window_closed` | Create |
| `app/Exceptions/ChildrenCount/AlreadySubmittedTodayException.php` | error code: `already_submitted_today` | Create |
| `app/Exceptions/ChildrenCount/NextdayNotReadyException.php` | error code: `nextday_not_ready` | Create |
| `app/Exceptions/ChildrenCount/InvalidAgeForKindgardenException.php` | error code: `invalid_age_for_kindgarden` | Create |
| `app/Exceptions/ChildrenCount/KindgardenNotAssignedException.php` | error code: `kindgarden_not_assigned` | Create |
| `app/Exceptions/Handler.php` | Add renderable callback for `ChildrenCountException` | Modify |
| `app/Services/ChildrenCount/ChildrenCountService.php` | Biznes mantiqi: `getTodayState()` + `submit()` | Create |
| `app/Http/Controllers/Api/V1/Chef/ChildrenCountController.php` | 2 ta endpoint + validation | Create |
| `routes/api.php` | 2 ta yangi route chef middleware ostida | Modify |
| `tests/Feature/Api/V1/Chef/ChildrenCountTest.php` | Endpoint feature testlar (serverda) | Create |

### Mobile (React Native)

| Fayl | Mas'uliyat | Amal |
|---|---|---|
| `mobile/src/api/childrenCount.ts` | API client: `getToday()`, `submit()` + TypeScript types | Create |
| `mobile/src/api/errors.ts` | 5 ta yangi error code uchun mapping | Modify |
| `mobile/src/components/ChildrenCountCard.tsx` | 3 holatni boshqaruvchi karta (input form / submitted view / closed) | Create |
| `mobile/src/screens/HomeScreen.tsx` | Placeholder o'rniga ChildrenCountCard mount qilish | Modify |

---

## Task 1: ChildrenCountWindow helper (TDD, mahalliy)

Sof helper sinfi — 3 ta metod: `isAllowedNow(Carbon)`, `nextOpensAt(Carbon)`, `isInCooldown(?Carbon $lastSubmit, Carbon $now)`. Vaqt: 03 ≤ hour < 21 (Asia/Tashkent). Cooldown: 12 soat.

**Files:**
- Create: `app/Services/ChildrenCount/ChildrenCountWindow.php`
- Test: `tests/Unit/Services/ChildrenCount/ChildrenCountWindowTest.php`

- [ ] **Step 1: Write the failing test**

`tests/Unit/Services/ChildrenCount/ChildrenCountWindowTest.php`:

```php
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

    public function test_allowed_at_10am_tashkent(): void
    {
        $now = Carbon::parse('2026-06-01 10:00:00', 'Asia/Tashkent');
        $this->assertTrue($this->window->isAllowedNow($now));
    }

    public function test_allowed_at_lower_boundary_03_00(): void
    {
        $now = Carbon::parse('2026-06-01 03:00:00', 'Asia/Tashkent');
        $this->assertTrue($this->window->isAllowedNow($now));
    }

    public function test_allowed_at_20_59(): void
    {
        $now = Carbon::parse('2026-06-01 20:59:00', 'Asia/Tashkent');
        $this->assertTrue($this->window->isAllowedNow($now));
    }

    public function test_blocked_at_upper_boundary_21_00(): void
    {
        $now = Carbon::parse('2026-06-01 21:00:00', 'Asia/Tashkent');
        $this->assertFalse($this->window->isAllowedNow($now));
    }

    public function test_blocked_at_22_00(): void
    {
        $now = Carbon::parse('2026-06-01 22:00:00', 'Asia/Tashkent');
        $this->assertFalse($this->window->isAllowedNow($now));
    }

    public function test_blocked_at_02_59(): void
    {
        $now = Carbon::parse('2026-06-01 02:59:00', 'Asia/Tashkent');
        $this->assertFalse($this->window->isAllowedNow($now));
    }

    public function test_blocked_at_00_00(): void
    {
        $now = Carbon::parse('2026-06-01 00:00:00', 'Asia/Tashkent');
        $this->assertFalse($this->window->isAllowedNow($now));
    }

    public function test_next_opens_at_returns_today_03_when_before(): void
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
    }

    public function test_in_cooldown_when_last_submit_was_5_hours_ago(): void
    {
        $now = Carbon::parse('2026-06-01 10:00:00', 'Asia/Tashkent');
        $last = Carbon::parse('2026-06-01 05:00:00', 'Asia/Tashkent');
        $this->assertTrue($this->window->isInCooldown($last, $now));
    }

    public function test_not_in_cooldown_when_last_submit_was_13_hours_ago(): void
    {
        $now = Carbon::parse('2026-06-01 16:00:00', 'Asia/Tashkent');
        $last = Carbon::parse('2026-06-01 03:00:00', 'Asia/Tashkent');
        $this->assertFalse($this->window->isInCooldown($last, $now));
    }

    public function test_not_in_cooldown_when_last_submit_is_null(): void
    {
        $now = Carbon::parse('2026-06-01 10:00:00', 'Asia/Tashkent');
        $this->assertFalse($this->window->isInCooldown(null, $now));
    }

    public function test_in_cooldown_when_exactly_12_hours_ago(): void
    {
        $now = Carbon::parse('2026-06-01 15:00:00', 'Asia/Tashkent');
        $last = Carbon::parse('2026-06-01 03:00:00', 'Asia/Tashkent');
        // 12 hours = cooldown still active (strict < 12 ends it)
        $this->assertTrue($this->window->isInCooldown($last, $now));
    }
}
```

- [ ] **Step 2: Run test to verify it fails**

Run: `& "D:\OSPanel\modules\PHP-8.3\PHP\php.exe" artisan test --filter=ChildrenCountWindowTest`
Expected: FAIL with "Class App\\Services\\ChildrenCount\\ChildrenCountWindow not found"

- [ ] **Step 3: Write the implementation**

`app/Services/ChildrenCount/ChildrenCountWindow.php`:

```php
<?php

namespace App\Services\ChildrenCount;

use Carbon\Carbon;

class ChildrenCountWindow
{
    public const TIMEZONE = 'Asia/Tashkent';
    public const OPEN_HOUR = 3;
    public const CLOSE_HOUR = 21;
    public const COOLDOWN_HOURS = 12;

    /**
     * True when the current Tashkent hour is in [03, 21).
     */
    public function isAllowedNow(Carbon $now): bool
    {
        $hour = (int) $now->copy()->setTimezone(self::TIMEZONE)->format('H');
        return $hour >= self::OPEN_HOUR && $hour < self::CLOSE_HOUR;
    }

    /**
     * Next Tashkent moment when the window opens (today 03:00 if not yet,
     * otherwise tomorrow 03:00).
     */
    public function nextOpensAt(Carbon $now): Carbon
    {
        $tashkent = $now->copy()->setTimezone(self::TIMEZONE);
        $todayOpen = $tashkent->copy()->setTime(self::OPEN_HOUR, 0, 0);
        if ($tashkent->lt($todayOpen)) {
            return $todayOpen;
        }
        return $todayOpen->copy()->addDay();
    }

    /**
     * True when the last submission was less than COOLDOWN_HOURS ago.
     * Null lastSubmit means "never submitted" → not in cooldown.
     */
    public function isInCooldown(?Carbon $lastSubmit, Carbon $now): bool
    {
        if ($lastSubmit === null) {
            return false;
        }
        return $lastSubmit->copy()->addHours(self::COOLDOWN_HOURS)->gt($now);
    }
}
```

- [ ] **Step 4: Run test to verify it passes**

Run: `& "D:\OSPanel\modules\PHP-8.3\PHP\php.exe" artisan test --filter=ChildrenCountWindowTest`
Expected: PASS (13 tests).

- [ ] **Step 5: Syntax check + commit**

```bash
& "D:\OSPanel\modules\PHP-8.3\PHP\php.exe" -l app/Services/ChildrenCount/ChildrenCountWindow.php
git add app/Services/ChildrenCount/ChildrenCountWindow.php tests/Unit/Services/ChildrenCount/ChildrenCountWindowTest.php
git commit -m "feat(plan-6): ChildrenCountWindow pure helper for time/cooldown logic"
```

---

## Task 2: Exception classes

6 ta sinf: 1 ta abstrakt base + 5 ta konkret (har biri o'z error code + xabari bilan). Plan 1'dagi `AttendanceException` pattern.

**Files:**
- Create: `app/Exceptions/ChildrenCount/ChildrenCountException.php`
- Create: `app/Exceptions/ChildrenCount/TimeWindowClosedException.php`
- Create: `app/Exceptions/ChildrenCount/AlreadySubmittedTodayException.php`
- Create: `app/Exceptions/ChildrenCount/NextdayNotReadyException.php`
- Create: `app/Exceptions/ChildrenCount/InvalidAgeForKindgardenException.php`
- Create: `app/Exceptions/ChildrenCount/KindgardenNotAssignedException.php`

- [ ] **Step 1: Create base abstract class**

`app/Exceptions/ChildrenCount/ChildrenCountException.php`:

```php
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
```

- [ ] **Step 2: Create 5 concrete exceptions**

`app/Exceptions/ChildrenCount/TimeWindowClosedException.php`:

```php
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
```

`app/Exceptions/ChildrenCount/AlreadySubmittedTodayException.php`:

```php
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
```

`app/Exceptions/ChildrenCount/NextdayNotReadyException.php`:

```php
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
```

`app/Exceptions/ChildrenCount/InvalidAgeForKindgardenException.php`:

```php
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

    public function context(): array
    {
        return ['age_id' => $this->ageId];
    }
}
```

`app/Exceptions/ChildrenCount/KindgardenNotAssignedException.php`:

```php
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
```

- [ ] **Step 3: Syntax check all 6 files**

```bash
& "D:\OSPanel\modules\PHP-8.3\PHP\php.exe" -l app/Exceptions/ChildrenCount/ChildrenCountException.php
& "D:\OSPanel\modules\PHP-8.3\PHP\php.exe" -l app/Exceptions/ChildrenCount/TimeWindowClosedException.php
& "D:\OSPanel\modules\PHP-8.3\PHP\php.exe" -l app/Exceptions/ChildrenCount/AlreadySubmittedTodayException.php
& "D:\OSPanel\modules\PHP-8.3\PHP\php.exe" -l app/Exceptions/ChildrenCount/NextdayNotReadyException.php
& "D:\OSPanel\modules\PHP-8.3\PHP\php.exe" -l app/Exceptions/ChildrenCount/InvalidAgeForKindgardenException.php
& "D:\OSPanel\modules\PHP-8.3\PHP\php.exe" -l app/Exceptions/ChildrenCount/KindgardenNotAssignedException.php
```

Expected: 6 ta "No syntax errors detected".

- [ ] **Step 4: Commit**

```bash
git add app/Exceptions/ChildrenCount/
git commit -m "feat(plan-6): ChildrenCount exception hierarchy (base + 5 concrete)"
```

---

## Task 3: Handler renderable callback for ChildrenCountException

JSON response API'lar uchun (Plan 1'dagi `AttendanceException` pattern).

**Files:**
- Modify: `app/Exceptions/Handler.php`

- [ ] **Step 1: Read existing Handler.php to find the register() method**

The file already has a `renderable` for `\App\Exceptions\Attendance\AttendanceException` — locate that block. The new callback goes right after it.

- [ ] **Step 2: Add the new renderable**

`app/Exceptions/Handler.php` — `register()` ichida, mavjud `renderable` AttendanceException blokidan keyin qo'shing:

```php
        $this->renderable(function (\App\Exceptions\ChildrenCount\ChildrenCountException $e, $request) {
            if ($request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'error' => $e->errorCode(),
                    'message' => $e->getMessage(),
                ] + $e->context(), $e->httpStatus());
            }
        });
```

- [ ] **Step 3: Syntax check + commit**

```bash
& "D:\OSPanel\modules\PHP-8.3\PHP\php.exe" -l app/Exceptions/Handler.php
git add app/Exceptions/Handler.php
git commit -m "feat(plan-6): JSON render for ChildrenCountException"
```

---

## Task 4: ChildrenCountService

Biznes mantiqi: `getTodayState($user)` va `submit($user, $counts)`. Ikkalasi ham bir xil array format qaytaradi (mobile bir marta query qilsa kifoya).

**Files:**
- Create: `app/Services/ChildrenCount/ChildrenCountService.php`

- [ ] **Step 1: Implement the service**

`app/Services/ChildrenCount/ChildrenCountService.php`:

```php
<?php

namespace App\Services\ChildrenCount;

use App\Exceptions\ChildrenCount\AlreadySubmittedTodayException;
use App\Exceptions\ChildrenCount\InvalidAgeForKindgardenException;
use App\Exceptions\ChildrenCount\KindgardenNotAssignedException;
use App\Exceptions\ChildrenCount\NextdayNotReadyException;
use App\Exceptions\ChildrenCount\TimeWindowClosedException;
use App\Models\ChildrenCountHistory;
use App\Models\Kindgarden;
use App\Models\Nextday_namber;
use App\Models\Notification;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ChildrenCountService
{
    public function __construct(private ChildrenCountWindow $window) {}

    /**
     * Returns the payload mobile needs to render the page.
     *
     * @return array{
     *   kindgarden: array{id:int, kingar_name:string},
     *   age_ranges: array<int, array{id:int, age_name:string}>,
     *   submitted: bool,
     *   today_counts: array<int,int>|null,
     *   submitted_at: string|null,
     *   time_window: array{allowed:bool, from:string, to:string, current_tashkent:string},
     *   nextday_ready: bool
     * }
     */
    public function getTodayState(User $user): array
    {
        $kindgarden = $this->resolveKindgarden($user);
        $kindgarden->load('age_range');
        $ageRanges = $kindgarden->age_range->map(fn ($a) => [
            'id' => (int) $a->id,
            'age_name' => (string) $a->age_name,
        ])->values()->all();

        $now = Carbon::now();
        $lastHistory = $this->lastSubmissionHistory($kindgarden->id);
        $lastAt = $lastHistory ? Carbon::parse($lastHistory->changed_at) : null;
        $submitted = $this->window->isInCooldown($lastAt, $now);

        $todayCounts = null;
        if ($submitted) {
            $todayCounts = Nextday_namber::where('kingar_name_id', $kindgarden->id)
                ->get(['king_age_name_id', 'kingar_children_number'])
                ->mapWithKeys(fn ($r) => [(int) $r->king_age_name_id => (int) $r->kingar_children_number])
                ->all();
        }

        $nextdayReady = Nextday_namber::where('kingar_name_id', $kindgarden->id)->exists();

        $tashkent = $now->copy()->setTimezone(ChildrenCountWindow::TIMEZONE);

        return [
            'kindgarden' => [
                'id' => (int) $kindgarden->id,
                'kingar_name' => (string) $kindgarden->kingar_name,
            ],
            'age_ranges' => $ageRanges,
            'submitted' => $submitted,
            'today_counts' => $todayCounts,
            'submitted_at' => $submitted && $lastAt ? $lastAt->copy()->utc()->toIso8601String() : null,
            'time_window' => [
                'allowed' => $this->window->isAllowedNow($now),
                'from' => sprintf('%02d:00', ChildrenCountWindow::OPEN_HOUR),
                'to' => sprintf('%02d:00', ChildrenCountWindow::CLOSE_HOUR),
                'current_tashkent' => $tashkent->format('H:i'),
            ],
            'nextday_ready' => $nextdayReady,
        ];
    }

    /**
     * Persists counts atomically: validation → history rows → notification → update.
     *
     * @param array<int,int> $counts  age_id => count
     * @return array  same shape as getTodayState() after submission
     */
    public function submit(User $user, array $counts): array
    {
        $kindgarden = $this->resolveKindgarden($user);
        $kindgarden->load('age_range');

        $now = Carbon::now();
        if (!$this->window->isAllowedNow($now)) {
            throw new TimeWindowClosedException();
        }

        $lastHistory = $this->lastSubmissionHistory($kindgarden->id);
        $lastAt = $lastHistory ? Carbon::parse($lastHistory->changed_at) : null;
        if ($this->window->isInCooldown($lastAt, $now)) {
            throw new AlreadySubmittedTodayException();
        }

        $nextdayRows = Nextday_namber::where('kingar_name_id', $kindgarden->id)->get();
        if ($nextdayRows->isEmpty()) {
            throw new NextdayNotReadyException();
        }

        $allowedAgeIds = $kindgarden->age_range->pluck('id')->map(fn ($i) => (int) $i)->all();
        foreach ($counts as $ageId => $value) {
            if (!in_array((int) $ageId, $allowedAgeIds, true)) {
                throw new InvalidAgeForKindgardenException((int) $ageId);
            }
        }

        DB::transaction(function () use ($kindgarden, $counts, $nextdayRows, $user, $now) {
            foreach ($counts as $ageId => $newValue) {
                $current = $nextdayRows->firstWhere('king_age_name_id', (int) $ageId);
                if (!$current) {
                    throw new NextdayNotReadyException();
                }
                $oldValue = (int) $current->kingar_children_number;

                ChildrenCountHistory::create([
                    'kingar_name_id' => $kindgarden->id,
                    'king_age_name_id' => (int) $ageId,
                    'old_children_count' => $oldValue,
                    'new_children_count' => (int) $newValue,
                    'changed_by' => $user->id,
                    'changed_at' => $now,
                    'change_reason' => 'Oshpaz mobil ilova orqali kunlik bolalar soni yuborildi',
                ]);

                Notification::createChildrenCountChangeNotification(
                    $kindgarden->id,
                    (int) $ageId,
                    $oldValue,
                    (int) $newValue,
                    $user->id
                );

                $current->update(['kingar_children_number' => (int) $newValue]);
            }
        });

        return $this->getTodayState($user->fresh());
    }

    private function resolveKindgarden(User $user): Kindgarden
    {
        $kg = $user->kindgarden()->first();
        if (!$kg) {
            throw new KindgardenNotAssignedException();
        }
        return $kg;
    }

    private function lastSubmissionHistory(int $kindgardenId): ?ChildrenCountHistory
    {
        return ChildrenCountHistory::where('kingar_name_id', $kindgardenId)
            ->orderByDesc('changed_at')
            ->first();
    }
}
```

- [ ] **Step 2: Syntax check + commit**

```bash
& "D:\OSPanel\modules\PHP-8.3\PHP\php.exe" -l app/Services/ChildrenCount/ChildrenCountService.php
git add app/Services/ChildrenCount/ChildrenCountService.php
git commit -m "feat(plan-6): ChildrenCountService - getTodayState + atomic submit"
```

---

## Task 5: ChildrenCountController + routes + feature tests

**Files:**
- Create: `app/Http/Controllers/Api/V1/Chef/ChildrenCountController.php`
- Modify: `routes/api.php`
- Create: `tests/Feature/Api/V1/Chef/ChildrenCountTest.php`

- [ ] **Step 1: Implement the controller**

`app/Http/Controllers/Api/V1/Chef/ChildrenCountController.php`:

```php
<?php

namespace App\Http\Controllers\Api\V1\Chef;

use App\Http\Controllers\Controller;
use App\Services\ChildrenCount\ChildrenCountService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ChildrenCountController extends Controller
{
    public function __construct(private ChildrenCountService $svc) {}

    public function today(Request $request): JsonResponse
    {
        return response()->json($this->svc->getTodayState($request->user()));
    }

    public function submit(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'counts' => 'required|array|min:1',
            'counts.*' => 'required|integer|min:0',
        ]);
        $counts = [];
        foreach ($validated['counts'] as $ageId => $value) {
            $counts[(int) $ageId] = (int) $value;
        }
        return response()->json($this->svc->submit($request->user(), $counts));
    }
}
```

- [ ] **Step 2: Add routes to `routes/api.php`**

Find the existing chef route group (the one with `Route::prefix('chef')->middleware(['auth:sanctum'])->group(...)`). Inside it, after the `location-events` route, add:

```php
        Route::get('children-count/today', [\App\Http\Controllers\Api\V1\Chef\ChildrenCountController::class, 'today']);
        Route::post('children-count', [\App\Http\Controllers\Api\V1\Chef\ChildrenCountController::class, 'submit'])
            ->middleware('throttle:30,1');
```

- [ ] **Step 3: Verify routes registered**

Run: `& "D:\OSPanel\modules\PHP-8.3\PHP\php.exe" artisan route:list --path=api/v1/chef/children-count`
Expected: 2 routes — `GET api/v1/chef/children-count/today` and `POST api/v1/chef/children-count`.

- [ ] **Step 4: Write the feature test (runs on server)**

`tests/Feature/Api/V1/Chef/ChildrenCountTest.php`:

```php
<?php

namespace Tests\Feature\Api\V1\Chef;

use App\Constants\Roles;
use App\Models\Age_range;
use App\Models\ChildrenCountHistory;
use App\Models\Kindgarden;
use App\Models\Nextday_namber;
use App\Models\User;
use Carbon\Carbon;
use Database\Seeders\AddelkadirRoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ChildrenCountTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(AddelkadirRoleSeeder::class);
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow(null);
        parent::tearDown();
    }

    private function chefWithKindgarden(): array
    {
        $chef = User::create([
            'name' => 'Test Chef', 'email' => 'c@t.lo',
            'password' => bcrypt('x'), 'role_id' => Roles::CHEF,
        ]);
        $kg = Kindgarden::create([
            'kingar_name' => 'Bog\'cha 1',
            'lat' => 41.31, 'lng' => 69.27, 'geofence_radius' => 200,
        ]);
        \DB::table('user_kindgardens')->insert(['user_id' => $chef->id, 'kindgarden_id' => $kg->id]);

        $age1 = Age_range::create(['age_name' => '3-4 yosh']);
        $age2 = Age_range::create(['age_name' => '5-6 yosh']);
        \DB::table('age_range_kindgarden')->insert([
            ['age_range_id' => $age1->id, 'kindgarden_id' => $kg->id],
            ['age_range_id' => $age2->id, 'kindgarden_id' => $kg->id],
        ]);

        Nextday_namber::create([
            'kingar_name_id' => $kg->id,
            'king_age_name_id' => $age1->id,
            'kingar_children_number' => 10,
            'workers_count' => 5,
        ]);
        Nextday_namber::create([
            'kingar_name_id' => $kg->id,
            'king_age_name_id' => $age2->id,
            'kingar_children_number' => 12,
            'workers_count' => 5,
        ]);

        return [$chef, $kg, $age1, $age2];
    }

    public function test_today_returns_fresh_state(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-06-01 10:00:00', 'Asia/Tashkent'));
        [$chef, $kg, $age1, $age2] = $this->chefWithKindgarden();

        Sanctum::actingAs($chef);
        $r = $this->getJson('/api/v1/chef/children-count/today');

        $r->assertOk();
        $r->assertJsonPath('kindgarden.id', $kg->id);
        $r->assertJsonPath('submitted', false);
        $r->assertJsonPath('time_window.allowed', true);
        $r->assertJsonPath('nextday_ready', true);
        $r->assertJsonCount(2, 'age_ranges');
    }

    public function test_submit_happy_path_writes_history_and_updates_nextday(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-06-01 10:00:00', 'Asia/Tashkent'));
        [$chef, $kg, $age1, $age2] = $this->chefWithKindgarden();

        Sanctum::actingAs($chef);
        $r = $this->postJson('/api/v1/chef/children-count', [
            'counts' => [(string) $age1->id => 30, (string) $age2->id => 25],
        ]);

        $r->assertOk();
        $r->assertJsonPath('submitted', true);
        $r->assertJsonPath('today_counts.' . $age1->id, 30);
        $r->assertJsonPath('today_counts.' . $age2->id, 25);

        $this->assertSame(2, ChildrenCountHistory::count());
        $this->assertSame(30, (int) Nextday_namber::where('king_age_name_id', $age1->id)->value('kingar_children_number'));
        $this->assertSame(25, (int) Nextday_namber::where('king_age_name_id', $age2->id)->value('kingar_children_number'));
    }

    public function test_submit_blocked_outside_window(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-06-01 22:00:00', 'Asia/Tashkent'));
        [$chef, $kg, $age1, $age2] = $this->chefWithKindgarden();

        Sanctum::actingAs($chef);
        $r = $this->postJson('/api/v1/chef/children-count', [
            'counts' => [(string) $age1->id => 30],
        ]);

        $r->assertStatus(422);
        $r->assertJsonPath('error', 'time_window_closed');
    }

    public function test_submit_blocked_during_cooldown(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-06-01 10:00:00', 'Asia/Tashkent'));
        [$chef, $kg, $age1, $age2] = $this->chefWithKindgarden();

        Sanctum::actingAs($chef);
        $this->postJson('/api/v1/chef/children-count', [
            'counts' => [(string) $age1->id => 30],
        ])->assertOk();

        $r = $this->postJson('/api/v1/chef/children-count', [
            'counts' => [(string) $age1->id => 31],
        ]);
        $r->assertStatus(422);
        $r->assertJsonPath('error', 'already_submitted_today');
    }

    public function test_submit_rejects_age_not_in_kindgarden(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-06-01 10:00:00', 'Asia/Tashkent'));
        [$chef, $kg, $age1, $age2] = $this->chefWithKindgarden();
        $foreignAge = Age_range::create(['age_name' => '7-8 yosh']);

        Sanctum::actingAs($chef);
        $r = $this->postJson('/api/v1/chef/children-count', [
            'counts' => [(string) $foreignAge->id => 99],
        ]);

        $r->assertStatus(422);
        $r->assertJsonPath('error', 'invalid_age_for_kindgarden');
    }

    public function test_nextday_not_ready_when_no_rows(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-06-01 10:00:00', 'Asia/Tashkent'));
        $chef = User::create([
            'name' => 'Test Chef', 'email' => 'c@t.lo',
            'password' => bcrypt('x'), 'role_id' => Roles::CHEF,
        ]);
        $kg = Kindgarden::create([
            'kingar_name' => 'Bog\'cha 1',
            'lat' => 41.31, 'lng' => 69.27, 'geofence_radius' => 200,
        ]);
        \DB::table('user_kindgardens')->insert(['user_id' => $chef->id, 'kindgarden_id' => $kg->id]);
        $age1 = Age_range::create(['age_name' => '3-4 yosh']);
        \DB::table('age_range_kindgarden')->insert([
            'age_range_id' => $age1->id, 'kindgarden_id' => $kg->id,
        ]);

        Sanctum::actingAs($chef);
        $r = $this->getJson('/api/v1/chef/children-count/today');
        $r->assertJsonPath('nextday_ready', false);

        $r = $this->postJson('/api/v1/chef/children-count', [
            'counts' => [(string) $age1->id => 30],
        ]);
        $r->assertStatus(422);
        $r->assertJsonPath('error', 'nextday_not_ready');
    }
}
```

NOTE FOR IMPLEMENTER: The pivot tables for `Kindgarden` ↔ `Age_range` use Laravel's default convention `age_range_kindgarden` (alphabetical order). If the server-side migration uses a different name (e.g. `kindgarden_age_range`), the test inserts will fail with "table not found" — adjust the table name in BOTH inserts. Verify with `& "D:\OSPanel\modules\PHP-8.3\PHP\php.exe" artisan tinker --execute="\App\Models\Kindgarden::first()->age_range()->getTable() && var_dump(\App\Models\Kindgarden::first()->age_range()->getTable());"` or by reading the `age_range()` definition's `belongsToMany` call (it may pass an explicit table name).

- [ ] **Step 5: Syntax check + commit**

```bash
& "D:\OSPanel\modules\PHP-8.3\PHP\php.exe" -l app/Http/Controllers/Api/V1/Chef/ChildrenCountController.php
& "D:\OSPanel\modules\PHP-8.3\PHP\php.exe" -l routes/api.php
& "D:\OSPanel\modules\PHP-8.3\PHP\php.exe" -l tests/Feature/Api/V1/Chef/ChildrenCountTest.php
git add app/Http/Controllers/Api/V1/Chef/ChildrenCountController.php routes/api.php tests/Feature/Api/V1/Chef/ChildrenCountTest.php
git commit -m "feat(plan-6): ChildrenCountController + 2 routes + feature tests"
```

---

## Task 6: Mobile — API client + error mapping

**Files:**
- Create: `mobile/src/api/childrenCount.ts`
- Modify: `mobile/src/api/errors.ts`

- [ ] **Step 1: Create the API client**

`mobile/src/api/childrenCount.ts`:

```typescript
import { api } from './client';

export interface AgeRange {
  id: number;
  age_name: string;
}

export interface TimeWindow {
  allowed: boolean;
  from: string;
  to: string;
  current_tashkent: string;
}

export interface ChildrenCountState {
  kindgarden: { id: number; kingar_name: string };
  age_ranges: AgeRange[];
  submitted: boolean;
  today_counts: Record<string, number> | null;
  submitted_at: string | null;
  time_window: TimeWindow;
  nextday_ready: boolean;
}

export async function getTodayState(): Promise<ChildrenCountState> {
  const r = await api.get<ChildrenCountState>('/chef/children-count/today');
  return r.data;
}

export async function submitChildrenCount(counts: Record<number, number>): Promise<ChildrenCountState> {
  const r = await api.post<ChildrenCountState>('/chef/children-count', { counts });
  return r.data;
}
```

- [ ] **Step 2: Add 5 error codes to `mobile/src/api/errors.ts`**

Find the existing `switch (payload.error)` block in `mapServerError()`. Add these `case` arms BEFORE the `default:` line:

```typescript
    case 'time_window_closed':
      return 'Hozir yuborish vaqti emas (03:00 - 21:00 oralig\'ida bo\'lishi kerak).';
    case 'already_submitted_today':
      return 'Bugungi son allaqachon yuborilgan. O\'zgartirish kerak bo\'lsa texnologga murojaat qiling.';
    case 'nextday_not_ready':
      return 'Texnolog kelajak kun uchun menyu sozlamagan. Texnologga murojaat qiling.';
    case 'invalid_age_for_kindgarden':
      return 'Tanlangan yosh toifasi bog\'chaga tegishli emas.';
    case 'kindgarden_not_assigned':
      return 'Sizning hisobingizga bog\'cha biriktirilmagan. Addelkadirga murojaat qiling.';
```

- [ ] **Step 3: Verify TypeScript compiles**

Run (from `d:\OSPanel\home\SupplyAutsorsingSystem\mobile`):
```
npx tsc --noEmit
```
Expected: no errors. (If `npx` is not on PATH from this directory, use `cd mobile` first or invoke node_modules\.bin\tsc directly.)

- [ ] **Step 4: Commit**

```bash
git add mobile/src/api/childrenCount.ts mobile/src/api/errors.ts
git commit -m "feat(plan-6): childrenCount API client + error code mapping"
```

---

## Task 7: Mobile — ChildrenCountCard component

3 holatni boshqaruvchi card. `react-query` bilan state yangilanadi.

**Files:**
- Create: `mobile/src/components/ChildrenCountCard.tsx`

- [ ] **Step 1: Implement the card**

`mobile/src/components/ChildrenCountCard.tsx`:

```typescript
import React, { useState } from 'react';
import { View, Text, TextInput, StyleSheet, ActivityIndicator } from 'react-native';
import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query';
import { PrimaryButton } from './PrimaryButton';
import { ErrorBanner } from './ErrorBanner';
import { colors } from '../theme/colors';
import {
  getTodayState,
  submitChildrenCount,
  ChildrenCountState,
} from '../api/childrenCount';
import { mapServerError } from '../api/errors';

const QUERY_KEY = ['children-count', 'today'] as const;

export function ChildrenCountCard() {
  const qc = useQueryClient();
  const { data, isLoading, error: loadError, refetch } = useQuery<ChildrenCountState>({
    queryKey: QUERY_KEY,
    queryFn: getTodayState,
    staleTime: 30_000,
  });

  const [inputs, setInputs] = useState<Record<number, string>>({});
  const [submitError, setSubmitError] = useState<string | null>(null);

  const mutation = useMutation({
    mutationFn: (counts: Record<number, number>) => submitChildrenCount(counts),
    onSuccess: (newState) => {
      qc.setQueryData(QUERY_KEY, newState);
      setInputs({});
      setSubmitError(null);
    },
    onError: (err: any) => {
      const payload = err?.response?.data;
      setSubmitError(payload ? mapServerError(payload) : err?.message ?? 'Xato');
    },
  });

  if (isLoading) {
    return (
      <View style={styles.card}>
        <ActivityIndicator />
      </View>
    );
  }

  if (loadError || !data) {
    return (
      <View style={styles.card}>
        <Text style={styles.h2}>Bolalar soni</Text>
        <ErrorBanner message={(loadError as any)?.message ?? 'Yuklab bo\'lmadi'} />
        <PrimaryButton label="Qayta urinish" onPress={() => void refetch()} />
      </View>
    );
  }

  const today = new Date().toISOString().slice(0, 10);

  // State C: closed or nextday not ready
  if (!data.time_window.allowed || !data.nextday_ready) {
    const reason = !data.nextday_ready
      ? 'Texnolog kelajak kun uchun menyu sozlamagan. Texnologga murojaat qiling.'
      : `Hozir yuborish vaqti emas (${data.time_window.from} - ${data.time_window.to} oralig'ida bo'lishi kerak).`;
    return (
      <View style={styles.card}>
        <Text style={styles.h2}>Bolalar soni</Text>
        <Text style={styles.subtitle}>{data.kindgarden.kingar_name} — {today}</Text>
        <View style={styles.blockedBox}>
          <Text style={styles.blockedTitle}>⏰  Hozir yuborish mumkin emas</Text>
          <Text style={styles.blockedReason}>{reason}</Text>
          <Text style={styles.muted}>
            Vaqt: {data.time_window.from} - {data.time_window.to} oralig'ida{'\n'}
            Hozir (Tashkent): {data.time_window.current_tashkent}
          </Text>
        </View>
      </View>
    );
  }

  // State B: submitted
  if (data.submitted) {
    const submittedTime = data.submitted_at
      ? new Date(data.submitted_at).toLocaleTimeString('uz-UZ', {
          hour: '2-digit', minute: '2-digit', timeZone: 'Asia/Tashkent',
        })
      : '';
    return (
      <View style={styles.card}>
        <Text style={styles.h2}>Bolalar soni</Text>
        <Text style={styles.subtitle}>{data.kindgarden.kingar_name} — {today}</Text>
        <View style={styles.submittedBox}>
          <Text style={styles.submittedTitle}>✅  Bugungi son yuborildi  ·  {submittedTime}</Text>
          {data.age_ranges.map((a) => (
            <Text key={a.id} style={styles.submittedRow}>
              •  {a.age_name}: {data.today_counts?.[a.id] ?? '—'}
            </Text>
          ))}
        </View>
        <Text style={styles.contactNote}>
          O'zgartirish kerak bo'lsa, texnologga murojaat qiling.
        </Text>
      </View>
    );
  }

  // State A: open input form
  const onSubmit = () => {
    setSubmitError(null);
    const counts: Record<number, number> = {};
    for (const age of data.age_ranges) {
      const raw = inputs[age.id];
      const n = parseInt(raw ?? '', 10);
      if (Number.isNaN(n) || n < 0) {
        setSubmitError(`"${age.age_name}" uchun butun musbat son kiriting.`);
        return;
      }
      counts[age.id] = n;
    }
    mutation.mutate(counts);
  };

  return (
    <View style={styles.card}>
      <Text style={styles.h2}>Bolalar soni</Text>
      <Text style={styles.subtitle}>{data.kindgarden.kingar_name} — {today}</Text>
      <ErrorBanner message={submitError} />
      {data.age_ranges.map((age) => (
        <View key={age.id} style={styles.row}>
          <Text style={styles.label}>{age.age_name}</Text>
          <TextInput
            style={styles.input}
            value={inputs[age.id] ?? ''}
            onChangeText={(t) => setInputs((p) => ({ ...p, [age.id]: t.replace(/[^0-9]/g, '') }))}
            keyboardType="number-pad"
            placeholder="0"
            editable={!mutation.isPending}
          />
        </View>
      ))}
      <PrimaryButton
        label="✅  Yuborish"
        variant="success"
        onPress={onSubmit}
        loading={mutation.isPending}
      />
      <Text style={styles.muted}>
        Vaqt: {data.time_window.from} - {data.time_window.to}
      </Text>
    </View>
  );
}

const styles = StyleSheet.create({
  card: {
    backgroundColor: '#fff',
    borderWidth: 1,
    borderColor: colors.border,
    borderRadius: 10,
    padding: 16,
    marginBottom: 16,
  },
  h2: { fontSize: 18, fontWeight: '700', color: colors.textPrimary },
  subtitle: { fontSize: 12, color: colors.textMuted, marginBottom: 12 },
  row: { marginBottom: 10 },
  label: { fontSize: 14, color: colors.textPrimary, marginBottom: 4 },
  input: {
    borderWidth: 1,
    borderColor: colors.border,
    borderRadius: 6,
    paddingHorizontal: 12,
    paddingVertical: 10,
    fontSize: 16,
    backgroundColor: '#fff',
  },
  muted: { fontSize: 11, color: colors.textMuted, marginTop: 10 },
  submittedBox: {
    backgroundColor: '#E6F4EA',
    borderWidth: 1,
    borderColor: '#34A853',
    borderRadius: 8,
    padding: 12,
    marginBottom: 12,
  },
  submittedTitle: { color: '#1E4620', fontWeight: '700', marginBottom: 6 },
  submittedRow: { color: '#1E4620', fontSize: 14, marginTop: 2 },
  contactNote: {
    fontSize: 13,
    color: colors.textMuted,
    borderTopWidth: 1,
    borderTopColor: colors.border,
    paddingTop: 10,
  },
  blockedBox: {
    backgroundColor: '#FFF7E6',
    borderWidth: 1,
    borderColor: '#F4B400',
    borderRadius: 8,
    padding: 12,
  },
  blockedTitle: { color: '#7A5C00', fontWeight: '700', marginBottom: 6 },
  blockedReason: { color: '#7A5C00', marginBottom: 8 },
});
```

- [ ] **Step 2: TypeScript compile check**

From `d:\OSPanel\home\SupplyAutsorsingSystem\mobile`:
```
npx tsc --noEmit
```
Expected: no errors.

- [ ] **Step 3: Commit**

```bash
git add mobile/src/components/ChildrenCountCard.tsx
git commit -m "feat(plan-6): ChildrenCountCard with 3 states (input/submitted/blocked)"
```

---

## Task 8: Mobile — HomeScreen modification

**Files:**
- Modify: `mobile/src/screens/HomeScreen.tsx`

- [ ] **Step 1: Replace the placeholder with the card**

`mobile/src/screens/HomeScreen.tsx`:

```typescript
import React from 'react';
import { ScrollView, RefreshControl, View } from 'react-native';
import { useQueryClient } from '@tanstack/react-query';
import { ScreenContainer } from '../components/ScreenContainer';
import { ChildrenCountCard } from '../components/ChildrenCountCard';

export function HomeScreen() {
  const qc = useQueryClient();
  const [refreshing, setRefreshing] = React.useState(false);
  const onRefresh = React.useCallback(async () => {
    setRefreshing(true);
    try {
      await qc.invalidateQueries({ queryKey: ['children-count', 'today'] });
    } finally {
      setRefreshing(false);
    }
  }, [qc]);

  return (
    <ScreenContainer>
      <ScrollView refreshControl={<RefreshControl refreshing={refreshing} onRefresh={onRefresh} />}>
        <View style={{ padding: 4 }}>
          <ChildrenCountCard />
        </View>
      </ScrollView>
    </ScreenContainer>
  );
}
```

- [ ] **Step 2: TypeScript compile check + commit**

From `d:\OSPanel\home\SupplyAutsorsingSystem\mobile`:
```
npx tsc --noEmit
```

```bash
git add mobile/src/screens/HomeScreen.tsx
git commit -m "feat(plan-6): mount ChildrenCountCard in HomeScreen with pull-to-refresh"
```

---

## Task 9: Deploy + smoke test (foydalanuvchi bajaradi)

- [ ] **Step 1: Push local commits**

```bash
git push origin master
```

- [ ] **Step 2: Server'da pull + cache clear**

```bash
cd ~/public_html
rm -f .git/gc.log 2>/dev/null
git pull origin master
php artisan route:clear && php artisan view:clear
```

- [ ] **Step 3: Feature testlarni serverda ishlatish**

```bash
php artisan test --filter=ChildrenCountTest
```

Expected: 6 tests PASS. Agar `age_range_kindgarden` pivot jadval nomi farq qilsa, testdagi insertlarni mos ravishda tuzating (yoki shu feedback bilan menga keling — birga tuzatamiz).

- [ ] **Step 4: APK build (local'da)**

```powershell
cd d:\OSPanel\home\SupplyAutsorsingSystem\mobile\android
.\gradlew assembleRelease
```

APK joyi: `mobile\android\app\build\outputs\apk\release\app-release.apk`.

- [ ] **Step 5: Telefonga o'rnatish**

```powershell
adb uninstall uz.kindergarden.chefmobile
adb install app\build\outputs\apk\release\app-release.apk
```

- [ ] **Step 6: Manual smoke test stsenariylari**

1. **A holat:** Oshpaz hisobi bilan kiring (vaqt 10:00 atrofida), Bosh sahifa ochilsin. "Bolalar soni" karta → 2 ta yosh input ko'rinishi, default qiymat bo'sh. Sonlarni kiriting, "Yuborish" bosing.
2. **B holat:** Sahifa o'z-o'zidan B holatga o'tadi: "✅ Bugungi son yuborildi · HH:MM" + sonlar ro'yxati + "O'zgartirish kerak bo'lsa texnologga murojaat qiling".
3. **Web verifikatsiya:** Texnolog yoki addelkadir web profilida → ChildrenCountHistory yangi qatorlar borligi. Texnolog Notification jadvalida xabar.
4. **C holat:** 22:00 dan keyin ilovani qayta oching → vaqt yopiq xabari ko'rinishi.
5. **Pull-to-refresh:** B holatda sahifani pastga torting → so'rov yangilanadi, holat saqlanadi.

Har qadam'da skreyni yoki natijani yuboring.

---

## Self-Review Checklist (plan yozuvchi tomonidan bajarildi)

- ✅ **Spec coverage:** § 3.1 GET endpoint → Task 5 step 1 `today()` metod; § 3.2 POST → Task 5 step 1 `submit()` metod; § 3.3 fayllar → Tasks 1-5 to'liq; § 4 mobile UI 3 holat → Tasks 6-8; § 5 vaqt logikasi → Task 1; § 6 schema "yangi migration kerak emas" → tasdiqlanadi; § 7 testing → Task 1 (unit), Task 5 (feature); § 8 build sequence A→E → Tasks 1-9.
- ✅ **Placeholder scan:** Barcha kod bloklar to'liq, hech qaerda "TBD/TODO" yo'q. Task 5 NOTE'i `age_range_kindgarden` pivot nomi haqidagi ehtiyotkorlik (loyihada Laravel default'idan farqli bo'lishi mumkin) — implementer tomonidan tasdiqlash mavjud risk sifatida hujjatlangan, placeholder emas.
- ✅ **Type consistency:** `ChildrenCountWindow::isAllowedNow/isInCooldown/nextOpensAt` — Task 1'da aniqlangan, Task 4 (Service)'da bir xil chaqiriladi. `ChildrenCountService::getTodayState/submit` — Task 4'da aniqlangan, Task 5 controller'da bir xil ishlatiladi. `ChildrenCountState` interfeysi mobile API → component oqimida bir xil property nomlari (kindgarden, age_ranges, submitted, today_counts, submitted_at, time_window, nextday_ready).
- ⚠️ **Risk:** `age_range_kindgarden` pivot jadval nomi. Laravel default convention alphabetical (`age_range_kindgarden`), lekin loyihaning `Kindgarden::age_range()` metod definitsiyasida ikkinchi parametr aniq ko'rsatilmagan, ya'ni default qabul qilinadi — bu **standart taxminga ko'ra to'g'ri**, lekin DB'da haqiqiy jadval nomini Task 5 implementer tasdiqlashi kerak.

---

## Notes for Implementer

- **PHP path:** Local'da php yo'lda yo'q. `& "D:\OSPanel\modules\PHP-8.3\PHP\php.exe"` ishlatilsin.
- **Plan 5b'da topilgan `kindgarden_id` pivot nomi (user_kindgardens uchun)** to'g'ri ekan (kingar_name_id emas). Plan 6'da ham xuddi shu pivot ishlatiladi (Task 5 feature test).
- **`age_range_kindgarden` (Plan 6'ning yangi pivot'i)** — implementer tomonidan tasdiqlanishi kerak. Agar farq qilsa, Task 5 feature test ham, Service kodi ham `$kindgarden->age_range` relation orqali ishlaydi (Eloquent o'zi pivot jadvalini ishlatadi), shuning uchun production kod ishlaydi. Faqat feature test ichidagi `\DB::table('age_range_kindgarden')->insert(...)` qatorlari pivot nomi qaytaroq bo'lishi mumkin.
- **Carbon::setTestNow** — feature testda majburiy, aks holda real soat bilan vaqt logikasi sinov bo'lmaydi.
- **Notification::createChildrenCountChangeNotification** — bu mavjud static method (`ChefController::sendnumbers`'da ishlatilgan). Yangi metod yaratmaymiz.
- **DB transaction** — Task 4'da history + Nextday_namber update bitta transactionda. Bu race condition'larni va qisman yozish'ni oldini oladi.
- **Mobile `keyboardType="number-pad"`** + `replace(/[^0-9]/g, '')` — vergul/nuqta yozish imkoniyatini blokirlaydi (faqat integer).
- **`color`/`PrimaryButton`/`ErrorBanner`/`ScreenContainer`** — mavjud Plan 2 komponentlari, yangi yaratmaymiz.
- **react-query `staleTime: 30_000`** — 30 sekund ichida sahifa qayta foydalanuvchi navigatsiyalansa, cache'dan o'qiydi. Bu yetarli; oshpaz bir kunda 1 marta yuboradi.
