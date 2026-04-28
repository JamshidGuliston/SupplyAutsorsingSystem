# Chef Mobile App — Dizayn hujjati

**Sana:** 2026-04-28
**Loyiha:** SupplyAutsorsingSystem
**Maqsad:** iOS va Android'da ishlovchi chef (oshpaz) mobil ilovasi va backend kengaytmasi
**Holat:** Brainstorming yakunlandi, implementation rejasi yozilmagan

---

## 1. Maqsad va kontekst

Mavjud `SupplyAutsorsingSystem` — Laravel asosidagi tizim, bog'chalarga oziq-ovqat ta'minotini boshqaradi. Tizimda bir necha rol bor: **technolog**, **chef** (oshpaz), **storage**, **boss**, **casher**, **accountant**, **buxgalter**.

Hozirda **chef** roli faqat web orqali ishlaydi. Bu hujjat:
1. Chef rol uchun **alohida mobil ilova** (iOS + Android) yaratishni belgilaydi.
2. Mavjud chef funksiyalarining hammasi mobilga ko'chiriladi.
3. **YANGI:** Davomat (attendance) tizimi qo'shiladi — selfi + GPS bilan kelish/ketish qayd qilinadi, oshpazlar geofence orqali nazorat qilinadi.
4. **YANGI:** Backend'da `addelkadir` deb ataluvchi yangi rol — barcha oshpazlarning davomatini va lokatsiyasini kuzatadi.

### 1.1 Hozirgi chef funksiyalari (mobil app qoplaydigan)

`app/Http/Controllers/ChefController.php` va `routes/web.php` ning `chef` prefiksli bloki bo'yicha:

1. Bosh sahifa — bugungi bolalar soni, mahsulotlar, menyu, kelgan zakaz
2. Keyingi kun bolalar sonini yuborish (`sendnumbers`)
3. Skladdan mahsulot ayirish (`minusproducts`)
4. Kelgan zakazni qabul qilish (`right`)
5. Sertifikatlar ro'yxati (`certificates`)
6. Bolalar soni tarixi (`children_count_history`)
7. Bolalar sonini qo'lda o'zgartirish (`updateChildrenCountByChef`)
8. Notification olish (mavjud `Notification` modeli orqali)

### 1.2 Yangi davomat tizimi qoidalari

- **Bir kun = 1 kelish + 1 ketish** (UNIQUE constraint).
- **Qat'iy geofencing**: oshpaz bog'cha radiusidan tashqarida bo'lsa, check-in/out **bloklanadi**. MVP'da radius hamma bog'chalar uchun **200m** (DB'da `kindgardens.geofence_radius` ustun mavjud — kelajakda har bog'chaga alohida sozlash uchun, lekin Faza 1 UI'da o'zgartirib bo'lmaydi).
- **Mock GPS bloklanadi** — `is_from_mock_provider` aniqlansa, server 422 xato qaytaradi.
- **Selfi shart**, faqat front kameradan, galereyaga ruxsat yo'q.
- **Qayta yuborish** ikki turi:
  - (i) eski yozuvni almashtirish (selfi/GPS noto'g'ri chiqdi) — geofence ichida bo'lishi shart
  - (ii) kechikkan yozuv qo'shish (oshpaz tongda yubormagan) — geofence ichida bo'lishi shart, `is_late=true` belgilanadi
- **Background lokatsiya kuzatuvi** — geofence-exit hodisasiga asoslangan: oshpaz check-in qilgandan keyin OS-level geofence ro'yxatdan o'tkaziladi, radius (200m) dan chiqsa hodisa yoziladi va Addelkadirga push yuboriladi. Check-out bilan to'xtaydi.
- **Offline rejim**: tarmoq yo'q bo'lsa, tadbir lokal queue'ga (mmkv) yoziladi va internet qaytganda avtomatik yuboriladi.

---

## 2. Texnik tanlov va arxitektura

