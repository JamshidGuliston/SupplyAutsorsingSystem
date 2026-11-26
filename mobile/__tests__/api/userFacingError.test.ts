import { userFacingError, errorCode, LOAD_FAILED_TEXT } from '../../src/api/errors';

describe('userFacingError', () => {
  it('server javobi mapServerError orqali', () => {
    expect(userFacingError({ response: { data: { error: 'invalid_credentials' } } })).toBe("Email yoki parol noto'g'ri.");
  });
  it("javob bor, tanasi yo'q", () => {
    expect(userFacingError({ response: { status: 500 } })).toBe("Serverda xato. Birozdan keyin qayta urinib ko'ring.");
  });
  it('GPS xatosi', () => {
    expect(userFacingError(new Error('GPS error (3): timeout'))).toBe("Joylashuvni aniqlab bo'lmadi. GPS yoqilganini tekshirib, qayta urinib ko'ring.");
  });
  it("kamera/selfi xabarlari o'zgarishsiz", () => {
    expect(userFacingError(new Error('Bekor qilindi'))).toBe('Bekor qilindi');
    expect(userFacingError(new Error('Rasm olinmadi'))).toBe('Rasm olinmadi');
    expect(userFacingError(new Error('Kamera xatosi: x'))).toBe('Kamera xatosi: x');
  });
  it('qolgani tarmoq xatosi', () => {
    expect(userFacingError(new Error('Network Error'))).toBe("Internet bilan bog'lanib bo'lmadi. Qayta urinib ko'ring.");
    expect(userFacingError(undefined)).toBe("Internet bilan bog'lanib bo'lmadi. Qayta urinib ko'ring.");
  });
  it('errorCode va LOAD_FAILED_TEXT', () => {
    expect(errorCode({ response: { data: { error: 'kindgarden_not_assigned' } } })).toBe('kindgarden_not_assigned');
    expect(errorCode(new Error('x'))).toBeUndefined();
    expect(LOAD_FAILED_TEXT).toBe("Internetni tekshirib, qayta urinib ko'ring.");
  });
});
