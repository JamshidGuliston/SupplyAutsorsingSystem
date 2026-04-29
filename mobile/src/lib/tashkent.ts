import { format } from 'date-fns';

const TZ = 'Asia/Tashkent';

/** Returns today's date in Asia/Tashkent timezone as YYYY-MM-DD. */
export function todayDateString(now: Date = new Date()): string {
  const localized = new Date(now.toLocaleString('en-US', { timeZone: TZ }));
  return format(localized, 'yyyy-MM-dd');
}

/** Returns the current instant as ISO-8601 with explicit UTC Z marker. */
export function isoNowUtc(now: Date = new Date()): string {
  return now.toISOString();
}