### 2.1 Texnologiyalar

| Qism | Texnologiya | Sabab |
|---|---|---|
| Mobil ilova | **React Native** | Bitta JS/TS kod bazasi, iOS+Android, GPS/kamera/push uchun ishonchli kutubxonalar, Laravel Sanctum bilan oson |
| Backend | Mavjud **Laravel** (qo'shimcha API namespace) | Mavjud kod o'zgarmaydi |
| Auth | **Laravel Sanctum** (personal access token, 6 oy) | Mavjud `users` jadval, web va mobil bir xil credential |
| Database | Mavjud **MySQL** | 3 yangi jadval, mavjud bittasiga 3 ustun |
| Push notification | **FCM (Firebase Cloud Messaging)** | Bepul, Android+iOS |
| Selfi va lokatsiya | `react-native-image-picker`, `react-native-geolocation-service`, `react-native-background-geolocation` | |
| Lokal saqlash | `react-native-mmkv`, `react-native-keychain` (token uchun) | |
| Network | `axios` + `@tanstack/react-query` + `@react-native-community/netinfo` | |
| State | `zustand` | Bu o'lchamdagi loyihaga yetarli |
| Navigatsiya | `@react-navigation/native` (bottom-tabs + native-stack) | |
| Mobil crash reporting | Sentry | Bepul tier |

### 2.2 Arxitektura tanlovi: Variant 1 — alohida `/api/v1/` namespace

Mavjud `ChefController` (web) va shu kabi controllerlar **o'zgarmaydi**. Yangi `app/Http/Controllers/Api/V1/Chef/*` controllerlari yoziladi va JSON qaytaradi. Davomat business logic alohida `App\Services\AttendanceService`'da, web va API ikkalasi shu service'ni ishlatish potensialiga ega bo'ladi (lekin web hozircha tegmaydi).

**Sabab:** Mavjud web sayt buzilish xavfini bartaraf etadi, mobil tezroq yetkaziladi, kelajakda bosqichma-bosqich refaktor qilish mumkin.

### 2.3 Yuqori darajadagi arxitektura

```
┌──────────────────────┐     ┌──────────────────────┐
│ Chef Mobile App      │     │ Mavjud Web Sayt      │
│ (React Native)       │     │ + YANGI Addelkadir   │
│ - Login              │     │   paneli             │
│ - Davomat            │     └──────────┬───────────┘
│ - Bosh sahifa        │                │
│ - Notification       │                │
│ - Lokal queue        │                │
│ - Geofence kuzatuv   │                │
└──────────┬───────────┘                │
           │ HTTPS / JSON                │ HTTPS / Blade
           ▼                             ▼
   ┌────────────────────────────────────────────┐
   │         Laravel Backend                    │
   │  ┌──────────────────┐ ┌──────────────────┐ │
   │  │ Mavjud (no-op)   │ │ YANGI            │ │
   │  │ - web.php        │ │ - api.php /v1/   │ │
   │  │ - ChefController │ │ - Api\V1\Chef\*  │ │
   │  │ - boshqalar      │ │ - AttendanceSvc  │ │
   │  │                  │ │ - PushService    │ │
   │  │                  │ │ - AddelkadirCtrl │ │
   │  └──────────────────┘ └──────────────────┘ │
   └────────────────┬───────────────────────────┘
                    ▼
            ┌────────────────┐
            │ MySQL DB       │
            │ + 3 yangi jadval│
            │ + ustunlar     │
            └────────────────┘
                    +
            ┌────────────────┐    ┌──────────────────┐
            │ FCM (push)     │    │ storage/private  │
            │                │    │ /attendance/...  │
            └────────────────┘    └──────────────────┘
```

---

## 3. Database o'zgarishlar

### 3.1 Mavjud `kindgardens` jadvaliga ustunlar

```sql
ALTER TABLE kindgardens
  ADD COLUMN lat DECIMAL(10,7) NULL,
  ADD COLUMN lng DECIMAL(10,7) NULL,
  ADD COLUMN geofence_radius INT NOT NULL DEFAULT 200;
```

`lat`/`lng` to'ldirilmaguncha shu bog'chaga biriktirilgan oshpazlar mobil orqali davomat qila olmaydi (UI'da xato xabari ko'rsatiladi).

### 3.2 Yangi jadval: `chef_attendances`

```sql
CREATE TABLE chef_attendances (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id BIGINT UNSIGNED NOT NULL,
  kindgarden_id BIGINT UNSIGNED NOT NULL,
  date DATE NOT NULL,

  check_in_at DATETIME NULL,
  check_in_lat DECIMAL(10,7) NULL,
  check_in_lng DECIMAL(10,7) NULL,
  check_in_distance_m INT NULL,
  check_in_selfie_path VARCHAR(255) NULL,
  check_in_is_late TINYINT(1) NOT NULL DEFAULT 0,
  check_in_replaced_count INT NOT NULL DEFAULT 0,

  check_out_at DATETIME NULL,
  check_out_lat DECIMAL(10,7) NULL,
  check_out_lng DECIMAL(10,7) NULL,
  check_out_distance_m INT NULL,
  check_out_selfie_path VARCHAR(255) NULL,
  check_out_replaced_count INT NOT NULL DEFAULT 0,

  created_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL,

  UNIQUE KEY uniq_user_date (user_id, date),
  KEY idx_kindgarden_date (kindgarden_id, date),
  CONSTRAINT fk_att_user FOREIGN KEY (user_id) REFERENCES users(id),
  CONSTRAINT fk_att_kindgarden FOREIGN KEY (kindgarden_id) REFERENCES kindgardens(id)
);
```

### 3.3 Yangi jadval: `chef_location_events`

```sql
CREATE TABLE chef_location_events (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id BIGINT UNSIGNED NOT NULL,
  kindgarden_id BIGINT UNSIGNED NOT NULL,
  event_type ENUM('exit', 'enter', 'beacon') NOT NULL,
  happened_at DATETIME NOT NULL,
  lat DECIMAL(10,7) NOT NULL,
  lng DECIMAL(10,7) NOT NULL,
  distance_m INT NOT NULL,
  is_mock TINYINT(1) NOT NULL DEFAULT 0,
  created_at TIMESTAMP NULL,

  KEY idx_user_happened (user_id, happened_at),
  KEY idx_kindgarden_happened (kindgarden_id, happened_at),
  CONSTRAINT fk_evt_user FOREIGN KEY (user_id) REFERENCES users(id),
  CONSTRAINT fk_evt_kindgarden FOREIGN KEY (kindgarden_id) REFERENCES kindgardens(id)
);
```

### 3.4 Yangi jadval: `chef_devices`

```sql
CREATE TABLE chef_devices (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id BIGINT UNSIGNED NOT NULL,
  platform ENUM('android', 'ios') NOT NULL,
  fcm_token VARCHAR(255) NOT NULL,
  device_model VARCHAR(100) NULL,
  app_version VARCHAR(20) NULL,
  last_seen_at DATETIME NULL,
  created_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL,

  UNIQUE KEY uniq_user_token (user_id, fcm_token),
  CONSTRAINT fk_dev_user FOREIGN KEY (user_id) REFERENCES users(id)
);
```

Bir oshpaz bir vaqtning o'zida faqat **bitta qurilmadan** kirishi mumkin: yangi qurilmadan login qilinsa, eski yozuvlar va ularning Sanctum tokenlari avtomatik bekor qilinadi.

### 3.5 `addelkadir` rolini qo'shish

Mavjud loyiha rol tizimi `users.role_id` integer ustun orqali ishlaydi:
- 2 = boss, 3 = technolog, 4 = storage, 5 = accountant, 6 = chef, 7 = casher

Yangi `addelkadir` roli **role_id = 8** sifatida qo'shiladi. `isAddelkadirMiddleware` (mavjud `isChefMiddleware` namunasiga ko'ra) `Auth::user()->role_id == 8` ni tekshiradi. `RedirectIfAuthenticated.php` ham yangilanib, `addelkadir` login qilganda `addelkadir.home`'ga yo'naltiriladi.

