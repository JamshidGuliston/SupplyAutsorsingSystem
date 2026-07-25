import React from 'react';
import { StyleSheet } from 'react-native';
import { render, fireEvent } from '@testing-library/react-native';
import { Field } from '../../src/components/Field';

const flat = (node: any) => StyleSheet.flatten(node.props.style);

describe('Field', () => {
  // Regressiya: tungi rejimdagi Android'da color berilmasa oq fonda oq matn chiqadi.
  it('matn rangini aniq beradi (oq fonda oq matn bo\'lmaydi)', () => {
    const { getByTestId } = render(<Field label="Email" testID="f" />);
    expect(flat(getByTestId('f')).color).toBe('#0D0D0D');
  });

  it('placeholder rangini aniq beradi', () => {
    const { getByTestId } = render(<Field label="Email" testID="f" />);
    expect(getByTestId('f').props.placeholderTextColor).toBe('#9A9CA2');
  });

  it('fon oq — matn rangi bilan bir xil emasligi kafolatlanadi', () => {
    const { getByTestId } = render(<Field label="Email" testID="f" />);
    const s = flat(getByTestId('f'));
    expect(s.backgroundColor).toBe('#FFFFFF');
    expect(s.color).not.toBe(s.backgroundColor);
  });

  it('label ni chizadi', () => {
    const { getByText } = render(<Field label="Parol" />);
    expect(getByText('Parol')).toBeTruthy();
  });

  it('TextInput proplarini o\'tkazadi', () => {
    const onChangeText = jest.fn();
    const { getByTestId } = render(
      <Field
        label="Email"
        testID="f"
        keyboardType="email-address"
        secureTextEntry
        value="a@b.uz"
        onChangeText={onChangeText}
      />,
    );
    const input = getByTestId('f');
    expect(input.props.keyboardType).toBe('email-address');
    expect(input.props.secureTextEntry).toBe(true);
    expect(input.props.value).toBe('a@b.uz');
    fireEvent.changeText(input, 'x@y.uz');
    expect(onChangeText).toHaveBeenCalledWith('x@y.uz');
  });

  it('bosish maydoni kamida 52px', () => {
    const { getByTestId } = render(<Field label="Email" testID="f" />);
    expect(flat(getByTestId('f')).minHeight).toBe(52);
  });
});
