jest.mock('../../src/attendance/attendanceApi', () => ({ getToday: jest.fn() }));

import { useAttendanceStore } from '../../src/attendance/attendanceStore';
import { getToday } from '../../src/attendance/attendanceApi';
import type { AttendanceTodayResponse } from '../../src/attendance/attendanceApi';

const kg = { id: 7, lat: 41.3, lng: 69.2, geofence_radius: 200 };
const initial = useAttendanceStore.getState();

describe('attendanceStore', () => {
  beforeEach(() => {
    jest.clearAllMocks();
    useAttendanceStore.setState(initial, true);
  });

  it("reset — boshlang'ich holat", async () => {
    (getToday as jest.Mock).mockResolvedValue({ attendance: null, kindgarden: kg, server_time: '' });
    await useAttendanceStore.getState().refresh();
    expect(useAttendanceStore.getState().loaded).toBe(true);
    useAttendanceStore.setState({ error: 'x' });

    useAttendanceStore.getState().reset();

    const s = useAttendanceStore.getState();
    expect(s).toMatchObject({ today: null, kindgarden: null, loading: false, loaded: false, error: null });
  });

  it("chiqish paytida ketayotgan so'rov javobi tozalangan do'konga yozilmaydi", async () => {
    let resolve: (r: AttendanceTodayResponse) => void = () => {};
    (getToday as jest.Mock).mockReturnValue(new Promise((r) => (resolve = r)));
    const pending = useAttendanceStore.getState().refresh();
    expect(useAttendanceStore.getState().loading).toBe(true);

    useAttendanceStore.getState().reset();
    resolve({ attendance: null, kindgarden: kg, server_time: '' });
    await pending;

    const s = useAttendanceStore.getState();
    expect(s.kindgarden).toBeNull();
    expect(s.loaded).toBe(false);
    expect(s.loading).toBe(false);

    // Keyingi (yangi hisob) yuklash odatdagidek ishlaydi.
    (getToday as jest.Mock).mockResolvedValue({ attendance: null, kindgarden: kg, server_time: '' });
    await useAttendanceStore.getState().refresh();
    expect(useAttendanceStore.getState().kindgarden).toEqual(kg);
    expect(useAttendanceStore.getState().loaded).toBe(true);
  });

  it("chiqish paytida ketayotgan so'rov xatosi ham yozilmaydi", async () => {
    let reject: (e: unknown) => void = () => {};
    (getToday as jest.Mock).mockReturnValue(new Promise((_r, j) => (reject = j)));
    const pending = useAttendanceStore.getState().refresh();
    useAttendanceStore.getState().reset();
    reject(new Error('Network Error'));
    await pending;
    expect(useAttendanceStore.getState().error).toBeNull();
    expect(useAttendanceStore.getState().loading).toBe(false);
  });
});
