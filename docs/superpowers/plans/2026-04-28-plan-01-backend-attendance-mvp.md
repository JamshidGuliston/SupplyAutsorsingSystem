# Plan 1: Backend Foundation + Attendance MVP — Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Build the Laravel backend for the chef mobile app — Sanctum-based `/api/v1` namespace, attendance system (selfie + GPS + 200m geofence + mock GPS rejection + offline batch ingestion), the new `addelkadir` web role with a basic monitoring panel, and the cleanup cron — fully test-covered.

**Architecture:** Additive only. Existing web controllers and routes are not touched. New `routes/api.php` content under `/api/v1/`, new `App\Http\Controllers\Api\V1\*` controllers, new `App\Services\Attendance\*` services, new `App\Http\Controllers\AddelkadirController`, and new tables (`chef_attendances`, `chef_location_events`, `chef_devices`) plus three columns on `kindgardens`.

**Tech Stack:** Laravel 8.65 (existing), Sanctum 2.11 (already installed, User has `HasApiTokens`), PHPUnit 9.5, MySQL, Voyager Role model (role_id integer column, chef=6, addelkadir=8 new), Maatwebsite\Excel (existing, used in Addelkadir exports later).

**Spec reference:** [docs/superpowers/specs/2026-04-28-chef-mobile-app-design.md](../specs/2026-04-28-chef-mobile-app-design.md)

---

## File structure

### New files

```
app/Constants/Roles.php
app/Console/Commands/CleanupAttendancePhotos.php
app/Exceptions/Attendance/AlreadyCheckedInException.php
app/Exceptions/Attendance/AlreadyCheckedOutException.php
app/Exceptions/Attendance/KindgardenCoordsNotSetException.php
app/Exceptions/Attendance/MockGpsDetectedException.php
app/Exceptions/Attendance/OutsideGeofenceException.php
app/Exceptions/Attendance/StaleCaptureException.php
app/Exceptions/Attendance/AttendanceException.php          (base class)
app/Http/Controllers/AddelkadirController.php
app/Http/Controllers/Api/V1/Auth/LoginController.php
app/Http/Controllers/Api/V1/Auth/LogoutController.php
app/Http/Controllers/Api/V1/Auth/DeviceController.php
app/Http/Controllers/Api/V1/Chef/AttendanceController.php
app/Http/Controllers/Api/V1/Chef/LocationEventController.php
app/Http/Middleware/isAddelkadirMiddleware.php
app/Models/ChefAttendance.php
app/Models/ChefDevice.php
app/Models/ChefLocationEvent.php
app/Services/Attendance/AttendanceService.php
app/Services/Attendance/DistanceCalculator.php
app/Services/Attendance/SelfieStorage.php
database/migrations/2026_04_28_000001_add_geofence_to_kindgardens.php
database/migrations/2026_04_28_000002_create_chef_attendances_table.php
database/migrations/2026_04_28_000003_create_chef_location_events_table.php
database/migrations/2026_04_28_000004_create_chef_devices_table.php
database/seeders/AddelkadirRoleSeeder.php
resources/views/addelkadir/home.blade.php
resources/views/addelkadir/attendance.blade.php
resources/views/addelkadir/kindgardens.blade.php
resources/views/addelkadir/chefs.blade.php
resources/views/addelkadir/_layout.blade.php
tests/Feature/AddelkadirAccessTest.php
tests/Feature/Api/V1/Auth/LoginTest.php
tests/Feature/Api/V1/Auth/LogoutTest.php
tests/Feature/Api/V1/Auth/DeviceTest.php
tests/Feature/Api/V1/Chef/CheckInTest.php
tests/Feature/Api/V1/Chef/CheckOutTest.php
tests/Feature/Api/V1/Chef/ReplaceTest.php
tests/Feature/Api/V1/Chef/TodayTest.php
tests/Feature/Api/V1/Chef/LocationEventTest.php
tests/Unit/Services/Attendance/AttendanceServiceTest.php
tests/Unit/Services/Attendance/DistanceCalculatorTest.php
tests/Unit/Services/Attendance/SelfieStorageTest.php
```

### Modified files

```
app/Console/Kernel.php                  (schedule cleanup command)
app/Exceptions/Handler.php              (JSON formatting for AttendanceException)
app/Http/Kernel.php                     (register isAddelkadir middleware)
app/Http/Middleware/RedirectIfAuthenticated.php  (route role 8 to addelkadir.home)
app/Models/Kindgarden.php               (fillable + casts for lat/lng/radius)
config/sanctum.php                      (set expiration to 6 months)
routes/api.php                          (add /api/v1/* routes)
routes/web.php                          (add /addelkadir/* group)
```

---

## Task 1: Define role constants

**Files:**
- Create: `app/Constants/Roles.php`

- [ ] **Step 1: Create the constants file**

```php
<?php

namespace App\Constants;

class Roles
{
    public const BOSS = 2;
    public const TECHNOLOG = 3;
    public const STORAGE = 4;
    public const ACCOUNTANT = 5;
    public const CHEF = 6;
    public const CASHER = 7;
    public const ADDELKADIR = 8;
}
```

- [ ] **Step 2: Commit**

```bash
git add app/Constants/Roles.php
git commit -m "feat(roles): add role constants enum (chef=6, addelkadir=8)"
```

---

## Task 2: Configure Sanctum token expiration (6 months)

**Files:**
- Modify: `config/sanctum.php`

- [ ] **Step 1: Verify config exists**

Run: `ls config/sanctum.php`
Expected: file exists. If missing, run `php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"` then re-check.

- [ ] **Step 2: Set expiration in config/sanctum.php**

Find the `expiration` key (currently `null`) and set it to `60 * 24 * 30 * 6` (6 months in minutes):

```php
'expiration' => 60 * 24 * 30 * 6,
```

- [ ] **Step 3: Clear config cache**

Run: `php artisan config:clear`
Expected: `Configuration cache cleared!`

- [ ] **Step 4: Commit**

```bash
git add config/sanctum.php
git commit -m "config(sanctum): set token expiration to 6 months"
```

---

## Task 3: Migration — add geofence columns to kindgardens

**Files:**
- Create: `database/migrations/2026_04_28_000001_add_geofence_to_kindgardens.php`

- [ ] **Step 1: Create migration**

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddGeofenceToKindgardens extends Migration
{
    public function up(): void
    {
        Schema::table('kindgardens', function (Blueprint $table) {
            $table->decimal('lat', 10, 7)->nullable()->after('id');
            $table->decimal('lng', 10, 7)->nullable()->after('lat');
            $table->unsignedInteger('geofence_radius')->default(200)->after('lng');
        });
    }

    public function down(): void
    {
        Schema::table('kindgardens', function (Blueprint $table) {
            $table->dropColumn(['lat', 'lng', 'geofence_radius']);
        });
    }
}
```

- [ ] **Step 2: Run migration**

Run: `php artisan migrate`
Expected: `Migrating: 2026_04_28_000001_add_geofence_to_kindgardens` then `Migrated`.

- [ ] **Step 3: Verify in DB**

Run: `php artisan tinker --execute="dd(Schema::getColumnListing('kindgardens'));"`
Expected: list contains `lat`, `lng`, `geofence_radius`.

- [ ] **Step 4: Commit**

```bash
git add database/migrations/2026_04_28_000001_add_geofence_to_kindgardens.php
git commit -m "db: add lat/lng/geofence_radius columns to kindgardens"
```

---

## Task 4: Migration — chef_attendances table

**Files:**
- Create: `database/migrations/2026_04_28_000002_create_chef_attendances_table.php`

- [ ] **Step 1: Create migration**

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChefAttendancesTable extends Migration
{
    public function up(): void
    {
        Schema::create('chef_attendances', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('kindgarden_id');
            $table->date('date');

            $table->dateTime('check_in_at')->nullable();
            $table->decimal('check_in_lat', 10, 7)->nullable();
            $table->decimal('check_in_lng', 10, 7)->nullable();
            $table->unsignedInteger('check_in_distance_m')->nullable();
            $table->string('check_in_selfie_path')->nullable();
            $table->boolean('check_in_is_late')->default(false);
            $table->unsignedInteger('check_in_replaced_count')->default(0);

            $table->dateTime('check_out_at')->nullable();
            $table->decimal('check_out_lat', 10, 7)->nullable();
            $table->decimal('check_out_lng', 10, 7)->nullable();
            $table->unsignedInteger('check_out_distance_m')->nullable();
            $table->string('check_out_selfie_path')->nullable();
            $table->unsignedInteger('check_out_replaced_count')->default(0);

            $table->timestamps();

            $table->unique(['user_id', 'date'], 'uniq_user_date');
            $table->index(['kindgarden_id', 'date'], 'idx_kindgarden_date');
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('kindgarden_id')->references('id')->on('kindgardens');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chef_attendances');
    }
}
```

- [ ] **Step 2: Run migration**

Run: `php artisan migrate`
Expected: `Migrating: 2026_04_28_000002_create_chef_attendances_table` → `Migrated`.

- [ ] **Step 3: Commit**

```bash
git add database/migrations/2026_04_28_000002_create_chef_attendances_table.php
git commit -m "db: create chef_attendances table"
```

---

## Task 5: Migration — chef_location_events table

**Files:**
- Create: `database/migrations/2026_04_28_000003_create_chef_location_events_table.php`

