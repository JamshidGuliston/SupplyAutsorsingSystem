import React from 'react';
import {
  View,
  Pressable,
  ActivityIndicator,
  StyleSheet,
  StyleProp,
  ViewStyle,
} from 'react-native';
import { AppText } from './AppText';
import { colors } from '../theme/colors';
import { radius, hit, spacing } from '../theme/spacing';

export type ButtonVariant = 'primary' | 'outline' | 'danger';

type Tone = { bg: string; fg: string; borderWidth: number; borderColor: string };

const TONES: Record<ButtonVariant, Tone> = {
  primary: { bg: colors.brand, fg: colors.surface, borderWidth: 0, borderColor: 'transparent' },
  danger: { bg: colors.danger, fg: colors.surface, borderWidth: 0, borderColor: 'transparent' },
  outline: { bg: colors.surface, fg: colors.ink, borderWidth: 2, borderColor: colors.ink },
};

const DISABLED: Tone = {
  bg: colors.disabledBg,
  fg: colors.disabledInk,
  borderWidth: 0,
  borderColor: 'transparent',
};

export function Button({
  label,
  onPress,
  variant = 'primary',
  loading = false,
  loadingLabel,
  disabled = false,
  style,
  testID,
}: {
  label: string;
  onPress: () => void;
  variant?: ButtonVariant;
  loading?: boolean;
  loadingLabel?: string | null;
  disabled?: boolean;
  style?: StyleProp<ViewStyle>;
  testID?: string;
}) {
  const tone = disabled ? DISABLED : TONES[variant];
  return (
    <Pressable
      testID={testID}
      onPress={onPress}
      disabled={disabled || loading}
      style={[
        styles.btn,
        {
          backgroundColor: tone.bg,
          borderWidth: tone.borderWidth,
          borderColor: tone.borderColor,
        },
        style,
      ]}
    >
      {loading ? (
        <View style={styles.loadingRow}>
          <ActivityIndicator color={tone.fg} />
          {!!loadingLabel && (
            <AppText variant="button" color={tone.fg} style={styles.loadingLabel}>
              {loadingLabel}
            </AppText>
          )}
        </View>
      ) : (
        <AppText variant="button" color={tone.fg}>
          {label}
        </AppText>
      )}
    </Pressable>
  );
}

const styles = StyleSheet.create({
  btn: {
    minHeight: hit.button,
    borderRadius: radius.button,
    paddingHorizontal: spacing.lg,
    alignItems: 'center',
    justifyContent: 'center',
  },
  loadingRow: { flexDirection: 'row', alignItems: 'center' },
  loadingLabel: { marginLeft: spacing.sm, flexShrink: 1 },
});
