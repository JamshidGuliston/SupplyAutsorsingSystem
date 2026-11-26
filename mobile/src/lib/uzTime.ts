// Toshkent (UTC+5) bo'yicha vaqt yordamchilari. Intl va toLocaleString ishlatilmaydi.
const TASHKENT_OFFSET_MS = 5 * 60 * 60 * 1000;
const DAY_MS = 24 * 60 * 60 * 1000;

const WEEKDAYS = ['Yakshanba', 'Dushanba', 'Seshanba', 'Chorshanba', 'Payshanba', 'Juma', 'Shanba'];
const MONTHS = [
  'yanvar', 'fevral', 'mart', 'aprel', 'may', 'iyun',
  'iyul', 'avgust', 'sentabr', 'oktabr', 'noyabr', 'dekabr',
];

const ISO_RE = /^(\d{4})-(\d{2})-(\d{2})[T ](\d{2}):(\d{2})(?::(\d{2})(?:\.\d+)?)?(Z|[+-]\d{2}:?\d{2})$/;

const pad = (n: number): string => (n < 10 ? `0${n}` : String(n));

// Toshkent devor soati maydonlarini UTC getterlari orqali o'qish uchun siljitilgan sana.
const shifted = (d: Date): Date => new Date(d.getTime() + TASHKENT_OFFSET_MS);

export function parseServerIso(iso: string): Date | null {
  if (typeof iso !== 'string') return null;
  const m = ISO_RE.exec(iso.trim());
  if (!m) return null;
  const [, y, mo, d, h, mi, s, tz] = m;
  const month = Number(mo);
  const day = Number(d);
  const hour = Number(h);
  const minute = Number(mi);
  const second = s ? Number(s) : 0;
  if (month < 1 || month > 12 || day < 1 || day > 31 || hour > 23 || minute > 59 || second > 59) {
    return null;
  }
  let ms = Date.UTC(Number(y), month - 1, day, hour, minute, second);
  if (tz !== 'Z') {
    const sign = tz[0] === '-' ? -1 : 1;
    const digits = tz.slice(1).replace(':', '');
    const offMin = Number(digits.slice(0, 2)) * 60 + Number(digits.slice(2));
    ms -= sign * offMin * 60 * 1000;
  }
  const date = new Date(ms);
  return isNaN(date.getTime()) ? null : date;
}

export function tashkentHHMM(d: Date): string {
  const t = shifted(d);
  return `${pad(t.getUTCHours())}:${pad(t.getUTCMinutes())}`;
}

export function tashkentDateKey(d: Date): string {
  const t = shifted(d);
  return `${t.getUTCFullYear()}-${pad(t.getUTCMonth() + 1)}-${pad(t.getUTCDate())}`;
}

export function utcIsoToTashkentHHMM(iso: string | null | undefined): string | null {
  if (!iso) return null;
  const d = parseServerIso(iso);
  return d ? tashkentHHMM(d) : null;
}

export function greeting(now: Date): 'Xayrli tong' | 'Xayrli kun' | 'Xayrli kech' {
  const h = shifted(now).getUTCHours();
  if (h >= 5 && h < 11) return 'Xayrli tong';
  if (h >= 11 && h < 17) return 'Xayrli kun';
  return 'Xayrli kech';
}

export function uzDateLabel(now: Date): string {
  const t = shifted(now);
  return `${WEEKDAYS[t.getUTCDay()]}, ${t.getUTCDate()}-${MONTHS[t.getUTCMonth()]}`;
}

export function submittedLabel(submittedAt: string | null, now: Date): string | null {
  if (!submittedAt) return null;
  const d = parseServerIso(submittedAt);
  if (!d) return null;
  const key = tashkentDateKey(d);
  const time = tashkentHHMM(d);
  if (key === tashkentDateKey(now)) return `Bugun, ${time} da`;
  if (key === tashkentDateKey(new Date(now.getTime() - DAY_MS))) return `Kecha, ${time} da`;
  return null;
}

export function nextOpeningLabel(w: { from: string; to: string; current_tashkent: string }): string | null {
  if (w.current_tashkent < w.from) return `Bugun ${w.from} da ochiladi`;
  if (w.current_tashkent >= w.to) return `Ertaga ${w.from} da ochiladi`;
  return null;
}

export function workDuration(inIso: string, outIso: string): string | null {
  const a = parseServerIso(inIso);
  const b = parseServerIso(outIso);
  if (!a || !b) return null;
  const diff = b.getTime() - a.getTime();
  if (diff < 0) return null;
  const total = Math.floor(diff / 60000);
  const hours = Math.floor(total / 60);
  const mins = total % 60;
  if (hours === 0) return `${mins} daq`;
  if (mins === 0) return `${hours} soat`;
  return `${hours} soat ${mins} daq`;
}
