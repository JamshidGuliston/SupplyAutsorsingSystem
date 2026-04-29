jest.mock('react-native-keychain', () => {
  let stored: string | null = null;
  return {
    setGenericPassword: jest.fn((_u: string, pw: string) => { stored = pw; return Promise.resolve(true); }),
    getGenericPassword: jest.fn(() => Promise.resolve(stored ? { username: 'token', password: stored } : false)),
    resetGenericPassword: jest.fn(() => { stored = null; return Promise.resolve(true); }),
  };
});

import { useAuthStore } from '../src/auth/authStore';

describe('authStore', () => {
  beforeEach(() => {
    useAuthStore.setState({ user: null, status: 'unauthenticated' });
  });

  it('starts unauthenticated', () => {
    expect(useAuthStore.getState().status).toBe('unauthenticated');
    expect(useAuthStore.getState().user).toBeNull();
  });

  it('setSession updates state and persists token', async () => {
    await useAuthStore.getState().setSession({ id: 1, name: 'A', email: 'a@t.l', role_id: 6 }, 'tok-xyz');
    expect(useAuthStore.getState().status).toBe('authenticated');
    expect(useAuthStore.getState().user?.id).toBe(1);
  });

  it('clearSession resets state', async () => {
    await useAuthStore.getState().setSession({ id: 1, name: 'A', email: 'a@t.l', role_id: 6 }, 'tok');
    await useAuthStore.getState().clearSession();
    expect(useAuthStore.getState().status).toBe('unauthenticated');
    expect(useAuthStore.getState().user).toBeNull();
  });
});
