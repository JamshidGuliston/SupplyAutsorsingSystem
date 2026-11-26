import {
  parseServerIso, tashkentHHMM, tashkentDateKey, utcIsoToTashkentHHMM, greeting,
  uzDateLabel, submittedLabel, nextOpeningLabel, workDuration,
} from '../../src/lib/uzTime';

const at = (iso: string) => new Date(iso);

describe('uzTime', () => {
  it("parseServerIso kasrli, Z va ofsetli formatlarni o'qiydi", () => {
    expect(parseServerIso('2026-11-26T03:47:00.000000Z')?.toISOString()).toBe('2026-11-26T03:47:00.000Z');
    expect(parseServerIso('2026-11-26T03:47:00Z')?.toISOString()).toBe('2026-11-26T03:47:00.000Z');
    expect(parseServerIso('2026-11-26T08:47:00+05:00')?.toISOString()).toBe('2026-11-26T03:47:00.000Z');
    expect(parseServerIso("bema'ni")).toBeNull();
    expect(parseServerIso('2026-13-26T03:47:00Z')).toBeNull();
  });
  it('tashkentHHMM va tashkentDateKey', () => {
    expect(tashkentHHMM(at('2026-11-26T03:47:00Z'))).toBe('08:47');
    expect(tashkentDateKey(at('2026-11-25T19:30:00Z'))).toBe('2026-11-26');
  });
  it("server kasrli formatini Toshkent HH:MM ga o'giradi", () => {
    expect(utcIsoToTashkentHHMM('2026-11-26T03:47:00.000000Z')).toBe('08:47');
    expect(utcIsoToTashkentHHMM('2026-11-26T19:05:00Z')).toBe('00:05');
    expect(utcIsoToTashkentHHMM(null)).toBeNull();
    expect(utcIsoToTashkentHHMM("bema'ni")).toBeNull();
  });
  it('salomlashuv chegaralari Toshkent vaqtida', () => {
    expect(greeting(at('2026-11-26T00:00:00Z'))).toBe('Xayrli tong');
    expect(greeting(at('2026-11-26T05:59:00Z'))).toBe('Xayrli tong');
    expect(greeting(at('2026-11-26T06:00:00Z'))).toBe('Xayrli kun');
    expect(greeting(at('2026-11-26T11:59:00Z'))).toBe('Xayrli kun');
    expect(greeting(at('2026-11-26T12:00:00Z'))).toBe('Xayrli kech');
    expect(greeting(at('2026-11-25T23:59:00Z'))).toBe('Xayrli kech');
  });
  it("o'zbekcha sana", () => {
    expect(uzDateLabel(at('2026-11-26T04:00:00Z'))).toBe('Payshanba, 26-noyabr');
    expect(uzDateLabel(at('2026-11-30T19:30:00Z'))).toBe('Seshanba, 1-dekabr');
  });
  it("submittedLabel Toshkent sanasi bo'yicha Bugun/Kecha", () => {
    const now = at('2026-11-26T06:00:00Z');
    expect(submittedLabel('2026-11-25T19:30:00.000000Z', now)).toBe('Bugun, 00:30 da');
    expect(submittedLabel('2026-11-25T18:50:00Z', now)).toBe('Kecha, 23:50 da');
    expect(submittedLabel('2026-11-23T08:00:00Z', now)).toBeNull();
    expect(submittedLabel(null, now)).toBeNull();
    expect(submittedLabel('chala', now)).toBeNull();
  });
  it('nextOpeningLabel', () => {
    expect(nextOpeningLabel({ from: '03:00', to: '21:00', current_tashkent: '02:10' })).toBe('Bugun 03:00 da ochiladi');
    expect(nextOpeningLabel({ from: '03:00', to: '21:00', current_tashkent: '21:00' })).toBe('Ertaga 03:00 da ochiladi');
    expect(nextOpeningLabel({ from: '03:00', to: '21:00', current_tashkent: '14:25' })).toBeNull();
  });
  it('workDuration', () => {
    expect(workDuration('2026-11-26T03:02:00.000000Z', '2026-11-26T12:12:00.000000Z')).toBe('9 soat 10 daq');
    expect(workDuration('2026-11-26T03:00:00Z', '2026-11-26T03:45:00Z')).toBe('45 daq');
    expect(workDuration('2026-11-26T03:00:00Z', '2026-11-26T12:00:00Z')).toBe('9 soat');
    expect(workDuration('2026-11-26T12:00:00Z', '2026-11-26T03:00:00Z')).toBeNull();
    expect(workDuration('x', '2026-11-26T03:00:00Z')).toBeNull();
  });
});
