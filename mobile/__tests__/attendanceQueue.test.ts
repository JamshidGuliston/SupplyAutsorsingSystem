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

import { enqueue, peekAll, removeById, clearAll } from '../src/attendance/attendanceQueue';

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
