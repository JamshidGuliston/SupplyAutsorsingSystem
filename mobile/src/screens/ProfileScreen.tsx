import React from 'react';
import { Text, View, StyleSheet, Alert } from 'react-native';
import { ScreenContainer } from '../components/ScreenContainer';
import { PrimaryButton } from '../components/PrimaryButton';
import { useAuthStore } from '../auth/authStore';
import { stopFlusher } from '../attendance/attendanceFlusher';
import { clearAll as clearQueue } from '../attendance/attendanceQueue';
import { colors } from '../theme/colors';

export function ProfileScreen() {
  const { user, clearSession } = useAuthStore();

  const onLogout = () => {
    Alert.alert(
      'Chiqish',
      'Hisobdan chiqishni xohlaysizmi? Yuborilmagan davomat ma\'lumotlari yo\'qoladi.',
      [
        { text: 'Bekor', style: 'cancel' },
        {
          text: 'Ha, chiqaman',
          style: 'destructive',
          onPress: async () => {
            stopFlusher();
            clearQueue();
            await clearSession();
          },
        },
      ],
    );
  };

  return (
    <ScreenContainer>
      <View>
        <Text style={styles.h1}>Profil</Text>
        {user && (
          <View style={styles.box}>
            <Text style={styles.label}>Ism</Text>
            <Text style={styles.val}>{user.name}</Text>
            <Text style={styles.label}>Email</Text>
            <Text style={styles.val}>{user.email}</Text>
          </View>
        )}
        <View style={{ marginTop: 24 }}>
          <PrimaryButton label="Chiqish" variant="danger" onPress={onLogout} />
        </View>
      </View>
    </ScreenContainer>
  );
}

const styles = StyleSheet.create({
  h1: { fontSize: 24, fontWeight: '700', marginBottom: 16, color: colors.textPrimary },
  box: { backgroundColor: '#fff', borderRadius: 8, padding: 14, borderWidth: 1, borderColor: colors.border },
  label: { fontSize: 12, color: colors.textMuted, marginTop: 8 },
  val: { fontSize: 16, color: colors.textPrimary, fontWeight: '600' },
});
