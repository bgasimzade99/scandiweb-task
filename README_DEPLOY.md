# Backend Deployment Guide

## Required PHP Version

**PHP 8.0 or higher** (phpdotenv 5.x requires 8.0; Dockerfile uses 8.0)

The backend uses PHP 7.4-compatible syntax. Hosting must provide PHP 7.4+.

## Setting PHP Version

### Railway

Railway uses the `Dockerfile` to determine the PHP version. The Dockerfile specifies:

```dockerfile
FROM php:7.4-cli-alpine
```

To change PHP version, edit the first line of `Dockerfile`:
- `php:7.4-cli-alpine` – PHP 7.4 (requires phpdotenv ^5.4)
- `php:8.0-cli-alpine` – PHP 8.0
- `php:8.2-cli-alpine` – PHP 8.2

### Other Hosting (Heroku, Render, etc.)

Set the PHP version in your stack/config. For Nixpacks/auto-detection, add a `composer.json` with `"require": {"php": ">=7.4"}`.

## Test URLs

After deployment, test these endpoints:

### Health Check (GET)

```bash
curl -i https://YOUR-APP.up.railway.app/health
```

**Expected response (200 OK):**

```json
{"status":"ok","php":"8.0.30"}
```

The `php` field confirms the runtime PHP version (e.g. `"7.4.33"` or `"8.0.30"`).

### GraphQL (POST)

```bash
curl -X POST https://YOUR-APP.up.railway.app/graphql \
  -H "Content-Type: application/json" \
  -d '{"query":"{ __typename }"}'
```

**Expected response (200 OK):**

```json
{"data":{"__typename":"Query"}}
```

### Products Query Example

```bash
curl -X POST https://YOUR-APP.up.railway.app/graphql \
  -H "Content-Type: application/json" \
  -d '{"query":"{ products(category: \"all\") { id name } }"}'
```

## Environment Variables (Railway)

| Variable  | Required | Description    |
|-----------|----------|----------------|
| `DB_HOST` | Yes      | MySQL host     |
| `DB_NAME` | Yes      | MySQL database |
| `DB_USER` | Yes      | MySQL user     |
| `DB_PASS` | Yes      | MySQL password |

Add as Variable References from the MySQL service.
