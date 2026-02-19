# Railway Deployment Guide

## Railway Settings

Configure these in your Railway project dashboard.

### Build

| Setting       | Value                         |
|---------------|-------------------------------|
| Build Command | *(leave empty – Dockerfile handles it)* |
| Builder       | Dockerfile                     |
| Dockerfile    | `Dockerfile` (project root)    |

### Run

| Setting      | Value                                                             |
|--------------|-------------------------------------------------------------------|
| Start Command| *(leave empty – Dockerfile CMD is used)*                           |

### Environment Variables (Backend Service)

| Variable  | Required | Description                          |
|-----------|----------|--------------------------------------|
| `PORT`    | Auto     | Set by Railway (don't override)     |
| `DB_HOST` | Yes      | MySQL host (e.g. from MySQL service) |
| `DB_NAME` | Yes      | MySQL database name                  |
| `DB_USER` | Yes      | MySQL username                       |
| `DB_PASS` | Yes      | MySQL password                       |

If you use Railway’s MySQL plugin, link it to the backend service and add the variables from its connection details.

---

## Netlify (Frontend)

| Setting            | Value                                          |
|--------------------|------------------------------------------------|
| Build Command      | `cd frontend && npm install && npm run build`  |
| Publish Directory  | `frontend/dist`                                 |
| Environment Variable| `VITE_GRAPHQL_URI` = `https://YOUR-RAILWAY-URL.up.railway.app/graphql` |

Replace `YOUR-RAILWAY-URL` with your Railway backend public URL.

---

## Test Endpoints

After deployment, use these URLs (replace `YOUR-RAILWAY-URL` with your backend URL):

### Root / Health Check (GET)

```bash
curl -i https://YOUR-RAILWAY-URL.up.railway.app/
curl -i https://YOUR-RAILWAY-URL.up.railway.app/health
```

**Expected:** `200 OK` with `{"status":"ok","endpoints":["/health","/graphql"]}` (root) or `{"status":"ok"}` (/health).

### GraphQL (POST)

```bash
curl -X POST https://YOUR-RAILWAY-URL.up.railway.app/graphql \
  -H "Content-Type: application/json" \
  -d '{"query":"{ __typename }"}'
```

**Expected:** `200 OK` with a JSON response such as `{"data":{"__typename":"Query"}}`.

### Browser

- Health: `https://YOUR-RAILWAY-URL.up.railway.app/health`
- GraphQL: The React app on Netlify should call `VITE_GRAPHQL_URI`, which must point to `https://YOUR-RAILWAY-URL.up.railway.app/graphql`.

---

## Why 404 Happened and How It Was Fixed

### Root Cause

The backend uses PHP’s built‑in server with `-t public` as the document root.  
Without a router script:

1. A request to `/graphql` causes PHP to look for `public/graphql` or `public/graphql/index.php`.
2. No such file exists.
3. PHP returns 404.

So `/graphql` never reached `public/index.php` or FastRoute.

### Fix

The Dockerfile `CMD` was changed to use the router script:

```text
php -S 0.0.0.0:${PORT:-8000} -t public public/index.php
```

With `public/index.php` as the router:

1. Every request is first handled by `public/index.php`.
2. FastRoute dispatches `/graphql` and `/health` correctly.
3. The backend returns 200 for valid requests instead of 404.

### Additional Changes

- **CORS:** Headers set in `index.php` for all responses (including preflight `OPTIONS`).
- **GET /health:** Simple health check returning `{"status":"ok"}`.
- **Composer:** `composer install` used instead of `composer update` for stable, reproducible builds.
