import React from 'react';
import { StyleSheet } from 'react-native';
import { render, fireEvent } from '@testing-library/react-native';
import { MailIcon } from '../../src/components/icons/MailIcon';
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

  it('bosish maydoni kamida 50px', () => {
    const { getByTestId } = render(<Field label="Email" testID="f" />);
    expect(flat(getByTestId('f')).minHeight).toBe(50);
  });

  it('tashqaridan berilgan style matn rangini bekor qila olmaydi', () => {
    const { getByTestId } = render(
      <Field label="Email" testID="f" style={{ color: '#FFFFFF' }} />,
    );
    expect(flat(getByTestId('f')).color).toBe('#0D0D0D');
  });

  it("fokusda chegara va halqa rangi o'zgaradi, blurda qaytadi", () => {
    const { getByTestId } = render(<Field label="Email" testID="f" />);
    expect(flat(getByTestId('f')).borderColor).toBe('#ECECEF');
    expect(flat(getByTestId('f-halo')).borderColor).toBe('transparent');
    fireEvent(getByTestId('f'), 'focus');
    expect(flat(getByTestId('f')).borderColor).toBe('#EF5B3C');
    expect(flat(getByTestId('f-halo')).borderColor).toBe('#FFE9E2');
    fireEvent(getByTestId('f'), 'blur');
    expect(flat(getByTestId('f')).borderColor).toBe('#ECECEF');
    expect(flat(getByTestId('f-halo')).borderColor).toBe('transparent');
  });

  it('caller onFocus va onBlur ham chaqiriladi', () => {
    const onFocus = jest.fn();
    const onBlur = jest.fn();
    const { getByTestId } = render(
      <Field label="Email" testID="f" onFocus={onFocus} onBlur={onBlur} />,
    );
    fireEvent(getByTestId('f'), 'focus');
    fireEvent(getByTestId('f'), 'blur');
    expect(onFocus).toHaveBeenCalledTimes(1);
    expect(onBlur).toHaveBeenCalledTimes(1);
  });

  it('ikonka berilsa chiziladi', () => {
    const { getByTestId } = render(
      <Field label="Email" testID="f" icon={(p) => <MailIcon {...p} testID="f-icon" />} />,
    );
    expect(getByTestId('f-icon')).toBeTruthy();
  });

  it("secureTextEntry da ko'z tugmasi parolni ko'rsatadi/yashiradi", () => {
    const { getByTestId } = render(<Field label="Parol" testID="f" secureTextEntry />);
    expect(getByTestId('f').props.secureTextEntry).toBe(true);
    expect(getByTestId('f-eye').props.accessibilityLabel).toBe("Parolni ko'rsatish");
    fireEvent.press(getByTestId('f-eye'));
    expect(getByTestId('f').props.secureTextEntry).toBe(false);
    expect(getByTestId('f-eye').props.accessibilityLabel).toBe('Parolni yashirish');
    fireEvent.press(getByTestId('f-eye'));
    expect(getByTestId('f').props.secureTextEntry).toBe(true);
  });

  it("secureTextEntry bo'lmasa ko'z tugmasi yo'q", () => {
    const { queryByTestId } = render(<Field label="Email" testID="f" />);
    expect(queryByTestId('f-eye')).toBeNull();
  });

  it("number varianti: o'lcham, fon, markazlash, klaviatura, matn rangi", () => {
    const { getByTestId, queryByText } = render(
      <Field variant="number" label="Yosh" testID="n" />,
    );
    const s = flat(getByTestId('n'));
    expect(s.width).toBe(74);
    expect(s.height).toBe(40);
    expect(s.backgroundColor).toBe('#F4F3F1');
    expect(s.borderColor).toBe('transparent');
    expect(s.textAlign).toBe('center');
    expect(s.color).toBe('#0D0D0D');
    expect(getByTestId('n').props.keyboardType).toBe('number-pad');
    expect(queryByText('Yosh')).toBeNull();
  });

  it("number varianti fokusda fon va chegara o'zgaradi", () => {
    const { getByTestId } = render(<Field variant="number" testID="n" />);
    fireEvent(getByTestId('n'), 'focus');
    const s = flat(getByTestId('n'));
    expect(s.backgroundColor).toBe('#FFFFFF');
    expect(s.borderColor).toBe('#EF5B3C');
    expect(flat(getByTestId('n-halo')).borderColor).toBe('#FFE9E2');
  });
});
