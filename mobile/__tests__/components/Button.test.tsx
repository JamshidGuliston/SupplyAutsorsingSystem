// mobile/__tests__/components/Button.test.tsx
import React from 'react';
import { StyleSheet } from 'react-native';
import { render, fireEvent } from '@testing-library/react-native';
import { Button } from '../../src/components/Button';
import { PinIcon } from '../../src/components/icons/PinIcon';

const flat = (node: any) => StyleSheet.flatten(node.props.style);

describe('Button', () => {
  it('primary — qora fon, oq matn', () => {
    const { getByTestId, getByText } = render(
      <Button label="Kirish" onPress={() => {}} testID="b" />,
    );
    expect(flat(getByTestId('b')).backgroundColor).toBe('#0D0D0D');
    expect(flat(getByText('Kirish')).color).toBe('#FFFFFF');
  });

  it('outline — oq fon, 1.5 chegara, qora matn', () => {
    const { getByTestId, getByText } = render(
      <Button label="Qayta" onPress={() => {}} variant="outline" testID="b" />,
    );
    const s = flat(getByTestId('b'));
    expect(s.backgroundColor).toBe('#FFFFFF');
    expect(s.borderWidth).toBe(1.5);
    expect(s.borderColor).toBe('#E2E1DE');
    expect(flat(getByText('Qayta')).color).toBe('#0D0D0D');
  });

  it('danger — oq fon, qizil chegara va matn', () => {
    const { getByTestId, getByText } = render(
      <Button label="Chiqish" onPress={() => {}} variant="danger" testID="b" />,
    );
    const s = flat(getByTestId('b'));
    expect(s.backgroundColor).toBe('#FFFFFF');
    expect(s.borderWidth).toBe(1.5);
    expect(s.borderColor).toBe('#F3D3CF');
    expect(flat(getByText('Chiqish')).color).toBe('#C62828');
  });

  it('dangerSolid — qizil fon, oq matn', () => {
    const { getByTestId, getByText } = render(
      <Button label="Ha" onPress={() => {}} variant="dangerSolid" testID="b" />,
    );
    expect(flat(getByTestId('b')).backgroundColor).toBe('#C62828');
    expect(flat(getByText('Ha')).color).toBe('#FFFFFF');
  });

  it('ghost — shaffof, chegarasiz, kulrang matn', () => {
    const { getByTestId, getByText } = render(
      <Button label="Bekor qilish" onPress={() => {}} variant="ghost" testID="b" />,
    );
    const s = flat(getByTestId('b'));
    expect(s.backgroundColor).toBe('transparent');
    expect(s.borderWidth ?? 0).toBe(0);
    expect(flat(getByText('Bekor qilish')).color).toBe('#55575C');
  });

  it('o\'lchamlar — md 52, sm 44', () => {
    const { getByTestId } = render(
      <>
        <Button label="X" onPress={() => {}} testID="b" />
        <Button label="Y" onPress={() => {}} size="sm" testID="s" />
      </>,
    );
    expect(flat(getByTestId('b')).minHeight).toBe(52);
    expect(flat(getByTestId('s')).minHeight).toBe(44);
  });

  it('ikonka matn chapida, matn rangida chiziladi', () => {
    const { getByTestId } = render(
      <Button
        label="Joy"
        onPress={() => {}}
        icon={(p) => <PinIcon {...p} testID="ico" />}
        testID="b"
      />,
    );
    expect(getByTestId('ico')).toBeTruthy();
  });

  it('disabled — kulrang fon va onPress chaqirilmaydi', () => {
    const onPress = jest.fn();
    const { getByTestId } = render(
      <Button label="X" onPress={onPress} disabled testID="b" />,
    );
    expect(flat(getByTestId('b')).backgroundColor).toBe('#F1F1F3');
    fireEvent.press(getByTestId('b'));
    expect(onPress).not.toHaveBeenCalled();
  });

  it('loading — matn o\'rniga spinner, onPress chaqirilmaydi', () => {
    const onPress = jest.fn();
    const { getByTestId, queryByText } = render(
      <Button label="Kirish" onPress={onPress} loading testID="b" />,
    );
    expect(queryByText('Kirish')).toBeNull();
    fireEvent.press(getByTestId('b'));
    expect(onPress).not.toHaveBeenCalled();
  });

  it('loading + progress — yorliq, qadam va to\'lish qatlami', () => {
    const fn = jest.fn();
    const { getByTestId, getByText, queryByText } = render(
      <Button
        label="Ketdim"
        onPress={fn}
        loading
        progress={{ step: 4, total: 6, label: 'Selfi olinmoqda' }}
        testID="b"
      />,
    );
    expect(queryByText('Ketdim')).toBeNull();
    expect(getByText('Selfi olinmoqda')).toBeTruthy();
    expect(getByText('4/6')).toBeTruthy();
    expect(flat(getByTestId('b-fill')).width).toBe('67%');
    fireEvent.press(getByTestId('b'));
    expect(fn).not.toHaveBeenCalled();
  });

  it('loading primary — qora fon saqlanadi, kulrang bo\'lmaydi', () => {
    const { getByTestId } = render(
      <Button label="Kirish" onPress={() => {}} loading testID="b" />,
    );
    expect(flat(getByTestId('b')).backgroundColor).toBe('#0D0D0D');
  });

  it('loading outline — oq fon va chegara saqlanadi', () => {
    const { getByTestId } = render(
      <Button label="Qayta" onPress={() => {}} variant="outline" loading testID="b" />,
    );
    const s = flat(getByTestId('b'));
    expect(s.backgroundColor).toBe('#FFFFFF');
    expect(s.borderWidth).toBe(1.5);
  });

  it('oddiy holatda onPress chaqiriladi', () => {
    const onPress = jest.fn();
    const { getByTestId } = render(<Button label="X" onPress={onPress} testID="b" />);
    fireEvent.press(getByTestId('b'));
    expect(onPress).toHaveBeenCalledTimes(1);
  });
});
