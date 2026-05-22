# Plan 5 — Background Location + Push Notifications + Addelkadir Location UI

**Sana:** 2026-05-22
**Asos:** [Chef mobile app design (2026-04-28)](2026-04-28-chef-mobile-app-design.md) § 3.3, § 6.5
**Holat:** Brainstormed, spec yozildi, plan yozish kutilmoqda

## 1. Maqsad va ko'lam

Plan 1 (backend MVP) va Plan 2 (mobile MVP) ishlab turibdi: oshpaz check-in/check-out qilyapti, Addelkadir admin panelda davomatni ko'ryapti, undo-check-out ishlaydi. Plan 5 — pilot davomida aniqlangan zaifliklarni yopadi:

- Oshpaz check-in qilgandan keyin bog'chadan **chiqib ketib qaytmasligi** hozir aniqlanmaydi
- Addelkadir hech qanday **push xabar** olmaydi — admin panelni qo'lda yangilashi kerak
- Yangi buyurtma qo'shilganda oshpaz **xabardor bo'lmaydi**, kelguniga qadar bilmaydi
- Ertalab kelmagan oshpazni **avtomatik aniqlash** yo'q

**Plan 5 — 3 ta mustaqil kichik tizim, bitta plan ostida:**
1. **Fon rejimida lokatsiya kuzatuvi** (mobil) — hybrid: OS-level geofence-exit + 30 daqiqalik heartbeat
2. **Push notifications** (backend + mobil + Firebase) — 4 ta trigger
3. **Addelkadir lokatsiya tarixi UI** (admin panel) — timeline jadval ko'rinishi

## 2. Arxitektura ko'rinishi

```
  MOBIL (RN 0.74)            BACKEND (Laravel 8)         ADMIN UI
  ───────────────            ───────────────────         ────────
  ┌──────────────┐
  │ Background   │ ─events──> ┌──────────────────┐ rows  ┌──────────┐
  │ Geolocation  │  (queued,  │ /chef/location-  │ ────> │ Adm UI:  │
  │ + 30min      │  batched)  │ events           │       │ tarixi   │
  │ heartbeat    │            └──────────────────┘       │ jadval   │
  └──────────────┘                    │                  └──────────┘
       ▲                              │ exit trigger
       │ check-in start               v
       │ check-out stop      ┌──────────────────┐
       │                     │ PushService      │
       │                     │ (kreait/firebase)│
       │                     └──────────────────┘
  ┌──────────────┐                    │  FCM HTTP v1
  │ FCM messaging│ ←──── push xabar ──┘
  │ qabul        │
  └──────────────┘            ┌─────────────────────┐
                              │ Scheduler (cron):   │
                              │ 08:00 chef reminder │
                              │ 09:15 no-checkin    │
                              └─────────────────────┘
                              ┌─────────────────────┐
                              │ Order::created      │
                              │ event hook → chef   │
                              └─────────────────────┘
```

### 2.1 Asosiy qarorlar (rationale)

- **Hybrid tracking pattern** — geofence-exit (eng past batareya) + 30 daq heartbeat (Xiaomi/Huawei OEM bloklash fallback). Faqat heartbeat — oraliqda chiqib qaytgan oshpazni topa olmaydi. Faqat geofence — OEM siyosatlari tufayli ishonchsiz.
- **Mavjud `chef_location_events` jadvali qayta ishlatiladi** — Plan 1'da yaratilgan, `enum('exit','enter','beacon')` allaqachon bor. Heartbeat uchun `beacon` ishlatiladi. `inside/outside` holati `distance_m vs kindgarden.geofence_radius` orqali hisoblanadi (yangi column kerak emas).
- **Mavjud `attendanceQueue` (mmkv) + `attendanceFlusher` qayta ishlatiladi** — yangi infrastruktura yaratilmaydi; `location_events` kind allaqachon mavjud.
- **`kreait/firebase-php` v6.x** backend FCM SDK uchun — PHP 8.0+ qo'llab-quvvatlaydi, FCM HTTP v1 API ishlatadi (legacy emas).
- **`react-native-background-geolocation` (transistorsoft)** mobile fon servisi uchun — eng ishonchli Android foreground service + WakeLock implementatsiyasi.
- **Lokatsiya UI faqat jadval** (xarita emas) pilot uchun yetarli; "Xaritada" link Yandex Maps'ni yangi tabda ochadi — hozircha xarita SDK kerak emas.