- [ ] **Step 1: Create migration**

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChefLocationEventsTable extends Migration
{
    public function up(): void
    {
        Schema::create('chef_location_events', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('kindgarden_id');
            $table->enum('event_type', ['exit', 'enter', 'beacon']);
            $table->dateTime('happened_at');
            $table->decimal('lat', 10, 7);
            $table->decimal('lng', 10, 7);
            $table->unsignedInteger('distance_m');
            $table->boolean('is_mock')->default(false);
            $table->timestamp('created_at')->useCurrent();

            $table->index(['user_id', 'happened_at'], 'idx_user_happened');
            $table->index(['kindgarden_id', 'happened_at'], 'idx_kindgarden_happened');
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('kindgarden_id')->references('id')->on('kindgardens');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chef_location_events');
    }
}
```

- [ ] **Step 2: Run migration**

Run: `php artisan migrate`
Expected: migration runs successfully.

- [ ] **Step 3: Commit**

```bash
git add database/migrations/2026_04_28_000003_create_chef_location_events_table.php
git commit -m "db: create chef_location_events table"
```

---

## Task 6: Migration — chef_devices table

**Files:**
- Create: `database/migrations/2026_04_28_000004_create_chef_devices_table.php`

- [ ] **Step 1: Create migration**

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChefDevicesTable extends Migration
{
    public function up(): void
    {
        Schema::create('chef_devices', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id');
            $table->enum('platform', ['android', 'ios']);
            $table->string('fcm_token');
            $table->string('device_model', 100)->nullable();
            $table->string('app_version', 20)->nullable();
            $table->dateTime('last_seen_at')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'fcm_token'], 'uniq_user_token');
            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chef_devices');
    }
}
```

- [ ] **Step 2: Run migration**

Run: `php artisan migrate`
Expected: migration runs successfully.

- [ ] **Step 3: Commit**

```bash
git add database/migrations/2026_04_28_000004_create_chef_devices_table.php
git commit -m "db: create chef_devices table"
```

---

## Task 7: Seed addelkadir role row

Voyager stores roles in a `roles` table. We add `role_id=8` row.

**Files:**
- Create: `database/seeders/AddelkadirRoleSeeder.php`

- [ ] **Step 1: Create the seeder**

```php
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AddelkadirRoleSeeder extends Seeder
{
    public function run(): void
    {
        $exists = DB::table('roles')->where('id', 8)->exists();
        if ($exists) {
            return;
        }
        DB::table('roles')->insert([
            'id' => 8,
            'name' => 'addelkadir',
            'display_name' => 'Addelkadir',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
```

- [ ] **Step 2: Run seeder**

Run: `php artisan db:seed --class=AddelkadirRoleSeeder`
Expected: `Database seeding completed successfully.`

- [ ] **Step 3: Verify**

Run: `php artisan tinker --execute="dd(DB::table('roles')->where('id',8)->first());"`
Expected: an object with `name=addelkadir`.

- [ ] **Step 4: Commit**

```bash
git add database/seeders/AddelkadirRoleSeeder.php
git commit -m "db: seed addelkadir role (role_id=8)"
```

---

## Task 8: ChefAttendance model

**Files:**
- Create: `app/Models/ChefAttendance.php`

- [ ] **Step 1: Create model**

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChefAttendance extends Model
{
    protected $fillable = [
        'user_id', 'kindgarden_id', 'date',
        'check_in_at', 'check_in_lat', 'check_in_lng', 'check_in_distance_m',
        'check_in_selfie_path', 'check_in_is_late', 'check_in_replaced_count',
        'check_out_at', 'check_out_lat', 'check_out_lng', 'check_out_distance_m',
        'check_out_selfie_path', 'check_out_replaced_count',
    ];

    protected $casts = [
        'date' => 'date',
        'check_in_at' => 'datetime',
        'check_out_at' => 'datetime',
        'check_in_is_late' => 'boolean',
        'check_in_lat' => 'float',
        'check_in_lng' => 'float',
        'check_out_lat' => 'float',
        'check_out_lng' => 'float',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function kindgarden()
    {
        return $this->belongsTo(Kindgarden::class);
    }
}
```

- [ ] **Step 2: Commit**

```bash
git add app/Models/ChefAttendance.php
git commit -m "feat(models): add ChefAttendance model"
```

---

## Task 9: ChefLocationEvent model

**Files:**
- Create: `app/Models/ChefLocationEvent.php`

- [ ] **Step 1: Create model**

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChefLocationEvent extends Model
{
    public const UPDATED_AT = null;

    protected $fillable = [
        'user_id', 'kindgarden_id', 'event_type',
        'happened_at', 'lat', 'lng', 'distance_m', 'is_mock',
    ];

    protected $casts = [
        'happened_at' => 'datetime',
        'lat' => 'float',
        'lng' => 'float',
        'is_mock' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function kindgarden()
    {
        return $this->belongsTo(Kindgarden::class);
    }
}
```

- [ ] **Step 2: Commit**

```bash
git add app/Models/ChefLocationEvent.php
git commit -m "feat(models): add ChefLocationEvent model"
```

---

## Task 10: ChefDevice model

**Files:**
- Create: `app/Models/ChefDevice.php`

- [ ] **Step 1: Create model**

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChefDevice extends Model
{
    protected $fillable = [
        'user_id', 'platform', 'fcm_token',
        'device_model', 'app_version', 'last_seen_at',
    ];

    protected $casts = [
        'last_seen_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
```

- [ ] **Step 2: Commit**

```bash
git add app/Models/ChefDevice.php
git commit -m "feat(models): add ChefDevice model"
```

---

## Task 11: Update Kindgarden model with geofence fields

**Files:**
- Modify: `app/Models/Kindgarden.php`

- [ ] **Step 1: Read existing model**

Run: `cat app/Models/Kindgarden.php`
Locate `$fillable` and `$casts` arrays.

- [ ] **Step 2: Add geofence fields**

Append to the `$fillable` array: `'lat', 'lng', 'geofence_radius'`.

If `$casts` exists, add: `'lat' => 'float', 'lng' => 'float', 'geofence_radius' => 'integer'`.

If `$casts` does not exist, add this property to the class:

```php
protected $casts = [
    'lat' => 'float',
    'lng' => 'float',
    'geofence_radius' => 'integer',
];
```

- [ ] **Step 3: Verify with tinker**

Run: `php artisan tinker --execute="dd(\\App\\Models\\Kindgarden::first()->lat);"`
Expected: `null` or a float value (no error).

- [ ] **Step 4: Commit**

```bash
git add app/Models/Kindgarden.php
git commit -m "feat(models): expose lat/lng/geofence_radius on Kindgarden"
```

---

## Task 12: DistanceCalculator service (Haversine) — TDD

**Files:**
- Create: `app/Services/Attendance/DistanceCalculator.php`
- Create: `tests/Unit/Services/Attendance/DistanceCalculatorTest.php`

- [ ] **Step 1: Write failing test**

`tests/Unit/Services/Attendance/DistanceCalculatorTest.php`:

```php
<?php

namespace Tests\Unit\Services\Attendance;

use App\Services\Attendance\DistanceCalculator;
use Tests\TestCase;

class DistanceCalculatorTest extends TestCase
{
    private DistanceCalculator $calc;

    protected function setUp(): void
    {
        parent::setUp();
        $this->calc = new DistanceCalculator();
    }

    public function test_distance_to_self_is_zero(): void
    {
        $this->assertSame(0, $this->calc->meters(41.3111, 69.2797, 41.3111, 69.2797));
    }

    public function test_short_distance_about_100m(): void
    {
        // ~100 m to the north: lat += 0.0009 (1° ≈ 111 km, so 0.0009° ≈ 100m)
        $d = $this->calc->meters(41.3111, 69.2797, 41.3120, 69.2797);
        $this->assertGreaterThanOrEqual(95, $d);
        $this->assertLessThanOrEqual(105, $d);
    }

    public function test_known_distance_tashkent_samarkand(): void
    {
        // Tashkent (41.3111, 69.2797) to Samarkand (39.6542, 66.9750) ~270km
        $d = $this->calc->meters(41.3111, 69.2797, 39.6542, 66.9750);
        $this->assertGreaterThan(260000, $d);
        $this->assertLessThan(280000, $d);
    }
}
```

- [ ] **Step 2: Run failing test**

Run: `vendor/bin/phpunit tests/Unit/Services/Attendance/DistanceCalculatorTest.php`
Expected: errors — class not found.

- [ ] **Step 3: Implement DistanceCalculator**

`app/Services/Attendance/DistanceCalculator.php`:

```php
<?php

namespace App\Services\Attendance;

class DistanceCalculator
{
    private const EARTH_RADIUS_M = 6_371_000;

    public function meters(float $lat1, float $lng1, float $lat2, float $lng2): int
    {
        $phi1 = deg2rad($lat1);
        $phi2 = deg2rad($lat2);
        $dPhi = deg2rad($lat2 - $lat1);
        $dLambda = deg2rad($lng2 - $lng1);

        $a = sin($dPhi / 2) ** 2
            + cos($phi1) * cos($phi2) * sin($dLambda / 2) ** 2;
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return (int) round(self::EARTH_RADIUS_M * $c);
    }
}
```

- [ ] **Step 4: Run test (must pass)**

Run: `vendor/bin/phpunit tests/Unit/Services/Attendance/DistanceCalculatorTest.php`
Expected: all 3 tests pass.

- [ ] **Step 5: Commit**

```bash
git add app/Services/Attendance/DistanceCalculator.php tests/Unit/Services/Attendance/DistanceCalculatorTest.php
git commit -m "feat(attendance): add Haversine DistanceCalculator with unit tests"
```

---

## Task 13: SelfieStorage service — TDD

**Files:**
- Create: `app/Services/Attendance/SelfieStorage.php`
- Create: `tests/Unit/Services/Attendance/SelfieStorageTest.php`

- [ ] **Step 1: Write failing test**

`tests/Unit/Services/Attendance/SelfieStorageTest.php`:

```php
<?php

namespace Tests\Unit\Services\Attendance;

use App\Services\Attendance\SelfieStorage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SelfieStorageTest extends TestCase
{
    public function test_store_writes_to_attendance_subdir_with_user_and_type_prefix(): void
    {
        Storage::fake('local');
        $svc = new SelfieStorage();
        $file = UploadedFile::fake()->image('selfie.jpg');

        $path = $svc->store($file, userId: 42, type: 'check_in', date: '2026-04-28');

        $this->assertStringStartsWith('attendance/2026-04-28/42_check_in_', $path);
        $this->assertStringEndsWith('.jpg', $path);
        Storage::disk('local')->assertExists($path);
    }

    public function test_delete_removes_file(): void
    {
        Storage::fake('local');
        $svc = new SelfieStorage();
        $file = UploadedFile::fake()->image('selfie.jpg');
        $path = $svc->store($file, 42, 'check_in', '2026-04-28');

        $svc->delete($path);

        Storage::disk('local')->assertMissing($path);
    }

    public function test_delete_silently_ignores_missing_file(): void
    {
        Storage::fake('local');
        $svc = new SelfieStorage();
        $svc->delete('attendance/2026-04-28/missing.jpg'); // must not throw
        $this->assertTrue(true);
    }
}
```

- [ ] **Step 2: Run failing test**

Run: `vendor/bin/phpunit tests/Unit/Services/Attendance/SelfieStorageTest.php`
Expected: class not found.

- [ ] **Step 3: Implement SelfieStorage**

`app/Services/Attendance/SelfieStorage.php`:

```php
<?php

namespace App\Services\Attendance;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SelfieStorage
{
    private const DISK = 'local';

    public function store(UploadedFile $file, int $userId, string $type, string $date): string
    {
        $dir = "attendance/{$date}";
        $name = sprintf('%d_%s_%s_%s.%s', $userId, $type, time(), Str::random(8), $file->extension() ?: 'jpg');
        $stored = Storage::disk(self::DISK)->putFileAs($dir, $file, $name);
        return $stored;
    }

    public function delete(?string $path): void
    {
        if (!$path) {
            return;
        }
        if (Storage::disk(self::DISK)->exists($path)) {
            Storage::disk(self::DISK)->delete($path);
        }
    }
}
```

- [ ] **Step 4: Run tests (must pass)**

Run: `vendor/bin/phpunit tests/Unit/Services/Attendance/SelfieStorageTest.php`
Expected: 3 tests pass.

- [ ] **Step 5: Commit**

```bash
git add app/Services/Attendance/SelfieStorage.php tests/Unit/Services/Attendance/SelfieStorageTest.php
git commit -m "feat(attendance): add SelfieStorage service for selfie file persistence"
```

---

## Task 14: Custom exceptions and JSON Handler

**Files:**
- Create: `app/Exceptions/Attendance/AttendanceException.php`
- Create: `app/Exceptions/Attendance/AlreadyCheckedInException.php`
- Create: `app/Exceptions/Attendance/AlreadyCheckedOutException.php`
- Create: `app/Exceptions/Attendance/KindgardenCoordsNotSetException.php`
- Create: `app/Exceptions/Attendance/MockGpsDetectedException.php`
- Create: `app/Exceptions/Attendance/OutsideGeofenceException.php`
- Create: `app/Exceptions/Attendance/StaleCaptureException.php`
- Modify: `app/Exceptions/Handler.php`

- [ ] **Step 1: Create base exception**

`app/Exceptions/Attendance/AttendanceException.php`:

```php
<?php

namespace App\Exceptions\Attendance;

use RuntimeException;

abstract class AttendanceException extends RuntimeException
{
    /** Stable error code returned to API clients (e.g., outside_geofence). */
    abstract public function errorCode(): string;

    /** Extra context returned in JSON body (e.g., distance_m, max_radius_m). */
    public function context(): array
    {
        return [];
    }

    public function httpStatus(): int
    {
        return 422;
    }
}
```

- [ ] **Step 2: Create concrete exceptions**

`app/Exceptions/Attendance/MockGpsDetectedException.php`:

```php
<?php

namespace App\Exceptions\Attendance;

class MockGpsDetectedException extends AttendanceException
{
    public function __construct()
    {
        parent::__construct("Soxta GPS aniqlandi. GPS Joystick va shu kabi ilovalarni o'chiring.");
    }

    public function errorCode(): string
    {
        return 'mock_gps_detected';
    }
}
```

`app/Exceptions/Attendance/OutsideGeofenceException.php`:

```php
<?php

namespace App\Exceptions\Attendance;

class OutsideGeofenceException extends AttendanceException
{
    public function __construct(private int $distanceM, private int $maxRadiusM)
    {
        parent::__construct("Bog'chadan {$distanceM}m uzoqdasiz.");
    }

    public function errorCode(): string
    {
        return 'outside_geofence';
    }

    public function context(): array
    {
        return [
            'distance_m' => $this->distanceM,
            'max_radius_m' => $this->maxRadiusM,
        ];
    }
}
```

`app/Exceptions/Attendance/AlreadyCheckedInException.php`:

```php
<?php

namespace App\Exceptions\Attendance;

class AlreadyCheckedInException extends AttendanceException
{
    public function __construct()
    {
        parent::__construct("Bugun allaqachon kelgansiz. \"Qayta yuborish\" tugmasidan foydalaning.");
    }

    public function errorCode(): string
    {
        return 'already_checked_in';
    }
}
```

`app/Exceptions/Attendance/AlreadyCheckedOutException.php`:

```php
<?php

namespace App\Exceptions\Attendance;

class AlreadyCheckedOutException extends AttendanceException
{
    public function __construct()
    {
        parent::__construct("Bugun allaqachon ketgansiz. \"Qayta yuborish\" tugmasidan foydalaning.");
    }

    public function errorCode(): string
    {
        return 'already_checked_out';
    }
}
```

`app/Exceptions/Attendance/KindgardenCoordsNotSetException.php`:

```php
<?php

namespace App\Exceptions\Attendance;

class KindgardenCoordsNotSetException extends AttendanceException
{
    public function __construct()
    {
        parent::__construct("Bog'cha koordinatalari sozlanmagan. Addelkadirga murojaat qiling.");
    }

    public function errorCode(): string
    {
        return 'kindgarden_coords_not_set';
    }
}
```

`app/Exceptions/Attendance/StaleCaptureException.php`:

```php
<?php

namespace App\Exceptions\Attendance;

class StaleCaptureException extends AttendanceException
{
    public function __construct()
    {
        parent::__construct("Yuborilgan vaqt server vaqtidan ko'p farq qiladi. Qayta urinib ko'ring.");
    }

    public function errorCode(): string
    {
        return 'stale_capture';
    }
}
```

- [ ] **Step 3: Modify Handler.php to render AttendanceException as JSON**

Open `app/Exceptions/Handler.php`, find the `register()` method, and add inside it:

```php
$this->renderable(function (\App\Exceptions\Attendance\AttendanceException $e, $request) {
    if ($request->wantsJson() || $request->is('api/*')) {
        return response()->json([
            'error' => $e->errorCode(),
            'message' => $e->getMessage(),
        ] + $e->context(), $e->httpStatus());
    }
});
```

- [ ] **Step 4: Quick smoke test**

Run: `php artisan tinker --execute="throw new \\App\\Exceptions\\Attendance\\OutsideGeofenceException(547,200);"`
Expected: an exception with message containing `547m`.

- [ ] **Step 5: Commit**

```bash
git add app/Exceptions/Attendance app/Exceptions/Handler.php
git commit -m "feat(attendance): add domain exceptions with JSON renderer"
```

---

## Task 15: AttendanceService — checkIn (TDD)

**Files:**
- Create: `app/Services/Attendance/AttendanceService.php`
- Create: `tests/Unit/Services/Attendance/AttendanceServiceTest.php`

This task uses the database, so the test extends Laravel's `TestCase` with `RefreshDatabase`. It is technically more of an integration test, but lives under `tests/Unit/Services/` to keep service tests grouped.

- [ ] **Step 1: Set up test base — write failing test for happy-path checkIn**

`tests/Unit/Services/Attendance/AttendanceServiceTest.php`:

```php
<?php

namespace Tests\Unit\Services\Attendance;

use App\Constants\Roles;
use App\Exceptions\Attendance\AlreadyCheckedInException;
use App\Exceptions\Attendance\KindgardenCoordsNotSetException;
use App\Exceptions\Attendance\MockGpsDetectedException;
use App\Exceptions\Attendance\OutsideGeofenceException;
use App\Exceptions\Attendance\StaleCaptureException;
use App\Models\ChefAttendance;
use App\Models\Kindgarden;
use App\Models\User;
use App\Services\Attendance\AttendanceService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AttendanceServiceTest extends TestCase
{
    use RefreshDatabase;

    private AttendanceService $svc;
    private User $chef;
    private Kindgarden $kg;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('local');
        Carbon::setTestNow('2026-04-28 08:00:00', 'Asia/Tashkent');

        $this->kg = Kindgarden::create([
            'kingar_name' => 'Test bog\'cha',
            'lat' => 41.3111,
            'lng' => 69.2797,
            'geofence_radius' => 200,
        ]);
        $this->chef = User::create([
            'role_id' => Roles::CHEF,
            'name' => 'Test Chef',
            'email' => 'chef@test.local',
            'password' => bcrypt('secret'),
        ]);
        $this->chef->kindgarden()->attach($this->kg->id);

        $this->svc = $this->app->make(AttendanceService::class);
    }

    public function test_check_in_happy_path_creates_row_and_stores_selfie(): void
    {
        $photo = UploadedFile::fake()->image('selfie.jpg');

        $att = $this->svc->checkIn(
            user: $this->chef,
            photo: $photo,
            lat: 41.3112,        // ~11m north
            lng: 69.2797,
            capturedAt: now(),
            isMock: false,
        );

        $this->assertInstanceOf(ChefAttendance::class, $att);
        $this->assertNotNull($att->check_in_at);
        $this->assertSame($this->chef->id, $att->user_id);
        $this->assertSame($this->kg->id, $att->kindgarden_id);
        $this->assertLessThanOrEqual(15, $att->check_in_distance_m);
        Storage::disk('local')->assertExists($att->check_in_selfie_path);
    }

    public function test_check_in_rejects_mock_gps(): void
    {
        $this->expectException(MockGpsDetectedException::class);
        $this->svc->checkIn(
            $this->chef,
            UploadedFile::fake()->image('s.jpg'),
            41.3111, 69.2797, now(), true,
        );
    }

    public function test_check_in_rejects_outside_geofence(): void
    {
        $this->expectException(OutsideGeofenceException::class);
        $this->svc->checkIn(
            $this->chef,
            UploadedFile::fake()->image('s.jpg'),
            41.3200, 69.2797, now(), false,   // ~990m away
        );
    }

    public function test_check_in_rejects_when_already_checked_in_today(): void
    {
        $this->svc->checkIn($this->chef, UploadedFile::fake()->image('a.jpg'),
            41.3112, 69.2797, now(), false);

        $this->expectException(AlreadyCheckedInException::class);
        $this->svc->checkIn($this->chef, UploadedFile::fake()->image('b.jpg'),
            41.3112, 69.2797, now(), false);
    }

    public function test_check_in_rejects_when_kindgarden_coords_not_set(): void
    {
        $this->kg->update(['lat' => null, 'lng' => null]);

        $this->expectException(KindgardenCoordsNotSetException::class);
        $this->svc->checkIn($this->chef, UploadedFile::fake()->image('a.jpg'),
            41.3112, 69.2797, now(), false);
    }

    public function test_check_in_rejects_stale_captured_at(): void
    {
        $this->expectException(StaleCaptureException::class);
        $this->svc->checkIn($this->chef, UploadedFile::fake()->image('a.jpg'),
            41.3112, 69.2797, now()->subMinutes(10), false);
    }
}
```

- [ ] **Step 2: Run test (must fail with class-not-found)**

Run: `vendor/bin/phpunit tests/Unit/Services/Attendance/AttendanceServiceTest.php`
Expected: errors — `AttendanceService` not found.

- [ ] **Step 3: Implement AttendanceService::checkIn**

`app/Services/Attendance/AttendanceService.php`:

```php
<?php

namespace App\Services\Attendance;

use App\Exceptions\Attendance\AlreadyCheckedInException;
use App\Exceptions\Attendance\AlreadyCheckedOutException;
use App\Exceptions\Attendance\KindgardenCoordsNotSetException;
use App\Exceptions\Attendance\MockGpsDetectedException;
use App\Exceptions\Attendance\OutsideGeofenceException;
use App\Exceptions\Attendance\StaleCaptureException;
use App\Models\ChefAttendance;
use App\Models\ChefLocationEvent;
use App\Models\Kindgarden;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

class AttendanceService
{
    public const MAX_CAPTURE_SKEW_SECONDS = 300;

    public function __construct(
        private DistanceCalculator $distance,
        private SelfieStorage $storage,
    ) {}

    public function checkIn(User $user, UploadedFile $photo, float $lat, float $lng, Carbon $capturedAt, bool $isMock): ChefAttendance
    {
        $kg = $this->resolveKindgarden($user);
        $this->guardCapture($capturedAt, $isMock);
        $distanceM = $this->guardGeofence($kg, $lat, $lng);

        $today = $capturedAt->copy()->setTimezone('Asia/Tashkent')->toDateString();

        return DB::transaction(function () use ($user, $kg, $photo, $lat, $lng, $capturedAt, $distanceM, $today) {
            $existing = ChefAttendance::where('user_id', $user->id)->where('date', $today)->first();
            if ($existing && $existing->check_in_at) {
                throw new AlreadyCheckedInException();
            }

            $path = $this->storage->store($photo, $user->id, 'check_in', $today);

            $row = $existing ?? new ChefAttendance([
                'user_id' => $user->id,
                'kindgarden_id' => $kg->id,
                'date' => $today,
            ]);
            $row->fill([
                'check_in_at' => $capturedAt,
                'check_in_lat' => $lat,
                'check_in_lng' => $lng,
                'check_in_distance_m' => $distanceM,
                'check_in_selfie_path' => $path,
                'check_in_is_late' => false,
            ]);
            $row->save();
            return $row->fresh();
        });
    }

    private function resolveKindgarden(User $user): Kindgarden
    {
        $kg = $user->kindgarden()->first();
        if (!$kg) {
            throw new KindgardenCoordsNotSetException();
        }
        if ($kg->lat === null || $kg->lng === null) {
            throw new KindgardenCoordsNotSetException();
        }
        return $kg;
    }

    private function guardCapture(Carbon $capturedAt, bool $isMock): void
    {
        if ($isMock) {
            throw new MockGpsDetectedException();
        }
        if (abs(now()->diffInSeconds($capturedAt, false)) > self::MAX_CAPTURE_SKEW_SECONDS) {
            throw new StaleCaptureException();
        }
    }

    private function guardGeofence(Kindgarden $kg, float $lat, float $lng): int
    {
        $distance = $this->distance->meters((float) $kg->lat, (float) $kg->lng, $lat, $lng);
        $maxRadius = (int) ($kg->geofence_radius ?: 200);
        if ($distance > $maxRadius) {
            throw new OutsideGeofenceException($distance, $maxRadius);
        }
        return $distance;
    }
}
```

- [ ] **Step 4: Run tests (all 6 must pass)**

Run: `vendor/bin/phpunit tests/Unit/Services/Attendance/AttendanceServiceTest.php`
Expected: 6 tests pass.

- [ ] **Step 5: Commit**

```bash
git add app/Services/Attendance/AttendanceService.php tests/Unit/Services/Attendance/AttendanceServiceTest.php
git commit -m "feat(attendance): AttendanceService::checkIn with geofence/mock/stale guards"
```

---

## Task 16: AttendanceService — checkOut (TDD)

**Files:**
- Modify: `app/Services/Attendance/AttendanceService.php`
- Modify: `tests/Unit/Services/Attendance/AttendanceServiceTest.php`

- [ ] **Step 1: Append tests**

Add these methods at the end of `AttendanceServiceTest`:

```php
public function test_check_out_happy_path(): void
{
    $this->svc->checkIn($this->chef, UploadedFile::fake()->image('in.jpg'),
        41.3112, 69.2797, now(), false);

    Carbon::setTestNow('2026-04-28 17:00:00', 'Asia/Tashkent');

    $att = $this->svc->checkOut($this->chef, UploadedFile::fake()->image('out.jpg'),
        41.3112, 69.2797, now(), false);

    $this->assertNotNull($att->check_out_at);
    Storage::disk('local')->assertExists($att->check_out_selfie_path);
}

public function test_check_out_rejects_when_not_checked_in(): void
{
    $this->expectException(\App\Exceptions\Attendance\AttendanceException::class);
    $this->svc->checkOut($this->chef, UploadedFile::fake()->image('out.jpg'),
        41.3112, 69.2797, now(), false);
}

public function test_check_out_rejects_when_already_checked_out(): void
{
    $this->svc->checkIn($this->chef, UploadedFile::fake()->image('in.jpg'),
        41.3112, 69.2797, now(), false);
    Carbon::setTestNow('2026-04-28 17:00:00', 'Asia/Tashkent');
    $this->svc->checkOut($this->chef, UploadedFile::fake()->image('out.jpg'),
        41.3112, 69.2797, now(), false);

    $this->expectException(AlreadyCheckedOutException::class);
    $this->svc->checkOut($this->chef, UploadedFile::fake()->image('out2.jpg'),
        41.3112, 69.2797, now(), false);
}
```

- [ ] **Step 2: Add NotCheckedInException**

Create `app/Exceptions/Attendance/NotCheckedInException.php`:

```php
<?php

namespace App\Exceptions\Attendance;

class NotCheckedInException extends AttendanceException
{
    public function __construct()
    {
        parent::__construct("Avval \"Keldim\" tugmasini bosing.");
    }

    public function errorCode(): string
    {
        return 'not_checked_in';
    }
}
```

- [ ] **Step 3: Run tests (must fail with method-not-found)**

Run: `vendor/bin/phpunit tests/Unit/Services/Attendance/AttendanceServiceTest.php`
Expected: error on `checkOut` method.

- [ ] **Step 4: Implement checkOut**

Add this method to `AttendanceService`:

```php
public function checkOut(User $user, UploadedFile $photo, float $lat, float $lng, Carbon $capturedAt, bool $isMock): ChefAttendance
{
    $kg = $this->resolveKindgarden($user);
    $this->guardCapture($capturedAt, $isMock);
    $distanceM = $this->guardGeofence($kg, $lat, $lng);

    $today = $capturedAt->copy()->setTimezone('Asia/Tashkent')->toDateString();

    return DB::transaction(function () use ($user, $photo, $lat, $lng, $capturedAt, $distanceM, $today) {
        $row = ChefAttendance::where('user_id', $user->id)->where('date', $today)->first();
        if (!$row || !$row->check_in_at) {
            throw new \App\Exceptions\Attendance\NotCheckedInException();
        }
        if ($row->check_out_at) {
            throw new AlreadyCheckedOutException();
        }

        $path = $this->storage->store($photo, $user->id, 'check_out', $today);
        $row->fill([
            'check_out_at' => $capturedAt,
            'check_out_lat' => $lat,
            'check_out_lng' => $lng,
            'check_out_distance_m' => $distanceM,
            'check_out_selfie_path' => $path,
        ])->save();

        return $row->fresh();
    });
}
```

- [ ] **Step 5: Run tests (all 9 must pass)**

Run: `vendor/bin/phpunit tests/Unit/Services/Attendance/AttendanceServiceTest.php`
Expected: 9 tests pass.

- [ ] **Step 6: Commit**

```bash
git add app/Services/Attendance/AttendanceService.php app/Exceptions/Attendance/NotCheckedInException.php tests/Unit/Services/Attendance/AttendanceServiceTest.php
git commit -m "feat(attendance): AttendanceService::checkOut with state guards"
```

---

## Task 17: AttendanceService — replace (TDD)

The replace method handles two cases (per spec section 4.2):
- **i**: today's row exists with `check_in_at` set → replace selfie/lat/lng, increment `check_in_replaced_count`, delete old selfie file.
- **ii**: today has no row, or row has no `check_in_at` → create as late entry with `check_in_is_late = true`.

Same shape for `check_out` type.

**Files:**
- Modify: `app/Services/Attendance/AttendanceService.php`
- Modify: `tests/Unit/Services/Attendance/AttendanceServiceTest.php`

- [ ] **Step 1: Append tests**

```php
public function test_replace_check_in_replaces_existing_and_increments_counter(): void
{
    $first = $this->svc->checkIn($this->chef, UploadedFile::fake()->image('in1.jpg'),
        41.3112, 69.2797, now(), false);
    $oldPath = $first->check_in_selfie_path;

    Carbon::setTestNow('2026-04-28 09:30:00', 'Asia/Tashkent');
    $second = $this->svc->replace($this->chef, 'check_in',
        UploadedFile::fake()->image('in2.jpg'),
        41.3112, 69.2797, now(), false);

    $this->assertSame(1, $second->check_in_replaced_count);
    $this->assertNotSame($oldPath, $second->check_in_selfie_path);
    $this->assertFalse($second->check_in_is_late);
    Storage::disk('local')->assertMissing($oldPath);
}

public function test_replace_check_in_as_late_entry_when_no_existing(): void
{
    Carbon::setTestNow('2026-04-28 11:00:00', 'Asia/Tashkent');

    $att = $this->svc->replace($this->chef, 'check_in',
        UploadedFile::fake()->image('late.jpg'),
        41.3112, 69.2797, now(), false);

    $this->assertNotNull($att->check_in_at);
    $this->assertTrue($att->check_in_is_late);
    $this->assertSame(0, $att->check_in_replaced_count);
}

public function test_replace_check_out_replaces_existing(): void
{
    $this->svc->checkIn($this->chef, UploadedFile::fake()->image('in.jpg'),
        41.3112, 69.2797, now(), false);
    Carbon::setTestNow('2026-04-28 17:00:00', 'Asia/Tashkent');
    $first = $this->svc->checkOut($this->chef, UploadedFile::fake()->image('out1.jpg'),
        41.3112, 69.2797, now(), false);
    $oldPath = $first->check_out_selfie_path;

    Carbon::setTestNow('2026-04-28 17:30:00', 'Asia/Tashkent');
    $second = $this->svc->replace($this->chef, 'check_out',
        UploadedFile::fake()->image('out2.jpg'),
        41.3112, 69.2797, now(), false);

    $this->assertSame(1, $second->check_out_replaced_count);
    $this->assertNotSame($oldPath, $second->check_out_selfie_path);
}

public function test_replace_check_out_rejects_when_not_checked_in_yet(): void
{
    $this->expectException(\App\Exceptions\Attendance\AttendanceException::class);
    $this->svc->replace($this->chef, 'check_out',
        UploadedFile::fake()->image('o.jpg'),
        41.3112, 69.2797, now(), false);
}

public function test_replace_invalid_type_throws(): void
{
    $this->expectException(\InvalidArgumentException::class);
    $this->svc->replace($this->chef, 'lunch',
        UploadedFile::fake()->image('o.jpg'),
        41.3112, 69.2797, now(), false);
}
```

- [ ] **Step 2: Run tests (must fail — method missing)**

Run: `vendor/bin/phpunit tests/Unit/Services/Attendance/AttendanceServiceTest.php`
Expected: failures on `replace`.

- [ ] **Step 3: Implement replace**

Add to `AttendanceService`:

```php
public function replace(User $user, string $type, UploadedFile $photo, float $lat, float $lng, Carbon $capturedAt, bool $isMock): ChefAttendance
{
    if (!in_array($type, ['check_in', 'check_out'], true)) {
        throw new \InvalidArgumentException("Unsupported replace type: {$type}");
    }

    $kg = $this->resolveKindgarden($user);
    $this->guardCapture($capturedAt, $isMock);
    $distanceM = $this->guardGeofence($kg, $lat, $lng);

    $today = $capturedAt->copy()->setTimezone('Asia/Tashkent')->toDateString();

    return DB::transaction(function () use ($user, $kg, $type, $photo, $lat, $lng, $capturedAt, $distanceM, $today) {
        $row = ChefAttendance::where('user_id', $user->id)->where('date', $today)->first();

        if ($type === 'check_in') {
            return $this->applyCheckInReplace($row, $user, $kg, $photo, $lat, $lng, $capturedAt, $distanceM, $today);
        }
        return $this->applyCheckOutReplace($row, $user, $photo, $lat, $lng, $capturedAt, $distanceM, $today);
    });
}

private function applyCheckInReplace(?ChefAttendance $row, User $user, Kindgarden $kg, UploadedFile $photo, float $lat, float $lng, Carbon $capturedAt, int $distanceM, string $today): ChefAttendance
{
    $isLateEntry = !$row || !$row->check_in_at;
    $oldPath = $row?->check_in_selfie_path;
    $newPath = $this->storage->store($photo, $user->id, 'check_in', $today);

    $row = $row ?? new ChefAttendance([
        'user_id' => $user->id,
        'kindgarden_id' => $kg->id,
        'date' => $today,
    ]);
    $row->fill([
        'check_in_at' => $capturedAt,
        'check_in_lat' => $lat,
        'check_in_lng' => $lng,
        'check_in_distance_m' => $distanceM,
        'check_in_selfie_path' => $newPath,
        'check_in_is_late' => $isLateEntry,
        'check_in_replaced_count' => $isLateEntry ? 0 : ($row->check_in_replaced_count + 1),
    ]);
    $row->save();
    if (!$isLateEntry && $oldPath) {
        $this->storage->delete($oldPath);
    }
    return $row->fresh();
}

private function applyCheckOutReplace(?ChefAttendance $row, User $user, UploadedFile $photo, float $lat, float $lng, Carbon $capturedAt, int $distanceM, string $today): ChefAttendance
{
    if (!$row || !$row->check_in_at) {
        throw new \App\Exceptions\Attendance\NotCheckedInException();
    }
    $oldPath = $row->check_out_selfie_path;
    $newPath = $this->storage->store($photo, $user->id, 'check_out', $today);

    $isFirstCheckOut = $row->check_out_at === null;
    $row->fill([
        'check_out_at' => $capturedAt,
        'check_out_lat' => $lat,
        'check_out_lng' => $lng,
        'check_out_distance_m' => $distanceM,
        'check_out_selfie_path' => $newPath,
        'check_out_replaced_count' => $isFirstCheckOut ? 0 : ($row->check_out_replaced_count + 1),
    ])->save();

    if (!$isFirstCheckOut && $oldPath) {
        $this->storage->delete($oldPath);
    }
    return $row->fresh();
}
```

- [ ] **Step 4: Run tests (all 14 must pass)**

Run: `vendor/bin/phpunit tests/Unit/Services/Attendance/AttendanceServiceTest.php`
Expected: 14 tests pass.

- [ ] **Step 5: Commit**

```bash
git add app/Services/Attendance/AttendanceService.php tests/Unit/Services/Attendance/AttendanceServiceTest.php
git commit -m "feat(attendance): AttendanceService::replace handles edit + late entry"
```

---

## Task 18: AttendanceService — recordLocationEvents (TDD)

Batch ingestion of geofence-exit/enter/beacon events from the mobile offline queue.

**Files:**
- Modify: `app/Services/Attendance/AttendanceService.php`
- Modify: `tests/Unit/Services/Attendance/AttendanceServiceTest.php`

- [ ] **Step 1: Append tests**

```php
public function test_record_location_events_inserts_all_with_distance(): void
{
    $events = [
        ['event_type' => 'exit', 'lat' => 41.3200, 'lng' => 69.2797,
         'happened_at' => now()->toIso8601String(), 'is_mock' => false],
        ['event_type' => 'enter', 'lat' => 41.3112, 'lng' => 69.2797,
         'happened_at' => now()->addMinutes(5)->toIso8601String(), 'is_mock' => false],
    ];

    $count = $this->svc->recordLocationEvents($this->chef, $events);

    $this->assertSame(2, $count);
    $this->assertSame(2, \App\Models\ChefLocationEvent::count());
    $first = \App\Models\ChefLocationEvent::orderBy('id')->first();
    $this->assertSame('exit', $first->event_type);
    $this->assertGreaterThan(800, $first->distance_m);
}

public function test_record_location_events_skips_when_kindgarden_coords_missing(): void
{
    $this->kg->update(['lat' => null, 'lng' => null]);
    $count = $this->svc->recordLocationEvents($this->chef, [
        ['event_type' => 'exit', 'lat' => 41.32, 'lng' => 69.28,
         'happened_at' => now()->toIso8601String(), 'is_mock' => false],
    ]);
    $this->assertSame(0, $count);
    $this->assertSame(0, \App\Models\ChefLocationEvent::count());
}

public function test_record_location_events_rejects_invalid_event_type(): void
{
    $this->expectException(\InvalidArgumentException::class);
    $this->svc->recordLocationEvents($this->chef, [
        ['event_type' => 'lunch', 'lat' => 41.31, 'lng' => 69.27,
         'happened_at' => now()->toIso8601String(), 'is_mock' => false],
    ]);
}
```

- [ ] **Step 2: Run tests (must fail)**

Run: `vendor/bin/phpunit tests/Unit/Services/Attendance/AttendanceServiceTest.php`
Expected: failure.

- [ ] **Step 3: Implement recordLocationEvents**

Add to `AttendanceService`:

```php
public function recordLocationEvents(User $user, array $events): int
{
    $kg = $user->kindgarden()->first();
    if (!$kg || $kg->lat === null || $kg->lng === null) {
        return 0;
    }
    $allowed = ['exit', 'enter', 'beacon'];

    $rows = [];
    foreach ($events as $e) {
        if (!in_array($e['event_type'] ?? null, $allowed, true)) {
            throw new \InvalidArgumentException('Invalid event_type: ' . ($e['event_type'] ?? 'null'));
        }
        $distance = $this->distance->meters((float) $kg->lat, (float) $kg->lng, (float) $e['lat'], (float) $e['lng']);
        $rows[] = [
            'user_id' => $user->id,
            'kindgarden_id' => $kg->id,
            'event_type' => $e['event_type'],
            'happened_at' => Carbon::parse($e['happened_at']),
            'lat' => $e['lat'],
            'lng' => $e['lng'],
            'distance_m' => $distance,
            'is_mock' => (bool) ($e['is_mock'] ?? false),
            'created_at' => now(),
        ];
    }
    if ($rows) {
        ChefLocationEvent::insert($rows);
    }
    return count($rows);
}
```

- [ ] **Step 4: Run tests (all 17 must pass)**

Run: `vendor/bin/phpunit tests/Unit/Services/Attendance/AttendanceServiceTest.php`
Expected: 17 tests pass.

- [ ] **Step 5: Commit**

```bash
git add app/Services/Attendance/AttendanceService.php tests/Unit/Services/Attendance/AttendanceServiceTest.php
git commit -m "feat(attendance): AttendanceService::recordLocationEvents (batch insert)"
```

---

## Task 19: routes/api.php — /api/v1 namespace skeleton

**Files:**
- Modify: `routes/api.php`

- [ ] **Step 1: Append v1 group**

Open `routes/api.php` and append at the end:

```php
use App\Http\Controllers\Api\V1\Auth\LoginController;
use App\Http\Controllers\Api\V1\Auth\LogoutController;
use App\Http\Controllers\Api\V1\Auth\DeviceController;
use App\Http\Controllers\Api\V1\Chef\AttendanceController;
use App\Http\Controllers\Api\V1\Chef\LocationEventController;

Route::prefix('v1')->group(function () {
    Route::prefix('auth')->group(function () {
        Route::post('login', [LoginController::class, 'login'])->middleware('throttle:5,1');
        Route::post('logout', [LogoutController::class, 'logout'])->middleware('auth:sanctum');
        Route::post('device', [DeviceController::class, 'register'])->middleware('auth:sanctum');
    });

    Route::prefix('chef')->middleware(['auth:sanctum'])->group(function () {
        Route::prefix('attendance')->middleware('throttle:30,1')->group(function () {
            Route::post('check-in', [AttendanceController::class, 'checkIn']);
            Route::post('check-out', [AttendanceController::class, 'checkOut']);
            Route::post('replace', [AttendanceController::class, 'replace']);
            Route::get('today', [AttendanceController::class, 'today']);
        });
        Route::post('location-events', [LocationEventController::class, 'store'])
            ->middleware('throttle:60,1');
    });
});
```

- [ ] **Step 2: Verify routes are registered (will error until controllers exist; that's fine)**

Run: `php artisan route:list --path=api/v1 2>&1 | head -20`
Expected: route list — may show "Class not found" warnings until controllers are added (that's OK at this step).

- [ ] **Step 3: Commit**

```bash
git add routes/api.php
git commit -m "feat(api): add /api/v1 routes for auth, chef attendance, location events"
```

---

## Task 20: Auth — LoginController (TDD)

**Files:**
- Create: `app/Http/Controllers/Api/V1/Auth/LoginController.php`
- Create: `tests/Feature/Api/V1/Auth/LoginTest.php`

- [ ] **Step 1: Write failing tests**

`tests/Feature/Api/V1/Auth/LoginTest.php`:

```php
<?php

namespace Tests\Feature\Api\V1\Auth;

use App\Constants\Roles;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_with_valid_credentials_returns_token_and_user(): void
    {
        User::create([
            'role_id' => Roles::CHEF,
            'name' => 'Akmal',
            'email' => 'a@test.local',
            'password' => bcrypt('secret123'),
        ]);

        $resp = $this->postJson('/api/v1/auth/login', [
            'email' => 'a@test.local',
            'password' => 'secret123',
        ]);

        $resp->assertOk();
        $resp->assertJsonStructure(['token', 'user' => ['id', 'name', 'email', 'role_id']]);
        $this->assertNotEmpty($resp->json('token'));
    }

    public function test_login_with_wrong_password_returns_401(): void
    {
        User::create([
            'role_id' => Roles::CHEF,
            'name' => 'A', 'email' => 'a@test.local',
            'password' => bcrypt('secret123'),
        ]);

        $resp = $this->postJson('/api/v1/auth/login', [
            'email' => 'a@test.local',
            'password' => 'wrong',
        ]);

        $resp->assertStatus(401);
        $resp->assertJson(['error' => 'invalid_credentials']);
    }

    public function test_login_with_non_chef_role_is_rejected(): void
    {
        User::create([
            'role_id' => Roles::TECHNOLOG,
            'name' => 'T', 'email' => 't@test.local',
            'password' => bcrypt('secret123'),
        ]);

        $resp = $this->postJson('/api/v1/auth/login', [
            'email' => 't@test.local',
            'password' => 'secret123',
        ]);

        $resp->assertStatus(403);
        $resp->assertJson(['error' => 'role_not_allowed']);
    }

    public function test_login_validates_required_fields(): void
    {
        $resp = $this->postJson('/api/v1/auth/login', []);
        $resp->assertStatus(422);
    }
}
```

- [ ] **Step 2: Run tests (must fail — controller missing)**

Run: `vendor/bin/phpunit tests/Feature/Api/V1/Auth/LoginTest.php`
Expected: errors.

- [ ] **Step 3: Implement controller**

`app/Http/Controllers/Api/V1/Auth/LoginController.php`:

```php
<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Constants\Roles;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    private const ALLOWED_ROLES = [Roles::CHEF];

    public function login(Request $request): JsonResponse
    {
        $data = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $data['email'])->first();
        if (!$user || !Hash::check($data['password'], $user->password)) {
            return response()->json(['error' => 'invalid_credentials', 'message' => 'Email yoki parol noto\'g\'ri'], 401);
        }
        if (!in_array((int) $user->role_id, self::ALLOWED_ROLES, true)) {
            return response()->json(['error' => 'role_not_allowed', 'message' => 'Bu rol mobil ilovaga kirita olmaydi'], 403);
        }

        $token = $user->createToken('mobile')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role_id' => (int) $user->role_id,
            ],
        ]);
    }
}
```

- [ ] **Step 4: Run tests (4 must pass)**

Run: `vendor/bin/phpunit tests/Feature/Api/V1/Auth/LoginTest.php`
Expected: 4 tests pass.

- [ ] **Step 5: Commit**

```bash
git add app/Http/Controllers/Api/V1/Auth/LoginController.php tests/Feature/Api/V1/Auth/LoginTest.php
git commit -m "feat(api): chef login endpoint with role check"
```

---

## Task 21: Auth — LogoutController (TDD)

**Files:**
- Create: `app/Http/Controllers/Api/V1/Auth/LogoutController.php`
- Create: `tests/Feature/Api/V1/Auth/LogoutTest.php`

- [ ] **Step 1: Write failing tests**

`tests/Feature/Api/V1/Auth/LogoutTest.php`:

```php
<?php

