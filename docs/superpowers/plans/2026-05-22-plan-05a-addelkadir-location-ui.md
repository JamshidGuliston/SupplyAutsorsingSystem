# Plan 5a — Addelkadir Location History UI Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Addelkadir admin panelida oshpazlarning fon lokatsiya hodisalarini (kirish/chiqish/heartbeat) sana va oshpaz bo'yicha filtrlash mumkin bo'lgan timeline jadval ko'rinishida ko'rsatish.

**Architecture:** Sof PHP `LocationEventSummary` klassi hodisalar to'plamidan statistika (chiqish soni, tashqarida o'tkazilgan vaqt) hisoblaydi va har bir hodisa uchun "ichida/tashqarida" flagini aniqlaydi — DB'siz unit-test qilinadi. `AddelkadirController::locationEvents` nozik qatlam: filtrlaydi, eager-load qiladi, view'ga uzatadi. View Bootstrap 5 jadval.

**Tech Stack:** Laravel 8.65, PHP 8.0/8.3, Blade, Bootstrap 5, PHPUnit. Mavjud `ChefLocationEvent` modeli (Plan 1) qayta ishlatiladi — yangi migration yo'q.

**Spec:** [docs/superpowers/specs/2026-05-22-plan-05-background-location-push-design.md](../specs/2026-05-22-plan-05-background-location-push-design.md) § 5, Bosqich A.

---

## Testing Constraint (MUHIM)

Bu loyihada local SQLite mavjud migration'lar bilan ishlamaydi (Plan 1'dagi multi-dropColumn muammosi). Shuning uchun:

- **Unit testlar** (DB'siz, masalan `LocationEventSummaryTest`) — local'da `php artisan test --filter=...` bilan ishlaydi va ishlashi SHART.
- **Feature testlar** (DB kerak, masalan `AddelkadirLocationEventsTest`) — local'da o'tkazib yuboriladi, **serverda MySQL bilan** yoki **brauzerda qo'lda** tekshiriladi. Plan'da yoziladi (xulq-atvorni hujjatlashtiradi), lekin local gate `php -l` syntax check.

Har bir PHP fayl yarat/o'zgartirilgandan keyin: `php -l <fayl>` (syntax xato yo'qligini tasdiqlaydi).

---

## File Structure

| Fayl | Mas'uliyat | Amal |
|---|---|---|
| `app/Services/Attendance/LocationEventSummary.php` | Hodisalar to'plamidan statistika + per-event inside flag (sof, DB'siz) | Create |
| `tests/Unit/Services/Attendance/LocationEventSummaryTest.php` | LocationEventSummary unit testlari | Create |
| `app/Http/Controllers/AddelkadirController.php` | `locationEvents()` metodi qo'shiladi | Modify |
| `routes/web.php` | `addelkadir.location-events` route | Modify |
| `resources/views/addelkadir/location_events.blade.php` | Timeline jadval + filtr formasi + statistika kartochkasi | Create |
| `resources/views/addelkadir/_layout.blade.php` | Nav'ga "Lokatsiya" link | Modify |
| `tests/Feature/AddelkadirLocationEventsTest.php` | Controller feature testi (serverda ishlaydi) | Create |

---

## Task 1: LocationEventSummary — inside/outside flag

**Files:**
- Create: `app/Services/Attendance/LocationEventSummary.php`
- Test: `tests/Unit/Services/Attendance/LocationEventSummaryTest.php`

- [ ] **Step 1: Write the failing test**

`tests/Unit/Services/Attendance/LocationEventSummaryTest.php`:

```php
<?php

namespace Tests\Unit\Services\Attendance;

use App\Services\Attendance\LocationEventSummary;
use Tests\TestCase;

class LocationEventSummaryTest extends TestCase
{
    public function test_is_inside_true_when_distance_within_radius(): void
    {
        $summary = new LocationEventSummary();
        $this->assertTrue($summary->isInside(150, 200));
        $this->assertTrue($summary->isInside(200, 200));
    }

    public function test_is_inside_false_when_distance_exceeds_radius(): void
    {
        $summary = new LocationEventSummary();
        $this->assertFalse($summary->isInside(201, 200));
        $this->assertFalse($summary->isInside(500, 200));
    }
}
```

- [ ] **Step 2: Run test to verify it fails**

Run: `php artisan test --filter=LocationEventSummaryTest`
Expected: FAIL with "Class LocationEventSummary not found"

- [ ] **Step 3: Write minimal implementation**

`app/Services/Attendance/LocationEventSummary.php`:

