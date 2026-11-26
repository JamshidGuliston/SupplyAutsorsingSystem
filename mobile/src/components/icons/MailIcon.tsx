import React from 'react';
import Svg, { Path, Rect } from 'react-native-svg';
import { IconProps } from './types';

export function MailIcon({ size = 22, color, testID }: IconProps) {
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
      <Rect x={3} y={5} width={18} height={14} rx={3} />
      <Path d="m4 7 8 6 8-6" />
    </Svg>
  );
}
