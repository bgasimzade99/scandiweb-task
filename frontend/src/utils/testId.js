/**
 * Converts a string to kebab-case for data-testid attributes.
 * Handles hex colors (#prefix), spaces, and special characters.
 */
export function toKebab(str) {
  if (str == null || str === '') return '';
  return String(str)
    .toLowerCase()
    .trim()
    .replace(/^#/, '')
    .replace(/\s+/g, '-')
    .replace(/[^a-z0-9-]/g, '');
}
