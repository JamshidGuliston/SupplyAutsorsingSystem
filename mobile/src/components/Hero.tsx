import React, { ReactNode } from 'react';
import { View, StyleSheet } from 'react-native';
import { useSafeAreaInsets } from 'react-native-safe-area-context';
import { GradientFill } from './GradientFill';
import { OfflinePill } from './OfflinePill';
import { colors } from '../theme/colors';

// Ekran tepasidagi marjon gradient sarlavha. Pastki `overlap` dp ini HeroScreen varag'i yopadi,
// shuning uchun HeroScreen ga ham aynan shu `overlap` berilishi kerak.
export function Hero({
  children,
  minHeight = 150,
  circles = 1,
  overlap = 22,
  testID,
}: {
  children: ReactNode;
  minHeight?: number;
  circles?: 1 | 2;
  overlap?: number;
  testID?: string;
}) {
  const insets = useSafeAreaInsets();
  return (
    <View
      testID={testID}
      style={[
        styles.hero,
        {
          minHeight: minHeight + insets.top,
          paddingTop: insets.top + 8,
          paddingBottom: overlap + 14,
        },
      ]}
    >
      <GradientFill />
      <View pointerEvents="none" style={[styles.circle, styles.circleTop]} />
      {circles === 2 ? (
        <View pointerEvents="none" style={[styles.circle, styles.circleBottom]} />
      ) : null}
      <OfflinePill />
      {children}
    </View>
  );
}

const styles = StyleSheet.create({
  hero: { paddingHorizontal: 20, overflow: 'hidden' },
  circle: { position: 'absolute' },
  circleTop: {
    width: 200,
    height: 200,
    borderRadius: 100,
    right: -60,
    top: -70,
    backgroundColor: colors.heroCircle,
  },
  circleBottom: {
    width: 130,
    height: 130,
    borderRadius: 65,
    left: -45,
    bottom: -5,
    backgroundColor: colors.heroCircle2,
  },
});
