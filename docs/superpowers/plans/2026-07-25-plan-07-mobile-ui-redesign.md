# Mobil ilova C1 UI qayta ishlash — Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Chef mobil ilovasini "Global Ta'm" brendiga mos C1 tipografik dizayn tizimiga o'tkazish va tungi rejimda login qilishni imkonsiz qilgan `TextInput` rang xatosini arxitektura darajasida yopish.

**Architecture:** Uch qatlam. Pastda `src/theme/` — ranglar, tipografika va oraliqlar tokenlari. O'rtada `src/components/` — tokenlardan boshqa hech narsani bilmaydigan primitiv komponentlar (`AppText`, `Field`, `Button`, `StatusLine`, `Banner`, `PageHeader`, `Screen`, `Section`, `Divider`, SVG ikonkalar). Yuqorida ekranlar — ular faqat kompozitsiya qiladi, o'z `StyleSheet` i va hex ranglari qolmaydi. Biznes mantiq (react-query, GPS, queue, auth) umuman ko'chirilmaydi — faqat chizish qatlami almashadi.

**Tech Stack:** React Native 0.74.5, TypeScript 5.0.4, `react-native-svg@15.3.0` (yangi), Jest 29 + `@testing-library/react-native@13.3.3`.

**Spec:** `docs/superpowers/specs/2026-07-25-mobile-ui-redesign-design.md`

**Branch:** `feature/mobile-ui-redesign`

## Global Constraints

Har bir vazifaning talablari quyidagilarni o'z ichiga oladi:

- Barcha ish `mobile/` papkasida. Buyruqlar `mobile/` ichidan ishga tushiriladi.
- **Boshlang'ich holat:** `npx jest` → 5 suite, 13 test o'tadi. `npx tsc --noEmit` → exit 0. Har vazifa oxirida ikkalasi ham shu holatda (yoki undan yaxshiroq) qolishi shart.
- **Vazifalardagi kutilgan test raqamlari yo'l-yo'riq, mezon emas.** Review paytida topilgan kamchiliklar tuzatilganda testlar qo'shiladi va keyingi vazifalarning raqamlari eskiradi. Majburiy shart ikkitasi: butun suite yashil (bironta ham yiqilgan test yo'q) va `npx tsc --noEmit` exit 0. Raqam mos kelmasa — o'sha vazifa boshlanishidagi haqiqiy holatni o'lchang va o'zingiz qo'shgan testlar sonicha o'sganini tekshiring.
- **Biznes mantiqqa tegilmaydi.** `react-query` chaqiruvlari, `onAction`, GPS/masofa hisobi, queue, `mapServerError`, auth — hech biri o'zgarmaydi. Faqat JSX va stillar almashadi.
- **Ekran fayllarida hex rang, `fontSize` yoki `fontWeight` yozilmaydi.** Hammasi tokenlar va `AppText` orqali.
- Yangi paket aniq versiya bilan qadaladi (`--save-exact`), `^` belgisisiz — loyihadagi mavjud amaliyot.
- Import uslubi: to'g'ridan-to'g'ri fayl yo'li bilan (`import { Button } from '../components/Button'`). `theme/index.ts` va `components/index.ts` barrel fayllari bo'sh (`export {}`) holicha qoldiriladi — mavjud kod shu uslubda yozilgan.
- Matn tili — o'zbekcha, mavjud ilovadagi so'zlar saqlanadi.
- Har vazifa oxirida commit qilinadi.

**Spec'dan bitta chetlanish:** spec'da `okBg`/`warnBg` fonlarida matn rangi sifatida `ok`/`warn` ishlatilishi nazarda tutilgan edi. Kontrast hisoblanganda `#D08700` ustida `#FDF3E0` fonida atigi 2.7:1 chiqdi (WCAG AA uchun 4.5:1 kerak), `#14884A` esa 4.0:1. Shu sababli banner matni uchun ikkita qo'shimcha token kiritiladi: `okInk: '#0E6B39'` (5.9:1) va `warnInk: '#7A5200'` (6.2:1). Nuqta va chegara ranglari o'zgarishsiz `ok`/`warn` bo'lib qoladi.

---

### Task 1: Token qatlami va mavjud kodni yangi nomlarga ko'chirish

Bu vazifa butun ilova bo'ylab rang nomlarini almashtiradi. Eski nomlar uchun alias qoldirilmaydi — TypeScript kompilyatori barcha ishlatilish joylarini ko'rsatib beradi va hammasi shu vazifada tuzatiladi. Vazifa oxirida daraxt yana kompilyatsiya bo'ladi.

