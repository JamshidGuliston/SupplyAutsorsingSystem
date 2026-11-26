import React from 'react';
import Svg, { Path } from 'react-native-svg';
import { IconProps } from './types';

export function SyncIcon({ size = 22, color, testID }: IconProps) {
  return (
    <Svg
      testID={testID}
      width={size}
      height={size}
      viewBox="0 0 24 24"
      fill="none"
      stroke={color}
      strokeWidth={2.1}
      strokeLinecap="round"
      strokeLinejoin="round"
    >
      <Path d="M4 12a8 8 0 0 1 14-5.3M20 12a8 8 0 0 1-14 5.3" />
      <Path d="M18 3v4h-4M6 21v-4h4" />
    </Svg>
  );
}
