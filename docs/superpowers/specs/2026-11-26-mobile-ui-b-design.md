# Mobil ilova — "B: Brend sarlavha" dizayni

**Sana:** 2026-11-26
**Loyiha:** SupplyAutsorsingSystem — `mobile/` (Chef Mobile App, React Native 0.74.5)
**Branch:** `feature/mobile-ui-redesign`
**Oldingi hujjat:** [2026-07-25-mobile-ui-redesign-design.md](2026-07-25-mobile-ui-redesign-design.md) ("C1 — tipografik"). Bu hujjat uning **vizual qismini almashtiradi**; texnik qoidalari (tokenlar, primitiv komponentlar, `TextInput` faqat `Field` orqali, eski token nomlari uchun alias qoldirilmaydi) o'z kuchida qoladi.
**Tasdiqlangan maketlar:** [2026-11-26-mobile-ui-b-mockups/](2026-11-26-mobile-ui-b-mockups/) — 6 ta HTML fayl, har biri loyiha egasi bilan sahifama-sahifa ko'rib chiqilgan va tasdiqlangan:
`01-kirish.html` (B ustuni), `02-bosh.html`, `03-davomat.html`, `04-xabarlar.html` (A varianti), `05-profil.html`, `06-umumiy-holatlar.html`.

**Maket va matn munosabati:** maketlardagi ko'rinish (ranglar, o'lchamlar, joylashuv, matnlar) — **majburiy manba**. Ushbu matn ularni tushuntiradi va maketda ko'rinmaydigan holat va qoidalarni qo'shadi. Ziddiyat bo'lsa — ushbu matn ustun (u ma'lum bir maket xatosini ataylab tuzatgan bo'ladi, 11-bo'limga qarang). Maketlardagi ism, bog'cha nomi, guruh nomlari va **vaqtlar (08:00–10:00 kabi) — misol**; ilova haqiqiy qiymatlarni serverdan oladi.

---

## 1. Maqsad va sabab

Loyiha egasi ilova sahifalarini **"chiroyli va zamonaviy"** qilishni so'radi. Iyuldagi C1 dizayni (oq fon, karta va soya yo'q, faqat ingichka chiziqlar) juda vazmin chiqdi. Endi ilova birinchi qarashdayoq **Global Ta'm brendiniki** ekani bilinishi va zamonaviy mobil ilova kabi ko'rinishi kerak.

Foydalanuvchilar — bog'cha oshpazlari, ko'pincha arzon Android telefonda. Ilovani har kuni qisqa vaqt ishlatishadi: kelishni belgilash, bolalar sonini yuborish, ketishni belgilash. Dizayn shu uch ishni **tezroq va aniqroq** qilishi kerak, bezak uchun bezak emas.

## 2. Maqsadlar, maqsad bo'lmaganlar, anti-maqsadlar

**Maqsadlar**
- Beshala ekran (Kirish, Bosh, Davomat, Xabarlar, Profil), pastki tab panel, "Internet yo'q" belgisi va sessiya tiklanish ekrani B uslubida qayta chiziladi.
- Har bir ekranning **barcha** holatlari (yuklanish, xato, bo'sh, ochiq, yuborilgan, yopiq, navbatda...) yangi uslubda bo'ladi.
- Loyiha egasi tasdiqlagan kichik qo'shimchalar (★ bilan belgilangan) qilinadi.

**Maqsad emas (bu ishda qilinmaydi)**
- Xabarlar funksiyasi (server API, push). Xabarlar ekrani faqat chiroyli bo'sh holat oladi.
- Tungi rejim. Ilova doim yorug' (`27cd5e8` dagi majburiy yorug' mavzu saqlanadi).
- Serverga (Laravel) har qanday o'zgarish. Bu ish **faqat `mobile/`** ichida.
- Davomat va bolalar soni **biznes mantig'ini** o'zgartirish: GPS, masofa, selfi, navbat va uning yuborilishi (`attendanceFlusher`), validatsiya (bo'sh maydon xato bo'lib qoladi), `mapServerError`.
- Oflayn navbatdagi "eskirgan yozuv jimgina o'chiriladi" xatosi (12-bo'lim) — alohida ish.

**Bitta tasdiqlangan istisno (mantiqqa tegadi):** foydalanuvchi ma'lumoti (ism, email) telefonda saqlanadi va ilova qayta ochilganda tiklanadi — 5.7. Loyiha egasi rozi bo'lgan.

**Anti-maqsadlar (ishlasa ham — muvaffaqiyatsizlik)**
- Oshpazga GPS koordinata, `isMock`, HTTP status, JSON yoki inglizcha texnik xato matni ("Request failed with status code 500", "Network Error") ko'rinishi.
- `src/` da biror emoji qolishi (har telefonda boshqacha chiziladi).
- Kiritish maydonida oq fonda oq matn xatosining qaytishi.
- Yangi **native** paket qo'shilishi (RN 0.74 versiya qulfi — 8-bo'lim).
- 360×640 dp telefonda klaviatura ochiq paytda asosiy tugmaga yetib bo'lmasligi yoki birinchi bosish faqat klaviaturani yopishi (`749496c` xatosi).
- Oshpazga noto'g'ri ma'lumot ko'rsatish: noto'g'ri vaqt mintaqasi, "Bugun" deb kechagi yuborishni ko'rsatish, "hammasi yuborilgan" deb yolg'on tinchlantirish.