---

## 4. Backend API endpoint'lari

Yangi `routes/api.php` faylida (Laravel Sanctum auth middleware bilan):

### 4.1 Auth

| Method | Path | Maqsad |
|---|---|---|
| POST | `/api/v1/auth/login` | email + parol → token |
| POST | `/api/v1/auth/logout` | tokenni bekor qilish |
| POST | `/api/v1/auth/device` | FCM token, platform, device_model, app_version ro'yxati |

### 4.2 Chef — mavjud funksiyalar JSON variantida

| Method | Path | Web ekvivalent |
|---|---|---|
| GET | `/api/v1/chef/home` | `chef.home` |
| GET | `/api/v1/chef/certificates` | `chef.certificates` |
| GET | `/api/v1/chef/notifications` | mavjud `Notification` |
| POST | `/api/v1/chef/notifications/{id}/read` | yangi |
| GET | `/api/v1/chef/children-count-history` | `chef.children_count_history` |
| POST | `/api/v1/chef/send-numbers` | `chef.sendnumbers` |
| POST | `/api/v1/chef/update-children-count` | `chef.update_children_count_by_chef` |
| POST | `/api/v1/chef/minus-products` | `chef.minusproducts` |
| POST | `/api/v1/chef/orders/{id}/accept` | `chef.right` |