```php
<?php

namespace App\Services\Attendance;

class LocationEventSummary
{
    public function isInside(int $distanceM, int $radiusM): bool
    {
        return $distanceM <= $radiusM;
    }
}
```

- [ ] **Step 4: Run test to verify it passes**

Run: `php artisan test --filter=LocationEventSummaryTest`
Expected: PASS (2 tests)

- [ ] **Step 5: Syntax check + commit**

```bash
php -l app/Services/Attendance/LocationEventSummary.php
git add app/Services/Attendance/LocationEventSummary.php tests/Unit/Services/Attendance/LocationEventSummaryTest.php
git commit -m "feat(plan-5a): LocationEventSummary.isInside helper"
```

---

## Task 2: LocationEventSummary — count exits and beacons

**Files:**
- Modify: `app/Services/Attendance/LocationEventSummary.php`
- Test: `tests/Unit/Services/Attendance/LocationEventSummaryTest.php`

- [ ] **Step 1: Write the failing test**

`LocationEventSummaryTest.php` ichiga qo'shing:

```php
    public function test_counts_exits_and_beacons(): void
    {
        $summary = new LocationEventSummary();
        $events = [
            ['event_type' => 'enter'],
            ['event_type' => 'beacon'],
            ['event_type' => 'exit'],
            ['event_type' => 'enter'],
            ['event_type' => 'beacon'],
            ['event_type' => 'exit'],
        ];
        $counts = $summary->countByType($events);
        $this->assertSame(2, $counts['exit']);
        $this->assertSame(2, $counts['enter']);
        $this->assertSame(2, $counts['beacon']);
    }

    public function test_counts_empty_events(): void
    {
        $summary = new LocationEventSummary();
        $counts = $summary->countByType([]);
        $this->assertSame(0, $counts['exit']);
        $this->assertSame(0, $counts['enter']);
        $this->assertSame(0, $counts['beacon']);
    }
```

- [ ] **Step 2: Run test to verify it fails**

Run: `php artisan test --filter=LocationEventSummaryTest`
Expected: FAIL with "Call to undefined method ...countByType()"

- [ ] **Step 3: Write minimal implementation**

`LocationEventSummary.php` ga metod qo'shing:

```php
    /**
     * @param iterable<array{event_type:string}|\App\Models\ChefLocationEvent> $events
     * @return array{exit:int, enter:int, beacon:int}
     */
    public function countByType(iterable $events): array
    {
        $counts = ['exit' => 0, 'enter' => 0, 'beacon' => 0];
        foreach ($events as $e) {
            $type = is_array($e) ? ($e['event_type'] ?? null) : $e->event_type;
            if (isset($counts[$type])) {
                $counts[$type]++;
            }
        }
        return $counts;
    }
```

- [ ] **Step 4: Run test to verify it passes**

Run: `php artisan test --filter=LocationEventSummaryTest`
Expected: PASS (4 tests)

- [ ] **Step 5: Syntax check + commit**

```bash
php -l app/Services/Attendance/LocationEventSummary.php
git add app/Services/Attendance/LocationEventSummary.php tests/Unit/Services/Attendance/LocationEventSummaryTest.php
git commit -m "feat(plan-5a): LocationEventSummary.countByType"
```

---

## Task 3: LocationEventSummary — total minutes outside

**Files:**
- Modify: `app/Services/Attendance/LocationEventSummary.php`
- Test: `tests/Unit/Services/Attendance/LocationEventSummaryTest.php`

Logika: vaqt bo'yicha tartiblangan hodisalarni bosib o'tamiz. `exit` hodisasi "tashqarida" davrini boshlaydi, keyingi `enter` uni yopadi. Davr summasi daqiqalarda. Yopilmagan oxirgi `exit` (kun oxirigacha qaytmagan) e'tiborga olinmaydi (0 daqiqa qo'shiladi — chunki qachon qaytganini bilmaymiz).

- [ ] **Step 1: Write the failing test**

`LocationEventSummaryTest.php` ichiga qo'shing:

```php
    public function test_total_minutes_outside_pairs_exit_enter(): void
    {
        $summary = new LocationEventSummary();
        // exit 13:00 -> enter 13:15 = 15 min; exit 14:00 -> enter 14:30 = 30 min
        $events = [
            ['event_type' => 'exit', 'happened_at' => '2026-05-22 13:00:00'],
            ['event_type' => 'enter', 'happened_at' => '2026-05-22 13:15:00'],
            ['event_type' => 'beacon', 'happened_at' => '2026-05-22 13:45:00'],
            ['event_type' => 'exit', 'happened_at' => '2026-05-22 14:00:00'],
            ['event_type' => 'enter', 'happened_at' => '2026-05-22 14:30:00'],
        ];
        $this->assertSame(45, $summary->totalMinutesOutside($events));
    }

    public function test_unclosed_exit_is_ignored(): void
    {
        $summary = new LocationEventSummary();
        // exit with no following enter -> 0 (we don't know when they returned)
        $events = [
            ['event_type' => 'exit', 'happened_at' => '2026-05-22 13:00:00'],
        ];
        $this->assertSame(0, $summary->totalMinutesOutside($events));
    }

    public function test_no_exits_is_zero(): void
    {
        $summary = new LocationEventSummary();
        $events = [
            ['event_type' => 'beacon', 'happened_at' => '2026-05-22 10:00:00'],
            ['event_type' => 'beacon', 'happened_at' => '2026-05-22 11:00:00'],
        ];
        $this->assertSame(0, $summary->totalMinutesOutside($events));
    }
```

- [ ] **Step 2: Run test to verify it fails**

Run: `php artisan test --filter=LocationEventSummaryTest`
Expected: FAIL with "Call to undefined method ...totalMinutesOutside()"

- [ ] **Step 3: Write minimal implementation**

`LocationEventSummary.php` ga metod qo'shing (yuqorida `use Carbon\Carbon;` faylga import qiling):

```php
    /**
     * Sum of minutes spent outside, pairing each `exit` with the next `enter`.
     * Events must be ordered by happened_at ascending. Unclosed exits add 0.
     *
     * @param iterable<array{event_type:string, happened_at:string}|\App\Models\ChefLocationEvent> $events
     */
    public function totalMinutesOutside(iterable $events): int
    {
        $minutes = 0;
        $openExitAt = null;
        foreach ($events as $e) {
            $type = is_array($e) ? ($e['event_type'] ?? null) : $e->event_type;
            $at = is_array($e) ? ($e['happened_at'] ?? null) : $e->happened_at;
            if ($type === 'exit') {
                $openExitAt = \Carbon\Carbon::parse($at);
            } elseif ($type === 'enter' && $openExitAt !== null) {
                $minutes += $openExitAt->diffInMinutes(\Carbon\Carbon::parse($at));
                $openExitAt = null;
            }
        }
        return $minutes;
    }
```

- [ ] **Step 4: Run test to verify it passes**

Run: `php artisan test --filter=LocationEventSummaryTest`
Expected: PASS (7 tests)

- [ ] **Step 5: Syntax check + commit**

```bash
php -l app/Services/Attendance/LocationEventSummary.php
git add app/Services/Attendance/LocationEventSummary.php tests/Unit/Services/Attendance/LocationEventSummaryTest.php
git commit -m "feat(plan-5a): LocationEventSummary.totalMinutesOutside"
```

---

## Task 4: Route + controller method

