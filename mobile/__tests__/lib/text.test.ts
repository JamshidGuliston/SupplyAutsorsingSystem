import { initials, sumFilled, displayName } from '../../src/lib/text';

describe('text', () => {
  it("initials birinchi ikki so'z bosh harfi", () => {
    expect(initials('Dilnoza Aliyeva')).toBe('DA');
    expect(initials('  aliyeva   dilnoza  karimovna ')).toBe('AD');
    expect(initials('Дилноза')).toBe('Д');
    expect(initials("O'g'iloy Toshmatova")).toBe('OT');
    expect(initials('')).toBeNull();
    expect(initials(null)).toBeNull();
  });
  it("sumFilled faqat raqamli qiymatlarni qo'shadi", () => {
    expect(sumFilled(['18', '42', ''])).toBe(60);
    expect(sumFilled(['18', undefined, 'x'])).toBe(18);
    expect(sumFilled(['', undefined])).toBeNull();
  });
  it('displayName probellarni tozalaydi', () => {
    expect(displayName('  Dilnoza   Aliyeva ')).toBe('Dilnoza Aliyeva');
    expect(displayName('   ')).toBeNull();
    expect(displayName(undefined)).toBeNull();
  });
});
