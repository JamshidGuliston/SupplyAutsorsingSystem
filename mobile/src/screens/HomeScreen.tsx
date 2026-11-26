import React from 'react';
import { RefreshControl } from 'react-native';
import { useQueryClient } from '@tanstack/react-query';
import { HeroScreen } from '../components/HeroScreen';
import { HomeHero } from '../components/childrenCount/HomeHero';
import { ChildrenCountSection } from '../components/ChildrenCountSection';
import { useChildrenCountToday, CHILDREN_COUNT_KEY } from '../api/childrenCount';
import { colors } from '../theme/colors';

export function HomeScreen() {
  const qc = useQueryClient();
  const { data, isLoading } = useChildrenCountToday();
  const [refreshing, setRefreshing] = React.useState(false);
  const onRefresh = React.useCallback(async () => {
    setRefreshing(true);
    try {
      await qc.invalidateQueries({ queryKey: CHILDREN_COUNT_KEY });
    } finally {
      setRefreshing(false);
    }
  }, [qc]);

  return (
    <HeroScreen
      hero={<HomeHero data={data} loading={isLoading} />}
      refreshControl={
        <RefreshControl refreshing={refreshing} onRefresh={onRefresh} colors={[colors.brand]} />
      }
      keyboardShouldPersistTaps="handled"
    >
      <ChildrenCountSection />
    </HeroScreen>
  );
}
