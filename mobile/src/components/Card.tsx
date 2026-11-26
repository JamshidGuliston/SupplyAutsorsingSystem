import React from 'react';
import { View, StyleSheet, StyleProp, ViewStyle } from 'react-native';
import { colors } from '../theme/colors';
import { radius } from '../theme/spacing';

export function Card({
  children,
  style,
  testID,
}: {
  children?: React.ReactNode;
  style?: StyleProp<ViewStyle>;
  testID?: string;
}) {
  return (
    <View testID={testID} style={[styles.card, style]}>
      {children}
    </View>
  );
}

export function CardRow({
  children,
  footer,
  last,
  testID,
}: {
  children?: React.ReactNode;
  footer?: boolean;
  last?: boolean;
  testID?: string;
}) {
  return (
    <View
      testID={testID}
      style={[styles.row, !last && styles.divider, footer && styles.footer]}
    >
      {children}
    </View>
  );
}

const styles = StyleSheet.create({
  card: {
    backgroundColor: colors.card,
    borderRadius: radius.card,
    elevation: 2,
    shadowColor: colors.shadow,
    shadowOpacity: 0.06,
    shadowRadius: 16,
    shadowOffset: { width: 0, height: 4 },
  },
  row: { paddingVertical: 12, paddingHorizontal: 16 },
  divider: { borderBottomWidth: 1, borderBottomColor: colors.cardDivider },
  footer: {
    backgroundColor: colors.cardFooter,
    borderBottomLeftRadius: radius.card,
    borderBottomRightRadius: radius.card,
  },
});
