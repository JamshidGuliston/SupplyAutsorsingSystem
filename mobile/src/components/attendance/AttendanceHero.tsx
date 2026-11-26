import React from 'react';
import { View, StyleSheet } from 'react-native';
import { Hero } from '../Hero';
import { AppText } from '../AppText';
import { Pill } from '../Pill';
import { PinIcon } from '../icons/PinIcon';
import { useAttendanceStore } from '../../attendance/attendanceStore';
import { useChildrenCountToday } from '../../api/childrenCount';
import { uzDateLabel } from '../../lib/uzTime';
import { colors } from '../../theme/colors';

// Davomat hero'si: sana, sarlavha, bog'cha nomi va radius. Davomat API'sida bog'cha nomi yo'q —
// u bolalar soni so'rovidan olinadi; o'sha so'rovning xatosi bu yerda ko'rsatilmaydi.
export function AttendanceHero() {
  const kindgarden = useAttendanceStore((s) => s.kindgarden);
  const { data } = useChildrenCountToday();

  return (
    <Hero>
      <AppText variant="heroSub" color={colors.onHeroMuted} style={styles.sub}>
        {uzDateLabel(new Date())}
      </AppText>
      <AppText variant="heroTitle" color={colors.onHero} style={styles.title}>
        Davomat
      </AppText>
      {kindgarden ? (
        <View style={styles.pills}>
          <Pill
            tone="onHero"
            icon={<PinIcon size={12} color={colors.onHero} />}
            label={data?.kindgarden.kingar_name || `Bog'cha #${kindgarden.id}`}
          />
          <Pill tone="onHero" label={`${kindgarden.geofence_radius} m radius`} />
        </View>
      ) : null}
    </Hero>
  );
}

const styles = StyleSheet.create({
  sub: { marginTop: 4 },
  title: { marginTop: 2 },
  pills: { flexDirection: 'row', flexWrap: 'wrap', gap: 6, marginTop: 10 },
});
