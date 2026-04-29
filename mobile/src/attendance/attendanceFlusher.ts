import NetInfo from '@react-native-community/netinfo';
import { peekAll, removeById, QueuedItem } from './attendanceQueue';
import { postCheckIn, postCheckOut, postReplace, postLocationEvents } from './attendanceApi';

let running = false;
let intervalHandle: ReturnType<typeof setInterval> | null = null;

async function trySend(item: QueuedItem): Promise<'sent' | 'permanent_fail' | 'transient_fail'> {
  try {
    if (item.kind === 'check_in') {
      await postCheckIn({
        lat: item.lat, lng: item.lng, capturedAt: item.capturedAt,
        isMock: item.isMock, photoUri: item.photoUri!,
        photoFileName: 'selfie.jpg', photoMimeType: 'image/jpeg',
      });
    } else if (item.kind === 'check_out') {
      await postCheckOut({
        lat: item.lat, lng: item.lng, capturedAt: item.capturedAt,
        isMock: item.isMock, photoUri: item.photoUri!,
        photoFileName: 'selfie.jpg', photoMimeType: 'image/jpeg',
      });
    } else if (item.kind === 'replace_check_in') {
      await postReplace('check_in', {
        lat: item.lat, lng: item.lng, capturedAt: item.capturedAt,
        isMock: item.isMock, photoUri: item.photoUri!,
        photoFileName: 'selfie.jpg', photoMimeType: 'image/jpeg',
      });
    } else if (item.kind === 'replace_check_out') {
      await postReplace('check_out', {
        lat: item.lat, lng: item.lng, capturedAt: item.capturedAt,
        isMock: item.isMock, photoUri: item.photoUri!,
        photoFileName: 'selfie.jpg', photoMimeType: 'image/jpeg',
      });
    } else if (item.kind === 'location_events') {
      await postLocationEvents(item.events!);
    }
    return 'sent';
  } catch (e: any) {
    const status = e?.response?.status;
    if (status && status >= 400 && status < 500 && status !== 401) {
      return 'permanent_fail';
    }
    return 'transient_fail';
  }
}

export async function flushOnce(): Promise<{ sent: number; failed: number; remaining: number }> {
  if (running) return { sent: 0, failed: 0, remaining: peekAll().length };
  running = true;
  let sent = 0, failed = 0;
  try {
    const net = await NetInfo.fetch();
    if (!net.isConnected) return { sent: 0, failed: 0, remaining: peekAll().length };
    for (const item of peekAll()) {
      const result = await trySend(item);
      if (result === 'sent' || result === 'permanent_fail') {
        removeById(item.id);
        if (result === 'sent') sent++;
        else failed++;
      } else {
        break;
      }
    }
  } finally {
    running = false;
  }
  return { sent, failed, remaining: peekAll().length };
}

export function startFlusher(intervalMs: number = 30_000): void {
  if (intervalHandle) return;
  intervalHandle = setInterval(() => { void flushOnce(); }, intervalMs);
  NetInfo.addEventListener((state) => {
    if (state.isConnected) void flushOnce();
  });
}

export function stopFlusher(): void {
  if (intervalHandle) {
    clearInterval(intervalHandle);
    intervalHandle = null;
  }
}
