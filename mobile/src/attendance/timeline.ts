import type { AttendanceRow } from './attendanceApi';
import { tashkentDateKey, utcIsoToTashkentHHMM, workDuration } from '../lib/uzTime';

export type StepState =
  | { kind: 'pending' }
  | { kind: 'locked' }
  | { kind: 'queued'; capturedHHMM: string }
  | { kind: 'done'; timeHHMM: string; distanceM: number | null; late: boolean };

export type Timeline = { checkIn: StepState; checkOut: StepState; workDuration: string | null };

export type ActionKind = 'check_in' | 'check_out' | 'replace_check_in' | 'replace_check_out';

export type AttendanceButton = {
  kind: ActionKind;
  label: string;
  variant: 'primary' | 'outline';
  size: 'md' | 'sm';
  icon?: 'camera';
};

const CHECK_IN_HINT =
  'Selfi va joylashuv avtomatik olinadi. Kechiksangiz ham bog\'chada turib bosing — tizim "kechikkan" deb belgilaydi.';
const COMPLETE_HINT = "Bugungi davomat to'liq. Ertaga ko'rishguncha!";

const done = (at: string, distanceM: number | null, late: boolean): StepState => ({
  kind: 'done',
  timeHHMM: utcIsoToTashkentHHMM(at) ?? '—:—',
  distanceM,
  late,
});

// Navbatdagi yozuv faqat vaqti o'qiladigan bo'lsa ko'rsatiladi; aks holda qadam kutilmoqda.
const waiting = (queuedAt: string | null): StepState => {
  const hhmm = utcIsoToTashkentHHMM(queuedAt);
  return hhmm ? { kind: 'queued', capturedHHMM: hhmm } : { kind: 'pending' };
};

// Ilova fonda tunab qolsa, do'kondagi "bugungi" yozuv aslida kechagi bo'ladi. `date` (server:
// "YYYY-MM-DD" yoki date cast "YYYY-MM-DDT00:00:00.000000Z") bugungi Toshkent sanasi bo'lmasa,
// yozuv yo'q deb olinadi. Sana o'qilmasa yozuv saqlanadi.
function rowForToday(row: AttendanceRow | null, now: Date): AttendanceRow | null {
  if (!row) return null;
  const m = typeof row.date === 'string' ? /^(\d{4}-\d{2}-\d{2})/.exec(row.date) : null;
  if (m && m[1] !== tashkentDateKey(now)) return null;
  return row;
}

// Server yozuvi navbatdan ustun: qadam serverda bo'lsa, navbatdagi qayta yuborish ko'rsatilmaydi.
export function buildTimeline(
  row: AttendanceRow | null,
  queued: { check_in: string | null; check_out: string | null },
  now: Date = new Date(),
): Timeline {
  const today = rowForToday(row, now);
  const checkIn: StepState = today?.check_in_at
    ? done(today.check_in_at, today.check_in_distance_m, today.check_in_is_late)
    : waiting(queued.check_in);

  const checkOut: StepState = today?.check_out_at
    ? done(today.check_out_at, today.check_out_distance_m, false)
    : checkIn.kind !== 'done'
      ? { kind: 'locked' }
      : waiting(queued.check_out);

  // Qayta yuborish vaqtni almashtiradi — o'sha kuni ish vaqti noto'g'ri chiqadi, shuning uchun yo'q.
  const duration =
    today?.check_in_at &&
    today.check_out_at &&
    today.check_in_replaced_count === 0 &&
    today.check_out_replaced_count === 0
      ? workDuration(today.check_in_at, today.check_out_at)
      : null;

  return { checkIn, checkOut, workDuration: duration };
}

export function attendanceActions(t: Timeline): { buttons: AttendanceButton[]; hint: string | null } {
  if (t.checkIn.kind === 'pending') {
    return {
      buttons: [{ kind: 'check_in', label: 'Keldim', variant: 'primary', size: 'md', icon: 'camera' }],
      hint: CHECK_IN_HINT,
    };
  }
  if (t.checkIn.kind !== 'done') return { buttons: [], hint: null };

  switch (t.checkOut.kind) {
    case 'pending':
      return {
        buttons: [
          { kind: 'check_out', label: 'Ketdim', variant: 'primary', size: 'md', icon: 'camera' },
          { kind: 'replace_check_in', label: 'Kelishni qayta yuborish', variant: 'outline', size: 'sm' },
        ],
        hint: null,
      };
    case 'queued':
      return {
        buttons: [
          { kind: 'replace_check_in', label: 'Kelishni qayta yuborish', variant: 'outline', size: 'sm' },
        ],
        hint: null,
      };
    case 'done':
      return {
        buttons: [
          { kind: 'replace_check_in', label: 'Kelishni qayta', variant: 'outline', size: 'sm' },
          { kind: 'replace_check_out', label: 'Ketishni qayta', variant: 'outline', size: 'sm' },
        ],
        hint: COMPLETE_HINT,
      };
    default:
      return { buttons: [], hint: null };
  }
}