## 3. Qurish usullari (Approaches)

Loyiha egasi natija haqida gapirdi va qurish usulini bizga qoldirdi. Quyidagi tanlov — **bizning qarorimiz**.

| Usul | Qisqacha | Natija |
|---|---|---|
| **1. Mavjud token + primitiv tizimini kengaytirish** | `src/theme/` yangilanadi; `Hero`, `Sheet`, `Card`, `Pill`, `EmptyState`, `ConfirmDialog`, `Skeleton` kabi primitivlar qo'shiladi; `Button`/`Field`/`Banner`/`StatusLine` qayta stillanadi. Ekranlar faqat primitivlardan yig'iladi. | **Tanlandi.** Iyuldagi tizim aynan shu uchun qurilgan; tarqoq hardcode ranglar qaytmaydi; arxitektura testlari ishlashda davom etadi. |
| 2. Har ekranni o'z `StyleSheet` i bilan qayta chizish | Tez boshlanadi | Rad etildi — "har faylda o'z rangi" muammosini qaytaradi. |
| 3. Tayyor UI kutubxona (react-native-paper, tamagui, gluestack) | Tayyor komponentlar | Rad etildi — native/og'ir paketlar, RN 0.74 versiya qulfiga zid, kutubxona uslubi B ga qarshi. |

**Gradient:** yangi `react-native-linear-gradient` emas, **o'rnatilgan `react-native-svg@15.3.0`** ning `LinearGradient` i. CSS 150° burchak SVG koordinatalarida taxminan beriladi (aniq moslik shart emas).

## 4. Vizual til — B

### 4.1 Umumiy kompozitsiya
- Har ekran tepasida **marjon gradient "hero"** (`Hero`), uning ustidan pastdan **yumaloq varaq** (`Sheet`, yuqori radius 24; Kirishda 28) chiqib, hero'ning pastki ~22–28 dp ini yopadi.
- Gradient: `#F47A55 → #EF5B3C (45%) → #D9431F`, chap-yuqoridan o'ng-pastga. Bezak: o'ng yuqorida yarim shaffof oq doira (`rgba(255,255,255,.09)`); Kirish va Profilda chap pastda ikkinchi doira (`.07`). Doiralar oddiy `View` (radius) bilan.
- Hero balandligi **minimal** qiymat (kontent uzun bo'lsa o'sadi): Kirish ~250, Bosh/Davomat ~150, Profil ~200, Xabarlar ~120 dp (safe-area'siz).
- Varaq foni `#F7F6F4`, ichida oq kartalar (radius 18, yumshoq soya). Kirishda varaq oq.

### 4.2 Status bar va aylantirish
- Butun ilova uchun **bitta global** `<StatusBar barStyle="light-content" translucent backgroundColor="transparent" />` (`App.tsx`). Har ekranda alohida `StatusBar` qo'yilmaydi (tab ekranlar birga yashaydi, oxirgisi g'olib chiqadi).
- Hero kontent bilan **birga aylanadi** (pastga tortib yangilash tabiiy ishlaydi). Lekin ekran tepasida **status bar balandligida qotib turadigan marjon (`#F47A55`) tasma** bo'ladi — aylantirilganda ham oq soat/batareya belgilari doim marjon ustida o'qiladi.
- Sessiya tiklanish ekrani (`RootNavigator` dagi spinner): marjon gradient fon, o'rtada oq spinner.

