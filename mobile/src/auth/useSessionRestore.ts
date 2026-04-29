import { useEffect } from 'react';
import { useAuthStore } from './authStore';
import { loadToken } from './tokenStore';
import { api } from '../api/client';
import { startFlusher } from '../attendance/attendanceFlusher';

/**
 * On app boot, check if we have a saved token. If so, validate it by hitting
 * the /chef/attendance/today endpoint. If valid, mark authenticated; if 401,
 * the api client interceptor will clear the token and we stay unauthenticated.
 */
export function useSessionRestore(): void {
  const setStatus = useAuthStore((s) => s.setStatus);

  useEffect(() => {
    let cancelled = false;
    (async () => {
      setStatus('restoring');
      const token = await loadToken();
      if (!token) {
        if (!cancelled) setStatus('unauthenticated');
        return;
      }
      try {
        await api.get('/chef/attendance/today');
        if (!cancelled) {
          useAuthStore.setState({ status: 'authenticated' });
          startFlusher();
        }
      } catch {
        if (!cancelled) setStatus('unauthenticated');
      }
    })();
    return () => { cancelled = true; };
  }, [setStatus]);
}
