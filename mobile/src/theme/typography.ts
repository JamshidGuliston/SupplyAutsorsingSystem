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
