import React, { useEffect, useState, useCallback } from 'react';
import { View, ScrollView, RefreshControl, StyleSheet } from 'react-native';
import { Screen } from '../components/Screen';
import { Section } from '../components/Section';
import { Divider } from '../components/Divider';
import { PageHeader } from '../components/PageHeader';
import { Button } from '../components/Button';
import { Banner } from '../components/Banner';
import { StatusLine } from '../components/StatusLine';
import { AppText } from '../components/AppText';
import { colors } from '../theme/colors';
import { spacing } from '../theme/spacing';
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

  const kgMeta = kindgarden
    ? `radius ${kindgarden.geofence_radius}m` +
      (kindgarden.lat != null
        ? ` · ${kindgarden.lat.toFixed(4)}, ${kindgarden.lng?.toFixed(4)}`
        : '')
    : null;

  return (
    <Screen>
      <ScrollView refreshControl={<RefreshControl refreshing={loading} onRefresh={refresh} />}>
        <PageHeader
          eyebrow={kindgarden ? `Bog'cha #${kindgarden.id}` : undefined}
          title="Davomat"
          meta={kgMeta}
        />
        <Divider />

        <Section>
          <Banner tone="error" message={actionError ?? error} />
          <Banner tone="success" message={actionInfo} />

          <StatusLine
            tone={checkedIn ? 'ok' : 'warn'}
            title={checkedIn ? 'Keldingiz — ' : 'Hali kelmadingiz'}
            accent={checkedIn ? today!.check_in_at!.slice(11, 16) : undefined}
            detail={
              checkedIn
                ? `Bog'cha markazidan ${today!.check_in_distance_m}m` +
                  (today!.check_in_is_late ? ' · kechikkan' : '')
                : null
            }
          />
        </Section>

        <Divider />

        <Section>
          <StatusLine
            tone={checkedOut ? 'ok' : checkedIn ? 'warn' : 'off'}
            title={checkedOut ? 'Ketdingiz — ' : 'Hali ketmadingiz'}
            accent={checkedOut ? today!.check_out_at!.slice(11, 16) : undefined}
          />
        </Section>

        <Section style={styles.actions}>
          {!checkedIn ? (
            <Button
              label="Keldim"
              loadingLabel={progress}
              onPress={() => onAction('check_in')}
              loading={actionLoading === 'check_in'}
            />
          ) : (
            <Button
              label="Kelishni qayta yuborish"
              loadingLabel={progress}
              variant="outline"
              onPress={() => onAction('replace_check_in')}
              loading={actionLoading === 'replace_check_in'}
            />
          )}

          <View style={styles.gap} />

          {checkedIn && !checkedOut ? (
            <Button
              label="Ketdim"
              loadingLabel={progress}
              onPress={() => onAction('check_out')}
              loading={actionLoading === 'check_out'}
            />
          ) : checkedOut ? (
            <Button
              label="Ketishni qayta yuborish"
              loadingLabel={progress}
              variant="outline"
              onPress={() => onAction('replace_check_out')}
              loading={actionLoading === 'replace_check_out'}
            />
          ) : (
            <Button label="Ketdim" onPress={() => {}} disabled />
          )}

          {!checkedIn && (
            <AppText variant="muted" color={colors.inkMuted} style={styles.hint}>
              Kelishni o'z vaqtida yuborolmadingizmi? Bog'chada turib yuqoridagi tugmani bosing —
              tizim avtomatik "kechikkan" deb belgilaydi.
            </AppText>
          )}
        </Section>

        {__DEV__ && debugInfo && (
          <Section style={styles.debug}>
            <AppText variant="caption" color={colors.inkMuted}>
              {debugInfo}
            </AppText>
          </Section>
        )}
      </ScrollView>
    </Screen>
  );
}

const styles = StyleSheet.create({
  actions: { paddingTop: spacing.sm },
  gap: { height: spacing.md },
  hint: { marginTop: spacing.lg },
  debug: { backgroundColor: colors.disabledBg },
});
