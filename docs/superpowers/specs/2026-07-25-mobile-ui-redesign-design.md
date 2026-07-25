# Mobil ilova UI qayta ishlash — Dizayn hujjati

**Sana:** 2026-07-25
**Loyiha:** SupplyAutsorsingSystem — `mobile/` (Chef Mobile App, React Native 0.74.5)
**Maqsad:** Ilovaning vizual ko'rinishini "Global Ta'm" brendiga moslash va yagona dizayn tizimiga o'tkazish
**Holat:** Brainstorming yakunlandi, implementation rejasi yozilmagan

---

## 1. Muammo

Ilova ishlaydi, lekin prototip kabi ko'rinadi. Aniq sabablari:

1. **Brend yo'q.** Asosiy rang `#4f46e5` (Tailwind indigo) — logotipdagi marjon-qizil bilan hech qanday aloqasi yo'q. Ilova "Global Ta'm"niki ekani bilinmaydi.
2. **Dizayn tizimi yo'q.** `src/theme/colors.ts` da atigi 8 ta rang bor, qolgan hamma narsa fayllarga tarqoq hardcode qilingan: `#E6F4EA`, `#34A853`, `#1E4620`, `#FFF7E6`, `#F4B400`, `#7A5C00`, `#F1F3F4`, `#9AA0A6`, `#3C4043`, `#fee2e2`, `#991b1b`. Oraliq (spacing) va shrift o'lchamlari shkalasi umuman yo'q — har faylda o'z qiymatlari.
3. **Emoji ikonkalar.** Tab paneli va tugmalarda 🏠 📍 🔔 👤 📷 ✅ ⏰ 📡. Samsung, Xiaomi va Realme o'z emoji shriftlarini ishlatadi — ilova har telefonda boshqacha ko'rinadi.
4. **Debug oynasi productionda.** `AttendanceScreen` oshpazga GPS koordinatalari, bog'cha koordinatalari, `isMock` bayrog'i, HTTP status va API JSON javobining bir qismini ko'rsatadi.
5. **Login xatosi.** Tungi rejimdagi Android telefonda login qilib bo'lmaydi (2-bo'limga qarang).

Foydalanuvchi bilan aniqlangan asosiy shikoyat: **"chiroyli emas, arzon ko'rinadi"** — ya'ni muammo qulaylikda emas, vizual identikada.

## 2. Login xatosi — sabab

**Alomat:** Login ekranida email va parol maydonlariga yozilgan matn ko'rinmaydi — oq fonda oq matn.

**Sabab:** `LoginScreen.tsx` dagi `input` stilida `backgroundColor: '#fff'` bor, lekin `color` yo'q va `placeholderTextColor` ham berilmagan. Android'da `TextInput` matn rangi berilmasa, tizim mavzusidan (theme) meros oladi — tungi rejimda bu oq rang. Natijada oq fonda oq matn.

Xuddi shu xato `ChildrenCountCard.tsx` dagi yosh guruhi maydonlarida ham bor — bolalar soni kiritilganda ham ko'rinmaydi.

**Yechim:** Ilovada yalang'och `TextInput` umuman qoldirilmaydi. Barcha matn maydonlari yagona `Field` komponenti orqali chiziladi, `color` va `placeholderTextColor` shu komponentda majburiy beriladi. Bu xato bir marta tuzatilibgina qolmay, arxitektura darajasida qaytishi imkonsiz bo'ladi.

## 3. Qabul qilingan qarorlar

| Masala | Qaror | Sabab |
|---|---|---|
| Vizual yo'nalish | **C1 — tipografik** | Katta shrift va yuqori kontrast oshpazlar uchun qulay; "qimmatlik" bezakdan emas, tipografiya va bo'sh joydan keladi; vazmin va uzoq muddat eskirmaydi |
| Hajm | **Login, Bosh, Davomat** | Oshpaz har kuni ishlatadigan uchta ekran. Xabarlar (hozir placeholder) va Profil keyingi bosqichda |
| Ikonkalar | **`react-native-svg`** | Har telefonda bir xil ko'rinadi; emoji aynan "arzon" tuyg'uning manbalaridan biri |
| Qurilish usuli | **Tokenlar + primitiv komponentlar** | C1 butunlay tipografik shkalaga tayanadi — uni har faylda qayta e'lon qilish bugungi tarqoqlik muammosini takrorlaydi |

### 3.1 Rad etilgan variantlar

