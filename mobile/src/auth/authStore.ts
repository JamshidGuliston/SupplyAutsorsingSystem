import { create } from 'zustand';
import { saveToken, clearToken } from './tokenStore';
import { saveUser, clearUser } from './userStore';
import { startFlusher, stopFlusher } from '../attendance/attendanceFlusher';
import { registerDeviceWithBackend } from '../push/fcm';

export interface AuthUser {
  id: number;
  name: string;
  email: string;
  role_id: number;
}

type AuthStatus = 'unauthenticated' | 'authenticated' | 'restoring';

interface AuthState {
  user: AuthUser | null;
  status: AuthStatus;
  setStatus: (s: AuthStatus) => void;
  setSession: (user: AuthUser, token: string) => Promise<void>;
  clearSession: () => Promise<void>;
}

export const useAuthStore = create<AuthState>((set) => ({
  user: null,
  status: 'unauthenticated',
  setStatus: (s) => set({ status: s }),
  setSession: async (user, token) => {
    await saveToken(token);
    saveUser(user);
    set({ user, status: 'authenticated' });
    startFlusher();
    void registerDeviceWithBackend('1.0.0').catch(() => { /* swallow — not critical */ });
  },
  clearSession: async () => {
    stopFlusher();
    await clearToken();
    clearUser();
    set({ user: null, status: 'unauthenticated' });
  },
}));
