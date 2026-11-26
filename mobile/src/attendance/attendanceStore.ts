import { create } from 'zustand';
import { AttendanceRow, KindgardenInfo, getToday } from './attendanceApi';
import { userFacingError } from '../api/errors';

interface AttendanceState {
  today: AttendanceRow | null;
  kindgarden: KindgardenInfo | null;
  loading: boolean;
  // Bugungi holat kamida bir marta muvaffaqiyatli yuklanganmi. Yuklanmaguncha `today === null`
  // "hali kelmagan" degani emas — ekran "Keldim" ni chizmaydi.
  loaded: boolean;
  error: string | null;
  refresh: () => Promise<void>;
}

export const useAttendanceStore = create<AttendanceState>((set) => ({
  today: null,
  kindgarden: null,
  loading: false,
  loaded: false,
  error: null,
  refresh: async () => {
    set({ loading: true, error: null });
    try {
      const r = await getToday();
      set({ today: r.attendance, kindgarden: r.kindgarden, loading: false, loaded: true });
    } catch (e) {
      set({ loading: false, error: userFacingError(e) });
    }
  },
}));
