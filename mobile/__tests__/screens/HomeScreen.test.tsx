import React from 'react';
import { RefreshControl } from 'react-native';
import { render, fireEvent, act } from '@testing-library/react-native';
import { QueryClient, QueryClientProvider } from '@tanstack/react-query';
import { HomeScreen } from '../../src/screens/HomeScreen';
import { api } from '../../src/api/client';
import type { ChildrenCountState, TimeWindow } from '../../src/api/childrenCount';

// getTodayState / submitChildrenCount shu axios nusxasi orqali ishlaydi. Ularni tarmoq chegarasida
// soxtalashtiramiz: useChildrenCountToday getTodayState ni o'z modulidan chaqiradi, uni modul
// mock'i bilan almashtirib bo'lmaydi.
jest.mock('../../src/api/client', () => ({ api: { get: jest.fn(), post: jest.fn() } }));

let mockUser: { name: string } | null = null;
jest.mock('../../src/auth/authStore', () => ({
  useAuthStore: (sel: (s: any) => any) => sel({ user: mockUser }),
}));

const getToday = api.get as jest.Mock;
const post = api.post as jest.Mock;

const w: TimeWindow = { allowed: true, from: '03:00', to: '21:00', current_tashkent: '14:25' };

const state = (over: Partial<ChildrenCountState> = {}): ChildrenCountState => ({
  kindgarden: { id: 7, kingar_name: '12-son DMTT' },
  age_ranges: [
    { id: 1, age_name: '1–3 yosh' },
    { id: 2, age_name: '3–7 yosh' },
    { id: 3, age_name: 'Qisqa muddatli' },
  ],
  submitted: false,
  today_counts: null,
  submitted_at: null,
  time_window: w,
  nextday_ready: true,
  ...over,
});

function renderHome() {
  const client = new QueryClient({
    defaultOptions: {
      queries: { retry: false, gcTime: Infinity },
      mutations: { retry: false, gcTime: Infinity },
    },
  });
  return render(
    <QueryClientProvider client={client}>
      <HomeScreen />
    </QueryClientProvider>,
  );
}

