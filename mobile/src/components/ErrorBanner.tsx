import React from 'react';
import { View, Text, StyleSheet } from 'react-native';
import { colors } from '../theme/colors';

export function ErrorBanner({ message }: { message?: string | null }) {
  if (!message) return null;
  return (
    <View style={styles.box}>
      <Text style={styles.text}>{message}</Text>
    </View>
  );
}

const styles = StyleSheet.create({
  box: {
    backgroundColor: '#fee2e2',
    borderLeftWidth: 4,
    borderLeftColor: colors.danger,
    padding: 12,
    borderRadius: 6,
    marginBottom: 12,
  },
  text: { color: '#991b1b', fontSize: 14 },
});
