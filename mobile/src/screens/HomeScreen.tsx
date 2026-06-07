import React from 'react';
import { ScrollView, RefreshControl, View } from 'react-native';
import { useQueryClient } from '@tanstack/react-query';
import { ScreenContainer } from '../components/ScreenContainer';
import { ChildrenCountCard } from '../components/ChildrenCountCard';

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
    <ScreenContainer>
      <ScrollView refreshControl={<RefreshControl refreshing={refreshing} onRefresh={onRefresh} />}>
        <View style={{ padding: 4 }}>
          <ChildrenCountCard />
        </View>
      </ScrollView>
    </ScreenContainer>
  );
}