### 4.3 Ranglar (yangi/o'zgargan tokenlar)
| Token | Qiymat | Vazifa |
|---|---|---|
| `brandGradient` | `['#F47A55','#EF5B3C','#D9431F']` | hero foni |
| `brandTop` | `#F47A55` | status bar ostidagi qotgan tasma |
| `brand` | `#EF5B3C` | faol tab, fokus chegarasi va ikonkasi, belgilar |
| `brandSoft` | `#FFE9E2` | faol tab kapsulasi, bo'sh holat doirasi, fokus halqasi |
| `canvas` | `#F7F6F4` | varaq foni |
| `card` | `#FFFFFF` | kartalar |
| `cardDivider` | `#F0EFED` | karta ichidagi qator chiziqlari |
| `cardFooter` | `#FAFAF9` | karta pastki "Jami" / "ish vaqti" qatori foni |
| `fieldBg` | `#F4F3F1` | raqam maydoni foni |
| `fieldBorder` | `#ECECEF` | matn maydoni chegarasi |
| `outlineBorder` | `#E2E1DE` | `outline` tugma chegarasi |
| `skeleton` | `#EEEDEA` | yuklanish "skelet" chiziqlari |
| `lineDone` / `linePending` | `#CFE8D9` / `#EEEDEB` | davomat vaqt chizig'i bog'lovchisi |
| `ink` | `#0D0D0D` | matn **va asosiy tugma foni** |

`ok/warn/danger` va ularning `Bg/Ink` juftlari saqlanadi. Ishlatilmay qolgan tokenlar o'chiriladi (alias qoldirilmaydi).

**Asosiy tugma endi qora** (`ink` fon, oq matn). Marjon — hero, faol tab, fokus va belgilar uchun. "Jami" **marjon emas** — `ink` rangda.

### 4.4 Komponentlar
- **`Button`**
  - `primary` — qora, balandlik 50–56, radius 14, matn 15/700.
  - `outline` — oq fon, 1.5 dp `outlineBorder`, qora matn; `size="sm"` balandligi ~44, matn 13.
  - `danger` — Profildagi "Hisobdan chiqish": oq fon, och qizil (`#F3D3CF`) chegara, `danger` matn, chapda chiqish ikonkasi.
  - `dangerSolid` — tasdiqlash oynasidagi "Ha, chiqaman": to'liq `danger` fon, oq matn.
  - `ghost` — chegarasiz, kulrang (`#55575c`) matn: "Bekor qilish".
  - `progress` holati (`{label, step, total}`) — 5.3 ga qarang.
- **`Field`** — balandlik ~50, radius 14, 1.5 dp `fieldBorder`; chapda ixtiyoriy ikonka. **Fokusda:** chegara va ikonka `brand`, atrofida 4 dp `brandSoft` halqa (RN 0.74 eski arxitekturada `boxShadow` spread yo'q — halqa `Field.tsx` ichidagi o'rovchi `View` bilan). `secureTextEntry` bo'lsa o'ngda ★ ko'z tugmasi (ko'rsatish/yashirish). `color`/`placeholderTextColor` majburiyligi saqlanadi.
- **Raqam maydoni** — `Field` ning varianti (arxitektura testi: `TextInput` faqat `Field.tsx` da): 74×40, `fieldBg`, raqam 17/800 markazda; fokusda oq fon + `brand` chegara + halqa. **Placeholder yo'q** (bo'sh maydon bo'sh ko'rinadi — 5.2).
- **`Banner`** — rangli yumaloq quti (radius 14) + chapda doira ichida ikonka. Uchta ton: `error` (`dangerBg`/qizil), `success` (`okBg`/yashil), `warn` (`warnBg`/sariq). Eski `info` ton `warn` ga birlashadi. Ko'rinishi — `06-umumiy-holatlar.html`.
- **`Pill`** — kichik yumaloq yorliq: "03:00–21:00 ochiq" (yashil), "kechikkan" (sariq), "Hozir: 14:25" (kulrang), hero ichida — oq shaffof.
- **Tab panel** — oq, balandlik ~62. Tepasida **chiziq emas, yumshoq soya**: Android `elevation` tepaga soya tashlamaydi, shuning uchun bir necha dp li SVG gradient tasma (yoki juda och ingichka chiziq). Faol tab: ikonka 48×28 `brandSoft` kapsulada, ikonka va yozuv `brand`; nofaol — `inkFaint`. `tabBarHideOnKeyboard: true`.
- **Ikonkalar** faqat SVG (`src/components/icons/`, mavjud stroke uslubida). Kerak bo'ladiganlar: email, qulf, ko'z, ko'z-yopiq, kalendar, kamera, soat, belgi (check), chiqish, uy, joy, qo'ng'iroq, foydalanuvchi, sinxron (navbat), internet-yo'q, undov.

## 5. Ekranlar

★ — loyiha egasi tasdiqlagan **yangi** funksional qo'shimcha.

