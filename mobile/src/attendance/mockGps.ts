import { NativeModules, Platform } from 'react-native';

const native = NativeModules.MockGps;

/**
 * Whether the current Android system has mock locations enabled in developer
 * settings (Settings.Secure.ALLOW_MOCK_LOCATION). Note: on Android 6+ this
 * flag is less reliable; we ALSO check pos.mocked from the geolocation library.
 */
export async function isMockGpsAllowed(): Promise<boolean> {
  if (Platform.OS !== 'android' || !native) return false;
  try {
    return await native.isAllowed();
  } catch {
    return false;
  }
}
