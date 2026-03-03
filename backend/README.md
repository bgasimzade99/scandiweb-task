# Backend – PHP GraphQL API

## Structure

```
backend/
├── src/
│   ├── Config/        # Database config
│   ├── Controller/    # GraphQL handler
│   ├── GraphQL/       # Schema, types, resolvers
│   ├── Model/         # Order, Attribute
│   ├── Repository/    # Product, Category, Price, BaseRepository
│   └── Service/       # OrderService
├── scripts/
│   ├── import-schema.php
│   ├── seed-db.php
│   ├── migrate-orders-normalized.php
│   └── migrate-gallery.php
├── public/
│   ├── index.php      # Entry point
│   └── .htaccess
├── scandiweb.sql      # Schema + base data
├── data.json          # Seed data (products, categories)
└── .env.example       # DB credentials template
```

## Setup

```bash
composer install
cp .env.example .env
# Edit .env: DB_HOST, DB_NAME, DB_USER, DB_PASS
mysql -u root -p scandiweb < scandiweb.sql
php scripts/seed-db.php
```

## Run

```bash
php -S localhost:8000 -t public
```

Endpoints: `POST /graphql`, `GET /health`, `GET /graphql-ping`

## Troubleshooting

**"PDOException: could not find driver"** – Enable `pdo_mysql`: Linux: `php-mysql` package; macOS: `extension=pdo_mysql` in php.ini; Windows (XAMPP/Laragon): enable `extension=pdo_mysql` in php.ini and restart.

## Composer Scripts

- `composer schema:import` – Import scandiweb.sql
- `composer seed` – Seed from data.json
- `composer db:setup` – Import + seed
- `composer migrate:orders` – Migrate orders to normalized tables (order_status, order_items, order_item_attributes)
- `composer migrate:drop-order-details` – Safely drop legacy order_details column from orders (if present)
