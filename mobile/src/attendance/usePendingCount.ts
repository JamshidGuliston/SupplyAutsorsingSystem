import { useCallback, useEffect, useState } from 'react';
import { attendancePendingCount } from './attendanceQueue';

// Navbatdagi davomat yozuvlari soni (joylashuv hodisalarisiz) — "Internet yo'q" kapsulasi va Profil
// bir xil manbadan, bir xil davr bilan o'qiydi, shuning uchun bir-biriga zid son ko'rsatmaydi.
// `refresh` darhol qayta o'qiydi (masalan chiqish dialogi ochilayotganda) va sonni qaytaradi.
export function usePendingCount(intervalMs: number = 5_000): {
  pending: number;
  refresh: () => number;
} {
  const [pending, setPending] = useState<number>(attendancePendingCount);

  const refresh = useCallback(() => {
    const n = attendancePendingCount();
    setPending(n);
    return n;
  }, []);

  useEffect(() => {
    const tick = setInterval(refresh, intervalMs);
    return () => clearInterval(tick);
  }, [refresh, intervalMs]);

  return { pending, refresh };
}
