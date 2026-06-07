# Plan 6 — Children Count Mobile Feature

**Sana:** 2026-06-01
**Asos:** Mavjud web funksiya — `ChefController::sendnumbers` + `resources/views/chef/home.blade.php` + `Nextday_namber` jadvali
**Holat:** Brainstormed, spec yozildi, plan yozish kutilmoqda

## 1. Maqsad va ko'lam

Oshpaz mobil ilovasi orqali har kuni bog'cha bolalar sonini yosh toifa bo'yicha bir marta yuborishi. Yuborilgandan keyin **mobilda tahrirlash yo'q** — agar xato bo'lsa, oshpaz texnologga murojaat qiladi (texnolog web orqali tuzatadi).

**Asos:** Hozir bu funksiya faqat web profilida (`/chef` Voyager sahifasi). Pilot oshpazlar bog'chada turib telefondan kun boshida kiritishlari mumkin bo'lishi kerak.

**Plan 6 ko'lamida:**
- Backend: 2 ta yangi `/api/v1/chef/children-count/*` endpoint
- Mobil: `HomeScreen` (hozir bo'sh placeholder) ga "Bolalar soni" karta
- Webga teginmaymiz (mavjud `chef/home` sahifasi o'z ishini qiladi, mobil va web ikkalasi ham xuddi shu DB'ga yozadi)

**Plan 6 dan tashqarida (YAGNI):**
- Mobilda tahrirlash UI'si — yuborilgandan keyin faqat ko'rsatish
- Workers count input — mavjud webda ham oshpaz kiritmaydi
- Tarix ko'rinishi mobilda — webda allaqachon to'liq tarix sahifasi bor (`/chef/children-count-history`)
- Offline navbat — bu feature uchun KISS, kun davomida (03-21) qayta urinish mumkin

## 2. Arxitektura ko'rinishi

```
  MOBIL (RN)                        BACKEND (Laravel 8)              DB
  ───────────                       ────────────────────             ──

  HomeScreen.tsx                    routes/api.php
  └─ ChildrenCountCard              POST /api/v1/chef/
       ├─ A. Input form               children-count
       ├─ B. Submitted view          GET  /.../today
       └─ C. Closed/blocked
            ↓                              ↓
       children-count-api.ts        ChildrenCountController
            ↓                              ↓
       react-query useQuery         ChildrenCountService
                                          ↓
                                      ChildrenCountWindow      ─┐
                                      (sof helper: 03-21,       │  Sof
                                       12 soat cooldown)        │  PHP
                                                                │  (testable)
                                          ↓                    ─┘
                                      Eloquent: Nextday_namber,
                                                 ChildrenCountHistory,
                                                 Notification
                                          ↓                          ↓
                                                                    DB
                                                                    (mavjud,
                                                                    web ham
                                                                    shu yerga
                                                                    yozadi)
```

**Asosiy qarorlar:**
- **Backend logikasi alohida servisga ko'chiriladi** (`ChildrenCountService`) — mavjud `ChefController::sendnumbers`'dagi inline mantiq mobil API uchun qayta ishlatiladi, lekin web controller'ga teginmaymiz (DRY pilot davomida; refactor ehtiyojidan tashqari).
- **`ChildrenCountWindow` sof helper** — 03-21 vaqt chegarasi + 12 soat cooldown logikasi DB'siz, local'da TDD bilan unit-test qilinadi.
- **Mobil offline navbat YO'Q** — feature uzoq vaqt oralig'iga ega (18 soat), tarmoq tiklanguncha kutish mumkin.
- **Vaqt logikasi serverda hal qiluvchi** — mobil UI qulaylik uchun mahalliy tekshiruv qiladi (tugmani disable), lekin to'g'rilash server tomonida.

## 3. Backend API

### 3.1 `GET /api/v1/chef/children-count/today`

**Maqsad:** Mobil ekranni boshqa yuklash uchun zarur bo'lgan barcha holat — bog'cha, yosh toifalari, vaqt oynasi, bugun yuborilganmi, agar yuborilgan bo'lsa hozirgi qiymatlar.

**Auth:** Sanctum (mavjud chef middleware).

**Response (200):**
```json
{
  "kindgarden": {
    "id": 12,
    "kingar_name": "Bog'cha 12"
  },
  "age_ranges": [
    { "id": 1, "age_name": "3-4 yosh" },
    { "id": 2, "age_name": "5-6 yosh" }
  ],
  "submitted": false,
  "today_counts": null,
  "submitted_at": null,
  "time_window": {
    "allowed": true,
    "from": "03:00",
    "to": "21:00",
    "current_tashkent": "10:32"
  },
  "nextday_ready": true
}
```

**`submitted=true`** holatida (oxirgi 12 soatda kamida bitta `ChildrenCountHistory` qatori bor):
```json
{
  ...,
  "submitted": true,
  "today_counts": { "1": 30, "2": 25 },
  "submitted_at": "2026-06-01T05:32:00Z",
  ...
}
```

**`nextday_ready=false`** holatida texnolog `Nextday_namber` qatorlarini yaratmagan — yuborish mumkin emas.

### 3.2 `POST /api/v1/chef/children-count`

**Body:**
```json
{
  "counts": { "1": 30, "2": 25 }
}
```

**Validatsiya (server tomon):**
1. Auth user role = CHEF
2. User'ning bog'chasi bor (via `user_kindgardens` pivot)
3. Joriy Tashkent vaqti 03:00 - 20:59 oralig'ida (21:00 — chegaradan tashqari)
4. Oxirgi 12 soatda bu bog'cha uchun `ChildrenCountHistory` qatori yo'q
5. Body'dagi har bir `age_id` bog'chaning yosh toifalariga tegishli (`kindgarden.age_range`)
6. Har bir count: integer ≥ 0
7. `Nextday_namber` qatorlari mavjud (kingar_name_id + king_age_name_id juftligi uchun)

**Muvaffaqiyat (200):** Yuborilgandan keyin `GET /today` bilan bir xil response qaytariladi (mobile o'zi qayta query qilmasin uchun).

**Xato status'lar (422 + JSON body):**
| `error` kodi | Sabab |
|---|---|
| `time_window_closed` | Joriy vaqt 03-21 oralig'idan tashqarida |
| `already_submitted_today` | 12 soat ichida yuborilgan |
| `nextday_not_ready` | Texnolog `Nextday_namber` yaratmagan |
| `invalid_age_for_kindgarden` | Body'dagi age_id bog'chaga tegishli emas |
| `kindgarden_not_assigned` | User'ning bog'chasi yo'q |

### 3.3 Backend file structure

| Fayl | Mas'uliyat | Amal |
|---|---|---|
| `app/Services/ChildrenCount/ChildrenCountWindow.php` | Sof helper: vaqt chegarasi + cooldown logikasi (Carbon based, DB'siz) | Create |
| `app/Services/ChildrenCount/ChildrenCountService.php` | Yuborish biznes mantiqi (validatsiya + history + notification + Nextday_namber update) | Create |
| `app/Http/Controllers/Api/V1/Chef/ChildrenCountController.php` | Endpoints + validation + ChildrenCountService chaqiruvi | Create |
| `app/Exceptions/ChildrenCount/*` | Custom exception sinflari (ChildrenCountException base + 5 ta konkret) | Create |
| `app/Exceptions/Handler.php` | `ChildrenCountException` uchun renderable callback (Plan 1'dagi `AttendanceException` pattern) | Modify |
| `routes/api.php` | 2 ta yangi route (chef middleware ostida) | Modify |
| `tests/Unit/Services/ChildrenCount/ChildrenCountWindowTest.php` | Sof helper testlari (local) | Create |
| `tests/Feature/Api/V1/Chef/ChildrenCountTest.php` | Endpoint feature testlar (serverda) | Create |

## 4. Mobile (RN) UI

### 4.1 Ekran tuzilishi

`HomeScreen.tsx` hozir 1 qatorli placeholder. Yangidan yoziladi:

```
┌──────────────────────────────────────┐
│ Bosh sahifa                          │
│                                      │
│  ┌─────────────────────────────────┐ │
│  │ Bolalar soni                    │ │  <- ChildrenCountCard
│  │ (3 ta holatdan biri)            │ │
│  └─────────────────────────────────┘ │
│                                      │
└──────────────────────────────────────┘
```

### 4.2 Karta — 3 ta holat

**A. Yuborish vaqti** (server: `submitted=false`, `time_window.allowed=true`, `nextday_ready=true`):

```
┌─────────────────────────────────┐
│ Bolalar soni                    │
│ Bog'cha 12 — 2026-06-01         │
├─────────────────────────────────┤
│ 3-4 yosh                        │
│ [        30        ]            │  <- number keyboard
│                                 │
│ 5-6 yosh                        │
│ [        25        ]            │
│                                 │
│ [ ✅  YUBORISH ]                │  <- yashil button, loading state
│                                 │
│ Vaqt: 03:00 - 21:00             │
└─────────────────────────────────┘
```

**B. Yuborilgan** (server: `submitted=true`):

```
┌─────────────────────────────────┐
│ Bolalar soni                    │
│ Bog'cha 12 — 2026-06-01         │
├─────────────────────────────────┤
│ ✅  Bugungi son yuborildi       │
│ 05:32                           │
│                                 │
│  • 3-4 yosh: 30                 │
│  • 5-6 yosh: 25                 │
│                                 │
│ ────────────────────────────    │
│ O'zgartirish kerak bo'lsa,      │
│ texnologga murojaat qiling.     │
└─────────────────────────────────┘
```

**C. Vaqt yopiq yoki nextday tayyor emas** (server: `time_window.allowed=false` yoki `nextday_ready=false`):

```
┌─────────────────────────────────┐
│ Bolalar soni                    │
├─────────────────────────────────┤
│ ⏰  Hozir yuborish mumkin emas  │
│                                 │
│ Sabab: <birinchi mos sabab>     │
│                                 │
│ Vaqt: 03:00 - 21:00 oralig'ida  │
│ Hozir: 22:15                    │
└─────────────────────────────────┘
```

Sabab matnlari:
- `time_window_closed`: "Hozir yuborish vaqti emas (03:00 - 21:00 oralig'ida bo'lishi kerak)"
- `nextday_not_ready`: "Texnolog kelajak kun uchun menyu sozlamagan. Texnologga murojaat qiling."

### 4.3 Mobile file structure

| Fayl | Mas'uliyat | Amal |
|---|---|---|
| `mobile/src/api/childrenCount.ts` | API client: `getToday()`, `submit(counts)` | Create |
| `mobile/src/screens/HomeScreen.tsx` | Card'ni mount qiladi (placeholder o'rniga) | Modify |
| `mobile/src/components/ChildrenCountCard.tsx` | 3 ta holatni boshqaruvchi karta | Create |
| `mobile/src/components/ChildrenCountInputs.tsx` | Yosh toifalar bo'yicha input'lar (A holat) | Create |

`react-query` (mavjud) bilan `useQuery(['children-count/today'])` + `useMutation(submit)`. Submit muvaffaqiyatli bo'lganda `queryClient.invalidateQueries(['children-count/today'])` — UI avtomatik B holatga o'tadi.

Xato'lar: oddiy `ErrorBanner` (mavjud komponent) bilan ko'rsatiladi. `mapServerError` ([api/errors.ts](mobile/src/api/errors.ts)) ga 5 ta yangi error code qo'shiladi.

## 5. Vaqt va cooldown logikasi (ChildrenCountWindow)

Sof helper — DB tegmaydi, faqat Carbon. Mahalliy TDD bilan testlanadi.

```php
class ChildrenCountWindow
{
    public function isAllowedNow(Carbon $now): bool;       // 03 <= hour < 21 (Asia/Tashkent)
    public function nextOpensAt(Carbon $now): Carbon;       // ergangi 03:00
    public function isInCooldown(Carbon $lastSubmit, Carbon $now): bool;  // 12 soat
}
```

| Stsenariy | `isAllowedNow` | `isInCooldown` (lastSubmit) |
|---|---|---|
| Hozir 10:00 | true | false (agar lastSubmit yo'q) |
| Hozir 22:00 | false | — |
| Hozir 02:59 | false | — |
| Hozir 03:00 | true | — |
| Hozir 20:59 | true | — |
| Hozir 21:00 | false (chegaradan tashqari) | — |
| Hozir 10:00, lastSubmit 05:00 (5 soat oldin) | true | true (12 soat tugamagan) |
| Hozir 10:00, lastSubmit 03:00 oldingi kun (31 soat oldin) | true | false |

## 6. Schema o'zgarishlari

**Yangi migration kerak emas.** Mavjud jadvallarni qayta ishlatamiz:
- `nextday_nambers` — yuborilganda update
- `children_count_histories` — append-only audit
- `notifications` — texnolog uchun

`Notification::createChildrenCountChangeNotification(...)` mavjud static method mavjud (web ham ishlatadi).

## 7. Testing strategy

### Mahalliy (local'da ishlaydi)
- **Unit:** `ChildrenCountWindowTest` — 8-10 ta keys: chegaralar, kun chegarasi (00-03), cooldown sinishlar
- Har PHP fayl uchun `php -l` syntax check
- Mobile: ChildrenCountCard render'ini mock API bilan tekshirish (3 holat)

### Serverda (MySQL bilan)
- `ChildrenCountTest` feature:
  - GET today returns expected payload structure for fresh kindergarten
  - POST happy path → DB ga history + Nextday_namber update + Notification yaratilishi
  - 422 `time_window_closed` (Carbon::setTestNow ishlatib)
  - 422 `already_submitted_today` (12 soat ichida ikkinchi POST)
  - 422 `nextday_not_ready` (Nextday_namber qatorisiz)
  - 422 `invalid_age_for_kindgarden` (boshqa bog'cha age_id'si)

### Real-device manual
1. Oshpaz ilovani 10:00 da ochadi → A holat (input'lar) ko'rinadi
2. Sonlarni kiritib YUBORISH bosadi → B holat (yuborildi)
3. Pull-to-refresh → B holat saqlanadi
4. Web admin tomondan `ChildrenCountHistory` jadvalida yangi qator borligi tasdiqlanadi
5. 22:00 da ilovani qayta ochsa → C holat (vaqt yopiq)
6. Ertasi kun 09:00 → A holat (yangi kun, yangi yuborish)

## 8. Build sequence

5 ta vazifa, har biri o'z-o'zicha deploy qilinmaydi (mobil yaxlit feature), lekin backend va mobile alohida batch'lar:

| # | Task | Bog'liqlik | Test joyi |
|---|---|---|---|
| 1 | Backend: `ChildrenCountWindow` helper + unit testlar | Yo'q | Local |
| 2 | Backend: `ChildrenCountService` + exception'lar | 1 | (server) |
| 3 | Backend: `ChildrenCountController` + 2 route + feature test + Handler renderable | 1, 2 | Server |
| 4 | Mobile: `childrenCount.ts` API + `ChildrenCountCard` + `HomeScreen` qayta yozish | 3 | APK |
| 5 | Server deploy + APK build + brauzer/telefon smoke test | 3, 4 | Production |

## 9. Risklar

| Risk | Ehtimoli | Yengillik |
|---|---|---|
| `user_kindgardens` pivot orqali bog'chani topish — har user 1 ta bog'chaga teng deb taxmin qilamiz, lekin pivot many-to-many | O'rta | `->first()` bilan birinchisini olamiz (web ham shu yo'l). Bir oshpaz ikki bog'chaga belgilangan bo'lsa, bu pilot'da paydo bo'lishi mumkin emas — keyinroq alohida feature. |
| Mavjud webga ta'sir | Past | Web `ChefController::sendnumbers` ga teginmaymiz. Mobil API alohida controller. Bir xil DB'ga yozish — `ChildrenCountService` orqali markazlashgan. |
| `Nextday_namber` qatorlari noaniq holatda | O'rta | Server `nextday_ready=false` qaytaradi; mobile aniq xabar ko'rsatadi. Bu pilot'da paydo bo'lsa, texnolog sozlash kerakligini bilib oladi. |
| Submit double-tap (race) | Past | API `already_submitted_today` ni atomic tekshiradi: bitta DB transaction'da `ChildrenCountHistory` mavjudligini tekshirib, bo'sh bo'lsa yozadi. |
| 12 soat cooldown vs kun chegarasi | Past | Logikani aniq belgilaymiz: cooldown joriy yuborishdan 12 soat. Agar oshpaz 23:00'da yuborgan bo'lsa (vaqt yopiq emas u kuni — bu xato), keyingi kunning 11:00'gacha qayta yuborilmaydi. Lekin chegara 21:00 bo'lgani uchun 23:00'da hech qanday submit bo'lmaydi → ziddiyat yo'q. |
| Mobil number input'ida vergul/nuqta | Past | `type=number` + integer cast. Input'da hech qanday float qabul qilmaymiz. |

## 10. YAGNI — qilmaymiz

- Tahrirlash UI mobilda (texnologga murojaat)
- Tarix ko'rinishi mobilda (webda bor)
- Workers count input (texnolog sozlaydi)
- Sabab yozish maydoni — webdagi bilan parallel; mobil tomondan oshpaz tushuntirmaydi
- Push xabar texnologga (mavjud `Notification` jadvali yetarli; Plan 5b push tizimi keyingi versiyada bog'lanishi mumkin)
- iOS qo'llab-quvvatlash (Plan 2 davomi — faqat Android)
