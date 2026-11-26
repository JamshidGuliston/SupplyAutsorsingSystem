import React from 'react';
import { View, StyleSheet } from 'react-native';
import { AppText } from '../AppText';
import { Card } from '../Card';
import { IconCircle } from '../IconCircle';
import { Pill } from '../Pill';
import { ClockIcon } from '../icons/ClockIcon';
import type { TimeWindow } from '../../api/childrenCount';
import { nextOpeningLabel } from '../../lib/uzTime';
import { colors } from '../../theme/colors';
import { spacing } from '../../theme/spacing';

export type CountNoticeKind = 'closed' | 'not_ready' | 'no_groups';

const TEXTS: Record<Exclude<CountNoticeKind, 'closed'>, { title: string; body: string }> = {
  not_ready: {
    title: 'Menyu hali tayyor emas',
    body: 'Texnolog kelajak kun uchun menyu sozlamagan. Texnologga murojaat qiling.',
  },
  no_groups: {
    title: 'Yosh guruhlari biriktirilmagan',
    body: "Bog'changizga yosh guruhlari biriktirilmagan. Texnologga murojaat qiling.",
  },
};

// Forma ochilmaydigan holatlar (02, 3-telefon): sariq soat doirasi, sarlavha va izoh.
// `window` faqat `closed` da ishlatiladi.
export function CountNotice({ kind, window }: { kind: CountNoticeKind; window?: TimeWindow }) {
  const text = kind === 'closed' ? null : TEXTS[kind];

  return (
    <View>
      <Card style={styles.card}>
        <IconCircle tone="warn" size={64}>
          <ClockIcon size={30} color={colors.warn} />
        </IconCircle>
        <AppText variant="sectionTitle" style={styles.title}>
          {text ? text.title : 'Hozir yuborish vaqti emas'}
        </AppText>
        {text ? (
          <AppText variant="muted" color={colors.inkMuted} style={styles.body}>
            {text.body}
          </AppText>
        ) : window ? (
          <ClosedDetails window={window} />
        ) : null}
      </Card>
      <AppText variant="caption" color={colors.inkFaint} style={styles.pull}>
        Pastga torting — yangilash
      </AppText>
    </View>
  );
}

function ClosedDetails({ window }: { window: TimeWindow }) {
  const next = nextOpeningLabel(window);
  return (
    <>
      <AppText variant="muted" color={colors.inkMuted} style={styles.body}>
        {'Bolalar soni har kuni '}
        <AppText variant="label">{`${window.from} – ${window.to}`}</AppText>
        {" oralig'ida yuboriladi"}
      </AppText>
      <View style={styles.pills}>
        <Pill tone="neutral" label={`Hozir: ${window.current_tashkent}`} />
        {next ? <Pill tone="neutral" label={next} /> : null}
      </View>
    </>
  );
}

const styles = StyleSheet.create({
  card: { alignItems: 'center', paddingVertical: 22, paddingHorizontal: spacing.lg },
  title: { marginTop: 10, textAlign: 'center' },
  body: { marginTop: spacing.xs, textAlign: 'center' },
  pills: {
    flexDirection: 'row',
    flexWrap: 'wrap',
    justifyContent: 'center',
    gap: spacing.sm,
    marginTop: 14,
  },
  pull: { marginTop: 14, textAlign: 'center' },
});
