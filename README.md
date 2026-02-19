# Scandiweb Junior Full Stack Developer Test

A simple eCommerce website with product listing and cart functionality, built for the Scandiweb Junior Full Stack Developer assessment.

## Tech Stack

- **Backend**: PHP 8.x, MySQL, GraphQL (no frameworks)
- **Frontend**: React, Vite, Apollo Client, React Router

## Requirements

- PHP 7.4+ (8.x recommended)
- MySQL 5.6+
- Node.js 18+
- Composer

## Setup

### 1. Database

Create a MySQL database, import the schema, then seed from data.json:

```bash
mysql -u root -p -e "CREATE DATABASE scandiweb;"
mysql -u root -p scandiweb < scandiweb.sql

# Seed/update from Scandiweb data.json (place in project root or src/Controller/)
php scripts/seed-db.php
```

### 2. Backend

```bash
# Install dependencies
composer install

# Copy environment file
cp .env.example .env

# Edit .env with your database credentials
# DB_HOST=localhost
# DB_NAME=scandiweb
# DB_USER=root
# DB_PASS=
```

### 3. Frontend

```bash
cd frontend
npm install
```

### 4. Development

**Terminal 1 - PHP backend:**
```bash
php -S localhost:8000 -t public
```
(Run from project root)

**Terminal 2 - Frontend (with proxy to backend):**
```bash
cd frontend
npm run dev
```

Visit http://localhost:5173

### 5. Production Build

```bash
cd frontend
npm run build
```

This outputs the React app to `public/`. The PHP backend serves:
- `POST /graphql` - GraphQL API
- `GET *` - React SPA (index.html)

### Apache Deployment

Ensure `.htaccess` is in the project root. Document root should point to the project folder. Requests are rewritten to `public/index.php`.

## Project Structure

```
├── public/           # PHP entry, built frontend
├── src/
│   ├── Config/        # Database config
│   ├── Controller/    # GraphQL controller
│   ├── GraphQL/       # Schema, types, resolvers
│   └── Model/         # Category, Product, Attribute, Order
├── frontend/          # React app
└── scandiweb.sql      # Database schema + seed data
```

## Auto QA Testing

Test the application at http://165.227.98.170/ before submission. Ensure all `data-testid` attributes are present as specified in the task.