**Files:**
- Create: `mobile/src/theme/typography.ts`
- Create: `mobile/src/theme/spacing.ts`
- Modify: `mobile/src/theme/colors.ts` (to'liq almashtiriladi)
- Modify: `mobile/src/screens/LoginScreen.tsx`, `mobile/src/screens/AttendanceScreen.tsx`, `mobile/src/screens/ProfileScreen.tsx`
- Modify: `mobile/src/components/ChildrenCountCard.tsx`, `mobile/src/components/ErrorBanner.tsx`, `mobile/src/components/ScreenContainer.tsx`, `mobile/src/components/PrimaryButton.tsx`, `mobile/src/components/OfflineIndicator.tsx`
- Modify: `mobile/src/navigation/MainTabs.tsx`

**Interfaces:**
- Consumes: —
- Produces: `colors` (18 ta rang tokeni), `typography: Record<TextVariant, TextStyle>`, `TextVariant` tipi, `spacing`, `radius`, `hit`, `screen` obyektlari.

- [ ] **Step 1: `colors.ts` ni to'liq almashtirish**

```ts
// mobile/src/theme/colors.ts
export const colors = {
  ink: '#0D0D0D',
  inkMuted: '#77797E',
  inkFaint: '#9A9CA2',

  brand: '#EF5B3C',
  brandPressed: '#C4401F',

  surface: '#FFFFFF',
  rule: '#E8E8EA',

  ok: '#14884A',
  warn: '#D08700',
  danger: '#C62828',

  okInk: '#0E6B39',
  warnInk: '#7A5200',

  okBg: '#E7F4EC',
  warnBg: '#FDF3E0',
  dangerBg: '#FDECEA',

  disabledBg: '#F1F1F3',
  disabledInk: '#A9ABAF',
  statusOff: '#C7C9CD',
} as const;
```

- [ ] **Step 2: `typography.ts` yaratish**

```ts
// mobile/src/theme/typography.ts
import { TextStyle } from 'react-native';

export type TextVariant =
  | 'h1'
  | 'statusBig'
  | 'body'
  | 'bodyStrong'
  | 'fieldLabel'
  | 'muted'
  | 'caption'
  | 'eyebrow'
  | 'button'
  | 'tab';

export const typography: Record<TextVariant, TextStyle> = {
  h1: { fontSize: 28, lineHeight: 30, fontWeight: '800', letterSpacing: -1.0 },
  statusBig: { fontSize: 22, lineHeight: 26, fontWeight: '800', letterSpacing: -0.7 },
  body: { fontSize: 15, lineHeight: 22, fontWeight: '400', letterSpacing: 0 },
  bodyStrong: { fontSize: 15, lineHeight: 22, fontWeight: '700', letterSpacing: 0 },
  fieldLabel: { fontSize: 13, lineHeight: 18, fontWeight: '700', letterSpacing: 0 },
  muted: { fontSize: 12, lineHeight: 17, fontWeight: '400', letterSpacing: 0 },
  caption: { fontSize: 12, lineHeight: 16, fontWeight: '400', letterSpacing: 0 },
  eyebrow: {
    fontSize: 11,
    lineHeight: 14,
    fontWeight: '800',
    letterSpacing: 1.5,
    textTransform: 'uppercase',
  },
  button: { fontSize: 15, lineHeight: 20, fontWeight: '800', letterSpacing: -0.2 },
  tab: { fontSize: 11, lineHeight: 13, fontWeight: '700', letterSpacing: 0 },
};
```

- [ ] **Step 3: `spacing.ts` yaratish**

```ts
// mobile/src/theme/spacing.ts
export const spacing = { xs: 4, sm: 8, md: 12, lg: 16, xl: 24, xxl: 32 } as const;
export const radius = { field: 11, button: 12, pill: 999 } as const;
export const hit = { button: 56, field: 52, tabBar: 60 } as const;
export const screen = { paddingH: 18, sectionPaddingV: 16 } as const;
```

- [ ] **Step 4: Kompilyatsiya xatolari ro'yxatini olish**

Run: `npx tsc --noEmit`
Expected: FAIL — `colors.primary`, `colors.success`, `colors.warning`, `colors.bgLight`, `colors.textPrimary`, `colors.textMuted`, `colors.border` ishlatilgan har bir satr uchun `Property ... does not exist` xatosi.

- [ ] **Step 5: Har bir xatoni quyidagi jadval bo'yicha almashtirish**

| Eski | Yangi |
|---|---|
| `colors.textPrimary` | `colors.ink` |
| `colors.textMuted` | `colors.inkMuted` |
| `colors.border` | `colors.rule` |
| `colors.success` | `colors.ok` |
| `colors.warning` | `colors.warn` |
| `colors.primary` | `colors.brand` |
| `colors.bgLight` | `colors.surface` |
| `colors.danger` | `colors.danger` (nomi o'zgarmaydi, qiymati `#dc2626` → `#C62828`) |

Qo'shimcha ravishda `OfflineIndicator.tsx` dagi ikkita hardcode rang almashtiriladi:

```ts
// mobile/src/components/OfflineIndicator.tsx — styles ichida
  offline: { backgroundColor: colors.danger },
  queueing: { backgroundColor: colors.warn },
```

va faylning yuqorisiga import qo'shiladi:

```ts
import { colors } from '../theme/colors';
```

Boshqa hech narsa o'zgarmaydi — bu fayllarning ko'pi keyingi vazifalarda qayta yoziladi yoki o'chiriladi, hozir maqsad daraxtni kompilyatsiya holatida saqlash.

- [ ] **Step 6: Kompilyatsiya va testlarni tekshirish**

Run: `npx tsc --noEmit && npx jest`
Expected: `tsc` exit 0, jest → 5 suite, 13 test o'tadi.

- [ ] **Step 7: Commit**

```bash
git add mobile/src/theme mobile/src/components mobile/src/screens mobile/src/navigation
git commit -m "feat(plan-7): brand token layer — colors, typography, spacing"
```

---

### Task 2: `react-native-svg` va tab ikonkalari

**Files:**
- Modify: `mobile/package.json`
- Modify: `mobile/jest.config.js`
- Create: `mobile/src/components/icons/types.ts`
- Create: `mobile/src/components/icons/HomeIcon.tsx`, `PinIcon.tsx`, `BellIcon.tsx`, `UserIcon.tsx`
- Test: `mobile/__tests__/components/icons.test.tsx`

**Interfaces:**
- Consumes: —
- Produces: `IconProps = { size?: number; color: string; testID?: string }`; `HomeIcon`, `PinIcon`, `BellIcon`, `UserIcon` — hammasi `(props: IconProps) => JSX.Element`, standart `size` 22.

- [ ] **Step 1: Paketni o'rnatish**

```bash
cd mobile && npm install react-native-svg@15.3.0 --save-exact
```

- [ ] **Step 2: Jest transform ro'yxatiga qo'shish**

```js
// mobile/jest.config.js
module.exports = {
  preset: 'react-native',
  transformIgnorePatterns: [
    'node_modules/(?!(?:@react-native|react-native|@react-navigation|react-native-mmkv|react-native-svg)/)',
  ],
};
```

`react-native-svg` ES modul sifatida tarqatiladi. U bu ro'yxatga qo'shilmasa, ikonka import qilgan har qanday test `SyntaxError: Cannot use import statement outside a module` bilan yiqiladi.

- [ ] **Step 3: Yiqiladigan testni yozish**

```tsx
// mobile/__tests__/components/icons.test.tsx
import React from 'react';
import { render } from '@testing-library/react-native';
import { HomeIcon } from '../../src/components/icons/HomeIcon';
import { PinIcon } from '../../src/components/icons/PinIcon';
import { BellIcon } from '../../src/components/icons/BellIcon';
import { UserIcon } from '../../src/components/icons/UserIcon';

describe('icons', () => {
  it('HomeIcon berilgan o\'lcham va rangni qo\'llaydi', () => {
    const { getByTestId } = render(<HomeIcon size={30} color="#EF5B3C" testID="i" />);
    const svg = getByTestId('i');
    expect(svg.props.width).toBe(30);
    expect(svg.props.height).toBe(30);
    expect(svg.props.stroke).toBe('#EF5B3C');
  });

  it('o\'lcham berilmasa 22 ni ishlatadi', () => {
    const { getByTestId } = render(<PinIcon color="#9A9CA2" testID="i" />);
    expect(getByTestId('i').props.width).toBe(22);
  });

  it('BellIcon va UserIcon rangni qo\'llaydi', () => {
    const bell = render(<BellIcon color="#0D0D0D" testID="b" />);
    expect(bell.getByTestId('b').props.stroke).toBe('#0D0D0D');
    const user = render(<UserIcon color="#0D0D0D" testID="u" />);
    expect(user.getByTestId('u').props.stroke).toBe('#0D0D0D');
  });
});
```

- [ ] **Step 4: Testni ishga tushirib yiqilishiga ishonch hosil qilish**

Run: `npx jest __tests__/components/icons.test.tsx`
Expected: FAIL — `Cannot find module '../../src/components/icons/HomeIcon'`.

- [ ] **Step 5: Ikonkalarni yozish**

```ts
// mobile/src/components/icons/types.ts
export type IconProps = {
  size?: number;
  color: string;
  testID?: string;
};
```

```tsx
// mobile/src/components/icons/HomeIcon.tsx
import React from 'react';
import Svg, { Path } from 'react-native-svg';
import { IconProps } from './types';

export function HomeIcon({ size = 22, color, testID }: IconProps) {
  return (
    <Svg
      testID={testID}
      width={size}
      height={size}
      viewBox="0 0 24 24"
      fill="none"
      stroke={color}
      strokeWidth={2.1}
      strokeLinecap="round"
      strokeLinejoin="round"
    >
      <Path d="M3 10.2 12 3.4l9 6.8V20a1 1 0 0 1-1 1h-5v-6H9v6H4a1 1 0 0 1-1-1z" />
    </Svg>
  );
}
```

```tsx
// mobile/src/components/icons/PinIcon.tsx
import React from 'react';
import Svg, { Path, Circle } from 'react-native-svg';
import { IconProps } from './types';

export function PinIcon({ size = 22, color, testID }: IconProps) {
  return (
    <Svg
      testID={testID}
      width={size}
      height={size}
      viewBox="0 0 24 24"
      fill="none"
      stroke={color}
      strokeWidth={2.1}
      strokeLinecap="round"
      strokeLinejoin="round"
    >
      <Path d="M20 10.5c0 6-8 12-8 12s-8-6-8-12a8 8 0 1 1 16 0Z" />
      <Circle cx={12} cy={10.3} r={2.8} />
    </Svg>
  );
}
```

```tsx
// mobile/src/components/icons/BellIcon.tsx
import React from 'react';
import Svg, { Path } from 'react-native-svg';
import { IconProps } from './types';

export function BellIcon({ size = 22, color, testID }: IconProps) {
  return (
    <Svg
      testID={testID}
      width={size}
      height={size}
      viewBox="0 0 24 24"
      fill="none"
      stroke={color}
      strokeWidth={2.1}
      strokeLinecap="round"
      strokeLinejoin="round"
    >
      <Path d="M18 8.7a6 6 0 1 0-12 0c0 6.4-2.5 8.3-2.5 8.3h17S18 15.1 18 8.7" />
      <Path d="M13.7 20.5a2 2 0 0 1-3.4 0" />
    </Svg>
  );
}
```

```tsx
// mobile/src/components/icons/UserIcon.tsx
import React from 'react';
import Svg, { Path, Circle } from 'react-native-svg';
import { IconProps } from './types';

export function UserIcon({ size = 22, color, testID }: IconProps) {
  return (
    <Svg
      testID={testID}
      width={size}
      height={size}
      viewBox="0 0 24 24"
      fill="none"
      stroke={color}
      strokeWidth={2.1}
      strokeLinecap="round"
      strokeLinejoin="round"
    >
      <Circle cx={12} cy={8} r={3.7} />
      <Path d="M4.5 20.5a7.5 7.5 0 0 1 15 0" />
    </Svg>
  );
}
```

- [ ] **Step 6: Testlarni ishga tushirish**

Run: `npx jest && npx tsc --noEmit`
Expected: 6 suite, 16 test o'tadi; `tsc` exit 0.

- [ ] **Step 7: Commit**

```bash
git add mobile/package.json mobile/package-lock.json mobile/jest.config.js mobile/src/components/icons mobile/__tests__/components/icons.test.tsx
git commit -m "feat(plan-7): react-native-svg + 4 tab icons"
```

---

### Task 3: `AppText`

**Files:**
- Create: `mobile/src/components/AppText.tsx`
- Test: `mobile/__tests__/components/AppText.test.tsx`

**Interfaces:**
- Consumes: `typography`, `TextVariant` (Task 1), `colors` (Task 1)
- Produces: `AppText` — proplar: barcha `TextProps` + `variant?: TextVariant` (standart `'body'`) + `color?: string` (standart `colors.ink`). Keyingi barcha vazifalar matnni faqat shu komponent orqali chizadi.

- [ ] **Step 1: Yiqiladigan testni yozish**

```tsx
// mobile/__tests__/components/AppText.test.tsx
import React from 'react';
import { StyleSheet } from 'react-native';
import { render } from '@testing-library/react-native';
import { AppText } from '../../src/components/AppText';

const flat = (node: any) => StyleSheet.flatten(node.props.style);

describe('AppText', () => {
  it('h1 varianti tipografika shkalasini qo\'llaydi', () => {
    const { getByText } = render(<AppText variant="h1">Davomat</AppText>);
    const s = flat(getByText('Davomat'));
    expect(s.fontSize).toBe(28);
    expect(s.fontWeight).toBe('800');
    expect(s.letterSpacing).toBe(-1.0);
  });

  it('variant berilmasa body ishlatiladi', () => {
    const { getByText } = render(<AppText>Salom</AppText>);
    expect(flat(getByText('Salom')).fontSize).toBe(15);
  });

  it('standart rang ink', () => {
    const { getByText } = render(<AppText>Salom</AppText>);
    expect(flat(getByText('Salom')).color).toBe('#0D0D0D');
  });

  it('color propi standart rangni bekor qiladi', () => {
    const { getByText } = render(<AppText color="#EF5B3C">Kirish</AppText>);
    expect(flat(getByText('Kirish')).color).toBe('#EF5B3C');
  });

  it('eyebrow varianti katta harfga o\'tkazadi', () => {
    const { getByText } = render(<AppText variant="eyebrow">bog'cha</AppText>);
    expect(flat(getByText('bog\'cha')).textTransform).toBe('uppercase');
  });

  it('noma\'lum variant kelsa body ga qaytadi', () => {
    const { getByText } = render(<AppText variant={'yoq' as any}>Salom</AppText>);
    expect(flat(getByText('Salom')).fontSize).toBe(15);
  });
});
```

- [ ] **Step 2: Testni ishga tushirib yiqilishiga ishonch hosil qilish**

Run: `npx jest __tests__/components/AppText.test.tsx`
Expected: FAIL — `Cannot find module '../../src/components/AppText'`.

- [ ] **Step 3: Komponentni yozish**

```tsx
// mobile/src/components/AppText.tsx
import React from 'react';
import { Text, TextProps, StyleProp, TextStyle } from 'react-native';
import { typography, TextVariant } from '../theme/typography';
import { colors } from '../theme/colors';

type Props = TextProps & {
  variant?: TextVariant;
  color?: string;
  style?: StyleProp<TextStyle>;
};

export function AppText({ variant = 'body', color = colors.ink, style, ...rest }: Props) {
  const base = typography[variant] ?? typography.body;
  return <Text {...rest} style={[base, { color }, style]} />;
}
```

- [ ] **Step 4: Testlarni ishga tushirish**

Run: `npx jest && npx tsc --noEmit`
Expected: 7 suite, 24 test o'tadi; `tsc` exit 0.

- [ ] **Step 5: Commit**

```bash
git add mobile/src/components/AppText.tsx mobile/__tests__/components/AppText.test.tsx
git commit -m "feat(plan-7): AppText — single source of typography"
```

---

### Task 4: `Screen`, `Section`, `Divider`

`Screen` uchun render testi yozilmaydi: u `SafeAreaView` ustidagi juda yupqa o'ram, o'z mantig'i yo'q, va uni test qilish uchun `react-native-safe-area-context` ni mock qilish kerak bo'ladi — bu foydasiz murakkablik. `Section` va `Divider` sof `View` bo'lgani uchun to'g'ridan-to'g'ri tekshiriladi.

**Files:**
- Create: `mobile/src/components/Screen.tsx`, `mobile/src/components/Section.tsx`, `mobile/src/components/Divider.tsx`
- Test: `mobile/__tests__/components/layout.test.tsx`

**Interfaces:**
- Consumes: `colors`, `screen` (Task 1)
- Produces:
  - `Screen({ children }: { children: ReactNode })` — oq fon, safe-area (`top`/`left`/`right`), `KeyboardAvoidingView`. **Padding yo'q.**
  - `Section({ children, style? })` — `paddingHorizontal: 18`, `paddingVertical: 16`
  - `Divider()` — 1px `colors.rule`

- [ ] **Step 1: Yiqiladigan testni yozish**

```tsx
// mobile/__tests__/components/layout.test.tsx
import React from 'react';
import { StyleSheet, Text } from 'react-native';
import { render } from '@testing-library/react-native';
import { Section } from '../../src/components/Section';
import { Divider } from '../../src/components/Divider';

const flat = (node: any) => StyleSheet.flatten(node.props.style);

describe('Section', () => {
  it('ekran oraliqlarini qo\'llaydi', () => {
    const { getByTestId } = render(
      <Section testID="s">
        <Text>ichida</Text>
      </Section>,
    );
    const s = flat(getByTestId('s'));
    expect(s.paddingHorizontal).toBe(18);
    expect(s.paddingVertical).toBe(16);
  });

  it('bolalarini chizadi', () => {
    const { getByText } = render(
      <Section>
        <Text>ichida</Text>
      </Section>,
    );
    expect(getByText('ichida')).toBeTruthy();
  });

  it('style propi oraliqlarni bekor qila oladi', () => {
    const { getByTestId } = render(
      <Section testID="s" style={{ paddingVertical: 0 }}>
        <Text>ichida</Text>
      </Section>,
    );
    expect(flat(getByTestId('s')).paddingVertical).toBe(0);
  });
});

describe('Divider', () => {
  it('1px rule rangidagi chiziq', () => {
    const { getByTestId } = render(<Divider testID="d" />);
    const s = flat(getByTestId('d'));
    expect(s.height).toBe(1);
    expect(s.backgroundColor).toBe('#E8E8EA');
  });
});
```

- [ ] **Step 2: Testni ishga tushirib yiqilishiga ishonch hosil qilish**

Run: `npx jest __tests__/components/layout.test.tsx`
Expected: FAIL — `Cannot find module '../../src/components/Section'`.

- [ ] **Step 3: Uch komponentni yozish**

```tsx
// mobile/src/components/Screen.tsx
import React, { ReactNode } from 'react';
import { StyleSheet, KeyboardAvoidingView, Platform } from 'react-native';
import { SafeAreaView } from 'react-native-safe-area-context';
import { colors } from '../theme/colors';

export function Screen({ children }: { children: ReactNode }) {
  return (
    <SafeAreaView style={styles.safe} edges={['top', 'left', 'right']}>
      <KeyboardAvoidingView
        style={styles.flex}
        behavior={Platform.OS === 'ios' ? 'padding' : undefined}
      >
        {children}
      </KeyboardAvoidingView>
    </SafeAreaView>
  );
}

const styles = StyleSheet.create({
  safe: { flex: 1, backgroundColor: colors.surface },
  flex: { flex: 1 },
});
```

```tsx
// mobile/src/components/Section.tsx
import React, { ReactNode } from 'react';
import { View, StyleSheet, StyleProp, ViewStyle } from 'react-native';
import { screen } from '../theme/spacing';

export function Section({
  children,
  style,
  testID,
}: {
  children: ReactNode;
  style?: StyleProp<ViewStyle>;
  testID?: string;
}) {
  return (
    <View testID={testID} style={[styles.section, style]}>
      {children}
    </View>
  );
}

const styles = StyleSheet.create({
  section: {
    paddingHorizontal: screen.paddingH,
    paddingVertical: screen.sectionPaddingV,
  },
});
```

```tsx
// mobile/src/components/Divider.tsx
import React from 'react';
import { View, StyleSheet } from 'react-native';
import { colors } from '../theme/colors';

export function Divider({ testID }: { testID?: string }) {
  return <View testID={testID} style={styles.rule} />;
}

const styles = StyleSheet.create({
  rule: { height: 1, backgroundColor: colors.rule },
});
```

- [ ] **Step 4: Testlarni ishga tushirish**

Run: `npx jest && npx tsc --noEmit`
Expected: 8 suite, 31 test o'tadi; `tsc` exit 0.

- [ ] **Step 5: Commit**

```bash
git add mobile/src/components/Screen.tsx mobile/src/components/Section.tsx mobile/src/components/Divider.tsx mobile/__tests__/components/layout.test.tsx
git commit -m "feat(plan-7): Screen, Section, Divider layout primitives"
```

---

### Task 5: `Button`

**Files:**
- Create: `mobile/src/components/Button.tsx`
- Test: `mobile/__tests__/components/Button.test.tsx`

**Interfaces:**
- Consumes: `colors`, `radius`, `hit`, `spacing` (Task 1), `AppText` (Task 3)
- Produces: `Button` — proplar `{ label: string; onPress: () => void; variant?: 'primary' | 'outline' | 'danger'; loading?: boolean; loadingLabel?: string | null; disabled?: boolean; style?: StyleProp<ViewStyle>; testID?: string }`. Standart `variant` — `'primary'`.

Rang qoidasi: `disabled` bo'lsa fon `disabledBg`, matn `disabledInk`, chegara yo'q. `loading` faqat bosishni bloklaydi va spinner ko'rsatadi, ranglarni o'zgartirmaydi.

`loadingLabel` — `loading` holatida spinner yonida ko'rsatiladigan matn. Davomat ekranidagi bosqichma-bosqich progress (`2/6 GPS koordinatasi olinmoqda...`) shu prop orqali tugmaning o'zida ko'rinadi. Usiz `loading` yorliqni butunlay yashiradi va oshpaz nima bo'layotganini bilmay qoladi.

- [ ] **Step 1: Yiqiladigan testni yozish**

```tsx
// mobile/__tests__/components/Button.test.tsx
import React from 'react';
import { StyleSheet } from 'react-native';
import { render, fireEvent } from '@testing-library/react-native';
import { Button } from '../../src/components/Button';

const flat = (node: any) => StyleSheet.flatten(node.props.style);

describe('Button', () => {
  it('primary — marjon fon, oq matn', () => {
    const { getByTestId, getByText } = render(
      <Button label="Kirish" onPress={() => {}} testID="b" />,
    );
    expect(flat(getByTestId('b')).backgroundColor).toBe('#EF5B3C');
    expect(flat(getByText('Kirish')).color).toBe('#FFFFFF');
  });

  it('outline — oq fon, 2px qora chegara, qora matn', () => {
    const { getByTestId, getByText } = render(
      <Button label="Qayta" onPress={() => {}} variant="outline" testID="b" />,
    );
    const s = flat(getByTestId('b'));
    expect(s.backgroundColor).toBe('#FFFFFF');
    expect(s.borderWidth).toBe(2);
    expect(s.borderColor).toBe('#0D0D0D');
    expect(flat(getByText('Qayta')).color).toBe('#0D0D0D');
  });

  it('danger — danger fon', () => {
    const { getByTestId } = render(
      <Button label="Chiqish" onPress={() => {}} variant="danger" testID="b" />,
    );
    expect(flat(getByTestId('b')).backgroundColor).toBe('#C62828');
  });

  it('bosish maydoni kamida 56px', () => {
    const { getByTestId } = render(<Button label="X" onPress={() => {}} testID="b" />);
    expect(flat(getByTestId('b')).minHeight).toBe(56);
  });

  it('disabled — kulrang fon va onPress chaqirilmaydi', () => {
    const onPress = jest.fn();
    const { getByTestId } = render(
      <Button label="X" onPress={onPress} disabled testID="b" />,
    );
    expect(flat(getByTestId('b')).backgroundColor).toBe('#F1F1F3');
    fireEvent.press(getByTestId('b'));
    expect(onPress).not.toHaveBeenCalled();
  });

  it('loading — matn o\'rniga spinner, onPress chaqirilmaydi', () => {
    const onPress = jest.fn();
    const { getByTestId, queryByText } = render(
      <Button label="Kirish" onPress={onPress} loading testID="b" />,
    );
    expect(queryByText('Kirish')).toBeNull();
    fireEvent.press(getByTestId('b'));
    expect(onPress).not.toHaveBeenCalled();
  });

  it('loadingLabel berilsa spinner yonida ko\'rsatiladi', () => {
    const { queryByText, getByText } = render(
      <Button
        label="Keldim"
        onPress={() => {}}
        loading
        loadingLabel="2/6 GPS koordinatasi olinmoqda..."
        testID="b"
      />,
    );
    expect(queryByText('Keldim')).toBeNull();
    expect(getByText('2/6 GPS koordinatasi olinmoqda...')).toBeTruthy();
  });

  it('oddiy holatda onPress chaqiriladi', () => {
    const onPress = jest.fn();
    const { getByTestId } = render(<Button label="X" onPress={onPress} testID="b" />);
    fireEvent.press(getByTestId('b'));
    expect(onPress).toHaveBeenCalledTimes(1);
  });
});
```

- [ ] **Step 2: Testni ishga tushirib yiqilishiga ishonch hosil qilish**

Run: `npx jest __tests__/components/Button.test.tsx`
Expected: FAIL — `Cannot find module '../../src/components/Button'`.

- [ ] **Step 3: Komponentni yozish**

```tsx
// mobile/src/components/Button.tsx
import React from 'react';
import {
  View,
  Pressable,
  ActivityIndicator,
  StyleSheet,
  StyleProp,
  ViewStyle,
} from 'react-native';
import { AppText } from './AppText';
import { colors } from '../theme/colors';
import { radius, hit, spacing } from '../theme/spacing';

export type ButtonVariant = 'primary' | 'outline' | 'danger';

type Tone = { bg: string; fg: string; borderWidth: number; borderColor: string };

const TONES: Record<ButtonVariant, Tone> = {
  primary: { bg: colors.brand, fg: colors.surface, borderWidth: 0, borderColor: 'transparent' },
  danger: { bg: colors.danger, fg: colors.surface, borderWidth: 0, borderColor: 'transparent' },
  outline: { bg: colors.surface, fg: colors.ink, borderWidth: 2, borderColor: colors.ink },
};

const DISABLED: Tone = {
  bg: colors.disabledBg,
  fg: colors.disabledInk,
  borderWidth: 0,
  borderColor: 'transparent',
};

export function Button({
  label,
  onPress,
  variant = 'primary',
  loading = false,
  loadingLabel,
  disabled = false,
  style,
  testID,
}: {
  label: string;
  onPress: () => void;
  variant?: ButtonVariant;
  loading?: boolean;
  loadingLabel?: string | null;
  disabled?: boolean;
  style?: StyleProp<ViewStyle>;
  testID?: string;
}) {
  const tone = disabled ? DISABLED : TONES[variant];
  return (
    <Pressable
      testID={testID}
      onPress={onPress}
      disabled={disabled || loading}
      style={[
        styles.btn,
        {
          backgroundColor: tone.bg,
          borderWidth: tone.borderWidth,
          borderColor: tone.borderColor,
        },
        style,
      ]}
    >
      {loading ? (
        <View style={styles.loadingRow}>
          <ActivityIndicator color={tone.fg} />
          {!!loadingLabel && (
            <AppText variant="button" color={tone.fg} style={styles.loadingLabel}>
              {loadingLabel}
            </AppText>
          )}
        </View>
      ) : (
        <AppText variant="button" color={tone.fg}>
          {label}
        </AppText>
      )}
    </Pressable>
  );
}

const styles = StyleSheet.create({
  btn: {
    minHeight: hit.button,
    borderRadius: radius.button,
    paddingHorizontal: spacing.lg,
    alignItems: 'center',
    justifyContent: 'center',
  },
  loadingRow: { flexDirection: 'row', alignItems: 'center' },
  loadingLabel: { marginLeft: spacing.sm, flexShrink: 1 },
});
```

- [ ] **Step 4: Testlarni ishga tushirish**

Run: `npx jest && npx tsc --noEmit`
Expected: 9 suite, 39 test o'tadi; `tsc` exit 0.

- [ ] **Step 5: Commit**

```bash
git add mobile/src/components/Button.tsx mobile/__tests__/components/Button.test.tsx
git commit -m "feat(plan-7): Button with primary/outline/danger variants"
```

---

### Task 6: `Field` — login xatosini yopish

Bu rejaning eng muhim vazifasi. Testlar aynan shu xatoning qaytishini bloklaydi: tungi rejimdagi Android'da `TextInput` ga `color` berilmasa, tizim mavzusidan oq rangni meros oladi va oq fonda ko'rinmay qoladi.

**Files:**
- Create: `mobile/src/components/Field.tsx`
- Test: `mobile/__tests__/components/Field.test.tsx`

**Interfaces:**
- Consumes: `colors`, `radius`, `hit`, `spacing` (Task 1), `AppText` (Task 3)
- Produces: `Field` — proplar: barcha `TextInputProps` + `label: string` + `testID?: string`. Bundan keyin ilovada yalang'och `TextInput` ishlatilmaydi.

- [ ] **Step 1: Yiqiladigan testni yozish**

```tsx
// mobile/__tests__/components/Field.test.tsx
import React from 'react';
import { StyleSheet } from 'react-native';
import { render, fireEvent } from '@testing-library/react-native';
import { Field } from '../../src/components/Field';

const flat = (node: any) => StyleSheet.flatten(node.props.style);

describe('Field', () => {
  // Regressiya: tungi rejimdagi Android'da color berilmasa oq fonda oq matn chiqadi.
  it('matn rangini aniq beradi (oq fonda oq matn bo\'lmaydi)', () => {
    const { getByTestId } = render(<Field label="Email" testID="f" />);
    expect(flat(getByTestId('f')).color).toBe('#0D0D0D');
  });

  it('placeholder rangini aniq beradi', () => {
    const { getByTestId } = render(<Field label="Email" testID="f" />);
    expect(getByTestId('f').props.placeholderTextColor).toBe('#9A9CA2');
  });

  it('fon oq — matn rangi bilan bir xil emasligi kafolatlanadi', () => {
    const { getByTestId } = render(<Field label="Email" testID="f" />);
    const s = flat(getByTestId('f'));
    expect(s.backgroundColor).toBe('#FFFFFF');
    expect(s.color).not.toBe(s.backgroundColor);
  });

  it('label ni chizadi', () => {
    const { getByText } = render(<Field label="Parol" />);
    expect(getByText('Parol')).toBeTruthy();
  });

  it('TextInput proplarini o\'tkazadi', () => {
    const onChangeText = jest.fn();
    const { getByTestId } = render(
      <Field
        label="Email"
        testID="f"
        keyboardType="email-address"
        secureTextEntry
        value="a@b.uz"
        onChangeText={onChangeText}
      />,
    );
    const input = getByTestId('f');
    expect(input.props.keyboardType).toBe('email-address');
    expect(input.props.secureTextEntry).toBe(true);
    expect(input.props.value).toBe('a@b.uz');
    fireEvent.changeText(input, 'x@y.uz');
    expect(onChangeText).toHaveBeenCalledWith('x@y.uz');
  });

  it('bosish maydoni kamida 52px', () => {
    const { getByTestId } = render(<Field label="Email" testID="f" />);
    expect(flat(getByTestId('f')).minHeight).toBe(52);
  });
});
```

- [ ] **Step 2: Testni ishga tushirib yiqilishiga ishonch hosil qilish**

Run: `npx jest __tests__/components/Field.test.tsx`
Expected: FAIL — `Cannot find module '../../src/components/Field'`.

- [ ] **Step 3: Komponentni yozish**

```tsx
// mobile/src/components/Field.tsx
import React from 'react';
import { View, TextInput, TextInputProps, StyleSheet } from 'react-native';
import { AppText } from './AppText';
import { colors } from '../theme/colors';
import { typography } from '../theme/typography';
import { radius, hit, spacing } from '../theme/spacing';

type Props = TextInputProps & {
  label: string;
  testID?: string;
};

export function Field({ label, style, testID, ...rest }: Props) {
  return (
    <View style={styles.wrap}>
      <AppText variant="fieldLabel">{label}</AppText>
      <TextInput
        {...rest}
        testID={testID}
        style={[styles.input, style]}
        placeholderTextColor={colors.inkFaint}
      />
    </View>
  );
}

const styles = StyleSheet.create({
  wrap: { marginBottom: spacing.md },
  input: {
    minHeight: hit.field,
    marginTop: 6,
    borderWidth: 1.5,
    borderColor: colors.rule,
    borderRadius: radius.field,
    paddingHorizontal: 13,
    paddingVertical: 12,
    fontSize: typography.bodyStrong.fontSize,
    fontWeight: typography.bodyStrong.fontWeight,
    color: colors.ink,
    backgroundColor: colors.surface,
  },
});
```

`placeholderTextColor` `{...rest}` dan **keyin** yoziladi — shunda chaqiruvchi uni tasodifan tushirib qoldira olmaydi.

Shrift o'lchami `typography.bodyStrong` dan olinadi, qo'lda yozilmaydi — shkala yagona manba bo'lib qoladi. `lineHeight` ataylab ko'chirilmaydi: Android'da `TextInput` ga `lineHeight` berilsa matn vertikal markazdan siljib, pastdan qirqilishi mumkin.

- [ ] **Step 4: Testlarni ishga tushirish**

Run: `npx jest && npx tsc --noEmit`
Expected: 10 suite, 45 test o'tadi; `tsc` exit 0.

- [ ] **Step 5: Commit**

```bash
git add mobile/src/components/Field.tsx mobile/__tests__/components/Field.test.tsx
git commit -m "fix(plan-7): Field always sets text and placeholder colour

Android night mode makes a TextInput without an explicit colour inherit
white text from the system theme, which rendered login inputs invisible
on their white background. Routing every input through Field closes this
for good; the tests assert colour is set and differs from the background."
```

---

### Task 7: `Banner`, `StatusLine`, `PageHeader`

**Files:**
- Create: `mobile/src/components/Banner.tsx`, `mobile/src/components/StatusLine.tsx`, `mobile/src/components/PageHeader.tsx`
- Test: `mobile/__tests__/components/feedback.test.tsx`

**Interfaces:**
- Consumes: `colors`, `spacing`, `screen` (Task 1), `AppText` (Task 3)
- Produces:
  - `Banner({ tone?, message, testID? })` — `tone`: `'error' | 'success' | 'info'` (standart `'error'`). `message` bo'sh yoki `null` bo'lsa `null` qaytaradi.
  - `StatusLine({ tone, title, accent?, detail?, testID? })` — `tone`: `'ok' | 'warn' | 'off'`. Rangli nuqta + `statusBig` sarlavha + ixtiyoriy rangli `accent` bo'lagi + `muted` izoh.
  - `PageHeader({ eyebrow?, title, meta? })`

- [ ] **Step 1: Yiqiladigan testni yozish**

```tsx
// mobile/__tests__/components/feedback.test.tsx
import React from 'react';
import { StyleSheet } from 'react-native';
import { render } from '@testing-library/react-native';
import { Banner } from '../../src/components/Banner';
import { StatusLine } from '../../src/components/StatusLine';
import { PageHeader } from '../../src/components/PageHeader';

const flat = (node: any) => StyleSheet.flatten(node.props.style);

describe('Banner', () => {
  it('xabar bo\'lmasa hech narsa chizmaydi', () => {
    const { toJSON } = render(<Banner message={null} />);
    expect(toJSON()).toBeNull();
  });

  it('error toni — qizil fon va chap chegara', () => {
    const { getByTestId } = render(<Banner message="Xato" testID="b" />);
    const s = flat(getByTestId('b'));
    expect(s.backgroundColor).toBe('#FDECEA');
    expect(s.borderLeftColor).toBe('#C62828');
  });

  it('success toni — yashil fon va quyuq yashil matn', () => {
    const { getByTestId, getByText } = render(
      <Banner tone="success" message="Qabul qilindi" testID="b" />,
    );
    expect(flat(getByTestId('b')).backgroundColor).toBe('#E7F4EC');
    expect(flat(getByText('Qabul qilindi')).color).toBe('#0E6B39');
  });

  it('info toni — sariq fon va quyuq sariq matn', () => {
    const { getByTestId, getByText } = render(
      <Banner tone="info" message="Kuting" testID="b" />,
    );
    expect(flat(getByTestId('b')).backgroundColor).toBe('#FDF3E0');
    expect(flat(getByText('Kuting')).color).toBe('#7A5200');
  });
});

describe('StatusLine', () => {
  it('ok toni — yashil nuqta', () => {
    const { getByTestId } = render(
      <StatusLine tone="ok" title="Keldingiz" testID="s" />,
    );
    expect(flat(getByTestId('s-dot')).backgroundColor).toBe('#14884A');
  });

  it('warn toni — sariq nuqta', () => {
    const { getByTestId } = render(
      <StatusLine tone="warn" title="Hali kelmadingiz" testID="s" />,
    );
    expect(flat(getByTestId('s-dot')).backgroundColor).toBe('#D08700');
  });

  it('off toni — neytral nuqta', () => {
    const { getByTestId } = render(<StatusLine tone="off" title="Yopiq" testID="s" />);
    expect(flat(getByTestId('s-dot')).backgroundColor).toBe('#C7C9CD');
  });

  it('sarlavha qora, accent ton rangida', () => {
    const { getByText } = render(
      <StatusLine tone="ok" title="Keldingiz — " accent="08:14" testID="s" />,
    );
    expect(flat(getByText('Keldingiz — ')).color).toBe('#0D0D0D');
    expect(flat(getByText('08:14')).color).toBe('#0E6B39');
  });

  it('izohni chizadi', () => {
    const { getByText } = render(
      <StatusLine tone="ok" title="Keldingiz" detail="45 m" testID="s" />,
    );
    expect(getByText('45 m')).toBeTruthy();
  });
});

describe('PageHeader', () => {
  it('eyebrow marjon rangda va katta harfda', () => {
    const { getByText } = render(<PageHeader eyebrow="42-son bog'cha" title="Davomat" />);
    const s = flat(getByText('42-son bog\'cha'));
    expect(s.color).toBe('#EF5B3C');
    expect(s.textTransform).toBe('uppercase');
  });

  it('sarlavha h1 shkalasida', () => {
    const { getByText } = render(<PageHeader title="Davomat" />);
    expect(flat(getByText('Davomat')).fontSize).toBe(28);
  });

  it('eyebrow va meta ixtiyoriy', () => {
    const { queryByText, getByText } = render(<PageHeader title="Kirish" />);
    expect(getByText('Kirish')).toBeTruthy();
    expect(queryByText('42-son bog\'cha')).toBeNull();
  });

  it('meta berilsa chiziladi', () => {
    const { getByText } = render(<PageHeader title="Davomat" meta="25-iyul" />);
    expect(getByText('25-iyul')).toBeTruthy();
  });
});
```

- [ ] **Step 2: Testni ishga tushirib yiqilishiga ishonch hosil qilish**

Run: `npx jest __tests__/components/feedback.test.tsx`
Expected: FAIL — `Cannot find module '../../src/components/Banner'`.

- [ ] **Step 3: Uch komponentni yozish**

```tsx
// mobile/src/components/Banner.tsx
import React from 'react';
import { View, StyleSheet } from 'react-native';
import { AppText } from './AppText';
import { colors } from '../theme/colors';
import { spacing, radius } from '../theme/spacing';

export type BannerTone = 'error' | 'success' | 'info';

const TONES: Record<BannerTone, { bg: string; bar: string; fg: string }> = {
  error: { bg: colors.dangerBg, bar: colors.danger, fg: colors.danger },
  success: { bg: colors.okBg, bar: colors.ok, fg: colors.okInk },
  info: { bg: colors.warnBg, bar: colors.warn, fg: colors.warnInk },
};

export function Banner({
  tone = 'error',
  message,
  testID,
}: {
  tone?: BannerTone;
  message?: string | null;
  testID?: string;
}) {
  if (!message) return null;
  const t = TONES[tone];
  return (
    <View
      testID={testID}
      style={[styles.box, { backgroundColor: t.bg, borderLeftColor: t.bar }]}
    >
      <AppText variant="body" color={t.fg}>
        {message}
      </AppText>
    </View>
  );
}

const styles = StyleSheet.create({
  box: {
    borderLeftWidth: 4,
    borderRadius: radius.field,
    padding: spacing.md,
    marginBottom: spacing.md,
  },
});
```

```tsx
// mobile/src/components/StatusLine.tsx
import React from 'react';
import { View, StyleSheet } from 'react-native';
import { AppText } from './AppText';
import { colors } from '../theme/colors';
import { spacing } from '../theme/spacing';

export type StatusTone = 'ok' | 'warn' | 'off';

const TONES: Record<StatusTone, { dot: string; accent: string }> = {
  ok: { dot: colors.ok, accent: colors.okInk },
  warn: { dot: colors.warn, accent: colors.warnInk },
  off: { dot: colors.statusOff, accent: colors.inkMuted },
};

export function StatusLine({
  tone,
  title,
  accent,
  detail,
  testID,
}: {
  tone: StatusTone;
  title: string;
  accent?: string;
  detail?: string | null;
  testID?: string;
}) {
  const t = TONES[tone];
  return (
    <View testID={testID}>
      <View style={styles.row}>
        <View
          testID={testID ? `${testID}-dot` : undefined}
          style={[styles.dot, { backgroundColor: t.dot }]}
        />
        <View style={styles.titleRow}>
          <AppText variant="statusBig">{title}</AppText>
          {!!accent && (
            <AppText variant="statusBig" color={t.accent}>
              {accent}
            </AppText>
          )}
        </View>
      </View>
      {!!detail && (
        <AppText variant="muted" color={colors.inkMuted} style={styles.detail}>
          {detail}
        </AppText>
      )}
    </View>
  );
}

const styles = StyleSheet.create({
  row: { flexDirection: 'row', alignItems: 'center' },
  titleRow: { flex: 1, flexDirection: 'row', flexWrap: 'wrap', alignItems: 'center' },
  dot: { width: 9, height: 9, borderRadius: 999, marginRight: spacing.sm },
  detail: { marginTop: 5 },
});
```

`title` va `accent` yonma-yon ikkita alohida `AppText` sifatida chiziladi, biri ikkinchisining ichiga joylashtirilmaydi. Joylashtirilgan `Text` da `@testing-library/react-native` birlashgan matnni ko'radi va `getByText('Keldingiz — ')` topilmaydi — shuning uchun qatorli joylashuv ishlatiladi.
```

```tsx
// mobile/src/components/PageHeader.tsx
import React from 'react';
import { View, StyleSheet } from 'react-native';
import { AppText } from './AppText';
import { colors } from '../theme/colors';
import { spacing, screen } from '../theme/spacing';

export function PageHeader({
  eyebrow,
  title,
  meta,
}: {
  eyebrow?: string;
  title: string;
  meta?: string | null;
}) {
  return (
    <View style={styles.wrap}>
      {!!eyebrow && (
        <AppText variant="eyebrow" color={colors.brand}>
          {eyebrow}
        </AppText>
      )}
      <AppText variant="h1" style={styles.title}>
        {title}
      </AppText>
      {!!meta && (
        <AppText variant="muted" color={colors.inkMuted} style={styles.meta}>
          {meta}
        </AppText>
      )}
    </View>
  );
}

const styles = StyleSheet.create({
  wrap: {
    paddingHorizontal: screen.paddingH,
    paddingTop: spacing.lg,
    paddingBottom: spacing.lg,
  },
  title: { marginTop: 5 },
  meta: { marginTop: 6 },
});
```

- [ ] **Step 4: Testlarni ishga tushirish**

Run: `npx jest && npx tsc --noEmit`
Expected: 11 suite, 58 test o'tadi; `tsc` exit 0.

- [ ] **Step 5: Commit**

```bash
git add mobile/src/components/Banner.tsx mobile/src/components/StatusLine.tsx mobile/src/components/PageHeader.tsx mobile/__tests__/components/feedback.test.tsx
git commit -m "feat(plan-7): Banner, StatusLine and PageHeader"
```

---

### Task 8: `LoginScreen` ni yangi tizimga o'tkazish

**Files:**
- Modify: `mobile/src/screens/LoginScreen.tsx` (to'liq almashtiriladi)

**Interfaces:**
- Consumes: `Screen`, `Section` (Task 4), `Field` (Task 6), `Button` (Task 5), `Banner`, `PageHeader` (Task 7)
- Produces: —

Auth mantig'i (`onSubmit`, `login`, `setSession`, `mapServerError`) so'zma-so'z saqlanadi.

- [ ] **Step 1: Ekranni qayta yozish**

```tsx
// mobile/src/screens/LoginScreen.tsx
import React, { useState } from 'react';
import { View, Image, ScrollView, StyleSheet } from 'react-native';
import { Screen } from '../components/Screen';
import { Section } from '../components/Section';
import { PageHeader } from '../components/PageHeader';
import { Field } from '../components/Field';
import { Button } from '../components/Button';
import { Banner } from '../components/Banner';
import { useAuthStore } from '../auth/authStore';
import { login } from '../auth/loginApi';
import { mapServerError } from '../api/errors';

export function LoginScreen() {
  const [email, setEmail] = useState('');
  const [password, setPassword] = useState('');
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState<string | null>(null);
  const setSession = useAuthStore((s) => s.setSession);

  const onSubmit = async () => {
    setError(null);
    if (!email.trim() || !password) {
      setError('Email va parolni kiriting');
      return;
    }
    setLoading(true);
    try {
      const { token, user } = await login(email.trim(), password);
      await setSession(user, token);
    } catch (e: any) {
      setError(mapServerError(e?.response?.data));
    } finally {
      setLoading(false);
    }
  };

  return (
    <Screen>
      <ScrollView keyboardShouldPersistTaps="handled" contentContainerStyle={styles.scroll}>
        <View style={styles.logoWrap}>
          <Image
            source={require('../assets/global_tam_logo.jpg')}
            style={styles.logo}
            resizeMode="contain"
          />
        </View>
        <PageHeader eyebrow="Oshpaz kabineti" title="Kirish" />
        <Section style={styles.form}>
          <Banner tone="error" message={error} />
          <Field
            label="Email"
            placeholder="email@globaltam.uz"
            autoCapitalize="none"
            autoCorrect={false}
            keyboardType="email-address"
            value={email}
            onChangeText={setEmail}
          />
          <Field
            label="Parol"
            placeholder="••••••••"
            secureTextEntry
            value={password}
            onChangeText={setPassword}
          />
          <Button label="Kirish" onPress={onSubmit} loading={loading} />
        </Section>
      </ScrollView>
    </Screen>
  );
}

const styles = StyleSheet.create({
  scroll: { flexGrow: 1, justifyContent: 'center' },
  logoWrap: { alignItems: 'center', paddingTop: 26 },
  logo: { width: 240, height: 96 },
  form: { paddingTop: 2 },
});
```

- [ ] **Step 2: Kompilyatsiya va testlarni tekshirish**

Run: `npx tsc --noEmit && npx jest`
Expected: `tsc` exit 0; 11 suite, 58 test o'tadi.

- [ ] **Step 3: Commit**

```bash
git add mobile/src/screens/LoginScreen.tsx
git commit -m "feat(plan-7): rebuild LoginScreen on the C1 design system"
```

---

### Task 9: Bolalar soni bo'limi va `HomeScreen`

**Files:**
- Create: `mobile/src/components/ChildrenCountSection.tsx`
- Delete: `mobile/src/components/ChildrenCountCard.tsx`
- Modify: `mobile/src/screens/HomeScreen.tsx`

**Interfaces:**
- Consumes: `Section`, `Divider` (Task 4), `Field` (Task 6), `Button` (Task 5), `Banner`, `PageHeader` (Task 7), `AppText` (Task 3)
- Produces: `ChildrenCountSection` — propsiz komponent, `HomeScreen` uni chaqiradi.

`useQuery`/`useMutation` chaqiruvlari, `QUERY_KEY`, Toshkent vaqti hisobi, validatsiya va `mapServerError` — hammasi mavjud `ChildrenCountCard.tsx` dan so'zma-so'z ko'chiriladi. Faqat JSX almashadi.

- [ ] **Step 1: Yangi faylni yozish**

```tsx
// mobile/src/components/ChildrenCountSection.tsx
import React, { useState } from 'react';
import { View, ActivityIndicator, StyleSheet } from 'react-native';
import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query';
import { AppText } from './AppText';
import { Section } from './Section';
import { Divider } from './Divider';
import { PageHeader } from './PageHeader';
import { Field } from './Field';
import { Button } from './Button';
import { Banner } from './Banner';
import { colors } from '../theme/colors';
import { spacing } from '../theme/spacing';
import {
  getTodayState,
  submitChildrenCount,
  ChildrenCountState,
} from '../api/childrenCount';
import { mapServerError } from '../api/errors';

const QUERY_KEY = ['children-count', 'today'] as const;
const TASHKENT_OFFSET_MS = 5 * 60 * 60 * 1000;

export function ChildrenCountSection() {
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
      <Section>
        <ActivityIndicator />
      </Section>
    );
  }

  if (loadError || !data) {
    return (
      <View>
        <PageHeader title="Bolalar soni" />
        <Section>
          <Banner tone="error" message={(loadError as any)?.message ?? 'Yuklab bo\'lmadi'} />
          <Button label="Qayta urinish" variant="outline" onPress={() => void refetch()} />
        </Section>
      </View>
    );
  }

  const today = new Date(Date.now() + TASHKENT_OFFSET_MS).toISOString().slice(0, 10);

  // Holat C: vaqt oynasi yopiq yoki texnolog menyu sozlamagan
  if (!data.time_window.allowed || !data.nextday_ready) {
    const reason = !data.nextday_ready
      ? 'Texnolog kelajak kun uchun menyu sozlamagan. Texnologga murojaat qiling.'
      : `Hozir yuborish vaqti emas (${data.time_window.from} - ${data.time_window.to} oralig'ida bo'lishi kerak).`;
    return (
      <View>
        <PageHeader
          eyebrow={data.kindgarden.kingar_name}
          title="Bolalar soni"
          meta={today}
        />
        <Divider />
        <Section>
          <Banner tone="info" message={reason} />
          <AppText variant="muted" color={colors.inkMuted}>
            Vaqt: {data.time_window.from} - {data.time_window.to} oralig'ida{'\n'}
            Hozir (Tashkent): {data.time_window.current_tashkent}
          </AppText>
        </Section>
      </View>
    );
  }

  // Holat B: bugun yuborilgan
  if (data.submitted) {
    const submittedTime = data.submitted_at
      ? new Date(new Date(data.submitted_at).getTime() + TASHKENT_OFFSET_MS)
          .toISOString()
          .slice(11, 16)
      : '';
    return (
      <View>
        <PageHeader
          eyebrow={data.kindgarden.kingar_name}
          title="Bolalar soni"
          meta={today}
        />
        <Divider />
        <Section>
          <Banner tone="success" message={`Bugungi son yuborildi · ${submittedTime}`} />
          {data.age_ranges.map((a) => (
            <AppText key={a.id} variant="body" style={styles.row}>
              {a.age_name}: {data.today_counts?.[a.id] ?? '—'}
            </AppText>
          ))}
        </Section>
        <Divider />
        <Section>
          <AppText variant="muted" color={colors.inkMuted}>
            O'zgartirish kerak bo'lsa, texnologga murojaat qiling.
          </AppText>
        </Section>
      </View>
    );
  }

  // Holat A: ochiq forma
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
    <View>
      <PageHeader
        eyebrow={data.kindgarden.kingar_name}
        title="Bolalar soni"
        meta={`${today} · ${data.time_window.from} - ${data.time_window.to} oralig'ida`}
      />
      <Divider />
      <Section>
        <Banner tone="error" message={submitError} />
        {data.age_ranges.map((age) => (
          <Field
            key={age.id}
            label={age.age_name}
            value={inputs[age.id] ?? ''}
            onChangeText={(t) =>
              setInputs((p) => ({ ...p, [age.id]: t.replace(/[^0-9]/g, '') }))
            }
            keyboardType="number-pad"
            placeholder="0"
            editable={!mutation.isPending}
          />
        ))}
        <Button label="Yuborish" onPress={onSubmit} loading={mutation.isPending} />
      </Section>
    </View>
  );
}

