import React from 'react';
import { Text } from 'react-native';
import { render, fireEvent } from '@testing-library/react-native';
import { ConfirmDialog } from '../../src/components/ConfirmDialog';

function setup(visible: boolean) {
  const onConfirm = jest.fn();
  const onCancel = jest.fn();
  const utils = render(
    <ConfirmDialog
      visible={visible}
      title="Chiqishni xohlaysizmi?"
      confirmLabel="Ha, chiqaman"
      cancelLabel="Bekor qilish"
      onConfirm={onConfirm}
      onCancel={onCancel}
    >
      <Text>Tana matni</Text>
    </ConfirmDialog>,
  );
  return { ...utils, onConfirm, onCancel };
}

describe('ConfirmDialog', () => {
  it('visible bo\'lmasa sarlavha chizilmaydi', () => {
    const { queryByText } = setup(false);
    expect(queryByText('Chiqishni xohlaysizmi?')).toBeNull();
  });

  it('sarlavha va tanani ko\'rsatadi', () => {
    const { getByText } = setup(true);
    expect(getByText('Chiqishni xohlaysizmi?')).toBeTruthy();
    expect(getByText('Tana matni')).toBeTruthy();
  });

  it('tugmalar callback\'larni chaqiradi', () => {
    const { getByText, onConfirm, onCancel } = setup(true);
    fireEvent.press(getByText('Ha, chiqaman'));
    expect(onConfirm).toHaveBeenCalledTimes(1);
    fireEvent.press(getByText('Bekor qilish'));
    expect(onCancel).toHaveBeenCalledTimes(1);
  });
});
