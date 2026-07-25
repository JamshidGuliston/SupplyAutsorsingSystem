import React from 'react';
import { View, TextInput, TextInputProps, StyleSheet } from 'react-native';
import { AppText } from './AppText';
import { colors } from '../theme/colors';
import { typography } from '../theme/typography';
import { radius, hit, spacing } from '../theme/spacing';

type Props = TextInputProps & {
  label: string;
  testID?: string;
};

export function Field({ label, style, testID, ...rest }: Props) {
  return (
    <View style={styles.wrap}>
      <AppText variant="fieldLabel">{label}</AppText>
      <TextInput
        {...rest}
        testID={testID}
        style={[styles.input, style]}
        placeholderTextColor={colors.inkFaint}
      />
    </View>
  );
}

const styles = StyleSheet.create({
  wrap: { marginBottom: spacing.md },
  input: {
    minHeight: hit.field,
    marginTop: 6,
    borderWidth: 1.5,
    borderColor: colors.rule,
    borderRadius: radius.field,
    paddingHorizontal: 13,
    paddingVertical: 12,
    fontSize: typography.bodyStrong.fontSize,
    fontWeight: typography.bodyStrong.fontWeight,
    color: colors.ink,
    backgroundColor: colors.surface,
  },
});
