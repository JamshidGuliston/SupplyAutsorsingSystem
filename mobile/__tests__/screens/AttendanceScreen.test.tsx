import React from 'react';
import { AppState, StyleSheet } from 'react-native';
import { render, fireEvent, act } from '@testing-library/react-native';
import { AttendanceScreen } from '../../src/screens/AttendanceScreen';
import { useAttendanceStore } from '../../src/attendance/attendanceStore';
import { enqueue, clearAll } from '../../src/attendance/attendanceQueue';
import { ensureLocation, ensureCamera } from '../../src/attendance/permissions';
import { getCurrentLocation } from '../../src/attendance/locationService';
import { captureSelfie } from '../../src/attendance/selfieCapture';
import { isMockGpsAllowed } from '../../src/attendance/mockGps';
import { getToday, postCheckIn } from '../../src/attendance/attendanceApi';
import type { AttendanceRow, KindgardenInfo } from '../../src/attendance/attendanceApi';
import { colors } from '../../src/theme/colors';
import { tashkentDateKey } from '../../src/lib/uzTime';

jest.mock('../../src/attendance/permissions', () => ({
  ensureLocation: jest.fn(),
  ensureCamera: jest.fn(),
}));
jest.mock('../../src/attendance/locationService', () => ({ getCurrentLocation: jest.fn() }));
jest.mock('../../src/attendance/selfieCapture', () => ({ captureSelfie: jest.fn() }));
jest.mock('../../src/attendance/mockGps', () => ({ isMockGpsAllowed: jest.fn() }));
jest.mock('../../src/attendance/attendanceApi', () => ({
  getToday: jest.fn(),
  postCheckIn: jest.fn(),
  postCheckOut: jest.fn(),
  postReplace: jest.fn(),
}));

let mockChildrenCount: { data?: { kindgarden: { id: number; kingar_name: string } } } = {};
jest.mock('../../src/api/childrenCount', () => ({
  useChildrenCountToday: () => mockChildrenCount,
}));

const kg: KindgardenInfo = { id: 7, lat: 41.311, lng: 69.279, geofence_radius: 200 };

// Ekran haqiqiy soat bilan ishlaydi: yozuv sanasi standart holda bugungi Toshkent sanasi.
const row = (o: Partial<AttendanceRow> = {}): AttendanceRow => ({
  id: 1,
  user_id: 2,
  kindgarden_id: 7,
  date: tashkentDateKey(new Date()),
  check_in_at: null,
  check_in_lat: null,
  check_in_lng: null,
  check_in_distance_m: null,
  check_in_selfie_path: null,
  check_in_is_late: false,
  check_in_replaced_count: 0,
  check_out_at: null,
  check_out_lat: null,
  check_out_lng: null,
  check_out_distance_m: null,
  check_out_selfie_path: null,
  check_out_replaced_count: 0,
  ...o,
});

const flat = (node: any) => StyleSheet.flatten(node.props.style);

// RN jest mock'idagi AppState.addEventListener — ekran qo'ygan oxirgi 'change' tinglovchisi.
const appStateCall = () => {
  const calls = (AppState.addEventListener as jest.Mock).mock.calls;
  const i = calls.map((c) => c[0]).lastIndexOf('change');
  expect(i).toBeGreaterThanOrEqual(0);
  return i;
};
const appStateListener = (): ((s: string) => void) =>
  (AppState.addEventListener as jest.Mock).mock.calls[appStateCall()][1];
const appStateSubscription = (): { remove: jest.Mock } =>
  (AppState.addEventListener as jest.Mock).mock.results[appStateCall()].value;

const initialStore = useAttendanceStore.getState();
const frozenRefresh = jest.fn();

// Holatni qotiramiz: ekran mount'dagi refresh hech narsani o'zgartirmaydi.
function setStore(over: Partial<ReturnType<typeof useAttendanceStore.getState>>) {
  useAttendanceStore.setState({
    today: null,
    kindgarden: kg,
    loading: false,
    error: null,
    loaded: true,
    refresh: frozenRefresh,
    ...over,
  });
}