### 5.1 Kirish — `01-kirish.html` (B ustuni), xato — `06`
- Hero: oq yumaloq kvadrat (64×64, radius 20) ichida **GT monogrammasi**, "Xush kelibsiz!", "Global Ta'm · Oshpaz kabineti".
- Oq varaq: Email (ikonka), Parol (qulf + ★ ko'z), qora "Kirish".
- ★ Pastda kulrang matn: **"Parolni unutdingizmi? Texnologga murojaat qiling"** (bosilmaydi).
- Xato — `Banner error` **maydonlar ustida**. Server qaysi maydon xatoligini aytmaydi, shuning uchun maydonlar qizarmaydi (06-maketdagi qizil parol chegarasi — maket xatosi, qilinmaydi).
- GT monogrammasi: `src/assets/` ga shaffof PNG. Manba `android/app/src/main/res/mipmap-xxxhdpi/ic_launcher_foreground.png` — adaptive-icon chetlari kesib olinadi.

### 5.2 Bosh sahifa (bolalar soni) — `02-bosh.html`, yuklanish/xato — `06`

**Hero** (barcha holatlarda bir xil):
- ★ Salomlashuv: **"Xayrli tong / Xayrli kun / Xayrli kech, {to'liq ism}"** (6-bo'lim). Ism bazada qaysi tartibda yozilgani noma'lum, shuning uchun **to'liq ism**, bir qatorda, sig'masa "…". Ism yo'q bo'lsa — "Xayrli tong!".
- Bog'cha nomi (`kindgarden.kingar_name`), katta, 2 qatorgacha.
- Sana yorlig'i kalendar ikonkasi bilan: "Payshanba, 26-noyabr".

**Holatlarni tanlash tartibi** (birinchi mos kelgani ko'rsatiladi):
1. Yuklanmoqda → 2. Yuklab bo'lmadi → 3. **Yuborilgan** (`submitted`) → 4. Menyu tayyor emas (`!nextday_ready`) → 5. Vaqt yopiq (`!time_window.allowed`) → 6. Yosh guruhlari yo'q (`age_ranges` bo'sh) → 7. Forma ochiq.

Hozirgi kodda "yopiq" "yuborilgan"dan oldin tekshiriladi; tartib **ataylab** o'zgartiriladi: server `submitted` ni "oxirgi 12 soat ichida yuborilgan" deb beradi (`ChildrenCountWindow::COOLDOWN_HOURS`), va yopiq holat birinchi bo'lsa "Ertaga … da ochiladi" yolg'on chiqishi mumkin. Bu faqat ko'rsatish tartibi — server va validatsiya o'zgarmaydi.

**Forma ochiq**
- "Bolalar soni" + "Ertangi menyu uchun"; o'ngda yashil `Pill` "{from}–{to} ochiq" (serverdan; hozir 03:00–21:00).
- Oq karta: har guruh — chapda nom (uzun bo'lsa o'raladi), o'ngda raqam maydoni. Bo'sh maydon **bo'sh** ko'rinadi, "0" yozilmaydi.
- ★ Karta ostida alohida qator: chapda "Jami" (`inkMuted`), o'ngda **"N bola"** (`ink`, 18/800). Faqat to'ldirilgan maydonlar qo'shiladi; hech biri to'ldirilmagan bo'lsa "—".
- `Banner error` (validatsiya/server xatosi) — karta va "Jami" orasida.
- Qora "Yuborish". Validatsiya **o'zgarmaydi**: barcha maydonlar to'ldirilishi shart (loyiha egasi tanlovi — bo'sh maydon 0 deb yuborilmaydi).

**Yuborilgan**
- Karta: katta yashil doira + belgi, "Yuborildi", ★ **"Bugun, HH:MM da"** yoki **"Kecha, HH:MM da"** — `submitted_at` ning Toshkent sanasi bugungi bilan solishtiriladi. `submitted_at` yo'q bo'lsa — faqat "Yuborildi".
- Ikkinchi karta: guruh — son; son yo'q bo'lsa "—". ★ Oxirgi qator `cardFooter` fonda: "Jami | N" ("—" lar qo'shilmaydi).
- Ostida: "O'zgartirish kerak bo'lsa, texnologga murojaat qiling."

**Menyu tayyor emas**
- Karta: sariq soat doirasi, sarlavha **"Menyu hali tayyor emas"**, matn "Texnolog kelajak kun uchun menyu sozlamagan. Texnologga murojaat qiling." Vaqt yorliqlari yo'q. Ostida "Pastga torting — yangilash".

**Vaqt yopiq**
- Karta: sariq soat doirasi, "Hozir yuborish vaqti emas", "Bolalar soni har kuni **{from} – {to}** oralig'ida yuboriladi".
- ★ Ikki kulrang `Pill`: "Hozir: {current_tashkent}" va keyingi ochilish — `current < from` → "Bugun {from} da ochiladi", `current >= to` → "Ertaga {from} da ochiladi" (`HH:MM` satrlarini solishtirish yetarli — server nol bilan to'ldiradi).
- Ostida "Pastga torting — yangilash".

**Yosh guruhlari yo'q** — "menyu tayyor emas" kartasi ko'rinishida: "Bog'changizga yosh guruhlari biriktirilmagan. Texnologga murojaat qiling." Tugma yo'q.

**Yuklanmoqda** (`06`, 3-telefon): hero'da salomlashuv va sana, bog'cha nomi o'rnida oq-shaffof skelet chiziq; varaqda skelet karta.
**Yuklab bo'lmadi** (`06`): markazda karta — qizil internet-yo'q doirasi, "Ma'lumotni yuklab bo'lmadi", "Internetni tekshirib, qayta urinib ko'ring.", `outline` "Qayta urinish". Server javob bergan bo'lsa matn `mapServerError` dan; `kindgarden_not_assigned` bo'lsa "Qayta urinish" ko'rsatilmaydi.

**Klaviatura:** `keyboardShouldPersistTaps="handled"` saqlanadi; fokusdagi maydon klaviatura ustiga aylantiriladi; oxirgi maydon fokusda bo'lganda "Yuborish" ga aylantirib yetsa bo'ladi va birinchi bosishda yuboradi. Tab panel klaviatura ochilganda yashirinadi.

### 5.3 Davomat — `03-davomat.html`, navbat/oflayn — `06`

**Hero:** sana (kichik), "Davomat" (katta), ikki `Pill`: ★ bog'cha nomi (joy ikonkasi) va "{radius} m radius". Bog'cha nomi davomat API'sida yo'q — **bolalar soni so'rovidan** (`['children-count','today']`) olinadi; Davomat shu `useQuery` ni o'zi chaqiradi. Bu so'rovning xatosi Davomatda **hech qachon ko'rsatilmaydi**; nom bo'lmasa — "Bog'cha #{id}". Ma'lumot yo'q `Pill` ko'rsatilmaydi.

**Vaqtlar Toshkent vaqtida.** Server `check_in_at`/`check_out_at` ni UTC (`…T03:47:00.000000Z`) beradi; hozirgi `slice(11,16)` oshpazga **5 soat erta** vaqtni ko'rsatadi. Yangi dizaynda sof yordamchi funksiya (UTC ISO → Toshkent `HH:MM`, `Intl` siz, test bilan) ishlatiladi.

**Vaqt chizig'i kartasi** (ikki qadam, orasida vertikal bog'lovchi — kelinmaguncha `linePending`, kelgach `lineDone`):
- Kelish: kelmagan — sariq soat + "Hali kelmadingiz" + "Bog'chaga yetib kelgach "Keldim" ni bosing"; kelgan — yashil belgi + "Keldingiz" + katta vaqt + "Bog'cha markazidan N m" (N yo'q bo'lsa qator yo'q); `check_in_is_late` bo'lsa vaqt yonida sariq "kechikkan".
- Ketish: kelinmagan — kulrang qulf + "—:—"; kelgan-ketmagan — sariq soat + "Hali ketmadingiz"; ketgan — yashil belgi + "Ketdingiz" + vaqt + masofa.
- ★ **Navbatda** (`06`, 1-telefon): navbatda bugungi `check_in`/`check_out` yozuvi bo'lsa, tegishli qadam: sariq sinxron doira, "Yuborilmoqda…", "HH:MM da olindi · navbatda"; shu qadamning tugmasi yashiriladi (oshpaz qayta bosmasin). Bu faqat navbatni o'qish — navbat mantig'i o'zgarmaydi.
- ★ Ikkalasi bo'lsa karta oxirida `cardFooter` qator: **"Bugungi ish vaqti | X soat Y daq"**. Kelish yoki ketish **qayta yuborilgan** bo'lsa (`*_replaced_count > 0`) bu qator ko'rsatilmaydi (qayta yuborish vaqtni almashtiradi va ish vaqti noto'g'ri chiqadi).

**Tugmalar**
- Kelmagan: qora "Keldim" (kamera ikonkasi) + izoh: "Selfi va joylashuv avtomatik olinadi. Kechiksangiz ham bog'chada turib bosing — tizim "kechikkan" deb belgilaydi." (O'chirilgan "Ketdim" tugmasi va eski izoh **ataylab** olib tashlanadi.)
- Kelgan, ketmagan: qora "Ketdim" + kichik `outline` "Kelishni qayta yuborish".
- Ikkalasi: ikki kichik `outline` yonma-yon — "Kelishni qayta" / "Ketishni qayta"; ostida ★ "Bugungi davomat to'liq. Ertaga ko'rishguncha!"
- **Jarayon paytida** (qaysi tugma bosilganidan qat'i nazar) tugmalar maydoni **bitta to'liq kenglikdagi qora jarayon tugmasi** bilan almashadi: chapda aylanuvchi ikonka, qadam nomi, `4/6` (60% shaffoflik), fon chapdan o'ngga `step/total` nisbatida och (`rgba(255,255,255,.12)`) to'ladi. `setProgress` ga matn o'rniga `{step, total, label}` beriladi; qadamlar va tartib o'zgarmaydi. Qadam nomlari (bitta qatorga sig'adigan): "Ruxsatlar tekshirilmoqda", "Joylashuv aniqlanmoqda", "Masofa hisoblanmoqda", "Selfi olinmoqda", "Serverga yuborilmoqda", "Yangilanmoqda".

**Xabarlar**
- "Kelish/Ketish qabul qilindi ✅" muvaffaqiyat banneri **olib tashlanadi** — natija vaqt chizig'ida yashil bo'lib ko'rinadi (va emoji yo'qoladi).
- Navbatga qo'shilganda — `Banner warn` + sinxron ikonka: "**Navbatga qo'shildi.** Internet tiklanganda avtomatik yuboriladi."
- Xatolar — `Banner error`, karta ustida; matn `mapServerError` (server javobi) yoki qat'iy o'zbekcha matn (javob yo'q). Xom `e.message` ko'rsatilmaydi.

**Yuklanish / xato** (bugungi ma'lumot hali bir marta ham kelmagan): vaqt chizig'i o'rnida skelet karta, tugmalar yo'q (aks holda kelgan oshpazga "Hali kelmadingiz" va faol "Keldim" chiqadi). Xatoda — Bosh sahifadagi kabi "Ma'lumotni yuklab bo'lmadi" kartasi va "Qayta urinish".

Debug paneli `{__DEV__ && debugInfo && ...}` shaklida qoladi (arxitektura testi shu shaklni tekshiradi).

### 5.4 Xabarlar — `04-xabarlar.html` (A)
- Ixcham hero "Xabarlar".
- Markazda `EmptyState`: 96 dp `brandSoft` doira ichida marjon qo'ng'iroq, "Hozircha xabar yo'q", "Texnolog yoki tizimdan kelgan eslatmalar shu yerda ko'rinadi."
- B varianti (haqiqiy ro'yxat) — keyingi alohida loyiha (12-bo'lim).

### 5.5 Profil — `05-profil.html`
- Hero (~200): oq doira avatar (70 dp) ichida ★ **bosh harflar** — ismning birinchi ikki so'zining bosh harflari, marjon rangda (bitta so'z bo'lsa bitta harf; kirill va `O'`/`G'` ham ishlaydi). Ostida to'liq ism (2 qatorgacha) va "Oshpaz" yorlig'i. Foydalanuvchi ma'lumoti yo'q bo'lsa (5.7) — avatar ichida foydalanuvchi ikonkasi, ism qatori yo'q.
- Oq karta qatorlari (chapda kichik kulrang ikonka kvadrati):
  - Email (ma'lumot yo'q bo'lsa qator yo'q).
  - ★ Bog'cha — 5.3 dagi kabi; nom bo'lmasa qator yo'q, xato ko'rsatilmaydi.
  - ★ Yuborilmagan davomat — navbatdagi **davomat** yozuvlari soni (`location_events` hisoblanmaydi): 0 → yashil nuqta + **"Navbat bo'sh"**; aks holda sariq nuqta + "N ta navbatda". Ekran fokuslanganda yangilanadi. ("Hammasi yuborilgan" deyilmaydi — navbat bo'shligi hammasi yetib borganini bildirmaydi, 12-bo'lim.)
- `danger` "Hisobdan chiqish".
- ★ "Global Ta'm · versiya X.Y.Z" — `Config.VERSION_NAME` (`react-native-config` Android `BuildConfig` dan o'qiydi; `src/types/config.d.ts` ga `VERSION_NAME?: string` qo'shiladi). Bo'lmasa yozuv yo'q.
- ★ **`ConfirmDialog`** (RN `Modal`), Android `Alert` o'rniga: qizil chiqish doirasi, "Chiqishni xohlaysizmi?"; navbatda yozuv bo'lsa — **"N ta davomat hali yuborilmagan"** (qalin, `warnInk`) **"— chiqsangiz, u yo'qoladi."**; bo'lmasa — "Qayta kirish uchun email va parol kerak bo'ladi."; `dangerSolid` "Ha, chiqaman" va `ghost` "Bekor qilish". Chiqish ketma-ketligi (`stopFlusher`, `clearQueue`, `clearSession`) o'zgarmaydi.

### 5.6 "Internet yo'q" belgisi — `06-umumiy-holatlar.html`
- Hozirgi to'liq kenglikdagi qizil/sariq tasma (`App.tsx` da navigator ustida) o'rniga **hero ichida, status bar ostida oq kapsula** — har ekranda (Kirish ham) shu joyda. Shu sababli u `Hero` komponentining bir qismi bo'ladi.
- Matnlar: internet yo'q + navbat bor — "Internet yo'q · N ta yuborilmagan" (qizil ikonka va matn); faqat internet yo'q — "Internet yo'q"; internet bor, navbat bor — "N ta yuborilmagan" (sariq). Hech narsa bo'lmasa — ko'rinmaydi.
- Sanash qoidasi Profil bilan **bir xil** (faqat davomat turlari). Emojilar olib tashlanadi.

### 5.7 Foydalanuvchi ma'lumotini saqlash (tasdiqlangan istisno)
- Hozir `authStore.user` faqat login paytida to'ladi; ilova qayta ochilganda (`useSessionRestore`) tiklanmaydi va serverda `/me` yo'q. Natijada salomlashuv va Profil ko'pincha bo'sh chiqardi.
- `setSession` `AuthUser` ni (id, name, email, role_id — maxfiy emas) **MMKV** ga yozadi; `useSessionRestore` tokendan keyin uni o'qib `user` ga qo'yadi; `clearSession` o'chiradi.
- Yangilanishdan oldin kirgan oshpazlarda saqlangan ma'lumot yo'q — ular keyingi login'gacha ismsiz holatni ko'radi (5.2, 5.5). **Majburan chiqarib yuborilmaydi.**

### 5.8 Umumiy
- Tab panel — 4.4.
- Pastga tortib yangilash Bosh va Davomatda saqlanadi; spinner rangi `brand`.
- `src/` dagi barcha emoji olib tashlanadi (hozir: `OfflineIndicator.tsx` dagi 📡 📤, `AttendanceScreen.tsx` dagi ✅).

## 6. Matn, sana va vaqt

- Barcha yangi matnlar o'zbekcha (lotin), maketdagidek.
- Sana/vaqt **`Intl` siz** (`5f77452` dagi kabi): Toshkent = UTC+5 qo'lda; hafta kunlari va oylar uchun o'zbekcha massivlar. Mavjud `todayDateString` (`toLocaleString` bilan) yangi kodda ishlatilmaydi.
- Sof yordamchi funksiyalar (hammasi testlanadi):
  - `utcIsoToTashkentHHMM` — server `…000000Z` formatini ham qabul qiladi (Hermes'da kasr qismi bilan `new Date()` ga tayanmaslik — maydonlarni o'zi ajratib `Date.UTC` bilan).
  - `greeting(now)` — Toshkent vaqti: 05:00–10:59 "Xayrli tong", 11:00–16:59 "Xayrli kun", qolgani "Xayrli kech".
  - `uzDateLabel(now)` — "Payshanba, 26-noyabr".
  - `submittedLabel(submittedAt, now)` — "Bugun, HH:MM da" / "Kecha, HH:MM da" / `null`.
  - `nextOpeningLabel(window)` — "Bugun {from} da ochiladi" / "Ertaga {from} da ochiladi".
  - `sumFilled(inputs)` — to'ldirilganlar yig'indisi yoki `null`.
  - `workDuration(inAt, outAt)` — "9 soat 10 daq" / "45 daq".
  - `initials(name)` — "DA", "D", bo'sh → `null`.

## 7. Testlar

**Boshlang'ich holat (2026-11-26):** `npx jest` → 12 suite, 64 test, hammasi o'tadi.

- Arxitektura testlari o'tishi shart: `TextInput` faqat `Field.tsx` da; debug paneli `{__DEV__ && debugInfo &&` shaklida.
- **Yangi arxitektura testi:** `src/**/*.{ts,tsx}` da `/\p{Extended_Pictographic}/u` mosligi yo'q.
- Komponent testlari yangi uslubga moslanadi. Albatta o'zgaradi: `Button.test.tsx` dagi primary/loading fon `#EF5B3C` → `#0D0D0D`, outline chegara `2`/`#0D0D0D` → `1.5`/`#E2E1DE`; `loadingLabel` o'rniga `progress` API. Boshqa yiqilgan test — haqiqiy regressiya belgisi bo'lishi mumkin, o'ylab o'zgartiriladi.
- 6-bo'limdagi barcha sof funksiyalar uchun testlar: chegaraviy holatlar (salomlashuv chegaralari, yarim tun atrofida "Kecha", `submitted_at=null`, bo'sh/noto'g'ri qiymatlar, uch so'zli va kirill ism, bo'sh ism).
- 5.2 holat tartibi uchun test (masalan, `submitted && !allowed` → Yuborilgan).
- 5.7: `setSession` saqlaydi, tiklash o'qiydi, `clearSession` o'chiradi.
- **Qo'lda tekshiruv** (APK, haqiqiy telefon): tungi rejimda Kirish va bolalar sonini kiritish; 360×640 dp da klaviatura ochiq holda "Yuborish" ga yetish va birinchi bosishda yuborish; aylantirilganda status bar belgilari o'qiladi; davomat vaqti server yozuvi bilan (Toshkent vaqtida) mos; ilovani to'liq yopib qayta ochganda salomlashuvda ism bor; internetni o'chirib "Keldim" — navbat holati va oq kapsula.

## 8. Cheklovlar

- **Yangi native paket yo'q.** RN 0.74.5 versiya qulfi (navigation v6, bottom-tabs 6.6.1, screens 3.34.0, safe-area 4.10.5, svg 15.3.0, mmkv 2.12.2, config 1.5.5). Gradient — `react-native-svg`, oyna — RN `Modal`, soya — `elevation`/`shadow*`, saqlash — mavjud MMKV.
- `android/gradle.properties`: `newArchEnabled=false` — eski arxitektura; yangi arxitekturaga xos stil xususiyatlari (`boxShadow`, `filter`) yo'q.
- Biznes mantiq ko'chirilmaydi — faqat chizish qatlami (va 5.7 istisnosi). Har qadamda `npx jest`.
- Tayyor bo'lgach versiya oshiriladi: **1.1.2 → 1.2.0**, `versionCode` 4 → 5.

## 9. Quruvchiga qoldirilganlar

- Komponentlarning aniq API va fayl bo'linishi (4.4 vazifalarini bajarsa bas).
- Piksel-aniq o'lchamlar (maketga yaqin).
- Animatsiyalar — jarayon tugmasining to'lishidan boshqasi shart emas; qo'shilsa, RN `Animated` bilan va yengil.
- Kirish ekranida klaviatura ochilganda hero'ni kichraytirish — kerak bo'lsa.

## 10. Bizning qarorlarimiz (loyiha egasi tekshirishi uchun)

Loyiha egasi so'ramagan, lekin quruvchi bilmasligi mumkin bo'lgan joylarda biz qaror qildik:
1. Salomlashuvda **to'liq ism** (ism tartibi noma'lum); bosh harflar — birinchi ikki so'zdan.
2. Bosh sahifada "Yuborilgan" holati "Vaqt yopiq" dan **oldin** ko'rsatiladi; "Bugun/Kecha" yorlig'i.
3. Davomat vaqtlari Toshkent vaqtiga o'giriladi (hozirgi 5 soatlik xato tuzatiladi).
4. Kelish/ketish qayta yuborilgan kuni "Bugungi ish vaqti" ko'rsatilmaydi.
5. Davomatdagi "qabul qilindi ✅" banneri olib tashlanadi — natija vaqt chizig'ida ko'rinadi.
6. Jarayon paytida barcha tugmalar o'rnida bitta to'liq kenglikdagi jarayon tugmasi.
7. Profilda "Hammasi yuborilgan" o'rniga "Navbat bo'sh".
8. Kirish xatosida maydonlar qizarmaydi (server qaysi maydonligini aytmaydi).
9. Status bar ostida qotgan marjon tasma — aylantirilganda ham belgilar ko'rinadi.
10. Yangi "Menyu hali tayyor emas" va "Yosh guruhlari yo'q" holat matnlari.
11. Versiya 1.2.0.

## 11. Maketlardagi ataylab qilinmaydigan narsalar

- `02-bosh.html`: bo'sh maydondagi xira "0" va bo'sh maydonni 0 deb qo'shgan "Jami" — **qilinmaydi** (loyiha egasi "b" ni tanladi: maydon bo'sh ko'rinadi, "Jami" faqat to'ldirilganlarni qo'shadi).
- `02-bosh.html`, `03-davomat.html`: 08:00–10:00, "Ertaga 08:00 da ochiladi" — misol; haqiqiy qiymatlar serverdan (hozir 03:00–21:00).
- `05-profil.html`: "Hammasi yuborilgan" → "Navbat bo'sh".
- `06-umumiy-holatlar.html`: Kirish xatosidagi qizil parol chegarasi — qilinmaydi.

## 12. Keyingi alohida ishlar (bu ishga kirmaydi)

1. **Oflayn navbat xatosi (jiddiy).** Server `captured_at` 300 soniyadan eski bo'lsa davomatni `stale_capture` (422) bilan rad etadi (`AttendanceService::MAX_CAPTURE_SKEW_SECONDS`), `attendanceFlusher` esa har qanday 4xx ni "doimiy xato" deb **jimgina o'chiradi**. Natijada 5 daqiqadan keyin yuborilgan oflayn davomat yo'qoladi.
2. **Xabarlar funksiyasi** — server API + push + `04-xabarlar.html` dagi B maketi.
3. Bolalar sonini yuborish vaqt oynasini (03:00–21:00) o'zgartirish kerak bo'lsa — server ishi.
