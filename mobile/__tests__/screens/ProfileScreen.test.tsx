import React from 'react';
import { render, fireEvent, waitFor } from '@testing-library/react-native';
import { ProfileScreen } from '../../src/screens/ProfileScreen';

jest.mock('@react-navigation/native', () => {
  const R = require('react');
  return { useFocusEffect: (cb: () => void) => R.useEffect(cb, []) };
});

let mockUser: { name: string; email: string } | null = null;
const mockClearSession = jest.fn(async () => {});
jest.mock('../../src/auth/authStore', () => ({
  useAuthStore: (sel?: (s: any) => any) => {
    const s = { user: mockUser, clearSession: mockClearSession };
    return sel ? sel(s) : s;
  },
}));

const mockStop = jest.fn();
jest.mock('../../src/attendance/attendanceFlusher', () => ({ stopFlusher: () => mockStop() }));

let mockPending = 0;
const mockClearQueue = jest.fn();
jest.mock('../../src/attendance/attendanceQueue', () => ({
  attendancePendingCount: () => mockPending,
  clearAll: () => mockClearQueue(),
}));

jest.mock('../../src/api/childrenCount', () => ({
  useChildrenCountToday: () => ({ data: { kindgarden: { id: 7, kingar_name: '12-son DMTT' } } }),
}));

beforeEach(() => {
  jest.clearAllMocks();
  mockPending = 0;
  mockUser = { name: 'Dilnoza Aliyeva', email: 'd@g.uz' };
});

describe('ProfileScreen', () => {
  it('foydalanuvchi, navbat holati va versiyani ko\'rsatadi; chiqish ketma-ketligi', async () => {
    const { getByText } = render(<ProfileScreen />);
    expect(getByText('DA')).toBeTruthy();
    expect(getByText('Dilnoza Aliyeva')).toBeTruthy();
    expect(getByText('d@g.uz')).toBeTruthy();
    expect(getByText('12-son DMTT')).toBeTruthy();
    expect(getByText("Navbat bo'sh")).toBeTruthy();
    expect(getByText("Global Ta'm · versiya 1.2.0")).toBeTruthy();
    fireEvent.press(getByText('Hisobdan chiqish'));
    expect(getByText("Qayta kirish uchun email va parol kerak bo'ladi.")).toBeTruthy();
    fireEvent.press(getByText('Ha, chiqaman'));
    await waitFor(() => expect(mockClearSession).toHaveBeenCalled());
    expect(mockStop).toHaveBeenCalled();
    expect(mockClearQueue).toHaveBeenCalled();
  });

  it('navbatda davomat bo\'lsa ogohlantiradi', () => {
    mockPending = 1;
    const { getByText } = render(<ProfileScreen />);
    expect(getByText('1 ta navbatda')).toBeTruthy();
    fireEvent.press(getByText('Hisobdan chiqish'));
    expect(getByText('1 ta davomat hali yuborilmagan')).toBeTruthy();
  });

  it('bekor qilish dialogni yopadi', () => {
    const { getByText, queryByText } = render(<ProfileScreen />);
    fireEvent.press(getByText('Hisobdan chiqish'));
    fireEvent.press(getByText('Bekor qilish'));
    expect(queryByText('Chiqishni xohlaysizmi?')).toBeNull();
    expect(mockClearSession).not.toHaveBeenCalled();
  });

  it('foydalanuvchi bo\'lmasa qulamaydi', () => {
    mockUser = null;
    const { queryByText, getByTestId } = render(<ProfileScreen />);
    expect(queryByText('Email')).toBeNull();
    expect(getByTestId('avatar-icon')).toBeTruthy();
  });
});