### 4.3 Davomat (yangi)

| Method | Path | Maqsad |
|---|---|---|
| POST | `/api/v1/chef/attendance/check-in` | multipart: photo + JSON {lat, lng, captured_at, is_mock} |
| POST | `/api/v1/chef/attendance/check-out` | xuddi shunday |
| POST | `/api/v1/chef/attendance/replace` | {type: 'check_in'\|'check_out'} + photo+GPS, eski yozuvni almashtiradi yoki kechikkan yozuv qo'shadi |
| GET | `/api/v1/chef/attendance/today` | bugungi holat |
| POST | `/api/v1/chef/location-events` | batch upload (offline queue uchun) |

### 4.4 Server xato javoblari (toza format)

```json
{
  "error": "outside_geofence",
  "message": "Bog'chadan 547m uzoqdasiz",
  "distance_m": 547,
  "max_radius_m": 200
}
```

Xato kodlari: `outside_geofence`, `mock_gps_detected`, `already_checked_in`, `already_checked_out`, `kindgarden_coords_not_set`, `invalid_credentials`, `validation_error`, `rate_limited`.

### 4.5 Rate limiting

| Endpoint | Limit |
|---|---|
| `/auth/login` | 5/min |
| `/chef/attendance/*` | 30/min |
| `/chef/location-events` | 60/min |
| Boshqa `/chef/*` | 60/min (default) |

---

## 5. Backend servislar

### 5.1 `App\Services\AttendanceService`

```
checkIn(User $user, UploadedFile $photo, float $lat, float $lng,
        DateTime $capturedAt, bool $isMock): ChefAttendance
checkOut(...): ChefAttendance
replace(User $user, string $type, UploadedFile $photo,
        float $lat, float $lng, DateTime $capturedAt, bool $isMock): ChefAttendance
recordLocationEvents(User $user, array $events): int
```

Vazifalari:
- Bog'chaning lat/lng o'rnatilganligini tekshirish
- Haversine bilan masofani **server tomonidan qayta hisoblash**
- Mock GPS bloklash
- Geofence chegarasini majburlash
- `captured_at` server vaqtidan 5 daqiqadan ko'p farq qilsa rad etish
- Selfi rasmni `storage/app/private/attendance/YYYY-MM-DD/` ga saqlash
- DB yozuvini yaratish/yangilash
- Geofence-exit bo'lsa Addelkadirga push yuborish

