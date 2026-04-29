// react-native-mmkv v4 only exports `MMKV` as a type; runtime construction
// goes through `createMMKV`. Tests mock the module to expose `MMKV` as a
// constructor, so we keep that shape and resolve the value lazily.
// eslint-disable-next-line @typescript-eslint/no-var-requires
const mmkvModule: any = require('react-native-mmkv');
const MMKVCtor: any = mmkvModule.MMKV ?? mmkvModule.createMMKV;

const KEY = 'attendance.queue.v1';
const storage: {
  getString: (k: string) => string | undefined;
  set: (k: string, v: string) => void;
  delete: (k: string) => void;
} = mmkvModule.MMKV
  ? new MMKVCtor({ id: 'attendance' })
  : MMKVCtor({ id: 'attendance' });

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
