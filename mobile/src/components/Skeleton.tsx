import React from 'react';
import { View, DimensionValue } from 'react-native';
import { colors } from '../theme/colors';

export function Skeleton({
  width,
  height,
  onHero,
  testID,
}: {
  width: DimensionValue;
  height: number;
  onHero?: boolean;
  testID?: string;
}) {
  return (
    <View
      testID={testID}
      style={{
        width,
        height,
        borderRadius: 8,
        backgroundColor: onHero ? colors.heroSkeleton : colors.skeleton,
      }}
    />
  );
}