### 5.2 `App\Services\PushService`

FCM SDK orqali notification yuborish wrapperi. Mavjud `Notification` modelidagi xabarlar shu service orqali push'ga aylanadi (mobil va web ikkalasiga).

### 5.3 Custom exception sinflari

`MockGpsDetectedException`, `OutsideGeofenceException`, `AlreadyCheckedInException`, `AlreadyCheckedOutException`, `KindgardenCoordsNotSetException`. Hammasi `app/Exceptions/Handler.php` orqali izchil JSON xatosiga aylantiriladi.

---

## 6. Mobile app strukturasi

### 6.1 Bottom tabs (4 ta)

1. **Bosh** — bugungi bolalar soni, mahsulotlar, kelgan zakaz
2. **Davomat** — Keldim/Ketdim tugmalari, bugungi holat, so'nggi 7 kun tarixi
3. **Xabarlar** — Notification ro'yxati, oqilmagan badge
4. **Boshqa** — qolgan funksiyalar (sendnumbers, minus-products, history, certificates, profile, settings)

### 6.2 Onboarding oqimi

```
1. Login (email + parol) → token saqlanadi (keychain)
2. Ruxsatlar so'raladi ketma-ket (location, camera, notification)
   - Location uchun: avval "While in use", keyin tushuntirib "Always Allow" so'raladi
3. FCM token olinadi → POST /auth/device
4. Bosh sahifaga o'tiladi
```

### 6.3 Davomat (check-in) batafsil oqimi

```
1. Foydalanuvchi "Keldim" bosadi
2. App GPS oladi (high accuracy, 10s timeout)
3. Mock GPS aniqlovchi tekshiruv
4. Front kamera ochiladi (faqat front), foydalanuvchi rasm oladi
5. Lokal queue'ga (mmkv) yoziladi
6. POST /api/v1/chef/attendance/check-in (multipart)
7. Server validatsiya qiladi → INSERT
8. UI yashil belgini ko'rsatadi: "✅ Keldim — 08:14, 45m bog'cha markazidan"
9. OS-level geofence ro'yxatdan o'tkaziladi (background exit/enter event'lari uchun)
```

Xato yo'llar:
- Mock GPS → "Soxta GPS aniqlandi. GPS Joystick va shu kabi ilovalarni o'chiring."
- Geofence tashqarida → "Bog'chadan 547m uzoqdasiz."
- GPS topilmadi (10s) → "GPS topilmadi. Tashqariga chiqing yoki Wi-Fi yoqing."
- Allaqachon kelgan → "Qayta yuborish" tugmasi ko'rsatiladi

### 6.4 "Qayta yuborish" mantig'i

**Tur i — almashtirish:** Bugungi yozuv mavjud, oshpaz "Qayta yuborish" bosadi. Yangi selfi+GPS olinadi, eski selfi rasm o'chiriladi, yangisi saqlanadi, `replaced_count++`. Geofence va mock GPS yangidan tekshiriladi.

**Tur ii — kechikkan yozuv:** Bugungi yozuv yo'q, kun yarmidan keyin oshpaz "Kechikkan kelishni qo'shish" bosadi. Yozuv qo'shiladi, `is_late = true`. **MUHIM:** Geofence qat'iy — oshpaz hozir bog'chada bo'lishi shart.

### 6.5 Background geofence-exit kuzatuvi

`react-native-background-geolocation` orqali:
- Check-in muvaffaqiyatli bo'lganda → bog'cha lat/lng + radius (200m) bilan geofence ro'yxatdan o'tkaziladi
- Telefon OS o'zi kuzatadi (deyarli 0% batareya)
- Exit/enter event lokal queue'ga, internet bo'lganda batch yuboriladi
- Backend Addelkadirga push: "Oshpaz Akmal — 11:23 da bog'chadan chiqib ketdi"
- Check-out muvaffaqiyatli bo'lganda geofence kuzatuvi o'chiriladi

