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
import { postCheckIn, postCheckOut, postReplace, SubmitParams } from '../attendance/attendanceApi';
import { mapServerError } from '../api/errors';
import { isoNowUtc } from '../lib/tashkent';
import { haversineMeters } from '../lib/distance';

type ActionKind = 'check_in' | 'check_out' | 'replace_check_in' | 'replace_check_out';

export function AttendanceScreen() {
  const { today, kindgarden, loading, error, refresh } = useAttendanceStore();
  const [actionLoading, setActionLoading] = useState<ActionKind | null>(null);
  const [actionError, setActionError] = useState<string | null>(null);
  const [actionInfo, setActionInfo] = useState<string | null>(null);
  const [progress, setProgress] = useState<string | null>(null);
  const [debugInfo, setDebugInfo] = useState<string | null>(null);

  useEffect(() => { void refresh(); }, [refresh]);

  const onAction = useCallback(async (kind: ActionKind) => {
    setActionError(null);
    setActionInfo(null);
    setDebugInfo(null);
    setActionLoading(kind);
    try {
      setProgress('1/6 Ruxsatlar tekshirilmoqda...');
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

      setProgress('2/6 GPS koordinatasi olinmoqda...');
      const fix = await getCurrentLocation(15_000);
      const allowed = await isMockGpsAllowed();
      const isMock = fix.isMock || allowed;

      setProgress('3/6 Masofa hisoblanmoqda...');
      if (kindgarden?.lat == null || kindgarden?.lng == null) {
        setActionError('Bog\'cha koordinatalari sozlanmagan. Addelkadirga murojaat qiling.');
        setDebugInfo(`kindgarden: ${JSON.stringify(kindgarden)}`);
        return;
      }
      const distance = haversineMeters(fix.lat, fix.lng, kindgarden.lat, kindgarden.lng);
      const maxRadius = kindgarden.geofence_radius ?? 200;
      setDebugInfo(`GPS: ${fix.lat.toFixed(6)}, ${fix.lng.toFixed(6)}\nBog'cha: ${kindgarden.lat}, ${kindgarden.lng}\nMasofa: ${distance}m / ruxsat: ${maxRadius}m\nisMock: ${isMock}`);

      if (distance > maxRadius) {
        setActionError(`Bog'chadan ${distance}m uzoqdasiz (ruxsat etilgan: ${maxRadius}m). Bog'chaga keling va qayta urinib ko'ring.`);
        return;
      }

      setProgress('4/6 Selfi olinmoqda...');
      const selfie = await captureSelfie();

      setProgress('5/6 Serverga yuborilmoqda...');
      const params: SubmitParams = {
        lat: fix.lat,
        lng: fix.lng,
        capturedAt: isoNowUtc(),
        isMock,
        photoUri: selfie.uri,
        photoFileName: selfie.fileName,
        photoMimeType: selfie.type,
      };

      try {
        if (kind === 'check_in') await postCheckIn(params);
        else if (kind === 'check_out') await postCheckOut(params);
        else if (kind === 'replace_check_in') await postReplace('check_in', params);
        else if (kind === 'replace_check_out') await postReplace('check_out', params);

        setProgress('6/6 Yangilanmoqda...');
        await refresh();
        setActionInfo(kind.includes('check_out') ? 'Ketish qabul qilindi ✅' : 'Kelish qabul qilindi ✅');
      } catch (apiErr: any) {
        const status = apiErr?.response?.status;
        const data = apiErr?.response?.data;
        if (data) {
          setActionError(mapServerError(data));
          setDebugInfo((prev) => `${prev ?? ''}\n\nAPI: HTTP ${status}\n${JSON.stringify(data).slice(0, 300)}`);
          return;
        }
        enqueue({
          kind,
          lat: params.lat,
          lng: params.lng,
          capturedAt: params.capturedAt,
          isMock: params.isMock,
          photoUri: params.photoUri,
        });
        setActionInfo('Internet yo\'q. Navbatga qo\'shildi, ulanish tiklanganda yuboriladi.');
        setDebugInfo((prev) => `${prev ?? ''}\n\nNetwork error: ${apiErr?.message ?? 'unknown'}`);
      }
    } catch (e: any) {
      setActionError(e?.message ?? 'Xato');
      setDebugInfo((prev) => `${prev ?? ''}\n\nException: ${e?.message ?? 'unknown'}\nStack: ${(e?.stack ?? '').slice(0, 200)}`);
    } finally {
      setProgress(null);
      setActionLoading(null);
    }
  }, [refresh, kindgarden]);

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
            {kindgarden.lat != null && ` · ${kindgarden.lat.toFixed(4)}, ${kindgarden.lng?.toFixed(4)}`}
          </Text>
        )}

        <ErrorBanner message={actionError ?? error} />
        {actionInfo && (
          <View style={styles.infoBox}>
            <Text style={styles.infoText}>{actionInfo}</Text>
          </View>
        )}
        {progress && (
          <View style={styles.progressBox}>
            <Text style={styles.progressText}>{progress}</Text>
          </View>
        )}
        {debugInfo && (
          <View style={styles.debugBox}>
            <Text style={styles.debugTitle}>Debug:</Text>
            <Text style={styles.debugText}>{debugInfo}</Text>
          </View>
        )}

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
  infoBox: {
    backgroundColor: '#E6F4EA',
    borderWidth: 1,
    borderColor: '#34A853',
    borderRadius: 8,
    padding: 12,
    marginBottom: 12,
  },
  infoText: { color: '#1E4620', fontWeight: '600' },
  progressBox: {
    backgroundColor: '#FFF7E6',
    borderWidth: 1,
    borderColor: '#F4B400',
    borderRadius: 8,
    padding: 10,
    marginBottom: 12,
  },
  progressText: { color: '#7A5C00', fontWeight: '600', fontSize: 13 },
  debugBox: {
    backgroundColor: '#F1F3F4',
    borderWidth: 1,
    borderColor: '#9AA0A6',
    borderRadius: 8,
    padding: 10,
    marginBottom: 12,
  },
  debugTitle: { fontSize: 11, fontWeight: '700', color: '#3C4043', marginBottom: 4 },
  debugText: { fontSize: 11, color: '#3C4043', fontFamily: 'monospace' },
  muted: { fontSize: 11, color: colors.textMuted },
  statusOk: { fontSize: 16, color: colors.success, marginTop: 6, fontWeight: '600' },
  statusWarn: { fontSize: 16, color: colors.warning, marginTop: 6, fontWeight: '600' },
});
