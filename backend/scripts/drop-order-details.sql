-- Drop legacy order_details JSON column from orders.
-- Prefer: php scripts/drop-order-details.php (checks if column exists first).
-- Or:     mysql -u user -p dbname < scripts/drop-order-details.sql
-- Note: Direct SQL fails if column does not exist.

ALTER TABLE `orders` DROP COLUMN `order_details`;
