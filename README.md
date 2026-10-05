# EZ_SIMBS

Smart Inventory Management & Billing System

A full-stack PHP 8 + MySQL 8 web application for managing inventory, sales, purchases, and billing with role-based access control.

## Features

- **Authentication** — Login/logout with lockout, password change, forgot-password with working reset page, self-service registration with **role selection** (Customer / Cashier / Branch Manager / Manager), role-based access control; every auth page has a "Back to Store" link to the landing page
- **Store Registration** — The landing page's **"Create free account"** button signs up a *store*, not just a user: it collects store info (name, owner, contact, address) and creates the store, its first branch, and the store-owner (`Admin`) account in one go, then auto-logs you into the new **Store Home**
- **Store Home & Role-Based Dashboards** — After registering, the **Store Home** page shows your store's profile, team, and quick stats, plus a **Role Dashboards** hub listing the 5 store roles — **Admin (Store Owner), Manager, Branch Manager, Branch Cashier, Customer** — each with its own tailored dashboard (`pages/roles/`). Access is role-aware: an Admin can open all 5 dashboards, a Manager the Manager/Branch Manager/Cashier/Customer ones, Branch Managers their branch + cashier/customer, a Cashier only cashier + customer. Backed by a new `stores` table with `store_id` on `users` and `branches`
- **Multi-Session (Concurrent Logins)** — Every login gets its own tracked session in `user_sessions` (device, IP, browser, expiry, last activity) so the same account can be signed in on many devices at once without kicking anyone out; per-session remember-me cookies (30 days); pages/sessions to view and **end individual devices or all others**; admins get a system-wide session overview and can revoke/end sessions for any user; password change/reset automatically terminates other sessions
- **Public Storefront (Landing)** — Positions EZ SIMBS as the inventory & billing/POS system for **online + offline stores**: hero "Run your store — online & offline", trust bar (real-time inventory, POS & billing, online+offline, 24/7 support), "Why EZ SIMBS" (POS & billing, live inventory, online/offline sync, reports & alerts), plus the live web storefront (catalog, featured products, search overlay, wishlist, add-to-cart) shown as proof of the shopping side; viewport-adaptive hero, scroll-reveal animations, sign-in/register CTAs
- **Customer Dashboard (UniMart-style super shop)** — The logged-in customer section is a **standalone premium online super shop** (indigo/violet branding matching the admin theme, no admin shell): sticky header with left-prefix search icon, live wishlist/cart count badges and account menu, category nav strip, auto-rotating hero banner slider with an **advertisement image portion** on each slide (bundled promo graphics in `assets/images/ads/`, overridden by a banner's uploaded image when set), trust bar, Shop by Category tiles, New Arrivals, and a sortable/searchable/paginated product grid with NEW/stock badges, ratings, wishlist hearts and Add-to-Cart. Full UniMart cart page (qty steppers, sticky order summary, coupon + checkout modal), wishlist grid, and card-style order history with status chips + detail modal. Powered by the same `api/storefront/*` endpoints as the public storefront
- **Online Orders** — Customers place storefront orders (`orders`/`order_items`) with coupon discounts and tax; separate from POS sales
- **Catalog** — Products, categories, brands, units CRUD; image upload; auto-SKU; barcode lookup; CSV export
- **Inventory** — Stock in/out/adjust/damage/return/transfer; summary cards; stock logs; low/overstock detection; reorder-level notifications
- **Suppliers** — CRUD with ratings, tax ID, contact info; purchase history; outstanding-due tracking; delete protection
- **Customers** — CRUD with auto membership ID (MEM-XXXXXX); loyalty points; VIP flagging; phone lookup for POS; delete protection
- **Purchases** — PO creation with multi-item rows; auto PO number; tax calculation; status workflow (Draft→Pending→Approved→Received→Cancelled); auto-stock update on receive; partial payments; payment tracking; monthly report
- **Supplier Returns & Credits** — Return goods to a supplier from a received PO (admin/manager/branch-manager only); request→approve→receive→credit lifecycle with rejection + photo evidence; credits settle the PO's `due` first and overflow into a running `suppliers.balance` you can refund or apply to later POs; separate Supplier Returns page, seventh reports tab, and balance visibility in the Suppliers list
- **POS Exchanges** — One atomic counter workflow: receive returned goods, pick replacements at current catalog prices, and settle the price difference (customer pays more or admin/manager cashes out) in a single transaction; restock + replacement sale + cross-links (`sales.exchange_return_id` ↔ `sale_returns.exchange_sale_id`) + loyalty clawback + ledger entry (`sale_return_payments.method = 'exchange'`); cashier allowed at zero or positive difference only
- **Sales / POS** — Product grid + barcode scan; cart with qty/remove; customer phone lookup; cash/card/mobile/mixed payments; discount/tax/shipping calc; stock deduction with validation; hold/cancel (restores stock)/return (restores stock); receipt printing; QR-code invoice; loyalty points auto-award; daily sales summary
- **Dashboard** — 8 stat cards (today/monthly sales, profit, orders, products, customers, low stock, pending payments); Chart.js sales chart (daily/weekly/monthly); top selling products; recent sales; low stock alerts
- **Reports** — Sales, inventory, profit/loss, purchase, and customer reports with date range filtering and CSV export
- **Notifications** — Real-time alerts for low stock, out of stock, and due payments; mark read/delete; 30-second auto-refresh
- **Auto-Reorder** — Automatically creates draft purchase orders when stock drops below reorder level; selects preferred supplier from purchase history
- **Multi-Branch** — Branch CRUD with manager assignment; per-branch inventory and stats; stock transfer suggestions between branches
- **Audit & Settings** — Full activity log with filters/pagination; system settings (company info, billing config, inventory threshold, dark/light theme); database backup/restore
- **Testing** — Postman collection with all endpoints; Jest test suite for auth, products, customers, sales, dashboard, and active sessions

## Tech Stack

- **Backend:** PHP 8 (Vanilla, no framework)
- **Database:** MySQL 8
- **Frontend:** Bootstrap 5, Vanilla JavaScript, Chart.js
- **Auth:** Session-based with password hashing (bcrypt)


## Installation

### 1. Clone the repo

```bash
git clone <repo-url> ez_simbs
cd ez_simbs
```

### 2. Create the database and import schema

```bash
mysql -u root -p -e "CREATE DATABASE IF NOT EXISTS ez_simbs"
mysql -u root -p ez_simbs < ez_simbs.sql
```

Or if your MySQL user is not root:

```bash
mysql -u aj -p ez_simbs < ez_simbs.sql
```

### 3. Configure database credentials

Edit `includes/config.php` and update the DB constants:

```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'ez_simbs');
define('DB_USER', 'aj');        // your MySQL username
define('DB_PASS', 'root123');   // your MySQL password
```

### 4. App URL (auto-detected)

`APP_URL` is derived automatically from the request (scheme + host + project path), so it works out of the box with both `php -S` and Apache — no manual configuration needed.

### 5. Set web server document root

Point your Apache/Nginx document root to the project directory:

- **Apache:** Set `DocumentRoot` to `/path/to/ez_simbs`
- **Nginx:** Set `root` to `/path/to/ez_simbs`

### 6. Create upload directories (writable by web server)

```bash
chmod -R 775 uploads/ exports/ backup/
chown -R www-data:www-data uploads/ exports/ backup/
```

### 7. Login

Open in browser and log in. The following **fixed demo accounts** are seeded and map to each role dashboard:

| Role           | Account                     | Password  | Lands On                          |
|----------------|-----------------------------|-----------|-----------------------------------|
| Admin          | `admin@ezsimbs.local`       | `admin123` | Store Home (owner command center)  |
| Manager        | `manager@ezsimbs.local`     | `admin123` | Staff dashboard (reports, purchases) |
| Branch Manager | `branch@ezsimbs.local`      | `admin123` | Staff dashboard (branch inventory)  |
| Cashier        | `cashier@ezsimbs.local`     | `admin123` | Staff dashboard (POS / billing)     |
| Customer       | `customer@ezsimbs.local`    | `admin123` | Customer storefront dashboard       |

New visitors landing on the homepage (logged out) see the public storefront. The **Create free account** button registers a new **store** (store info + owner login in one step). Existing-store staff/customers can still sign up via the **Register As** role selector on the account-registration page.

**Change passwords immediately after first login.**

---

### 8. Optional: load the manual-testing seed data

`ez_simbs_seed_data.sql` adds a deterministic dataset for hands-on feature testing: **exactly 20 rows (ids `1001`–`1020`) in every one of the 33 tables**, covering every status, payment method, workflow stage and edge case the app supports.

```bash
mysql -u aj -p ez_simbs < ez_simbs_seed_data.sql
```

- **Strictly additive** — it only `INSERT`s and never deletes or updates a pre-existing row. All ids live in the `1001`–`1020` band and every generated code is `SEED_`-prefixed (`SEED-SKU-####`, `SEED-PO-…`, `SEED-INV-…`, `SEED-ORD-…`), so it can never collide with live data.
- **Not idempotent** (plain `INSERT`s). To reload it, run the commented `REMOVE SEED DATA` block at the bottom of the file first, or restore your own backup. Prefer a `mysqldump` snapshot before loading it.
- The bottom of the file contains **12 verification queries** that print only mismatches. Run them after importing; empty output means the dataset is internally consistent.
- All 20 seeded users share the password `admin123`. The role-gate accounts are `seed.admin@`, `seed.manager@`, `seed.branch@`, `seed.cashier@` and `seed.customer@` (all `@ezsimbs.test`).
- Branches `1001`–`1004` are attached to store `1` on purpose, so the existing `admin@ezsimbs.local` account can exercise the multi-branch screens without switching tenant.

See **[`SEED_DATA.txt`](SEED_DATA.txt)** for the full credentials list, the id map, every table's deliberate edge cases, and seven suggested test walkthroughs.

**Note on `stock_logs`:** the 20 seeded rows are a representative sample covering each movement type, **not** a complete audit ledger — `SUM(in) - SUM(out)` will not equal `inventory.qty`. Every seeded `in`/`out` row does reference a real purchase or sale that contains that product, and the held and cancelled sales correctly have no stock movement at all.

---

## Running the Dev Server

This is a pure PHP project (no Node/Composer tooling required). Use PHP's built-in web server for development.

### 1. Install PHP

On Ubuntu/Debian:

```bash
sudo apt update
sudo apt install php php-cli php-mysql
```

Or use XAMPP ([apachefriends.org](https://www.apachefriends.org/)) and add `/opt/lampp/bin` to your PATH.

Verify PHP is installed:

```bash
php -v
```

### 2. Start the server

From the project root:

```bash
php -S localhost:8000
```

- Server: `http://localhost:8000`
- Non-root port option: `php -S localhost:8080`

### 3. Open the app

Visit `http://localhost:8000` — logged-out visitors see the public storefront (`landing.php`); staff redirect to their dashboard; customers to the customer dashboard.

Tip: make sure the `mysql` service is running first (e.g. `sudo systemctl start mysql` or via XAMPP control panel).

## Project Structure

```
ez_simbs/
├── assets/          CSS, JS, images, fonts (incl. storefront.css + storefront.js for the customer storefront)
├── classes/         PHP class files (Auth, Brand, Category, Customer, Dashboard, Inventory, Invoice, Product, Purchase, Report, Sale, Supplier, Unit, Notification, AutoReorder, ActivityLog, Settings, Branch, Storefront, Store)
├── includes/        Config, DB, helpers, layout partials (incl. storefront-header/footer.php)
├── api/             REST-style JSON API endpoints (auth, users, categories, brands, units, products, inventory, suppliers, customers, purchases, sales, invoices, dashboard, reports, notifications, settings, branches, activity-logs, storefront)
├── pages/           Page controllers (auth, dashboard, products, inventory, suppliers, customers, purchases, sales/POS, invoices, reports, notifications, settings, branches, activity-log, users, categories, brands, units, customer, store, roles)
├── docs/            Project documentation (overview, architecture, ERD, DFD, use cases, API docs, test cases, user manual, deployment guide)
├── uploads/         User-uploaded files
├── exports/         PDF/Excel exports
├── tests/           Jest/Postman test files
├── backup/          Database backups
├── ez_simbs.sql     Full schema + seed data
├── ez_simbs_storefront.sql  Storefront tables migration (banners, wishlist, cart, reviews, coupons, orders)
├── ez_simbs_stores.sql      Store tables migration (stores + store_id on users/branches)
├── landing.php      Public storefront landing page
└── index.php        Entry point (routes by role / landing for guests)
```

## Default Roles

| Role            | Access                                |
|-----------------|---------------------------------------|
| admin           | Full system access                    |
| manager         | Reports, analytics, supplier/purchase |
| branch_manager  | Branch-specific inventory, sales      |
| cashier         | POS, invoicing, customer lookup       |
| customer        | View own invoices, loyalty points     |

## Default Settings

| Setting | Value |
|---------|-------|
| Company | EZ_SIMBS Store |
| Currency | USD ($) |
| Tax Rate | 10% |
| Timezone | UTC |

## Development

This project uses no build tools or package managers for PHP. Just edit PHP/JS/CSS files directly.

### Running Tests

```bash
# Install Jest (optional, for API tests)
npm install

# Run all tests
npm test

# Run specific test suite
npm run test:auth
npm run test:products
npm run test:sales
npm run test:dashboard
```

To enable error reporting (already on in dev), in `includes/config.php`:

```php
error_reporting(E_ALL);
ini_set('display_errors', 1);
```

To disable in production:

```php
error_reporting(0);
ini_set('display_errors', 0);
```

## License

Private - All rights reserved.
