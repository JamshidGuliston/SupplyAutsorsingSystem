import React from 'react';
import { ScrollView, RefreshControl, View, Image, StyleSheet } from 'react-native';
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
        <View style={styles.header}>
          <Image
            source={require('../assets/global_tam_logo.jpg')}
            style={styles.logo}
            resizeMode="contain"
          />
        </View>
        <View style={{ padding: 4 }}>
          <ChildrenCountCard />
        </View>
      </ScrollView>
    </ScreenContainer>
  );
}

const styles = StyleSheet.create({
  header: { alignItems: 'center', paddingVertical: 12 },
  logo: { width: 160, height: 68 },
});
