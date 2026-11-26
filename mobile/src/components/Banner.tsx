import React from 'react';
import { View, StyleSheet } from 'react-native';
import { AppText } from './AppText';
import { AlertIcon } from './icons/AlertIcon';
import { CheckIcon } from './icons/CheckIcon';
import { SyncIcon } from './icons/SyncIcon';
import { colors } from '../theme/colors';
import { spacing, radius } from '../theme/spacing';

export type BannerTone = 'error' | 'success' | 'warn';

const TONES: Record<
  BannerTone,
  { bg: string; iconBg: string; fg: string; Icon: typeof AlertIcon }
> = {
  error: { bg: colors.dangerBg, iconBg: colors.dangerIconBg, fg: colors.dangerInk, Icon: AlertIcon },
  success: { bg: colors.okBg, iconBg: colors.okIconBg, fg: colors.okInk, Icon: CheckIcon },
  warn: { bg: colors.warnBg, iconBg: colors.warnIconBg, fg: colors.warnInk, Icon: SyncIcon },
};

export function Banner({
  tone = 'error',
  message,
  title,
  testID,
}: {
  tone?: BannerTone;
  message?: string | null;
  title?: string;
  testID?: string;
}) {
  if (!message) return null;
  const t = TONES[tone];
  return (
    <View testID={testID} style={[styles.box, { backgroundColor: t.bg }]}>
      <View
        testID={testID ? `${testID}-icon` : undefined}
        style={[styles.iconCircle, { backgroundColor: t.iconBg }]}
      >
        <t.Icon size={13} color={t.fg} />
      </View>
      <AppText variant="body" color={t.fg} style={styles.text}>
        {title ? (
          <AppText variant="bodyStrong" color={t.fg}>
            {title}{' '}
          </AppText>
        ) : null}
        {message}
      </AppText>
    </View>
  );
}

const styles = StyleSheet.create({
  box: {
    flexDirection: 'row',
    alignItems: 'flex-start',
    gap: 10,
    borderRadius: radius.banner,
    paddingVertical: 11,
    paddingHorizontal: spacing.md,
    marginBottom: spacing.md,
  },
  iconCircle: {
    width: 22,
    height: 22,
    borderRadius: 11,
    alignItems: 'center',
    justifyContent: 'center',
  },
  text: { flex: 1 },
});
