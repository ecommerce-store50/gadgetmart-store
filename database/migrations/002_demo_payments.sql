-- Run this migration on an existing database after schema.sql.
ALTER TABLE orders
  ADD COLUMN payment_method VARCHAR(30) NOT NULL DEFAULT 'cod' AFTER status,
  ADD COLUMN payment_status VARCHAR(30) NOT NULL DEFAULT 'unpaid' AFTER payment_method,
  ADD COLUMN payment_reference VARCHAR(80) NULL AFTER payment_status;

-- If creating a fresh database, these columns are already represented in the main schema.
