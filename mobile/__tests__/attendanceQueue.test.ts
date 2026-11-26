jest.mock('react-native-mmkv', () => {
  const store: Record<string, string> = {};
  return {
    MMKV: jest.fn().mockImplementation(() => ({
      getString: (k: string) => store[k],
      set: (k: string, v: string) => { store[k] = v; },
      delete: (k: string) => { delete store[k]; },
    })),
  };
});

import {
  enqueue,
  peekAll,
  removeById,
  clearAll,
  attendancePendingCount,
  queuedCaptureToday,
} from '../src/attendance/attendanceQueue';

describe('attendanceQueue', () => {
  beforeEach(() => {
    clearAll();
  });

  it('enqueues and lists items in FIFO order', () => {
    enqueue({ kind: 'check_in', lat: 41.31, lng: 69.27, capturedAt: '2026-04-29T08:00:00Z',
              isMock: false, photoUri: 'file://a.jpg' });
    enqueue({ kind: 'check_out', lat: 41.31, lng: 69.27, capturedAt: '2026-04-29T17:00:00Z',
              isMock: false, photoUri: 'file://b.jpg' });
    const list = peekAll();
    expect(list).toHaveLength(2);
    expect(list[0].kind).toBe('check_in');
    expect(list[1].kind).toBe('check_out');
    expect(typeof list[0].id).toBe('string');
  });

  it('removeById drops only the targeted item', () => {
    enqueue({ kind: 'check_in', lat: 41.31, lng: 69.27, capturedAt: '2026-04-29T08:00:00Z',
              isMock: false, photoUri: 'file://a.jpg' });
    enqueue({ kind: 'check_in', lat: 41.31, lng: 69.27, capturedAt: '2026-04-29T08:01:00Z',
              isMock: false, photoUri: 'file://b.jpg' });
    const [first, second] = peekAll();
    removeById(first.id);
    const remaining = peekAll();
    expect(remaining).toHaveLength(1);
    expect(remaining[0].id).toBe(second.id);
  });
});

describe('attendancePendingCount', () => {
  beforeEach(() => {
    clearAll();
  });

  it('faqat davomat yozuvlarini sanaydi (location_events hisobga olinmaydi)', () => {
    enqueue({ kind: 'location_events', lat: 41.31, lng: 69.27, capturedAt: '2026-11-26T03:00:00Z',
              isMock: false, events: [] });
    enqueue({ kind: 'check_in', lat: 41.31, lng: 69.27, capturedAt: '2026-11-26T03:05:00Z',
              isMock: false, photoUri: 'file://a.jpg' });
    expect(attendancePendingCount()).toBe(1);
  });

  it("navbat bo'sh bo'lsa 0", () => {
    expect(attendancePendingCount()).toBe(0);
  });
});

describe('queuedCaptureToday', () => {
  const now = new Date('2026-11-26T05:00:00Z');

  beforeEach(() => {
    clearAll();
  });

  it('bugungi replace_check_in ni kelish sifatida topadi', () => {
    enqueue({ kind: 'replace_check_in', lat: 41.31, lng: 69.27, capturedAt: '2026-11-26T03:10:00Z',
              isMock: false, photoUri: 'file://a.jpg' });
    expect(queuedCaptureToday('check_in', now)).toBe('2026-11-26T03:10:00Z');
    expect(queuedCaptureToday('check_out', now)).toBeNull();
  });

  it('kechagi yozuv hisobga olinmaydi', () => {
    enqueue({ kind: 'check_in', lat: 41.31, lng: 69.27, capturedAt: '2026-11-25T03:10:00Z',
              isMock: false, photoUri: 'file://a.jpg' });
    expect(queuedCaptureToday('check_in', now)).toBeNull();
  });

  it('sana Toshkent bo\'yicha: 25-noyabr 19:30Z (Toshkentda 26-noyabr 00:30) — bugungi', () => {
    enqueue({ kind: 'check_in', lat: 41.31, lng: 69.27, capturedAt: '2026-11-25T19:30:00Z',
              isMock: false, photoUri: 'file://a.jpg' });
    expect(queuedCaptureToday('check_in', now)).toBe('2026-11-25T19:30:00Z');
  });

  it('bir nechta bugungi yozuv bo\'lsa eng oxirgisini qaytaradi', () => {
    enqueue({ kind: 'check_out', lat: 41.31, lng: 69.27, capturedAt: '2026-11-26T04:00:00Z',
              isMock: false, photoUri: 'file://a.jpg' });
    enqueue({ kind: 'replace_check_out', lat: 41.31, lng: 69.27, capturedAt: '2026-11-26T04:30:00Z',
              isMock: false, photoUri: 'file://b.jpg' });
    expect(queuedCaptureToday('check_out', now)).toBe('2026-11-26T04:30:00Z');
  });

  it('location_events yozuvlari hisobga olinmaydi', () => {
    enqueue({ kind: 'location_events', lat: 41.31, lng: 69.27, capturedAt: '2026-11-26T03:00:00Z',
              isMock: false, events: [] });
    expect(queuedCaptureToday('check_in', now)).toBeNull();
  });
});