// Keldim oqimi uchun qurilma: ruxsatlar bor, GPS bog'cha markazida, selfi olindi.
function deviceReady() {
  (ensureLocation as jest.Mock).mockResolvedValue('granted');
  (ensureCamera as jest.Mock).mockResolvedValue('granted');
  (getCurrentLocation as jest.Mock).mockResolvedValue({ lat: kg.lat, lng: kg.lng, isMock: false });
  (isMockGpsAllowed as jest.Mock).mockResolvedValue(false);
  (captureSelfie as jest.Mock).mockResolvedValue({
    uri: 'file://selfie.jpg',
    fileName: 'selfie.jpg',
    type: 'image/jpeg',
  });
}

describe('AttendanceScreen', () => {
  afterEach(() => {
    jest.useRealTimers();
  });

  beforeEach(() => {
    jest.clearAllMocks();
    clearAll();
    useAttendanceStore.setState(initialStore, true);
    mockChildrenCount = { data: { kindgarden: { id: 7, kingar_name: '12-son DMTT' } } };
  });

  it('birinchi yuklanishgacha skelet, "Keldim" chizilmaydi', () => {
    setStore({ loaded: false, today: null, loading: true });
    const { getByTestId, queryByText } = render(<AttendanceScreen />);
    expect(getByTestId('attendance-skeleton')).toBeTruthy();
    expect(queryByText('Keldim')).toBeNull();
    expect(queryByText('Hali kelmadingiz')).toBeNull();
  });

  it('yuklash xatosida "Ma\'lumotni yuklab bo\'lmadi", "Keldim" chizilmaydi', () => {
    setStore({
      loaded: false,
      error: "Internet bilan bog'lanib bo'lmadi. Qayta urinib ko'ring.",
    });
    const { getByText, queryByText } = render(<AttendanceScreen />);
    expect(getByText("Ma'lumotni yuklab bo'lmadi")).toBeTruthy();
    expect(getByText("Internet bilan bog'lanib bo'lmadi. Qayta urinib ko'ring.")).toBeTruthy();
    expect(queryByText('Keldim')).toBeNull();
    fireEvent.press(getByText('Qayta urinish'));
    expect(frozenRefresh).toHaveBeenCalled();
  });

  it('yuklangandan keyin yangilash xatosi: "Keldim" qoladi, xato bannerda', () => {
    setStore({
      loaded: true,
      today: null,
      error: "Internet bilan bog'lanib bo'lmadi. Qayta urinib ko'ring.",
    });
    const { getByText, queryByText } = render(<AttendanceScreen />);
    expect(queryByText("Ma'lumotni yuklab bo'lmadi")).toBeNull();
    expect(getByText('Keldim')).toBeTruthy();
    expect(getByText('Hali kelmadingiz')).toBeTruthy();
    expect(getByText("Internet bilan bog'lanib bo'lmadi. Qayta urinib ko'ring.")).toBeTruthy();
  });

  it(`do'konda kechagi yozuv qolgan (ilova tunab qolgan) — kechagi to'liq davomat emas, "Keldim"`, () => {
    setStore({
      loaded: true,
      today: row({
        date: tashkentDateKey(new Date(Date.now() - 24 * 60 * 60 * 1000)),
        check_in_at: '2026-11-25T03:02:00.000000Z',
        check_out_at: '2026-11-25T12:12:00.000000Z',
      }),
    });
    const { getByText, queryByText } = render(<AttendanceScreen />);
    expect(getByText('Hali kelmadingiz')).toBeTruthy();
    expect(getByText('Keldim')).toBeTruthy();
    expect(queryByText("Bugungi davomat to'liq. Ertaga ko'rishguncha!")).toBeNull();
    expect(queryByText('Kelishni qayta')).toBeNull();
  });

  it('ilova fondan qaytganda (AppState active) bugungi holat qayta yuklanadi', () => {
    setStore({ loaded: true });
    const { unmount } = render(<AttendanceScreen />);
    const onChange = appStateListener();
    frozenRefresh.mockClear();

    act(() => onChange('background'));
    expect(frozenRefresh).not.toHaveBeenCalled();
    act(() => onChange('active'));
    expect(frozenRefresh).toHaveBeenCalledTimes(1);

    unmount();
    expect(appStateSubscription().remove).toHaveBeenCalled();
  });

  it("jarayon paytida (kamera/ruxsat oynasidan qaytish) AppState active qayta yuklamaydi", () => {
    setStore({ loaded: true });
    (ensureLocation as jest.Mock).mockReturnValue(new Promise(() => {}));
    const { getByText, getByTestId } = render(<AttendanceScreen />);
    fireEvent.press(getByText('Keldim'));
    expect(getByTestId('action-progress')).toBeTruthy();
    frozenRefresh.mockClear();
    act(() => appStateListener()('active'));
    expect(frozenRefresh).not.toHaveBeenCalled();
  });

  it('navbatda bugungi kelish bor — "Yuborilmoqda…", "Keldim" chizilmaydi', () => {
    enqueue({
      kind: 'check_in',
      lat: kg.lat as number,
      lng: kg.lng as number,
      capturedAt: new Date().toISOString(),
      isMock: false,
      photoUri: 'file://a.jpg',
    });
    setStore({ loaded: true, today: null });
    const { getByText, queryByText, getByTestId } = render(<AttendanceScreen />);
    expect(getByText('Yuborilmoqda…')).toBeTruthy();
    expect(getByText(/da olindi · navbatda$/)).toBeTruthy();
    expect(queryByText('Keldim')).toBeNull();
    expect(flat(getByTestId('timeline-line')).backgroundColor).toBe(colors.linePending);
  });

  it('navbatdagi kelish fonda yuborilsa, 5 s ichida bugungi holat qayta yuklanadi', () => {
    jest.useFakeTimers();
    enqueue({
      kind: 'check_in',
      lat: kg.lat as number,
      lng: kg.lng as number,
      capturedAt: new Date().toISOString(),
      isMock: false,
      photoUri: 'file://a.jpg',
    });
    setStore({ loaded: true, today: null });
    const { getByText, queryByText } = render(<AttendanceScreen />);
    expect(getByText('Yuborilmoqda…')).toBeTruthy();
    frozenRefresh.mockClear();

    // Flusher yozuvni yubordi va navbatdan o'chirdi; server endi kelishni qaytaradi.
    frozenRefresh.mockImplementationOnce(async () => {
      useAttendanceStore.setState({ today: row({ check_in_at: '2026-11-26T03:10:00.000000Z' }) });
    });
    clearAll();
    act(() => {
      jest.advanceTimersByTime(5_000);
    });

    expect(frozenRefresh).toHaveBeenCalledTimes(1);
    expect(queryByText('Yuborilmoqda…')).toBeNull();
    expect(getByText('Keldingiz')).toBeTruthy();
    expect(queryByText('Keldim')).toBeNull();

    act(() => {
      jest.advanceTimersByTime(10_000);
    });
    expect(frozenRefresh).toHaveBeenCalledTimes(1);
  });

  it('kelgan va kechikkan — Toshkent vaqti, "kechikkan" va "Ketdim"', () => {
    setStore({
      loaded: true,
      today: row({ check_in_at: '2026-11-26T03:47:00.000000Z', check_in_is_late: true, check_in_distance_m: 45 }),
    });
    const { getByText, getByTestId, queryByText } = render(<AttendanceScreen />);
    expect(getByText('Keldingiz')).toBeTruthy();
    expect(getByText('08:47')).toBeTruthy();
    expect(getByText('kechikkan')).toBeTruthy();
    expect(getByText("Bog'cha markazidan 45 m")).toBeTruthy();
    expect(getByText('Hali ketmadingiz')).toBeTruthy();
    expect(getByText('Ketdim')).toBeTruthy();
    expect(getByText('Kelishni qayta yuborish')).toBeTruthy();
    expect(queryByText('Keldim')).toBeNull();
    expect(flat(getByTestId('timeline-line')).backgroundColor).toBe(colors.lineDone);
  });

  it("ikkalasi — ish vaqti, ikki kichik qayta yuborish va yakuniy izoh", () => {
    setStore({
      loaded: true,
      today: row({
        check_in_at: '2026-11-26T03:02:00.000000Z',
        check_in_distance_m: 38,
        check_out_at: '2026-11-26T12:12:00.000000Z',
        check_out_distance_m: 52,
      }),
    });
    const { getByText, queryByText } = render(<AttendanceScreen />);
    expect(getByText('Ketdingiz')).toBeTruthy();
    expect(getByText('17:12')).toBeTruthy();
    expect(getByText('Bugungi ish vaqti')).toBeTruthy();
    expect(getByText('9 soat 10 daq')).toBeTruthy();
    expect(getByText('Kelishni qayta')).toBeTruthy();
    expect(getByText('Ketishni qayta')).toBeTruthy();
    expect(getByText("Bugungi davomat to'liq. Ertaga ko'rishguncha!")).toBeTruthy();
    expect(queryByText('Ketdim')).toBeNull();
  });

  it("hero: sana, sarlavha, bog'cha nomi bolalar soni so'rovidan va radius", () => {
    setStore({ loaded: true });
    const first = render(<AttendanceScreen />);
    expect(first.getByText('Davomat')).toBeTruthy();
    expect(first.getByText('12-son DMTT')).toBeTruthy();
    expect(first.getByText('200 m radius')).toBeTruthy();
    first.unmount();

    mockChildrenCount = {};
    const second = render(<AttendanceScreen />);
    expect(second.getByText("Bog'cha #7")).toBeTruthy();
    second.unmount();

    setStore({ loaded: true, kindgarden: null });
    const third = render(<AttendanceScreen />);
    expect(third.queryByText("Bog'cha #7")).toBeNull();
    expect(third.queryByText(/m radius$/)).toBeNull();
  });

  it('jarayon paytida tugmalar o\'rnida bitta progress tugma', () => {
    setStore({ loaded: true });
    (ensureLocation as jest.Mock).mockReturnValue(new Promise(() => {}));
    const { getByText, getByTestId, queryByText } = render(<AttendanceScreen />);
    fireEvent.press(getByText('Keldim'));
    expect(getByTestId('action-progress')).toBeTruthy();
    expect(getByText('Ruxsatlar tekshirilmoqda')).toBeTruthy();
    expect(getByText('1/6')).toBeTruthy();
    expect(queryByText('Keldim')).toBeNull();
  });

  it("muvaffaqiyatda banner yo'q, natija vaqt chizig'ida; hech qayerda ✅ yo'q", async () => {
    deviceReady();
    (getToday as jest.Mock)
      .mockResolvedValueOnce({ attendance: null, kindgarden: kg, server_time: '' })
      .mockResolvedValueOnce({
        attendance: row({ check_in_at: '2026-11-26T03:02:00.000000Z', check_in_distance_m: 12 }),
        kindgarden: kg,
        server_time: '',
      });
    (postCheckIn as jest.Mock).mockResolvedValue({ attendance: row() });
    const { findByText, getByText, queryByText } = render(<AttendanceScreen />);
    fireEvent.press(await findByText('Keldim'));
    expect(await findByText('Ketdim')).toBeTruthy();
    expect(getByText('08:02')).toBeTruthy();
    expect(queryByText(/qabul qilindi/)).toBeNull();
    expect(queryByText(/✅/)).toBeNull();
  });

  it('tarmoq xatosida navbatga qo\'shiladi: sariq banner va "Yuborilmoqda…"', async () => {
    deviceReady();
    setStore({ loaded: true });
    (postCheckIn as jest.Mock).mockRejectedValue(new Error('Network Error'));
    const { getByText, findByText, queryByText } = render(<AttendanceScreen />);
    fireEvent.press(getByText('Keldim'));
    expect(await findByText('Yuborilmoqda…')).toBeTruthy();
    expect(getByText(/Navbatga qo'shildi\./)).toBeTruthy();
    expect(getByText(/Internet tiklanganda avtomatik yuboriladi\./)).toBeTruthy();
    expect(queryByText('Keldim')).toBeNull();
  });

  it("server 500 qaytarsa inglizcha 'Server Error' emas, o'zbekcha matn; navbatga qo'shilmaydi", async () => {
    deviceReady();
    setStore({ loaded: true });
    (postCheckIn as jest.Mock).mockRejectedValue({
      response: { status: 500, data: { message: 'Server Error' } },
    });
    const { getByText, findByText, queryByText } = render(<AttendanceScreen />);
    fireEvent.press(getByText('Keldim'));
    expect(await findByText("Serverda xato. Birozdan keyin qayta urinib ko'ring.")).toBeTruthy();
    expect(queryByText('Server Error')).toBeNull();
    expect(queryByText(/Navbatga qo'shildi\./)).toBeNull();
    expect(getByText('Keldim')).toBeTruthy();
  });

  it("server ma'lum kod qaytarsa mapServerError matni o'zgarmagan", async () => {
    deviceReady();
    setStore({ loaded: true });
    (postCheckIn as jest.Mock).mockRejectedValue({
      response: { status: 422, data: { error: 'stale_capture', message: 'Stale' } },
    });
    const { getByText, findByText } = render(<AttendanceScreen />);
    fireEvent.press(getByText('Keldim'));
    expect(
      await findByText("Yuborilgan vaqt server vaqtidan ko'p farq qiladi. Qayta urinib ko'ring."),
    ).toBeTruthy();
  });

  it("qurilma xatosida xom e.message emas, tushunarli matn", async () => {
    deviceReady();
    setStore({ loaded: true });
    (getCurrentLocation as jest.Mock).mockRejectedValue(new Error('GPS error: 3 timeout'));
    const { getByText, findByText, queryByText } = render(<AttendanceScreen />);
    fireEvent.press(getByText('Keldim'));
    expect(
      await findByText("Joylashuvni aniqlab bo'lmadi. GPS yoqilganini tekshirib, qayta urinib ko'ring."),
    ).toBeTruthy();
    // Eski ekran banner'ga xom e.message ni aynan shunday chiqarardi (debug paneli — faqat __DEV__).
    expect(queryByText('GPS error: 3 timeout')).toBeNull();
    expect(getByText('Keldim')).toBeTruthy();
  });

  it("birinchi yuklash xatosi: tushunarli matn, Qayta urinishdan keyin Keldim", async () => {
    (getToday as jest.Mock)
      .mockRejectedValueOnce(new Error('Network Error'))
      .mockResolvedValueOnce({ attendance: null, kindgarden: kg, server_time: '' });
    const { findByText, getByText, queryByText } = render(<AttendanceScreen />);
    expect(await findByText("Ma'lumotni yuklab bo'lmadi")).toBeTruthy();
    expect(getByText("Internet bilan bog'lanib bo'lmadi. Qayta urinib ko'ring.")).toBeTruthy();
    expect(queryByText('Network Error')).toBeNull();
    expect(queryByText('Keldim')).toBeNull();
    fireEvent.press(getByText('Qayta urinish'));
    expect(await findByText('Keldim')).toBeTruthy();
    expect(getByText('Hali kelmadingiz')).toBeTruthy();
  });
});
