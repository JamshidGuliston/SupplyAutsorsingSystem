// FCM disabled for MVP build (no google-services.json).
// Plan 5 will restore @react-native-firebase/messaging integration.

export async function requestPushPermission(): Promise<boolean> {
  return false;
}

export async function registerDeviceWithBackend(_appVersion: string): Promise<void> {
  // no-op
}
