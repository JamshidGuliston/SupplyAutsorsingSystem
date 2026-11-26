import React from 'react';
import { StyleSheet, Text } from 'react-native';
import { render } from '@testing-library/react-native';
import { IconCircle } from '../../src/components/IconCircle';
import { Pill } from '../../src/components/Pill';
import { Card, CardRow } from '../../src/components/Card';
import { Skeleton } from '../../src/components/Skeleton';
import { EmptyState } from '../../src/components/EmptyState';

const flat = (node: any) => StyleSheet.flatten(node.props.style);

describe('IconCircle', () => {
  it('warn toni — sariq fon va yarim o\'lcham radius', () => {
    const { getByTestId } = render(
      <IconCircle tone="warn" size={34} testID="c"><Text>x</Text></IconCircle>,
    );
    const s = flat(getByTestId('c'));
    expect(s.backgroundColor).toBe('#FDF3E0');
    expect(s.borderRadius).toBe(17);
    expect(s.width).toBe(34);
  });

  it('neutral toni — maydon foni', () => {
    const { getByTestId } = render(
      <IconCircle tone="neutral" size={40} testID="c"><Text>x</Text></IconCircle>,
    );
    expect(flat(getByTestId('c')).backgroundColor).toBe('#F4F3F1');
  });
});

describe('Pill', () => {
  it('ok toni nuqta bilan', () => {
    const { getByTestId, getByText } = render(<Pill tone="ok" label="Keldi" dot testID="p" />);
    expect(flat(getByTestId('p')).backgroundColor).toBe('#E7F4EC');
    expect(flat(getByText('Keldi')).color).toBe('#0E6B39');
    const dot = flat(getByTestId('p-dot'));
    expect(dot.backgroundColor).toBe('#14884A');
    expect(dot.width).toBe(6);
  });

  it('nuqta so\'ralmasa chizilmaydi', () => {
    const { queryByTestId } = render(<Pill tone="ok" label="Keldi" testID="p" />);
    expect(queryByTestId('p-dot')).toBeNull();
  });

  it('onHero toni', () => {
    const { getByTestId, getByText } = render(<Pill tone="onHero" label="12-son" testID="p" />);
    expect(flat(getByTestId('p')).backgroundColor).toBe('rgba(255,255,255,0.18)');
    expect(flat(getByText('12-son')).color).toBe('#FFFFFF');
  });

  it('warn va neutral tonlari', () => {
    const w = render(<Pill tone="warn" label="Kutilmoqda" testID="w" />);
    expect(flat(w.getByTestId('w')).backgroundColor).toBe('#FDF3E0');
    expect(flat(w.getByText('Kutilmoqda')).color).toBe('#7A5200');
    const n = render(<Pill tone="neutral" label="Yopiq" testID="n" />);
    expect(flat(n.getByTestId('n')).backgroundColor).toBe('#F4F3F1');
    expect(flat(n.getByText('Yopiq')).color).toBe('#55575C');
  });
});

describe('Card', () => {
  it('oq fon, 18 radius va soya', () => {
    const { getByTestId } = render(<Card testID="c"><Text>x</Text></Card>);
    const s = flat(getByTestId('c'));
    expect(s.borderRadius).toBe(18);
    expect(s.backgroundColor).toBe('#FFFFFF');
    expect(s.elevation).toBe(2);
    expect(s.shadowOpacity).toBe(0.06);
    expect(s.shadowRadius).toBe(16);
  });

  it('CardRow footer — alohida fon; last bo\'lmasa ajratgich', () => {
    const f = render(<CardRow footer last testID="r"><Text>x</Text></CardRow>);
    const fs = flat(f.getByTestId('r'));
    expect(fs.backgroundColor).toBe('#FAFAF9');
    expect(fs.borderBottomLeftRadius).toBe(18);
    expect(fs.borderBottomWidth ?? 0).toBe(0);
    const m = render(<CardRow testID="m"><Text>x</Text></CardRow>);
    const ms = flat(m.getByTestId('m'));
    expect(ms.borderBottomWidth).toBe(1);
    expect(ms.borderBottomColor).toBe('#F0EFED');
  });
});

describe('Skeleton', () => {
  it('oddiy va onHero ranglari', () => {
    const a = render(<Skeleton width={100} height={12} testID="s" />);
    expect(flat(a.getByTestId('s')).backgroundColor).toBe('#EEEDEA');
    expect(flat(a.getByTestId('s')).borderRadius).toBe(8);
    const b = render(<Skeleton width={100} height={12} onHero testID="h" />);
    expect(flat(b.getByTestId('h')).backgroundColor).toBe('rgba(255,255,255,0.25)');
  });
});

describe('EmptyState', () => {
  it('sarlavha, matn va action ni chizadi', () => {
    const { getByText, getByTestId } = render(
      <EmptyState
        icon={<Text>i</Text>}
        tone="warn"
        title="Hali xabar yo'q"
        body="Yangi xabarlar shu yerda"
        action={<Text>Qayta</Text>}
        testID="e"
      />,
    );
    expect(getByText("Hali xabar yo'q")).toBeTruthy();
    expect(getByText('Yangi xabarlar shu yerda')).toBeTruthy();
    expect(getByText('Qayta')).toBeTruthy();
    expect(flat(getByTestId('e-icon')).width).toBe(96);
  });
});
