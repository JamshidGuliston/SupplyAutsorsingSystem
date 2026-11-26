import React from 'react';
import { render, fireEvent, waitFor } from '@testing-library/react-native';
import { LoginScreen } from '../../src/screens/LoginScreen';
import { login } from '../../src/auth/loginApi';

jest.mock('../../src/auth/loginApi', () => ({ login: jest.fn() }));

const mockSetSession = jest.fn();
jest.mock('../../src/auth/authStore', () => ({
  useAuthStore: (sel: (s: any) => any) => sel({ setSession: mockSetSession }),
}));

describe('LoginScreen', () => {
  beforeEach(() => jest.clearAllMocks());

  it('sarlavha va yordam matnini chizadi', () => {
    const { getByText } = render(<LoginScreen />);
    expect(getByText('Xush kelibsiz!')).toBeTruthy();
    expect(getByText("Global Ta'm · Oshpaz kabineti")).toBeTruthy();
    expect(getByText('Parolni unutdingizmi? Texnologga murojaat qiling')).toBeTruthy();
  });

  it("bo'sh maydonlarda xato ko'rsatadi va so'rov yubormaydi", () => {
    const { getByText } = render(<LoginScreen />);
    fireEvent.press(getByText('Kirish'));
    expect(getByText('Email va parolni kiriting')).toBeTruthy();
    expect(login).not.toHaveBeenCalled();
  });

  it("login rad etilsa foydalanuvchiga tushunarli xato ko'rsatadi", async () => {
    (login as jest.Mock).mockRejectedValue({ response: { data: { error: 'invalid_credentials' } } });
    const { getByText, getByPlaceholderText, findByText } = render(<LoginScreen />);
    fireEvent.changeText(getByPlaceholderText('email@globaltam.uz'), 'a@b.uz');
    fireEvent.changeText(getByPlaceholderText('••••••••'), 'secret');
    fireEvent.press(getByText('Kirish'));
    expect(await findByText("Email yoki parol noto'g'ri.")).toBeTruthy();
    expect(mockSetSession).not.toHaveBeenCalled();
  });

  it("muvaffaqiyatli kirishda sessiyani saqlaydi", async () => {
    const user = { id: 1, name: 'Ali', email: 'a@b.uz' };
    (login as jest.Mock).mockResolvedValue({ token: 't', user });
    const { getByText, getByPlaceholderText } = render(<LoginScreen />);
    fireEvent.changeText(getByPlaceholderText('email@globaltam.uz'), ' a@b.uz ');
    fireEvent.changeText(getByPlaceholderText('••••••••'), 'secret');
    fireEvent.press(getByText('Kirish'));
    await waitFor(() => expect(mockSetSession).toHaveBeenCalledWith(user, 't'));
    expect(login).toHaveBeenCalledWith('a@b.uz', 'secret');
    expect(mockSetSession).toHaveBeenCalledWith(user, 't');
  });
});
