import { create } from 'zustand';
import { saveToken, clearToken } from './tokenStore';

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
    set({ user, status: 'authenticated' });
  },
  clearSession: async () => {
    await clearToken();
    set({ user: null, status: 'unauthenticated' });
  },
}));
