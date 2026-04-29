jest.mock('react-native-keychain', () => {
  let stored: string | null = null;
  return {
    setGenericPassword: jest.fn((_user: string, pw: string) => {
      stored = pw;
      return Promise.resolve(true);
    }),
    getGenericPassword: jest.fn(() => Promise.resolve(stored ? { username: 'token', password: stored } : false)),
    resetGenericPassword: jest.fn(() => {
      stored = null;
      return Promise.resolve(true);
    }),
  };
});

import { saveToken, loadToken, clearToken } from '../src/auth/tokenStore';

describe('tokenStore', () => {
  beforeEach(async () => {
    await clearToken();
  });

  it('saves and loads a token', async () => {
    await saveToken('abc-123');
    const loaded = await loadToken();
    expect(loaded).toBe('abc-123');
  });

  it('returns null when no token stored', async () => {
    const loaded = await loadToken();
    expect(loaded).toBeNull();
  });

  it('clears the stored token', async () => {
    await saveToken('abc');
    await clearToken();
    expect(await loadToken()).toBeNull();
  });
});
