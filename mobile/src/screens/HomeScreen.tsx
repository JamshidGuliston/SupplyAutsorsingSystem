import React from 'react';
import { ScrollView, RefreshControl } from 'react-native';
import { useQueryClient } from '@tanstack/react-query';
import { Screen } from '../components/Screen';
import { ChildrenCountSection } from '../components/ChildrenCountSection';

export function HomeScreen() {
  const qc = useQueryClient();
  const [refreshing, setRefreshing] = React.useState(false);
  const onRefresh = React.useCallback(async () => {
    setRefreshing(true);
    try {
      await qc.invalidateQueries({ queryKey: ['children-count', 'today'] });
    } finally {
      setRefreshing(false);
    }
  }, [qc]);

  return (
    <Screen>
      <ScrollView
        refreshControl={<RefreshControl refreshing={refreshing} onRefresh={onRefresh} />}
      >
        <ChildrenCountSection />
      </ScrollView>
    </Screen>
  );
}
