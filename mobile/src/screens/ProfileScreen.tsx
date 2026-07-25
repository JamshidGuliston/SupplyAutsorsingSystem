import React from 'react';
import { View, StyleSheet, Alert } from 'react-native';
import { Screen } from '../components/Screen';
import { Section } from '../components/Section';
import { PageHeader } from '../components/PageHeader';
import { AppText } from '../components/AppText';
import { Button } from '../components/Button';
import { useAuthStore } from '../auth/authStore';
import { stopFlusher } from '../attendance/attendanceFlusher';
import { clearAll as clearQueue } from '../attendance/attendanceQueue';
import { colors } from '../theme/colors';
import { spacing } from '../theme/spacing';

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
    <Screen>
      <PageHeader title="Profil" />
      <Section>
        {user && (
          <View>
            <AppText variant="muted" color={colors.inkMuted}>Ism</AppText>
            <AppText variant="bodyStrong">{user.name}</AppText>
            <AppText variant="muted" color={colors.inkMuted} style={styles.gap}>Email</AppText>
            <AppText variant="bodyStrong">{user.email}</AppText>
          </View>
        )}
        <View style={styles.logout}>
          <Button label="Chiqish" variant="danger" onPress={onLogout} />
        </View>
      </Section>
    </Screen>
  );
}

const styles = StyleSheet.create({
  gap: { marginTop: spacing.md },
  logout: { marginTop: spacing.xl },
});