const styles = StyleSheet.create({
  row: { marginTop: spacing.xs },
});
```

- [ ] **Step 2: Eski faylni o'chirish**

```bash
git rm mobile/src/components/ChildrenCountCard.tsx
```

- [ ] **Step 3: `HomeScreen` ni yangilash**

```tsx
// mobile/src/screens/HomeScreen.tsx
import React from 'react';
import { ScrollView, RefreshControl } from 'react-native';
import { useQueryClient } from '@tanstack/react-query';
import { Screen } from '../components/Screen';
import { ChildrenCountSection } from '../components/ChildrenCountSection';

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
    <Screen>
      <ScrollView
        refreshControl={<RefreshControl refreshing={refreshing} onRefresh={onRefresh} />}
      >
        <ChildrenCountSection />
      </ScrollView>
    </Screen>
  );
}
```

Logotip `HomeScreen` dan olib tashlandi — bog'cha nomi endi `PageHeader` ning eyebrow qatorida turadi va logotip har ekranda takrorlanishi C1 ning bo'sh joy tamoyiliga zid.

- [ ] **Step 4: Kompilyatsiya va testlarni tekshirish**

Run: `npx tsc --noEmit && npx jest`
Expected: `tsc` exit 0; 11 suite, 58 test o'tadi.

- [ ] **Step 5: Commit**

```bash
git add mobile/src/components/ChildrenCountSection.tsx mobile/src/screens/HomeScreen.tsx
git commit -m "feat(plan-7): ChildrenCountSection replaces the card layout"
```

---

### Task 10: `AttendanceScreen`

**Files:**
- Modify: `mobile/src/screens/AttendanceScreen.tsx` (chizish qatlami almashtiriladi)

**Interfaces:**
- Consumes: `Screen`, `Section`, `Divider` (Task 4), `Button` (Task 5), `Banner`, `StatusLine`, `PageHeader` (Task 7), `AppText` (Task 3)
- Produces: —

`onAction` funksiyasi, GPS olish, masofa hisobi, mock GPS tekshiruvi, queue va `refresh` — **hammasi so'zma-so'z saqlanadi**. Uch narsa o'zgaradi: debug oynasi `__DEV__` bilan o'raladi, progress matni tugma ichiga ko'chadi, JSX yangi komponentlarga o'tadi.

- [ ] **Step 1: Import bloki va JSX ni almashtirish**

Fayl boshidagi importlar:

```tsx
import React, { useEffect, useState, useCallback } from 'react';
import { View, ScrollView, RefreshControl, StyleSheet } from 'react-native';
import { Screen } from '../components/Screen';
import { Section } from '../components/Section';
import { Divider } from '../components/Divider';
import { PageHeader } from '../components/PageHeader';
import { Button } from '../components/Button';
import { Banner } from '../components/Banner';
import { StatusLine } from '../components/StatusLine';
import { AppText } from '../components/AppText';
import { colors } from '../theme/colors';
import { spacing } from '../theme/spacing';
import { useAttendanceStore } from '../attendance/attendanceStore';
import { ensureLocation, ensureCamera } from '../attendance/permissions';
import { getCurrentLocation } from '../attendance/locationService';
import { captureSelfie } from '../attendance/selfieCapture';
import { isMockGpsAllowed } from '../attendance/mockGps';
import { enqueue } from '../attendance/attendanceQueue';
import { postCheckIn, postCheckOut, postReplace, SubmitParams } from '../attendance/attendanceApi';
import { mapServerError } from '../api/errors';
import { isoNowUtc } from '../lib/tashkent';
import { haversineMeters } from '../lib/distance';
```

`ActionKind` tipi va butun `onAction` funksiyasi (30–117-satrlar) **o'zgarishsiz** qoladi. Faqat `return (...)` bloki va `styles` almashtiriladi:

```tsx
  const checkedIn = !!today?.check_in_at;
  const checkedOut = !!today?.check_out_at;

  const kgMeta = kindgarden
    ? `radius ${kindgarden.geofence_radius}m` +
      (kindgarden.lat != null
        ? ` · ${kindgarden.lat.toFixed(4)}, ${kindgarden.lng?.toFixed(4)}`
        : '')
    : null;

  return (
    <Screen>
      <ScrollView refreshControl={<RefreshControl refreshing={loading} onRefresh={refresh} />}>
        <PageHeader
          eyebrow={kindgarden ? `Bog'cha #${kindgarden.id}` : undefined}
          title="Davomat"
          meta={kgMeta}
        />
        <Divider />

        <Section>
          <Banner tone="error" message={actionError ?? error} />
          <Banner tone="success" message={actionInfo} />

          <StatusLine
            tone={checkedIn ? 'ok' : 'warn'}
            title={checkedIn ? 'Keldingiz — ' : 'Hali kelmadingiz'}
            accent={checkedIn ? today!.check_in_at!.slice(11, 16) : undefined}
            detail={
              checkedIn
                ? `Bog'cha markazidan ${today!.check_in_distance_m}m` +
                  (today!.check_in_is_late ? ' · kechikkan' : '')
                : null
            }
          />
        </Section>

        <Divider />

        <Section>
          <StatusLine
            tone={checkedOut ? 'ok' : checkedIn ? 'warn' : 'off'}
            title={checkedOut ? 'Ketdingiz — ' : 'Hali ketmadingiz'}
            accent={checkedOut ? today!.check_out_at!.slice(11, 16) : undefined}
          />
        </Section>

        <Section style={styles.actions}>
          {!checkedIn ? (
            <Button
              label="Keldim"
              loadingLabel={progress}
              onPress={() => onAction('check_in')}
              loading={actionLoading === 'check_in'}
            />
          ) : (
            <Button
              label="Kelishni qayta yuborish"
              loadingLabel={progress}
              variant="outline"
              onPress={() => onAction('replace_check_in')}
              loading={actionLoading === 'replace_check_in'}
            />
          )}

          <View style={styles.gap} />

          {checkedIn && !checkedOut ? (
            <Button
              label="Ketdim"
              loadingLabel={progress}
              onPress={() => onAction('check_out')}
              loading={actionLoading === 'check_out'}
            />
          ) : checkedOut ? (
            <Button
              label="Ketishni qayta yuborish"
              loadingLabel={progress}
              variant="outline"
              onPress={() => onAction('replace_check_out')}
              loading={actionLoading === 'replace_check_out'}
            />
          ) : (
            <Button label="Ketdim" onPress={() => {}} disabled />
          )}

          {!checkedIn && (
            <AppText variant="muted" color={colors.inkMuted} style={styles.hint}>
              Kelishni o'z vaqtida yuborolmadingizmi? Bog'chada turib yuqoridagi tugmani bosing —
              tizim avtomatik "kechikkan" deb belgilaydi.
            </AppText>
          )}
        </Section>

        {__DEV__ && debugInfo && (
          <Section style={styles.debug}>
            <AppText variant="caption" color={colors.inkMuted}>
              {debugInfo}
            </AppText>
          </Section>
        )}
      </ScrollView>
    </Screen>
  );
}

