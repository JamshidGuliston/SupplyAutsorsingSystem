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
import { IconProps } from './icons/types';
import { colors } from '../theme/colors';
import { radius, hit, spacing } from '../theme/spacing';

export type ButtonVariant = 'primary' | 'outline' | 'danger' | 'dangerSolid' | 'ghost';
export type Progress = { step: number; total: number; label: string };

type Tone = { bg: string; fg: string; borderWidth: number; borderColor: string };

const NO_BORDER = { borderWidth: 0, borderColor: 'transparent' };

const TONES: Record<ButtonVariant, Tone> = {
  primary: { bg: colors.ink, fg: colors.onHero, ...NO_BORDER },
  dangerSolid: { bg: colors.danger, fg: colors.onHero, ...NO_BORDER },
  outline: { bg: colors.card, fg: colors.ink, borderWidth: 1.5, borderColor: colors.outlineBorder },
  danger: { bg: colors.card, fg: colors.danger, borderWidth: 1.5, borderColor: colors.dangerBorder },
  ghost: { bg: 'transparent', fg: colors.inkSoft, ...NO_BORDER },
};

const DISABLED: Tone = { bg: colors.disabledBg, fg: colors.disabledInk, ...NO_BORDER };

export function Button({
  label,
  onPress,
  variant = 'primary',
  size = 'md',
  icon,
  loading = false,
  progress,
  disabled = false,
  style,
  testID,
}: {
  label: string;
  onPress: () => void;
  variant?: ButtonVariant;
  size?: 'md' | 'sm';
  icon?: (p: IconProps) => JSX.Element;
  loading?: boolean;
  progress?: Progress | null;
  disabled?: boolean;
  style?: StyleProp<ViewStyle>;
  testID?: string;
}) {
  const tone = disabled ? DISABLED : TONES[variant];
  const textVariant = size === 'sm' ? 'buttonSm' : 'button';
  return (
    <Pressable
      testID={testID}
      onPress={onPress}
      disabled={disabled || loading}
      style={[
        styles.btn,
        { minHeight: size === 'sm' ? hit.buttonSm : hit.button },
        {
          backgroundColor: tone.bg,
          borderWidth: tone.borderWidth,
          borderColor: tone.borderColor,
        },
        style,
      ]}
    >
      {loading && progress ? (
        <>
          <View
            testID={`${testID}-fill`}
            style={[
              styles.fill,
              { width: `${Math.round((progress.step / progress.total) * 100)}%` },
            ]}
          />
          <View style={styles.row}>
            <ActivityIndicator color={tone.fg} />
            <AppText variant={textVariant} color={tone.fg} style={styles.progressLabel}>
              {progress.label}
            </AppText>
            <AppText variant={textVariant} color={tone.fg} style={styles.step}>
              {`${progress.step}/${progress.total}`}
            </AppText>
          </View>
        </>
      ) : loading ? (
        <ActivityIndicator color={tone.fg} />
      ) : (
        <View style={styles.row}>
          {icon && <View style={styles.icon}>{icon({ size: 18, color: tone.fg })}</View>}
          <AppText variant={textVariant} color={tone.fg}>
            {label}
          </AppText>
        </View>
      )}
    </Pressable>
  );
}

const styles = StyleSheet.create({
  btn: {
    borderRadius: radius.button,
    paddingHorizontal: spacing.lg,
    alignItems: 'center',
    justifyContent: 'center',
    overflow: 'hidden',
  },
  fill: {
    position: 'absolute',
    left: 0,
    top: 0,
    bottom: 0,
    backgroundColor: colors.heroChip,
  },
  row: { flexDirection: 'row', alignItems: 'center' },
  icon: { marginRight: 9 },
  progressLabel: { marginLeft: spacing.sm, flexShrink: 1 },
  step: { marginLeft: spacing.sm, opacity: 0.6 },
});
