import { MMKV } from 'react-native-mmkv';
import { saveUser, loadUser, clearUser } from '../src/auth/userStore';

const u = { id: 1, name: 'Ali', email: 'ali@t.l', role_id: 6 };

describe('userStore', () => {
  beforeEach(() => clearUser());

  it('saqlangan foydalanuvchi o\'qilganda bir xil obyekt qaytadi', () => {
    saveUser(u);
    expect(loadUser()).toEqual(u);
  });

  it('clearUser dan keyin null', () => {
    saveUser(u);
    clearUser();
    expect(loadUser()).toBeNull();
  });

  it('buzilgan JSON bo\'lsa null', () => {
    new MMKV({ id: 'auth' }).set('auth.user.v1', '{x');
    expect(loadUser()).toBeNull();
  });

  it('id/name/email maydonlari yo\'q bo\'lsa null', () => {
    new MMKV({ id: 'auth' }).set('auth.user.v1', JSON.stringify({ id: 1, name: 'Ali' }));
    expect(loadUser()).toBeNull();
  });
});
