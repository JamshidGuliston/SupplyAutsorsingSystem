import React, { useState } from 'react';
import {
  View,
  TextInput,
  TextInputProps,
  Pressable,
  StyleSheet,
  NativeSyntheticEvent,
  TextInputFocusEventData,
} from 'react-native';
import { AppText } from './AppText';
import { EyeIcon } from './icons/EyeIcon';
import { EyeOffIcon } from './icons/EyeOffIcon';
import { IconProps } from './icons/types';
import { colors } from '../theme/colors';
import { typography } from '../theme/typography';
import { radius, hit, spacing } from '../theme/spacing';

type FocusEvent = NativeSyntheticEvent<TextInputFocusEventData>;

type Props = TextInputProps & {
  label?: string;
  variant?: 'text' | 'number';
  icon?: (p: IconProps) => JSX.Element;
  testID?: string;
};

export function Field({
  label,
  variant = 'text',
  icon,
  style,
  testID,
  onFocus,
  onBlur,
  secureTextEntry,
  keyboardType,
  ...rest
}: Props) {
  const [focused, setFocused] = useState(false);
  const [revealed, setRevealed] = useState(false);
  const isNumber = variant === 'number';
  const hasEye = !isNumber && !!secureTextEntry;
  const hasIcon = !isNumber && !!icon;

  const handleFocus = (e: FocusEvent) => {
    setFocused(true);
    onFocus?.(e);
  };
  const handleBlur = (e: FocusEvent) => {
    setFocused(false);
    onBlur?.(e);
  };

  const halo = (
    <View
      testID={testID ? `${testID}-halo` : undefined}
      style={[styles.halo, { borderColor: focused ? colors.brandSoft : 'transparent' }]}
    >
      <View style={styles.row}>
        {hasIcon && icon ? (
          <View style={styles.iconSlot} pointerEvents="none">
            {icon({ size: 18, color: focused ? colors.brand : colors.inkFaint })}
          </View>
        ) : null}
        <TextInput
          {...rest}
          testID={testID}
          onFocus={handleFocus}
          onBlur={handleBlur}
          keyboardType={keyboardType ?? (isNumber ? 'number-pad' : undefined)}
          secureTextEntry={hasEye ? !revealed : secureTextEntry}
          style={[
            isNumber ? styles.number : styles.input,
            isNumber
              ? focused && styles.numberFocused
              : focused && styles.inputFocused,
            hasIcon && styles.withIcon,
            hasEye && styles.withEye,
            style,
            styles.lockedInk,
          ]}
          placeholderTextColor={colors.inkFaint}
        />
        {hasEye ? (
          <Pressable
            testID={testID ? `${testID}-eye` : undefined}
            accessibilityRole="button"
            accessibilityLabel={revealed ? 'Parolni yashirish' : "Parolni ko'rsatish"}
            onPress={() => setRevealed((v) => !v)}
            hitSlop={8}
            style={styles.eye}
          >
            {revealed ? (
              <EyeOffIcon size={18} color={colors.inkFaint} />
            ) : (
              <EyeIcon size={18} color={colors.inkFaint} />
            )}
          </Pressable>
        ) : null}
      </View>
    </View>
  );

  if (isNumber) return halo;

  return (
    <View style={styles.wrap}>
      {label ? (
        <AppText variant="label" color={colors.inkSoft} style={styles.label}>
          {label}
        </AppText>
      ) : null}
      {halo}
    </View>
  );
}

const styles = StyleSheet.create({
  wrap: { marginBottom: spacing.md },
  label: { marginBottom: 6 },
  halo: {
    borderWidth: 4,
    borderRadius: radius.field + 4,
    margin: -4,
  },
  row: { justifyContent: 'center' },
  iconSlot: { position: 'absolute', left: 14, zIndex: 1 },
  eye: { position: 'absolute', right: 14, justifyContent: 'center' },
  input: {
    ...typography.bodyStrong,
    minHeight: hit.field,
    borderWidth: 1.5,
    borderColor: colors.fieldBorder,
    borderRadius: radius.field,
    paddingHorizontal: 14,
    paddingVertical: 12,
    color: colors.ink,
    backgroundColor: colors.card,
  },
  inputFocused: { borderColor: colors.brand },
  withIcon: { paddingLeft: 42 },
  withEye: { paddingRight: 44 },
  number: {
    ...typography.numberInput,
    width: hit.numberW,
    height: hit.numberH,
    borderWidth: 1.5,
    borderColor: 'transparent',
    borderRadius: radius.field,
    paddingVertical: 0,
    paddingHorizontal: 0,
    textAlign: 'center',
    color: colors.ink,
    backgroundColor: colors.fieldBg,
  },
  numberFocused: { backgroundColor: colors.card, borderColor: colors.brand },
  lockedInk: { color: colors.ink },
});
