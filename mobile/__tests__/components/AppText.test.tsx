import React from 'react';
import { StyleSheet } from 'react-native';
import { render } from '@testing-library/react-native';
import { AppText } from '../../src/components/AppText';

const flat = (node: any) => StyleSheet.flatten(node.props.style);

describe('AppText', () => {
  it('h1 varianti tipografika shkalasini qo\'llaydi', () => {
    const { getByText } = render(<AppText variant="h1">Davomat</AppText>);
    const s = flat(getByText('Davomat'));
    expect(s.fontSize).toBe(28);
    expect(s.fontWeight).toBe('800');
    expect(s.letterSpacing).toBe(-1.0);
  });

  it('variant berilmasa body ishlatiladi', () => {
    const { getByText } = render(<AppText>Salom</AppText>);
    expect(flat(getByText('Salom')).fontSize).toBe(15);
  });

  it('standart rang ink', () => {
    const { getByText } = render(<AppText>Salom</AppText>);
    expect(flat(getByText('Salom')).color).toBe('#0D0D0D');
  });

  it('color propi standart rangni bekor qiladi', () => {
    const { getByText } = render(<AppText color="#EF5B3C">Kirish</AppText>);
    expect(flat(getByText('Kirish')).color).toBe('#EF5B3C');
  });

  it('eyebrow varianti katta harfga o\'tkazadi', () => {
    const { getByText } = render(<AppText variant="eyebrow">bog'cha</AppText>);
    expect(flat(getByText('bog\'cha')).textTransform).toBe('uppercase');
  });

  it('noma\'lum variant kelsa body ga qaytadi', () => {
    const { getByText } = render(<AppText variant={'yoq' as any}>Salom</AppText>);
    expect(flat(getByText('Salom')).fontSize).toBe(15);
  });

  it('o\'ziga xos stil variantning asosini bekor qiladi', () => {
    const { getByText } = render(
      <AppText variant="h1" style={{ fontSize: 11 }}>
        Davomat
      </AppText>
    );
    expect(flat(getByText('Davomat')).fontSize).toBe(11);
  });

  it('o\'ziga xos stil color propni bekor qiladi', () => {
    const { getByText } = render(
      <AppText color="#EF5B3C" style={{ color: '#111111' }}>
        Salom
      </AppText>
    );
    expect(flat(getByText('Salom')).color).toBe('#111111');
  });

  it('color prop variantning asosida rangni bekor qiladi', () => {
    const { getByText } = render(
      <AppText variant="h1" color="#EF5B3C">
        Davomat
      </AppText>
    );
    expect(flat(getByText('Davomat')).color).toBe('#EF5B3C');
  });
});
