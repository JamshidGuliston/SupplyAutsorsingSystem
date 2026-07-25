import React from 'react';
import { Text, TextProps, StyleProp, TextStyle } from 'react-native';
import { typography, TextVariant } from '../theme/typography';
import { colors } from '../theme/colors';

type Props = TextProps & {
  variant?: TextVariant;
  color?: string;
  style?: StyleProp<TextStyle>;
};

export function AppText({ variant = 'body', color = colors.ink, style, ...rest }: Props) {
  const base = typography[variant] ?? typography.body;
  return <Text {...rest} style={[base, { color }, style]} />;
}
