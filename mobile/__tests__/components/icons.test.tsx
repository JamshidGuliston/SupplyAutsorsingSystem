import React from 'react';
import { render } from '@testing-library/react-native';
import { Path, Circle } from 'react-native-svg';
import { HomeIcon } from '../../src/components/icons/HomeIcon';
import { PinIcon } from '../../src/components/icons/PinIcon';
import { BellIcon } from '../../src/components/icons/BellIcon';
import { UserIcon } from '../../src/components/icons/UserIcon';
import { MailIcon } from '../../src/components/icons/MailIcon';
import { LockIcon } from '../../src/components/icons/LockIcon';
import { EyeIcon } from '../../src/components/icons/EyeIcon';
import { EyeOffIcon } from '../../src/components/icons/EyeOffIcon';
import { CalendarIcon } from '../../src/components/icons/CalendarIcon';
import { CameraIcon } from '../../src/components/icons/CameraIcon';
import { ClockIcon } from '../../src/components/icons/ClockIcon';
import { CheckIcon } from '../../src/components/icons/CheckIcon';
import { LogoutIcon } from '../../src/components/icons/LogoutIcon';
import { SyncIcon } from '../../src/components/icons/SyncIcon';
import { WifiOffIcon } from '../../src/components/icons/WifiOffIcon';
import { AlertIcon } from '../../src/components/icons/AlertIcon';

describe('icons', () => {
  it('HomeIcon berilgan o\'lcham va rangni qo\'llaydi, o\'ziga xos yo\'lni chizadi', () => {
    const { getByTestId, UNSAFE_getAllByType } = render(
      <HomeIcon size={30} color="#EF5B3C" testID="i" />,
    );
    const svg = getByTestId('i');
    expect(svg.props.width).toBe(30);
    expect(svg.props.height).toBe(30);
    expect(svg.props.stroke).toBe('#EF5B3C');

    const paths = UNSAFE_getAllByType(Path);
    expect(paths).toHaveLength(1);
    expect(paths[0].props.d).toBe(
      'M3 10.2 12 3.4l9 6.8V20a1 1 0 0 1-1 1h-5v-6H9v6H4a1 1 0 0 1-1-1z',
    );
  });

  it('PinIcon o\'lcham berilmasa 22 ni ishlatadi, o\'ziga xos yo\'l va doirani chizadi', () => {
    const { getByTestId, UNSAFE_getAllByType } = render(
      <PinIcon color="#9A9CA2" testID="i" />,
    );
    expect(getByTestId('i').props.width).toBe(22);

    const paths = UNSAFE_getAllByType(Path);
    const circles = UNSAFE_getAllByType(Circle);
    expect(paths).toHaveLength(1);
    expect(paths[0].props.d).toBe('M20 10.5c0 6-8 12-8 12s-8-6-8-12a8 8 0 1 1 16 0Z');
    expect(circles).toHaveLength(1);
    expect(circles[0].props.cx).toBe(12);
    expect(circles[0].props.cy).toBe(10.3);
    expect(circles[0].props.r).toBe(2.8);
  });

  it('BellIcon rangni qo\'llaydi va o\'ziga xos ikkita yo\'lni chizadi', () => {
    const { getByTestId, UNSAFE_getAllByType } = render(
      <BellIcon color="#0D0D0D" testID="b" />,
    );
    expect(getByTestId('b').props.stroke).toBe('#0D0D0D');

    const paths = UNSAFE_getAllByType(Path);
    expect(paths).toHaveLength(2);
    expect(paths[0].props.d).toBe(
      'M18 8.7a6 6 0 1 0-12 0c0 6.4-2.5 8.3-2.5 8.3h17S18 15.1 18 8.7',
    );
    expect(paths[1].props.d).toBe('M13.7 20.5a2 2 0 0 1-3.4 0');
  });

  it('UserIcon rangni qo\'llaydi va o\'ziga xos doira hamda yo\'lni chizadi', () => {
    const { getByTestId, UNSAFE_getAllByType } = render(
      <UserIcon color="#0D0D0D" testID="u" />,
    );
    expect(getByTestId('u').props.stroke).toBe('#0D0D0D');

    const circles = UNSAFE_getAllByType(Circle);
    const paths = UNSAFE_getAllByType(Path);
    expect(circles).toHaveLength(1);
    expect(circles[0].props.cx).toBe(12);
    expect(circles[0].props.cy).toBe(8);
    expect(circles[0].props.r).toBe(3.7);
    expect(paths).toHaveLength(1);
    expect(paths[0].props.d).toBe('M4.5 20.5a7.5 7.5 0 0 1 15 0');
  });

  it("barcha 16 ikonka umumiy chiziq uslubini (fill, strokeWidth, viewBox, burchaklar) qo'llaydi", () => {
    const all = [
      HomeIcon, PinIcon, BellIcon, UserIcon, MailIcon, LockIcon, EyeIcon, EyeOffIcon,
      CalendarIcon, CameraIcon, ClockIcon, CheckIcon, LogoutIcon, SyncIcon, WifiOffIcon, AlertIcon,
    ];
    expect(all).toHaveLength(16);
    all.forEach((Icon) => {
      const svg = render(<Icon color="#000000" testID="x" />).getByTestId('x');
      // react-native-svg parses viewBox="0 0 24 24" into these four numeric
      // props on the rendered native view; `viewBox` itself is not forwarded.
      expect(svg.props.minX).toBe(0);
      expect(svg.props.minY).toBe(0);
      expect(svg.props.vbWidth).toBe(24);
      expect(svg.props.vbHeight).toBe(24);
      expect(svg.props.fill).toBe('none');
      expect(svg.props.strokeWidth).toBe(2.1);
      expect(svg.props.strokeLinecap).toBe('round');
      expect(svg.props.strokeLinejoin).toBe('round');
    });
  });
});
