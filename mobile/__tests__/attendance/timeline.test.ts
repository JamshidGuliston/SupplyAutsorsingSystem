import { buildTimeline, attendanceActions } from '../../src/attendance/timeline';
import type { AttendanceRow } from '../../src/attendance/attendanceApi';

const row = (o: Partial<AttendanceRow>) =>
  ({
    check_in_at: null,
    check_out_at: null,
    check_in_is_late: false,
    check_in_distance_m: null,
    check_out_distance_m: null,
    check_in_replaced_count: 0,
    check_out_replaced_count: 0,
    ...o,
  } as AttendanceRow);

const none = { check_in: null, check_out: null };
const queuedIn = { check_in: '2026-11-26T03:10:00Z', check_out: null };

const both = buildTimeline(
  row({
    check_in_at: '2026-11-26T03:02:00.000000Z',
    check_in_distance_m: 38,
    check_out_at: '2026-11-26T12:12:00.000000Z',
    check_out_distance_m: 52,
  }),
  none,
);

describe('buildTimeline', () => {
  it("yozuv ham, navbat ham yo'q — kelish kutilmoqda, ketish qulf", () => {
    expect(buildTimeline(null, none)).toEqual({
      checkIn: { kind: 'pending' },
      checkOut: { kind: 'locked' },
      workDuration: null,
    });
  });

  it('navbatdagi kelish — Toshkent vaqti bilan queued', () => {
    const t = buildTimeline(null, queuedIn);
    expect(t.checkIn).toEqual({ kind: 'queued', capturedHHMM: '08:10' });
    expect(t.checkOut).toEqual({ kind: 'locked' });
  });

  it('kelgan va ketgan — vaqtlar Toshkentda, masofa va ish vaqti', () => {
    expect(both.checkIn).toEqual({ kind: 'done', timeHHMM: '08:02', distanceM: 38, late: false });
    expect(both.checkOut).toEqual({ kind: 'done', timeHHMM: '17:12', distanceM: 52, late: false });
    expect(both.workDuration).toBe('9 soat 10 daq');
  });

  it('kechikkan kelish late bilan belgilanadi', () => {
    const t = buildTimeline(row({ check_in_at: '2026-11-26T03:47:00.000000Z', check_in_is_late: true }), none);
    expect(t.checkIn).toEqual({ kind: 'done', timeHHMM: '08:47', distanceM: null, late: true });
    expect(t.checkOut).toEqual({ kind: 'pending' });
    expect(t.workDuration).toBeNull();
  });

  it('kelgandan keyin navbatdagi ketish — checkOut queued', () => {
    const t = buildTimeline(row({ check_in_at: '2026-11-26T03:02:00.000000Z' }), {
      check_in: null,
      check_out: '2026-11-26T12:30:00Z',
    });
    expect(t.checkOut).toEqual({ kind: 'queued', capturedHHMM: '17:30' });
  });

  it("server yozuvi navbatdan ustun: kelgan bo'lsa navbatdagi kelish e'tiborsiz", () => {
    const t = buildTimeline(row({ check_in_at: '2026-11-26T03:02:00.000000Z' }), queuedIn);
    expect(t.checkIn.kind).toBe('done');
  });

  it("qayta yuborilgan kun ish vaqti ko'rsatilmaydi", () => {
    const base = {
      check_in_at: '2026-11-26T03:02:00.000000Z',
      check_out_at: '2026-11-26T12:12:00.000000Z',
    };
    expect(buildTimeline(row({ ...base, check_in_replaced_count: 1 }), none).workDuration).toBeNull();
    expect(buildTimeline(row({ ...base, check_out_replaced_count: 2 }), none).workDuration).toBeNull();
  });
});

