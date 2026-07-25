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
