import { api } from '../api/client';

export interface AttendanceTodayResponse {
  attendance: AttendanceRow | null;
  kindgarden: KindgardenInfo | null;
  server_time: string;
}

export interface AttendanceRow {
  id: number;
  user_id: number;
  kindgarden_id: number;
  date: string;
  check_in_at: string | null;
  check_in_lat: number | null;
  check_in_lng: number | null;
  check_in_distance_m: number | null;
  check_in_selfie_path: string | null;
  check_in_is_late: boolean;
  check_in_replaced_count: number;
  check_out_at: string | null;
  check_out_lat: number | null;
  check_out_lng: number | null;
  check_out_distance_m: number | null;
  check_out_selfie_path: string | null;
  check_out_replaced_count: number;
}

export interface KindgardenInfo {
  id: number;
  lat: number | null;
  lng: number | null;
  geofence_radius: number;
}

export interface SubmitParams {
  lat: number;
  lng: number;
  capturedAt: string;
  isMock: boolean;
  photoUri: string;
  photoFileName: string;
  photoMimeType: string;
}

function buildFormData(p: SubmitParams): FormData {
  const fd = new FormData();
  fd.append('lat', String(p.lat));
  fd.append('lng', String(p.lng));
  fd.append('captured_at', p.capturedAt);
  fd.append('is_mock', p.isMock ? '1' : '0');
  fd.append('photo', {
    uri: p.photoUri,
    name: p.photoFileName,
    type: p.photoMimeType,
  } as any);
  return fd;
}

export async function getToday(): Promise<AttendanceTodayResponse> {
  const r = await api.get<AttendanceTodayResponse>('/chef/attendance/today');
  return r.data;
}

export async function postCheckIn(p: SubmitParams): Promise<{ attendance: AttendanceRow }> {
  const r = await api.post('/chef/attendance/check-in', buildFormData(p), {
    headers: { 'Content-Type': 'multipart/form-data' },
  });
  return r.data;
}

export async function postCheckOut(p: SubmitParams): Promise<{ attendance: AttendanceRow }> {
  const r = await api.post('/chef/attendance/check-out', buildFormData(p), {
    headers: { 'Content-Type': 'multipart/form-data' },
  });
  return r.data;
}

export async function postReplace(type: 'check_in' | 'check_out', p: SubmitParams): Promise<{ attendance: AttendanceRow }> {
  const fd = buildFormData(p);
  fd.append('type', type);
  const r = await api.post('/chef/attendance/replace', fd, {
    headers: { 'Content-Type': 'multipart/form-data' },
  });
  return r.data;
}

export async function postLocationEvents(events: Array<{
  event_type: 'exit' | 'enter' | 'beacon';
  lat: number; lng: number; happened_at: string; is_mock: boolean;
}>): Promise<{ inserted: number }> {
  const r = await api.post('/chef/location-events', { events });
  return r.data;
}