### 6.6 Lokal queue

**Queue'ga tushadi:** check-in, check-out, replace, location-events, send-numbers, minus-products
**Queue'ga tushmaydi (faqat GET, react-query cache):** home, certificates, notifications, history

Queue holati UI'da: tab badge, "Sozlamalar > Yuborilmagan ma'lumotlar" sahifasi, "Hozir yubor" tugmasi.

Persistence: app o'chirilsa ham yo'qolmaydi (mmkv fayl). Telefon almashtirilsa yo'qoladi.

---

## 7. Addelkadir paneli (web)

### 7.1 Sahifalar

| Path | Maqsad |
|---|---|
| `/addelkadir/home` | Bugungi dashboard: jami/keldi/kechikdi/kelmadi statistika, oshpazlar ro'yxati holat bilan |
| `/addelkadir/attendance` | Davomat tarixi, sana oraliq filtri, selfi modal, Excel/PDF eksport |
| `/addelkadir/location-events` | Geofence exit/enter ro'yxati, xarita ko'rinishi (Leaflet/OpenStreetMap), mock GPS alohida tab |
| `/addelkadir/chef/{id}` | Oshpaz batafsil: profil, 30 kun davomat kalendar, lokatsiya hodisalari xaritada |
| `/addelkadir/kindgardens` | Bog'chalar va geofence sozlamalari — xarita orqali markaz nuqtani belgilash, radius (default 200m), bulk Excel import |
| `/addelkadir/chefs` | Hammasi ro'yxat: bog'cha, oxirgi faollik, qurilma, app versiyasi |
| `/addelkadir/reports` | Oylik hisobotlar, kechikish reytingi, eksport |

### 7.2 Real-time push (Addelkadirga)

- Mock GPS urinishi
- Oshpaz check-in qilgan, lekin geofence'dan chiqib ketdi
- Oshpaz soat 9:30 gacha kelmagan (Laravel Schedule kron)

MVP'da web push (`web-push` PHP paket) yoki oddiy polling (har 30 sek). Keyinchalik websocket (Laravel Reverb).

---

## 8. Xato boshqaruvi va offline

### 8.1 Mobile retry strategiyasi

- 5xx → 3 marta retry (eksponensial: 1s, 4s, 16s)
- 4xx (geofence, mock, validation) → queue'dan o'chiradi, foydalanuvchiga xato xabari
- Timeout (30s) → retry'ga qaytadi

### 8.2 Crash reporting

Sentry — bepul tier (5K event/oy). React Native va Laravel ikkalasi.

---

## 9. Xavfsizlik

### 9.1 Token va sessiya

- **Sanctum personal access token**, 6 oy amal qiladi
- Mobil ichida `react-native-keychain` (iOS Keychain / Android Keystore) — `AsyncStorage` emas
- 401 → login ekraniga qaytariladi
- Bir oshpaz = bir aktiv qurilma (yangi login eski tokenni bekor qiladi)
- Foydalanuvchi parolini o'zgartirsa — barcha tokenlar bekor

### 9.2 Server tomonida himoya (replay attack)

- `is_mock` flag'iga ishonib qolmaymiz
- `captured_at` server vaqtidan 5 daqiqadan ko'p farq qilsa rad etiladi
- Selfi rasm hash'i loglanadi (takror yuborish aniqlanadi)
- Geofence majburan server'da Haversine bilan qayta hisoblanadi
- Mock GPS — Android `Location.isFromMockProvider()` bilan native modul. **iOS**'da to'g'ridan-to'g'ri API yo'q, shuning uchun iOS uchun (Faza 3) qo'shimcha tekshiruvlar: GPS qisqa vaqt ichida juda katta sakrash, vaqt zonasi nomuvofiqligi, accelerometer bilan harakat tekshiruvi (heuristic, 100% ishonchli emas).

