# Scandiweb Junior Full Stack Developer Test

E-commerce product listing and cart built for the Scandiweb assessment.

## Tech Stack

- **Backend**: PHP 8.x, MySQL, GraphQL (no framework)
- **Frontend**: React, Vite, Apollo Client, React Router

## Prerequisites

- PHP 7.4+ (8.x recommended)
- MySQL 5.6+
- Node.js 18+
- Composer

## Project Structure

```
├── backend/           # PHP GraphQL API
│   ├── src/
│   ├── scripts/
│   ├── public/
│   ├── scandiweb.sql
│   └── data.json
├── frontend/          # React SPA (Vite)
└── docs/              # Additional documentation
```

## Setup

### 1. Database

Create a MySQL database and import the schema:

```bash
mysql -u root -p -e "CREATE DATABASE scandiweb;"
mysql -u root -p scandiweb < backend/scandiweb.sql
```

### 2. Backend

```bash
cd backend
composer install
cp .env.example .env
# Edit .env with your DB credentials: DB_HOST, DB_NAME, DB_USER, DB_PASS
```

Seed from data.json (updates products, prices, attributes):

```bash
cd backend
php scripts/seed-db.php
```

### 3. Frontend

```bash
cd frontend
npm install
```

### 4. Run Development

**Terminal 1 – Backend:**
```bash
cd backend
php -S localhost:8000 -t public
```

**Terminal 2 – Frontend:**
```bash
cd frontend
npm run dev
```

Visit http://localhost:5173. The Vite dev server proxies `/graphql` to the backend.

### Alternative: Mock API (no PHP)

```bash
cd frontend
npm run start
```

Uses Node mock server + Vite. Requires `backend/data.json` for mock data.

## Environment Variables

| Location | Variables |
|----------|-----------|
| `backend/.env` | `DB_HOST`, `DB_NAME`, `DB_USER`, `DB_PASS` (or `MYSQL_PUBLIC_URL`) |
| `frontend/.env` | `VITE_GRAPHQL_URI` (production backend URL, e.g. Netlify deploy) |

## Production / Deployment

- **Frontend**: Netlify (see `netlify.toml`, builds from `frontend/`)
- **Backend**: Railway or Render (Dockerfile or `render.yaml` with `rootDir: backend`)

After backend deploy, run migrations if needed:

```bash
cd backend
php scripts/import-schema.php   # Fresh schema
php scripts/seed-db.php         # Seed from data.json
php scripts/migrate-orders-normalized.php  # For existing DBs with old orders
```

## Troubleshooting

**"PDOException: could not find driver"** – The `pdo_mysql` extension is not loaded.

- **Linux**: `sudo apt install php-mysql` (or `php8.x-mysql`), restart PHP.
- **macOS (brew)**: `brew install php` and ensure `extension=pdo_mysql` in `php.ini`.
- **Windows (XAMPP/WAMP/Laragon)**: Uncomment `extension=pdo_mysql` in `php.ini` and restart Apache/PHP.

## Scripts Reference

| Command | Description |
|---------|-------------|
| `cd backend && composer schema:import` | Import scandiweb.sql |
| `cd backend && composer seed` | Seed from data.json |
| `cd backend && composer db:setup` | Import + seed |
| `cd backend && composer migrate:orders` | Migrate orders to normalized schema |
| `cd frontend && npm run dev` | Vite dev server |
| `cd frontend && npm run build` | Production build |
| `cd frontend && npm run start` | Mock API + dev (no PHP) |
