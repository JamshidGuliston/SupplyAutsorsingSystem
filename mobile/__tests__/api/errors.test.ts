import { mapServerError } from '../../src/api/errors';

describe('mapServerError', () => {
  it('maps known error codes to Uzbek messages', () => {
    expect(mapServerError({ error: 'mock_gps_detected', message: 'x' })).toContain('Soxta GPS');
    expect(mapServerError({ error: 'outside_geofence', distance_m: 547, max_radius_m: 200 }))
      .toContain('547');
    expect(mapServerError({ error: 'already_checked_in' })).toContain('allaqachon');
    expect(mapServerError({ error: 'invalid_credentials' })).toContain('parol');
  });

  it('falls back to server message when error code is unknown', () => {
    expect(mapServerError({ error: 'unknown_thing', message: 'Server xabari' })).toBe('Server xabari');
  });

  it('returns generic message when nothing useful', () => {
    expect(mapServerError({})).toMatch(/xato/i);
  });
});
