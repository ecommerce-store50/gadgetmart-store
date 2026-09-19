# GadgetMart Store

InfinityFree-compatible PHP/MySQL ecommerce starter with a configurable storefront and admin settings.

## Included
- Bengali GadgetMart-style homepage
- Admin login and dashboard
- Editable site name, tagline, footer description, phone, email, address
- Editable Facebook, Instagram, YouTube, X/Twitter and WhatsApp links
- Categories, products, stock and orders schema

## Installation
1. Create a MySQL database in InfinityFree and import `database/schema.sql`.
2. Copy `config/config.example.php` to `config/config.php` and enter database credentials.
3. Upload the repository files to `htdocs`.
4. Open `/install.php` once to create the first admin (change/delete this file afterward).
5. Visit `/admin/login.php`.

Do not commit real credentials. Change the generated admin password and remove `install.php` after setup.