const styles = StyleSheet.create({
  actions: { paddingTop: spacing.sm },
  gap: { height: spacing.md },
  hint: { marginTop: spacing.lg },
  debug: { backgroundColor: colors.disabledBg },
});
```

`progress` `loadingLabel` propi orqali beriladi — shunda `loading` paytida tugma ichida spinner yonida `2/6 GPS koordinatasi olinmoqda...` ko'rinadi. Uni `label` ga berish noto'g'ri bo'lardi: `loading` holatida `Button` `label` ni umuman chizmaydi, ya'ni progress hech qachon ko'rinmasdi.

Eski `progressBox` va `infoBox` `View` lari hamda ularga tegishli `styles` yozuvlari faylda qolmaydi.

- [ ] **Step 2: Kompilyatsiya va testlarni tekshirish**

Run: `npx tsc --noEmit && npx jest`
Expected: `tsc` exit 0; 11 suite, 58 test o'tadi.

- [ ] **Step 3: Debug oynasi yashiringanini kod bo'yicha tasdiqlash**

Run: `grep -n "__DEV__" mobile/src/screens/AttendanceScreen.tsx`
Expected: `debugInfo` chizilishidan oldin `__DEV__ &&` shartini ko'rsatuvchi bitta satr.

- [ ] **Step 4: Commit**

```bash
git add mobile/src/screens/AttendanceScreen.tsx
git commit -m "feat(plan-7): rebuild AttendanceScreen; hide debug panel behind __DEV__

