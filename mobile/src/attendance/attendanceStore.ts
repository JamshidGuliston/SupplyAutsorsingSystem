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
  // Chiqishda: keyingi hisob oldingisining bog'chasi va geozonasini ko'rmasin.
  reset: () => void;
}

const EMPTY = { today: null, kindgarden: null, loading: false, loaded: false, error: null };

// reset() dan oldin boshlangan so'rov javobi tozalangan do'konga yozilmaydi.
let generation = 0;

export const useAttendanceStore = create<AttendanceState>((set) => ({
  ...EMPTY,
  refresh: async () => {
    const gen = generation;
    set({ loading: true, error: null });
    try {
      const r = await getToday();
      if (gen !== generation) return;
      set({ today: r.attendance, kindgarden: r.kindgarden, loading: false, loaded: true });
    } catch (e) {
      if (gen !== generation) return;
      set({ loading: false, error: userFacingError(e) });
    }
  },
  reset: () => {
    generation += 1;
    set(EMPTY);
  },
}));
