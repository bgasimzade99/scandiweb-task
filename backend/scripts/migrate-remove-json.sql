-- Migration: Remove JSON columns (company policy: no JSON in SQL)
-- Run in Railway MySQL Query tab if you already have data with JSON columns.
-- For fresh installs, use scandiweb.sql directly.

-- 1. prices: JSON currency -> currency_label, currency_symbol
ALTER TABLE prices ADD COLUMN currency_label VARCHAR(50) NOT NULL DEFAULT 'USD' AFTER amount;
ALTER TABLE prices ADD COLUMN currency_symbol VARCHAR(10) NOT NULL DEFAULT '$' AFTER currency_label;

UPDATE prices SET
  currency_label = COALESCE(JSON_UNQUOTE(JSON_EXTRACT(currency, '$.label')), 'USD'),
  currency_symbol = COALESCE(JSON_UNQUOTE(JSON_EXTRACT(currency, '$.symbol')), '$')
WHERE currency IS NOT NULL;

ALTER TABLE prices DROP COLUMN currency;

-- 2. products: JSON gallery -> TEXT (pipe-separated URLs)
ALTER TABLE products ADD COLUMN gallery_new TEXT NULL;
UPDATE products SET gallery_new = REPLACE(REPLACE(REPLACE(REPLACE(CAST(gallery AS CHAR), '["', ''), '"]', ''), '","', '|'), '[]', '');
ALTER TABLE products DROP COLUMN gallery;
ALTER TABLE products CHANGE COLUMN gallery_new gallery TEXT NOT NULL;
