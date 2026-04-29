import React, { useEffect, useState } from 'react';
import { View, Text, StyleSheet } from 'react-native';
import NetInfo from '@react-native-community/netinfo';
import { peekAll } from '../attendance/attendanceQueue';

export function OfflineIndicator() {
  const [online, setOnline] = useState<boolean>(true);
  const [pending, setPending] = useState<number>(0);

  useEffect(() => {
    const sub = NetInfo.addEventListener((state) => {
      setOnline(!!state.isConnected);
    });
    const tick = setInterval(() => setPending(peekAll().length), 5_000);
    return () => { sub(); clearInterval(tick); };
  }, []);

  if (online && pending === 0) return null;

  return (
    <View style={[styles.box, online ? styles.queueing : styles.offline]}>
      <Text style={styles.text}>
        {!online ? '📡 Internet yo\'q · ' : '📤 '}
        {pending > 0 && `${pending} yuborilmagan`}
      </Text>
    </View>
  );
}

const styles = StyleSheet.create({
  box: { padding: 8, alignItems: 'center' },
  offline: { backgroundColor: '#dc2626' },
  queueing: { backgroundColor: '#f59e0b' },
  text: { color: '#fff', fontSize: 12, fontWeight: '600' },
});
