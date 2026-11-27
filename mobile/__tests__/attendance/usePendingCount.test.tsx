import { renderHook, act } from '@testing-library/react-native';
import { usePendingCount } from '../../src/attendance/usePendingCount';
import { enqueue, clearAll } from '../../src/attendance/attendanceQueue';

const item = (kind: 'check_in' | 'location_events') => ({
  kind,
  lat: 41.31,
  lng: 69.27,
  capturedAt: '2026-11-26T03:10:00Z',
  isMock: false,
  photoUri: 'file://a.jpg',
});

describe('usePendingCount', () => {
  beforeEach(() => {
    clearAll();
    jest.useFakeTimers();
  });
  afterEach(() => {
    jest.useRealTimers();
  });

  it("boshlang'ich qiymat navbatdan, keyin har 5 s da yangilanadi (joylashuv hodisalarisiz)", () => {
    enqueue(item('check_in'));
    const { result, unmount } = renderHook(() => usePendingCount());
    expect(result.current.pending).toBe(1);

    enqueue(item('check_in'));
    enqueue(item('location_events'));
    act(() => {
      jest.advanceTimersByTime(4_999);
    });
    expect(result.current.pending).toBe(1);
    act(() => {
      jest.advanceTimersByTime(1);
    });
    expect(result.current.pending).toBe(2);
    unmount();
  });

  it('refresh darhol qayta o\'qiydi va sonni qaytaradi', () => {
    const { result } = renderHook(() => usePendingCount());
    expect(result.current.pending).toBe(0);
    enqueue(item('check_in'));
    let n = -1;
    act(() => {
      n = result.current.refresh();
    });
    expect(n).toBe(1);
    expect(result.current.pending).toBe(1);
  });

  it('interval berilsa shuncha tez-tez', () => {
    const { result } = renderHook(() => usePendingCount(1_000));
    enqueue(item('check_in'));
    act(() => {
      jest.advanceTimersByTime(1_000);
    });
    expect(result.current.pending).toBe(1);
  });
});
