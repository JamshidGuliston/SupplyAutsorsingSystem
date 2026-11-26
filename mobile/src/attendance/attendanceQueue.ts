import { MMKV } from 'react-native-mmkv';
import { parseServerIso, tashkentDateKey } from '../lib/uzTime';

const KEY = 'attendance.queue.v1';
const storage = new MMKV({ id: 'attendance' });

export type QueueKind = 'check_in' | 'check_out' | 'replace_check_in' | 'replace_check_out' | 'location_events';

export interface QueuedItem {
  id: string;
  kind: QueueKind;
  lat: number;
  lng: number;
  capturedAt: string;
  isMock: boolean;
  photoUri?: string;
  events?: Array<{
    event_type: 'exit' | 'enter' | 'beacon';
    lat: number;
    lng: number;
    happened_at: string;
    is_mock: boolean;
  }>;
}

function genId(): string {
  return `${Date.now()}_${Math.random().toString(36).slice(2, 10)}`;
}

function readAll(): QueuedItem[] {
  const raw = storage.getString(KEY);
  if (!raw) return [];
  try {
    return JSON.parse(raw) as QueuedItem[];
  } catch {
    return [];
  }
}

function writeAll(items: QueuedItem[]): void {
  storage.set(KEY, JSON.stringify(items));
}

export function enqueue(item: Omit<QueuedItem, 'id'>): QueuedItem {
  const queued: QueuedItem = { ...item, id: genId() };
  writeAll([...readAll(), queued]);
  return queued;
}

export function peekAll(): QueuedItem[] {
  return readAll();
}

export function removeById(id: string): void {
  writeAll(readAll().filter((i) => i.id !== id));
}

export function clearAll(): void {
  storage.delete(KEY);
}

// Profil va "Internet yo'q" kapsulasi uchun: faqat davomat yozuvlari (joylashuv hodisalarisiz).
export function attendancePendingCount(): number {
  return readAll().filter((i) => i.kind !== 'location_events').length;
}

// Navbatdagi bugungi (Toshkent sanasi bo'yicha) kelish/ketishning eng oxirgisi — capturedAt, yo'q bo'lsa null.
export function queuedCaptureToday(kind: 'check_in' | 'check_out', now: Date = new Date()): string | null {
  const today = tashkentDateKey(now);
  const items = readAll();
  for (let i = items.length - 1; i >= 0; i--) {
    const item = items[i];
    if (item.kind !== kind && item.kind !== `replace_${kind}`) continue;
    const captured = parseServerIso(item.capturedAt);
    if (captured && tashkentDateKey(captured) === today) return item.capturedAt;
  }
  return null;
}
