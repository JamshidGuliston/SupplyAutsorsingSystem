import React, { useEffect, useState } from 'react';
import { View, StyleSheet } from 'react-native';
import NetInfo from '@react-native-community/netinfo';
import { AppText } from './AppText';
import { WifiOffIcon } from './icons/WifiOffIcon';
import { SyncIcon } from './icons/SyncIcon';
import { usePendingCount } from '../attendance/usePendingCount';
import { colors } from '../theme/colors';
import { radius } from '../theme/spacing';

export function offlinePillText(
  online: boolean,
  pending: number,
): { text: string; tone: 'danger' | 'warn' } | null {
  if (!online) {
    return {
      text: pending > 0 ? `Internet yo'q · ${pending} ta yuborilmagan` : "Internet yo'q",
      tone: 'danger',
    };
  }
  if (pending > 0) return { text: `${pending} ta yuborilmagan`, tone: 'warn' };
  return null;
}

// Hero ichida, status bar ostidagi oq kapsula: internet yo'qligi va yuborilmagan davomatlar soni.
export function OfflinePill() {
  const [online, setOnline] = useState<boolean>(true);
  const { pending } = usePendingCount();

  useEffect(() => {
    const unsubscribe = NetInfo.addEventListener((state) => {
      setOnline(!!state.isConnected);
    });
    return unsubscribe;
  }, []);

  const pill = offlinePillText(online, pending);
  if (!pill) return null;

  const danger = pill.tone === 'danger';
  const Icon = danger ? WifiOffIcon : SyncIcon;
  return (
    <View testID="offline-pill" style={styles.box}>
      <Icon size={13} color={danger ? colors.danger : colors.warn} />
      <AppText variant="pill" color={danger ? colors.danger : colors.warnInk} numberOfLines={1}>
        {pill.text}
      </AppText>
    </View>
  );
}

const styles = StyleSheet.create({
  box: {
    flexDirection: 'row',
    alignItems: 'center',
    alignSelf: 'flex-start',
    gap: 6,
    paddingVertical: 4,
    paddingLeft: 8,
    paddingRight: 10,
    marginBottom: 8,
    borderRadius: radius.pill,
    backgroundColor: colors.surface,
    elevation: 3,
    shadowColor: colors.ink,
    shadowOpacity: 0.12,
    shadowRadius: 10,
    shadowOffset: { width: 0, height: 4 },
  },
});