The debug panel showed chefs raw GPS coordinates, the kindergarten's
coordinates and a slice of the API JSON response. It is now development-only."
```

---

### Task 11: Tab paneli, qolgan ekranlar va eski komponentlarni o'chirish

**Files:**
- Modify: `mobile/src/navigation/MainTabs.tsx`
- Modify: `mobile/src/screens/ProfileScreen.tsx`, `mobile/src/screens/NotificationsScreen.tsx`
- Delete: `mobile/src/components/PrimaryButton.tsx`, `mobile/src/components/ErrorBanner.tsx`, `mobile/src/components/ScreenContainer.tsx`
- Modify: `mobile/README.md`

**Interfaces:**
- Consumes: ikonkalar (Task 2), `Screen`, `Section` (Task 4), `Button` (Task 5), `AppText` (Task 3)
- Produces: —

- [ ] **Step 1: `MainTabs` ni SVG ikonkalarga o'tkazish**

```tsx
// mobile/src/navigation/MainTabs.tsx
import React from 'react';
import { createBottomTabNavigator } from '@react-navigation/bottom-tabs';
import { HomeScreen } from '../screens/HomeScreen';
import { AttendanceScreen } from '../screens/AttendanceScreen';
import { NotificationsScreen } from '../screens/NotificationsScreen';
import { ProfileScreen } from '../screens/ProfileScreen';
import { HomeIcon } from '../components/icons/HomeIcon';
import { PinIcon } from '../components/icons/PinIcon';
import { BellIcon } from '../components/icons/BellIcon';
import { UserIcon } from '../components/icons/UserIcon';
import { IconProps } from '../components/icons/types';
import { colors } from '../theme/colors';
import { typography } from '../theme/typography';
import { hit } from '../theme/spacing';

