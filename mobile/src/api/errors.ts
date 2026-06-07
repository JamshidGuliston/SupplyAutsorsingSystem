export interface ServerErrorPayload {
  error?: string;
  message?: string;
  distance_m?: number;
  max_radius_m?: number;
}

export function mapServerError(payload: ServerErrorPayload | undefined): string {
  if (!payload) return 'Noma\'lum xato. Qayta urinib ko\'ring.';
  switch (payload.error) {
    case 'mock_gps_detected':
      return 'Soxta GPS aniqlandi. GPS Joystick va shu kabi ilovalarni o\'chiring.';
    case 'outside_geofence':
      return `Bog'chadan ${payload.distance_m}m uzoqdasiz. Bog'chaga keling va qayta urinib ko'ring.`;
    case 'kindgarden_coords_not_set':
      return 'Bog\'cha koordinatalari sozlanmagan. Addelkadirga murojaat qiling.';
    case 'already_checked_in':
      return 'Bugun allaqachon kelgansiz. "Qayta yuborish" tugmasidan foydalaning.';
    case 'already_checked_out':
      return 'Bugun allaqachon ketgansiz.';
    case 'not_checked_in':
      return 'Avval "Keldim" tugmasini bosing.';
    case 'stale_capture':
      return 'Yuborilgan vaqt server vaqtidan ko\'p farq qiladi. Qayta urinib ko\'ring.';
    case 'invalid_credentials':
      return 'Email yoki parol noto\'g\'ri.';
    case 'role_not_allowed':
      return 'Bu hisob mobil ilovaga kirishi mumkin emas.';
    case 'rate_limited':
      return 'Juda ko\'p urinish. Bir oz kuting va qayta urinib ko\'ring.';
    case 'time_window_closed':
      return 'Hozir yuborish vaqti emas (03:00 - 21:00 oralig\'ida bo\'lishi kerak).';
    case 'already_submitted_today':
      return 'Bugungi son allaqachon yuborilgan. O\'zgartirish kerak bo\'lsa texnologga murojaat qiling.';
    case 'nextday_not_ready':
      return 'Texnolog kelajak kun uchun menyu sozlamagan. Texnologga murojaat qiling.';
    case 'invalid_age_for_kindgarden':
      return 'Tanlangan yosh toifasi bog\'chaga tegishli emas.';
    case 'incomplete_submission':
      return 'Barcha yosh toifalari uchun son kiriting.';
    case 'kindgarden_not_assigned':
      return 'Sizning hisobingizga bog\'cha biriktirilmagan. Addelkadirga murojaat qiling.';
    default:
      return payload.message || 'Noma\'lum xato. Qayta urinib ko\'ring.';
  }
}
