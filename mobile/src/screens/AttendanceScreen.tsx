import React, { useEffect, useState, useCallback } from 'react';
import { Text, View, ScrollView, RefreshControl, StyleSheet } from 'react-native';
import { ScreenContainer } from '../components/ScreenContainer';
import { PrimaryButton } from '../components/PrimaryButton';
import { ErrorBanner } from '../components/ErrorBanner';
import { colors } from '../theme/colors';
import { useAttendanceStore } from '../attendance/attendanceStore';
import { ensureLocation, ensureCamera } from '../attendance/permissions';
import { getCurrentLocation } from '../attendance/locationService';
import { captureSelfie } from '../attendance/selfieCapture';
import { isMockGpsAllowed } from '../attendance/mockGps';
import { enqueue } from '../attendance/attendanceQueue';
import { flushOnce } from '../attendance/attendanceFlusher';
import { mapServerError } from '../api/errors';
import { isoNowUtc } from '../lib/tashkent';

type ActionKind = 'check_in' | 'check_out' | 'replace_check_in' | 'replace_check_out';

export function AttendanceScreen() {
  const { today, kindgarden, loading, error, refresh } = useAttendanceStore();
  const [actionLoading, setActionLoading] = useState<ActionKind | null>(null);
  const [actionError, setActionError] = useState<string | null>(null);

  useEffect(() => { void refresh(); }, [refresh]);

  const onAction = useCallback(async (kind: ActionKind) => {
    setActionError(null);
    setActionLoading(kind);
    try {
      const loc = await ensureLocation();
      if (loc !== 'granted') {
        setActionError('Lokatsiya ruxsati kerak. Sozlamalarda yoqing.');
        return;
      }
      const cam = await ensureCamera();
      if (cam !== 'granted') {
        setActionError('Kamera ruxsati kerak. Sozlamalarda yoqing.');
        return;
      }
      const fix = await getCurrentLocation(10_000);
      const allowed = await isMockGpsAllowed();
      const isMock = fix.isMock || allowed;
      const selfie = await captureSelfie();
      enqueue({
        kind,
        lat: fix.lat,
        lng: fix.lng,
        capturedAt: isoNowUtc(),
        isMock,
        photoUri: selfie.uri,
      });
      await flushOnce();
      await refresh();
    } catch (e: any) {
      const apiError = e?.response?.data;
      setActionError(apiError ? mapServerError(apiError) : (e?.message ?? 'Xato'));
    } finally {
      setActionLoading(null);
    }
  }, [refresh]);

  const checkedIn = !!today?.check_in_at;
  const checkedOut = !!today?.check_out_at;

  return (
    <ScreenContainer>
      <ScrollView
        refreshControl={<RefreshControl refreshing={loading} onRefresh={refresh} />}
      >
        <Text style={styles.h1}>Davomat</Text>
        {kindgarden && (
          <Text style={styles.kg}>
            Bog'cha #{kindgarden.id} · radius {kindgarden.geofence_radius}m
          </Text>
        )}

        <ErrorBanner message={actionError ?? error} />

        <View style={styles.statusBox}>
          <Text style={styles.muted}>BUGUN</Text>
          {checkedIn ? (
            <Text style={styles.statusOk}>
              ✅ Keldim — {today!.check_in_at!.slice(11, 16)} ({today!.check_in_distance_m}m)
              {today!.check_in_is_late && '  ⚠️ kechikkan'}
            </Text>
          ) : (
            <Text style={styles.statusWarn}>⏰ Hali kelmadingiz</Text>
          )}
          {checkedOut && (
            <Text style={styles.statusOk}>
              ✅ Ketdim — {today!.check_out_at!.slice(11, 16)}
            </Text>
          )}
        </View>

        {!checkedIn ? (
          <PrimaryButton
            label="📷 Keldim"
            variant="success"
            onPress={() => onAction('check_in')}
            loading={actionLoading === 'check_in'}
          />
        ) : (
          <PrimaryButton
            label="📷 Kelishni qayta yuborish"
            onPress={() => onAction('replace_check_in')}
            loading={actionLoading === 'replace_check_in'}
          />
        )}

        <View style={{ height: 12 }} />

        {checkedIn && !checkedOut ? (
          <PrimaryButton
            label="📷 Ketdim"
            variant="danger"
            onPress={() => onAction('check_out')}
            loading={actionLoading === 'check_out'}
          />
        ) : checkedOut ? (
          <PrimaryButton
            label="📷 Ketishni qayta yuborish"
            onPress={() => onAction('replace_check_out')}
            loading={actionLoading === 'replace_check_out'}
          />
        ) : (
          <PrimaryButton label="📷 Ketdim" onPress={() => {}} disabled />
        )}

        {!checkedIn && (
          <View style={{ marginTop: 16 }}>
            <Text style={styles.muted}>
              Kelishni o'z vaqtida yuborolmadingizmi? Bog'chada turib yuqoridagi tugmani bosing —
              tizim avtomatik "kechikkan" deb belgilaydi.
            </Text>
          </View>
        )}
      </ScrollView>
    </ScreenContainer>
  );
}

const styles = StyleSheet.create({
  h1: { fontSize: 24, fontWeight: '700', marginBottom: 4, color: colors.textPrimary },
  kg: { fontSize: 12, color: colors.textMuted, marginBottom: 16 },
  statusBox: {
    backgroundColor: '#fff',
    borderWidth: 1,
    borderColor: colors.border,
    borderRadius: 8,
    padding: 14,
    marginBottom: 16,
  },
  muted: { fontSize: 11, color: colors.textMuted },
  statusOk: { fontSize: 16, color: colors.success, marginTop: 6, fontWeight: '600' },
  statusWarn: { fontSize: 16, color: colors.warning, marginTop: 6, fontWeight: '600' },
});
