import {
  PERMISSIONS,
  RESULTS,
  request,
  check,
  Permission,
} from 'react-native-permissions';
import { Platform } from 'react-native';

const LOCATION = Platform.OS === 'android'
  ? PERMISSIONS.ANDROID.ACCESS_FINE_LOCATION
  : PERMISSIONS.IOS.LOCATION_WHEN_IN_USE;
const CAMERA = Platform.OS === 'android'
  ? PERMISSIONS.ANDROID.CAMERA
  : PERMISSIONS.IOS.CAMERA;

export type PermissionStatus = 'granted' | 'denied' | 'blocked';

async function ensureOne(p: Permission): Promise<PermissionStatus> {
  const current = await check(p);
  if (current === RESULTS.GRANTED) return 'granted';
  if (current === RESULTS.BLOCKED) return 'blocked';
  const next = await request(p);
  if (next === RESULTS.GRANTED) return 'granted';
  if (next === RESULTS.BLOCKED) return 'blocked';
  return 'denied';
}

export async function ensureLocation(): Promise<PermissionStatus> {
  return ensureOne(LOCATION);
}

export async function ensureCamera(): Promise<PermissionStatus> {
  return ensureOne(CAMERA);
}