describe('HomeScreen', () => {
  beforeEach(() => {
    jest.clearAllMocks();
    mockUser = { name: 'Dilnoza Aliyeva' };
  });

  it("hero'da to'liq ismli salomlashuv va bog'cha nomi", async () => {
    getToday.mockResolvedValue({ data: state() });
    const { findByText, getByText } = renderHome();
    expect(await findByText('12-son DMTT')).toBeTruthy();
    expect(getByText(/^Xayrli (tong|kun|kech), Dilnoza Aliyeva$/)).toBeTruthy();
    expect(getToday).toHaveBeenCalledWith('/chef/children-count/today');
  });

  it("ism yo'q bo'lsa salomlashuv ismsiz, yuklanayotganda nom o'rnida skelet", () => {
    mockUser = null;
    getToday.mockReturnValue(new Promise(() => {}));
    const { getByText, getByTestId, queryByText } = renderHome();
    expect(getByText(/^Xayrli (tong|kun|kech)!$/)).toBeTruthy();
    expect(getByTestId('home-hero-skeleton')).toBeTruthy();
    expect(getByTestId('count-skeleton')).toBeTruthy();
    expect(queryByText('Yuborish')).toBeNull();
  });

  it("forma ochiq: Jami faqat to'ldirilganlarni qo'shadi, bo'sh maydon bilan so'rov ketmaydi", async () => {
    getToday.mockResolvedValue({ data: state() });
    const { findByLabelText, getByLabelText, getByText } = renderHome();
    const first = await findByLabelText('1–3 yosh');
    expect(getByText('Bolalar soni')).toBeTruthy();
    expect(getByText('Ertangi menyu uchun')).toBeTruthy();
    expect(getByText('03:00–21:00 ochiq')).toBeTruthy();
    expect(getByText('—')).toBeTruthy();

    fireEvent.changeText(first, '18');
    fireEvent.changeText(getByLabelText('3–7 yosh'), '42');
    expect(getByText('60 bola')).toBeTruthy();

    fireEvent.press(getByText('Yuborish'));
    expect(getByText('"Qisqa muddatli" uchun butun musbat son kiriting.')).toBeTruthy();
    expect(post).not.toHaveBeenCalled();
  });

  it("hammasi to'ldirilsa yuboradi va Yuborildi holatiga o'tadi", async () => {
    getToday.mockResolvedValue({ data: state() });
    post.mockResolvedValue({
      data: state({ submitted: true, today_counts: { '1': 18, '2': 42, '3': 5 } }),
    });
    const { findByLabelText, getByLabelText, getByText, findByText } = renderHome();
    fireEvent.changeText(await findByLabelText('1–3 yosh'), '18');
    fireEvent.changeText(getByLabelText('3–7 yosh'), '42');
    fireEvent.changeText(getByLabelText('Qisqa muddatli'), '5');
    fireEvent.press(getByText('Yuborish'));
    expect(await findByText('Yuborildi')).toBeTruthy();
    expect(post).toHaveBeenCalledWith('/chef/children-count', { counts: { 1: 18, 2: 42, 3: 5 } });
    expect(getByText('65')).toBeTruthy();
  });

  it("yuborishda tarmoq xatosi: tushunarli banner, texnik matn yo'q", async () => {
    getToday.mockResolvedValue({ data: state() });
    post.mockRejectedValue(new Error('Network Error'));
    const { findByLabelText, getByLabelText, getByText, findByText, queryByText } = renderHome();
    fireEvent.changeText(await findByLabelText('1–3 yosh'), '18');
    fireEvent.changeText(getByLabelText('3–7 yosh'), '42');
    fireEvent.changeText(getByLabelText('Qisqa muddatli'), '5');
    fireEvent.press(getByText('Yuborish'));
    expect(await findByText("Internet bilan bog'lanib bo'lmadi. Qayta urinib ko'ring.")).toBeTruthy();
    expect(queryByText('Network Error')).toBeNull();
    expect(post).toHaveBeenCalledTimes(1);
  });

  it("yuborishda server xatosi matni mapServerError orqali o'zgarmagan", async () => {
    getToday.mockResolvedValue({ data: state() });
    post.mockRejectedValue({ response: { status: 422, data: { error: 'time_window_closed' } } });
    const { findByLabelText, getByLabelText, getByText, findByText } = renderHome();
    fireEvent.changeText(await findByLabelText('1–3 yosh'), '18');
    fireEvent.changeText(getByLabelText('3–7 yosh'), '42');
    fireEvent.changeText(getByLabelText('Qisqa muddatli'), '5');
    fireEvent.press(getByText('Yuborish'));
    expect(
      await findByText("Hozir yuborish vaqti emas (03:00 - 21:00 oralig'ida bo'lishi kerak)."),
    ).toBeTruthy();
  });

  it("yuborilgan va vaqt yopiq: Yuborildi ko'rinadi, yopiq xabari yo'q", async () => {
    getToday.mockResolvedValue({
      data: state({
        submitted: true,
        today_counts: { '1': 18, '2': 42 },
        time_window: { ...w, allowed: false, current_tashkent: '21:30' },
      }),
    });
    const { findByText, getByText, queryByText } = renderHome();
    expect(await findByText('Yuborildi')).toBeTruthy();
    expect(queryByText('Hozir yuborish vaqti emas')).toBeNull();
    expect(getByText('—')).toBeTruthy();
    expect(getByText('Jami')).toBeTruthy();
    expect(getByText('60')).toBeTruthy();
    expect(getByText("O'zgartirish kerak bo'lsa, texnologga murojaat qiling.")).toBeTruthy();
  });

  it("vaqt yopiq: oraliq, hozirgi vaqt va keyingi ochilish", async () => {
    getToday.mockResolvedValue({
      data: state({ time_window: { ...w, allowed: false, current_tashkent: '21:30' } }),
    });
    const { findByText, getByText, queryByText } = renderHome();
    expect(await findByText('Hozir yuborish vaqti emas')).toBeTruthy();
    expect(getByText("Bolalar soni har kuni 03:00 – 21:00 oralig'ida yuboriladi")).toBeTruthy();
    expect(getByText('Hozir: 21:30')).toBeTruthy();
    expect(getByText('Ertaga 03:00 da ochiladi')).toBeTruthy();
    expect(getByText('Pastga torting — yangilash')).toBeTruthy();
    expect(queryByText('Yuborish')).toBeNull();
  });

  it("menyu tayyor emas va yosh guruhlari yo'q matnlari", async () => {
    getToday.mockResolvedValue({ data: state({ nextday_ready: false }) });
    const first = renderHome();
    expect(await first.findByText('Menyu hali tayyor emas')).toBeTruthy();
    expect(
      first.getByText('Texnolog kelajak kun uchun menyu sozlamagan. Texnologga murojaat qiling.'),
    ).toBeTruthy();
    first.unmount();

    getToday.mockResolvedValue({ data: state({ age_ranges: [] }) });
    const second = renderHome();
    expect(await second.findByText('Yosh guruhlari biriktirilmagan')).toBeTruthy();
    expect(
      second.getByText("Bog'changizga yosh guruhlari biriktirilmagan. Texnologga murojaat qiling."),
    ).toBeTruthy();
    expect(second.queryByText('Yuborish')).toBeNull();
  });

  it("yuklab bo'lmasa tushunarli xabar va Qayta urinish, texnik matn yo'q", async () => {
    getToday.mockRejectedValue(new Error('Network Error'));
    const { findByText, getByText, queryByText } = renderHome();
    expect(await findByText("Ma'lumotni yuklab bo'lmadi")).toBeTruthy();
    expect(getByText("Internet bilan bog'lanib bo'lmadi. Qayta urinib ko'ring.")).toBeTruthy();
    expect(queryByText('Network Error')).toBeNull();

    getToday.mockResolvedValue({ data: state() });
    fireEvent.press(getByText('Qayta urinish'));
    expect(await findByText('Yuborish')).toBeTruthy();
    expect(getToday).toHaveBeenCalledTimes(2);
  });

  it("yuklangandan keyin yangilash xatosi: yarim to'ldirilgan forma qoladi, ogohlantirish chiqadi", async () => {
    getToday.mockResolvedValueOnce({ data: state() });
    const screen = renderHome();
    fireEvent.changeText(await screen.findByLabelText('1–3 yosh'), '18');

    // Pastga tortib yangilash oflaynda: react-query 5 data ni saqlaydi, lekin error ni ham qo'yadi.
    getToday.mockRejectedValue(new Error('Network Error'));
    await act(async () => {
      await screen.UNSAFE_getByType(RefreshControl).props.onRefresh();
    });

    expect(screen.queryByText("Ma'lumotni yuklab bo'lmadi")).toBeNull();
    expect(screen.getByLabelText('1–3 yosh').props.value).toBe('18');
    expect(screen.getByText('Yuborish')).toBeTruthy();
    expect(screen.getByText("Internet bilan bog'lanib bo'lmadi. Qayta urinib ko'ring.")).toBeTruthy();
    expect(screen.queryByText('Network Error')).toBeNull();
    expect(getToday).toHaveBeenCalledTimes(2);
  });

  it("yuklangandan keyin yangilash xatosi: Yuborildi kartasi qoladi", async () => {
    getToday.mockResolvedValueOnce({
      data: state({ submitted: true, today_counts: { '1': 18, '2': 42, '3': 5 } }),
    });
    const screen = renderHome();
    expect(await screen.findByText('Yuborildi')).toBeTruthy();

    getToday.mockRejectedValue({ response: { status: 500, data: { message: 'Server Error' } } });
    await act(async () => {
      await screen.UNSAFE_getByType(RefreshControl).props.onRefresh();
    });

    expect(screen.queryByText("Ma'lumotni yuklab bo'lmadi")).toBeNull();
    expect(screen.getByText('Yuborildi')).toBeTruthy();
    expect(screen.getByText('65')).toBeTruthy();
  });

  it("bog'cha biriktirilmagan bo'lsa Qayta urinish ko'rsatilmaydi", async () => {
    getToday.mockRejectedValue({ response: { status: 403, data: { error: 'kindgarden_not_assigned' } } });
    const { findByText, queryByText } = renderHome();
    expect(
      await findByText("Sizning hisobingizga bog'cha biriktirilmagan. Addelkadirga murojaat qiling."),
    ).toBeTruthy();
    expect(queryByText('Qayta urinish')).toBeNull();
  });
});
