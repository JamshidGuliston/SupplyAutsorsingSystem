import React from 'react';
import { View, StyleSheet } from 'react-native';
import { Hero } from '../Hero';
import { AppText } from '../AppText';
import { Pill } from '../Pill';
import { Skeleton } from '../Skeleton';
import { CalendarIcon } from '../icons/CalendarIcon';
import { useAuthStore } from '../../auth/authStore';
import type { ChildrenCountState } from '../../api/childrenCount';
import { greeting, uzDateLabel } from '../../lib/uzTime';
import { displayName } from '../../lib/text';
import { colors } from '../../theme/colors';

// Bosh sahifa hero'si — barcha holatlarda bir xil: salomlashuv, bog'cha nomi, sana.
export function HomeHero({ data, loading }: { data?: ChildrenCountState; loading: boolean }) {
  const user = useAuthStore((s) => s.user);
  const now = new Date();
  const name = displayName(user?.name);
  const hello = name ? `${greeting(now)}, ${name}` : `${greeting(now)}!`;

  return (
    <Hero>
      <AppText variant="heroSub" color={colors.onHeroMuted} numberOfLines={1} style={styles.hello}>
        {hello}
      </AppText>
      {data ? (
        <AppText variant="heroTitle" color={colors.onHero} numberOfLines={2} style={styles.title}>
          {data.kindgarden.kingar_name}
        </AppText>
      ) : loading ? (
        <View style={styles.skeleton}>
          <Skeleton onHero width={150} height={20} testID="home-hero-skeleton" />
        </View>
      ) : null}
      <View style={styles.date}>
        <Pill
          tone="onHero"
          icon={<CalendarIcon size={12} color={colors.onHero} />}
          label={uzDateLabel(now)}
        />
      </View>
    </Hero>
  );
}

const styles = StyleSheet.create({
  hello: { marginTop: 4 },
  title: { marginTop: 2 },
  // Sarlavha qatori balandligiga (2 + 26) teng — nom kelganda joy sakramaydi.
  skeleton: { marginTop: 5, marginBottom: 3 },
  date: { marginTop: 10 },
});
