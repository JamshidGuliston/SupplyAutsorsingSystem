jest.mock('react-native-keychain', () => {
  let stored: string | null = null;
  return {
    setGenericPassword: jest.fn((_u: string, pw: string) => { stored = pw; return Promise.resolve(true); }),
    getGenericPassword: jest.fn(() => Promise.resolve(stored ? { username: 'token', password: stored } : false)),
    resetGenericPassword: jest.fn(() => { stored = null; return Promise.resolve(true); }),
  };
});

// authStore now transitively imports the attendance flusher (which uses NetInfo)
// and the FCM helper. Mock both so this unit test stays focused on auth state.
jest.mock('../src/attendance/attendanceFlusher', () => ({
  startFlusher: jest.fn(),
  stopFlusher: jest.fn(),
  flushOnce: jest.fn().mockResolvedValue({ sent: 0, failed: 0, remaining: 0 }),
}));

jest.mock('../src/push/fcm', () => ({
  requestPushPermission: jest.fn().mockResolvedValue(false),
  registerDeviceWithBackend: jest.fn().mockResolvedValue(undefined),
}));

import { useAuthStore } from '../src/auth/authStore';
import { loadUser } from '../src/auth/userStore';
import { queryClient } from '../src/api/queryClient';
import { CHILDREN_COUNT_KEY } from '../src/api/childrenCount';
import { useAttendanceStore } from '../src/attendance/attendanceStore';
import type { AttendanceRow } from '../src/attendance/attendanceApi';

describe('authStore', () => {
  beforeEach(() => {
    useAuthStore.setState({ user: null, status: 'unauthenticated' });
  });
  // Kesh yozuvi 5 daqiqalik GC taymerini ushlab turadi — jest jarayoni osilib qolmasin.
  afterEach(() => {
    queryClient.clear();
  });

  it('starts unauthenticated', () => {
    expect(useAuthStore.getState().status).toBe('unauthenticated');
    expect(useAuthStore.getState().user).toBeNull();
  });

  it('setSession updates state and persists token', async () => {
    await useAuthStore.getState().setSession({ id: 1, name: 'A', email: 'a@t.l', role_id: 6 }, 'tok-xyz');
    expect(useAuthStore.getState().status).toBe('authenticated');
    expect(useAuthStore.getState().user?.id).toBe(1);
    expect(loadUser()?.name).toBe('A');
  });

  it('clearSession resets state', async () => {
    await useAuthStore.getState().setSession({ id: 1, name: 'A', email: 'a@t.l', role_id: 6 }, 'tok');
    await useAuthStore.getState().clearSession();
    expect(useAuthStore.getState().status).toBe('unauthenticated');
    expect(useAuthStore.getState().user).toBeNull();
    expect(loadUser()).toBeNull();
  });

  it("chiqishda kesh va davomat do'koni tozalanadi — keyingi hisob oldingisining bog'chasini ko'rmaydi", async () => {
    await useAuthStore.getState().setSession({ id: 1, name: 'A', email: 'a@t.l', role_id: 6 }, 'tok');
    queryClient.setQueryData(CHILDREN_COUNT_KEY, { kindgarden: { id: 7, kingar_name: '12-son DMTT' } });
    useAttendanceStore.setState({
      today: { id: 1, date: '2026-11-26' } as AttendanceRow,
      kindgarden: { id: 7, lat: 41.3, lng: 69.2, geofence_radius: 200 },
      loaded: true,
      error: 'x',
    });

    await useAuthStore.getState().clearSession();

    expect(queryClient.getQueryData(CHILDREN_COUNT_KEY)).toBeUndefined();
    expect(queryClient.getQueryCache().getAll()).toEqual([]);
    const a = useAttendanceStore.getState();
    expect(a.today).toBeNull();
    expect(a.kindgarden).toBeNull();
    expect(a.loaded).toBe(false);
    expect(a.error).toBeNull();
    expect(a.loading).toBe(false);
  });
});
