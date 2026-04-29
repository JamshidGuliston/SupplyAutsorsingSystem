import axios, { AxiosError, AxiosInstance } from 'axios';
import Config from 'react-native-config';
import { loadToken, clearToken } from '../auth/tokenStore';

let onUnauthenticated: (() => void) | null = null;

export function registerUnauthenticatedHandler(fn: () => void): void {
  onUnauthenticated = fn;
}

export const api: AxiosInstance = axios.create({
  baseURL: Config.API_BASE_URL,
  timeout: 30_000,
  headers: { Accept: 'application/json' },
});

api.interceptors.request.use(async (config) => {
  const token = await loadToken();
  if (token) {
    config.headers = config.headers ?? {};
    (config.headers as any).Authorization = `Bearer ${token}`;
  }
  return config;
});

api.interceptors.response.use(
  (resp) => resp,
  async (err: AxiosError) => {
    if (err.response?.status === 401) {
      await clearToken();
      onUnauthenticated?.();
    }
    return Promise.reject(err);
  },
);
