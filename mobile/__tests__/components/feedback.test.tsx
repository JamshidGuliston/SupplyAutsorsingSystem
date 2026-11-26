import React from 'react';
import { StyleSheet } from 'react-native';
import { render } from '@testing-library/react-native';
import { Banner } from '../../src/components/Banner';

const flat = (node: any) => StyleSheet.flatten(node.props.style);

describe('Banner', () => {
  it('xabar bo\'lmasa hech narsa chizmaydi', () => {
    const { toJSON } = render(<Banner message={null} />);
    expect(toJSON()).toBeNull();
  });

  it('error toni — qizil fon, quyuq qizil matn va ikonka doirasi', () => {
    const { getByTestId, getByText } = render(<Banner message="Xato" testID="b" />);
    const s = flat(getByTestId('b'));
    expect(s.backgroundColor).toBe('#FDECEA');
    expect(s.borderRadius).toBe(14);
    expect(flat(getByText('Xato')).color).toBe('#8E1C1C');
    const icon = flat(getByTestId('b-icon'));
    expect(icon.backgroundColor).toBe('#F8C9C4');
    expect(icon.width).toBe(22);
  });

  it('success toni — yashil fon va quyuq yashil matn', () => {
    const { getByTestId, getByText } = render(
      <Banner tone="success" message="Qabul qilindi" testID="b" />,
    );
    expect(flat(getByTestId('b')).backgroundColor).toBe('#E7F4EC');
    expect(flat(getByText('Qabul qilindi')).color).toBe('#0E6B39');
    expect(flat(getByTestId('b-icon')).backgroundColor).toBe('#CFE8D9');
  });

  it('warn toni sarlavha bilan — sarlavha qalin', () => {
    const { getByTestId, getByText } = render(
      <Banner tone="warn" title="Navbatga qo'shildi." message="Internet kelganda yuboriladi" testID="b" />,
    );
    expect(flat(getByTestId('b')).backgroundColor).toBe('#FDF3E0');
    expect(flat(getByText("Navbatga qo'shildi.")).fontWeight).toBe('700');
    expect(flat(getByText("Navbatga qo'shildi.")).color).toBe('#7A5200');
    expect(flat(getByTestId('b-icon')).backgroundColor).toBe('#F6DFAF');
  });
});
