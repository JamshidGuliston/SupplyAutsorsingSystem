import React, { useState } from 'react';
import { View, ActivityIndicator, StyleSheet } from 'react-native';
import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query';
import { AppText } from './AppText';
import { Section } from './Section';
import { Divider } from './Divider';
import { PageHeader } from './PageHeader';
import { Field } from './Field';
import { Button } from './Button';
import { Banner } from './Banner';
import { colors } from '../theme/colors';
import { spacing } from '../theme/spacing';
import {
  getTodayState,
  submitChildrenCount,
  ChildrenCountState,
} from '../api/childrenCount';
import { mapServerError } from '../api/errors';

const QUERY_KEY = ['children-count', 'today'] as const;
const TASHKENT_OFFSET_MS = 5 * 60 * 60 * 1000;

export function ChildrenCountSection() {
  const qc = useQueryClient();
  const { data, isLoading, error: loadError, refetch } = useQuery<ChildrenCountState>({
    queryKey: QUERY_KEY,
    queryFn: getTodayState,
    staleTime: 30_000,
  });

  const [inputs, setInputs] = useState<Record<number, string>>({});
  const [submitError, setSubmitError] = useState<string | null>(null);

  const mutation = useMutation({
    mutationFn: (counts: Record<number, number>) => submitChildrenCount(counts),
    onSuccess: (newState) => {
      qc.setQueryData(QUERY_KEY, newState);
      setInputs({});
      setSubmitError(null);
    },
    onError: (err: any) => {
      const payload = err?.response?.data;
      setSubmitError(payload ? mapServerError(payload) : err?.message ?? 'Xato');
    },
  });

  if (isLoading) {
    return (
      <Section>
        <ActivityIndicator />
      </Section>
    );
  }

  if (loadError || !data) {
    return (
      <View>
        <PageHeader title="Bolalar soni" />
        <Section>
          <Banner tone="error" message={(loadError as any)?.message ?? 'Yuklab bo\'lmadi'} />
          <Button label="Qayta urinish" variant="outline" onPress={() => void refetch()} />
        </Section>
      </View>
    );
  }

  const today = new Date(Date.now() + TASHKENT_OFFSET_MS).toISOString().slice(0, 10);

  // Holat C: vaqt oynasi yopiq yoki texnolog menyu sozlamagan
  if (!data.time_window.allowed || !data.nextday_ready) {
    const reason = !data.nextday_ready
      ? 'Texnolog kelajak kun uchun menyu sozlamagan. Texnologga murojaat qiling.'
      : `Hozir yuborish vaqti emas (${data.time_window.from} - ${data.time_window.to} oralig'ida bo'lishi kerak).`;
    return (
      <View>
        <PageHeader
          eyebrow={data.kindgarden.kingar_name}
          title="Bolalar soni"
          meta={today}
        />
        <Divider />
        <Section>
          <Banner tone="info" message={reason} />
          <AppText variant="muted" color={colors.inkMuted}>
            Vaqt: {data.time_window.from} - {data.time_window.to} oralig'ida{'\n'}
            Hozir (Tashkent): {data.time_window.current_tashkent}
          </AppText>
        </Section>
      </View>
    );
  }

  // Holat B: bugun yuborilgan
  if (data.submitted) {
    const submittedTime = data.submitted_at
      ? new Date(new Date(data.submitted_at).getTime() + TASHKENT_OFFSET_MS)
          .toISOString()
          .slice(11, 16)
      : '';
    return (
      <View>
        <PageHeader
          eyebrow={data.kindgarden.kingar_name}
          title="Bolalar soni"
          meta={today}
        />
        <Divider />
        <Section>
          <Banner tone="success" message={`Bugungi son yuborildi · ${submittedTime}`} />
          {data.age_ranges.map((a) => (
            <AppText key={a.id} variant="body" style={styles.row}>
              {a.age_name}: {data.today_counts?.[a.id] ?? '—'}
            </AppText>
          ))}
        </Section>
        <Divider />
        <Section>
          <AppText variant="muted" color={colors.inkMuted}>
            O'zgartirish kerak bo'lsa, texnologga murojaat qiling.
          </AppText>
        </Section>
      </View>
    );
  }

  // Holat A: ochiq forma
  const onSubmit = () => {
    setSubmitError(null);
    const counts: Record<number, number> = {};
    for (const age of data.age_ranges) {
      const raw = inputs[age.id];
      const n = parseInt(raw ?? '', 10);
      if (Number.isNaN(n) || n < 0) {
        setSubmitError(`"${age.age_name}" uchun butun musbat son kiriting.`);
        return;
      }
      counts[age.id] = n;
    }
    mutation.mutate(counts);
  };

  return (
    <View>
      <PageHeader
        eyebrow={data.kindgarden.kingar_name}
        title="Bolalar soni"
        meta={`${today} · ${data.time_window.from} - ${data.time_window.to} oralig'ida`}
      />
      <Divider />
      <Section>
        <Banner tone="error" message={submitError} />
        {data.age_ranges.map((age) => (
          <Field
            key={age.id}
            label={age.age_name}
            value={inputs[age.id] ?? ''}
            onChangeText={(t) =>
              setInputs((p) => ({ ...p, [age.id]: t.replace(/[^0-9]/g, '') }))
            }
            keyboardType="number-pad"
            placeholder="0"
            editable={!mutation.isPending}
          />
        ))}
        <Button label="Yuborish" onPress={onSubmit} loading={mutation.isPending} />
      </Section>
    </View>
  );
}

const styles = StyleSheet.create({
  row: { marginTop: spacing.xs },
});
