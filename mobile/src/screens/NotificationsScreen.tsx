import React from 'react';
import { View, StyleSheet } from 'react-native';
import { HeroScreen } from '../components/HeroScreen';
import { Hero } from '../components/Hero';
import { AppText } from '../components/AppText';
import { EmptyState } from '../components/EmptyState';
import { BellIcon } from '../components/icons/BellIcon';
import { colors } from '../theme/colors';

export function NotificationsScreen() {
  return (
    <HeroScreen
      hero={
        <Hero minHeight={120}>
          <AppText variant="heroTitle" color={colors.onHero}>
            Xabarlar
          </AppText>
        </Hero>
      }
    >
      <View style={styles.center}>
        <EmptyState
          icon={<BellIcon size={40} color={colors.brand} />}
          tone="brand"
          size={96}
          title="Hozircha xabar yo'q"
          body="Texnolog yoki tizimdan kelgan eslatmalar shu yerda ko'rinadi."
        />
      </View>
    </HeroScreen>
  );
}

const styles = StyleSheet.create({
  center: { flex: 1, justifyContent: 'center', paddingVertical: 32 },
});
