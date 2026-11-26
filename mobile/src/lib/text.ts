export function displayName(name: string | null | undefined): string | null {
  if (!name) return null;
  const v = name.trim().replace(/\s+/g, ' ');
  return v === '' ? null : v;
}

export function initials(name: string | null | undefined): string | null {
  const v = displayName(name);
  if (!v) return null;
  return v
    .split(' ')
    .slice(0, 2)
    .map((w) => Array.from(w)[0].toUpperCase())
    .join('');
}

export function sumFilled(values: Array<string | undefined>): number | null {
  let total = 0;
  let any = false;
  for (const v of values) {
    if (v !== undefined && /^\d+$/.test(v)) {
      total += Number(v);
      any = true;
    }
  }
  return any ? total : null;
}
