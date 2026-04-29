import { todayDateString, isoNowUtc } from '../../src/lib/tashkent';

describe('tashkent helpers', () => {
  it('todayDateString returns YYYY-MM-DD format', () => {
    const result = todayDateString();
    expect(result).toMatch(/^\d{4}-\d{2}-\d{2}$/);
  });

  it('isoNowUtc returns ISO-8601 UTC with Z suffix', () => {
    const result = isoNowUtc();
    expect(result).toMatch(/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}(\.\d+)?Z$/);
  });
});
