import React from 'react';
import { View, StyleSheet } from 'react-native';
import { useFocusEffect } from '@react-navigation/native';
import Config from 'react-native-config';
import { HeroScreen } from '../components/HeroScreen';
import { Hero } from '../components/Hero';
import { AppText } from '../components/AppText';
import { Pill } from '../components/Pill';
import { Card, CardRow } from '../components/Card';
import { Button } from '../components/Button';
import { ConfirmDialog } from '../components/ConfirmDialog';
import { UserIcon } from '../components/icons/UserIcon';
import { MailIcon } from '../components/icons/MailIcon';
import { HomeIcon } from '../components/icons/HomeIcon';
import { SyncIcon } from '../components/icons/SyncIcon';
import { LogoutIcon } from '../components/icons/LogoutIcon';
import { IconProps } from '../components/icons/types';
import { useAuthStore } from '../auth/authStore';
import { stopFlusher } from '../attendance/attendanceFlusher';
import { clearAll as clearQueue, attendancePendingCount } from '../attendance/attendanceQueue';
import { useChildrenCountToday } from '../api/childrenCount';
import { displayName, initials } from '../lib/text';
import { colors } from '../theme/colors';
import { spacing } from '../theme/spacing';

function InfoRow({
  icon,
  label,
  last,
  children,
}: {
  icon: (p: IconProps) => JSX.Element;
  label: string;
  last?: boolean;
  children: React.ReactNode;
}) {
  return (
    <CardRow last={last}>
      <View style={styles.infoRow}>
        <View style={styles.iconBox}>{icon({ size: 16, color: colors.inkSoft })}</View>
        <View style={styles.infoText}>
          <AppText variant="caption" color={colors.inkFaint}>
            {label}
          </AppText>
          {children}
        </View>
      </View>
    </CardRow>
  );
}

export function ProfileScreen() {
  const user = useAuthStore((s) => s.user);
  const clearSession = useAuthStore((s) => s.clearSession);
  const { data } = useChildrenCountToday();
  const [pending, setPending] = React.useState(0);
  const [confirm, setConfirm] = React.useState(false);

  useFocusEffect(
    React.useCallback(() => {
      setPending(attendancePendingCount());
    }, []),
  );

  const name = displayName(user?.name);
  const ini = initials(user?.name);
  const kindgarden = data?.kindgarden?.kingar_name;
  const version = Config.VERSION_NAME;

  const doLogout = async () => {
    setConfirm(false);
    stopFlusher();
    clearQueue();
    await clearSession();
  };

  return (
    <HeroScreen
      hero={
        <Hero minHeight={200} circles={2}>
          <View style={styles.heroCenter}>
            <View style={styles.avatar}>
              {ini ? (
                <AppText variant="heroTitleLg" color={colors.brand}>
                  {ini}
                </AppText>
              ) : (
                <UserIcon size={34} color={colors.brand} testID="avatar-icon" />
              )}
            </View>
            {name ? (
              <AppText
                variant="heroTitle"
                color={colors.onHero}
                numberOfLines={2}
                style={styles.name}
              >
                {name}
              </AppText>
            ) : null}
            <View style={styles.role}>
              <Pill tone="onHero" label="Oshpaz" />
            </View>
          </View>
        </Hero>
      }
    >
      <Card>
        {user ? (
          <InfoRow icon={MailIcon} label="Email">
            <AppText variant="bodyStrong">{user.email}</AppText>
          </InfoRow>
        ) : null}
        {kindgarden ? (
          <InfoRow icon={HomeIcon} label="Bog'cha">
            <AppText variant="bodyStrong">{kindgarden}</AppText>
          </InfoRow>
        ) : null}
        <InfoRow icon={SyncIcon} label="Yuborilmagan davomat" last>
          <View style={styles.queueRow}>
            <View
              style={[styles.dot, { backgroundColor: pending > 0 ? colors.warn : colors.ok }]}
            />
            <AppText variant="bodyStrong" color={pending > 0 ? colors.warnInk : colors.ink}>
              {pending > 0 ? `${pending} ta navbatda` : "Navbat bo'sh"}
            </AppText>
          </View>
        </InfoRow>
      </Card>

      <View style={styles.logout}>
        <Button
          label="Hisobdan chiqish"
          variant="danger"
          icon={LogoutIcon}
          onPress={() => setConfirm(true)}
        />
      </View>

      {version ? (
        <AppText variant="caption" color={colors.inkFaint} style={styles.version}>
          {`Global Ta'm · versiya ${version}`}
        </AppText>
      ) : null}

      <ConfirmDialog
        visible={confirm}
        title="Chiqishni xohlaysizmi?"
        confirmLabel="Ha, chiqaman"
        cancelLabel="Bekor qilish"
        onConfirm={doLogout}
        onCancel={() => setConfirm(false)}
      >
        {pending > 0 ? (
          <>
            <AppText variant="bodyStrong" color={colors.warnInk}>
              {`${pending} ta davomat hali yuborilmagan`}
            </AppText>
            {" — chiqsangiz, u yo'qoladi."}
          </>
        ) : (
          "Qayta kirish uchun email va parol kerak bo'ladi."
        )}
      </ConfirmDialog>
    </HeroScreen>
  );
}

const styles = StyleSheet.create({
  heroCenter: { alignItems: 'center' },
  avatar: {
    width: 70,
    height: 70,
    borderRadius: 35,
    backgroundColor: colors.onHero,
    alignItems: 'center',
    justifyContent: 'center',
  },
  name: { marginTop: spacing.md, textAlign: 'center' },
  role: { marginTop: spacing.sm },
  infoRow: { flexDirection: 'row', alignItems: 'center' },
  iconBox: {
    width: 32,
    height: 32,
    borderRadius: 10,
    backgroundColor: colors.fieldBg,
    alignItems: 'center',
    justifyContent: 'center',
    marginRight: spacing.md,
  },
  infoText: { flex: 1 },
  queueRow: { flexDirection: 'row', alignItems: 'center' },
  dot: { width: 8, height: 8, borderRadius: 4, marginRight: 6 },
  logout: { marginTop: spacing.lg },
  version: { marginTop: spacing.lg, textAlign: 'center' },
});
