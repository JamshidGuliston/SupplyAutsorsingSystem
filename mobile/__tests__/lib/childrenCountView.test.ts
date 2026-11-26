import { childrenCountView } from '../../src/lib/childrenCountView';
import type { ChildrenCountState, TimeWindow } from '../../src/api/childrenCount';

const w: TimeWindow = { allowed: true, from: '03:00', to: '21:00', current_tashkent: '14:25' };

const base: ChildrenCountState = {
  kindgarden: { id: 7, kingar_name: '12-son DMTT' },
  age_ranges: [{ id: 1, age_name: '1–3 yosh' }],
  submitted: false,
  today_counts: null,
  submitted_at: null,
  time_window: w,
  nextday_ready: true,
};

describe('childrenCountView', () => {
  it("yuborilgan holat vaqt yopiqligidan oldin tanlanadi", () => {
    expect(childrenCountView({ ...base, submitted: true, time_window: { ...w, allowed: false } })).toBe('submitted');
  });

  it("menyu tayyor emasligi vaqt yopiqligidan oldin tanlanadi", () => {
    expect(childrenCountView({ ...base, nextday_ready: false, time_window: { ...w, allowed: false } })).toBe('not_ready');
  });

  it('vaqt oynasi yopiq', () => {
    expect(childrenCountView({ ...base, time_window: { ...w, allowed: false } })).toBe('closed');
  });

  it("yosh guruhlari bo'sh", () => {
    expect(childrenCountView({ ...base, age_ranges: [] })).toBe('no_groups');
  });

  it('forma ochiq', () => {
    expect(childrenCountView(base)).toBe('open');
  });
});
