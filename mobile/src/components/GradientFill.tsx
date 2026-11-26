import React from 'react';
import { View, StyleSheet } from 'react-native';
import Svg, { Defs, LinearGradient, Rect, Stop } from 'react-native-svg';
import { colors } from '../theme/colors';

const [FROM, MID, TO] = colors.brandGradient;

// Ota-ona View ni to'liq qoplaydigan marjon gradient (chap-yuqoridan o'ng-pastga).
export function GradientFill() {
  return (
    <View pointerEvents="none" style={StyleSheet.absoluteFill}>
      <Svg width="100%" height="100%">
        <Defs>
          <LinearGradient
            id="brandGradient"
            x1="0"
            y1="0"
            x2="1"
            y2="1"
            gradientUnits="objectBoundingBox"
          >
            <Stop offset="0" stopColor={FROM} />
            <Stop offset="0.45" stopColor={MID} />
            <Stop offset="1" stopColor={TO} />
          </LinearGradient>
        </Defs>
        <Rect x="0" y="0" width="100%" height="100%" fill="url(#brandGradient)" />
      </Svg>
    </View>
  );
}