- **A — issiq brend** (krem fon, yumaloq soyali kartalar) va **B — operatsion** (qora yuqori panel): rad etildi, foydalanuvchi soddaroq yo'nalishni tanladi.
- **C2 — rangli bloklar** (marjon sarlavha bloki, to'liq kenglikdagi yashil/sariq holat tasmalari): holat tezroq o'qiladi, lekin ranglar ko'p va "qalin" ko'rinadi.
- **Faqat token fayli** (primitiv komponentlarsiz): diff kichikroq bo'lardi, lekin shrift o'lchamlari har faylda takrorlanib, bugungi tarqoqlik yangi shaklda qaytardi.
- **Tayyor UI kutubxona** (tamagui, restyle): og'ir native paket, RN 0.74 qulflangan versiyalari bilan ziddiyat xavfi, o'z uslubi C1 bilan kurashadi.

## 4. Vizual til — C1

- Fon **doim oq**. Karta yo'q, soya yo'q.
- Bo'limlar 1px ingichka chiziq bilan ajratiladi, chiziq ekran chetigacha boradi.
- Sarlavhalar katta, qalin va zich terilgan (manfiy `letterSpacing`).
- **Marjon rang faqat uch joyda:** asosiy tugma, sarlavha ustidagi eyebrow yozuv, faol tab. Boshqa hech qayerda ishlatilmaydi.
- Holat rangli nuqta (9px doira) + katta matn bilan ko'rsatiladi.
- Tugmalar baland (56px), matni qalin.

## 5. Token qatlami

`src/theme/` uch faylga bo'linadi, `index.ts` ularni birlashtiradi va tashqariga chiqaradi.

### 5.1 `colors.ts`

| Token | Qiymat | Vazifasi |
|---|---|---|
| `ink` | `#0D0D0D` | asosiy matn, `outline` tugma chegarasi |
| `inkMuted` | `#77797E` | ikkilamchi matn, izohlar |
| `inkFaint` | `#9A9CA2` | eyebrow yozuvlar, nofaol tablar, placeholder |
| `brand` | `#EF5B3C` | asosiy tugma, eyebrow, faol tab |
| `brandPressed` | `#C4401F` | bosilgan holat |
| `surface` | `#FFFFFF` | fon |
| `rule` | `#E8E8EA` | ajratuvchi chiziqlar, input chegarasi |
| `ok` | `#14884A` | muvaffaqiyat holati |
| `warn` | `#D08700` | kutish / e'tibor holati |
| `danger` | `#C62828` | xatolar |
| `okBg` | `#E7F4EC` | muvaffaqiyat banneri foni |
| `warnBg` | `#FDF3E0` | ogohlantirish banneri foni |
| `dangerBg` | `#FDECEA` | xato banneri foni |
| `disabledBg` | `#F1F1F3` | o'chirilgan tugma foni |
| `disabledInk` | `#A9ABAF` | o'chirilgan tugma matni |
| `statusOff` | `#C7C9CD` | neytral holat nuqtasi |

**`danger` ataylab brend marjonidan quyuqroq.** Agar xato rangi brend rangiga yaqin bo'lsa, oshpaz xato xabarini oddiy brend elementi deb qabul qiladi va e'tibor bermaydi.

### 5.2 `typography.ts`

`variant` → `{fontSize, lineHeight, fontWeight, letterSpacing, textTransform}`. RN'da `letterSpacing` piksel birligida.

| Variant | Size | Line | Weight | Tracking | Qo'shimcha |
|---|---|---|---|---|---|
| `h1` | 28 | 30 | 800 | −1.0 | ekran sarlavhasi |
| `statusBig` | 22 | 26 | 800 | −0.7 | davomat holati |
| `body` | 15 | 22 | 400 | 0 | |
| `bodyStrong` | 15 | 22 | 700 | 0 | |
| `fieldLabel` | 13 | 18 | 700 | 0 | maydon nomi |
| `muted` | 12 | 17 | 400 | 0 | izoh, meta |
| `caption` | 12 | 16 | 400 | 0 | |
| `eyebrow` | 11 | 14 | 800 | +1.5 | `uppercase` |
| `button` | 15 | 20 | 800 | −0.2 | |
| `tab` | 11 | 13 | 700 | 0 | |

### 5.3 `spacing.ts`

```
spacing = { xs: 4, sm: 8, md: 12, lg: 16, xl: 24, xxl: 32 }
radius  = { field: 11, button: 12, pill: 999 }
hit     = { button: 56, field: 52, tabBar: 60 }
screen  = { paddingH: 18, sectionPaddingV: 16 }
```

## 6. Primitiv komponentlar

Hammasi `src/components/` ichida, faqat tokenlardan foydalanadi. Ekran fayllarida `fontSize`, `color` yoki hex rang yozilmaydi.

| Komponent | API | Vazifasi |
|---|---|---|
| `AppText` | `variant`, `color?`, `style?` | Shriftga oid barcha qaror shu yerda |
| `Screen` | `children` | `ScreenContainer` o'rnini bosadi. Oq fon, safe-area, **standart padding yo'q** — C1 da chiziqlar ekran chetigacha borishi kerak |
| `Section` | `children`, `style?` | `paddingHorizontal: 18`, `paddingVertical: 16` |
| `Divider` | — | 1px `rule`, to'liq kenglik |
| `Field` | `label`, + `TextInputProps` | `color: ink` va `placeholderTextColor: inkFaint` **majburiy** — 2-bo'limdagi xato shu bilan yopiladi |
| `Button` | `label`, `onPress`, `variant`, `loading?`, `disabled?` | `variant`: `primary` (marjon to'ldirilgan) / `outline` (2px `ink` chegara, oq fon) / `danger` (`danger` rangi bilan to'ldirilgan). `minHeight: 56` |

| `StatusLine` | `tone`, `title`, `detail?` | `tone`: `ok` / `warn` / `off`. Rangli nuqta + `statusBig` matn + `muted` izoh |
| `Banner` | `tone`, `message` | `tone`: `error` / `success` / `info`. Hozirgi `ErrorBanner`, `infoBox`, `progressBox` — uchalasi shunga birlashadi |
| `PageHeader` | `eyebrow?`, `title`, `meta?` | C1 sarlavha bloki |

`Button` ning `danger` varianti Profil ekranidagi "Chiqish" tugmasi uchun kerak — u ekran hajmdan tashqarida bo'lsa ham, `PrimaryButton` o'chirilgach ishlashda davom etishi shart.

`src/components/icons/` ichida `HomeIcon`, `PinIcon`, `BellIcon`, `UserIcon` — `react-native-svg` ustida, `size` va `color` proplari bilan.

Eski `PrimaryButton`, `ErrorBanner` va `ScreenContainer` fayllari o'chiriladi. Ular hajmdan tashqaridagi ekranlarda ham ishlatiladi, shuning uchun o'sha ekranlar ham yangilanadi — 7.5-bo'limga qarang.

## 7. Ekranlar

### 7.1 `LoginScreen`

Logo → `PageHeader` ("OSHPAZ KABINETI" eyebrow + "Kirish") → ikkita `Field` → `Button variant="primary"`. Xato `Banner tone="error"` bilan. Lokal `StyleSheet` qolmaydi.

### 7.2 `HomeScreen` va bolalar soni

`ChildrenCountCard` → `ChildrenCountSection` deb qayta nomlanadi va karta konsepsiyasidan voz kechadi. Uchala holat saqlanadi:

- **Ochiq** — `PageHeader` + `Divider` + yosh guruhlari `Field` lari + `Button`
- **Yuborilgan** — `PageHeader` + `Divider` + yuborilgan vaqt va sonlar `Section` ichida
- **Bloklangan** — `PageHeader` + `Banner tone="info"` sabab bilan

Biznes mantiq (`react-query` chaqiruvlari, validatsiya, `mapServerError`) o'zgarmaydi — faqat chizish qatlami almashadi.

### 7.3 `AttendanceScreen`

`PageHeader` + `StatusLine` bo'limlari (kelish / ketish) + tugmalar.

- **Debug oynasi `__DEV__` bilan o'raladi.** Hozir u oshpazga GPS koordinatalari va API JSON javobini ko'rsatib turibdi.
- Progress matni (`1/6 Ruxsatlar tekshirilmoqda...`) alohida sariq quti o'rniga tugma ichidagi `loading` holatiga ko'chadi — oshpaz nima bo'layotganini tugmaning o'zidan ko'radi.
- Tugmalardagi 📷 emoji olib tashlanadi.

Davomat mantig'i (`onAction` funksiyasi, GPS, masofa hisobi, queue) o'zgarmaydi.

### 7.4 `MainTabs`

Emoji o'rniga SVG ikonkalar, `height: 60`, faol rang `brand`, nofaol `inkFaint`, yuqorida 1px `rule` chizig'i.

### 7.5 Hajmdan tashqaridagi ekranlarni ishlashda saqlash

`NotificationsScreen` va `ProfileScreen` qayta ishlanmaydi, lekin eski komponentlar va eski token nomlari o'chirilgani uchun ular **mexanik ravishda** yangilanishi shart. Aks holda ilova umuman kompilyatsiya bo'lmaydi.

| Fayl | Nima o'zgaradi |
|---|---|
| `NotificationsScreen` | `ScreenContainer` → `Screen` + `Section` (yangi `Screen` da padding yo'q, matn ekran chetiga yopishib qolmasligi uchun) |
| `ProfileScreen` | `ScreenContainer` → `Screen` + `Section`; `PrimaryButton variant="danger"` → `Button variant="danger"`; `StyleSheet` dagi `colors.textPrimary` → `colors.ink`, `colors.textMuted` → `colors.inkMuted`, `colors.border` → `colors.rule` |
| `MainTabs` | `colors.primary` → `colors.brand` |
| `OfflineIndicator` | hardcode `#dc2626` → `colors.danger`, `#f59e0b` → `colors.warn`; tuzilishi o'zgarmaydi |

Bu fayllarda vizual dizayn o'zgarmaydi — faqat nomlar almashadi. To'liq qayta ishlanishi keyingi bosqichda.

**Eski token nomlari uchun alias qoldirilmaydi.** `colors.ts` dagi `primary`, `success`, `warning`, `bgLight`, `textPrimary`, `textMuted`, `border` butunlay olib tashlanadi. Sabab: alias qoldirilsa, eski nomlar yangi kodda ham ishlatilib, ikki xil nomlash tizimi yonma-yon yashab qoladi. TypeScript kompilyatsiya xatosi barcha ishlatilish joylarini bir yo'la ko'rsatib beradi — bu foyda, to'siq emas.

## 8. Bog'liqliklar

`react-native-svg@15.3.0` qo'shiladi — RN 0.74 uchun mos versiya. `package.json` da aniq versiya bilan qadaladi (`^` belgisisiz), loyihadagi mavjud amaliyot bo'yicha.

**Bu native paket.** `npm install` dan keyin APK qayta yig'ilishi shart — Metro orqali eski APK ustiga yangilanmaydi.

## 9. Test strategiyasi

**Boshlang'ich holat (2026-07-25 da tekshirilgan):** `npx jest` → 5 suite, 13 test, hammasi o'tadi. Ular API, queue, store va sana hisobiga oid — UI ga tegmaydi, shuning uchun bu ish ularni buzmasligi kerak.

**Jest sozlamasiga e'tibor.** `@testing-library/react-native@13.3.3` `devDependencies` da bor, lekin hozircha bironta test uni ishlatmaydi — quyidagi render testlari birinchi bo'ladi. `jest.config.js` dagi `transformIgnorePatterns` ro'yxatiga `react-native-svg` qo'shilishi kerak, aks holda ikonka import qilgan har qanday test transform xatosi bilan yiqiladi.

Yangi testlar:

- `Field` `color` va `placeholderTextColor` ni haqiqatan `TextInput` ga uzatishini tekshiradi — aynan 2-bo'limdagi xato qaytmasligi uchun
- `Button` uchala variantda to'g'ri fon va matn rangini beradi; `loading` holatida `onPress` chaqirilmaydi
- `StatusLine` uchala `tone` da to'g'ri nuqta rangini beradi
- `AppText` noma'lum `variant` bilan chaqirilganda `body` ga qaytadi

**Qo'lda vizual tekshiruv:** APK yig'ib, telefonni **tungi rejimga o'tkazib** login qilish. Bu asl xatoning takrorlanish sharti edi — kunduzgi rejimda xato ko'rinmaydi.

## 10. Hajmdan tashqarida

- `NotificationsScreen` va `ProfileScreen` to'liq qayta ishlash
- Tungi rejim (dark mode) qo'llab-quvvatlash — ilova doim yorug' rejimda ishlaydi
- Animatsiya va o'tishlar
- Logotipni shaffof fonli PNG ga almashtirish — C1 foni oq bo'lgani uchun hozirgi `.jpg` yetarli, lekin PNG mavjud bo'lsa almashtiriladi
- Yangi funksiyalar (push, plan-4 ekranlari, background geofence)

## 11. Xavflar

| Xavf | Ehtimol | Ta'sir | Himoyalanish |
|---|---|---|---|
| `react-native-svg` RN 0.74 bilan mos kelmasligi | Past | O'rta | 15.3.0 versiyasi RN 0.74 uchun rasman qo'llab-quvvatlanadi; birinchi qadamda o'rnatib, APK yig'ib tekshiriladi |
| Eski APK ustiga yangilanmasligi | Yuqori | Past | Native paket qo'shilgani hujjatlashtiriladi, `mobile/README.md` ga eslatma qo'shiladi |
| C1 haddan tashqari sodda chiqishi | O'rta | O'rta | Maketlar tasdiqlangan; birinchi ekran tayyor bo'lgach APK yig'ib real telefonda ko'riladi |
| Ekranlarni qayta yozishda biznes mantiq buzilishi | O'rta | Yuqori | Mantiq umuman ko'chirilmaydi — faqat chizish qatlami almashadi; mavjud testlar har qadamda ishga tushiriladi |

---

**Hujjat oxiri.**
