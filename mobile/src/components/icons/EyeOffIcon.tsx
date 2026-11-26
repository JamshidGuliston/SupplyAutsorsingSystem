import React from 'react';
import Svg, { Path, Circle } from 'react-native-svg';
import { IconProps } from './types';

export function EyeOffIcon({ size = 22, color, testID }: IconProps) {
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
      <Path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12Z" />
      <Circle cx={12} cy={12} r={3} />
      <Path d="M3 3l18 18" />
    </Svg>
  );
}
