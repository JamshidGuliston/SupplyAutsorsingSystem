import React from 'react';
import { StyleSheet, Text } from 'react-native';
import { render } from '@testing-library/react-native';
import { Section } from '../../src/components/Section';
import { Divider } from '../../src/components/Divider';

const flat = (node: any) => StyleSheet.flatten(node.props.style);

describe('Section', () => {
  it('ekran oraliqlarini qo\'llaydi', () => {
    const { getByTestId } = render(
      <Section testID="s">
        <Text>ichida</Text>
      </Section>,
    );
    const s = flat(getByTestId('s'));
    expect(s.paddingHorizontal).toBe(18);
    expect(s.paddingVertical).toBe(16);
  });

  it('bolalarini chizadi', () => {
    const { getByText } = render(
      <Section>
        <Text>ichida</Text>
      </Section>,
    );
    expect(getByText('ichida')).toBeTruthy();
  });

  it('style propi oraliqlarni bekor qila oladi', () => {
    const { getByTestId } = render(
      <Section testID="s" style={{ paddingVertical: 0 }}>
        <Text>ichida</Text>
      </Section>,
    );
    expect(flat(getByTestId('s')).paddingVertical).toBe(0);
  });
});

describe('Divider', () => {
  it('1px rule rangidagi chiziq', () => {
    const { getByTestId } = render(<Divider testID="d" />);
    const s = flat(getByTestId('d'));
    expect(s.height).toBe(1);
    expect(s.backgroundColor).toBe('#E8E8EA');
  });
});
