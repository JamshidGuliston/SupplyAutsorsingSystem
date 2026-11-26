import React, { useState } from 'react';
import { StyleSheet } from 'react-native';
import { useMutation, useQueryClient } from '@tanstack/react-query';
import { Card } from './Card';
import { Skeleton } from './Skeleton';
import { CountForm } from './childrenCount/CountForm';
import { CountSubmitted } from './childrenCount/CountSubmitted';
import { CountNotice } from './childrenCount/CountNotice';
import { LoadFailed } from './childrenCount/LoadFailed';
import {
  submitChildrenCount,
  useChildrenCountToday,
  CHILDREN_COUNT_KEY,
} from '../api/childrenCount';
import { userFacingError, errorCode } from '../api/errors';
import { childrenCountView } from '../lib/childrenCountView';

export function ChildrenCountSection() {
  const qc = useQueryClient();
  const { data, isLoading, error: loadError, refetch } = useChildrenCountToday();

  const [inputs, setInputs] = useState<Record<number, string>>({});
  const [submitError, setSubmitError] = useState<string | null>(null);

  const mutation = useMutation({
    mutationFn: (counts: Record<number, number>) => submitChildrenCount(counts),
    onSuccess: (newState) => {
      qc.setQueryData(CHILDREN_COUNT_KEY, newState);
      setInputs({});
      setSubmitError(null);
    },
    // Server payload bo'lsa userFacingError mapServerError ga beradi (matnlar o'zgarmaydi);
    // tarmoq / bo'sh javobda xom "Network Error" o'rniga tushunarli matn.
    onError: (err) => setSubmitError(userFacingError(err)),
  });

  if (isLoading) {
    return (
      <Card testID="count-skeleton" style={styles.skeleton}>
        <Skeleton width="60%" height={12} />
        <Skeleton width="80%" height={12} />
        <Skeleton width="45%" height={12} />
      </Card>
    );
  }

  if (loadError || !data) {
    return (
      <LoadFailed
        message={userFacingError(loadError)}
        onRetry={
          errorCode(loadError) !== 'kindgarden_not_assigned' ? () => void refetch() : undefined
        }
      />
    );
  }

  const view = childrenCountView(data);

  if (view === 'submitted') {
    return <CountSubmitted data={data} />;
  }

  if (view !== 'open') {
    return <CountNotice kind={view} window={data.time_window} />;
  }

  // Validatsiya o'zgarmagan: barcha maydonlar to'ldirilishi shart, bo'sh maydon 0 deb yuborilmaydi.
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
    <CountForm
      ageRanges={data.age_ranges}
      window={data.time_window}
      inputs={inputs}
      onChangeInput={(id, t) => setInputs((p) => ({ ...p, [id]: t.replace(/[^0-9]/g, '') }))}
      error={submitError}
      pending={mutation.isPending}
      onSubmit={onSubmit}
    />
  );
}

const styles = StyleSheet.create({
  skeleton: { padding: 14, gap: 10 },
});
