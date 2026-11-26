import React from 'react';
import { StyleSheet, Text } from 'react-native';
import { render } from '@testing-library/react-native';
import { SafeAreaProvider } from 'react-native-safe-area-context';
import NetInfo from '@react-native-community/netinfo';
import { Hero } from '../../src/components/Hero';
import { HeroScreen } from '../../src/components/HeroScreen';
import { enqueue, clearAll } from '../../src/attendance/attendanceQueue';

const flat = (node: any) => StyleSheet.flatten(node.props.style);

const METRICS = {
  frame: { x: 0, y: 0, width: 360, height: 720 },
  insets: { top: 24, left: 0, right: 0, bottom: 0 },
};

beforeEach(() => {
  clearAll();
});

describe('HeroScreen', () => {
  it('hero va varaq ichidagini chizadi, status bar tasmasi marjon', () => {
    const { getByText, getByTestId } = render(
      <HeroScreen hero={<Hero><Text>Davomat</Text></Hero>}>
        <Text>ichida</Text>
      </HeroScreen>,
    );
    expect(getByText('Davomat')).toBeTruthy();
    expect(getByText('ichida')).toBeTruthy();
    expect(flat(getByTestId('status-strip')).backgroundColor).toBe('#F47A55');
  });

  it('status bar tasmasi tepada qotgan, balandligi safe-area tepasiga teng', () => {
    const { getByTestId } = render(
      <SafeAreaProvider initialMetrics={METRICS}>
        <HeroScreen hero={<Hero><Text>Davomat</Text></Hero>}>
          <Text>ichida</Text>
        </HeroScreen>
      </SafeAreaProvider>,
    );
    const s = flat(getByTestId('status-strip'));
    expect(s.position).toBe('absolute');
    expect(s.top).toBe(0);
    expect(s.height).toBe(24);
  });

  it('varaq canvas fonida, yuqori radius 24, hero ustiga 22 dp chiqadi', () => {
    const { getByTestId } = render(
      <HeroScreen testID="hs" hero={<Hero><Text>Davomat</Text></Hero>}>
        <Text>ichida</Text>
      </HeroScreen>,
    );
    const s = flat(getByTestId('hs-sheet'));
    expect(s.backgroundColor).toBe('#F7F6F4');
    expect(s.borderTopLeftRadius).toBe(24);
    expect(s.borderTopRightRadius).toBe(24);
    expect(s.marginTop).toBe(-22);
    expect(s.paddingHorizontal).toBe(14);
    expect(s.paddingVertical).toBe(16);
  });

  it("oq varaq — oq fon va radius 28; overlap berilsa varaq shuncha yuqoriga chiqadi", () => {
    const { getByTestId } = render(
      <HeroScreen
        testID="hs"
        sheet="white"
        overlap={28}
        hero={<Hero overlap={28}><Text>Kirish</Text></Hero>}
      >
        <Text>ichida</Text>
      </HeroScreen>,
    );
    const s = flat(getByTestId('hs-sheet'));
    expect(s.backgroundColor).toBe('#FFFFFF');
    expect(s.borderTopLeftRadius).toBe(28);
    expect(s.marginTop).toBe(-28);
  });
});

describe('Hero', () => {
  it("oraliqlar: tepada safe-area + 8, yonlarda 20, pastda overlap + 14", () => {
    const { getByTestId } = render(
      <SafeAreaProvider initialMetrics={METRICS}>
        <Hero testID="h">
          <Text>Davomat</Text>
        </Hero>
      </SafeAreaProvider>,
    );
    const s = flat(getByTestId('h'));
    expect(s.paddingTop).toBe(32);
    expect(s.paddingHorizontal).toBe(20);
    expect(s.paddingBottom).toBe(36);
    expect(s.overflow).toBe('hidden');
  });

  it('pastki oraliq berilgan overlap ga moslashadi', () => {
    const { getByTestId } = render(
      <Hero testID="h" overlap={28}>
        <Text>Kirish</Text>
      </Hero>,
    );
    expect(flat(getByTestId('h')).paddingBottom).toBe(42);
  });

  it("hammasi joyida bo'lsa \"Internet yo'q\" kapsulasi chizilmaydi", () => {
    const { queryByTestId } = render(
      <Hero>
        <Text>Davomat</Text>
      </Hero>,
    );
    expect(queryByTestId('offline-pill')).toBeNull();
  });

  it("navbatda davomat bo'lsa hero ichida sariq matnli kapsula chiqadi", () => {
    enqueue({ kind: 'check_in', lat: 41.31, lng: 69.27, capturedAt: '2026-11-26T03:10:00Z',
              isMock: false, photoUri: 'file://a.jpg' });
    const { getByTestId, getByText } = render(
      <Hero>
        <Text>Davomat</Text>
      </Hero>,
    );
    expect(flat(getByTestId('offline-pill')).backgroundColor).toBe('#FFFFFF');
    expect(flat(getByText('1 ta yuborilmagan')).color).toBe('#7A5200');
  });

  it("internet yo'q bo'lsa qizil matnli kapsula chiqadi", () => {
    (NetInfo.addEventListener as jest.Mock).mockImplementationOnce((cb: any) => {
      cb({ isConnected: false });
      return jest.fn();
    });
    const { getByText } = render(
      <Hero>
        <Text>Davomat</Text>
      </Hero>,
    );
    expect(flat(getByText("Internet yo'q")).color).toBe('#C62828');
  });
});