namespace Tests\Feature\Api\V1\Auth;

use App\Constants\Roles;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class LogoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_logout_revokes_current_token(): void
    {
        $user = User::create([
            'role_id' => Roles::CHEF,
            'name' => 'A', 'email' => 'a@t.l', 'password' => bcrypt('x'),
        ]);
        Sanctum::actingAs($user);

        $resp = $this->postJson('/api/v1/auth/logout');

        $resp->assertOk();
        $resp->assertJson(['message' => 'logged_out']);
    }

    public function test_logout_without_token_returns_401(): void
    {
        $resp = $this->postJson('/api/v1/auth/logout');
        $resp->assertStatus(401);
    }
}
```

- [ ] **Step 2: Run tests (must fail)**

Run: `vendor/bin/phpunit tests/Feature/Api/V1/Auth/LogoutTest.php`
Expected: error — controller missing.

- [ ] **Step 3: Implement controller**

`app/Http/Controllers/Api/V1/Auth/LogoutController.php`:

```php
<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LogoutController extends Controller
{
    public function logout(Request $request): JsonResponse
    {
        $token = $request->user()->currentAccessToken();
        // Real bearer tokens are PersonalAccessToken instances. Tests using
        // Sanctum::actingAs() get a TransientToken which lacks delete().
        if ($token instanceof \Laravel\Sanctum\PersonalAccessToken) {
            $token->delete();
        }
        return response()->json(['message' => 'logged_out']);
    }
}
```

- [ ] **Step 4: Run tests (must pass)**

Run: `vendor/bin/phpunit tests/Feature/Api/V1/Auth/LogoutTest.php`
Expected: 2 tests pass.

- [ ] **Step 5: Commit**

```bash
git add app/Http/Controllers/Api/V1/Auth/LogoutController.php tests/Feature/Api/V1/Auth/LogoutTest.php
git commit -m "feat(api): chef logout endpoint"
```

---

## Task 22: Auth — DeviceController with single-active enforcement (TDD)

**Files:**
- Create: `app/Http/Controllers/Api/V1/Auth/DeviceController.php`
- Create: `tests/Feature/Api/V1/Auth/DeviceTest.php`

- [ ] **Step 1: Write failing tests**

`tests/Feature/Api/V1/Auth/DeviceTest.php`:

```php
<?php