## 3. Subsistema #1 — Fon rejimida lokatsiya kuzatuvi (mobil)

### 3.1 Hayot tsikli

```
check-in success
  → BackgroundGeolocation.start()
      ├─ Foreground service yoqiladi (sticky notification: "Davomat kuzatuvi faol")
      ├─ OS-level geofence ro'yxatdan o'tkaziladi (radius=200m, kindgarden lat/lng)
      └─ Heartbeat AlarmManager: har 30 daqiqada beacon event

check-out success
  → BackgroundGeolocation.stop()
      ├─ Foreground service to'xtaydi
      ├─ Geofence olib tashlanadi
      └─ AlarmManager bekor qilinadi

Ilova qayta ochilganda
  → Agar today.check_in_at va !today.check_out_at, BGL holatini tekshir; o'chgan bo'lsa qayta start
```

### 3.2 Hodisa turlari

| event_type | Qachon hosil bo'ladi | Server postlanadi |
|---|---|---|
| `exit` | Chef geofence chetidan chiqdi | Darrov (online), aks holda navbatga |
| `enter` | Chef geofence ichiga qaytdi | Darrov yoki navbatga |
| `beacon` | Har 30 daqiqada (heartbeat) | Darrov yoki navbatga |

Har bir hodisa ushbu maydonlar bilan saqlanadi: `event_type`, `lat`, `lng`, `happened_at` (ISO 8601 UTC), `is_mock`. Server `distance_m` ni o'zi hisoblaydi (mobile yubormaydi — chunki kindgarden coordinates serverda dolzarbroq).

### 3.3 Yangi fayllar (mobile/src/)

- **`attendance/backgroundLocation.ts`** — `react-native-background-geolocation` ni o'rab beruvchi. Eksport: `startTracking(kindgarden)`, `stopTracking()`, `onLocation(listener)`. Geofence-exit/enter event handler attendanceQueue.enqueue chaqiradi.
- **`attendance/heartbeatScheduler.ts`** — Native Android AlarmManager wrapperi. Eksport: `scheduleHeartbeat()`, `cancelHeartbeat()`. Triggerlanganda current location oladi va beacon event navbatga qo'shadi.
- **`screens/AttendanceScreen.tsx`** (mavjud) — check-in muvaffaqiyat callback'iga `startTracking(kindgarden)` qo'shiladi; check-out callback'iga `stopTracking()`.

### 3.4 Yangi Android native modul

`mobile/android/app/src/main/java/uz/kindergarden/chefmobile/heartbeat/HeartbeatModule.kt`:
- AlarmManager bilan 30 daqiqalik takrorlanuvchi alarm
- BroadcastReceiver kelganida JS context'ga `heartbeat` event yuboradi
- Doze mode'da `setExactAndAllowWhileIdle` ishlatiladi (kechikishni minimal qilish)

### 3.5 Ruxsatlar va manifest

Yangi qo'shimchalar (`AndroidManifest.xml`):
```xml
<uses-permission android:name="android.permission.ACCESS_BACKGROUND_LOCATION" />
<uses-permission android:name="android.permission.FOREGROUND_SERVICE" />
<uses-permission android:name="android.permission.FOREGROUND_SERVICE_LOCATION" />
<uses-permission android:name="android.permission.SCHEDULE_EXACT_ALARM" />
<uses-permission android:name="android.permission.RECEIVE_BOOT_COMPLETED" />
```

Ruxsat oqimi (birinchi check-in vaqtida):
1. Foreground location ruxsati (mavjud)
2. Background location ruxsati — Android 10+ uchun yangi modal: "Doim ruxsat berish"
3. Battery optimization istisnosi — `Settings.ACTION_REQUEST_IGNORE_BATTERY_OPTIMIZATIONS` intent

