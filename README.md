# GadgetMart Store

PHP/MySQL ecommerce starter for InfinityFree.

## Payment mode
- **Cash on Delivery** is the real enabled checkout mode.
- bKash, Nagad and Card are **demo-only** options. They never call a payment gateway and never charge money; they generate a `DEMO-...` reference for testing.

## Update an existing database
1. Import `database/migrations/002_demo_payments.sql` once.
2. If you created the database from scratch, use the current `database/schema.sql`.
3. Configure `config/config.php` using your InfinityFree database credentials.
4. Ensure `uploads/products/` is writable for image uploads.

## Security notes
Delete `install.php` after creating the first admin. Use HTTPS, strong credentials, and do not store real payment secrets in the repository.
