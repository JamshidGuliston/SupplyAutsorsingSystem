import React from 'react';
import { View, StyleSheet } from 'react-native';
import { AppText } from './AppText';
import { IconCircle, IconCircleTone } from './IconCircle';
import { colors } from '../theme/colors';
import { spacing } from '../theme/spacing';

export function EmptyState({
  icon,
  tone,
  title,
  body,
  size = 96,
  action,
  testID,
}: {
  icon: React.ReactNode;
  tone: IconCircleTone;
  title: string;
  body: string;
  size?: number;
  action?: React.ReactNode;
  testID?: string;
}) {
  return (
    <View testID={testID} style={styles.box}>
      <IconCircle tone={tone} size={size} testID={testID ? `${testID}-icon` : undefined}>
        {icon}
      </IconCircle>
      <AppText variant="sectionTitle" style={styles.title}>
        {title}
      </AppText>
      <AppText variant="muted" color={colors.inkMuted} style={styles.body}>
        {body}
      </AppText>
      {action ? <View style={styles.action}>{action}</View> : null}
    </View>
  );
}

const styles = StyleSheet.create({
  box: { alignItems: 'center', paddingHorizontal: spacing.xl },
  title: { marginTop: spacing.lg, textAlign: 'center' },
  body: { marginTop: spacing.xs, textAlign: 'center' },
  action: { marginTop: spacing.lg, alignSelf: 'stretch' },
});