### 9.3 Selfi va lokatsiya saqlash

- `storage/app/private/attendance/YYYY-MM-DD/{user_id}_{type}_{timestamp}.jpg`
- **Public emas**, faqat Addelkadir signed URL (1 soat) orqali ko'radi
- Maxfiylik: davomat yozuvlari 2 yil; selfi rasmlar 6 oy keyin avtomatik o'chiriladi (`php artisan attendance:cleanup-photos` kron); lokatsiya hodisalari 6 oy

---

## 10. Test strategiyasi

### 10.1 Backend

- Feature testlar har endpoint uchun (`tests/Feature/Api/V1/Chef/*`):
  - `AttendanceCheckInTest` — happy path, geofence rejection, mock GPS rejection, duplicate prevention, replace, late entry
  - `LocationEventsTest`, `LoginTest`
- Unit testlar: `AttendanceServiceTest` (Haversine), `PushServiceTest` (FCM mock)
- Coverage maqsadi: yangi kod 70%+

### 10.2 Mobile

- Component testlar (Jest + React Native Testing Library)
- Integration testlar (API mock bilan)
- E2E (Detox) faqat 3 oqim: login, check-in, check-out
- Manual matritsa: Android 8/10/12/14, Samsung/Xiaomi/Realme

### 10.3 Maxsus sinovlar

- Mock GPS bilan urinish ("Fake GPS Location" ilovasi orqali) → bloklanishi shart
- Airplane mode'da check-in → queue'da turishi va keyin yuborilishi
- Haqiqiy bog'chada borib geofence-exit/enter test qilish

---

## 11. Rollout rejasi

### Faza 1 — MVP (taxminan 6-8 hafta)
- Backend: DB migratsiyalar, API endpoint'lar, AttendanceService, AddelkadirController
- Mobile (Android): Login, Bosh sahifa, Davomat, Notification
- Addelkadir paneli: Bosh dashboard + davomat tarix + bog'cha geofence sozlamalari
- 2-3 ta pilot bog'cha

### Faza 2 — Kengaytirish (3-4 hafta)
- Mobile: send-numbers, minus-products, orderni qabul qilish, sertifikatlar, history
- Addelkadir: lokatsiya xaritasi, hisobotlar, eksport
- Background geofence-exit kuzatuvi
- Pilot feedbackdan tuzatish

### Faza 3 — To'liq tarqatish + iOS (2-3 hafta)
- Hammasiga yoyish
- iOS versiyasi (TestFlight → keyin App Store)
- Push notification turli holatlar uchun

### Faza 4 — Kelajakda (rejada yo'q)
- Texnolog/sklad uchun mobil
- Voice notification
- Offline xarita
- Websocket real-time (Laravel Reverb)

---

## 12. Migratsiya — eski tizimdan yangiga

- 3-6 oy davomida web va mobil **parallel ishlaydi**
- Mobil ishonchli ekanligi tasdiqlangach, web chef paneli `read-only` rejimga
- Keyin web chef paneli o'chirilishi mumkin (qaror keyinroq)

**Bog'cha lat/lng birinchi qadam:** Addelkadir 40+ bog'chani Excel orqali bulk import qilib to'ldiradi yoki har birini xaritada qo'lda belgilaydi. Bu tugamasdan mobil davomat ishlamaydi.

---

## 13. Xavf-xatarlar

