import React from 'react';
import { View, StyleSheet } from 'react-native';
import { AppText } from '../AppText';
import { Card, CardRow } from '../Card';
import { IconCircle } from '../IconCircle';
import { CheckIcon } from '../icons/CheckIcon';
import type { ChildrenCountState } from '../../api/childrenCount';
import { submittedLabel } from '../../lib/uzTime';
import { sumFilled } from '../../lib/text';
import { colors } from '../../theme/colors';
import { spacing } from '../../theme/spacing';

// Yuborilgan holat (02, 2-telefon): belgi va vaqt, ostida yuborilgan sonlar va "Jami".
export function CountSubmitted({ data }: { data: ChildrenCountState }) {
  const when = submittedLabel(data.submitted_at, new Date());
  const counts = data.age_ranges.map((a) => {
    const n = data.today_counts?.[a.id];
    return n === undefined || n === null ? undefined : String(n);
  });
  const total = sumFilled(counts);

  return (
    <View>
      <Card style={styles.head}>
        <IconCircle tone="ok" size={64}>
          <CheckIcon size={32} color={colors.ok} />
        </IconCircle>
        <AppText variant="sectionTitle" style={styles.title}>
          Yuborildi
        </AppText>
        {when ? (
          <AppText variant="muted" color={colors.inkMuted} style={styles.when}>
            {when}
          </AppText>
        ) : null}
      </Card>

      <Card style={styles.list}>
        {data.age_ranges.map((a, i) => (
          <CardRow key={a.id}>
            <View style={styles.row}>
              <AppText variant="bodyStrong" style={styles.name}>
                {a.age_name}
              </AppText>
              <AppText variant="numberInput">{counts[i] ?? '—'}</AppText>
            </View>
          </CardRow>
        ))}
        <CardRow footer last>
          <View style={styles.row}>
            <AppText variant="bodyStrong" style={styles.name}>
              Jami
            </AppText>
            <AppText variant="numberInput">{total === null ? '—' : String(total)}</AppText>
          </View>
        </CardRow>
      </Card>

      <AppText variant="muted" color={colors.inkMuted} style={styles.hint}>
        O'zgartirish kerak bo'lsa, texnologga murojaat qiling.
      </AppText>
    </View>
  );
}

const styles = StyleSheet.create({
  head: { alignItems: 'center', paddingVertical: 20, paddingHorizontal: spacing.lg },
  title: { marginTop: 10, textAlign: 'center' },
  when: { marginTop: 2 },
  list: { marginTop: spacing.md },
  row: { flexDirection: 'row', alignItems: 'center', justifyContent: 'space-between' },
  name: { flex: 1, marginRight: spacing.md },
  hint: { marginTop: spacing.md, paddingHorizontal: 10, textAlign: 'center' },
});
