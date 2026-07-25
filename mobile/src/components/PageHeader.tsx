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
