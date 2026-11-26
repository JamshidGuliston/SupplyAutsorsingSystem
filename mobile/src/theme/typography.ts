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
  | 'tab'
  | 'heroTitle'
  | 'heroTitleLg'
  | 'heroSub'
  | 'sectionTitle'
  | 'timeValue'
  | 'stepLabel'
  | 'numberInput'
  | 'total'
  | 'label'
  | 'pill'
  | 'buttonSm';

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
  button: { fontSize: 15, lineHeight: 20, fontWeight: '700', letterSpacing: 0 },
  tab: { fontSize: 10, lineHeight: 12, fontWeight: '600', letterSpacing: 0 },
  heroTitle: { fontSize: 21, lineHeight: 26, fontWeight: '800', letterSpacing: -0.5 },
  heroTitleLg: { fontSize: 26, lineHeight: 30, fontWeight: '800', letterSpacing: -0.6 },
  heroSub: { fontSize: 12, lineHeight: 16, fontWeight: '400', letterSpacing: 0 },
  sectionTitle: { fontSize: 18, lineHeight: 22, fontWeight: '800', letterSpacing: -0.4 },
  timeValue: { fontSize: 20, lineHeight: 24, fontWeight: '800', letterSpacing: -0.5 },
  stepLabel: { fontSize: 12, lineHeight: 16, fontWeight: '600', letterSpacing: 0 },
  numberInput: { fontSize: 17, lineHeight: 22, fontWeight: '800', letterSpacing: 0 },
  total: { fontSize: 18, lineHeight: 22, fontWeight: '800', letterSpacing: -0.3 },
  label: { fontSize: 12, lineHeight: 16, fontWeight: '600', letterSpacing: 0 },
  pill: { fontSize: 11, lineHeight: 14, fontWeight: '700', letterSpacing: 0 },
  buttonSm: { fontSize: 13, lineHeight: 18, fontWeight: '700', letterSpacing: 0 },
};
