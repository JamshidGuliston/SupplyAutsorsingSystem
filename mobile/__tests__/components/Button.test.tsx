// mobile/__tests__/components/Button.test.tsx
import React from 'react';
import { StyleSheet } from 'react-native';
import { render, fireEvent } from '@testing-library/react-native';
import { Button } from '../../src/components/Button';

const flat = (node: any) => StyleSheet.flatten(node.props.style);

describe('Button', () => {
  it('primary — marjon fon, oq matn', () => {
    const { getByTestId, getByText } = render(
      <Button label="Kirish" onPress={() => {}} testID="b" />,
    );
    expect(flat(getByTestId('b')).backgroundColor).toBe('#EF5B3C');
    expect(flat(getByText('Kirish')).color).toBe('#FFFFFF');
  });

  it('outline — oq fon, 2px qora chegara, qora matn', () => {
    const { getByTestId, getByText } = render(
      <Button label="Qayta" onPress={() => {}} variant="outline" testID="b" />,
    );
    const s = flat(getByTestId('b'));
    expect(s.backgroundColor).toBe('#FFFFFF');
    expect(s.borderWidth).toBe(2);
    expect(s.borderColor).toBe('#0D0D0D');
    expect(flat(getByText('Qayta')).color).toBe('#0D0D0D');
  });

  it('danger — danger fon', () => {
    const { getByTestId } = render(
      <Button label="Chiqish" onPress={() => {}} variant="danger" testID="b" />,
    );
    expect(flat(getByTestId('b')).backgroundColor).toBe('#C62828');
  });

  it('bosish maydoni kamida 56px', () => {
    const { getByTestId } = render(<Button label="X" onPress={() => {}} testID="b" />);
    expect(flat(getByTestId('b')).minHeight).toBe(56);
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

  it('loadingLabel berilsa spinner yonida ko\'rsatiladi', () => {
    const { queryByText, getByText } = render(
      <Button
        label="Keldim"
        onPress={() => {}}
        loading
        loadingLabel="2/6 GPS koordinatasi olinmoqda..."
        testID="b"
      />,
    );
    expect(queryByText('Keldim')).toBeNull();
    expect(getByText('2/6 GPS koordinatasi olinmoqda...')).toBeTruthy();
  });

  it('loading primary — marjon fon saqlanadi, kulrang bo\'lmaydi', () => {
    const { getByTestId } = render(
      <Button label="Kirish" onPress={() => {}} loading testID="b" />,
    );
    expect(flat(getByTestId('b')).backgroundColor).toBe('#EF5B3C');
  });

  it('loading outline — oq fon va 2px chegara saqlanadi', () => {
    const { getByTestId } = render(
      <Button label="Qayta" onPress={() => {}} variant="outline" loading testID="b" />,
    );
    const s = flat(getByTestId('b'));
    expect(s.backgroundColor).toBe('#FFFFFF');
    expect(s.borderWidth).toBe(2);
    expect(s.borderColor).toBe('#0D0D0D');
  });

  it('oddiy holatda onPress chaqiriladi', () => {
    const onPress = jest.fn();
    const { getByTestId } = render(<Button label="X" onPress={onPress} testID="b" />);
    fireEvent.press(getByTestId('b'));
    expect(onPress).toHaveBeenCalledTimes(1);
  });
});
