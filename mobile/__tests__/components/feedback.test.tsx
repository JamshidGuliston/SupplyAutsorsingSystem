import React from 'react';
import { StyleSheet } from 'react-native';
import { render } from '@testing-library/react-native';
import { Banner } from '../../src/components/Banner';
import { StatusLine } from '../../src/components/StatusLine';
import { PageHeader } from '../../src/components/PageHeader';

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

describe('StatusLine', () => {
  it('ok toni — yashil nuqta', () => {
    const { getByTestId } = render(
      <StatusLine tone="ok" title="Keldingiz" testID="s" />,
    );
    expect(flat(getByTestId('s-dot')).backgroundColor).toBe('#14884A');
  });

  it('warn toni — sariq nuqta', () => {
    const { getByTestId } = render(
      <StatusLine tone="warn" title="Hali kelmadingiz" testID="s" />,
    );
    expect(flat(getByTestId('s-dot')).backgroundColor).toBe('#D08700');
  });

  it('off toni — neytral nuqta', () => {
    const { getByTestId } = render(<StatusLine tone="off" title="Yopiq" testID="s" />);
    expect(flat(getByTestId('s-dot')).backgroundColor).toBe('#C7C9CD');
  });

  it('sarlavha qora, accent ton rangida', () => {
    const { getByText } = render(
      <StatusLine tone="ok" title="Keldingiz — " accent="08:14" testID="s" />,
    );
    expect(flat(getByText('Keldingiz — ')).color).toBe('#0D0D0D');
    expect(flat(getByText('08:14')).color).toBe('#0E6B39');
  });

  it('izohni chizadi', () => {
    const { getByText } = render(
      <StatusLine tone="ok" title="Keldingiz" detail="45 m" testID="s" />,
    );
    expect(getByText('45 m')).toBeTruthy();
  });
});

describe('PageHeader', () => {
  it('eyebrow marjon rangda va katta harfda', () => {
    const { getByText } = render(<PageHeader eyebrow="42-son bog'cha" title="Davomat" />);
    const s = flat(getByText('42-son bog\'cha'));
    expect(s.color).toBe('#EF5B3C');
    expect(s.textTransform).toBe('uppercase');
  });

  it('sarlavha h1 shkalasida', () => {
    const { getByText } = render(<PageHeader title="Davomat" />);
    expect(flat(getByText('Davomat')).fontSize).toBe(28);
  });

  it('eyebrow va meta ixtiyoriy', () => {
    const { queryByText, getByText } = render(<PageHeader title="Kirish" />);
    expect(getByText('Kirish')).toBeTruthy();
    expect(queryByText('42-son bog\'cha')).toBeNull();
  });

  it('meta berilsa chiziladi', () => {
    const { getByText } = render(<PageHeader title="Davomat" meta="25-iyul" />);
    expect(getByText('25-iyul')).toBeTruthy();
  });
});