const Tab = createBottomTabNavigator();

const icon =
  (Cmp: (p: IconProps) => JSX.Element) =>
  ({ focused }: { focused: boolean }) =>
    <Cmp size={22} color={focused ? colors.brand : colors.inkFaint} />;

export function MainTabs() {
  return (
    <Tab.Navigator
      screenOptions={{
        headerShown: false,
        tabBarActiveTintColor: colors.ink,
        tabBarInactiveTintColor: colors.inkFaint,
        tabBarLabelStyle: typography.tab,
        tabBarStyle: {
          height: hit.tabBar,
          paddingTop: 6,
          paddingBottom: 6,
          backgroundColor: colors.surface,
          borderTopWidth: 1,
          borderTopColor: colors.rule,
        },
      }}
    >
      <Tab.Screen
        name="Home"
        component={HomeScreen}
        options={{ tabBarLabel: 'Bosh', tabBarIcon: icon(HomeIcon) }}
      />
      <Tab.Screen
        name="Attendance"
        component={AttendanceScreen}
        options={{ tabBarLabel: 'Davomat', tabBarIcon: icon(PinIcon) }}
      />
      <Tab.Screen
        name="Notifications"
        component={NotificationsScreen}
        options={{ tabBarLabel: 'Xabarlar', tabBarIcon: icon(BellIcon) }}
      />
      <Tab.Screen
        name="Profile"
        component={ProfileScreen}
        options={{ tabBarLabel: 'Profil', tabBarIcon: icon(UserIcon) }}
      />
    </Tab.Navigator>
  );
}
```

- [ ] **Step 2: `NotificationsScreen` ni mexanik ko'chirish**

```tsx
// mobile/src/screens/NotificationsScreen.tsx
import React from 'react';
import { Screen } from '../components/Screen';
import { Section } from '../components/Section';
import { AppText } from '../components/AppText';

