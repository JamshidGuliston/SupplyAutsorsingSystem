import React from 'react';
import Svg, { Path, Rect } from 'react-native-svg';
import { IconProps } from './types';

export function LockIcon({ size = 22, color, testID }: IconProps) {
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
      <Rect x={4} y={11} width={16} height={10} rx={3} />
      <Path d="M8 11V8a4 4 0 0 1 8 0v3" />
    </Svg>
  );
}
