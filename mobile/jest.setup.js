/* eslint-env jest */
// Barcha test fayllari uchun umumiy native modul o'rinbosarlari.
// Test fayli ichidagi o'z jest.mock chaqiruvi bulardan ustun turadi.

jest.mock('react-native-safe-area-context', () =>
  require('react-native-safe-area-context/jest/mock').default,
);

jest.mock('@react-native-community/netinfo', () =>
  require('@react-native-community/netinfo/jest/netinfo-mock.js'),
);

jest.mock('react-native-config', () => ({
  __esModule: true,
  default: { API_BASE_URL: 'http://test', APP_ENV: 'development', VERSION_NAME: '1.2.0' },
}));

// Haqiqiy MMKV kabi: bir xil `id` li nusxalar bitta omborni ko'radi (id yo'q — standart ombor).
jest.mock('react-native-mmkv', () => {
  const stores = new Map();
  class MMKV {
    constructor(config = {}) {
      const id = config.id ?? 'mmkv.default';
      if (!stores.has(id)) stores.set(id, new Map());
      this.store = stores.get(id);
    }
    getString(key) {
      return this.store.get(key);
    }
    set(key, value) {
      this.store.set(key, value);
    }
    delete(key) {
      this.store.delete(key);
    }
  }
  return { MMKV };
});
