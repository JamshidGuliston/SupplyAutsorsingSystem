import React from 'react';
import { View } from 'react-native';
import { colors } from '../theme/colors';

export type IconCircleTone = 'ok' | 'warn' | 'danger' | 'brand' | 'locked' | 'neutral';

const BG: Record<IconCircleTone, string> = {
  ok: colors.okBg,
  warn: colors.warnBg,
  danger: colors.dangerBg,
  brand: colors.brandSoft,
  locked: colors.lockedBg,
  neutral: colors.fieldBg,
};

export function IconCircle({
  tone,
  size,
  children,
  testID,
}: {
  tone: IconCircleTone;
  size: number;
  children?: React.ReactNode;
  testID?: string;
}) {
  return (
    <View
      testID={testID}
      style={{
        width: size,
        height: size,
        borderRadius: size / 2,
        backgroundColor: BG[tone],
        alignItems: 'center',
        justifyContent: 'center',
      }}
    >
      {children}
    </View>
  );
}
