import { create } from 'zustand';
import { AttendanceRow, KindgardenInfo, getToday } from './attendanceApi';

interface AttendanceState {
  today: AttendanceRow | null;
  kindgarden: KindgardenInfo | null;
  loading: boolean;
  error: string | null;
  refresh: () => Promise<void>;
}

export const useAttendanceStore = create<AttendanceState>((set) => ({
  today: null,
  kindgarden: null,
  loading: false,
  error: null,
  refresh: async () => {
    set({ loading: true, error: null });
    try {
      const r = await getToday();
      set({ today: r.attendance, kindgarden: r.kindgarden, loading: false });
    } catch (e: any) {
      set({ loading: false, error: e?.message ?? 'Yuklab bo\'lmadi' });
    }
  },
}));
