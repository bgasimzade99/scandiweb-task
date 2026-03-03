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

**Preferred** (Railway MySQL plugin):

| Variable            | Description                                         |
|---------------------|-----------------------------------------------------|
| `MYSQL_PUBLIC_URL`  | Full URL `mysql://user:pass@host:port/db` (best)    |
| `MYSQLHOST`         | MySQL host                                          |
| `MYSQLPORT`         | MySQL port (default 3306)                           |
| `MYSQLDATABASE`     | Database name                                       |
| `MYSQLUSER`         | Username                                            |
| `MYSQLPASSWORD`     | Password                                            |

**Alternative:** `DB_HOST`, `DB_NAME`, `DB_USER`, `DB_PASS`.

Use Railway's "Add Reference" to link variables from the MySQL service. Never use literal `${VAR}` syntax—values must be expanded.

## Manual DB Setup (after deploy)

The app starts without a database. Run migrations manually once the DB is available:

```bash
railway run php scripts/import-schema.php
railway run php scripts/seed-db.php
```

Or via Railway shell / one-off run.
