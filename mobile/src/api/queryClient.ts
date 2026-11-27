import { AppState } from 'react-native';
import { QueryClient, focusManager } from '@tanstack/react-query';

// Ilovaning yagona QueryClient'i: App.tsx provider'ga beradi, chiqishda kesh tozalanadi.
export const queryClient = new QueryClient({
  defaultOptions: {
    queries: {
      retry: 2,
      staleTime: 30_000,
    },
  },
});

// RN da `window` fokus hodisalari yo'q: react-query fokusni AppState dan oladi. Ilova fondan
// qaytganda (masalan ertasi kuni ertalab) eskirgan so'rovlar qayta yuklanadi.
export function bindQueryFocusToAppState(): void {
  focusManager.setEventListener((handleFocus) => {
    const sub = AppState.addEventListener('change', (state) => handleFocus(state === 'active'));
    return () => sub.remove();
  });
}
