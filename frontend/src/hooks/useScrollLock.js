import { useEffect } from 'react';

/**
 * Locks body scroll when lock is true. Cleans up on unmount or when lock becomes false.
 * Uses body class instead of inline style for cleaner separation.
 */
export function useScrollLock(lock) {
  useEffect(() => {
    if (lock) {
      document.body.classList.add('scroll-lock');
    }
    return () => {
      document.body.classList.remove('scroll-lock');
    };
  }, [lock]);
}
