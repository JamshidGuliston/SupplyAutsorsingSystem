import fs from 'fs';
import path from 'path';

const SRC_DIR = path.join(__dirname, '..', 'src');

function walk(dir: string, out: string[] = []): string[] {
  for (const entry of fs.readdirSync(dir, { withFileTypes: true })) {
    const full = path.join(dir, entry.name);
    if (entry.isDirectory()) {
      walk(full, out);
    } else if (entry.isFile() && full.endsWith('.tsx')) {
      out.push(full);
    }
  }
  return out;
}

describe('arxitektura invariantlari', () => {
  // Bu test tungi rejim xatosining (oq fonda oq matn) qaytishining oldini oladi:
  // TextInput faqat Field.tsx ichida, aniq rang bilan ishlatilishi kerak.
  it('TextInput faqat Field.tsx ichida ishlatiladi', () => {
    const files = walk(SRC_DIR);
    const offenders: string[] = [];

    for (const file of files) {
      const isField = path.basename(file) === 'Field.tsx';
      const content = fs.readFileSync(file, 'utf8');
      if (/<TextInput\b/.test(content) && !isField) {
        offenders.push(path.relative(SRC_DIR, file));
      }
    }

    expect(offenders).toEqual([]);
  });

  it('Field.tsx haqiqatan ham TextInput ni o\'z ichiga oladi (tekshiruv o\'zi ishlayotganiga ishonch)', () => {
    const fieldPath = path.join(SRC_DIR, 'components', 'Field.tsx');
    const content = fs.readFileSync(fieldPath, 'utf8');
    expect(/<TextInput\b/.test(content)).toBe(true);
  });

  // Bu test debug panelining (xom GPS va API JSON) ishlab chiqarishda
  // chef'larga ko'rsatilib qolishining oldini oladi.
  it('AttendanceScreen dagi debug paneli __DEV__ sharti bilan yopilgan', () => {
    const screenPath = path.join(SRC_DIR, 'screens', 'AttendanceScreen.tsx');
    const content = fs.readFileSync(screenPath, 'utf8');
    expect(content).toMatch(/\{\s*__DEV__\s*&&\s*debugInfo\s*&&/);
  });
});
