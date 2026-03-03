# ParseError Fix Summary – PHP 7.4/8.0 Compatibility

## Why the ":" ParseError Happened

The error `syntax error, unexpected token ":"` with `ParseError` occurred because:

1. **graphql-php v15.30** (and possibly other deps) uses **PHP 8.3 typed class constants** (`const string FOO = 'bar'`), which are invalid in PHP 8.2 and earlier.
2. **str_starts_with()** (PHP 8.0+) was used – on PHP 7.4 this would cause a fatal error.
3. **Nested ternary operators** with colons can trigger parser edge cases in some PHP builds.
4. The production environment (Railway/Nixpacks) may have been on **PHP 7.4 or 8.2**, causing a ParseError when loading vendor code that used PHP 8.3 syntax.

## How It Was Eliminated

1. **Dockerfile**: Switched from `php:8.3-cli-alpine` to `php:8.0-cli-alpine` – avoids PHP 8.3-only syntax in dependencies.
2. **graphql-php**: Pinned to `15.2.1` (was `^15.2` which resolved to 15.30).
3. **str_starts_with()** replaced with `substr($s, 0, n) === $prefix`.
4. **Arrow function** in SchemaBuilder replaced with a traditional closure for compatibility.
5. **Nested ternary** in SchemaBuilder (currency resolution) replaced with explicit `if`/`else` to avoid parser issues.
6. **get_class($e)** used instead of `$e::class` for exception type.

---

## Files Changed

### 1. `public/index.php`
- `str_starts_with($path, $basePath)` → `substr($path, 0, strlen($basePath)) === $basePath`
- `str_starts_with($result, '{')` → `substr($result, 0, 1) === '{'`
- `/health` now returns `{"status":"ok","php":PHP_VERSION}`

### 2. `src/GraphQL/SchemaBuilder.php`
- `static fn () => $categoryModel->findAll()` → `static function () use ($categoryModel) { return $categoryModel->findAll(); }`
- Nested ternary for `$currency` replaced with an `if` block:

```php
// Before (nested ternary):
$currency = $price && isset($price['currency'])
    ? (is_string($price['currency'])
        ? json_decode($price['currency'], true)
        : $price['currency']
    : ['label' => 'USD', 'symbol' => '$'];

// After (if/else):
$currency = ['label' => 'USD', 'symbol' => '$'];
if ($price && isset($price['currency'])) {
    $currency = is_string($price['currency'])
        ? json_decode($price['currency'], true)
        : $price['currency'];
    $currency = is_array($currency) ? $currency : ['label' => 'USD', 'symbol' => '$'];
}
```

### 3. `src/Controller/GraphQL.php`
- `$e::class` → `get_class($e)` (no functional change, defensive)

### 4. `Dockerfile`
- `FROM php:8.3-cli-alpine` → `FROM php:8.0-cli-alpine`

### 5. `composer.json`
- `"php":">=8.1"` → `"php":">=7.4"`
- `"webonyx/graphql-php":"^15.2"` → `"webonyx/graphql-php":"15.2.1"` (pinned)
- `"vlucas/phpdotenv":"^5.5"` → `"vlucas/phpdotenv":"^5.4"` (for PHP 7.4 hosts)

### 6. `README_DEPLOY.md` (new)
- PHP version requirements
- How to set PHP version on Railway
- Test URLs: `/health`, `/graphql`
- Example curl commands

---

## Test After Deploy

```bash
# Health (includes PHP version)
curl https://YOUR-APP.up.railway.app/health

# GraphQL
curl -X POST https://YOUR-APP.up.railway.app/graphql \
  -H "Content-Type: application/json" \
  -d '{"query":"{ __typename }"}'
```
