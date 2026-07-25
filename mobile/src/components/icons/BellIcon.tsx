import React from 'react';
import Svg, { Path } from 'react-native-svg';
import { IconProps } from './types';

export function BellIcon({ size = 22, color, testID }: IconProps) {
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
      <Path d="M18 8.7a6 6 0 1 0-12 0c0 6.4-2.5 8.3-2.5 8.3h17S18 15.1 18 8.7" />
      <Path d="M13.7 20.5a2 2 0 0 1-3.4 0" />
    </Svg>
  );
}
