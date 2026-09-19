# GadgetMart Store

PHP/MySQL ecommerce project for InfinityFree. The storefront follows the supplied GadgetMart screenshots: navy background, orange accents, Bengali headings, category cards, product cards, offers, footer and responsive layouts.

## Important upload layout
Upload the contents of this repository into the hosting `htdocs` root. Keep these paths unchanged:

- `index.php` — storefront homepage
- `config/config.php` — database credentials
- `assets/style.css` — stylesheet (CSS only)
- `database/schema.sql` — SQL only; import this in phpMyAdmin, never upload it as PHP
- `admin/` — admin area
- `user/` — customer area
- `uploads/products/` — writable product image directory

Never rename `checkout.php`, `product.php`, or another PHP file to `schema.sql`. After creating the first admin, delete `install.php`.