**Files:**
- Modify: `routes/web.php:522` (addelkadir group ichida, `chefs` route'dan keyin)
- Modify: `app/Http/Controllers/AddelkadirController.php`

- [ ] **Step 1: Add the route**

`routes/web.php` da addelkadir group ichida (`chefs` route'dan keyin, qatorda 522 atrofida), yopuvchi `});` dan oldin qo'shing:

```php
    Route::get('location-events', [AddelkadirController::class, 'locationEvents'])->name('addelkadir.location-events');
```

- [ ] **Step 2: Add the controller method**

`AddelkadirController.php` da `use` qatorlariga qo'shing (mavjud import bloki ichiga):

```php
use App\Models\ChefLocationEvent;
use App\Services\Attendance\LocationEventSummary;
```

`chefs()` metodidan keyin (class yopilishidan oldin) yangi metod qo'shing:

```php
    public function locationEvents(Request $request, LocationEventSummary $summary): View
    {
        $date = $request->input('date', now()->setTimezone('Asia/Tashkent')->toDateString());
        $chefId = $request->input('chef_id');
        $eventType = $request->input('event_type');

        $query = ChefLocationEvent::with('user', 'kindgarden')
            ->whereDate('happened_at', $date)
            ->orderBy('happened_at');

        if ($chefId) {
            $query->where('user_id', $chefId);
        }
        if (in_array($eventType, ['exit', 'enter', 'beacon'], true)) {
            $query->where('event_type', $eventType);
        }

        $events = $query->paginate(100)->withQueryString();

        // Statistika faqat joriy sahifa uchun emas — barcha shu kun hodisalari uchun.
        $allForStats = ChefLocationEvent::whereDate('happened_at', $date)
            ->when($chefId, fn ($q) => $q->where('user_id', $chefId))
            ->orderBy('happened_at')
            ->get(['event_type', 'happened_at']);

        $counts = $summary->countByType($allForStats);
        $minutesOutside = $summary->totalMinutesOutside($allForStats);

        $chefs = User::where('role_id', Roles::CHEF)->orderBy('name')->get(['id', 'name']);

        return view('addelkadir.location_events', [
            'events' => $events,
            'summary' => $summary,
            'counts' => $counts,
            'minutesOutside' => $minutesOutside,
            'chefs' => $chefs,
            'date' => $date,
            'chefId' => $chefId,
            'eventType' => $eventType,
        ]);
    }
```

- [ ] **Step 3: Syntax check**

Run: `php -l app/Http/Controllers/AddelkadirController.php`
Expected: "No syntax errors detected"

- [ ] **Step 4: Verify route registered**

Run: `php artisan route:list --name=addelkadir.location-events`
Expected: route ko'rinadi (GET addelkadir/location-events)

- [ ] **Step 5: Commit**

```bash
git add routes/web.php app/Http/Controllers/AddelkadirController.php
git commit -m "feat(plan-5a): location-events route + controller"
```

---

## Task 5: Blade view — filter form + summary card + timeline table

**Files:**
- Create: `resources/views/addelkadir/location_events.blade.php`

- [ ] **Step 1: Create the view**

`resources/views/addelkadir/location_events.blade.php`:

```blade
@extends('addelkadir._layout')
@section('title', 'Lokatsiya tarixi')
@section('content')
<h1 class="mb-4">Lokatsiya tarixi</h1>

<form method="GET" class="row g-2 mb-3">
    <div class="col-auto">
        <input type="date" name="date" value="{{ $date }}" class="form-control">
    </div>
    <div class="col-auto">
        <select name="chef_id" class="form-select">
            <option value="">Barcha oshpazlar</option>
            @foreach ($chefs as $c)
                <option value="{{ $c->id }}" @selected($chefId == $c->id)>{{ $c->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-auto">
        <select name="event_type" class="form-select">
            <option value="">Barcha hodisalar</option>
            <option value="exit" @selected($eventType === 'exit')>Chiqish</option>
            <option value="enter" @selected($eventType === 'enter')>Kirish</option>
            <option value="beacon" @selected($eventType === 'beacon')>Heartbeat</option>
        </select>
    </div>
    <div class="col-auto"><button class="btn btn-primary">Filtr</button></div>
</form>

<div class="card mb-3">
    <div class="card-body">
        Bugun: <strong>{{ $counts['exit'] }}</strong> chiqish hodisasi ·
        Jami tashqarida: <strong>{{ $minutesOutside }}</strong> daq ·
        <strong>{{ $counts['beacon'] }}</strong> ta heartbeat ·
        <strong>{{ $counts['enter'] }}</strong> ta kirish
    </div>
</div>

<table class="table table-striped">
    <thead><tr>
        <th>Vaqt</th><th>Oshpaz</th><th>Bog'cha</th><th>Hodisa</th>
        <th>Masofa</th><th>Holat</th><th>Xaritada</th>
    </tr></thead>
    <tbody>
    @forelse ($events as $e)
        @php
            $radius = optional($e->kindgarden)->geofence_radius ?: 200;
            $inside = $summary->isInside((int) $e->distance_m, (int) $radius);
        @endphp
        <tr>
            <td>{{ $e->happened_at->copy()->setTimezone('Asia/Tashkent')->format('H:i') }}</td>
            <td>{{ optional($e->user)->name }}</td>
            <td>{{ optional($e->kindgarden)->kingar_name }}</td>
            <td>
                @if($e->event_type === 'enter')<span class="badge bg-success">🟢 Kirish</span>
                @elseif($e->event_type === 'exit')<span class="badge bg-danger">🔴 Chiqish</span>
                @else<span class="badge bg-secondary">🔄 Heartbeat</span>@endif
                @if($e->is_mock)<span class="badge bg-warning text-dark">soxta GPS</span>@endif
            </td>
            <td>{{ $e->distance_m }}m</td>
            <td>
                @if($inside)<span class="text-success">Ichida</span>
                @else<span class="text-danger fw-bold">Tashqarida</span>@endif
            </td>
            <td>
                <a target="_blank" href="https://yandex.uz/maps/?ll={{ $e->lng }},{{ $e->lat }}&z=18&pt={{ $e->lng }},{{ $e->lat }},pm2rdm">📍</a>
            </td>
        </tr>
    @empty
        <tr><td colspan="7" class="text-center text-muted">Bu kun uchun lokatsiya hodisalari yo'q.</td></tr>
    @endforelse
    </tbody>
</table>

{{ $events->links() }}
@endsection
```

- [ ] **Step 2: Manual render note**

Bu Blade fayl serverda yoki local'da `php artisan serve` orqali brauzerda tekshiriladi (DB kerak). Local'da DB yo'q bo'lsa, faqat syntax to'g'riligiga ishonamiz; serverda Task 7 manual test'da ko'riladi.

- [ ] **Step 3: Commit**

```bash
git add resources/views/addelkadir/location_events.blade.php
git commit -m "feat(plan-5a): location events timeline view"
```

---

## Task 6: Navigation link

**Files:**
- Modify: `resources/views/addelkadir/_layout.blade.php`

- [ ] **Step 1: Add nav link**

`_layout.blade.php` da `chefs` link'idan keyin, `Chiqish` link'idan oldin qo'shing:

```blade
            <a class="text-white me-3" href="{{ route('addelkadir.location-events') }}">Lokatsiya</a>
```

To'liq kontekst (mavjud `Oshpazlar` link'idan keyin):

```blade
            <a class="text-white me-3" href="{{ url('addelkadir/chefs') }}">Oshpazlar</a>
            <a class="text-white me-3" href="{{ route('addelkadir.location-events') }}">Lokatsiya</a>
            <a class="text-white" href="{{ route('logout') }}"
```

- [ ] **Step 2: Commit**

```bash
git add resources/views/addelkadir/_layout.blade.php
git commit -m "feat(plan-5a): nav link to location events"
```

---

## Task 7: Feature test (server-verified)

**Files:**
- Create: `tests/Feature/AddelkadirLocationEventsTest.php`

Bu test DB talab qiladi — local'da o'tkazib yuboriladi, **serverda** `php artisan test --filter=AddelkadirLocationEventsTest` bilan ishlatiladi.

- [ ] **Step 1: Write the feature test**

`tests/Feature/AddelkadirLocationEventsTest.php`:

```php
<?php

namespace Tests\Feature;

use App\Constants\Roles;
use App\Models\ChefLocationEvent;
use App\Models\Kindgarden;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AddelkadirLocationEventsTest extends TestCase
{
    use RefreshDatabase;

    public function test_addelkadir_can_view_location_events_for_a_date(): void
    {
        $addelkadir = User::factory()->create(['role_id' => Roles::ADDELKADIR]);
        $chef = User::factory()->create(['role_id' => Roles::CHEF, 'name' => 'Test Chef']);
        $kg = Kindgarden::create(['kingar_name' => 'Bog\'cha 1', 'lat' => 41.31, 'lng' => 69.27, 'geofence_radius' => 200]);

        ChefLocationEvent::create([
            'user_id' => $chef->id, 'kindgarden_id' => $kg->id, 'event_type' => 'exit',
            'happened_at' => '2026-05-22 13:00:00', 'lat' => 41.32, 'lng' => 69.28,
            'distance_m' => 250, 'is_mock' => false,
        ]);
        ChefLocationEvent::create([
            'user_id' => $chef->id, 'kindgarden_id' => $kg->id, 'event_type' => 'enter',
            'happened_at' => '2026-05-22 13:20:00', 'lat' => 41.311, 'lng' => 69.271,
            'distance_m' => 30, 'is_mock' => false,
        ]);

        $response = $this->actingAs($addelkadir)
            ->get('/addelkadir/location-events?date=2026-05-22');

        $response->assertOk();
        $response->assertSee('Test Chef');
        $response->assertSee('Chiqish');
        $response->assertSee('Tashqarida');
        $response->assertSee('20 daq'); // 13:00 exit -> 13:20 enter
    }

    public function test_non_addelkadir_is_blocked(): void
    {
        $chef = User::factory()->create(['role_id' => Roles::CHEF]);
        $this->actingAs($chef)
            ->get('/addelkadir/location-events')
            ->assertForbidden();
    }

    public function test_chef_filter_narrows_results(): void
    {
        $addelkadir = User::factory()->create(['role_id' => Roles::ADDELKADIR]);
        $chefA = User::factory()->create(['role_id' => Roles::CHEF, 'name' => 'Chef Alpha']);
        $chefB = User::factory()->create(['role_id' => Roles::CHEF, 'name' => 'Chef Beta']);
        $kg = Kindgarden::create(['kingar_name' => 'KG', 'lat' => 41.31, 'lng' => 69.27, 'geofence_radius' => 200]);

        foreach ([$chefA, $chefB] as $c) {
            ChefLocationEvent::create([
                'user_id' => $c->id, 'kindgarden_id' => $kg->id, 'event_type' => 'beacon',
                'happened_at' => '2026-05-22 10:00:00', 'lat' => 41.31, 'lng' => 69.27,
                'distance_m' => 20, 'is_mock' => false,
            ]);
        }

        $response = $this->actingAs($addelkadir)
            ->get("/addelkadir/location-events?date=2026-05-22&chef_id={$chefA->id}");

        $response->assertOk();
        $response->assertSee('Chef Alpha');
        $response->assertDontSee('Chef Beta');
    }
}
```

- [ ] **Step 2: Local syntax check**

Run: `php -l tests/Feature/AddelkadirLocationEventsTest.php`
Expected: "No syntax errors detected"

- [ ] **Step 3: Commit (test runs on server)**

```bash
git add tests/Feature/AddelkadirLocationEventsTest.php
git commit -m "test(plan-5a): AddelkadirLocationEvents feature test"
```

- [ ] **Step 4: Server verification (deploy step)**

Serverda `git pull` qilingandan keyin:
```bash
php artisan test --filter=AddelkadirLocationEventsTest
```
Expected: 3 tests PASS. Ag `Kindgarden` yoki `User` factory mavjud bo'lmasa, factory yaratish kerak bo'lishi mumkin — server natijasiga qarab hal qilamiz.

---

## Task 8: Manual browser smoke test (server)

- [ ] **Step 1: Deploy**

Serverda:
```bash
cd /home/c/cj56359/public_html  # yoki to'g'ri yo'l
git pull origin master
php artisan route:clear && php artisan view:clear
```

- [ ] **Step 2: Browser check**

1. Addelkadir hisobi bilan kiring
2. Yuqori nav'da "Lokatsiya" link'ini bosing
3. URL: `/addelkadir/location-events` ochilishini tekshiring
4. Bugungi sana default tanlangani
5. Agar bugun hodisalar bo'lmasa "Bu kun uchun lokatsiya hodisalari yo'q" ko'rinadi
6. Oshpaz dropdown'i to'ldirilgani
7. (Agar test data bo'lsa) "Xaritada" 📍 link Yandex Maps ochishi

- [ ] **Step 3: Note any issues**

Muammo bo'lsa, qayd qiling va keyingi commit'da tuzating.

---

## Self-Review Checklist (plan yozuvchi tomonidan bajarildi)

- ✅ **Spec coverage:** § 5.1 (route+controller) → Task 4; § 5.2 (view, jadval, format, Yandex link) → Task 5; § 5.3 (nav) → Task 6; statistika (exit count, minutes outside) → Task 2,3 + controller Task 4.
- ✅ **Placeholder scan:** Barcha kod bloklar to'liq, TBD/TODO yo'q.
- ✅ **Type consistency:** `isInside(int, int): bool`, `countByType(iterable): array`, `totalMinutesOutside(iterable): int` — Task 1-3'da aniqlangan, Task 4 controller'da bir xil chaqiriladi.
- ⚠️ **Risk:** `User::factory()` va `Kindgarden::create()` server'da mavjudligini Task 7 step 4'da tasdiqlash kerak (Voyager loyihasida factory bo'lmasligi mumkin). `kingar_name` ustun nomi `Kindgarden` modelida tasdiqlangan (mavjud attendance view'da ishlatilgan).

---

## Notes for Implementer

- `kingar_name` — Kindgarden modelidagi nom ustuni (mavjud `addelkadir/attendance.blade.php`'da ishlatilgan, to'g'ri).
- `Roles::CHEF` = 6, `Roles::ADDELKADIR` = 10 (`app/Constants/Roles.php`).
- `happened_at` UTC saqlanadi, ko'rsatishda `->setTimezone('Asia/Tashkent')` (Plan 1 pattern).
- Statistika butun kun uchun hisoblanadi (sahifalashdan mustaqil) — shuning uchun `$allForStats` alohida query.
- Bu plan **faqat backend** — mobile hali event yubormaydi (Plan 5c'da). Jadval bo'sh ko'rinishi normal holat; manual SQL bilan test data qo'shib sinash mumkin.
