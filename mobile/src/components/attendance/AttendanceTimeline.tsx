import React from 'react';
import { View, StyleSheet } from 'react-native';
import { AppText } from '../AppText';
import { Card, CardRow } from '../Card';
import { IconCircle, IconCircleTone } from '../IconCircle';
import { Pill } from '../Pill';
import { ClockIcon } from '../icons/ClockIcon';
import { CheckIcon } from '../icons/CheckIcon';
import { SyncIcon } from '../icons/SyncIcon';
import { LockIcon } from '../icons/LockIcon';
import type { StepState, Timeline } from '../../attendance/timeline';
import { colors } from '../../theme/colors';

type Which = 'checkIn' | 'checkOut';

type StepView = {
  tone: IconCircleTone;
  icon: React.ReactNode;
  label: string;
  value: string;
  valueColor?: string;
  late?: boolean;
  detail?: string | null;
};

const DOT = 34;

function stepView(which: Which, s: StepState): StepView {
  const isIn = which === 'checkIn';
  switch (s.kind) {
    case 'done':
      return {
        tone: 'ok',
        icon: <CheckIcon size={18} color={colors.ok} />,
        label: isIn ? 'Keldingiz' : 'Ketdingiz',
        value: s.timeHHMM,
        late: s.late,
        detail: s.distanceM === null ? null : `Bog'cha markazidan ${s.distanceM} m`,
      };
    case 'queued':
      return {
        tone: 'warn',
        icon: <SyncIcon size={18} color={colors.warn} />,
        label: isIn ? 'Kelish' : 'Ketish',
        value: 'Yuborilmoqda…',
        detail: `${s.capturedHHMM} da olindi · navbatda`,
      };
    case 'locked':
      return {
        tone: 'locked',
        icon: <LockIcon size={16} color={colors.lockedInk} />,
        label: isIn ? 'Kelish' : 'Ketish',
        value: '—:—',
        valueColor: colors.lockedInk,
      };
    case 'pending':
      return {
        tone: 'warn',
        icon: <ClockIcon size={18} color={colors.warn} />,
        label: isIn ? 'Kelish' : 'Ketish',
        value: isIn ? 'Hali kelmadingiz' : 'Hali ketmadingiz',
        detail: isIn ? 'Bog\'chaga yetib kelgach "Keldim" ni bosing' : null,
      };
  }
}

function Step({
  which,
  state,
  connector,
  divided,
}: {
  which: Which;
  state: StepState;
  connector?: string;
  divided?: boolean;
}) {
  const v = stepView(which, state);
  return (
    <View style={[styles.step, divided && styles.divided]}>
      {connector ? (
        <View testID="timeline-line" style={[styles.line, { backgroundColor: connector }]} />
      ) : null}
      <IconCircle tone={v.tone} size={DOT}>
        {v.icon}
      </IconCircle>
      <View style={styles.text}>
        <AppText variant="stepLabel" color={colors.inkMuted}>
          {v.label}
        </AppText>
        <View style={styles.valueRow}>
          <AppText variant="timeValue" color={v.valueColor ?? colors.ink}>
            {v.value}
          </AppText>
          {v.late ? <Pill tone="warn" label="kechikkan" /> : null}
        </View>
        {v.detail ? (
          <AppText variant="muted" color={colors.inkMuted} style={styles.detail}>
            {v.detail}
          </AppText>
        ) : null}
      </View>
    </View>
  );
}

// Davomat vaqt chizig'i (03, 06 1-telefon): kelish va ketish bitta kartada, orasida bog'lovchi;
// ikkalasi bo'lsa (va qayta yuborilmagan bo'lsa) pastda "Bugungi ish vaqti".
export function AttendanceTimeline({ timeline }: { timeline: Timeline }) {
  const { checkIn, checkOut, workDuration } = timeline;
  return (
    <Card>
      <Step
        which="checkIn"
        state={checkIn}
        connector={checkIn.kind === 'done' ? colors.lineDone : colors.linePending}
      />
      <Step which="checkOut" state={checkOut} divided={!!workDuration} />
      {workDuration ? (
        <CardRow footer last>
          <View style={styles.footer}>
            <AppText variant="label" color={colors.inkSoft}>
              Bugungi ish vaqti
            </AppText>
            <AppText variant="bodyStrong">{workDuration}</AppText>
          </View>
        </CardRow>
      ) : null}
    </Card>
  );
}

const STEP_PAD_V = 14;
const STEP_PAD_H = 16;

const styles = StyleSheet.create({
  step: {
    flexDirection: 'row',
    gap: 12,
    paddingVertical: STEP_PAD_V,
    paddingHorizontal: STEP_PAD_H,
  },
  divided: { borderBottomWidth: 1, borderBottomColor: colors.cardDivider },
  // Birinchi doira ostidan ikkinchi doira tepasigacha: keyingi qadamning yuqori chekinishiga cho'ziladi.
  line: {
    position: 'absolute',
    width: 2,
    left: STEP_PAD_H + DOT / 2 - 1,
    top: STEP_PAD_V + DOT + 2,
    bottom: -STEP_PAD_V,
  },
  text: { flex: 1 },
  valueRow: { flexDirection: 'row', alignItems: 'center', gap: 8, marginTop: 1 },
  detail: { marginTop: 2 },
  footer: { flexDirection: 'row', alignItems: 'center', justifyContent: 'space-between' },
});
