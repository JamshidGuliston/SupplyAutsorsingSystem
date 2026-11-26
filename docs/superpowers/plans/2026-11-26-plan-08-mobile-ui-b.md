# Mobil ilova "B: Brend sarlavha" dizayni — Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Chef mobil ilovasining beshala ekranini, tab panelini va umumiy holatlarini tasdiqlangan "B: Brend sarlavha" maketlari bo'yicha qayta chizish, ko'rsatishdagi xatolarni (UTC vaqt, ism yo'qolishi) tuzatish.

**Architecture:** Iyuldagi uch qatlam saqlanadi: `src/theme/` tokenlari → `src/components/` primitivlari → ekranlar (faqat kompozitsiya). Ekranlar ichidagi qaror mantig'i (qaysi holat, qaysi tugma, qanday matn) **sof funksiyalarga** (`src/lib/`, `src/attendance/timeline.ts`) chiqariladi va birinchi navbatda testlanadi; komponentlar ularni faqat chizadi. Hero gradienti `react-native-svg` bilan; yangi native paket yo'q.

**Tech Stack:** React Native 0.74.5 (eski arxitektura, Hermes), TypeScript 5.0.4, react-native-svg 15.3.0, react-native-mmkv 2.12.2, react-native-config 1.5.5, @react-navigation v6, @tanstack/react-query 5, zustand 4, Jest 29 + @testing-library/react-native 13.3.3.

**Spec:** `docs/superpowers/specs/2026-11-26-mobile-ui-b-design.md` + maketlar `docs/superpowers/specs/2026-11-26-mobile-ui-b-mockups/` (01–06). Har vazifa oldidan spec'ning tegishli bo'limini va maketini o'qing — maketdagi qiymatlar majburiy (spec 0-qism), spec 11-bo'limdagi istisnolardan tashqari.

**Branch:** `feature/mobile-ui-redesign`

## Global Constraints