export function NotificationsScreen() {
  return (
    <Screen>
      <Section>
        <AppText variant="body">Xabarlar — keyingi versiyada</AppText>
      </Section>
    </Screen>
  );
}
```

- [ ] **Step 3: `ProfileScreen` ni mexanik ko'chirish**

Chiqish mantig'i (`Alert`, `stopFlusher`, `clearQueue`, `clearSession`) o'zgarmaydi.

```tsx
// mobile/src/screens/ProfileScreen.tsx
import React from 'react';
import { View, StyleSheet, Alert } from 'react-native';
import { Screen } from '../components/Screen';
import { Section } from '../components/Section';
import { PageHeader } from '../components/PageHeader';
import { AppText } from '../components/AppText';
import { Button } from '../components/Button';
import { useAuthStore } from '../auth/authStore';
import { stopFlusher } from '../attendance/attendanceFlusher';
import { clearAll as clearQueue } from '../attendance/attendanceQueue';
import { colors } from '../theme/colors';
import { spacing } from '../theme/spacing';

export function ProfileScreen() {
  const { user, clearSession } = useAuthStore();

  const onLogout = () => {
    Alert.alert(
      'Chiqish',
      'Hisobdan chiqishni xohlaysizmi? Yuborilmagan davomat ma\'lumotlari yo\'qoladi.',
      [
        { text: 'Bekor', style: 'cancel' },
        {
          text: 'Ha, chiqaman',
          style: 'destructive',
          onPress: async () => {
            stopFlusher();
            clearQueue();
            await clearSession();
          },
        },
      ],
    );
  };

  return (
    <Screen>
      <PageHeader title="Profil" />
      <Section>
        {user && (
          <View>
            <AppText variant="muted" color={colors.inkMuted}>Ism</AppText>
            <AppText variant="bodyStrong">{user.name}</AppText>
            <AppText variant="muted" color={colors.inkMuted} style={styles.gap}>Email</AppText>
            <AppText variant="bodyStrong">{user.email}</AppText>
          </View>
        )}
        <View style={styles.logout}>
          <Button label="Chiqish" variant="danger" onPress={onLogout} />
        </View>
      </Section>
    </Screen>
  );
}

