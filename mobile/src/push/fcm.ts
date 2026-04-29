import messaging from '@react-native-firebase/messaging';
import { Platform, PermissionsAndroid } from 'react-native';
import { api } from '../api/client';

export async function requestPushPermission(): Promise<boolean> {
  if (Platform.OS === 'android' && Platform.Version >= 33) {
    const r = await PermissionsAndroid.request(
      PermissionsAndroid.PERMISSIONS.POST_NOTIFICATIONS,
    );
    if (r !== PermissionsAndroid.RESULTS.GRANTED) return false;
  }
  const status = await messaging().requestPermission();
  return (
    status === messaging.AuthorizationStatus.AUTHORIZED ||
    status === messaging.AuthorizationStatus.PROVISIONAL
  );
}

export async function registerDeviceWithBackend(appVersion: string): Promise<void> {
  const allowed = await requestPushPermission();
  if (!allowed) return;
  const fcmToken = await messaging().getToken();
  if (!fcmToken) return;
  await api.post('/auth/device', {
    platform: 'android',
    fcm_token: fcmToken,
    device_model: 'unknown',
    app_version: appVersion,
  });
}
