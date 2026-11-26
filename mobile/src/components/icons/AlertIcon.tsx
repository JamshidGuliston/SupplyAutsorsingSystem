import React from 'react';
import Svg, { Path } from 'react-native-svg';
import { IconProps } from './types';

export function AlertIcon({ size = 22, color, testID }: IconProps) {
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
      <Path d="M12 7v6M12 17h.01" />
    </Svg>
  );
}
