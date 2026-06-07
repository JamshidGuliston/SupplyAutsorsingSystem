import { api } from './client';

export interface AgeRange {
  id: number;
  age_name: string;
}

export interface TimeWindow {
  allowed: boolean;
  from: string;
  to: string;
  current_tashkent: string;
}

export interface ChildrenCountState {
  kindgarden: { id: number; kingar_name: string };
  age_ranges: AgeRange[];
  submitted: boolean;
  today_counts: Record<string, number> | null;
  submitted_at: string | null;
  time_window: TimeWindow;
  nextday_ready: boolean;
}

export async function getTodayState(): Promise<ChildrenCountState> {
  const r = await api.get<ChildrenCountState>('/chef/children-count/today');
  return r.data;
}

export async function submitChildrenCount(counts: Record<number, number>): Promise<ChildrenCountState> {
  const r = await api.post<ChildrenCountState>('/chef/children-count', { counts });
  return r.data;
}
