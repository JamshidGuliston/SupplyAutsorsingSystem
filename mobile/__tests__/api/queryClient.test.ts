import { AppState } from 'react-native';
import { QueryObserver, focusManager } from '@tanstack/react-query';
import { waitFor } from '@testing-library/react-native';
import { queryClient, bindQueryFocusToAppState } from '../../src/api/queryClient';

// RN jest mock'idagi AppState.addEventListener — oxirgi 'change' tinglovchisi.
const appStateListener = (): ((s: string) => void) => {
  const calls = (AppState.addEventListener as jest.Mock).mock.calls.filter((c) => c[0] === 'change');
  expect(calls.length).toBeGreaterThan(0);
  return calls[calls.length - 1][1];
};

describe('queryClient', () => {
  afterEach(() => {
    queryClient.clear();
  });

  it("standart sozlamalar o'zgarmagan: retry 2, staleTime 30 s", () => {
    const q = queryClient.getDefaultOptions().queries;
    expect(q?.retry).toBe(2);
    expect(q?.staleTime).toBe(30_000);
  });

  it("react-query fokusi AppState dan: fonda — fokus yo'q, qaytganda — fokus bor", () => {
    bindQueryFocusToAppState();
    const onChange = appStateListener();
    onChange('background');
    expect(focusManager.isFocused()).toBe(false);
    onChange('active');
    expect(focusManager.isFocused()).toBe(true);
  });

  it('ilova fondan qaytganda eskirgan so\'rov qayta yuklanadi', async () => {
    bindQueryFocusToAppState();
    queryClient.mount();
    const queryFn = jest.fn().mockResolvedValue({ ok: true });
    const observer = new QueryObserver(queryClient, { queryKey: ['t'], queryFn, staleTime: 0 });
    const unsubscribe = observer.subscribe(() => {});
    await waitFor(() => expect(observer.getCurrentResult().isSuccess).toBe(true));
    expect(queryFn).toHaveBeenCalledTimes(1);

    const onChange = appStateListener();
    onChange('background');
    expect(queryFn).toHaveBeenCalledTimes(1);
    onChange('active');
    await waitFor(() => expect(queryFn).toHaveBeenCalledTimes(2));
    unsubscribe();
    queryClient.unmount();
  });
});
