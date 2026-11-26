import React from 'react';
import { View, StyleSheet } from 'react-native';
import { AppText } from '../AppText';
import { Pill } from '../Pill';
import { Card, CardRow } from '../Card';
import { Field } from '../Field';
import { Banner } from '../Banner';
import { Button } from '../Button';
import type { AgeRange, TimeWindow } from '../../api/childrenCount';
import { sumFilled } from '../../lib/text';
import { colors } from '../../theme/colors';
import { spacing } from '../../theme/spacing';

// Ochiq forma (02, 1-telefon). Holat va validatsiya ChildrenCountSection da — bu faqat chizadi.
export function CountForm({
  ageRanges,
  window,
  inputs,
  onChangeInput,
  error,
  pending,
  onSubmit,
}: {
  ageRanges: AgeRange[];
  window: TimeWindow;
  inputs: Record<number, string>;
  onChangeInput: (ageId: number, text: string) => void;
  error: string | null;
  pending: boolean;
  onSubmit: () => void;
}) {
  const total = sumFilled(ageRanges.map((a) => inputs[a.id]));

  return (
    <View>
      <View style={styles.header}>
        <View style={styles.headerText}>
          <AppText variant="sectionTitle">Bolalar soni</AppText>
          <AppText variant="muted" color={colors.inkMuted} style={styles.sub}>
            Ertangi menyu uchun
          </AppText>
        </View>
        {/* Pill o'zi alignSelf:'flex-start' — o'rovchi View uni qatorda vertikal markazlaydi. */}
        <View>
          <Pill tone="ok" dot label={`${window.from}–${window.to} ochiq`} />
        </View>
      </View>

      <Card style={styles.card}>
        {ageRanges.map((age, i) => (
          <CardRow key={age.id} last={i === ageRanges.length - 1}>
            <View style={styles.row}>
              <AppText variant="bodyStrong" style={styles.name}>
                {age.age_name}
              </AppText>
              <Field
                variant="number"
                accessibilityLabel={age.age_name}
                value={inputs[age.id] ?? ''}
                onChangeText={(t) => onChangeInput(age.id, t)}
                editable={!pending}
              />
            </View>
          </CardRow>
        ))}
      </Card>

      {error ? (
        <View style={styles.banner}>
          <Banner tone="error" message={error} />
        </View>
      ) : null}

      <View style={styles.total}>
        <AppText variant="muted" color={colors.inkMuted}>
          Jami
        </AppText>
        <AppText variant="total">{total === null ? '—' : `${total} bola`}</AppText>
      </View>

      <Button label="Yuborish" onPress={onSubmit} loading={pending} style={styles.submit} />
    </View>
  );
}

const styles = StyleSheet.create({
  header: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-between',
    paddingHorizontal: spacing.xs,
  },
  headerText: { flexShrink: 1, marginRight: spacing.sm },
  sub: { marginTop: 2 },
  card: { marginTop: spacing.md },
  row: { flexDirection: 'row', alignItems: 'center', justifyContent: 'space-between' },
  name: { flex: 1, marginRight: spacing.md },
  banner: { marginTop: spacing.md },
  total: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-between',
    paddingTop: spacing.md,
    paddingHorizontal: spacing.lg,
  },
  submit: { marginTop: spacing.md },
});