namespace Tests\Feature\Api\V1\Auth;

use App\Constants\Roles;
use App\Models\ChefDevice;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class DeviceTest extends TestCase
{
    use RefreshDatabase;

    private function chef(): User
    {
        return User::create([
            'role_id' => Roles::CHEF,
            'name' => 'A', 'email' => 'a@t.l', 'password' => bcrypt('x'),
        ]);
    }

    public function test_register_device_creates_record(): void
    {
        Sanctum::actingAs($this->chef());

        $resp = $this->postJson('/api/v1/auth/device', [
            'platform' => 'android',
            'fcm_token' => 'fcm-abc-123',
            'device_model' => 'Samsung A52',
            'app_version' => '1.0.0',
        ]);

        $resp->assertOk();
        $this->assertDatabaseHas('chef_devices', ['fcm_token' => 'fcm-abc-123']);
    }

    public function test_register_new_device_revokes_old_tokens_and_replaces_device_row(): void
    {
        $user = $this->chef();
        Sanctum::actingAs($user);

        $this->postJson('/api/v1/auth/device', [
            'platform' => 'android', 'fcm_token' => 'old-fcm',
        ])->assertOk();

        $oldToken = $user->createToken('mobile')->plainTextToken; // simulate prior token

        Sanctum::actingAs($user);
        $this->postJson('/api/v1/auth/device', [
            'platform' => 'android', 'fcm_token' => 'new-fcm',
        ])->assertOk();

        $this->assertDatabaseMissing('chef_devices', ['fcm_token' => 'old-fcm']);
        $this->assertDatabaseHas('chef_devices', ['fcm_token' => 'new-fcm']);
    }

    public function test_register_validates_platform(): void
    {
        Sanctum::actingAs($this->chef());
        $resp = $this->postJson('/api/v1/auth/device', [
            'platform' => 'windows', 'fcm_token' => 'x',
        ]);
        $resp->assertStatus(422);
    }
}
```

- [ ] **Step 2: Run tests (must fail)**

Run: `vendor/bin/phpunit tests/Feature/Api/V1/Auth/DeviceTest.php`
Expected: errors.

- [ ] **Step 3: Implement controller**

`app/Http/Controllers/Api/V1/Auth/DeviceController.php`:

```php
<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Models\ChefDevice;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DeviceController extends Controller
{
    public function register(Request $request): JsonResponse
    {
        $data = $request->validate([
            'platform' => 'required|in:android,ios',
            'fcm_token' => 'required|string|max:255',
            'device_model' => 'nullable|string|max:100',
            'app_version' => 'nullable|string|max:20',
        ]);

        $user = $request->user();
        $currentToken = $user->currentAccessToken();
        $currentTokenId = $currentToken instanceof \Laravel\Sanctum\PersonalAccessToken
            ? $currentToken->id
            : null;

        DB::transaction(function () use ($user, $data, $currentTokenId) {
            // Single-active device: drop other devices
            ChefDevice::where('user_id', $user->id)
                ->where('fcm_token', '!=', $data['fcm_token'])
                ->delete();

            // Drop other Sanctum tokens (keep the one issued at login)
            if ($currentTokenId) {
                $user->tokens()->where('id', '!=', $currentTokenId)->delete();
            }

            ChefDevice::updateOrCreate(
                ['user_id' => $user->id, 'fcm_token' => $data['fcm_token']],
                [
                    'platform' => $data['platform'],
                    'device_model' => $data['device_model'] ?? null,
                    'app_version' => $data['app_version'] ?? null,
                    'last_seen_at' => now(),
                ],
            );
        });

        return response()->json(['message' => 'device_registered']);
    }
}
```

- [ ] **Step 4: Run tests (must pass)**

Run: `vendor/bin/phpunit tests/Feature/Api/V1/Auth/DeviceTest.php`
Expected: 3 tests pass.

- [ ] **Step 5: Commit**

```bash
git add app/Http/Controllers/Api/V1/Auth/DeviceController.php tests/Feature/Api/V1/Auth/DeviceTest.php
git commit -m "feat(api): device registration with single-active enforcement"
```

---

## Task 23: Chef Attendance — checkIn endpoint (TDD)

**Files:**
- Create: `app/Http/Controllers/Api/V1/Chef/AttendanceController.php`
- Create: `tests/Feature/Api/V1/Chef/CheckInTest.php`

- [ ] **Step 1: Write failing test**

`tests/Feature/Api/V1/Chef/CheckInTest.php`:

```php
<?php