### 3.6 Battery optimization onboarding modal

Birinchi muvaffaqiyatli check-in'dan keyin:

> **Davomat kuzatuvi uchun batareya sozlamasi**
>
> Ilova bog'chada turganingizni doimiy kuzatish uchun batareya optimizatsiyasidan istisno bo'lishi kerak. Aks holda telefon ilovani ishchi vaqtda to'xtatib qo'yishi mumkin.
>
> [Sozlamalarga o'tish] [Hozir emas]

"Sozlamalarga o'tish" — `IGNORE_BATTERY_OPTIMIZATIONS` intent ochadi. Xiaomi/Huawei uchun qo'shimcha qadamlar README'ga yoziladi (Autostart, MIUI Optimizations).

### 3.7 Offline rejimi

Mavjud `attendanceFlusher` qayta ishlatiladi. Location event'lar batchda yuboriladi:
- `attendanceQueue` ichida bir nechta beacon/exit/enter event'lar to'planib turadi
- Tarmoq tiklanganda yoki har 30 sekundda flusher `POST /chef/location-events` bilan `{events: [...]}` body'da bir martada jo'natadi
- Backend allaqachon batch ingestion'ni qo'llaydi (Plan 1'da yaratilgan)

## 4. Subsistema #2 — Push notifications

### 4.1 Backend (Laravel)

**Composer paket:** `kreait/firebase-php` v6.x (composer.json'ga qo'shiladi)

**Yangi servis:** `app/Services/Push/PushService.php`

```php
class PushService {
    public function __construct(\Kreait\Firebase\Messaging $messaging) {}

    public function sendToUser(User $user, string $title, string $body, array $data = []): void;
    public function sendToUsers(\Illuminate\Support\Collection $users, string $title, string $body, array $data = []): array;

    private function getTokens(User|Collection $u): array;  // chef_devices'dan
    private function pruneInvalidTokens(array $invalid): void;  // NotRegistered tokenlarni o'chiradi
}
```

**Konfiguratsiya:**
- `config/services.php` — `'firebase' => ['credentials_path' => env('FIREBASE_CREDENTIALS')]`
- `.env` (production): `FIREBASE_CREDENTIALS=storage/app/firebase-credentials.json`
- Service account JSON `storage/app/firebase-credentials.json` (gitignore'da, 0600 chmod)

### 4.2 Push triggerlari

| # | Trigger joyi | Vaqt/shart | Kimga | Xabar nusxasi |
|---|---|---|---|---|
| 1 | `LocationEventController::store` | `event_type='exit'` + ish vaqti (08:00-19:00 Tashkent) | Barcha Addelkadir (role_id=10) — bog'cha-Addelkadir biriktirish hozircha yo'q | "**Sodikov A.** Bog'cha #12 dan chiqib ketdi — 215m" |
| 2 | `app/Console/Commands/NotifyMissingCheckIn.php` | Har kuni 09:15 (cron) | Hamma Addelkadir | "Bugun **3 oshpaz** hali kelmadi: Sodikov A., Karimov B., Toshev C." |
| 3 | `app/Console/Commands/SendMorningReminders.php` | Har kuni 08:00 (cron) | Hamma faol CHEF (role_id=6) | "Bugungi davomatni unutmang — ChefMobile ilovasini oching" |
| 4 | `app/Observers/OrderObserver.php` (`created`) | Yangi buyurtma yaratildi (aniq Eloquent model implementation plan'da topiladi — `Order` yoki `ProductOrder`) | Buyurtma tegishli oshpaz | "Yangi buyurtma: **2026-05-22 — 12:00**" |

**Cron sozlash (`app/Console/Kernel.php`):**
```php
protected function schedule(Schedule $schedule) {
    $schedule->command('chef:morning-reminder')
        ->dailyAt('08:00')
        ->timezone('Asia/Tashkent');
    $schedule->command('chef:notify-missing-checkin')
        ->dailyAt('09:15')
        ->timezone('Asia/Tashkent');
}
```

Server tomonda: `* * * * * cd /home/c/cj56359/public_html && php artisan schedule:run >> /dev/null 2>&1` cron entry. Agar tw1.ru'da cron interfeysi bo'lmasa, alternativ: `while true; do php artisan schedule:run; sleep 60; done` background process.

### 4.3 Mobile (React Native)

**Qayta tiklash (oldin stub qilingan):**
- `package.json` — `@react-native-firebase/app` va `@react-native-firebase/messaging` qaytariladi (versiya: 19.x — RN 0.74 mos)
- `android/build.gradle` — `classpath 'com.google.gms:google-services:4.4.0'` qaytariladi
- `android/app/build.gradle` — `apply plugin: 'com.google.gms.google-services'` qaytariladi

**`google-services.json`** — `mobile/android/app/google-services.json` ga qo'yiladi (foydalanuvchi tomonidan yuklab olingan, Firebase project `chefmobile-ab8a1`)

**`mobile/src/push/fcm.ts` qayta yoziladi:**
```typescript
export async function requestPushPermission(): Promise<boolean>;
export async function registerDeviceWithBackend(appVersion: string): Promise<void>;
export function onForegroundMessage(handler: (msg: FirebaseMessagingTypes.RemoteMessage) => void): () => void;
export function setupBackgroundHandler(): void;  // index.js'da chaqiriladi
```

**Foreground handler:** ilova ochiq paytda — yuqorida slide-in banner (3 sekund). Mavjud `OfflineIndicator` komponenti uchun shablon ishlatiladi.

**Background handler:** OS sistema notification ko'rsatadi avtomatik (`@react-native-firebase/messaging` default).

### 4.4 Firebase loyihasi (foydalanuvchi tomonidan)

✅ Yaratilgan: `chefmobile-ab8a1`, package `uz.kindergarden.chefmobile`, `google-services.json` joyida.

❌ Hali kerak: Service account JSON — Firebase Console → ⚙️ Project Settings → Service accounts → "Generate new private key" → JSON faylni serverga `storage/app/firebase-credentials.json` qilib qo'yish.

## 5. Subsistema #3 — Addelkadir lokatsiya tarixi UI

### 5.1 Yangi route va controller

`routes/web.php` (Addelkadir middleware ostida):
```php
Route::get('addelkadir/location-events', [AddelkadirController::class, 'locationEvents'])
    ->name('addelkadir.location-events');
```

`AddelkadirController::locationEvents(Request $request): View`:
- Filtrlar (GET): `chef_id` (optional), `date` (default: bugun, Asia/Tashkent), `event_type` (optional)
- Query: `ChefLocationEvent::with('user', 'kindgarden')->whereDate('happened_at', $date)->orderBy('happened_at')->paginate(100)`
- Yig'ma statistika: bugun nechta `exit`, jami tashqarida bo'lgan vaqt (exit→enter intervallari summa)

### 5.2 Yangi view: `resources/views/addelkadir/location_events.blade.php`

**Filtr formasi (yuqorida):**
- Sana (date input)
- Oshpaz (select dropdown — barcha CHEF role)
- Hodisa turi (select: hammasi, exit, enter, beacon)

**Yig'ma statistika kartochkasi:**
```
Bugun: 3 chiqish hodisasi · Jami tashqarida: 47 daq · 12 ta heartbeat
```

**Jadval:**

| Vaqt | Oshpaz | Bog'cha | Hodisa | Masofa | Holat | Xaritada |
|---|---|---|---|---|---|---|
| 10:32 | Sodikov A. | Bog'cha #12 | 🟢 Kirish | 45m | Ichida | [📍](maps) |
| 11:15 | Sodikov A. | Bog'cha #12 | 🔄 Heartbeat | 38m | Ichida | [📍] |
| 13:42 | Sodikov A. | Bog'cha #12 | 🔴 Chiqish | 215m | Tashqarida | [📍] |

**Format detallari:**
- Vaqt: `happened_at` Asia/Tashkent timezone'da `H:i` format
- Hodisa: emoji + uzbek matn (🟢 Kirish, 🔴 Chiqish, 🔄 Heartbeat)
- Holat: `distance_m <= kindgarden.geofence_radius` ? "Ichida" : "Tashqarida" (rang: yashil/qizil)
- Xaritada: `<a href="https://yandex.uz/maps/?ll={lng},{lat}&z=18&pt={lng},{lat},pm2rdm" target="_blank">📍</a>`

### 5.3 Navigatsiya

Mavjud Addelkadir layout sidebar/menu'ga "Lokatsiya tarixi" link qo'shiladi (`addelkadir.location-events` route'ga).

## 6. Schema o'zgarishlari

**`chef_location_events`** jadvali Plan 1'da yaratilgan, schema o'zgartirilmaydi:

```php
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
});
```

**`chef_devices`** jadvali Plan 1'da yaratilgan, FCM token uchun ishlatiladi:
```
user_id, fcm_token, platform, device_model, app_version, created_at, updated_at
```

**Yangi migration kerak emas.**

## 7. Build sequence (bosqichli rollout)

Risk kamaytirish uchun har bosqich mustaqil deploy qilinadi. Avvalgisi sinmagunicha keyingisiga o'tilmaydi.

### Bosqich A — Backend admin UI (kichik o'zgarish, deploy oson)
1. `ChefLocationEvent` modeliga `inside()` helper metodi
2. `AddelkadirController::locationEvents` + filtrlash logikasi
3. `resources/views/addelkadir/location_events.blade.php`
4. Sidebar nav'iga "Lokatsiya tarixi" link
5. Route qo'shilishi

→ Deploy + test: mobile hali event yubormayapti, lekin admin UI ishlashga tayyor; manual SQL bilan test data qo'shib tekshirish mumkin.

### Bosqich B — Push infrastructure (server)
6. `composer require kreait/firebase-php:^6.0`
7. `config/services.php` ga firebase config
8. `app/Services/Push/PushService.php` + unit testlar (FCM mock'lab)
9. Service account JSON serverga manual upload
10. Test artisan command: `php artisan chef:test-push {user_id}` — bitta test xabar yuborish

→ Test: Firebase Console'da "Cloud Messaging" → "Send test message" → mobile telefonga test push.

### Bosqich C — Push triggers (server)
11. `app/Console/Commands/NotifyMissingCheckIn.php`
12. `app/Console/Commands/SendMorningReminders.php`
13. `app/Observers/OrderObserver.php` — `Order::created` hook + `AppServiceProvider::boot()` ro'yxatlash
14. `LocationEventController` — exit event keldida ish vaqti ichida bo'lsa `PushService::sendToUsers(addelkadirs, ...)`
15. `app/Console/Kernel.php` — schedule entries
16. tw1.ru hosting'da cron sozlash (`schedule:run`)

→ Test: artisan command'larni qo'lda ishga tushirish, push borishini Firebase Console'da kuzatish.

### Bosqich D — Mobile FCM qaytarish (kichik APK)
17. `npm install @react-native-firebase/app@^19 @react-native-firebase/messaging@^19`
18. `android/build.gradle` + `app/build.gradle` Google Services plugin qaytarish
19. `mobile/src/push/fcm.ts` haqiqiy versiya (foreground + background handlers)
20. `App.tsx`'da `onForegroundMessage` listener
21. `index.js`'da `setupBackgroundHandler`

→ APK build (`assembleRelease`) + telefonga o'rnatish + login qilish → FCM token serverda `chef_devices` jadvalida ko'rinishi kerak.

### Bosqich E — Background geofence (eng murakkab qism)
22. `npm install react-native-background-geolocation` (kommersial litsenziya — bu paket bepul, lekin Pro versiya pulli)
23. `AndroidManifest.xml` ga yangi permissions
24. `mobile/src/attendance/backgroundLocation.ts`
25. Native modul: `heartbeat/HeartbeatModule.kt` + AlarmManager
26. `mobile/src/attendance/heartbeatScheduler.ts`
27. `AttendanceScreen` — check-in/out callback'larda start/stop
28. Battery optimization onboarding modal

→ APK build + real-device test (pilot oshpaz bilan): check-in qilib bog'chadan chiqadi, 30 sekund ichida exit event Addelkadir UI'da ko'rinishi kerak. 30 daqiqa kutib beacon event paydo bo'lishini ham tekshirish.

### Bosqich F — To'liq integratsiya testi
29. Pilot oshpaz 1 to'liq ish kunini ishlatadi
30. Loglar tekshiriladi: nechta exit, nechta beacon, nechta push, qaysi failed
31. Battery drain o'lchovi: telefon Sozlamalar → Batareya → ChefMobile foizi
32. Xiaomi/Huawei telefonlarda alohida tekshiruv

**Jami ~32 task.** Subagent-driven workflow'da har bosqich 1-2 task batch'i. To'liq tugatish: 2-3 hafta (pilot bilan parallel).

## 8. Testing strategy

### 8.1 Unit testlar
- **`PushServiceTest`** (PHPUnit) — FCM SDK'ni mock qilib, bo'sh token, NotRegistered javobi, ko'p user bilan batch send
- **`LocationEventControllerTest`** — exit trigger ish vaqtida push chaqirilishi, ish vaqti tashqarida chaqirilmasligi, soxta GPS event'lari rad etilishi
- **`NotifyMissingCheckInTest`** — bugun check-in qilmagan chef'lar to'g'ri ro'yxatlanishi, push to'g'ri Addelkadirlarga ketishi
- **Mobile**: `backgroundLocation.test.ts` — start/stop chaqiruvlari, geofence event handler attendanceQueue'ga to'g'ri qo'shishi

### 8.2 Integration testlar
- `SendMorningRemindersTest` — barcha CHEF role'lar ro'yxatlanishi, FCM token'siz user'lar o'tkazib yuborilishi
- `OrderObserverTest` — yangi Order yaratilganda push triggerlanishi

### 8.3 Real-device manual test plan

To'liq scenariy (siz qilasiz, pilot oshpaz bilan):

1. **Check-in + foreground service** — Chef check-in qiladi → notification panelda "Davomat kuzatuvi faol" ko'rinadi
2. **Geofence-exit** — Chef bog'chadan 250m masofaga chiqadi → 30 sekund ichida `exit` event Addelkadir UI'da paydo bo'ladi, **push xabar Addelkadir telefoniga keladi**
3. **Geofence-enter** — Chef qaytib kiradi → `enter` event paydo bo'ladi
4. **Beacon (heartbeat)** — 30 daqiqa kutiladi → `beacon` event paydo bo'ladi
5. **Morning reminder** — Ertasi kuni 08:00 — chef telefoniga eslatma push keladi
6. **Missing check-in** — Soat 09:15 da chef hali check-in qilmagan bo'lsa — Addelkadirlarga "X ta oshpaz hali kelmadi" push keladi
7. **New order** — Texnolog admin panel'dan yangi buyurtma qo'shadi → oshpazga push keladi
8. **Check-out** — Chef check-out qiladi → foreground service to'xtaydi, notification panel'dan yo'qoladi
9. **Restart resilience** — Telefon o'chirib yoqilsa, agar bugun check-in bor + check-out yo'q bo'lsa, tracking avtomatik tiklanishi
10. **Battery drain** — 8 soat ishchi kun'dan keyin ChefMobile batareya iste'moli ~3-5% (qabul qilinadigan)

## 9. Asosiy risklar

| Risk | Ehtimoli | Yengillik strategiyasi |
|---|---|---|
| **Xiaomi/Huawei fon servisni o'chiradi** | Yuqori | (1) Heartbeat fallback — agar geofence-exit kelmasa, kelgusi heartbeat'da outside aniqlanadi. (2) Battery optimization onboarding modal. (3) README'da OEM-specific qo'shimcha qadamlar (MIUI Autostart, Huawei Protected Apps). |
| **FCM token muddati tugaydi yoki user ilovani o'chiradi** | O'rta | App har ochilganda token yangilanadi va `/auth/device` ga jo'natiladi. PushService NotRegistered javobida tokenni `chef_devices` jadvalidan o'chiradi. |
| **Battery drain shikoyatlari** | O'rta | Heartbeat aniqlik 10m (juda yuqori aniqlik kerak emas). Foreground service notification doim ochiq (qonuniy, OS talab qiladi). Pilot davomida o'lchov olinadi, kerak bo'lsa interval 30 → 60 daqiqaga ko'tariladi. |
| **tw1.ru cron sozlash murakkab bo'lsa** | Past | Alternativ: serverda `while true; sleep 60; php artisan schedule:run; done` background process (screen yoki systemd unit ostida). |
| **`Order::created` observer mavjud kodga ta'sir qilsa** | O'rta | Observer faqat yangi Order yaratilishida triggerlanadi, mavjud Order o'zgartirishlariga teginmaydi. Qo'shimcha try/catch — push xatosi Order yaratilishini bloklamasligi kerak. |
| **Service account JSON o'g'irlanishi** | Past, lekin og'ir | `.gitignore`'da, `storage/app/` (web ostida emas), 0600 chmod, monitoring (Firebase Console'da har oy hujum/anomaly tekshirish). |
| **Push spam (40 chef × har soatda 1 push)** | O'rta | Rate limit: bir foydalanuvchi soatiga 5 push'dan ko'p emas (Redis counter). Trigger #1 (exit) — 5 daq cooldown bitta chef uchun. |
| **Background geolocation native library hosting muammosi** | Past | Bu paket allaqachon RN ekotizimida 5+ yil, ishonchli. Build muammosi bo'lsa, alternativ: `expo-location` background mode'i (lekin Expo eject kerak). |

## 10. Bog'liqliklar va majburiyatlar

### Foydalanuvchi tomonidan kerakli ishlar:
- ✅ Firebase loyihasi yaratilgan (`chefmobile-ab8a1`)
- ✅ `google-services.json` yuklab olingan va `mobile/android/app/` da
- ❌ **Service account JSON yaratish** (Firebase Console → Project Settings → Service accounts → Generate new private key)
- ❌ Service account JSON serverga upload (`storage/app/firebase-credentials.json`)
- ❌ Server hosting'da cron sozlash (`* * * * * php artisan schedule:run`) — tw1.ru control panel orqali
- ❌ Pilot oshpaz tanlash (Xiaomi va Samsung telefon ideal, OEM farqlarni ko'rish uchun)

### Texnik kutuvchi qarorlar (implementation plan'da hal qilinadi):
- `react-native-background-geolocation` versiya pin (ehtimol 4.x, RN 0.74 mos)
- `@react-native-firebase/*` versiya (19.x yoki 20.x — RN 0.74 mos bo'lgan)
- Rate limiter implementatsiyasi (Redis vs database vs in-memory)
- Yangi buyurtma Eloquent modeli aniq nomi va joylashuvi (`Order` yoki `ProductOrder` — codebase'da grep qilinadi)
- Kelajakda kindgarden ↔ Addelkadir biriktirish jadvali kerak bo'lsa, alohida feature

## 11. YAGNI — Hozir QILMAYMIZ

Kelajakda foydali bo'lishi mumkin, lekin Plan 5'ga kirmaydi:

- iOS qo'llab-quvvatlash (Plan 5 faqat Android — Plan 2 davomi)
- Real-time xarita (Yandex/Google Maps SDK) — pilot uchun jadval yetarli
- Lokatsiya tarixini eksport qilish (CSV/Excel) — keyinroq alohida feature
- Geofence radiusini sozlash UI'si — kindgarden.geofence_radius column allaqachon bor, lekin UI'siz qoladi
- Chef'ga "Bog'chadan chiqib ketdingiz" o'zining push xabari — faqat Addelkadirga yetarli pilot uchun
- WebSocket bilan real-time admin panel — refresh button yetarli
- Lokatsiya hodisalari uchun audit log — chef_location_events jadvali allaqachon vaqt belgisini saqlaydi
- Heartbeat intervalni dinamik o'zgartirish (battery low'da 60 daq'ga ko'tarish) — keyingi versiyada
