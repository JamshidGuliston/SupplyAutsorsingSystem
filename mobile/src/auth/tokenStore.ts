import * as Keychain from 'react-native-keychain';

const SERVICE = 'chef-mobile-auth';

export async function saveToken(token: string): Promise<void> {
  await Keychain.setGenericPassword('token', token, { service: SERVICE });
}

export async function loadToken(): Promise<string | null> {
  const result = await Keychain.getGenericPassword({ service: SERVICE });
  if (result === false || !result) return null;
  return result.password;
}

export async function clearToken(): Promise<void> {
  await Keychain.resetGenericPassword({ service: SERVICE });
}