| Xavf | Ehtimol | Ta'sir | Himoyalanish |
|---|---|---|---|
| Bog'cha geofence noto'g'ri sozlangan | O'rta | Yuqori | Pilot 2-3 bog'cha; har birini Addelkadir vizual tekshiradi |
| Mock GPS soxta pozitiv | Past | O'rta | Backend qo'shimcha tekshiruvlar (vaqt, takror selfi hash) |
| iOS App Store background location uchun rad etadi | O'rta | O'rta | Faza 1 faqat Android; iOS keyinroq |
| Oshpazlar mobilni ishlatishni bilmaydi | O'rta | O'rta | Sodda UI, video instruksiya, Telegram support |
| Xitoy telefonlari (Xiaomi, Huawei) FCM push'ni o'tkazib yuboradi | Yuqori | O'rta | Push'ga qo'shimcha har 5 daq polling; push faqat "tezroq ko'rsatish" |
| Selfi yuz emas (kim bo'lsa rasmga tushadi) | Past | Past (MVP) | Hozircha matn ogohlantirish + Addelkadir manual; keyinchalik face detection |

---

## 14. Muvaffaqiyat ko'rsatkichlari (KPI)

- 30 kunda pilot bog'chalardagi 90%+ oshpazlar mobil orqali check-in qiladi
- Davomat yozuvlarining 99%+ to'g'ri yetkazib beriladi (queue ishonchliligi)
- Mock GPS urinishi haqiqiy oshpazlardan < 5%
- App ishga tushish < 3 sek, check-in oqimi < 30 sek

---

## 15. Aniqlanishi kerak bo'lgan masalalar (kelajakdagi)

Quyidagilar dizayn vaqtida yopilmagan, implementation rejasida hisobga olinishi yoki sizdan keyinroq tasdiqlash kerak:

1. **Tunda ishlovchi oshpazlar** — hozir hammasi kunduzi (07:00–18:00) deb taxmin qilingan. Agar tun smenasi bo'lsa, sutka chegarasi mantig'i o'zgartiriladi.
2. **Kechikkan kelish chegarasi vaqti** — "kechikkan yozuv" tugmasi soat nechadan keyin paydo bo'lishi kerak (masalan, 09:00 dan keyin)? Hozircha ma'muriy sozlama deb qoldiraylik.
3. **Domain va HTTPS sertifikat** — mobil APK build'idan oldin `https://...` bilan ishlovchi domen tayyor bo'lishi kerak.
4. **FCM Firebase loyihasi** — Google Cloud'da yaratilishi va `google-services.json` (Android) faylini olish kerak.
5. **Apple Developer hisobi** — iOS faza 3 da kerak ($99/yil).
6. **Sentry akkaunti** — bepul tier ham ro'yxatdan o'tish kerak.

---

## 16. Ro'yxat: nima yangi qo'shiladi

**Backend (mavjud kod o'zgarmaydi, faqat qo'shiladi):**
- `routes/api.php` (yangi)
- `app/Http/Controllers/Api/V1/Chef/{Auth,Home,Attendance,...}Controller.php`
- `app/Http/Controllers/AddelkadirController.php`
- `app/Http/Middleware/isAddelkadirMiddleware.php`
- `app/Services/{AttendanceService,PushService,DeviceService}.php`
- `app/Models/{ChefAttendance,ChefLocationEvent,ChefDevice}.php`
- `app/Exceptions/{MockGpsDetected,OutsideGeofence,...}Exception.php`
- `database/migrations/*.php` (kindgardens kengaytma + 3 yangi jadval)
- `resources/views/addelkadir/*.blade.php`
- `tests/Feature/Api/V1/Chef/*.php`, `tests/Unit/Services/AttendanceServiceTest.php`
- `php artisan attendance:cleanup-photos` kron buyrug'i
- `config/services.php` (FCM credentials)
- Sanctum konfiguratsiya

**Mobile (yangi loyiha):**
- React Native loyihasi `mobile/` papkada (yoki alohida repo)
- Asosiy ekranlar: Login, Home, Attendance, Notifications, Children Count Send, Children Count Update, Children Count History, Minus Products, Orders, Certificates, Profile, Settings
- Servislar: ApiClient (axios), AttendanceService (lokal queue), LocationService (geofence), CameraService, PushService (FCM), AuthService
- State: zustand stores, react-query keys
- Navigatsiya: bottom-tabs + native-stack

---

**Hujjat oxiri.**