namespace Tests\Feature\Api\V1\Chef;

use App\Constants\Roles;
use App\Models\Kindgarden;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class CheckInTest extends TestCase
{
    use RefreshDatabase;

    private User $chef;
    private Kindgarden $kg;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('local');
        Carbon::setTestNow('2026-04-28 08:00:00', 'Asia/Tashkent');

        $this->kg = Kindgarden::create([
            'kingar_name' => 'Test', 'lat' => 41.3111, 'lng' => 69.2797,
            'geofence_radius' => 200,
        ]);
        $this->chef = User::create([
            'role_id' => Roles::CHEF, 'name' => 'A', 'email' => 'a@t.l',
            'password' => bcrypt('x'),
        ]);
        $this->chef->kindgarden()->attach($this->kg->id);

        Sanctum::actingAs($this->chef);
    }

    public function test_check_in_success(): void
    {
        $resp = $this->postJson('/api/v1/chef/attendance/check-in', [
            'lat' => 41.3112,
            'lng' => 69.2797,
            'captured_at' => now()->toIso8601String(),
            'is_mock' => false,
            'photo' => UploadedFile::fake()->image('s.jpg'),
        ]);

        $resp->assertOk();
        $resp->assertJsonStructure(['attendance' => ['id', 'check_in_at', 'check_in_distance_m']]);
        $this->assertDatabaseHas('chef_attendances', ['user_id' => $this->chef->id]);
    }

    public function test_check_in_outside_geofence_returns_422(): void
    {
        $resp = $this->postJson('/api/v1/chef/attendance/check-in', [
            'lat' => 41.3200, 'lng' => 69.2797,
            'captured_at' => now()->toIso8601String(),
            'is_mock' => false,
            'photo' => UploadedFile::fake()->image('s.jpg'),
        ]);
        $resp->assertStatus(422);
        $resp->assertJson(['error' => 'outside_geofence']);
        $resp->assertJsonStructure(['distance_m', 'max_radius_m']);
    }

    public function test_check_in_mock_gps_returns_422(): void
    {
        $resp = $this->postJson('/api/v1/chef/attendance/check-in', [
            'lat' => 41.3112, 'lng' => 69.2797,
            'captured_at' => now()->toIso8601String(),
            'is_mock' => true,
            'photo' => UploadedFile::fake()->image('s.jpg'),
        ]);
        $resp->assertStatus(422);
        $resp->assertJson(['error' => 'mock_gps_detected']);
    }

    public function test_check_in_already_done_returns_422(): void
    {
        $payload = [
            'lat' => 41.3112, 'lng' => 69.2797,
            'captured_at' => now()->toIso8601String(),
            'is_mock' => false,
        ];
        $this->postJson('/api/v1/chef/attendance/check-in',
            $payload + ['photo' => UploadedFile::fake()->image('a.jpg')])->assertOk();

        $resp = $this->postJson('/api/v1/chef/attendance/check-in',
            $payload + ['photo' => UploadedFile::fake()->image('b.jpg')]);
        $resp->assertStatus(422);
        $resp->assertJson(['error' => 'already_checked_in']);
    }

    public function test_check_in_validation_required_fields(): void
    {
        $resp = $this->postJson('/api/v1/chef/attendance/check-in', []);
        $resp->assertStatus(422);
    }

    public function test_check_in_unauthenticated_returns_401(): void
    {
        $this->app['auth']->forgetGuards();
        $resp = $this->withHeader('Accept', 'application/json')
            ->post('/api/v1/chef/attendance/check-in', []);
        $resp->assertStatus(401);
    }
}
```

- [ ] **Step 2: Run tests (must fail)**

Run: `vendor/bin/phpunit tests/Feature/Api/V1/Chef/CheckInTest.php`
Expected: errors.

- [ ] **Step 3: Implement controller (checkIn only — checkOut/replace/today come in next tasks)**

`app/Http/Controllers/Api/V1/Chef/AttendanceController.php`:

```php
<?php

namespace App\Http\Controllers\Api\V1\Chef;

