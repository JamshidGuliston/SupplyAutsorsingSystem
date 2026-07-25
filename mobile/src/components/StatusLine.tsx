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
