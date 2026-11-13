# AnnSatu

AnnSatu is a restaurant website and lightweight management system built with PHP, MySQL, HTML, CSS, and JavaScript. Guests can browse the menu, place pickup or delivery orders, request a table, and leave feedback. Staff can sign in to manage orders and reservations and read guest notes.

## Run locally with XAMPP

1. Copy the project folder into `C:\xampp\htdocs\AnnSatu`.
2. Start **Apache** and **MySQL** from the XAMPP Control Panel.
3. Open phpMyAdmin at `http://localhost/phpmyadmin`, import `schema.sql`, and make sure the database is named `annsatu`.
4. If your MySQL connection differs from the defaults (`127.0.0.1`, user `root`, no password), set `DB_HOST`, `DB_NAME`, `DB_USER`, and `DB_PASS` in your PHP environment before starting Apache.
5. Visit `http://localhost/AnnSatu/` to explore the restaurant.
6. Visit `http://localhost/AnnSatu/setup.php` once to create the first staff account. Use a password with at least 12 characters. After that account is created, setup closes and the staff dashboard is available at `http://localhost/AnnSatu/admin.php`.

The setup page only allows account creation while the employee table is empty. Do not expose a development server or phpMyAdmin to the public internet.

## Included features

- Responsive restaurant landing page, menu category filters, and a working cart.
- Server-validated pickup and delivery orders; menu prices are read from MySQL, not trusted from the browser.
- Table requests with date, time, and party-size validation, plus customer cancellation using the confirmation number and booking phone.
- Customer feedback form and staff dashboard.
- Employee sign-in with password hashing, session protection, and order/reservation status updates.
- Prepared SQL statements, output escaping, and session-bound CSRF tokens.

## Database configuration

`bootstrap.php` uses these defaults for XAMPP: host `127.0.0.1`, database `annsatu`, user `root`, and an empty password. Override them with the `DB_HOST`, `DB_NAME`, `DB_USER`, and `DB_PASS` environment variables when needed. PHP requires PDO MySQL; the setup and feedback forms also use `mbstring`.

The menu is shown in `index.php` and its matching purchasable prices are seeded in `schema.sql`. If you change a menu item's ID or price, update both places.