- Barcha ish `mobile/` ichida; buyruqlar `mobile/` dan ishga tushiriladi. Server (Laravel) o'zgarmaydi.
- **Boshlang'ich holat:** `npx jest` → 12 suite, 64 test o'tadi; `npx tsc --noEmit` → exit 0. Har vazifa oxirida: butun suite yashil va `tsc` exit 0. Test sonlari yo'l-yo'riq, mezon emas.
- **Yangi npm paket qo'shilmaydi** (native ham, JS ham). RN 0.74 versiya qulfi.
- RN 0.74 eski arxitektura: `boxShadow`, `filter`, CSS gradient yo'q. Soya — `elevation` + `shadow*`; gradient — `react-native-svg` `LinearGradient`.
- **Biznes mantiq o'zgarmaydi:** GPS/masofa/selfi qadamlari va tartibi, `attendanceFlusher`, navbatga yozish, `mapServerError` matnlari, bolalar soni validatsiyasi (bo'sh maydon — xato), chiqish ketma-ketligi. Yagona istisno — Task 6 (foydalanuvchini saqlash).
- Ekran va komponent fayllarida hex rang, `fontSize`, `fontWeight` yozilmaydi — faqat `colors`, `typography`, `AppText`.
- `TextInput` faqat `src/components/Field.tsx` da (mavjud arxitektura testi).
- AttendanceScreen'dagi debug paneli aynan `{__DEV__ && debugInfo && (` shaklida qoladi (mavjud arxitektura testi).
- `src/` da emoji yo'q (Task 11 da test bilan qotiriladi).
- Sana/vaqt **`Intl` siz**, `toLocaleString` siz: Toshkent = UTC+5 qo'lda (`src/lib/uzTime.ts`).
- Importlar to'g'ridan-to'g'ri fayl yo'li bilan; barrel `index.ts` lar bo'sh qoladi.
- Matnlar o'zbekcha (lotin), spec va maketdagidek so'zma-so'z. Mavjud xato matnlari qayta yozilmaydi.
- Test fayllari `__tests__/` da, mavjud uslubda (`describe`/`it` nomlari o'zbekcha, `StyleSheet.flatten` bilan stil tekshirish).
- Har vazifa oxirida commit; xabar oxirida `Co-Authored-By: Claude Opus 5.5 <noreply@anthropic.com>`.

## Review Focus

1. **Yangilanishdan oldingi sessiya** (token bor, saqlangan foydalanuvchi yo'q) — ilova qulamaydi, salomlashuv "Xayrli tong!" (ismsiz), Profilda ism/email qatorlari yo'q, avatar ichida ikonka. → Task 6, Task 10 testlari.
2. **Toshkent sana chegarasi**: `submitted_at = 2026-11-25T19:30:00.000000Z` (Toshkentda 26-noyabr 00:30) 26-noyabrda "Bugun, 00:30 da"; `2026-11-25T18:50:00Z` (25-noyabr 23:50) "Kecha, 23:50 da". Server kasrli `.000000Z` formati. → Task 1 testlari.
3. **Davomat birinchi yuklanishdan oldin yoki yuklash xatosida** — "Keldim" tugmasi umuman chizilmaydi (aks holda kelgan oshpaz ikki marta bosadi). → Task 9 testi.
4. **Navbatda bugungi kelish bor** — vaqt chizig'ida "Yuborilmoqda…", "Keldim" chizilmaydi. → Task 9 testi.
5. **Bitta guruh bo'sh qolgan holda "Yuborish"** — xato banneri, so'rov ketmaydi; "Jami" faqat to'ldirilganlarni qo'shadi. → Task 8 testi.

---

### Task 1: Sof yordamchilar — vaqt, matn, xato

**Files:**
- Create: `mobile/src/lib/uzTime.ts`, `mobile/src/lib/text.ts`
- Modify: `mobile/src/api/errors.ts` (qo'shish, mavjud `mapServerError` o'zgarmaydi)
- Test: `mobile/__tests__/lib/uzTime.test.ts`, `mobile/__tests__/lib/text.test.ts`, `mobile/__tests__/api/userFacingError.test.ts`

**Interfaces — Produces:**
```ts
// uzTime.ts — hammasi Toshkent (UTC+5) bo'yicha, Intl siz
export function parseServerIso(iso: string): Date | null;          // '2026-11-26T03:47:00.000000Z', '…Z', '…+05:00'; noto'g'ri → null. Maydonlarni regex bilan ajratib Date.UTC — new Date(iso) ga tayanmaydi.
export function tashkentHHMM(d: Date): string;                    // '08:47'
export function tashkentDateKey(d: Date): string;                 // '2026-11-26'
export function utcIsoToTashkentHHMM(iso: string | null | undefined): string | null;
export function greeting(now: Date): 'Xayrli tong' | 'Xayrli kun' | 'Xayrli kech';
export function uzDateLabel(now: Date): string;                   // 'Payshanba, 26-noyabr'
export function submittedLabel(submittedAt: string | null, now: Date): string | null; // 'Bugun, 08:42 da' | 'Kecha, 23:50 da' | null (null/noto'g'ri yoki 2+ kun oldin → null)
export function nextOpeningLabel(w: { from: string; to: string; current_tashkent: string }): string | null; // current<from → 'Bugun 03:00 da ochiladi'; current>=to → 'Ertaga 03:00 da ochiladi'; aks holda null
export function workDuration(inIso: string, outIso: string): string | null; // '9 soat 10 daq' | '45 daq' | '9 soat' ; manfiy/noto'g'ri → null
// text.ts
export function initials(name: string | null | undefined): string | null; // birinchi ikki so'z bosh harfi, katta harf
export function sumFilled(values: Array<string | undefined>): number | null; // faqat /^\d+$/ qiymatlar; hech biri yo'q → null
export function displayName(name: string | null | undefined): string | null; // trim, ko'p probelni bittaga; bo'sh → null
// errors.ts
export const LOAD_FAILED_TEXT = "Internetni tekshirib, qayta urinib ko'ring.";
export function userFacingError(err: unknown): string;
export function errorCode(err: unknown): string | undefined;      // err.response.data.error
```
Hafta kunlari: `['Yakshanba','Dushanba','Seshanba','Chorshanba','Payshanba','Juma','Shanba']`; oylar: `['yanvar','fevral','mart','aprel','may','iyun','iyul','avgust','sentabr','oktabr','noyabr','dekabr']`. Salomlashuv chegaralari: 05:00–10:59 tong, 11:00–16:59 kun, qolgani kech.

`userFacingError` qoidalari (birinchi mos kelgani):
1. `err.response.data` bor → `mapServerError(data)`.
2. `err.response` bor, `data` yo'q → `"Serverda xato. Birozdan keyin qayta urinib ko'ring."`
3. `err.message` `'GPS error'` bilan boshlanadi → `"Joylashuvni aniqlab bo'lmadi. GPS yoqilganini tekshirib, qayta urinib ko'ring."`
4. `err.message` ∈ {`'Bekor qilindi'`, `'Rasm olinmadi'`} yoki `'Kamera xatosi'` bilan boshlanadi → `err.message` (bular allaqachon o'zbekcha, `selfieCapture.ts`).
5. Qolgani → `"Internet bilan bog'lanib bo'lmadi. Qayta urinib ko'ring."`

- [ ] **Step 1: Failing testlarni yozing**

```ts
// __tests__/lib/uzTime.test.ts — asosiy holatlar (qolganlarini shu uslubda)
const at = (iso: string) => new Date(iso);
it('server kasrli formatini Toshkent HH:MM ga o\'giradi', () => {
  expect(utcIsoToTashkentHHMM('2026-11-26T03:47:00.000000Z')).toBe('08:47');
  expect(utcIsoToTashkentHHMM('2026-11-26T19:05:00Z')).toBe('00:05');
  expect(utcIsoToTashkentHHMM(null)).toBeNull();
  expect(utcIsoToTashkentHHMM('bema\'ni')).toBeNull();
});
it('salomlashuv chegaralari Toshkent vaqtida', () => {
  expect(greeting(at('2026-11-26T00:00:00Z'))).toBe('Xayrli tong');  // 05:00
  expect(greeting(at('2026-11-26T05:59:00Z'))).toBe('Xayrli tong');  // 10:59
  expect(greeting(at('2026-11-26T06:00:00Z'))).toBe('Xayrli kun');   // 11:00
  expect(greeting(at('2026-11-26T12:00:00Z'))).toBe('Xayrli kech');  // 17:00
  expect(greeting(at('2026-11-25T23:59:00Z'))).toBe('Xayrli kech');  // 04:59
});
it('o\'zbekcha sana', () => {
  expect(uzDateLabel(at('2026-11-26T04:00:00Z'))).toBe('Payshanba, 26-noyabr');
  expect(uzDateLabel(at('2026-11-30T19:30:00Z'))).toBe('Seshanba, 1-dekabr'); // UTC 30-noyabr, Toshkentda 1-dekabr
});
it('submittedLabel Toshkent sanasi bo\'yicha Bugun/Kecha', () => {
  const now = at('2026-11-26T06:00:00Z');
  expect(submittedLabel('2026-11-25T19:30:00.000000Z', now)).toBe('Bugun, 00:30 da');
  expect(submittedLabel('2026-11-25T18:50:00Z', now)).toBe('Kecha, 23:50 da');
  expect(submittedLabel('2026-11-23T08:00:00Z', now)).toBeNull();
  expect(submittedLabel(null, now)).toBeNull();
});
it('nextOpeningLabel', () => {
  expect(nextOpeningLabel({ from: '03:00', to: '21:00', current_tashkent: '02:10' })).toBe('Bugun 03:00 da ochiladi');
  expect(nextOpeningLabel({ from: '03:00', to: '21:00', current_tashkent: '21:00' })).toBe('Ertaga 03:00 da ochiladi');
  expect(nextOpeningLabel({ from: '03:00', to: '21:00', current_tashkent: '14:25' })).toBeNull();
});
it('workDuration', () => {
  expect(workDuration('2026-11-26T03:02:00.000000Z', '2026-11-26T12:12:00.000000Z')).toBe('9 soat 10 daq');
  expect(workDuration('2026-11-26T03:00:00Z', '2026-11-26T03:45:00Z')).toBe('45 daq');
  expect(workDuration('2026-11-26T03:00:00Z', '2026-11-26T12:00:00Z')).toBe('9 soat');
  expect(workDuration('2026-11-26T12:00:00Z', '2026-11-26T03:00:00Z')).toBeNull();
});
```
```ts
// __tests__/lib/text.test.ts
expect(initials('Dilnoza Aliyeva')).toBe('DA');
expect(initials('  aliyeva   dilnoza  karimovna ')).toBe('AD');
expect(initials('Дилноза')).toBe('Д');
expect(initials("O'g'iloy Toshmatova")).toBe('OT');
expect(initials('')).toBeNull(); expect(initials(null)).toBeNull();
expect(sumFilled(['18', '42', ''])).toBe(60);
expect(sumFilled(['18', undefined, 'x'])).toBe(18);
expect(sumFilled(['', undefined])).toBeNull();
expect(displayName('  Dilnoza   Aliyeva ')).toBe('Dilnoza Aliyeva');
expect(displayName('   ')).toBeNull();
```
```ts
// __tests__/api/userFacingError.test.ts
expect(userFacingError({ response: { data: { error: 'invalid_credentials' } } })).toBe("Email yoki parol noto'g'ri.");
expect(userFacingError({ response: { status: 500 } })).toBe("Serverda xato. Birozdan keyin qayta urinib ko'ring.");
expect(userFacingError(new Error('GPS error (3): timeout'))).toBe("Joylashuvni aniqlab bo'lmadi. GPS yoqilganini tekshirib, qayta urinib ko'ring.");
expect(userFacingError(new Error('Bekor qilindi'))).toBe('Bekor qilindi');
expect(userFacingError(new Error('Network Error'))).toBe("Internet bilan bog'lanib bo'lmadi. Qayta urinib ko'ring.");
expect(errorCode({ response: { data: { error: 'kindgarden_not_assigned' } } })).toBe('kindgarden_not_assigned');
expect(errorCode(new Error('x'))).toBeUndefined();
```

- [ ] **Step 2:** `npx jest __tests__/lib __tests__/api/userFacingError.test.ts` → FAIL (modullar yo'q).
- [ ] **Step 3:** Yuqoridagi signaturalarni amalga oshiring.
- [ ] **Step 4:** Shu buyruq → PASS; `npx jest` va `npx tsc --noEmit` → yashil.
- [ ] **Step 5: Commit** — `feat(lib): Tashkent time, Uzbek labels and user-facing error helpers`

---

### Task 2: Tokenlar, tipografiya va `Button`

**Files:**
- Modify: `mobile/src/theme/colors.ts`, `mobile/src/theme/typography.ts`, `mobile/src/theme/spacing.ts`, `mobile/src/components/Button.tsx`
- Test: `mobile/__tests__/components/Button.test.tsx` (qayta yoziladi)

**Interfaces — Produces:**
- `colors` ga **qo'shiladi** (eskilari Task 11 gacha qoladi): `brandGradient: ['#F47A55','#EF5B3C','#D9431F']`, `brandTop '#F47A55'`, `brandSoft '#FFE9E2'`, `canvas '#F7F6F4'`, `card '#FFFFFF'`, `cardDivider '#F0EFED'`, `cardFooter '#FAFAF9'`, `fieldBg '#F4F3F1'`, `fieldBorder '#ECECEF'`, `outlineBorder '#E2E1DE'`, `skeleton '#EEEDEA'`, `lineDone '#CFE8D9'`, `linePending '#EEEDEB'`, `inkSoft '#55575C'`, `onHero '#FFFFFF'`, `onHeroMuted 'rgba(255,255,255,0.85)'`, `heroChip 'rgba(255,255,255,0.18)'`, `heroCircle 'rgba(255,255,255,0.09)'`, `heroCircle2 'rgba(255,255,255,0.07)'`, `heroSkeleton 'rgba(255,255,255,0.25)'`, `dangerBorder '#F3D3CF'`, `dangerInk '#8E1C1C'`, `dangerIconBg '#F8C9C4'`, `warnIconBg '#F6DFAF'`, `okIconBg '#CFE8D9'`, `lockedBg '#F2F1EF'`, `lockedInk '#B5B7BB'`, `scrim 'rgba(0,0,0,0.45)'`, `shadow '#281E14'`, `tabShadow 'rgba(0,0,0,0.05)'`.
- `TextVariant` ga qo'shiladi (size/lineHeight/weight/letterSpacing): `heroTitle 21/26/800/-0.5`, `heroTitleLg 26/30/800/-0.6`, `heroSub 12/16/400/0`, `sectionTitle 18/22/800/-0.4`, `timeValue 20/24/800/-0.5`, `stepLabel 12/16/600/0`, `numberInput 17/22/800/0`, `total 18/22/800/-0.3`, `label 12/16/600/0`, `pill 11/14/700/0`, `buttonSm 13/18/700/0`. O'zgaradi: `button → 15/20/700/0`, `tab → 10/12/600/0`.
- `spacing.ts`: `radius = { field: 14, button: 14, banner: 14, card: 18, sheet: 24, sheetLg: 28, badge: 20, pill: 999 }`, `hit = { button: 52, buttonSm: 44, field: 50, numberW: 74, numberH: 40, tabBar: 62 }`. `screen.paddingH` → 14 (varaq ichki chetlari, maketdagidek). Mavjud kod shu nomlarni ishlatadi — qiymat o'zgarishi kutilgan vizual o'zgarish.
- `Button`:
```ts
export type ButtonVariant = 'primary' | 'outline' | 'danger' | 'dangerSolid' | 'ghost';
export type Progress = { step: number; total: number; label: string };
export function Button(p: {
  label: string; onPress: () => void; variant?: ButtonVariant; size?: 'md' | 'sm';
  icon?: (p: IconProps) => JSX.Element; loading?: boolean; progress?: Progress | null;
  loadingLabel?: string | null; // Task 9 da o'chiriladi
  disabled?: boolean; style?: StyleProp<ViewStyle>; testID?: string;
}): JSX.Element;
```
Tonlar: primary `ink` fon / `onHero` matn; outline `card` fon, 1.5 `outlineBorder`, `ink` matn; danger `card` fon, 1.5 `dangerBorder`, `danger` matn; dangerSolid `danger` fon / oq matn; ghost shaffof, chegarasiz, `inkSoft` matn. `md` → minHeight 52, `button` matn; `sm` → minHeight 44, `buttonSm` matn. Ikonka matn chapida, 9 dp oraliq, matn rangida. `loading && progress` → `ActivityIndicator` + `progress.label` + `` `${step}/${total}` `` (60% shaffoflik) va absolyut to'lish qatlami testID `${testID}-fill`, `width: Math.round(step/total*100)+'%'`, fon `heroChip`.

- [ ] **Step 1: `Button.test.tsx` ni qayta yozing** (mavjud tuzilishda). Asosiy tekshiruvlar:
```ts
expect(flat(getByTestId('b')).backgroundColor).toBe('#0D0D0D');        // primary
expect(flat(getByText('Kirish')).color).toBe('#FFFFFF');
// outline
expect(s.backgroundColor).toBe('#FFFFFF'); expect(s.borderWidth).toBe(1.5); expect(s.borderColor).toBe('#E2E1DE');
// danger
expect(s.borderColor).toBe('#F3D3CF'); expect(flat(getByText('Chiqish')).color).toBe('#C62828');
// dangerSolid
expect(flat(getByTestId('b')).backgroundColor).toBe('#C62828');
// ghost
expect(s.borderWidth ?? 0).toBe(0); expect(flat(getByText('Bekor qilish')).color).toBe('#55575C');
// o'lchamlar
expect(flat(getByTestId('b')).minHeight).toBe(52);  // md
expect(flat(getByTestId('s')).minHeight).toBe(44);  // size="sm"
// progress
render(<Button label="Ketdim" onPress={fn} loading progress={{ step: 4, total: 6, label: 'Selfi olinmoqda' }} testID="b" />);
expect(queryByText('Ketdim')).toBeNull();
expect(getByText('Selfi olinmoqda')).toBeTruthy(); expect(getByText('4/6')).toBeTruthy();
expect(flat(getByTestId('b-fill')).width).toBe('67%');
fireEvent.press(getByTestId('b')); expect(fn).not.toHaveBeenCalled();
// loading primary fon qora qoladi; disabled — '#F1F1F3', onPress chaqirilmaydi; oddiy — onPress 1 marta
```
- [ ] **Step 2:** `npx jest __tests__/components/Button.test.tsx` → FAIL.
- [ ] **Step 3:** Tokenlar va `Button` ni amalga oshiring.
- [ ] **Step 4:** Shu test → PASS. `npx jest` — `Field.test` ("minHeight 52") va `AppText.test` dagi o'zgargan qiymatlar yiqilsa, ular Task 3/4 da qayta yoziladi; hozir faqat token qiymati o'zgargani uchun yiqilgan tekshiruvni yangi qiymatga moslang. `npx tsc --noEmit` → 0.
- [ ] **Step 5: Commit** — `feat(theme): B tokens, typography scale and black primary Button`

---

### Task 3: Ikonkalar, `IconCircle`, `Pill`, `Banner`, `Card`, `Skeleton`, `EmptyState`

**Files:**
- Create: `mobile/src/components/icons/{Mail,Lock,Eye,EyeOff,Calendar,Camera,Clock,Check,Logout,Sync,WifiOff,Alert}Icon.tsx`, `mobile/src/components/IconCircle.tsx`, `mobile/src/components/Pill.tsx`, `mobile/src/components/Card.tsx`, `mobile/src/components/Skeleton.tsx`, `mobile/src/components/EmptyState.tsx`
- Modify: `mobile/src/components/Banner.tsx`
- Test: `mobile/__tests__/components/icons.test.tsx` (kengaytiriladi), `mobile/__tests__/components/primitives.test.tsx` (yangi), `mobile/__tests__/components/feedback.test.tsx` (Banner qismi qayta yoziladi)

**Interfaces — Produces:**
- Har ikonka: `export function XIcon(p: IconProps): JSX.Element` — mavjud `PinIcon` uslubida (viewBox 24, `fill="none"`, `strokeWidth={2.1}`, round cap/join). Yo'llar maketlardagi `<svg>` lardan olinadi: Mail/Lock/Eye — `01-kirish.html`; Calendar — `02-bosh.html`; Camera/Clock/Check — `03-davomat.html`; Logout — `05-profil.html`; Sync/WifiOff/Alert — `06-umumiy-holatlar.html`; EyeOff — Eye + diagonal `M3 3l18 18`.
- `IconCircle({ tone, size, children, testID })`, `tone: 'ok'|'warn'|'danger'|'brand'|'locked'|'neutral'` → fon `okBg|warnBg|dangerBg|brandSoft|lockedBg|fieldBg`, `borderRadius: size/2`.
- `Pill({ label, tone, icon?, dot?, testID })`, `tone: 'ok'|'warn'|'neutral'|'onHero'` → (fon, matn): ok `okBg/okInk` (+ `dot` bo'lsa 6 dp `ok` nuqta), warn `warnBg/warnInk`, neutral `fieldBg/inkSoft`, onHero `heroChip/onHero`; `pill` tipografiya, radius `pill`, `numberOfLines={1}`.
- `Card({ children, style, testID })` — `card` fon, radius 18, soya (`elevation: 2`, `shadowColor: colors.shadow`, `shadowOpacity 0.06`, `shadowRadius 16`, `shadowOffset {0,4}`). `CardRow({ children, footer?, last?, testID })` — `paddingVertical 11–14, paddingHorizontal 14–16`, `last` bo'lmasa pastida 1 dp `cardDivider`; `footer` → fon `cardFooter`, pastki burchaklar 18.
- `Skeleton({ width, height, onHero?, testID })` — fon `skeleton` yoki `heroSkeleton`, radius 8.
- `EmptyState({ icon, tone, title, body, size = 96, action?, testID })` — markazda `IconCircle` + `sectionTitle` + `muted` body + ixtiyoriy action.
- `Banner({ tone, message, title?, testID })`, `tone: 'error'|'success'|'warn'` (`'info'` olib tashlanadi; mavjud `tone="info"` chaqiruvlari `warn` ga o'tkaziladi). Ko'rinish — `06` maket: radius 14, chapda 22 dp doira (`dangerIconBg|okIconBg|warnIconBg`) ichida 13 dp ikonka (`Alert|Check|Sync`), matn rangi `dangerInk|okInk|warnInk`; `title` bo'lsa qalin, keyin probel va `message`. `message` bo'sh → `null`.

- [ ] **Step 1: Failing testlar.**
  - `icons.test.tsx`: mavjud "umumiy chiziq uslubi" testini **barcha 16** ikonka ustida aylantiring (`[HomeIcon, PinIcon, …, AlertIcon].forEach`).
  - `primitives.test.tsx`: `IconCircle tone="warn" size={34}` → `backgroundColor '#FDF3E0'`, `borderRadius 17`; `Pill tone="ok" dot` → fon `'#E7F4EC'`, matn `'#0E6B39'`, `getByTestId('p-dot')` fon `'#14884A'`; `Pill tone="onHero"` → fon `'rgba(255,255,255,0.18)'`, matn `'#FFFFFF'`; `Card` → `borderRadius 18`, `backgroundColor '#FFFFFF'`; `CardRow footer` → `'#FAFAF9'`; `Skeleton onHero` → `'rgba(255,255,255,0.25)'`; `EmptyState` sarlavha va matnni chizadi.
  - `feedback.test.tsx` Banner qismi: `error` → fon `'#FDECEA'`, matn `'#8E1C1C'`, `getByTestId('b-icon')` fon `'#F8C9C4'`; `warn` + `title="Navbatga qo'shildi."` → `getByText("Navbatga qo'shildi.")` qalin (`fontWeight '700'`); `success` → `'#E7F4EC'`/`'#0E6B39'`; bo'sh xabar → `toJSON()` null.
- [ ] **Step 2:** `npx jest __tests__/components` → yangi testlar FAIL.
- [ ] **Step 3:** Amalga oshiring; `ChildrenCountSection.tsx` dagi `tone="info"` → `tone="warn"`.
- [ ] **Step 4:** `npx jest` va `npx tsc --noEmit` → yashil.
- [ ] **Step 5: Commit** — `feat(components): icon set and B primitives (Pill, Card, IconCircle, Skeleton, EmptyState, Banner)`

---

### Task 4: `Field` — ikonka, fokus halqasi, ko'z tugmasi, raqam varianti

**Files:**
- Modify: `mobile/src/components/Field.tsx`
- Test: `mobile/__tests__/components/Field.test.tsx`

**Interfaces — Consumes:** `MailIcon`, `LockIcon`, `EyeIcon`, `EyeOffIcon` (Task 3). **Produces:**
```ts
type Props = TextInputProps & {
  label?: string; variant?: 'text' | 'number';
  icon?: (p: IconProps) => JSX.Element; testID?: string;
};
export function Field(p: Props): JSX.Element;
```
- `text`: label (`label` tipografiya, `inkSoft`, 6 dp pastda), keyin halqa o'rovchisi (testID `${testID}-halo`: `borderWidth 4`, `borderRadius radius.field + 4`, `margin -4`; fokusda `borderColor brandSoft`, aks holda `'transparent'`), ichida qator: ikonka (18 dp; fokusda `brand`, aks holda `inkFaint`), `TextInput` (minHeight 50, radius 14, 1.5 `fieldBorder` → fokusda `brand`, fon `card`), `secureTextEntry` bo'lsa o'ngda ko'z tugmasi (testID `${testID}-eye`, `accessibilityLabel` "Parolni ko'rsatish"/"Parolni yashirish", bosilganda `secureTextEntry` almashadi).
- `number`: label yo'q; 74×40, `fieldBg` fon, 1.5 `'transparent'` chegara, `numberInput` tipografiya, `textAlign 'center'`, `keyboardType` sukut bo'yicha `'number-pad'`; fokusda fon `card`, chegara `brand`, o'sha halqa.
- `color: ink` va `placeholderTextColor: inkFaint` har doim, caller `style` dan keyin (mavjud `lockedInk` tamoyili). Caller'ning `onFocus`/`onBlur` i ham chaqiriladi.

- [ ] **Step 1: Testlarni yangilang/qo'shing** (mavjud 7 ta saqlanadi, "minHeight 52" → 50):
```ts
fireEvent(getByTestId('f'), 'focus');
expect(flat(getByTestId('f')).borderColor).toBe('#EF5B3C');
expect(flat(getByTestId('f-halo')).borderColor).toBe('#FFE9E2');
fireEvent(getByTestId('f'), 'blur');
expect(flat(getByTestId('f-halo')).borderColor).toBe('transparent');
// caller onFocus ham chaqiriladi
// ko'z: secureTextEntry → true; press f-eye → false; yana → true
// number varianti: width 74, height 40, backgroundColor '#F4F3F1', textAlign 'center', keyboardType 'number-pad', color '#0D0D0D'
```
- [ ] **Step 2:** `npx jest __tests__/components/Field.test.tsx` → FAIL.
- [ ] **Step 3:** Amalga oshiring.
- [ ] **Step 4:** Shu test + `npx jest __tests__/architecture.test.ts` → PASS; to'liq `npx jest`, `tsc` → yashil.
- [ ] **Step 5: Commit** — `feat(field): icon, focus halo, password eye toggle and number variant`

---

### Task 5: Hero qatlami, status bar, "Internet yo'q" kapsulasi, jest setup

**Files:**
- Create: `mobile/src/components/GradientFill.tsx`, `mobile/src/components/Hero.tsx`, `mobile/src/components/HeroScreen.tsx`, `mobile/src/components/OfflinePill.tsx`, `mobile/jest.setup.js`
- Modify: `mobile/src/attendance/attendanceQueue.ts`, `mobile/src/App.tsx`, `mobile/src/navigation/RootNavigator.tsx`, `mobile/jest.config.js`
- Delete: `mobile/src/components/OfflineIndicator.tsx`
- Test: `mobile/__tests__/components/hero.test.tsx`, `mobile/__tests__/components/OfflinePill.test.ts`, `mobile/__tests__/attendanceQueue.test.ts` (qo'shiladi)

**Interfaces — Produces:**
```ts
// attendanceQueue.ts
export function attendancePendingCount(): number;  // kind !== 'location_events'
export function queuedCaptureToday(kind: 'check_in' | 'check_out', now?: Date): string | null;
// kind yoki `replace_${kind}` bo'lgan, capturedAt Toshkent sanasi now bilan bir xil bo'lgan eng oxirgi yozuvning capturedAt'i (uzTime.tashkentDateKey)
// OfflinePill.tsx
export function offlinePillText(online: boolean, pending: number): { text: string; tone: 'danger' | 'warn' } | null;
export function OfflinePill(): JSX.Element | null; // NetInfo + 5 s interval attendancePendingCount (OfflineIndicator dagi kabi)
// GradientFill.tsx — absolyut to'ldiruvchi SVG LinearGradient (brandGradient, 0/45/100%, x1=0 y1=0 x2=1 y2=1 objectBoundingBox)
export function GradientFill(): JSX.Element;
// Hero.tsx
export function Hero(p: { children: ReactNode; minHeight?: number /*150*/; circles?: 1 | 2; overlap?: number /*22 — HeroScreen.overlap bilan bir xil berilsin*/; testID?: string }): JSX.Element;
// paddingTop = insets.top + 8, paddingHorizontal 20, paddingBottom = overlap + 14; ichida GradientFill, bezak doiralari (heroCircle o'ng-yuqori 200 dp, heroCircle2 chap-past 120–140 dp), birinchi bola sifatida <OfflinePill/>
// HeroScreen.tsx
export function HeroScreen(p: {
  hero: ReactNode; children: ReactNode; sheet?: 'canvas' | 'white'; overlap?: number /*22*/;
  refreshControl?: ReactElement; keyboardShouldPersistTaps?: 'handled'; testID?: string;
}): JSX.Element;
// KeyboardAvoidingView (iOS padding) → ScrollView(contentContainer flexGrow 1) [hero, varaq(marginTop -overlap, yuqori radius 24 (white bo'lsa 28), fon canvas/card, padding 16/14, flexGrow 1)]
// + absolyut qotgan tasma testID 'status-strip': top 0, height insets.top, fon brandTop
```
`offlinePillText` matnlari: `(false, 0)` → `"Internet yo'q"`/danger; `(false, 2)` → `"Internet yo'q · 2 ta yuborilmagan"`/danger; `(true, 1)` → `"1 ta yuborilmagan"`/warn; `(true, 0)` → `null`. Ko'rinish: oq kapsula, ikonka `WifiOff` (danger) yoki `Sync` (warn), matn `danger`/`warnInk`, `pill` tipografiya.

`App.tsx`: `<StatusBar barStyle="light-content" translucent backgroundColor="transparent" />`; `<OfflineIndicator />` olib tashlanadi. `RootNavigator` `restoring` holati: `GradientFill` fonida oq `ActivityIndicator` (testID `restore-screen`).

`jest.setup.js` (`jest.config.js` → `setupFiles: ['./jest.setup.js']`): `react-native-safe-area-context` → `require('react-native-safe-area-context/jest/mock').default`; `@react-native-community/netinfo` → `require('@react-native-community/netinfo/jest/netinfo-mock.js')`; `react-native-config` → `{ __esModule: true, default: { API_BASE_URL: 'http://test', APP_ENV: 'development', VERSION_NAME: '1.2.0' } }`; `react-native-mmkv` → in-memory `MMKV` (har instansiya o'z `Map`i; `getString/set/delete`). Fayl ichidagi mavjud `jest.mock` lar ustun qoladi.

- [ ] **Step 1: Failing testlar.**
```ts
// OfflinePill.test.ts — yuqoridagi 4 holat (toEqual)
// attendanceQueue.test.ts
enqueue({ kind: 'location_events', ... }); enqueue({ kind: 'check_in', ... });
expect(attendancePendingCount()).toBe(1);
const now = new Date('2026-11-26T05:00:00Z');
enqueue({ kind: 'replace_check_in', capturedAt: '2026-11-26T03:10:00Z', ... });
expect(queuedCaptureToday('check_in', now)).toBe('2026-11-26T03:10:00Z');
expect(queuedCaptureToday('check_out', now)).toBeNull();
// kechagi yozuv: capturedAt '2026-11-25T03:10:00Z' → null
// hero.test.tsx
render(<HeroScreen hero={<Hero><Text>Davomat</Text></Hero>}><Text>ichida</Text></HeroScreen>);
expect(getByText('Davomat')).toBeTruthy(); expect(getByText('ichida')).toBeTruthy();
expect(flat(getByTestId('status-strip')).backgroundColor).toBe('#F47A55');
```
- [ ] **Step 2:** `npx jest __tests__/components/hero.test.tsx __tests__/components/OfflinePill.test.ts __tests__/attendanceQueue.test.ts` → FAIL.
- [ ] **Step 3:** Amalga oshiring.
- [ ] **Step 4:** Shu testlar → PASS; `npx jest`, `tsc` → yashil.
- [ ] **Step 5: Commit** — `feat(layout): gradient Hero, HeroScreen, fixed status strip and in-hero offline pill`

---

### Task 6: Foydalanuvchini telefonda saqlash (spec 5.7 — tasdiqlangan istisno)

**Files:**
- Create: `mobile/src/auth/userStore.ts`
- Modify: `mobile/src/auth/authStore.ts`, `mobile/src/auth/useSessionRestore.ts`
- Test: `mobile/__tests__/authStore.test.ts`, `mobile/__tests__/userStore.test.ts`

**Interfaces — Produces:**
```ts
// userStore.ts — MMKV({ id: 'auth' }), kalit 'auth.user.v1'
export function saveUser(u: AuthUser): void;
export function loadUser(): AuthUser | null;   // yo'q yoki buzilgan JSON → null; id/name/email maydonlari yo'q → null
export function clearUser(): void;
```
`setSession`: `saveToken` dan keyin `saveUser(user)`. `clearSession`: `clearUser()`. `useSessionRestore`: muvaffaqiyatli tekshiruvdan keyin `useAuthStore.setState({ status: 'authenticated', user: loadUser() })`. `AuthUser` tipi `authStore.ts` da qoladi (`userStore` faqat `import type`).

- [ ] **Step 1: Failing testlar.** `userStore.test.ts`: saqlash→o'qish bir xil obyekt; `clearUser` → `null`; buzilgan JSON (`'{x'`) → `null`. `authStore.test.ts` ga: `setSession` dan keyin `loadUser()?.name === 'A'`; `clearSession` dan keyin `loadUser() === null`.
- [ ] **Step 2:** `npx jest __tests__/userStore.test.ts __tests__/authStore.test.ts` → FAIL.
- [ ] **Step 3:** Amalga oshiring.
- [ ] **Step 4:** → PASS; `npx jest`, `tsc` → yashil.
- [ ] **Step 5: Commit** — `feat(auth): persist the signed-in user so name survives app restarts`

---

### Task 7: Tab panel va Kirish ekrani

**Files:**
- Create: `mobile/src/assets/gt_mark.png`
- Modify: `mobile/src/navigation/MainTabs.tsx`, `mobile/src/screens/LoginScreen.tsx`
- Test: `mobile/__tests__/screens/LoginScreen.test.tsx`

**Interfaces — Consumes:** `HeroScreen`, `Hero`, `Field` (`icon`), `Button`, `Banner`, `MailIcon`, `LockIcon`.

`gt_mark.png`: `android/app/src/main/res/mipmap-xxxhdpi/ic_launcher_foreground.png` dan adaptive-icon chetlarini kesib (markaziy ~66%), shaffof fonda. Kesish uchun bir martalik skript scratchpad'da; faylning o'zi commit qilinadi.

Tab panel (`01–05` maketlar): `tabBarHideOnKeyboard: true`; `tabBarStyle` — height 62, fon `card`, `borderTopWidth: 0`, `elevation: 8`; tepada soya o'rniga `tabBarBackground` bilan 6 dp li SVG gradient tasma (`colors.tabShadow` → shaffof, yuqoriga). Ikonka `tabBarIcon` ichida 48×28 kapsulada: faol — fon `brandSoft`, ikonka `brand`; nofaol — shaffof, `inkFaint`. `tabBarActiveTintColor: colors.brand`, label `typography.tab`.

Kirish (`01-kirish.html` B): `HeroScreen sheet="white" overlap={28}`; `Hero minHeight={250} circles={2} overlap={28}`: oq 64×64 radius 20 nishon ichida `gt_mark.png`, `heroTitleLg` "Xush kelibsiz!", `heroSub` "Global Ta'm · Oshpaz kabineti". Varaq: `Banner error` (maydonlar **ustida**), `Field label="Email" icon={MailIcon} placeholder="email@globaltam.uz"` (mavjud proplar), `Field label="Parol" icon={LockIcon} secureTextEntry`, `Button label="Kirish"`, pastda markazda `caption` `inkFaint` "Parolni unutdingizmi? Texnologga murojaat qiling". Xato `userFacingError(e)` orqali. `onSubmit` mantiqi o'zgarmaydi.

- [ ] **Step 1: Failing test** (`login` va `setSession` mock):
```ts
expect(getByText('Xush kelibsiz!')).toBeTruthy();
expect(getByText("Parolni unutdingizmi? Texnologga murojaat qiling")).toBeTruthy();
fireEvent.press(getByText('Kirish'));                         // bo'sh maydonlar
expect(getByText('Email va parolni kiriting')).toBeTruthy();
// login rad etadi: { response: { data: { error: 'invalid_credentials' } } }
expect(await findByText("Email yoki parol noto'g'ri.")).toBeTruthy();
```
- [ ] **Step 2:** `npx jest __tests__/screens/LoginScreen.test.tsx` → FAIL.
- [ ] **Step 3:** Amalga oshiring.
- [ ] **Step 4:** → PASS; `npx jest`, `tsc` → yashil.
- [ ] **Step 5: Commit** — `feat(login,tabs): B login screen and pill tab bar`

---

### Task 8: Bosh sahifa (bolalar soni)

**Files:**
- Create: `mobile/src/lib/childrenCountView.ts`, `mobile/src/components/childrenCount/HomeHero.tsx`, `mobile/src/components/childrenCount/CountForm.tsx`, `mobile/src/components/childrenCount/CountSubmitted.tsx`, `mobile/src/components/childrenCount/CountNotice.tsx`, `mobile/src/components/childrenCount/LoadFailed.tsx`
- Modify: `mobile/src/api/childrenCount.ts`, `mobile/src/components/ChildrenCountSection.tsx`, `mobile/src/screens/HomeScreen.tsx`
- Test: `mobile/__tests__/lib/childrenCountView.test.ts`, `mobile/__tests__/screens/HomeScreen.test.tsx`

**Interfaces — Produces:**
```ts
// api/childrenCount.ts
export const CHILDREN_COUNT_KEY = ['children-count', 'today'] as const;
export function useChildrenCountToday(): UseQueryResult<ChildrenCountState>; // staleTime 30_000; Davomat/Profil ham ishlatadi
// lib/childrenCountView.ts — spec 5.2 tartibi
export type ChildrenCountView = 'submitted' | 'not_ready' | 'closed' | 'no_groups' | 'open';
export function childrenCountView(d: ChildrenCountState): ChildrenCountView;
// components/childrenCount/LoadFailed.tsx — Bosh va Davomat uchun umumiy
export function LoadFailed(p: { message: string; onRetry?: () => void }): JSX.Element;
// EmptyState: WifiOff, tone danger, size 56, sarlavha "Ma'lumotni yuklab bo'lmadi", matn = message, action = outline sm "Qayta urinish" (onRetry bo'lsa)
```
- `HomeHero({ data?: ChildrenCountState; loading: boolean })` — `greeting(now)` + `, ${displayName}` yoki `!`; `heroTitle` bog'cha nomi `numberOfLines={2}` yoki (loading) `Skeleton onHero` 150×20; `Pill tone="onHero" icon={CalendarIcon}` `uzDateLabel(now)`.
- `ChildrenCountSection`: `useChildrenCountToday` + mavjud mutation/validatsiya **o'zgarmagan holda**; `childrenCountView` bo'yicha komponent tanlaydi. Yuklanish — `Skeleton` karta. Xato — `LoadFailed message={userFacingError(err)}`, `onRetry` faqat `errorCode(err) !== 'kindgarden_not_assigned'`.
- `CountForm` (`02` 1-telefon): sarlavha qatori (`sectionTitle` "Bolalar soni" + `muted` "Ertangi menyu uchun"; o'ngda `Pill tone="ok" dot` `` `${from}–${to} ochiq` ``), `Card` ichida har guruh `CardRow` (nom `bodyStrong` o'raladi, o'ngda `Field variant="number"`, placeholder yo'q), `Banner error` (karta va Jami orasida), Jami qatori (`muted` "Jami" chapda, `total` `` `${n} bola` `` yoki "—" o'ngda, `sumFilled` bilan), `Button` "Yuborish".
- `CountSubmitted` (`02` 2-telefon): `Card` — `IconCircle ok 64` + `CheckIcon`, `sectionTitle` "Yuborildi", `muted` `submittedLabel(...)` (null bo'lsa yo'q); ikkinchi `Card` — guruh qatorlari (son yoki "—"), oxirgi `CardRow footer` "Jami" | yig'indi; ostida `muted` "O'zgartirish kerak bo'lsa, texnologga murojaat qiling."
- `CountNotice({ kind, window? })` (`02` 3-telefon): `IconCircle warn 64` + `ClockIcon`. Matnlar:
  - `closed`: sarlavha "Hozir yuborish vaqti emas", matn "Bolalar soni har kuni {from} – {to} oralig'ida yuboriladi", `Pill neutral` "Hozir: {current_tashkent}" va `nextOpeningLabel` (null bo'lsa yo'q).
  - `not_ready`: "Menyu hali tayyor emas" / "Texnolog kelajak kun uchun menyu sozlamagan. Texnologga murojaat qiling."
  - `no_groups`: "Yosh guruhlari biriktirilmagan" / "Bog'changizga yosh guruhlari biriktirilmagan. Texnologga murojaat qiling."
  - Uchalasida ostida `caption` "Pastga torting — yangilash".
- `HomeScreen`: `HeroScreen hero={<HomeHero …/>} refreshControl={<RefreshControl colors={[colors.brand]} …/>} keyboardShouldPersistTaps="handled"`; mavjud invalidatsiya mantiqi saqlanadi.

- [ ] **Step 1: Failing testlar.**
```ts
// childrenCountView.test.ts — base: allowed true, nextday_ready true, submitted false, age_ranges [{id:1}]
expect(childrenCountView({ ...base, submitted: true, time_window: { ...w, allowed: false } })).toBe('submitted');
expect(childrenCountView({ ...base, nextday_ready: false, time_window: { ...w, allowed: false } })).toBe('not_ready');
expect(childrenCountView({ ...base, time_window: { ...w, allowed: false } })).toBe('closed');
expect(childrenCountView({ ...base, age_ranges: [] })).toBe('no_groups');
expect(childrenCountView(base)).toBe('open');
// HomeScreen.test.tsx — QueryClientProvider (retry: false), getTodayState/submitChildrenCount mock, authStore user {name:'Dilnoza Aliyeva'}
// ochiq: '1–3 yosh' ga 18, '3–7 yosh' ga 42, uchinchisi bo'sh →
expect(getByText('60 bola')).toBeTruthy();
fireEvent.press(getByText('Yuborish'));
expect(getByText('"Qisqa muddatli" uchun butun musbat son kiriting.')).toBeTruthy();
expect(submitChildrenCount).not.toHaveBeenCalled();
// submitted + allowed=false → 'Yuborildi' ko'rinadi, 'Hozir yuborish vaqti emas' yo'q
// getTodayState rad etadi (new Error('Network Error')) → "Ma'lumotni yuklab bo'lmadi" va 'Qayta urinish'; 'Network Error' matni yo'q
// user = null → hero'da /^Xayrli (tong|kun|kech)!$/
```
- [ ] **Step 2:** `npx jest __tests__/lib/childrenCountView.test.ts __tests__/screens/HomeScreen.test.tsx` → FAIL.
- [ ] **Step 3:** Amalga oshiring.
- [ ] **Step 4:** → PASS; `npx jest`, `tsc` → yashil.
- [ ] **Step 5: Commit** — `feat(home): B children-count screen with all five states`

---

### Task 9: Davomat

**Files:**
- Create: `mobile/src/attendance/timeline.ts`, `mobile/src/components/attendance/AttendanceTimeline.tsx`, `mobile/src/components/attendance/AttendanceHero.tsx`
- Modify: `mobile/src/screens/AttendanceScreen.tsx`, `mobile/src/attendance/attendanceStore.ts`, `mobile/src/components/Button.tsx` (`loadingLabel` o'chiriladi)
- Test: `mobile/__tests__/attendance/timeline.test.ts`, `mobile/__tests__/screens/AttendanceScreen.test.tsx`

**Interfaces — Consumes:** `utcIsoToTashkentHHMM`, `workDuration`, `uzDateLabel` (Task 1); `queuedCaptureToday`, `attendancePendingCount` (Task 5); `useChildrenCountToday` (Task 8); `LoadFailed` (Task 8); `Progress` (Task 2). **Produces:**
```ts
// timeline.ts
export type StepState =
  | { kind: 'pending' } | { kind: 'locked' }
  | { kind: 'queued'; capturedHHMM: string }
  | { kind: 'done'; timeHHMM: string; distanceM: number | null; late: boolean };
export type Timeline = { checkIn: StepState; checkOut: StepState; workDuration: string | null };
export function buildTimeline(today: AttendanceRow | null, queued: { check_in: string | null; check_out: string | null }): Timeline;
export type ActionKind = 'check_in' | 'check_out' | 'replace_check_in' | 'replace_check_out';
export function attendanceActions(t: Timeline): {
  buttons: Array<{ kind: ActionKind; label: string; variant: 'primary' | 'outline'; size: 'md' | 'sm'; icon?: 'camera' }>;
  hint: string | null;
};
```
`buildTimeline` qoidalari: checkIn — `check_in_at` bor → done (`late = check_in_is_late`); aks holda queued (bo'lsa) yoki pending. checkOut — `check_out_at` bor → done (`late: false`); checkIn done bo'lmasa → locked; queued (bo'lsa) yoki pending. `workDuration` — ikkalasi done va `check_in_replaced_count === 0 && check_out_replaced_count === 0` bo'lsa `workDuration(in, out)`, aks holda `null`.

`attendanceActions`:
| checkIn | checkOut | tugmalar | hint |
|---|---|---|---|
| pending | — | primary md "Keldim" (camera) | "Selfi va joylashuv avtomatik olinadi. Kechiksangiz ham bog'chada turib bosing — tizim "kechikkan" deb belgilaydi." |
| queued | — | yo'q | null |
| done | pending | primary md "Ketdim" (camera); outline sm "Kelishni qayta yuborish" | null |
| done | queued | outline sm "Kelishni qayta yuborish" | null |
| done | done | outline sm "Kelishni qayta", outline sm "Ketishni qayta" (yonma-yon) | "Bugungi davomat to'liq. Ertaga ko'rishguncha!" |

`AttendanceTimeline` (`03`, `06` 1-telefon) — `Card` ichida ikki qadam, orasida 2 dp bog'lovchi (`lineDone` agar checkIn done, aks holda `linePending`). Matnlar (`stepLabel` sarlavha / `timeValue` qiymat / `muted` izoh):
- checkIn pending: "Kelish" / "Hali kelmadingiz" / "Bog'chaga yetib kelgach "Keldim" ni bosing" — `IconCircle warn` + Clock.
- checkIn done: "Keldingiz" / `HH:MM` (+ `late` → `Pill warn` "kechikkan") / `Bog'cha markazidan ${d} m` (d null → yo'q) — `IconCircle ok` + Check.
- queued: "Kelish" yoki "Ketish" / "Yuborilmoqda…" / `${HH:MM} da olindi · navbatda` — `IconCircle warn` + Sync.
- checkOut locked: "Ketish" / "—:—" (`lockedInk`) — `IconCircle locked` + Lock.
- checkOut pending: "Ketish" / "Hali ketmadingiz" — warn + Clock. done: "Ketdingiz" / `HH:MM` / masofa.
- `workDuration` → `CardRow footer`: "Bugungi ish vaqti" | qiymat.

`AttendanceHero`: `heroSub` `uzDateLabel(now)`, `heroTitle` "Davomat", `Pill onHero icon={PinIcon}` — bog'cha nomi (`useChildrenCountToday().data?.kindgarden.kingar_name`, bo'lmasa `` `Bog'cha #${kindgarden.id}` ``, kindgarden yo'q bo'lsa pill yo'q; bu so'rov xatosi hech qayerda ko'rsatilmaydi), `Pill onHero` `` `${radius} m radius` `` (kindgarden bo'lsa).

`AttendanceScreen`:
- Ekrandagi lokal `ActionKind` tipi o'chiriladi, `timeline.ts` dagisi import qilinadi; tugmalar `attendanceActions(buildTimeline(...))` dan chiziladi.
- `progress` state → `Progress | null`; qadamlar `{step, total: 6, label}`: 1 "Ruxsatlar tekshirilmoqda", 2 "Joylashuv aniqlanmoqda", 3 "Masofa hisoblanmoqda", 4 "Selfi olinmoqda", 5 "Serverga yuborilmoqda", 6 "Yangilanmoqda". Qadamlar tartibi va mantiq o'zgarmaydi.
- `actionLoading !== null` → tugmalar o'rnida **bitta** `Button primary md loading progress={progress}` (testID `action-progress`), boshqa tugma yo'q.
- Muvaffaqiyat banneri (`'… qabul qilindi ✅'`) **olib tashlanadi** (`setActionInfo` muvaffaqiyatda chaqirilmaydi). Navbatga qo'shilganda: `Banner tone="warn" title="Navbatga qo'shildi." message="Internet tiklanganda avtomatik yuboriladi."`. Xatolar — `Banner error`, matn `userFacingError` (server javobi bor joyda mavjud `mapServerError` chaqiruvi saqlanadi; xom `e.message` yo'q).
- Navbat: `queuedCaptureToday('check_in'|'check_out')` render paytida o'qiladi (MMKV sinxron) va `onAction` tugagach qayta chiziladi.
- `today === null` va (`loading` yoki hali bir marta ham muvaffaqiyatli yuklanmagan) → vaqt chizig'i o'rnida `Skeleton` karta, **tugma yo'q**. `error && today === null` → `LoadFailed message={error} onRetry={refresh}`, tugma yo'q. Buning uchun `attendanceStore` ga `loaded: boolean` (birinchi muvaffaqiyatli `refresh` da `true`) qo'shiladi va `error` = `userFacingError(e)`.
- Debug paneli o'zgarmaydi: `{__DEV__ && debugInfo && (`.
- `HeroScreen hero={<AttendanceHero/>} refreshControl={…}`.

- [ ] **Step 1: Failing testlar.**
```ts
// timeline.test.ts
const row = (o: Partial<AttendanceRow>) => ({ check_in_at: null, check_out_at: null, check_in_is_late: false,
  check_in_distance_m: null, check_out_distance_m: null, check_in_replaced_count: 0, check_out_replaced_count: 0, ...o } as AttendanceRow);
const none = { check_in: null, check_out: null };
expect(buildTimeline(null, none)).toEqual({ checkIn: { kind: 'pending' }, checkOut: { kind: 'locked' }, workDuration: null });
expect(buildTimeline(null, { check_in: '2026-11-26T03:10:00Z', check_out: null }).checkIn).toEqual({ kind: 'queued', capturedHHMM: '08:10' });
const both = buildTimeline(row({ check_in_at: '2026-11-26T03:02:00.000000Z', check_in_distance_m: 38,
  check_out_at: '2026-11-26T12:12:00.000000Z', check_out_distance_m: 52 }), none);
expect(both.checkIn).toEqual({ kind: 'done', timeHHMM: '08:02', distanceM: 38, late: false });
expect(both.workDuration).toBe('9 soat 10 daq');
expect(buildTimeline(row({ check_in_at: '2026-11-26T03:02:00.000000Z', check_out_at: '2026-11-26T12:12:00.000000Z',
  check_in_replaced_count: 1 }), none).workDuration).toBeNull();
expect(attendanceActions(buildTimeline(null, none)).buttons.map(b => b.label)).toEqual(['Keldim']);
expect(attendanceActions(buildTimeline(null, { check_in: '2026-11-26T03:10:00Z', check_out: null })).buttons).toEqual([]);
expect(attendanceActions(both).buttons.map(b => b.label)).toEqual(['Kelishni qayta', 'Ketishni qayta']);
expect(attendanceActions(both).hint).toBe("Bugungi davomat to'liq. Ertaga ko'rishguncha!");
// AttendanceScreen.test.tsx — mock: attendanceStore (setState), permissions, locationService, selfieCapture, mockGps, attendanceApi, useChildrenCountToday
// loaded=false, today=null, loading=true → queryByText('Keldim') null
// loaded=false, error="Internet bilan bog'lanib bo'lmadi. Qayta urinib ko'ring." → "Ma'lumotni yuklab bo'lmadi", queryByText('Keldim') null
// loaded=true, today=null, navbatda bugungi check_in → getByText('Yuborilmoqda…'), queryByText('Keldim') null
// loaded=true, today=row(check_in_at '…T03:47:00.000000Z', late true) → getByText('08:47'), getByText('kechikkan'), getByText('Ketdim')
// hech qanday matnda '✅' yo'q
```
- [ ] **Step 2:** `npx jest __tests__/attendance/timeline.test.ts __tests__/screens/AttendanceScreen.test.tsx` → FAIL.
- [ ] **Step 3:** Amalga oshiring; `Button` dan `loadingLabel` ni o'chiring va `Button.test` dagi tegishli testni olib tashlang.
- [ ] **Step 4:** → PASS; `npx jest` (arxitektura testi ham), `tsc` → yashil.
- [ ] **Step 5: Commit** — `feat(attendance): timeline card, in-button progress, queued state, Tashkent times`

---

### Task 10: Xabarlar, Profil va `ConfirmDialog`

**Files:**
- Create: `mobile/src/components/ConfirmDialog.tsx`
- Modify: `mobile/src/screens/NotificationsScreen.tsx`, `mobile/src/screens/ProfileScreen.tsx`, `mobile/src/types/config.d.ts`
- Test: `mobile/__tests__/components/ConfirmDialog.test.tsx`, `mobile/__tests__/screens/ProfileScreen.test.tsx`

**Interfaces — Produces:**
```ts
export function ConfirmDialog(p: {
  visible: boolean; title: string; children: ReactNode; confirmLabel: string; cancelLabel: string;
  onConfirm: () => void; onCancel: () => void; testID?: string;
}): JSX.Element; // RN Modal transparent, fade; scrim fon; oq karta radius 22; IconCircle danger 52 + LogoutIcon; Button dangerSolid + ghost; Android orqaga = onCancel
```
`config.d.ts` → `Env` ga `VERSION_NAME?: string`.

Xabarlar (`04` A): `HeroScreen` + `Hero minHeight={120}` `heroTitle` "Xabarlar"; varaqda markazda `EmptyState icon={BellIcon} tone="brand" size={96}` "Hozircha xabar yo'q" / "Texnolog yoki tizimdan kelgan eslatmalar shu yerda ko'rinadi."

Profil (`05`):
- `Hero minHeight={200} circles={2}`, markazda: 70 dp oq doira — `initials(user?.name)` (`heroTitleLg`, `brand`) yoki `UserIcon` (`brand`); `displayName` (`heroTitle`, `numberOfLines={2}`) — bo'lmasa yo'q; `Pill onHero` "Oshpaz".
- `Card` qatorlari (chapda 32 dp `fieldBg` kvadrat radius 10 ichida 16 dp ikonka `inkSoft`, keyin `caption` kalit `inkFaint` va `bodyStrong` qiymat): Email (user bo'lsa), Bog'cha (`useChildrenCountToday` nomi bo'lsa; xatosi ko'rsatilmaydi), "Yuborilmagan davomat" — `useFocusEffect` da `attendancePendingCount()`: 0 → "Navbat bo'sh" + 8 dp `ok` nuqta; n → `` `${n} ta navbatda` `` (`warnInk`) + `warn` nuqta.
- `Button variant="danger" icon={LogoutIcon}` "Hisobdan chiqish" → dialogni ochadi.
- `caption` `inkFaint` markazda `` `Global Ta'm · versiya ${Config.VERSION_NAME}` `` (yo'q bo'lsa yozuv yo'q).
- `ConfirmDialog` sarlavha "Chiqishni xohlaysizmi?"; tana: n>0 → **`${n} ta davomat hali yuborilmagan`** (qalin `warnInk`) + " — chiqsangiz, u yo'qoladi."; n=0 → "Qayta kirish uchun email va parol kerak bo'ladi."; "Ha, chiqaman" → mavjud ketma-ketlik (`stopFlusher`, `clearQueue`, `clearSession`); "Bekor qilish" → yopiladi. `Alert` importi olib tashlanadi.

- [ ] **Step 1: Failing testlar.** `ConfirmDialog`: `visible` bo'lmasa sarlavha yo'q; tasdiq/bekor tugmalari callback'larni chaqiradi. `ProfileScreen` (`@react-navigation/native` `useFocusEffect` → `React.useEffect` ga mock; authStore, attendanceFlusher, attendanceQueue, useChildrenCountToday mock):
```ts
// user {name:'Dilnoza Aliyeva', email:'d@g.uz'}, navbat 0
expect(getByText('DA')).toBeTruthy(); expect(getByText('Navbat bo\'sh')).toBeTruthy();
expect(getByText("Global Ta'm · versiya 1.2.0")).toBeTruthy();
fireEvent.press(getByText('Hisobdan chiqish'));
expect(getByText("Qayta kirish uchun email va parol kerak bo'ladi.")).toBeTruthy();
fireEvent.press(getByText('Ha, chiqaman'));
await waitFor(() => expect(clearSession).toHaveBeenCalled()); expect(stopFlusher).toHaveBeenCalled(); expect(clearQueue).toHaveBeenCalled();
// navbat 1 → '1 ta navbatda'; dialogda '1 ta davomat hali yuborilmagan'
// user = null → queryByText('Email') null, getByTestId('avatar-icon') bor, render qulamaydi
```
- [ ] **Step 2:** `npx jest __tests__/components/ConfirmDialog.test.tsx __tests__/screens/ProfileScreen.test.tsx` → FAIL.
- [ ] **Step 3:** Amalga oshiring.
- [ ] **Step 4:** → PASS; `npx jest`, `tsc` → yashil.
- [ ] **Step 5: Commit** — `feat(profile,notifications): B profile with queue status and custom logout dialog`

---

### Task 11: Tozalash, emoji qo'riqchisi, versiya, APK va qo'lda tekshiruv

**Files:**
- Delete: `mobile/src/components/{PageHeader,StatusLine,Divider,Section,Screen}.tsx` — agar `src/` da import qolmagan bo'lsa (grep bilan tekshiring); ularning testlari (`layout.test.tsx`, `feedback.test.tsx` dagi StatusLine/PageHeader qismlari).
- Modify: `mobile/src/theme/colors.ts`, `mobile/src/theme/typography.ts` (src'da ishlatilmaydigan token va variantlar o'chiriladi — alias yo'q), `mobile/__tests__/architecture.test.ts`, `mobile/__tests__/components/AppText.test.tsx` (o'chirilgan variantlarga bog'liq bo'lsa), `mobile/android/app/build.gradle`

- [ ] **Step 1: Emoji testini qo'shing** (`architecture.test.ts`, mavjud `walk` ni `.ts` ni ham oladigan qilib):
```ts
it('src ichida emoji yo\'q', () => {
  const offenders = walkAll(SRC_DIR).filter((f) => /\p{Extended_Pictographic}/u.test(fs.readFileSync(f, 'utf8')));
  expect(offenders.map((f) => path.relative(SRC_DIR, f))).toEqual([]);
});
```
- [ ] **Step 2:** `npx jest __tests__/architecture.test.ts` → PASS bo'lishi kerak (emojilar Task 5 va 9 da olib tashlangan). FAIL bo'lsa — ko'rsatilgan faylni tuzating.
- [ ] **Step 3:** Ishlatilmaydigan komponent, token va variantlarni o'chiring (har biri uchun `grep -rn "<nom>" src` → 0 natija). `build.gradle`: `versionCode 5`, `versionName "1.2.0"`.
- [ ] **Step 4:** `npx jest` va `npx tsc --noEmit` → yashil. `npx eslint src` → yangi xato yo'q.
- [ ] **Step 5: Commit** — `chore(mobile): drop C1 leftovers, guard against emoji, bump to 1.2.0`
- [ ] **Step 6: APK yig'ing** — `cd android && ENVFILE=.env.production ./gradlew assembleRelease` (keystore parollari `~/.gradle/gradle.properties` da bo'lishi kerak). Kutilgan: `BUILD SUCCESSFUL`, `android/app/build/outputs/apk/release/app-release.apk`.
- [ ] **Step 7: Qo'lda tekshiruv (loyiha egasi bilan, haqiqiy telefonda)** — spec 7-bo'lim ro'yxati: tungi rejimda Kirish va son kiritish; 360×640 da klaviatura ochiq holda "Yuborish" ga yetish va birinchi bosishda yuborish; aylantirilganda status bar belgilari o'qiladi; davomat vaqti server yozuvi bilan Toshkent vaqtida mos; ilovani to'liq yopib qayta ochganda ism bor (1.1.2 dan yangilangan holda — avval ismsiz, qayta login'dan keyin ism bilan); internetni o'chirib "Keldim" — navbat holati va oq kapsula. Natijalar loyiha egasiga hisobot qilinadi.