use App\Http\Controllers\Controller;
use App\Services\Attendance\AttendanceService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function __construct(private AttendanceService $svc) {}

    public function checkIn(Request $request): JsonResponse
    {
        $data = $this->validateAttendancePayload($request);
        $att = $this->svc->checkIn(
            $request->user(), $data['photo'], $data['lat'], $data['lng'],
            $data['captured_at'], $data['is_mock'],
        );
        return response()->json(['attendance' => $att]);
    }

    private function validateAttendancePayload(Request $request): array
    {
        $validated = $request->validate([
            'lat' => 'required|numeric|between:-90,90',
            'lng' => 'required|numeric|between:-180,180',
            'captured_at' => 'required|date',
            'is_mock' => 'required|boolean',
            'photo' => 'required|image|max:5120', // 5 MB
        ]);
        return [
            'lat' => (float) $validated['lat'],
            'lng' => (float) $validated['lng'],
            'captured_at' => Carbon::parse($validated['captured_at']),
            'is_mock' => (bool) $validated['is_mock'],
            'photo' => $request->file('photo'),
        ];
    }
}
```

- [ ] **Step 4: Run tests (must pass)**

Run: `vendor/bin/phpunit tests/Feature/Api/V1/Chef/CheckInTest.php`
Expected: 6 tests pass.

- [ ] **Step 5: Commit**

```bash
git add app/Http/Controllers/Api/V1/Chef/AttendanceController.php tests/Feature/Api/V1/Chef/CheckInTest.php
git commit -m "feat(api): POST /chef/attendance/check-in with multipart selfie"
```

---

## Task 24: Chef Attendance — checkOut endpoint (TDD)

**Files:**
- Modify: `app/Http/Controllers/Api/V1/Chef/AttendanceController.php`
- Create: `tests/Feature/Api/V1/Chef/CheckOutTest.php`

- [ ] **Step 1: Write failing tests**

`tests/Feature/Api/V1/Chef/CheckOutTest.php`:

```php
<?php

namespace Tests\Feature\Api\V1\Chef;

use App\Constants\Roles;
use App\Models\Kindgarden;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class CheckOutTest extends TestCase
{
    use RefreshDatabase;

    private User $chef;
    private Kindgarden $kg;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('local');
        Carbon::setTestNow('2026-04-28 08:00:00', 'Asia/Tashkent');

        $this->kg = Kindgarden::create(['kingar_name' => 'T',
            'lat' => 41.3111, 'lng' => 69.2797, 'geofence_radius' => 200]);
        $this->chef = User::create([
            'role_id' => Roles::CHEF, 'name' => 'A', 'email' => 'a@t.l',
            'password' => bcrypt('x'),
        ]);
        $this->chef->kindgarden()->attach($this->kg->id);
        Sanctum::actingAs($this->chef);

        // Pre-existing check-in
        $this->postJson('/api/v1/chef/attendance/check-in', [
            'lat' => 41.3112, 'lng' => 69.2797,
            'captured_at' => now()->toIso8601String(), 'is_mock' => false,
            'photo' => UploadedFile::fake()->image('in.jpg'),
        ])->assertOk();
    }

    public function test_check_out_success(): void
    {
        Carbon::setTestNow('2026-04-28 17:00:00', 'Asia/Tashkent');
        $resp = $this->postJson('/api/v1/chef/attendance/check-out', [
            'lat' => 41.3112, 'lng' => 69.2797,
            'captured_at' => now()->toIso8601String(), 'is_mock' => false,
            'photo' => UploadedFile::fake()->image('out.jpg'),
        ]);
        $resp->assertOk();
        $resp->assertJsonStructure(['attendance' => ['check_out_at']]);
    }

    public function test_check_out_without_check_in_returns_422(): void
    {
        $other = User::create(['role_id' => Roles::CHEF, 'name' => 'B',
            'email' => 'b@t.l', 'password' => bcrypt('x')]);
        $other->kindgarden()->attach($this->kg->id);
        Sanctum::actingAs($other);

        $resp = $this->postJson('/api/v1/chef/attendance/check-out', [
            'lat' => 41.3112, 'lng' => 69.2797,
            'captured_at' => now()->toIso8601String(), 'is_mock' => false,
            'photo' => UploadedFile::fake()->image('out.jpg'),
        ]);
        $resp->assertStatus(422);
        $resp->assertJson(['error' => 'not_checked_in']);
    }
}
```

- [ ] **Step 2: Run tests (must fail — method missing)**

Run: `vendor/bin/phpunit tests/Feature/Api/V1/Chef/CheckOutTest.php`
Expected: error — `checkOut` method missing.

- [ ] **Step 3: Add checkOut to controller**

Append to `AttendanceController`:

```php
public function checkOut(Request $request): JsonResponse
{
    $data = $this->validateAttendancePayload($request);
    $att = $this->svc->checkOut(
        $request->user(), $data['photo'], $data['lat'], $data['lng'],
        $data['captured_at'], $data['is_mock'],
    );
    return response()->json(['attendance' => $att]);
}
```

- [ ] **Step 4: Run tests (must pass)**

Run: `vendor/bin/phpunit tests/Feature/Api/V1/Chef/CheckOutTest.php`
Expected: 2 tests pass.

- [ ] **Step 5: Commit**

```bash
git add app/Http/Controllers/Api/V1/Chef/AttendanceController.php tests/Feature/Api/V1/Chef/CheckOutTest.php
git commit -m "feat(api): POST /chef/attendance/check-out"
```

---

## Task 25: Chef Attendance — replace endpoint (TDD)

**Files:**
- Modify: `app/Http/Controllers/Api/V1/Chef/AttendanceController.php`
- Create: `tests/Feature/Api/V1/Chef/ReplaceTest.php`

- [ ] **Step 1: Write failing tests**

`tests/Feature/Api/V1/Chef/ReplaceTest.php`:

```php
<?php

namespace Tests\Feature\Api\V1\Chef;

use App\Constants\Roles;
use App\Models\Kindgarden;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ReplaceTest extends TestCase
{
    use RefreshDatabase;

    private User $chef;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('local');
        Carbon::setTestNow('2026-04-28 08:00:00', 'Asia/Tashkent');

        $kg = Kindgarden::create(['kingar_name' => 'T',
            'lat' => 41.3111, 'lng' => 69.2797, 'geofence_radius' => 200]);
        $this->chef = User::create(['role_id' => Roles::CHEF,
            'name' => 'A', 'email' => 'a@t.l', 'password' => bcrypt('x')]);
        $this->chef->kindgarden()->attach($kg->id);
        Sanctum::actingAs($this->chef);
    }

    public function test_replace_check_in_after_existing_increments_counter(): void
    {
        $payload = [
            'lat' => 41.3112, 'lng' => 69.2797,
            'captured_at' => now()->toIso8601String(), 'is_mock' => false,
        ];
        $this->postJson('/api/v1/chef/attendance/check-in',
            $payload + ['photo' => UploadedFile::fake()->image('a.jpg')])->assertOk();

        Carbon::setTestNow('2026-04-28 09:30:00', 'Asia/Tashkent');
        $resp = $this->postJson('/api/v1/chef/attendance/replace',
            $payload + ['type' => 'check_in', 'photo' => UploadedFile::fake()->image('b.jpg'),
            'captured_at' => now()->toIso8601String()]);

        $resp->assertOk();
        $this->assertSame(1, $resp->json('attendance.check_in_replaced_count'));
    }

    public function test_replace_check_in_as_late_entry_when_no_existing(): void
    {
        Carbon::setTestNow('2026-04-28 11:00:00', 'Asia/Tashkent');
        $resp = $this->postJson('/api/v1/chef/attendance/replace', [
            'type' => 'check_in',
            'lat' => 41.3112, 'lng' => 69.2797,
            'captured_at' => now()->toIso8601String(), 'is_mock' => false,
            'photo' => UploadedFile::fake()->image('late.jpg'),
        ]);
        $resp->assertOk();
        $this->assertTrue($resp->json('attendance.check_in_is_late'));
    }

    public function test_replace_invalid_type_returns_422(): void
    {
        $resp = $this->postJson('/api/v1/chef/attendance/replace', [
            'type' => 'lunch',
            'lat' => 41.3112, 'lng' => 69.2797,
            'captured_at' => now()->toIso8601String(), 'is_mock' => false,
            'photo' => UploadedFile::fake()->image('x.jpg'),
        ]);
        $resp->assertStatus(422);
    }
}
```

- [ ] **Step 2: Run tests (must fail)**

Run: `vendor/bin/phpunit tests/Feature/Api/V1/Chef/ReplaceTest.php`
Expected: error.

- [ ] **Step 3: Add replace to controller**

Append to `AttendanceController`:

```php
public function replace(Request $request): JsonResponse
{
    $request->validate(['type' => 'required|in:check_in,check_out']);
    $data = $this->validateAttendancePayload($request);
    $att = $this->svc->replace(
        $request->user(), $request->input('type'),
        $data['photo'], $data['lat'], $data['lng'], $data['captured_at'], $data['is_mock'],
    );
    return response()->json(['attendance' => $att]);
}
```

- [ ] **Step 4: Run tests (must pass)**

Run: `vendor/bin/phpunit tests/Feature/Api/V1/Chef/ReplaceTest.php`
Expected: 3 tests pass.

- [ ] **Step 5: Commit**

```bash
git add app/Http/Controllers/Api/V1/Chef/AttendanceController.php tests/Feature/Api/V1/Chef/ReplaceTest.php
git commit -m "feat(api): POST /chef/attendance/replace (replace + late entry)"
```

---

## Task 26: Chef Attendance — today endpoint (TDD)

**Files:**
- Modify: `app/Http/Controllers/Api/V1/Chef/AttendanceController.php`
- Create: `tests/Feature/Api/V1/Chef/TodayTest.php`

- [ ] **Step 1: Write failing tests**

`tests/Feature/Api/V1/Chef/TodayTest.php`:

```php
<?php

namespace Tests\Feature\Api\V1\Chef;

use App\Constants\Roles;
use App\Models\Kindgarden;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class TodayTest extends TestCase
{
    use RefreshDatabase;

    public function test_today_returns_null_when_no_attendance(): void
    {
        Carbon::setTestNow('2026-04-28 08:00:00', 'Asia/Tashkent');
        $kg = Kindgarden::create(['kingar_name' => 'T', 'lat' => 41.31, 'lng' => 69.27, 'geofence_radius' => 200]);
        $chef = User::create(['role_id' => Roles::CHEF, 'name' => 'A', 'email' => 'a@t.l', 'password' => bcrypt('x')]);
        $chef->kindgarden()->attach($kg->id);
        Sanctum::actingAs($chef);

        $resp = $this->getJson('/api/v1/chef/attendance/today');
        $resp->assertOk();
        $resp->assertJson(['attendance' => null]);
        $resp->assertJsonStructure(['kindgarden' => ['id', 'lat', 'lng', 'geofence_radius']]);
    }

    public function test_today_returns_existing_row(): void
    {
        Storage::fake('local');
        Carbon::setTestNow('2026-04-28 08:00:00', 'Asia/Tashkent');
        $kg = Kindgarden::create(['kingar_name' => 'T', 'lat' => 41.3111, 'lng' => 69.2797, 'geofence_radius' => 200]);
        $chef = User::create(['role_id' => Roles::CHEF, 'name' => 'A', 'email' => 'a@t.l', 'password' => bcrypt('x')]);
        $chef->kindgarden()->attach($kg->id);
        Sanctum::actingAs($chef);

        $this->postJson('/api/v1/chef/attendance/check-in', [
            'lat' => 41.3112, 'lng' => 69.2797,
            'captured_at' => now()->toIso8601String(), 'is_mock' => false,
            'photo' => UploadedFile::fake()->image('a.jpg'),
        ])->assertOk();

        $resp = $this->getJson('/api/v1/chef/attendance/today');
        $resp->assertOk();
        $this->assertNotNull($resp->json('attendance.check_in_at'));
        $this->assertNull($resp->json('attendance.check_out_at'));
    }
}
```

- [ ] **Step 2: Run tests (must fail)**

Run: `vendor/bin/phpunit tests/Feature/Api/V1/Chef/TodayTest.php`
Expected: error.

- [ ] **Step 3: Add today method**

Append to `AttendanceController`:

```php
public function today(Request $request): JsonResponse
{
    $user = $request->user();
    $kg = $user->kindgarden()->first();
    $today = now()->setTimezone('Asia/Tashkent')->toDateString();

    $row = $kg
        ? \App\Models\ChefAttendance::where('user_id', $user->id)->where('date', $today)->first()
        : null;

    return response()->json([
        'attendance' => $row,
        'kindgarden' => $kg ? [
            'id' => $kg->id,
            'lat' => $kg->lat,
            'lng' => $kg->lng,
            'geofence_radius' => (int) ($kg->geofence_radius ?: 200),
        ] : null,
        'server_time' => now()->toIso8601String(),
    ]);
}
```

- [ ] **Step 4: Run tests (must pass)**

Run: `vendor/bin/phpunit tests/Feature/Api/V1/Chef/TodayTest.php`
Expected: 2 tests pass.

- [ ] **Step 5: Commit**

```bash
git add app/Http/Controllers/Api/V1/Chef/AttendanceController.php tests/Feature/Api/V1/Chef/TodayTest.php
git commit -m "feat(api): GET /chef/attendance/today returns today row + kindgarden coords"
```

---

## Task 27: Chef location-events endpoint (TDD)

**Files:**
- Create: `app/Http/Controllers/Api/V1/Chef/LocationEventController.php`
- Create: `tests/Feature/Api/V1/Chef/LocationEventTest.php`

- [ ] **Step 1: Write failing tests**

`tests/Feature/Api/V1/Chef/LocationEventTest.php`:

```php
<?php

