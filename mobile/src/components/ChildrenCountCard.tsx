import React, { useState } from 'react';
import { View, Text, TextInput, StyleSheet, ActivityIndicator } from 'react-native';
import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query';
import { PrimaryButton } from './PrimaryButton';
import { ErrorBanner } from './ErrorBanner';
import { colors } from '../theme/colors';
import {
  getTodayState,
  submitChildrenCount,
  ChildrenCountState,
} from '../api/childrenCount';
import { mapServerError } from '../api/errors';

const QUERY_KEY = ['children-count', 'today'] as const;

export function ChildrenCountCard() {
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
      <View style={styles.card}>
        <ActivityIndicator />
      </View>
    );
  }

  if (loadError || !data) {
    return (
      <View style={styles.card}>
        <Text style={styles.h2}>Bolalar soni</Text>
        <ErrorBanner message={(loadError as any)?.message ?? 'Yuklab bo\'lmadi'} />
        <PrimaryButton label="Qayta urinish" onPress={() => void refetch()} />
      </View>
    );
  }

  const TASHKENT_OFFSET_MS = 5 * 60 * 60 * 1000;
  const today = new Date(Date.now() + TASHKENT_OFFSET_MS).toISOString().slice(0, 10);

  // State C: closed or nextday not ready
  if (!data.time_window.allowed || !data.nextday_ready) {
    const reason = !data.nextday_ready
      ? 'Texnolog kelajak kun uchun menyu sozlamagan. Texnologga murojaat qiling.'
      : `Hozir yuborish vaqti emas (${data.time_window.from} - ${data.time_window.to} oralig'ida bo'lishi kerak).`;
    return (
      <View style={styles.card}>
        <Text style={styles.h2}>Bolalar soni</Text>
        <Text style={styles.subtitle}>{data.kindgarden.kingar_name} — {today}</Text>
        <View style={styles.blockedBox}>
          <Text style={styles.blockedTitle}>⏰  Hozir yuborish mumkin emas</Text>
          <Text style={styles.blockedReason}>{reason}</Text>
          <Text style={styles.muted}>
            Vaqt: {data.time_window.from} - {data.time_window.to} oralig'ida{'\n'}
            Hozir (Tashkent): {data.time_window.current_tashkent}
          </Text>
        </View>
      </View>
    );
  }

  // State B: submitted
  if (data.submitted) {
    const submittedTime = data.submitted_at
      ? new Date(new Date(data.submitted_at).getTime() + TASHKENT_OFFSET_MS).toISOString().slice(11, 16)
      : '';
    return (
      <View style={styles.card}>
        <Text style={styles.h2}>Bolalar soni</Text>
        <Text style={styles.subtitle}>{data.kindgarden.kingar_name} — {today}</Text>
        <View style={styles.submittedBox}>
          <Text style={styles.submittedTitle}>✅  Bugungi son yuborildi  ·  {submittedTime}</Text>
          {data.age_ranges.map((a) => (
            <Text key={a.id} style={styles.submittedRow}>
              •  {a.age_name}: {data.today_counts?.[a.id] ?? '—'}
            </Text>
          ))}
        </View>
        <Text style={styles.contactNote}>
          O'zgartirish kerak bo'lsa, texnologga murojaat qiling.
        </Text>
      </View>
    );
  }

  // State A: open input form
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
    <View style={styles.card}>
      <Text style={styles.h2}>Bolalar soni</Text>
      <Text style={styles.subtitle}>{data.kindgarden.kingar_name} — {today}</Text>
      <ErrorBanner message={submitError} />
      {data.age_ranges.map((age) => (
        <View key={age.id} style={styles.row}>
          <Text style={styles.label}>{age.age_name}</Text>
          <TextInput
            style={styles.input}
            value={inputs[age.id] ?? ''}
            onChangeText={(t) => setInputs((p) => ({ ...p, [age.id]: t.replace(/[^0-9]/g, '') }))}
            keyboardType="number-pad"
            placeholder="0"
            editable={!mutation.isPending}
          />
        </View>
      ))}
      <PrimaryButton
        label="✅  Yuborish"
        variant="success"
        onPress={onSubmit}
        loading={mutation.isPending}
      />
      <Text style={styles.muted}>
        Vaqt: {data.time_window.from} - {data.time_window.to}
      </Text>
    </View>
  );
}

const styles = StyleSheet.create({
  card: {
    backgroundColor: '#fff',
    borderWidth: 1,
    borderColor: colors.border,
    borderRadius: 10,
    padding: 16,
    marginBottom: 16,
  },
  h2: { fontSize: 18, fontWeight: '700', color: colors.textPrimary },
  subtitle: { fontSize: 12, color: colors.textMuted, marginBottom: 12 },
  row: { marginBottom: 10 },
  label: { fontSize: 14, color: colors.textPrimary, marginBottom: 4 },
  input: {
    borderWidth: 1,
    borderColor: colors.border,
    borderRadius: 6,
    paddingHorizontal: 12,
    paddingVertical: 10,
    fontSize: 16,
    backgroundColor: '#fff',
  },
  muted: { fontSize: 11, color: colors.textMuted, marginTop: 10 },
  submittedBox: {
    backgroundColor: '#E6F4EA',
    borderWidth: 1,
    borderColor: '#34A853',
    borderRadius: 8,
    padding: 12,
    marginBottom: 12,
  },
  submittedTitle: { color: '#1E4620', fontWeight: '700', marginBottom: 6 },
  submittedRow: { color: '#1E4620', fontSize: 14, marginTop: 2 },
  contactNote: {
    fontSize: 13,
    color: colors.textMuted,
    borderTopWidth: 1,
    borderTopColor: colors.border,
    paddingTop: 10,
  },
  blockedBox: {
    backgroundColor: '#FFF7E6',
    borderWidth: 1,
    borderColor: '#F4B400',
    borderRadius: 8,
    padding: 12,
  },
  blockedTitle: { color: '#7A5C00', fontWeight: '700', marginBottom: 6 },
  blockedReason: { color: '#7A5C00', marginBottom: 8 },
});