const styles = StyleSheet.create({
  gap: { marginTop: spacing.md },
  logout: { marginTop: spacing.xl },
});
```

- [ ] **Step 4: Eski komponentlarni o'chirish**

```bash
git rm mobile/src/components/PrimaryButton.tsx mobile/src/components/ErrorBanner.tsx mobile/src/components/ScreenContainer.tsx
```

- [ ] **Step 5: Hech qanday qoldiq havola qolmaganini tekshirish**

Run: `grep -rn "PrimaryButton\|ErrorBanner\|ScreenContainer\|ChildrenCountCard" mobile/src mobile/__tests__`
Expected: natija yo'q (exit 1).

- [ ] **Step 6: Kompilyatsiya va testlarni tekshirish**

Run: `npx tsc --noEmit && npx jest`
Expected: `tsc` exit 0; 11 suite, 58 test o'tadi.

- [ ] **Step 7: `README.md` ga native paket eslatmasini qo'shish**

`mobile/README.md` dagi "Quick start" bo'limidan keyin quyidagi bo'lim qo'shiladi:

```markdown
## Native dependency — rebuild required

`react-native-svg` is a native module. After pulling changes that add or
update it, `npm install` alone is not enough — Metro cannot ship native code
over a running app. Rebuild the APK:

```bash
cd mobile && npm install
npx react-native run-android
```

Symptom of skipping this: the app crashes on launch with
`Invariant Violation: requireNativeComponent: "RNSVGSvgView" was not found`.
```

Shu bilan birga "Tests" bo'limidagi jumla yangilanadi: `Currently 13 unit tests across 5 suites pass: tashkent helpers, tokenStore, error mapping, authStore, attendanceQueue.` → `Currently 58 tests across 11 suites pass: the five original logic suites plus render tests for AppText, Button, Field, Section/Divider, Banner/StatusLine/PageHeader and the SVG icons.`

"Known limitations" ro'yxatiga tegilmaydi — undagi bandlar (mockGps native modul, dead-letter UI, Plan 4/5/6 ekranlari) bu ishga aloqador emas va hammasi hali ham o'z kuchida.

- [ ] **Step 8: Commit**

```bash
git add mobile/src/navigation/MainTabs.tsx mobile/src/screens/ProfileScreen.tsx mobile/src/screens/NotificationsScreen.tsx mobile/README.md
git commit -m "feat(plan-7): SVG tab icons, migrate remaining screens, drop legacy primitives"
```

---

### Task 12: APK yig'ish va qo'lda vizual tekshiruv

Bu vazifa kod yozmaydi — u rejaning haqiqiy maqsadi bajarilganini tasdiqlaydi. Avtomatik testlar rang tokenlarini tekshiradi, lekin ilova telefonda qanday ko'rinishini tekshira olmaydi.

**Files:** —

- [ ] **Step 1: APK yig'ish**

```bash
cd mobile && npx react-native run-android
```

Agar `RNSVGSvgView was not found` xatosi chiqsa — eski APK ishlatilyapti, `cd android && ./gradlew clean` qilib qayta yig'ing.

- [ ] **Step 2: Tungi rejimda login tekshiruvi (asosiy regressiya)**

1. Telefon sozlamalarida **tungi rejimni (dark mode) yoqing**
2. Ilovani oching
3. Email maydoniga matn yozing

Expected: yozilgan matn **qora rangda ko'rinadi**. Placeholder kulrang. Kunduzgi rejimda ham xuddi shunday.

Bu asl xatoning takrorlanish sharti edi — kunduzgi rejimda xato umuman ko'rinmaydi.

- [ ] **Step 3: Uch ekranni ko'zdan kechirish**

| Ekran | Nimani tekshirish |
|---|---|
| Login | Logo, "OSHPAZ KABINETI" marjon yozuv, "Kirish" katta qora sarlavha, ikkita maydon, marjon tugma |
| Bosh | Bog'cha nomi eyebrow'da, "Bolalar soni" sarlavha, chiziq, yosh maydonlari, marjon "Yuborish" |
| Davomat | Sarlavha, ikkita holat bo'limi rangli nuqta bilan, marjon va qora chegarali tugmalar, **debug oynasi yo'q** |
| Tab paneli | To'rtta chizmali ikonka (emoji emas), faol ikonka marjon, faol yorliq qora |

- [ ] **Step 4: Yorug'da o'qish tekshiruvi**

Telefonni quyosh nuriga olib chiqib Davomat ekranini o'qing. Holat matni va tugma yorliqlari o'qilishi kerak.

- [ ] **Step 5: Natijani yozib qo'yish**

Topilgan nuqsonlar `docs/superpowers/plans/` dagi shu reja fayliga qo'shimcha bo'lim sifatida yoziladi yoki alohida issue sifatida ochiladi. Vizual nuqson topilsa — u tuzatilmaguncha reja tugallangan hisoblanmaydi.

---

## Yakuniy holat

Reja to'liq bajarilganda:

- `npx tsc --noEmit` → exit 0
- `npx jest` → 11 suite, 58 test o'tadi (boshlang'ich 5 suite / 13 testdan o'sgan)
- `mobile/src/screens/` va `mobile/src/components/` da bironta hex rang qolmaydi (`icons/` dan tashqari — u yerda ham rang prop orqali keladi)
- Tungi rejimda login ishlaydi
- Oshpaz debug ma'lumotlarini ko'rmaydi
