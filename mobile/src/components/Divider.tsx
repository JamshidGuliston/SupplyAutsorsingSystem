import React from 'react';
import { View, StyleSheet } from 'react-native';
import { colors } from '../theme/colors';

export function Divider({ testID }: { testID?: string }) {
  return <View testID={testID} style={styles.rule} />;
}

const styles = StyleSheet.create({
  rule: { height: 1, backgroundColor: colors.rule },
});