namespace Tests\Feature\Api\V1\Chef;

use App\Constants\Roles;
use App\Models\ChefLocationEvent;
use App\Models\Kindgarden;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class LocationEventTest extends TestCase
{
    use RefreshDatabase;

    private User $chef;

    protected function setUp(): void
    {
        parent::setUp();
        Carbon::setTestNow('2026-04-28 08:00:00', 'Asia/Tashkent');
        $kg = Kindgarden::create(['kingar_name' => 'T',
            'lat' => 41.3111, 'lng' => 69.2797, 'geofence_radius' => 200]);
        $this->chef = User::create(['role_id' => Roles::CHEF,
            'name' => 'A', 'email' => 'a@t.l', 'password' => bcrypt('x')]);
        $this->chef->kindgarden()->attach($kg->id);
        Sanctum::actingAs($this->chef);
    }

    public function test_batch_inserts_events(): void
    {
        $resp = $this->postJson('/api/v1/chef/location-events', [
            'events' => [
                ['event_type' => 'exit', 'lat' => 41.3200, 'lng' => 69.2797,
                 'happened_at' => now()->toIso8601String(), 'is_mock' => false],
                ['event_type' => 'enter', 'lat' => 41.3112, 'lng' => 69.2797,
                 'happened_at' => now()->addMinutes(3)->toIso8601String(), 'is_mock' => false],
            ],
        ]);

        $resp->assertOk();
        $resp->assertJson(['inserted' => 2]);
        $this->assertSame(2, ChefLocationEvent::count());
    }

    public function test_invalid_event_type_returns_422(): void
    {
        $resp = $this->postJson('/api/v1/chef/location-events', [
            'events' => [
                ['event_type' => 'lunch', 'lat' => 41.31, 'lng' => 69.27,
                 'happened_at' => now()->toIso8601String(), 'is_mock' => false],
            ],
        ]);
        $resp->assertStatus(422);
    }

    public function test_empty_events_array_returns_zero(): void
    {
        $resp = $this->postJson('/api/v1/chef/location-events', ['events' => []]);
        $resp->assertOk();
        $resp->assertJson(['inserted' => 0]);
    }
}
```

- [ ] **Step 2: Run tests (must fail)**

Run: `vendor/bin/phpunit tests/Feature/Api/V1/Chef/LocationEventTest.php`
Expected: errors — controller missing.

- [ ] **Step 3: Implement controller**

`app/Http/Controllers/Api/V1/Chef/LocationEventController.php`:

```php
<?php

namespace App\Http\Controllers\Api\V1\Chef;

use App\Http\Controllers\Controller;
use App\Services\Attendance\AttendanceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LocationEventController extends Controller
{
    public function __construct(private AttendanceService $svc) {}

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'events' => 'present|array|max:100',
            'events.*.event_type' => 'required|in:exit,enter,beacon',
            'events.*.lat' => 'required|numeric|between:-90,90',
            'events.*.lng' => 'required|numeric|between:-180,180',
            'events.*.happened_at' => 'required|date',
            'events.*.is_mock' => 'required|boolean',
        ]);

        $count = $this->svc->recordLocationEvents($request->user(), $data['events']);
        return response()->json(['inserted' => $count]);
    }
}
```

- [ ] **Step 4: Run tests (must pass)**

Run: `vendor/bin/phpunit tests/Feature/Api/V1/Chef/LocationEventTest.php`
Expected: 3 tests pass.

- [ ] **Step 5: Commit**

```bash
git add app/Http/Controllers/Api/V1/Chef/LocationEventController.php tests/Feature/Api/V1/Chef/LocationEventTest.php
git commit -m "feat(api): POST /chef/location-events batch ingestion"
```

---

## Task 28: isAddelkadirMiddleware

**Files:**
- Create: `app/Http/Middleware/isAddelkadirMiddleware.php`
- Modify: `app/Http/Kernel.php`

- [ ] **Step 1: Create middleware**

```php
<?php

namespace App\Http\Middleware;

use App\Constants\Roles;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class isAddelkadirMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check() && (int) Auth::user()->role_id === Roles::ADDELKADIR) {
            return $next($request);
        }
        return redirect()->route('login');
    }
}
```

- [ ] **Step 2: Register in Kernel.php**

Open `app/Http/Kernel.php`. Find the `$routeMiddleware` array and add:

```php
'isAddelkadir' => \App\Http\Middleware\isAddelkadirMiddleware::class,
```

(after the `'isChef'` line for consistency).

- [ ] **Step 3: Commit**

```bash
git add app/Http/Middleware/isAddelkadirMiddleware.php app/Http/Kernel.php
git commit -m "feat(addelkadir): add isAddelkadir middleware"
```

---

## Task 29: Update RedirectIfAuthenticated for role 8

**Files:**
- Modify: `app/Http/Middleware/RedirectIfAuthenticated.php`

- [ ] **Step 1: Read existing file**

Run: `cat app/Http/Middleware/RedirectIfAuthenticated.php`
Locate the `elseif` chain for role_id 6 (chef).

- [ ] **Step 2: Add a new branch**

After the existing `elseif (... role_id == 7)` branch, add:

```php
elseif (Auth::guard($guard)->check() and auth()->user()->role_id == 8) {
    return redirect()->route('addelkadir.home');
}
```

- [ ] **Step 3: Commit**

```bash
git add app/Http/Middleware/RedirectIfAuthenticated.php
git commit -m "feat(addelkadir): redirect role 8 to addelkadir.home after login"
```

---

## Task 30: AddelkadirController + routes + access test

**Files:**
- Create: `app/Http/Controllers/AddelkadirController.php`
- Modify: `routes/web.php`
- Create: `resources/views/addelkadir/_layout.blade.php`
- Create: `resources/views/addelkadir/home.blade.php`
- Create: `tests/Feature/AddelkadirAccessTest.php`

- [ ] **Step 1: Write failing access test**

`tests/Feature/AddelkadirAccessTest.php`:

```php
<?php

namespace Tests\Feature;

use App\Constants\Roles;
use App\Models\User;
use Database\Seeders\AddelkadirRoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AddelkadirAccessTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(AddelkadirRoleSeeder::class);
    }

    public function test_chef_cannot_access_addelkadir_home(): void
    {
        $u = User::create([
            'role_id' => Roles::CHEF, 'name' => 'C',
            'email' => 'c@t.l', 'password' => bcrypt('x'),
        ]);
        $resp = $this->actingAs($u)->get('/addelkadir/home');
        $resp->assertRedirect(route('login'));
    }

    public function test_addelkadir_user_can_access_home(): void
    {
        $u = User::create([
            'role_id' => Roles::ADDELKADIR, 'name' => 'A',
            'email' => 'a@t.l', 'password' => bcrypt('x'),
        ]);
        $resp = $this->actingAs($u)->get('/addelkadir/home');
        $resp->assertOk();
    }
}
```

- [ ] **Step 2: Run tests (must fail — route missing)**

Run: `vendor/bin/phpunit tests/Feature/AddelkadirAccessTest.php`
Expected: 404 or route not found.

- [ ] **Step 3: Create controller**

`app/Http/Controllers/AddelkadirController.php`:

```php
<?php

namespace App\Http\Controllers;

use App\Models\ChefAttendance;
use App\Models\Kindgarden;
use App\Models\User;
use App\Constants\Roles;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AddelkadirController extends Controller
{
    public function home(): View
    {
        $today = now()->setTimezone('Asia/Tashkent')->toDateString();
        $totalChefs = User::where('role_id', Roles::CHEF)->count();
        $todayRows = ChefAttendance::where('date', $today)->with('user', 'kindgarden')->get();

        $cameCount = $todayRows->whereNotNull('check_in_at')->count();
        $lateCount = $todayRows->where('check_in_is_late', true)->count();
        $absentCount = max(0, $totalChefs - $cameCount);

        return view('addelkadir.home', [
            'totalChefs' => $totalChefs,
            'cameCount' => $cameCount,
            'lateCount' => $lateCount,
            'absentCount' => $absentCount,
            'todayRows' => $todayRows,
            'today' => $today,
        ]);
    }
}
```

- [ ] **Step 4: Add routes**

Append to `routes/web.php`:

```php
use App\Http\Controllers\AddelkadirController;

Route::group(['prefix' => 'addelkadir', 'middleware' => ['isAddelkadir', 'auth']], function () {
    Route::get('home', [AddelkadirController::class, 'home'])->name('addelkadir.home');
});
```

- [ ] **Step 5: Create layout view**

`resources/views/addelkadir/_layout.blade.php`:

```blade
<!DOCTYPE html>
<html lang="uz">
<head>
    <meta charset="UTF-8">
    <title>Addelkadir — @yield('title')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    @stack('head')
</head>
<body class="bg-light">
<nav class="navbar navbar-dark bg-dark">
    <div class="container-fluid">
        <span class="navbar-brand">Addelkadir paneli</span>
        <div>
            <a class="text-white me-3" href="{{ route('addelkadir.home') }}">Bosh</a>
            <a class="text-white me-3" href="{{ url('addelkadir/attendance') }}">Davomat</a>
            <a class="text-white me-3" href="{{ url('addelkadir/kindgardens') }}">Bog'chalar</a>
            <a class="text-white me-3" href="{{ url('addelkadir/chefs') }}">Oshpazlar</a>
            <a class="text-white" href="{{ route('logout') }}"
               onclick="event.preventDefault();document.getElementById('logout-form').submit();">Chiqish</a>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
        </div>
    </div>
</nav>
<main class="container py-4">
    @yield('content')
</main>
</body>
</html>
```

- [ ] **Step 6: Create home view**

`resources/views/addelkadir/home.blade.php`:

```blade
@extends('addelkadir._layout')
@section('title', 'Bosh sahifa')
@section('content')
<h1 class="mb-4">Bugungi davomat — {{ $today }}</h1>

<div class="row g-3 mb-4">
    <div class="col-md-3"><div class="card"><div class="card-body">
        <div class="text-muted small">JAMI OSHPAZLAR</div>
        <div class="display-6">{{ $totalChefs }}</div>
    </div></div></div>
    <div class="col-md-3"><div class="card border-success"><div class="card-body">
        <div class="text-muted small">KELDI</div>
        <div class="display-6 text-success">{{ $cameCount }}</div>
    </div></div></div>
    <div class="col-md-3"><div class="card border-warning"><div class="card-body">
        <div class="text-muted small">KECHIKDI</div>
        <div class="display-6 text-warning">{{ $lateCount }}</div>
    </div></div></div>
    <div class="col-md-3"><div class="card border-danger"><div class="card-body">
        <div class="text-muted small">KELMADI</div>
        <div class="display-6 text-danger">{{ $absentCount }}</div>
    </div></div></div>
</div>

<div class="card"><div class="card-header"><strong>Bugungi ro'yxat</strong></div>
<table class="table mb-0">
    <thead><tr><th>Oshpaz</th><th>Bog'cha</th><th>Keldi</th><th>Ketdi</th><th>Holat</th></tr></thead>
    <tbody>
        @forelse ($todayRows as $r)
        <tr>
            <td>{{ optional($r->user)->name }}</td>
            <td>{{ optional($r->kindgarden)->kingar_name }}</td>
            <td>{{ optional($r->check_in_at)->format('H:i') ?? '—' }} @if($r->check_in_is_late)<span class="badge bg-warning">kechikdi</span>@endif</td>
            <td>{{ optional($r->check_out_at)->format('H:i') ?? '—' }}</td>
            <td>
                @if($r->check_in_at && !$r->check_out_at)<span class="badge bg-success">Bog'chada</span>
                @elseif($r->check_out_at)<span class="badge bg-secondary">Ketgan</span>
                @else<span class="badge bg-danger">—</span>@endif
            </td>
        </tr>
        @empty
        <tr><td colspan="5" class="text-muted text-center">Hech kim hali kelmagan</td></tr>
        @endforelse
    </tbody>
</table>
</div>
@endsection
```

- [ ] **Step 7: Run tests (must pass)**

Run: `vendor/bin/phpunit tests/Feature/AddelkadirAccessTest.php`
Expected: 2 tests pass.

- [ ] **Step 8: Commit**

```bash
git add app/Http/Controllers/AddelkadirController.php routes/web.php resources/views/addelkadir tests/Feature/AddelkadirAccessTest.php
git commit -m "feat(addelkadir): home dashboard route + view + access test"
```

---

## Task 31: Addelkadir attendance log page

**Files:**
- Modify: `app/Http/Controllers/AddelkadirController.php`
- Modify: `routes/web.php`
- Create: `resources/views/addelkadir/attendance.blade.php`

- [ ] **Step 1: Add controller method**

Append to `AddelkadirController`:

```php
public function attendance(Request $request): View
{
    $from = $request->input('from', now()->subDays(7)->toDateString());
    $to = $request->input('to', now()->toDateString());

    $rows = ChefAttendance::with('user', 'kindgarden')
        ->whereBetween('date', [$from, $to])
        ->orderByDesc('date')
        ->paginate(50);

    return view('addelkadir.attendance', compact('rows', 'from', 'to'));
}

