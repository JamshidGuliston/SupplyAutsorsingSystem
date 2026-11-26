import type { ChildrenCountState } from '../api/childrenCount';

export type ChildrenCountView = 'submitted' | 'not_ready' | 'closed' | 'no_groups' | 'open';

// Spec 5.2 tartibi: birinchi mos kelgani. "Yuborilgan" ataylab "yopiq" dan oldin — server
// `submitted` ni oxirgi 12 soat bo'yicha beradi, yopiq birinchi bo'lsa yolg'on "ochiladi" chiqadi.
export function childrenCountView(d: ChildrenCountState): ChildrenCountView {
  if (d.submitted) return 'submitted';
  if (!d.nextday_ready) return 'not_ready';
  if (!d.time_window.allowed) return 'closed';
  if (d.age_ranges.length === 0) return 'no_groups';
  return 'open';
}
