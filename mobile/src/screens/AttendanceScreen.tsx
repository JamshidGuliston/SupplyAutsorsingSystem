import React, { useEffect, useState, useCallback, useRef } from 'react';
import { View, RefreshControl, StyleSheet } from 'react-native';
import { HeroScreen } from '../components/HeroScreen';
import { Section } from '../components/Section';
import { Button, Progress } from '../components/Button';
import { Banner } from '../components/Banner';
import { Card } from '../components/Card';
import { Skeleton } from '../components/Skeleton';
import { AppText } from '../components/AppText';
import { CameraIcon } from '../components/icons/CameraIcon';
import { AttendanceHero } from '../components/attendance/AttendanceHero';
import { AttendanceTimeline } from '../components/attendance/AttendanceTimeline';
import { LoadFailed } from '../components/childrenCount/LoadFailed';
import { colors } from '../theme/colors';
import { useAttendanceStore } from '../attendance/attendanceStore';
import { ensureLocation, ensureCamera } from '../attendance/permissions';
import { getCurrentLocation } from '../attendance/locationService';
import { captureSelfie } from '../attendance/selfieCapture';
import { isMockGpsAllowed } from '../attendance/mockGps';
import { enqueue, queuedCaptureToday } from '../attendance/attendanceQueue';
import { postCheckIn, postCheckOut, postReplace, SubmitParams } from '../attendance/attendanceApi';
import { ActionKind, attendanceActions, buildTimeline } from '../attendance/timeline';
import { userFacingError } from '../api/errors';
import { isoNowUtc } from '../lib/tashkent';
import { haversineMeters } from '../lib/distance';

const STEPS = [
  'Ruxsatlar tekshirilmoqda',
  'Joylashuv aniqlanmoqda',
  'Masofa hisoblanmoqda',
  'Selfi olinmoqda',
  'Serverga yuborilmoqda',
  'Yangilanmoqda',
] as const;

const stepProgress = (step: number): Progress => ({
  step,
  total: STEPS.length,
  label: STEPS[step - 1],
});

