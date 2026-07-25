import React from 'react';
import { render } from '@testing-library/react-native';
import { HomeIcon } from '../../src/components/icons/HomeIcon';
import { PinIcon } from '../../src/components/icons/PinIcon';
import { BellIcon } from '../../src/components/icons/BellIcon';
import { UserIcon } from '../../src/components/icons/UserIcon';

describe('icons', () => {
  it('HomeIcon berilgan o\'lcham va rangni qo\'llaydi', () => {
    const { getByTestId } = render(<HomeIcon size={30} color="#EF5B3C" testID="i" />);
    const svg = getByTestId('i');
    expect(svg.props.width).toBe(30);
    expect(svg.props.height).toBe(30);
    expect(svg.props.stroke).toBe('#EF5B3C');
  });

  it('o\'lcham berilmasa 22 ni ishlatadi', () => {
    const { getByTestId } = render(<PinIcon color="#9A9CA2" testID="i" />);
    expect(getByTestId('i').props.width).toBe(22);
  });

  it('BellIcon va UserIcon rangni qo\'llaydi', () => {
    const bell = render(<BellIcon color="#0D0D0D" testID="b" />);
    expect(bell.getByTestId('b').props.stroke).toBe('#0D0D0D');
    const user = render(<UserIcon color="#0D0D0D" testID="u" />);
    expect(user.getByTestId('u').props.stroke).toBe('#0D0D0D');
  });
});
