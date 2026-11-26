import React from 'react';
import { View, StyleSheet } from 'react-native';
import { AppText } from './AppText';
import { colors } from '../theme/colors';
import { radius } from '../theme/spacing';

export type PillTone = 'ok' | 'warn' | 'neutral' | 'onHero';

const TONES: Record<PillTone, { bg: string; fg: string }> = {
  ok: { bg: colors.okBg, fg: colors.okInk },
  warn: { bg: colors.warnBg, fg: colors.warnInk },
  neutral: { bg: colors.fieldBg, fg: colors.inkSoft },
  onHero: { bg: colors.heroChip, fg: colors.onHero },
};

export function Pill({
  label,
  tone,
  icon,
  dot,
  testID,
}: {
  label: string;
  tone: PillTone;
  icon?: React.ReactNode;
  dot?: boolean;
  testID?: string;
}) {
  const t = TONES[tone];
  return (
    <View testID={testID} style={[styles.box, { backgroundColor: t.bg }]}>
      {dot ? (
        <View testID={testID ? `${testID}-dot` : undefined} style={styles.dot} />
      ) : null}
      {icon}
      <AppText variant="pill" color={t.fg} numberOfLines={1}>
        {label}
      </AppText>
    </View>
  );
}

const styles = StyleSheet.create({
  box: {
    flexDirection: 'row',
    alignItems: 'center',
    alignSelf: 'flex-start',
    gap: 5,
    paddingVertical: 5,
    paddingHorizontal: 10,
    borderRadius: radius.pill,
  },
  dot: { width: 6, height: 6, borderRadius: 3, backgroundColor: colors.ok },
});