export function AttendanceScreen() {
  const { today, kindgarden, loading, loaded, error, refresh } = useAttendanceStore();
  const [actionLoading, setActionLoading] = useState<ActionKind | null>(null);
  const [actionError, setActionError] = useState<string | null>(null);
  const [enqueued, setEnqueued] = useState(false);
  const [progress, setProgress] = useState<Progress | null>(null);
  const [debugInfo, setDebugInfo] = useState<string | null>(null);

  useEffect(() => { void refresh(); }, [refresh]);

  // Navbat MMKV dan sinxron o'qiladi: har render'da (jumladan onAction tugagandagi) yangi holat.
  const queued = {
    check_in: queuedCaptureToday('check_in'),
    check_out: queuedCaptureToday('check_out'),
  };
  const shownQueue = useRef(queued);
  useEffect(() => {
    shownQueue.current = queued;
  });

  // Flusher navbatni fonda yuboradi, lekin do'konni yangilamaydi. Ekranda ko'rsatilgan navbatdagi
  // yozuv yo'qolsa, bugungi holat qayta yuklanadi — aks holda "Yuborilmoqda…" qotib qoladi yoki
  // keyingi render'da "Keldim" qaytadi (ikkinchi bosish).
  useEffect(() => {
    const tick = setInterval(() => {
      const shown = shownQueue.current;
      const flushed =
        (shown.check_in !== null && queuedCaptureToday('check_in') === null) ||
        (shown.check_out !== null && queuedCaptureToday('check_out') === null);
      if (flushed) {
        shownQueue.current = { check_in: null, check_out: null };
        setEnqueued(false);
        void refresh();
      }
    }, 5_000);
    return () => clearInterval(tick);
  }, [refresh]);

  const onAction = useCallback(async (kind: ActionKind) => {
    setActionError(null);
    setEnqueued(false);
    setDebugInfo(null);
    setActionLoading(kind);
    try {
      setProgress(stepProgress(1));
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

      setProgress(stepProgress(2));
      const fix = await getCurrentLocation(15_000);
      const allowed = await isMockGpsAllowed();
      const isMock = fix.isMock || allowed;

      setProgress(stepProgress(3));
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

      setProgress(stepProgress(4));
      const selfie = await captureSelfie();

      setProgress(stepProgress(5));
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

        // Muvaffaqiyat banneri yo'q — natija vaqt chizig'ida yashil bo'lib ko'rinadi.
        setProgress(stepProgress(6));
        await refresh();
      } catch (apiErr: any) {
        const status = apiErr?.response?.status;
        const data = apiErr?.response?.data;
        if (data) {
          setActionError(userFacingError(apiErr));
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
        setEnqueued(true);
        setDebugInfo((prev) => `${prev ?? ''}\n\nNetwork error: ${apiErr?.message ?? 'unknown'}`);
      }
    } catch (e: any) {
      setActionError(userFacingError(e));
      setDebugInfo((prev) => `${prev ?? ''}\n\nException: ${e?.message ?? 'unknown'}\nStack: ${(e?.stack ?? '').slice(0, 200)}`);
    } finally {
      setProgress(null);
      setActionLoading(null);
    }
  }, [refresh, kindgarden]);

  const timeline = buildTimeline(today, queued);
  const { buttons, hint } = attendanceActions(timeline);
  const sideBySide = buttons.length > 1 && buttons.every((b) => b.size === 'sm');

  // Bugungi holat noma'lum bo'lsa (hali yuklanmagan yoki yuklab bo'lmagan) "Keldim" chizilmaydi —
  // aks holda kelgan oshpaz "Hali kelmadingiz" ni ko'rib, ikkinchi marta bosadi. Bir marta
  // yuklangandan keyingi yangilash xatosi esa faqat bannerda: vaqt chizig'i va tugmalar qoladi
  // (oflayn "Keldim" navbatga yoziladi).
  const loadFailed = !loaded && today === null && !!error;
  const unknown = today === null && (loading || !loaded);

  return (
    <HeroScreen
      hero={<AttendanceHero />}
      refreshControl={
        <RefreshControl refreshing={loading} onRefresh={refresh} colors={[colors.brand]} />
      }
    >
      <Banner tone="error" message={actionError ?? (loadFailed ? null : error)} />
      <Banner
        tone="warn"
        title="Navbatga qo'shildi."
        message={enqueued ? 'Internet tiklanganda avtomatik yuboriladi.' : null}
      />

      {loadFailed ? (
        <LoadFailed message={error} onRetry={() => void refresh()} />
      ) : unknown ? (
        <Card testID="attendance-skeleton" style={styles.skeleton}>
          <Skeleton width="60%" height={12} />
          <Skeleton width="80%" height={12} />
          <Skeleton width="45%" height={12} />
        </Card>
      ) : (
        <AttendanceTimeline timeline={timeline} />
      )}

      {actionLoading !== null ? (
        <View style={styles.actions}>
          <Button
            label={progress?.label ?? ''}
            onPress={() => {}}
            loading
            progress={progress}
            testID="action-progress"
          />
        </View>
      ) : !loadFailed && !unknown ? (
        <>
          {buttons.length > 0 ? (
            <View style={[styles.actions, sideBySide && styles.sideBySide]}>
              {buttons.map((b) => (
                <Button
                  key={b.kind}
                  label={b.label}
                  variant={b.variant}
                  size={b.size}
                  icon={b.icon === 'camera' ? CameraIcon : undefined}
                  onPress={() => onAction(b.kind)}
                  style={sideBySide ? styles.flex : undefined}
                />
              ))}
            </View>
          ) : null}
          {hint ? (
            <AppText variant="muted" color={colors.inkMuted} style={styles.hint}>
              {hint}
            </AppText>
          ) : null}
        </>
      ) : null}

      {__DEV__ && debugInfo && (
        <Section style={styles.debug}>
          <AppText variant="caption" color={colors.inkMuted}>
            {debugInfo}
          </AppText>
        </Section>
      )}
    </HeroScreen>
  );
}

const styles = StyleSheet.create({
  skeleton: { padding: 14, gap: 10 },
  actions: { marginTop: 14, gap: 10 },
  sideBySide: { flexDirection: 'row', gap: 8 },
  flex: { flex: 1 },
  hint: { marginTop: 10, paddingHorizontal: 8, textAlign: 'center' },
  debug: { backgroundColor: colors.disabledBg },
});
