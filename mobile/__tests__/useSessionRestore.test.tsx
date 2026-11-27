jest.mock('../src/auth/tokenStore', () => ({
  loadToken: jest.fn(),
  saveToken: jest.fn(async () => {}),
  clearToken: jest.fn(async () => {}),
}));
jest.mock('../src/api/client', () => ({ api: { get: jest.fn() } }));
jest.mock('../src/attendance/attendanceFlusher', () => ({
  startFlusher: jest.fn(),
  stopFlusher: jest.fn(),
}));
jest.mock('../src/push/fcm', () => ({
  registerDeviceWithBackend: jest.fn().mockResolvedValue(undefined),
}));

import { renderHook, waitFor } from '@testing-library/react-native';
import { useSessionRestore } from '../src/auth/useSessionRestore';
import { useAuthStore } from '../src/auth/authStore';
import { saveUser, loadUser, clearUser } from '../src/auth/userStore';
import { loadToken } from '../src/auth/tokenStore';
import { api } from '../src/api/client';
import { startFlusher } from '../src/attendance/attendanceFlusher';

const saved = { id: 3, name: 'Dilnoza Aliyeva', email: 'd@g.uz', role_id: 6 };

describe('useSessionRestore', () => {
  beforeEach(() => {
    jest.clearAllMocks();
    clearUser();
    useAuthStore.setState({ user: null, status: 'unauthenticated' });
  });

  it("token yaroqli bo'lsa saqlangan foydalanuvchi tiklanadi", async () => {
    (loadToken as jest.Mock).mockResolvedValue('tok');
    (api.get as jest.Mock).mockResolvedValue({ data: {} });
    saveUser(saved);

    renderHook(() => useSessionRestore());

    await waitFor(() => expect(useAuthStore.getState().status).toBe('authenticated'));
    expect(useAuthStore.getState().user?.name).toBe('Dilnoza Aliyeva');
    expect(useAuthStore.getState().user).toEqual(saved);
    expect(api.get).toHaveBeenCalledWith('/chef/attendance/today');
    expect(startFlusher).toHaveBeenCalled();
  });

  it("yangilanishdan oldingi sessiya (saqlangan foydalanuvchi yo'q) — kiradi, user null", async () => {
    (loadToken as jest.Mock).mockResolvedValue('tok');
    (api.get as jest.Mock).mockResolvedValue({ data: {} });

    renderHook(() => useSessionRestore());

    await waitFor(() => expect(useAuthStore.getState().status).toBe('authenticated'));
    expect(useAuthStore.getState().user).toBeNull();
  });

  it("token yo'q — kirmaydi, so'rov ketmaydi", async () => {
    (loadToken as jest.Mock).mockResolvedValue(null);
    saveUser(saved);

    renderHook(() => useSessionRestore());

    await waitFor(() => expect(useAuthStore.getState().status).toBe('unauthenticated'));
    expect(api.get).not.toHaveBeenCalled();
    expect(useAuthStore.getState().user).toBeNull();
  });

  it("403 da sessiya tozalanadi: saqlangan foydalanuvchi ham o'chadi", async () => {
    (loadToken as jest.Mock).mockResolvedValue('tok');
    (api.get as jest.Mock).mockRejectedValue({ response: { status: 403 } });
    saveUser(saved);

    renderHook(() => useSessionRestore());

    await waitFor(() => expect(loadUser()).toBeNull());
    await waitFor(() => expect(useAuthStore.getState().status).toBe('unauthenticated'));
    expect(useAuthStore.getState().user).toBeNull();
  });
});