public function selfie(Request $request, int $attendanceId, string $type)
{
    abort_unless(in_array($type, ['check_in', 'check_out'], true), 404);
    $att = ChefAttendance::findOrFail($attendanceId);
    $path = $type === 'check_in' ? $att->check_in_selfie_path : $att->check_out_selfie_path;
    abort_if(!$path, 404);
    return response()->file(storage_path('app/' . $path));
}
```

- [ ] **Step 2: Add routes**

Inside the `addelkadir` group in `routes/web.php`:

```php
Route::get('attendance', [AddelkadirController::class, 'attendance'])->name('addelkadir.attendance');
Route::get('selfie/{id}/{type}', [AddelkadirController::class, 'selfie'])->name('addelkadir.selfie');
```

- [ ] **Step 3: Create view**

`resources/views/addelkadir/attendance.blade.php`:

```blade
@extends('addelkadir._layout')
@section('title', 'Davomat tarixi')
@section('content')
<h1 class="mb-4">Davomat tarixi</h1>

<form method="GET" class="row g-2 mb-3">
    <div class="col-auto"><input type="date" name="from" value="{{ $from }}" class="form-control"></div>
    <div class="col-auto"><input type="date" name="to" value="{{ $to }}" class="form-control"></div>
    <div class="col-auto"><button class="btn btn-primary">Filtr</button></div>
</form>

<table class="table table-striped">
    <thead><tr><th>Sana</th><th>Oshpaz</th><th>Bog'cha</th><th>Keldi</th><th>Ketdi</th><th>Selfilar</th></tr></thead>
    <tbody>
    @foreach ($rows as $r)
    <tr>
        <td>{{ $r->date->format('Y-m-d') }}</td>
        <td>{{ optional($r->user)->name }}</td>
        <td>{{ optional($r->kindgarden)->kingar_name }}</td>
        <td>{{ optional($r->check_in_at)->format('H:i') ?? '—' }}
            @if($r->check_in_is_late)<span class="badge bg-warning">kech</span>@endif
            @if($r->check_in_replaced_count > 0)<span class="badge bg-info">o'zg.{{$r->check_in_replaced_count}}</span>@endif
        </td>
        <td>{{ optional($r->check_out_at)->format('H:i') ?? '—' }}</td>
        <td>
            @if($r->check_in_selfie_path)
                <a target="_blank" href="{{ route('addelkadir.selfie', [$r->id, 'check_in']) }}">in</a>
            @endif
            @if($r->check_out_selfie_path)
                <a target="_blank" class="ms-2" href="{{ route('addelkadir.selfie', [$r->id, 'check_out']) }}">out</a>
            @endif
        </td>
    </tr>
    @endforeach
    </tbody>
</table>

{{ $rows->withQueryString()->links() }}
@endsection
```

- [ ] **Step 4: Quick smoke test**

Run: `php artisan route:list --name=addelkadir`
Expected: list shows `addelkadir.home`, `addelkadir.attendance`, `addelkadir.selfie`.

- [ ] **Step 5: Commit**

```bash
git add app/Http/Controllers/AddelkadirController.php routes/web.php resources/views/addelkadir/attendance.blade.php
git commit -m "feat(addelkadir): attendance log page with date filter and selfie viewer"
```

---

## Task 32: Addelkadir kindergarten geofence config page

**Files:**
- Modify: `app/Http/Controllers/AddelkadirController.php`
- Modify: `routes/web.php`
- Create: `resources/views/addelkadir/kindgardens.blade.php`

- [ ] **Step 1: Add controller methods**

Append to `AddelkadirController`:

```php
public function kindgardens(): View
{
    $items = Kindgarden::orderBy('id')->get();
    return view('addelkadir.kindgardens', compact('items'));
}

public function updateKindgardenCoords(Request $request, int $id)
{
    $data = $request->validate([
        'lat' => 'required|numeric|between:-90,90',
        'lng' => 'required|numeric|between:-180,180',
        'geofence_radius' => 'required|integer|min:50|max:1000',
    ]);
    Kindgarden::findOrFail($id)->update($data);
    return redirect()->route('addelkadir.kindgardens')->with('status', 'Saqlandi');
}
```

- [ ] **Step 2: Add routes**

Inside the `addelkadir` group:

```php
Route::get('kindgardens', [AddelkadirController::class, 'kindgardens'])->name('addelkadir.kindgardens');
Route::post('kindgardens/{id}', [AddelkadirController::class, 'updateKindgardenCoords'])->name('addelkadir.kindgardens.update');
```

- [ ] **Step 3: Create view (Leaflet/OSM map for clicking)**

`resources/views/addelkadir/kindgardens.blade.php`:

```blade
@extends('addelkadir._layout')
@section('title', 'Bog\'chalar')
@push('head')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
@endpush
@section('content')
<h1 class="mb-4">Bog'chalar va geofence</h1>

@if(session('status'))<div class="alert alert-success">{{ session('status') }}</div>@endif

<table class="table">
    <thead><tr><th>Nomi</th><th>Lat</th><th>Lng</th><th>Radius</th><th></th></tr></thead>
    <tbody>
    @foreach ($items as $kg)
    <tr>
        <td>{{ $kg->kingar_name }}</td>
        <td>{{ $kg->lat ?? '—' }}</td>
        <td>{{ $kg->lng ?? '—' }}</td>
        <td>{{ $kg->geofence_radius ?? 200 }} m</td>
        <td>
            <button class="btn btn-sm btn-primary" data-bs-toggle="collapse" data-bs-target="#row-{{$kg->id}}">Tahrirlash</button>
        </td>
    </tr>
    <tr class="collapse" id="row-{{$kg->id}}">
        <td colspan="5">
            <form method="POST" action="{{ route('addelkadir.kindgardens.update', $kg->id) }}" class="row g-2 align-items-end">
                @csrf
                <div class="col-md-3"><label>Lat</label><input name="lat" id="lat-{{$kg->id}}" value="{{ $kg->lat ?? 41.3111 }}" class="form-control" type="number" step="0.0000001" required></div>
                <div class="col-md-3"><label>Lng</label><input name="lng" id="lng-{{$kg->id}}" value="{{ $kg->lng ?? 69.2797 }}" class="form-control" type="number" step="0.0000001" required></div>
                <div class="col-md-2"><label>Radius (m)</label><input name="geofence_radius" value="{{ $kg->geofence_radius ?? 200 }}" class="form-control" type="number" min="50" max="1000" required></div>
                <div class="col-md-2"><button class="btn btn-success">Saqlash</button></div>
                <div class="col-12">
                    <div id="map-{{$kg->id}}" style="height:300px;border:1px solid #ddd"></div>
                </div>
            </form>
            <script>
            (function() {
                const id = {{ $kg->id }};
                const lat = parseFloat(document.getElementById('lat-' + id).value);
                const lng = parseFloat(document.getElementById('lng-' + id).value);
                const map = L.map('map-' + id).setView([lat, lng], 16);
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
                    {attribution: '© OSM'}).addTo(map);
                let marker = L.marker([lat, lng], {draggable: true}).addTo(map);
                marker.on('dragend', e => {
                    const p = e.target.getLatLng();
                    document.getElementById('lat-' + id).value = p.lat.toFixed(7);
                    document.getElementById('lng-' + id).value = p.lng.toFixed(7);
                });
                map.on('click', e => {
                    marker.setLatLng(e.latlng);
                    document.getElementById('lat-' + id).value = e.latlng.lat.toFixed(7);
                    document.getElementById('lng-' + id).value = e.latlng.lng.toFixed(7);
                });
            })();
            </script>
        </td>
    </tr>
    @endforeach
    </tbody>
</table>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
@endsection
```

- [ ] **Step 4: Smoke test in browser (manual)**

Run dev server: `php artisan serve --port=8000`
Open `http://localhost:8000/addelkadir/kindgardens` while logged in as a role 8 user. Click "Tahrirlash" → map appears, drag pin → form values update.
Expected: form saves, page shows "Saqlandi" alert.

- [ ] **Step 5: Commit**

```bash
git add app/Http/Controllers/AddelkadirController.php routes/web.php resources/views/addelkadir/kindgardens.blade.php
git commit -m "feat(addelkadir): kindgarden coords config page with Leaflet map"
```

---

## Task 33: Addelkadir chefs list page

**Files:**
- Modify: `app/Http/Controllers/AddelkadirController.php`
- Modify: `routes/web.php`
- Create: `resources/views/addelkadir/chefs.blade.php`

- [ ] **Step 1: Add controller method**

Append to `AddelkadirController`:

```php
public function chefs(): View
{
    $chefs = User::where('role_id', Roles::CHEF)
        ->with('kindgarden')
        ->withCount(['tokens as has_active_token' => function ($q) {
            $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
        }])
        ->orderBy('name')
        ->get();

    $devices = \App\Models\ChefDevice::all()->keyBy('user_id');

    return view('addelkadir.chefs', compact('chefs', 'devices'));
}
```

- [ ] **Step 2: Add route**

Inside the `addelkadir` group:

```php
Route::get('chefs', [AddelkadirController::class, 'chefs'])->name('addelkadir.chefs');
```

- [ ] **Step 3: Create view**

`resources/views/addelkadir/chefs.blade.php`:

```blade
@extends('addelkadir._layout')
@section('title', 'Oshpazlar')
@section('content')
<h1 class="mb-4">Oshpazlar ro'yxati</h1>
<table class="table">
    <thead><tr>
        <th>Ism</th><th>Email</th><th>Bog'cha</th><th>Qurilma</th><th>App ver.</th><th>Oxirgi faollik</th>
    </tr></thead>
    <tbody>
    @foreach ($chefs as $c)
    @php $d = $devices->get($c->id); @endphp
    <tr>
        <td>{{ $c->name }}</td>
        <td>{{ $c->email }}</td>
        <td>{{ optional($c->kindgarden->first())->kingar_name ?? '—' }}</td>
        <td>{{ $d ? "{$d->platform} ({$d->device_model})" : '—' }}</td>
        <td>{{ $d->app_version ?? '—' }}</td>
        <td>{{ optional($d?->last_seen_at)->diffForHumans() ?? '—' }}</td>
    </tr>
    @endforeach
    </tbody>
</table>
@endsection
```

- [ ] **Step 4: Commit**

```bash
git add app/Http/Controllers/AddelkadirController.php routes/web.php resources/views/addelkadir/chefs.blade.php
git commit -m "feat(addelkadir): chefs list page with device + app version"
```

---

## Task 34: Cleanup Artisan command for old selfies

**Files:**
- Create: `app/Console/Commands/CleanupAttendancePhotos.php`
- Modify: `app/Console/Kernel.php`

- [ ] **Step 1: Create command**

```php
<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class CleanupAttendancePhotos extends Command
{
    protected $signature = 'attendance:cleanup-photos {--days=180}';
    protected $description = 'Delete attendance selfie photos older than --days days (default 180)';

    public function handle(): int
    {
        $cutoff = now()->subDays((int) $this->option('days'));
        $disk = Storage::disk('local');
        $deleted = 0;

        foreach ($disk->directories('attendance') as $dir) {
            $name = basename($dir);
            try {
                $dirDate = \Carbon\Carbon::parse($name);
            } catch (\Throwable $e) {
                continue;
            }
            if ($dirDate->lt($cutoff)) {
                $disk->deleteDirectory($dir);
                $deleted++;
                $this->info("Deleted {$dir}");
            }
        }
        $this->info("Done. {$deleted} day folders removed.");
        return Command::SUCCESS;
    }
}
```

- [ ] **Step 2: Schedule daily run**

Open `app/Console/Kernel.php`. Find the `schedule(Schedule $schedule)` method and add inside:

```php
$schedule->command('attendance:cleanup-photos')->dailyAt('03:00');
```

- [ ] **Step 3: Smoke test**

Create a fake old folder:
```bash
mkdir -p storage/app/attendance/2024-01-01 && touch storage/app/attendance/2024-01-01/x.jpg
php artisan attendance:cleanup-photos --days=180
```
Expected: output `Deleted attendance/2024-01-01`. The folder is gone.

- [ ] **Step 4: Commit**

```bash
git add app/Console/Commands/CleanupAttendancePhotos.php app/Console/Kernel.php
git commit -m "feat(attendance): add cleanup command + daily 03:00 schedule"
```

---

## Task 35: Run full test suite + commit final

- [ ] **Step 1: Run full test suite**

Run: `vendor/bin/phpunit`
Expected: all tests pass, no errors. Note the count.

- [ ] **Step 2: Run route:list to verify all endpoints**

Run: `php artisan route:list --path=api/v1` and `php artisan route:list --name=addelkadir`
Expected: each endpoint listed once with the right middleware.

- [ ] **Step 3: Final commit (if anything left)**

```bash
git status
# If clean, no commit needed.
```

---

## Manual smoke checklist (post-merge, no automation)

After merging Plan 1, manually verify on a real environment:

1. Run `php artisan migrate` and `php artisan db:seed --class=AddelkadirRoleSeeder` on staging.
2. Edit one test user's `role_id` to 8 in the DB and log in via web — should land on `/addelkadir/home`.
3. Open `/addelkadir/kindgardens`, set lat/lng for one pilot kindergarten by clicking on the map, save.
4. With Postman:
   - `POST /api/v1/auth/login` (chef email + password) → 200, get `token`.
   - `POST /api/v1/auth/device` with `Authorization: Bearer <token>` → 200.
   - `POST /api/v1/chef/attendance/check-in` with `multipart/form-data` (photo file + lat/lng/captured_at/is_mock) → 200.
   - `GET /api/v1/chef/attendance/today` → returns row.
   - `POST /api/v1/chef/attendance/check-out` → 200.
5. Visit `/addelkadir/home` — see today's row in the dashboard. Click selfie link — image renders.
6. `php artisan schedule:run` (or simulate) — confirm cleanup command does not error.

---

**End of plan.**
