import { MMKV } from 'react-native-mmkv';
import type { AuthUser } from './authStore';

const KEY = 'auth.user.v1';
const storage = new MMKV({ id: 'auth' });

export function saveUser(u: AuthUser): void {
  storage.set(KEY, JSON.stringify(u));
}

export function loadUser(): AuthUser | null {
  const raw = storage.getString(KEY);
  if (!raw) return null;
  try {
    const u = JSON.parse(raw);
    if (
      u && typeof u === 'object' &&
      typeof u.id === 'number' &&
      typeof u.name === 'string' &&
      typeof u.email === 'string'
    ) {
      return u as AuthUser;
    }
    return null;
  } catch {
    return null;
  }
}

export function clearUser(): void {
  storage.delete(KEY);
}
