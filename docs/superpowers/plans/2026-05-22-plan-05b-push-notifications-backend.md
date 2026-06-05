# Plan 5b — Push Notifications Backend Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Backend tomondan FCM orqali push notification yuborish — 4 ta trigger: geofence-exit (bog'chadan chiqib ketgan oshpaz), ertalab kelmagan oshpaz, ertalabki eslatma oshpazga, yangi buyurtma oshpazga.

**Architecture:** `kreait/firebase-php` v7 FCM SDK. Sof `MulticastResultParser` helper (DB'siz, unit-test mahalliy ishlaydi) parses Firebase batch send report'idan qaysi tokenlar invalid ekanini ajratadi. `PushService` ushbu helper'ni va `Messaging` interfeysini ishlatadi — Firebase mock qilingan unit testlar mahalliy ishlaydi. DB'ga tegadigan operatsiyalar (chef_devices'ni o'qish, scheduled commands, observer, location event trigger) feature testlar bilan **serverda** tasdiqlanadi.

**Tech Stack:** Laravel 8.83, PHP 8.1+ (composer.json constraint bumped), `kreait/firebase-php` v7.x, `Illuminate\Support\Facades\Cache` (cooldown), existing chef_devices/users/kindgardens tables.

**Spec:** [docs/superpowers/specs/2026-05-22-plan-05-background-location-push-design.md](../specs/2026-05-22-plan-05-background-location-push-design.md) § 4, Bosqich B + C.

---

## Testing Constraint (eslatma)

Plan 5a'dagi qoidalar bu yerda ham amal qiladi:
- **Unit testlar** (mocked Messaging, helper'lar): local'da `& "D:\OSPanel\modules\PHP-8.3\PHP\php.exe" artisan test --filter=...` bilan ishlaydi va ishlashi shart.
- **Feature testlar** (DB kerak): local'da `php -l` syntax check, **serverda** `php artisan test --filter=...` bilan tekshiriladi.

Har bir PHP fayl yarat/o'zgartirilgandan keyin: `& "D:\OSPanel\modules\PHP-8.3\PHP\php.exe" -l <fayl>`.

---

## Manual prerequisites (foydalanuvchi tomonidan)

Bu plan'ni ijro etishdan oldin:
- ✅ Firebase loyihasi `chefmobile-ab8a1` yaratilgan
- ⚠️ Service account JSON xavfsiz joyda (sizning `C:\Users\Administrator\firebase-keys\chefmobile-credentials.json`). Plan'ning oxirgi qadamida serverga yuklash kerak.
- ⚠️ Server hosting'da cron interfeysi ishlashini bilish kerak. tw1.ru control panel'da cron sozlamasi bormi, yo'qmi tekshirilishi kerak — agar yo'q bo'lsa, alternativ qadamlar 12-vazifada keltirilgan.

---

## File Structure

| Fayl | Mas'uliyat | Amal |
|---|---|---|
| `composer.json` | `kreait/firebase-php` paketi + PHP 8.1+ constraint | Modify |
| `config/services.php` | `firebase.credentials_path` config kaliti | Modify |
| `app/Services/Push/MulticastResultParser.php` | Sof helper: Firebase MulticastSendReport'dan invalid tokenlar va xatolarni ajratadi | Create |
| `tests/Unit/Services/Push/MulticastResultParserTest.php` | MulticastResultParser unit testlari | Create |
| `app/Services/Push/PushService.php` | Asosiy push servis: chef_devices'ni o'qiydi, sendMulticast chaqiradi, invalid tokenlarni o'chiradi | Create |
| `tests/Unit/Services/Push/PushServiceTest.php` | PushService unit testlari (mocked Messaging) | Create |
| `app/Console/Commands/TestPush.php` | Manual verification: `php artisan chef:test-push {user_id}` | Create |
| `app/Console/Commands/SendMorningReminders.php` | 08:00 cron — barcha cheflar uchun | Create |
| `app/Console/Commands/NotifyMissingCheckIn.php` | 09:15 cron — kelmagan oshpazlar haqida Addelkadirlarga | Create |
| `app/Observers/OrderProductObserver.php` | `order_product::created` hook → kindergarten chef'lariga push | Create |
| `app/Providers/AppServiceProvider.php` | Observer ro'yxati | Modify |
| `app/Http/Controllers/Api/V1/Chef/LocationEventController.php` | exit-trigger: ish vaqtida exit kelsa, Addelkadirlarga push (5 daq cooldown) | Modify |
| `app/Console/Kernel.php` | Yangi 2 ta schedule entry | Modify |
| `tests/Feature/PushDispatcherTest.php` | sendToAllChefs / sendToAllAddelkadirs / sendToChefsOfKindgarden ni DB bilan tekshirish | Create |
| `tests/Feature/SendMorningRemindersTest.php` | Cron command'i barcha CHEF'larni topib push'ni chaqiradi | Create |
| `tests/Feature/NotifyMissingCheckInTest.php` | Bugun check-in qilmaganlar to'g'ri ro'yxatlanishi | Create |
| `tests/Feature/OrderProductObserverTest.php` | Yangi order_product yaratilganda kindergarten chef'lariga push | Create |
| `tests/Feature/LocationEventExitPushTest.php` | Ish vaqtida exit event Addelkadirlarga push + 5 daq cooldown | Create |

---

## Task 1: Composer — kreait/firebase-php paketi va PHP constraint

**Files:**
- Modify: `composer.json`

- [ ] **Step 1: Update `composer.json` require block**

Joriy `composer.json`'da:
```json
    "require": {
        "php": "^7.3|^8.0",
        ...
```

Buni quyidagiga o'zgartiring (PHP constraint'ni 8.1+ ga olib chiqamiz va `kreait/firebase-php` qo'shamiz):
```json
    "require": {
        "php": "^8.1",
        "kreait/firebase-php": "^7.16",
        ...
```

(Boshqa hech narsani o'zgartirmang — faqat `php` constraint'ni almashtiring va `kreait/firebase-php` qatorini qo'shing. Composer paketlarini alfavit tartibida saqlaydi, lekin biz qo'lda joylashtirsak ham bo'ladi.)

- [ ] **Step 2: Local'da install qilib bo'lmaydi (PHP 8.3 mavjud, lekin OSPanel'da composer yo'lda yo'q)**

`composer install` ni serverda (Task 12'da, kalit faylni yuklayotgan paytda) ishga tushiramiz. Local'da uchun: faqat `composer.json` o'zgartirildi.

- [ ] **Step 3: Commit**

```bash
git add composer.json
git commit -m "feat(plan-5b): require kreait/firebase-php + bump PHP to 8.1+"
```

---

## Task 2: Firebase config

**Files:**
- Modify: `config/services.php`

- [ ] **Step 1: Add `firebase` block to `config/services.php`**

Mavjud arrayning oxirida, `telegram` qatoridan keyin (yopuvchi `];` dan oldin):

```php
    'firebase' => [
        'credentials_path' => env('FIREBASE_CREDENTIALS'),
        'project_id' => env('FIREBASE_PROJECT_ID', 'chefmobile-ab8a1'),
    ],
```

- [ ] **Step 2: Syntax check**

Run: `& "D:\OSPanel\modules\PHP-8.3\PHP\php.exe" -l config/services.php`
Expected: No syntax errors.

- [ ] **Step 3: Commit**

```bash
git add config/services.php
git commit -m "feat(plan-5b): firebase services config"
```

---

## Task 3: MulticastResultParser — sof helper (TDD, local)

`kreait/firebase-php` v7'da `Messaging::sendMulticast(CloudMessage $message, array $tokens)` chaqiruvi `Kreait\Firebase\Messaging\MulticastSendReport` qaytaradi. Bu reportda har bir token uchun `SendReport` bor: muvaffaqiyat yoki xato. Permanent xatolar (`messaging/registration-token-not-registered`, `messaging/invalid-argument`, `messaging/invalid-registration-token`) tokenni `chef_devices` jadvalidan o'chirish kerakligini bildiradi. Transient xatolar (`messaging/server-unavailable`) keyingi safar qayta urinish.

**Files:**
- Create: `app/Services/Push/MulticastResultParser.php`
- Test: `tests/Unit/Services/Push/MulticastResultParserTest.php`

- [ ] **Step 1: Write the failing test (TDD)**

`tests/Unit/Services/Push/MulticastResultParserTest.php`:

```php
<?php

namespace Tests\Unit\Services\Push;

use App\Services\Push\MulticastResultParser;
use Tests\TestCase;

class MulticastResultParserTest extends TestCase
{
    public function test_classifies_empty_report_as_no_invalid_tokens(): void
    {
        $parser = new MulticastResultParser();
        $result = $parser->classify([], []);
        $this->assertSame([], $result['invalid_tokens']);
        $this->assertSame(0, $result['success_count']);
        $this->assertSame(0, $result['failure_count']);
    }

    public function test_classifies_all_successful(): void
    {
        $parser = new MulticastResultParser();
        $reports = [
            ['token' => 'tok1', 'success' => true, 'errorCode' => null],
            ['token' => 'tok2', 'success' => true, 'errorCode' => null],
        ];
        $result = $parser->classify($reports, ['tok1', 'tok2']);
        $this->assertSame(2, $result['success_count']);
        $this->assertSame(0, $result['failure_count']);
        $this->assertSame([], $result['invalid_tokens']);
    }

    public function test_classifies_invalid_registration_as_invalid(): void
    {
        $parser = new MulticastResultParser();
        $reports = [
            ['token' => 'tok1', 'success' => false, 'errorCode' => 'messaging/registration-token-not-registered'],
            ['token' => 'tok2', 'success' => true, 'errorCode' => null],
        ];
        $result = $parser->classify($reports, ['tok1', 'tok2']);
        $this->assertSame(1, $result['success_count']);
        $this->assertSame(1, $result['failure_count']);
        $this->assertSame(['tok1'], $result['invalid_tokens']);
    }

    public function test_treats_transient_errors_as_not_invalid(): void
    {
        $parser = new MulticastResultParser();
        $reports = [
            ['token' => 'tok1', 'success' => false, 'errorCode' => 'messaging/server-unavailable'],
        ];
        $result = $parser->classify($reports, ['tok1']);
        $this->assertSame(0, $result['success_count']);
        $this->assertSame(1, $result['failure_count']);
        $this->assertSame([], $result['invalid_tokens']);
    }

    public function test_invalid_argument_and_invalid_registration_token_are_pruned(): void
    {
        $parser = new MulticastResultParser();
        $reports = [
            ['token' => 'tok1', 'success' => false, 'errorCode' => 'messaging/invalid-argument'],
            ['token' => 'tok2', 'success' => false, 'errorCode' => 'messaging/invalid-registration-token'],
            ['token' => 'tok3', 'success' => false, 'errorCode' => 'messaging/registration-token-not-registered'],
        ];
        $result = $parser->classify($reports, ['tok1', 'tok2', 'tok3']);
        $this->assertSame(3, $result['failure_count']);
        $this->assertEqualsCanonicalizing(['tok1', 'tok2', 'tok3'], $result['invalid_tokens']);
    }
}
```

- [ ] **Step 2: Run test to verify it fails**

Run: `& "D:\OSPanel\modules\PHP-8.3\PHP\php.exe" artisan test --filter=MulticastResultParserTest`
Expected: FAIL (class not found).

- [ ] **Step 3: Implement**

`app/Services/Push/MulticastResultParser.php`:

```php
<?php

namespace App\Services\Push;

class MulticastResultParser
{
    /**
     * FCM error codes that indicate the token is permanently invalid and must be removed.
     */
    private const PERMANENT_FAILURE_CODES = [
        'messaging/registration-token-not-registered',
        'messaging/invalid-registration-token',
        'messaging/invalid-argument',
    ];

    /**
     * Classify per-token send results.
     *
     * @param array<int, array{token:string, success:bool, errorCode:?string}> $reports
     * @param array<int, string> $allTokens  the token list that was sent (used to keep ordering stable)
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
```

- [ ] **Step 4: Run test → PASS (5 tests)**

Run: `& "D:\OSPanel\modules\PHP-8.3\PHP\php.exe" artisan test --filter=MulticastResultParserTest`
Expected: PASS (5 tests).

- [ ] **Step 5: Syntax check + commit**

```bash
& "D:\OSPanel\modules\PHP-8.3\PHP\php.exe" -l app/Services/Push/MulticastResultParser.php
git add app/Services/Push/MulticastResultParser.php tests/Unit/Services/Push/MulticastResultParserTest.php
git commit -m "feat(plan-5b): MulticastResultParser pure helper for FCM batch reports"
```

---

## Task 4: PushService — core (mockable, unit-tested)

**Files:**
- Create: `app/Services/Push/PushService.php`
- Test: `tests/Unit/Services/Push/PushServiceTest.php`

PushService DI orqali `Kreait\Firebase\Contract\Messaging` interfeysini oladi. Testlar Mockery yoki Laravel'ning ichki mock'i bilan Messaging'ni almashtiradi. `ChefDevice` jadvali DB talab qiladi — shuning uchun `PushService::sendToDevices(Collection $devices, ...)` parametrlash usuli orqali ishlatamiz. Yuqori-darajadagi metodlar (`sendToAllChefs`, `sendToAllAddelkadirs`, `sendToChefsOfKindgarden`) Task 6'da feature-test qilinadi.

- [ ] **Step 1: Write the failing test**

`tests/Unit/Services/Push/PushServiceTest.php`:

```php
<?php

namespace Tests\Unit\Services\Push;

use App\Services\Push\PushService;
use App\Services\Push\MulticastResultParser;
use Kreait\Firebase\Contract\Messaging;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\MulticastSendReport;
use Kreait\Firebase\Messaging\SendReport;
use Tests\TestCase;
use Mockery;

class PushServiceTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_send_to_devices_with_empty_collection_short_circuits(): void
    {
        $messaging = Mockery::mock(Messaging::class);
        $messaging->shouldNotReceive('sendMulticast');

        $service = new PushService($messaging, new MulticastResultParser());
        $result = $service->sendToDevices(collect([]), 'Title', 'Body');

        $this->assertSame(0, $result['success_count']);
        $this->assertSame(0, $result['failure_count']);
        $this->assertSame([], $result['invalid_tokens']);
    }

    public function test_send_to_devices_extracts_tokens_and_calls_messaging(): void
    {
        $devices = collect([
            (object) ['fcm_token' => 'tok-A'],
            (object) ['fcm_token' => 'tok-B'],
        ]);

        $reportA = SendReport::success(['token' => 'tok-A'], ['name' => 'projects/p/messages/1']);
        $reportB = SendReport::success(['token' => 'tok-B'], ['name' => 'projects/p/messages/2']);
        $multicast = MulticastSendReport::withItems([$reportA, $reportB]);

        $messaging = Mockery::mock(Messaging::class);
        $messaging->shouldReceive('sendMulticast')
            ->once()
            ->withArgs(function ($message, $tokens) {
                return $message instanceof CloudMessage
                    && $tokens === ['tok-A', 'tok-B'];
            })
            ->andReturn($multicast);

        $service = new PushService($messaging, new MulticastResultParser());
        $result = $service->sendToDevices($devices, 'Hello', 'World', ['kind' => 'test']);

        $this->assertSame(2, $result['success_count']);
        $this->assertSame(0, $result['failure_count']);
        $this->assertSame([], $result['invalid_tokens']);
    }

    public function test_send_to_devices_returns_invalid_tokens_for_permanent_failures(): void
    {
        $devices = collect([
            (object) ['fcm_token' => 'tok-bad'],
        ]);

        $failure = SendReport::failure(
            ['token' => 'tok-bad'],
            new \Kreait\Firebase\Messaging\Http\Request\MessageTooBig() // any exception class is fine; we'll override via reflection in code that needs it
        );
        // Simpler: construct failure with an exception that has firebaseErrorCode 'messaging/registration-token-not-registered'.
        // If the test setup is too brittle, replace this test with one that mocks the parser instead.

        $messaging = Mockery::mock(Messaging::class);
        $messaging->shouldReceive('sendMulticast')->andReturn(
            MulticastSendReport::withItems([$failure])
        );

        $parser = Mockery::mock(MulticastResultParser::class);
        $parser->shouldReceive('classify')->andReturn([
            'success_count' => 0,
            'failure_count' => 1,
            'invalid_tokens' => ['tok-bad'],
        ]);

        $service = new PushService($messaging, $parser);
        $result = $service->sendToDevices($devices, 'Hi', 'There');

        $this->assertSame(['tok-bad'], $result['invalid_tokens']);
    }
}
```

NOTE FOR IMPLEMENTER: SendReport::failure() ning ikkinchi parametri Throwable. `kreait/firebase-php` ichki yuk'larida `MessageTooBig`-like sinflar bo'lishi mumkin. Agar real SendReport mock qilish qiyin bo'lsa, **uchinchi testni `MulticastResultParser` mock bilan qiling** (yuqoridagi `parser->shouldReceive('classify')->andReturn(...)`). Asosiy maqsad — PushService parser natijasini to'g'ri qaytarayotganini ko'rsatish.

- [ ] **Step 2: Run test to verify it fails**

Run: `& "D:\OSPanel\modules\PHP-8.3\PHP\php.exe" artisan test --filter=PushServiceTest`
Expected: FAIL (PushService class not found / kreait classes not installed locally).

ESLATMA: `kreait/firebase-php` paketi local'da o'rnatilmaganligi sababli `Kreait\Firebase\...` use'lari nomavjud bo'ladi. Bu test serverda composer install bo'lgandan keyin ishlaydi. **Local'da test fail bo'lishi normal — bu bosqichda commit qilamiz va serverda tasdiqlaymiz.**

Alternativa: Mockery bilan to'liq mocking qilingan, real SendReport quruvchisidan qochilgan testni yozish (faqat interfeysni mock qilish). Buni quyidagicha qiling:

```php
public function test_send_to_devices_with_empty_collection_short_circuits(): void
{
    $messaging = Mockery::mock(Messaging::class);
    $messaging->shouldNotReceive('sendMulticast');
    $service = new PushService($messaging, new MulticastResultParser());
    $result = $service->sendToDevices(collect([]), 'Title', 'Body');
    $this->assertSame(0, $result['success_count']);
}
```

Bu test composer install'siz ham ishlaydi agar `Kreait\Firebase\Contract\Messaging` faqat type-hint sifatida ishlatilsa va testda Mockery bilan moklansa. Lekin namespace import'i bo'lgani uchun class topilmasa autoload xato beradi. **Shuning uchun bu Task 4'ning testi ham serverda tekshiriladi**, local'da `php -l` yetarli.

- [ ] **Step 3: Implement PushService**

`app/Services/Push/PushService.php`:

```php
<?php

namespace App\Services\Push;

use App\Models\ChefDevice;
use Illuminate\Support\Collection;
use Kreait\Firebase\Contract\Messaging;
use Kreait\Firebase\Messaging\CloudMessage;
use Throwable;

class PushService
{
    public function __construct(
        private Messaging $messaging,
        private MulticastResultParser $parser,
    ) {}

    /**
     * Send a notification to a collection of ChefDevice objects.
     *
     * @param Collection $devices  collection of ChefDevice (or any object with fcm_token property)
     * @param array<string,string> $data  optional FCM data payload (string-string only per FCM rules)
     * @return array{success_count:int, failure_count:int, invalid_tokens:array<int,string>}
     */
    public function sendToDevices(Collection $devices, string $title, string $body, array $data = []): array
    {
        $tokens = $devices->pluck('fcm_token')->filter()->unique()->values()->all();

        if (empty($tokens)) {
            return ['success_count' => 0, 'failure_count' => 0, 'invalid_tokens' => []];
        }

        $message = CloudMessage::new()
            ->withNotification(['title' => $title, 'body' => $body])
            ->withData($data);

        try {
            $report = $this->messaging->sendMulticast($message, $tokens);
        } catch (Throwable $e) {
            \Log::error('FCM sendMulticast failed', ['error' => $e->getMessage(), 'token_count' => count($tokens)]);
            return ['success_count' => 0, 'failure_count' => count($tokens), 'invalid_tokens' => []];
        }

        $perTokenReports = [];
        foreach ($report->getItems() as $item) {
            $perTokenReports[] = [
                'token' => $item->target()->value(),
                'success' => $item->isSuccess(),
                'errorCode' => $item->error()?->getCode() ?? ($item->error()?->getMessage() ?? null),
            ];
        }

        $classified = $this->parser->classify($perTokenReports, $tokens);

        if (!empty($classified['invalid_tokens'])) {
            $this->pruneInvalidTokens($classified['invalid_tokens']);
        }

        return $classified;
    }

    /**
     * Send to all FCM devices belonging to a single user.
     */
    public function sendToUser(int $userId, string $title, string $body, array $data = []): array
    {
        $devices = ChefDevice::where('user_id', $userId)->get();
        return $this->sendToDevices($devices, $title, $body, $data);
    }

    /**
     * Send to all FCM devices of users matching a role.
     */
    public function sendToRole(int $roleId, string $title, string $body, array $data = []): array
    {
        $devices = ChefDevice::whereIn('user_id', function ($q) use ($roleId) {
            $q->select('id')->from('users')->where('role_id', $roleId);
        })->get();
        return $this->sendToDevices($devices, $title, $body, $data);
    }

    /**
     * Send to all chefs assigned to a kindergarten via the user_kindgardens pivot.
     */
    public function sendToChefsOfKindgarden(int $kindgardenId, string $title, string $body, array $data = []): array
    {
        $devices = ChefDevice::whereIn('user_id', function ($q) use ($kindgardenId) {
            $q->select('user_id')
              ->from('user_kindgardens')
              ->where('kingar_name_id', $kindgardenId);
        })->get();
        return $this->sendToDevices($devices, $title, $body, $data);
    }

    /**
     * Delete invalid tokens from chef_devices so future sends don't retry them.
     */
    private function pruneInvalidTokens(array $invalidTokens): void
    {
        if (empty($invalidTokens)) {
            return;
        }
        ChefDevice::whereIn('fcm_token', $invalidTokens)->delete();
    }
}
```

NOTE: `user_kindgardens` table'ning aniq nomi va kolonna nomi (`user_id` + `kingar_name_id` yoki boshqacha) **Implementer tomonidan tekshirilishi kerak**. Eski codebase'da pivot jadval nomi farq qilishi mumkin (`user_kindgarden`, `user_kindergartens` va h.k.). Migration fayllarda yoki `User::kindgarden()` relation'ida grep qiling: `grep -r "user_kindgarden" app/Models database/migrations`.

- [ ] **Step 4: Local syntax check**

Run: `& "D:\OSPanel\modules\PHP-8.3\PHP\php.exe" -l app/Services/Push/PushService.php`
Expected: No syntax errors. (Test running uchun composer install kerak — server'da ishlatamiz.)

- [ ] **Step 5: Commit**

```bash
git add app/Services/Push/PushService.php tests/Unit/Services/Push/PushServiceTest.php
git commit -m "feat(plan-5b): PushService with mockable Messaging + multi-device support"
```

---

## Task 5: Test artisan command — `chef:test-push`

Manual verification command — Bosqich B oxirida bitta foydalanuvchining FCM token'iga test xabar yuborish uchun.

**Files:**
- Create: `app/Console/Commands/TestPush.php`

- [ ] **Step 1: Implement**

`app/Console/Commands/TestPush.php`:

```php
<?php

namespace App\Console\Commands;

use App\Services\Push\PushService;
use Illuminate\Console\Command;

class TestPush extends Command
{
    protected $signature = 'chef:test-push {user_id : Target user ID}';
    protected $description = 'Send a test push notification to all FCM devices of the given user';

    public function handle(PushService $push): int
    {
        $userId = (int) $this->argument('user_id');
        $result = $push->sendToUser(
            $userId,
            'Test xabari',
            'ChefMobile push tizimidan test xabar (' . now()->setTimezone('Asia/Tashkent')->format('H:i') . ')',
            ['kind' => 'test']
        );
        $this->info(json_encode($result));
        return self::SUCCESS;
    }
}
```

- [ ] **Step 2: Syntax check + commit**

```bash
& "D:\OSPanel\modules\PHP-8.3\PHP\php.exe" -l app/Console/Commands/TestPush.php
git add app/Console/Commands/TestPush.php
git commit -m "feat(plan-5b): chef:test-push artisan command for manual verification"
```

---

## Task 6: Feature test — PushService DB-touching methods (server-verified)

DB feature testlar. Local'da `php -l` yetarli, serverda `php artisan test --filter=PushDispatcherTest` ishlatamiz.

**Files:**
- Create: `tests/Feature/PushDispatcherTest.php`

- [ ] **Step 1: Read existing patterns**

Read `tests/Feature/AddelkadirLocationEventsTest.php` (Plan 5a'dagi) — DB setUp, User::create, RefreshDatabase pattern qanday ishlatilganini ko'ring.

- [ ] **Step 2: Write the feature test**

`tests/Feature/PushDispatcherTest.php`:

```php
<?php

namespace Tests\Feature;

use App\Constants\Roles;
use App\Models\ChefDevice;
use App\Models\User;
use App\Services\Push\MulticastResultParser;
use App\Services\Push\PushService;
use Database\Seeders\AddelkadirRoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Kreait\Firebase\Contract\Messaging;
use Kreait\Firebase\Messaging\MulticastSendReport;
use Mockery;
use Tests\TestCase;

class PushDispatcherTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(AddelkadirRoleSeeder::class);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_send_to_user_fetches_user_devices_and_sends(): void
    {
        $user = User::create(['name' => 'Chef X', 'email' => 'cx@test.lo', 'password' => bcrypt('x'), 'role_id' => Roles::CHEF]);
        ChefDevice::create(['user_id' => $user->id, 'platform' => 'android', 'fcm_token' => 'tok-A', 'app_version' => '1.0']);
        ChefDevice::create(['user_id' => $user->id, 'platform' => 'android', 'fcm_token' => 'tok-B', 'app_version' => '1.0']);

        $messaging = Mockery::mock(Messaging::class);
        $messaging->shouldReceive('sendMulticast')
            ->once()
            ->withArgs(fn ($msg, $tokens) => count($tokens) === 2 && in_array('tok-A', $tokens, true) && in_array('tok-B', $tokens, true))
            ->andReturn(MulticastSendReport::withItems([]));

        $service = new PushService($messaging, new MulticastResultParser());
        $service->sendToUser($user->id, 'Hi', 'Body');
    }

    public function test_send_to_role_only_includes_devices_of_that_role(): void
    {
        $chef = User::create(['name' => 'C', 'email' => 'c@t.lo', 'password' => bcrypt('x'), 'role_id' => Roles::CHEF]);
        $boss = User::create(['name' => 'B', 'email' => 'b@t.lo', 'password' => bcrypt('x'), 'role_id' => Roles::BOSS]);
        ChefDevice::create(['user_id' => $chef->id, 'platform' => 'android', 'fcm_token' => 'chef-tok', 'app_version' => '1.0']);
        ChefDevice::create(['user_id' => $boss->id, 'platform' => 'android', 'fcm_token' => 'boss-tok', 'app_version' => '1.0']);

        $messaging = Mockery::mock(Messaging::class);
        $messaging->shouldReceive('sendMulticast')
            ->once()
            ->withArgs(fn ($msg, $tokens) => $tokens === ['chef-tok'])
            ->andReturn(MulticastSendReport::withItems([]));

        $service = new PushService($messaging, new MulticastResultParser());
        $service->sendToRole(Roles::CHEF, 'Hi', 'Body');
    }

    public function test_no_devices_short_circuits_without_calling_messaging(): void
    {
        $user = User::create(['name' => 'No Device', 'email' => 'nd@t.lo', 'password' => bcrypt('x'), 'role_id' => Roles::CHEF]);

        $messaging = Mockery::mock(Messaging::class);
        $messaging->shouldNotReceive('sendMulticast');

        $service = new PushService($messaging, new MulticastResultParser());
        $result = $service->sendToUser($user->id, 'Hi', 'Body');

        $this->assertSame(0, $result['success_count']);
    }
}
```

- [ ] **Step 3: Local syntax check + commit (test runs on server)**

```bash
& "D:\OSPanel\modules\PHP-8.3\PHP\php.exe" -l tests/Feature/PushDispatcherTest.php
git add tests/Feature/PushDispatcherTest.php
git commit -m "test(plan-5b): PushService DB-touching methods feature test"
```

---

## Task 7: Scheduled command — `chef:morning-reminder`

Har kuni 08:00 Asia/Tashkent vaqtida hamma CHEF rolidagi foydalanuvchilarga eslatma yuboradi.

**Files:**
- Create: `app/Console/Commands/SendMorningReminders.php`
- Test: `tests/Feature/SendMorningRemindersTest.php`

- [ ] **Step 1: Implement command**

`app/Console/Commands/SendMorningReminders.php`:

```php
<?php

namespace App\Console\Commands;

use App\Constants\Roles;
use App\Services\Push\PushService;
use Illuminate\Console\Command;

class SendMorningReminders extends Command
{
    protected $signature = 'chef:morning-reminder';
    protected $description = 'Send morning push reminder to all chefs (08:00 Asia/Tashkent)';

    public function handle(PushService $push): int
    {
        $result = $push->sendToRole(
            Roles::CHEF,
            'Davomatni unutmang',
            'Bog\'chaga kelganingizda ChefMobile ilovasini oching va "Keldim" tugmasini bosing.',
            ['kind' => 'morning_reminder']
        );
        $this->info('Sent: ' . $result['success_count'] . ', invalid: ' . count($result['invalid_tokens']));
        return self::SUCCESS;
    }
}
```

- [ ] **Step 2: Feature test**

`tests/Feature/SendMorningRemindersTest.php`:

```php
<?php

namespace Tests\Feature;

use App\Constants\Roles;
use App\Models\ChefDevice;
use App\Models\User;
use App\Services\Push\PushService;
use Database\Seeders\AddelkadirRoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

class SendMorningRemindersTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(AddelkadirRoleSeeder::class);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_command_invokes_push_to_chef_role(): void
    {
        $chef = User::create(['name' => 'C', 'email' => 'c@t.lo', 'password' => bcrypt('x'), 'role_id' => Roles::CHEF]);
        ChefDevice::create(['user_id' => $chef->id, 'platform' => 'android', 'fcm_token' => 'tok', 'app_version' => '1.0']);

        $mock = Mockery::mock(PushService::class);
        $mock->shouldReceive('sendToRole')
            ->once()
            ->with(Roles::CHEF, Mockery::type('string'), Mockery::type('string'), Mockery::on(fn ($d) => ($d['kind'] ?? null) === 'morning_reminder'))
            ->andReturn(['success_count' => 1, 'failure_count' => 0, 'invalid_tokens' => []]);
        $this->app->instance(PushService::class, $mock);

        $this->artisan('chef:morning-reminder')->assertExitCode(0);
    }
}
```

- [ ] **Step 3: Syntax check + commit**

```bash
& "D:\OSPanel\modules\PHP-8.3\PHP\php.exe" -l app/Console/Commands/SendMorningReminders.php
& "D:\OSPanel\modules\PHP-8.3\PHP\php.exe" -l tests/Feature/SendMorningRemindersTest.php
git add app/Console/Commands/SendMorningReminders.php tests/Feature/SendMorningRemindersTest.php
git commit -m "feat(plan-5b): chef:morning-reminder artisan command + test"
```

---

## Task 8: Scheduled command — `chef:notify-missing-checkin`

Har kuni 09:15 — bugun check-in qilmagan barcha CHEF'larni topib, Addelkadirlarga bitta yig'ma push yuboradi.

**Files:**
- Create: `app/Console/Commands/NotifyMissingCheckIn.php`
- Test: `tests/Feature/NotifyMissingCheckInTest.php`

- [ ] **Step 1: Implement command**

`app/Console/Commands/NotifyMissingCheckIn.php`:

```php
<?php

namespace App\Console\Commands;

use App\Constants\Roles;
use App\Models\ChefAttendance;
use App\Models\User;
use App\Services\Push\PushService;
use Illuminate\Console\Command;

class NotifyMissingCheckIn extends Command
{
    protected $signature = 'chef:notify-missing-checkin';
    protected $description = 'Notify all Addelkadirs about chefs who have not checked in today (09:15 Asia/Tashkent)';

    public function handle(PushService $push): int
    {
        $today = now()->setTimezone('Asia/Tashkent')->toDateString();

        $checkedInUserIds = ChefAttendance::where('date', $today)
            ->whereNotNull('check_in_at')
            ->pluck('user_id')
            ->all();

        $missing = User::where('role_id', Roles::CHEF)
            ->whereNotIn('id', $checkedInUserIds)
            ->orderBy('name')
            ->get(['id', 'name']);

        if ($missing->isEmpty()) {
            $this->info('All chefs checked in today.');
            return self::SUCCESS;
        }

        $names = $missing->pluck('name')->take(5)->implode(', ');
        $extra = $missing->count() > 5 ? ' va yana ' . ($missing->count() - 5) . ' ta' : '';
        $body = $missing->count() . ' oshpaz hali kelmadi: ' . $names . $extra;

        $result = $push->sendToRole(
            Roles::ADDELKADIR,
            'Kelmagan oshpazlar',
            $body,
            ['kind' => 'missing_checkin', 'count' => (string) $missing->count()]
        );

        $this->info('Sent: ' . $result['success_count'] . ', missing chefs: ' . $missing->count());
        return self::SUCCESS;
    }
}
```

- [ ] **Step 2: Feature test**

`tests/Feature/NotifyMissingCheckInTest.php`:

```php
<?php

namespace Tests\Feature;

use App\Constants\Roles;
use App\Models\ChefAttendance;
use App\Models\User;
use App\Services\Push\PushService;
use Database\Seeders\AddelkadirRoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

class NotifyMissingCheckInTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(AddelkadirRoleSeeder::class);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_notifies_addelkadirs_when_chefs_missing(): void
    {
        $checkedIn = User::create(['name' => 'In Chef', 'email' => 'ic@t.lo', 'password' => bcrypt('x'), 'role_id' => Roles::CHEF]);
        $missing1 = User::create(['name' => 'Out Chef A', 'email' => 'oa@t.lo', 'password' => bcrypt('x'), 'role_id' => Roles::CHEF]);
        $missing2 = User::create(['name' => 'Out Chef B', 'email' => 'ob@t.lo', 'password' => bcrypt('x'), 'role_id' => Roles::CHEF]);

        $today = now()->setTimezone('Asia/Tashkent')->toDateString();
        ChefAttendance::create([
            'user_id' => $checkedIn->id,
            'kindgarden_id' => 1,
            'date' => $today,
            'check_in_at' => now(),
            'check_in_lat' => 41.31, 'check_in_lng' => 69.27,
            'check_in_distance_m' => 10, 'check_in_is_late' => false, 'check_in_replaced_count' => 0,
            'check_out_replaced_count' => 0, 'check_out_undo_count' => 0,
        ]);

        $mock = Mockery::mock(PushService::class);
        $mock->shouldReceive('sendToRole')
            ->once()
            ->withArgs(function ($role, $title, $body, $data) {
                return $role === Roles::ADDELKADIR
                    && str_contains($body, 'Out Chef A')
                    && str_contains($body, 'Out Chef B')
                    && !str_contains($body, 'In Chef')
                    && ($data['kind'] ?? null) === 'missing_checkin';
            })
            ->andReturn(['success_count' => 1, 'failure_count' => 0, 'invalid_tokens' => []]);
        $this->app->instance(PushService::class, $mock);

        $this->artisan('chef:notify-missing-checkin')->assertExitCode(0);
    }

    public function test_no_op_when_all_chefs_checked_in(): void
    {
        $chef = User::create(['name' => 'A', 'email' => 'a@t.lo', 'password' => bcrypt('x'), 'role_id' => Roles::CHEF]);
        $today = now()->setTimezone('Asia/Tashkent')->toDateString();
        ChefAttendance::create([
            'user_id' => $chef->id, 'kindgarden_id' => 1, 'date' => $today,
            'check_in_at' => now(), 'check_in_lat' => 41.31, 'check_in_lng' => 69.27,
            'check_in_distance_m' => 10, 'check_in_is_late' => false, 'check_in_replaced_count' => 0,
            'check_out_replaced_count' => 0, 'check_out_undo_count' => 0,
        ]);

        $mock = Mockery::mock(PushService::class);
        $mock->shouldNotReceive('sendToRole');
        $this->app->instance(PushService::class, $mock);

        $this->artisan('chef:notify-missing-checkin')->assertExitCode(0);
    }
}
```

- [ ] **Step 3: Syntax check + commit**

```bash
& "D:\OSPanel\modules\PHP-8.3\PHP\php.exe" -l app/Console/Commands/NotifyMissingCheckIn.php
& "D:\OSPanel\modules\PHP-8.3\PHP\php.exe" -l tests/Feature/NotifyMissingCheckInTest.php
git add app/Console/Commands/NotifyMissingCheckIn.php tests/Feature/NotifyMissingCheckInTest.php
git commit -m "feat(plan-5b): chef:notify-missing-checkin command + test"
```

---

## Task 9: order_product Observer — yangi buyurtma push'i

**Files:**
- Create: `app/Observers/OrderProductObserver.php`
- Modify: `app/Providers/AppServiceProvider.php`
- Test: `tests/Feature/OrderProductObserverTest.php`

`App\Models\order_product` modelining `created` hodisasi'da kindergarten chef'lariga push yuboradi.

- [ ] **Step 1: Implement observer**

`app/Observers/OrderProductObserver.php`:

```php
<?php

namespace App\Observers;

use App\Models\order_product;
use App\Services\Push\PushService;
use Throwable;

class OrderProductObserver
{
    public function __construct(private PushService $push) {}

    public function created(order_product $order): void
    {
        if (!$order->kingar_name_id) {
            return;
        }
        try {
            $this->push->sendToChefsOfKindgarden(
                (int) $order->kingar_name_id,
                'Yangi buyurtma',
                'Bog\'cha #' . $order->kingar_name_id . ' uchun yangi buyurtma: ' . ($order->order_title ?? '—'),
                ['kind' => 'new_order', 'order_id' => (string) $order->id]
            );
        } catch (Throwable $e) {
            // Never let a push failure block order creation.
            \Log::error('OrderProductObserver push failed', ['error' => $e->getMessage(), 'order_id' => $order->id]);
        }
    }
}
```

- [ ] **Step 2: Register the observer in AppServiceProvider**

`app/Providers/AppServiceProvider.php` ning `boot()` metodida (mavjud `boot()` ichiga qo'shing):

```php
        \App\Models\order_product::observe(\App\Observers\OrderProductObserver::class);
```

(Agar `boot()` metodi bo'sh bo'lsa, hududni yarating va shu qatorni qo'shing. Boshqa registratsiyalar bo'lsa, ular yonida qoldiring.)

- [ ] **Step 3: Feature test**

`tests/Feature/OrderProductObserverTest.php`:

```php
<?php

namespace Tests\Feature;

use App\Constants\Roles;
use App\Models\Kindgarden;
use App\Models\order_product;
use App\Models\User;
use App\Services\Push\PushService;
use Database\Seeders\AddelkadirRoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

class OrderProductObserverTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(AddelkadirRoleSeeder::class);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_creating_order_product_triggers_push_to_kindergarten_chefs(): void
    {
        $kg = Kindgarden::create(['kingar_name' => 'KG', 'lat' => 41.31, 'lng' => 69.27, 'geofence_radius' => 200]);

        $mock = Mockery::mock(PushService::class);
        $mock->shouldReceive('sendToChefsOfKindgarden')
            ->once()
            ->withArgs(function ($kid, $title, $body, $data) use ($kg) {
                return $kid === $kg->id
                    && str_contains($title, 'Yangi buyurtma')
                    && ($data['kind'] ?? null) === 'new_order';
            })
            ->andReturn(['success_count' => 0, 'failure_count' => 0, 'invalid_tokens' => []]);
        $this->app->instance(PushService::class, $mock);

        order_product::create([
            'kingar_name_id' => $kg->id,
            'order_title' => 'Test buyurtma',
            'day_id' => 1,
        ]);
    }

    public function test_creating_order_without_kingar_name_id_skips_push(): void
    {
        $mock = Mockery::mock(PushService::class);
        $mock->shouldNotReceive('sendToChefsOfKindgarden');
        $this->app->instance(PushService::class, $mock);

        order_product::create(['order_title' => 'Orphan', 'day_id' => 1]);
    }
}
```

- [ ] **Step 4: Syntax check + commit**

```bash
& "D:\OSPanel\modules\PHP-8.3\PHP\php.exe" -l app/Observers/OrderProductObserver.php
& "D:\OSPanel\modules\PHP-8.3\PHP\php.exe" -l app/Providers/AppServiceProvider.php
& "D:\OSPanel\modules\PHP-8.3\PHP\php.exe" -l tests/Feature/OrderProductObserverTest.php
git add app/Observers/OrderProductObserver.php app/Providers/AppServiceProvider.php tests/Feature/OrderProductObserverTest.php
git commit -m "feat(plan-5b): OrderProductObserver pushes to kindergarten chefs on create"
```

---

## Task 10: LocationEventController — exit-during-work-hours trigger + cache cooldown

Ish vaqti = 08:00–19:00 Asia/Tashkent. Bir oshpaz uchun har 5 daqiqada bittadan ko'p exit push yuborilmaydi (cache cooldown).

**Files:**
- Modify: `app/Http/Controllers/Api/V1/Chef/LocationEventController.php`
- Test: `tests/Feature/LocationEventExitPushTest.php`

- [ ] **Step 1: Modify the controller**

`LocationEventController.php` da `use` ga qo'shing:
```php
use App\Constants\Roles;
use App\Services\Push\PushService;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;
```

`store()` metodining oxirida, `return response()->json(...)` dan oldin quyidagini qo'shing:

```php
        $this->maybeNotifyAddelkadirsOfExit($request->user(), $data['events']);
```

Method'ning konstruktor parametriga PushService qo'shing:
```php
    public function __construct(
        private AttendanceService $svc,
        private PushService $push,
    ) {}
```

Class oxiriga yangi private metod:

```php
    private function maybeNotifyAddelkadirsOfExit(\App\Models\User $user, array $events): void
    {
        $tashkent = now()->setTimezone('Asia/Tashkent');
        $hour = (int) $tashkent->format('H');
        if ($hour < 8 || $hour >= 19) {
            return;
        }
        $hasExit = false;
        foreach ($events as $e) {
            if (($e['event_type'] ?? null) === 'exit') {
                $hasExit = true;
                break;
            }
        }
        if (!$hasExit) {
            return;
        }
        $cacheKey = 'exit-push:user:' . $user->id;
        if (Cache::has($cacheKey)) {
            return;
        }
        Cache::put($cacheKey, true, now()->addMinutes(5));
        try {
            $this->push->sendToRole(
                Roles::ADDELKADIR,
                'Oshpaz bog\'chadan chiqdi',
                ($user->name ?? 'Oshpaz') . ' bog\'cha hududidan chiqib ketdi',
                ['kind' => 'exit', 'user_id' => (string) $user->id]
            );
        } catch (\Throwable $e) {
            \Log::error('Exit push failed', ['error' => $e->getMessage(), 'user_id' => $user->id]);
        }
    }
```

- [ ] **Step 2: Feature test**

`tests/Feature/LocationEventExitPushTest.php`:

```php
<?php

namespace Tests\Feature;

use App\Constants\Roles;
use App\Models\Kindgarden;
use App\Models\User;
use App\Services\Push\PushService;
use Carbon\Carbon;
use Database\Seeders\AddelkadirRoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Laravel\Sanctum\Sanctum;
use Mockery;
use Tests\TestCase;

class LocationEventExitPushTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(AddelkadirRoleSeeder::class);
        Cache::flush();
    }

    protected function tearDown(): void
    {
        Mockery::close();
        Carbon::setTestNow(null);
        parent::tearDown();
    }

    public function test_exit_during_work_hours_triggers_push(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-06-05 12:00:00', 'Asia/Tashkent'));

        $chef = User::create(['name' => 'C', 'email' => 'c@t.lo', 'password' => bcrypt('x'), 'role_id' => Roles::CHEF]);
        $kg = Kindgarden::create(['kingar_name' => 'KG', 'lat' => 41.31, 'lng' => 69.27, 'geofence_radius' => 200]);
        // Chef must be linked to kindergarten via pivot for recordLocationEvents to accept
        \DB::table('user_kindgardens')->insert(['user_id' => $chef->id, 'kingar_name_id' => $kg->id]);

        $mock = Mockery::mock(PushService::class);
        $mock->shouldReceive('sendToRole')
            ->once()
            ->withArgs(fn ($role, $t, $b, $d) => $role === Roles::ADDELKADIR && ($d['kind'] ?? null) === 'exit')
            ->andReturn(['success_count' => 0, 'failure_count' => 0, 'invalid_tokens' => []]);
        $this->app->instance(PushService::class, $mock);

        Sanctum::actingAs($chef);
        $this->postJson('/api/v1/chef/location-events', [
            'events' => [
                ['event_type' => 'exit', 'lat' => 41.32, 'lng' => 69.28, 'happened_at' => '2026-06-05T07:00:00Z', 'is_mock' => false],
            ],
        ])->assertOk();
    }

    public function test_exit_outside_work_hours_does_not_push(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-06-05 20:00:00', 'Asia/Tashkent'));

        $chef = User::create(['name' => 'C', 'email' => 'c@t.lo', 'password' => bcrypt('x'), 'role_id' => Roles::CHEF]);
        $kg = Kindgarden::create(['kingar_name' => 'KG', 'lat' => 41.31, 'lng' => 69.27, 'geofence_radius' => 200]);
        \DB::table('user_kindgardens')->insert(['user_id' => $chef->id, 'kingar_name_id' => $kg->id]);

        $mock = Mockery::mock(PushService::class);
        $mock->shouldNotReceive('sendToRole');
        $this->app->instance(PushService::class, $mock);

        Sanctum::actingAs($chef);
        $this->postJson('/api/v1/chef/location-events', [
            'events' => [
                ['event_type' => 'exit', 'lat' => 41.32, 'lng' => 69.28, 'happened_at' => '2026-06-05T15:00:00Z', 'is_mock' => false],
            ],
        ])->assertOk();
    }

    public function test_second_exit_within_5_minutes_is_suppressed_by_cache_cooldown(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-06-05 12:00:00', 'Asia/Tashkent'));

        $chef = User::create(['name' => 'C', 'email' => 'c@t.lo', 'password' => bcrypt('x'), 'role_id' => Roles::CHEF]);
        $kg = Kindgarden::create(['kingar_name' => 'KG', 'lat' => 41.31, 'lng' => 69.27, 'geofence_radius' => 200]);
        \DB::table('user_kindgardens')->insert(['user_id' => $chef->id, 'kingar_name_id' => $kg->id]);

        $mock = Mockery::mock(PushService::class);
        $mock->shouldReceive('sendToRole')->once()->andReturn(['success_count' => 0, 'failure_count' => 0, 'invalid_tokens' => []]);
        $this->app->instance(PushService::class, $mock);

        Sanctum::actingAs($chef);
        $payload = ['events' => [
            ['event_type' => 'exit', 'lat' => 41.32, 'lng' => 69.28, 'happened_at' => '2026-06-05T07:00:00Z', 'is_mock' => false],
        ]];
        $this->postJson('/api/v1/chef/location-events', $payload)->assertOk();
        $this->postJson('/api/v1/chef/location-events', $payload)->assertOk();
    }
}
```

NOTE: `user_kindgardens` pivot jadval va ustun nomi loyihada farq qilishi mumkin. Implementer tasdiqlasin: `grep -r "user_kindgardens\|user_kindgarden\|userKindgardens" app/Models database/migrations` natijasiga qarab kerakli nomlarni almashtirsin.

- [ ] **Step 3: Syntax check + commit**

```bash
& "D:\OSPanel\modules\PHP-8.3\PHP\php.exe" -l app/Http/Controllers/Api/V1/Chef/LocationEventController.php
& "D:\OSPanel\modules\PHP-8.3\PHP\php.exe" -l tests/Feature/LocationEventExitPushTest.php
git add app/Http/Controllers/Api/V1/Chef/LocationEventController.php tests/Feature/LocationEventExitPushTest.php
git commit -m "feat(plan-5b): LocationEventController exit push to addelkadirs + 5min cooldown"
```

---

## Task 11: Kernel.php — schedule entries

**Files:**
- Modify: `app/Console/Kernel.php`

- [ ] **Step 1: Add two schedule entries**

`schedule()` metodida, mavjud entries oxirida quyidagini qo'shing:

```php
        $schedule->command('chef:morning-reminder')
                 ->dailyAt('08:00')
                 ->timezone('Asia/Tashkent')
                 ->withoutOverlapping();

        $schedule->command('chef:notify-missing-checkin')
                 ->dailyAt('09:15')
                 ->timezone('Asia/Tashkent')
                 ->withoutOverlapping();
```

- [ ] **Step 2: Verify both commands registered**

Run: `& "D:\OSPanel\modules\PHP-8.3\PHP\php.exe" artisan list chef 2>&1`
Expected: `chef:morning-reminder`, `chef:notify-missing-checkin`, `chef:test-push` ko'rinishi.

(Local'da `composer install` hali bo'lmagani uchun PushService DI qiziqarli xato bermasligi shart — interfeysga bog'lanish boshqacha, run-time'da yuzaga keladi. Agar artisan list ishlamasa, server'da tasdiqlanadi.)

- [ ] **Step 3: Commit**

```bash
git add app/Console/Kernel.php
git commit -m "feat(plan-5b): schedule chef:morning-reminder (08:00) + chef:notify-missing-checkin (09:15)"
```

---

## Task 12: Manual deploy steps (foydalanuvchi tomonidan, serverda)

Bu kod commit'lari emas — bular siz serverda qo'lda bajaradigan qadamlar.

- [ ] **Step 1: Push local commits**

Local'da:
```bash
git push origin master
```

- [ ] **Step 2: Server'ga ulaning + git pull**

```bash
cd ~/public_html
rm -f .git/gc.log 2>/dev/null
git pull origin master
```

- [ ] **Step 3: Composer install (yangi paket kerak)**

```bash
composer install --no-dev --optimize-autoloader
```

Kutilgan: `kreait/firebase-php` o'rnatildi.

- [ ] **Step 4: Service account JSON faylini serverga yuklang**

Hosting control panel yoki SFTP orqali:
- Local fayl: `C:\Users\Administrator\firebase-keys\chefmobile-credentials.json`
- Server manzili: `~/public_html/storage/app/firebase-credentials.json`
- Ruxsat: `chmod 600 storage/app/firebase-credentials.json` (faqat o'qiy oladigan)

- [ ] **Step 5: `.env` ga FIREBASE_CREDENTIALS qo'shing**

Server `.env` faylida:
```
FIREBASE_CREDENTIALS=storage/app/firebase-credentials.json
FIREBASE_PROJECT_ID=chefmobile-ab8a1
```

Keyin cache clear:
```bash
php artisan config:clear
php artisan route:clear
```

- [ ] **Step 6: Test push artisan command bilan tekshirish**

Avval: ChefDevice'da kamida bitta test token bo'lsa, uni `user_id` orqali yuboring:

```bash
php artisan chef:test-push 1
```

(`1` o'rniga sizning haqiqiy foydalanuvchi ID'sini qo'ying. Faqat o'sha foydalanuvchining `chef_devices` jadvalida `fcm_token` bo'lsa push boradi. Mobile FCM hali Plan 5c'da yoqiladi, hozir bu jadval bo'sh bo'lishi mumkin — u holda "0 success, 0 failure" qaytadi va bu **kutilgan natija**.)

- [ ] **Step 7: Feature testlar (5 ta) serverda**

```bash
php artisan test --filter=PushDispatcherTest
php artisan test --filter=SendMorningRemindersTest
php artisan test --filter=NotifyMissingCheckInTest
php artisan test --filter=OrderProductObserverTest
php artisan test --filter=LocationEventExitPushTest
```

Kutilgan: hammasi PASS. Agar `user_kindgardens` pivot jadval nomi farq qilsa, `LocationEventExitPushTest`ni mos ravishda tuzating.

- [ ] **Step 8: Cron sozlash**

tw1.ru hosting control panel'ida cron entry qo'shing:
```
* * * * * cd ~/public_html && php artisan schedule:run >> /dev/null 2>&1
```

Agar control panel'da cron'siz bo'lsa, alternativ — `nohup` bilan boshqarib turuvchi script:
```bash
nohup bash -c 'while true; do php artisan schedule:run; sleep 60; done' > /tmp/scheduler.log 2>&1 &
```

(Bu sessiyani saqlab qoladi. Hosting qayta yuklansa, qaytadan ishga tushirish kerak — shuning uchun cron asosiy variant.)

- [ ] **Step 9: Verifikatsiya — schedule:list**

```bash
php artisan schedule:list
```

Kutilgan: `chef:morning-reminder` (08:00 Asia/Tashkent) va `chef:notify-missing-checkin` (09:15 Asia/Tashkent) ko'rinishi.

- [ ] **Step 10: Ertasi kun monitor qiling**

- 08:00 — Mobile FCM yoqilgan oshpazlar (Plan 5c bajarilgandan keyin) telefoniga eslatma keladimi?
- 09:15 — Addelkadirlarga kelmaganlar haqida push?
- TechnologController orqali yangi order_product yaratilsa — kindergarten chef'lariga push?

---

## Self-Review Checklist (plan yozuvchi tomonidan bajarildi)

- ✅ **Spec coverage:** § 4.1 PushService → Task 4; § 4.2 4 trigger → Task 7 (morning reminder), Task 8 (missing check-in), Task 9 (order), Task 10 (geofence-exit); § 4.4 Firebase setup → Task 12.
- ✅ **Placeholder scan:** Hammasi to'liq kod, TBD yo'q. Faqat ikkita NOTE: SendReport mock murakkabligi (Task 4 step 1) va `user_kindgardens` pivot jadval nomi (Task 4 + Task 10) — ikkalasi ham implementer tomonidan tekshirilishi kerakligi aniq belgilangan.
- ✅ **Type consistency:** `PushService::sendToDevices(Collection, string, string, array): array` va `array{success_count, failure_count, invalid_tokens}` butun plan davomida bir xil ishlatilgan.
- ⚠️ **Risk #1:** `user_kindgardens` pivot jadval va ustun nomlari noma'lum. Implementer Task 4 boshlanishida codebase'dan grep qilib aniqlaydi. Agar topilmasa, BLOCKED hisobotini berib eskalatsiya qilishi kerak.
- ⚠️ **Risk #2:** `kreait/firebase-php` `SendReport::failure()` mock qilish murakkab — Task 4'ning 3-testi `MulticastResultParser` mock'i bilan ham qilinishi mumkin (oddiyroq). Implementer xohlasa shu yo'lni tanlasin.
- ⚠️ **Risk #3:** Local'da `composer install` yo'qligi sababli `kreait/firebase-php` namespace'lari topilmaydi. Task 4 unit testi local'da run qilinmasligi mumkin — bu kutilgan; serverda Task 12 step 7'da ishlatamiz.

---

## Notes for Implementer

- **PHP path:** Local'da php yo'lda yo'q. `& "D:\OSPanel\modules\PHP-8.3\PHP\php.exe"` ishlatilsin.
- **Firebase SDK installation:** Composer install local'da OSPanel'da mumkin emas (composer yo'lda yo'q). Composer install serverda Task 12'da bajariladi. Local'da test running cheklangan — bu sababli ko'p tests serverda tasdiqlanadi.
- **`order_product` model snake_case:** Bu loyihaning eski qismi. Yangi kod yozilganda ham `App\Models\order_product` bilan ishlash kerak. PSR-12'ga zid lekin codebase pattern.
- **Composer.json constraint bump (`^7.3|^8.0` → `^8.1`):** Server PHP 8.3'da ishlaydi, bu xavfsiz. Agar hosting PHP 7.x ga qaytarilsa, plan butunlay buziladi — implementatsiyadan oldin server `php -v` ni tekshirish kerak.
- **Cache driver:** Plan `Illuminate\Support\Facades\Cache` ishlatadi. Server'da default `file` driver ishlaydi (Redis kerak emas). 5 daqiqalik TTL `Cache::put($key, $value, now()->addMinutes(5))` bilan to'g'ri ishlaydi.
- **Pivot table mavjud:** Spec § 2.1 ga ko'ra `user_kindgardens` jadvali allaqachon mavjud (Voyager via Kindgarden::users() relation). Implementer model relation'idan o'qib aniqlaydi.
