import React, { ReactElement, ReactNode } from 'react';
import { View, ScrollView, KeyboardAvoidingView, Platform, StyleSheet } from 'react-native';
import { useSafeAreaInsets } from 'react-native-safe-area-context';
import { colors } from '../theme/colors';
import { radius, screen } from '../theme/spacing';

// Hero kontent bilan birga aylanadi; ustidan yumaloq varaq chiqadi. Status bar ostidagi
// marjon tasma esa qotib turadi — aylantirilganda ham oq status bar belgilari o'qiladi.
export function HeroScreen({
  hero,
  children,
  sheet = 'canvas',
  overlap = 22,
  refreshControl,
  keyboardShouldPersistTaps,
  testID,
}: {
  hero: ReactNode;
  children: ReactNode;
  sheet?: 'canvas' | 'white';
  overlap?: number;
  refreshControl?: ReactElement;
  keyboardShouldPersistTaps?: 'handled';
  testID?: string;
}) {
  const insets = useSafeAreaInsets();
  const white = sheet === 'white';
  const sheetBg = white ? colors.card : colors.canvas;
  const sheetRadius = white ? radius.sheetLg : radius.sheet;

  return (
    <KeyboardAvoidingView
      testID={testID}
      style={[styles.flex, { backgroundColor: sheetBg }]}
      behavior={Platform.OS === 'ios' ? 'padding' : undefined}
    >
      <ScrollView
        contentContainerStyle={styles.content}
        refreshControl={refreshControl}
        keyboardShouldPersistTaps={keyboardShouldPersistTaps}
      >
        {hero}
        <View
          testID={testID ? `${testID}-sheet` : undefined}
          style={[
            styles.sheet,
            {
              marginTop: -overlap,
              borderTopLeftRadius: sheetRadius,
              borderTopRightRadius: sheetRadius,
              backgroundColor: sheetBg,
            },
          ]}
        >
          {children}
        </View>
      </ScrollView>
      <View
        testID="status-strip"
        pointerEvents="none"
        style={[styles.strip, { height: insets.top }]}
      />
    </KeyboardAvoidingView>
  );
}

const styles = StyleSheet.create({
  flex: { flex: 1 },
  content: { flexGrow: 1 },
  sheet: {
    flexGrow: 1,
    paddingVertical: screen.sectionPaddingV,
    paddingHorizontal: screen.paddingH,
  },
  strip: {
    position: 'absolute',
    top: 0,
    left: 0,
    right: 0,
    backgroundColor: colors.brandTop,
  },
});
