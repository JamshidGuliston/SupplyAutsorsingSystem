import { offlinePillText } from '../../src/components/OfflinePill';

describe('offlinePillText', () => {
  it("internet yo'q, navbat bo'sh — qizil \"Internet yo'q\"", () => {
    expect(offlinePillText(false, 0)).toEqual({ text: "Internet yo'q", tone: 'danger' });
  });

  it("internet yo'q, navbatda yozuv bor — qizil, son bilan", () => {
    expect(offlinePillText(false, 2)).toEqual({
      text: "Internet yo'q · 2 ta yuborilmagan",
      tone: 'danger',
    });
  });

  it('internet bor, navbatda yozuv bor — sariq', () => {
    expect(offlinePillText(true, 1)).toEqual({ text: '1 ta yuborilmagan', tone: 'warn' });
  });

  it("internet bor, navbat bo'sh — hech narsa ko'rsatilmaydi", () => {
    expect(offlinePillText(true, 0)).toBeNull();
  });
});