describe("buildTimeline: kechagi yozuv bugungi emas", () => {
  // Toshkentda 26-noyabr 10:00.
  const now = new Date('2026-11-26T05:00:00Z');
  const yesterday = row({
    date: '2026-11-25',
    check_in_at: '2026-11-25T03:02:00.000000Z',
    check_out_at: '2026-11-25T12:12:00.000000Z',
  });

  it("sanasi bugungi Toshkent sanasi bo'lmagan yozuv — kelish kutilmoqda, faqat \"Keldim\"", () => {
    const t = buildTimeline(yesterday, none, now);
    expect(t).toEqual({ checkIn: { kind: 'pending' }, checkOut: { kind: 'locked' }, workDuration: null });
    expect(attendanceActions(t).buttons.map((b) => b.label)).toEqual(['Keldim']);
  });

  it("bugungi sanali yozuv saqlanadi (YYYY-MM-DD va Laravel'ning date cast shakli)", () => {
    const at = { check_in_at: '2026-11-26T03:02:00.000000Z' };
    expect(buildTimeline(row({ ...at, date: '2026-11-26' }), none, now).checkIn.kind).toBe('done');
    expect(
      buildTimeline(row({ ...at, date: '2026-11-26T00:00:00.000000Z' }), none, now).checkIn.kind,
    ).toBe('done');
  });

  it('Toshkent yarim tun chegarasi: 25-noyabr 19:30Z — Toshkentda allaqachon 26-noyabr', () => {
    const justAfterMidnight = new Date('2026-11-25T19:30:00Z');
    expect(buildTimeline(yesterday, none, justAfterMidnight).checkIn.kind).toBe('pending');
    expect(
      buildTimeline(row({ date: '2026-11-26', check_in_at: '2026-11-25T19:20:00Z' }), none, justAfterMidnight)
        .checkIn.kind,
    ).toBe('done');
  });

  it("kechagi yozuv o'rnida navbatdagi bugungi kelish ko'rsatiladi", () => {
    const t = buildTimeline(yesterday, { check_in: '2026-11-26T03:10:00Z', check_out: null }, now);
    expect(t.checkIn).toEqual({ kind: 'queued', capturedHHMM: '08:10' });
  });
});

describe('attendanceActions', () => {
  it('kelmagan — faqat qora "Keldim" (kamera) va izoh', () => {
    const a = attendanceActions(buildTimeline(null, none));
    expect(a.buttons).toEqual([
      { kind: 'check_in', label: 'Keldim', variant: 'primary', size: 'md', icon: 'camera' },
    ]);
    expect(a.hint).toBe(
      'Selfi va joylashuv avtomatik olinadi. Kechiksangiz ham bog\'chada turib bosing — tizim "kechikkan" deb belgilaydi.',
    );
  });

  it("kelish navbatda — tugma yo'q, izoh yo'q", () => {
    const a = attendanceActions(buildTimeline(null, queuedIn));
    expect(a.buttons).toEqual([]);
    expect(a.hint).toBeNull();
  });

  it('kelgan, ketmagan — "Ketdim" va kichik "Kelishni qayta yuborish"', () => {
    const a = attendanceActions(buildTimeline(row({ check_in_at: '2026-11-26T03:02:00.000000Z' }), none));
    expect(a.buttons).toEqual([
      { kind: 'check_out', label: 'Ketdim', variant: 'primary', size: 'md', icon: 'camera' },
      { kind: 'replace_check_in', label: 'Kelishni qayta yuborish', variant: 'outline', size: 'sm' },
    ]);
    expect(a.hint).toBeNull();
  });

  it('kelgan, ketish navbatda — faqat "Kelishni qayta yuborish"', () => {
    const a = attendanceActions(
      buildTimeline(row({ check_in_at: '2026-11-26T03:02:00.000000Z' }), {
        check_in: null,
        check_out: '2026-11-26T12:30:00Z',
      }),
    );
    expect(a.buttons.map((b) => b.label)).toEqual(['Kelishni qayta yuborish']);
    expect(a.hint).toBeNull();
  });

  it('ikkalasi — ikki kichik outline va yakuniy izoh', () => {
    const a = attendanceActions(both);
    expect(a.buttons).toEqual([
      { kind: 'replace_check_in', label: 'Kelishni qayta', variant: 'outline', size: 'sm' },
      { kind: 'replace_check_out', label: 'Ketishni qayta', variant: 'outline', size: 'sm' },
    ]);
    expect(a.hint).toBe("Bugungi davomat to'liq. Ertaga ko'rishguncha!");
  });
});
