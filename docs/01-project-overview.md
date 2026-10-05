# EZ_SIMBS - Project Overview

## Introduction

EZ_SIMBS (Smart Inventory Management & Billing System) is a full-stack PHP web application designed for small to medium businesses to manage their inventory, sales, purchases, and billing operations efficiently.

## Key Features

- **Product Management**: Categories, brands, units, auto-SKU generation, barcode support
- **Inventory Tracking**: Multi-branch stock management, stock in/out/adjust/transfer, low-stock alerts
- **Sales & POS**: Full point-of-sale with cart, multi-payment methods, receipts, invoice generation with QR codes
- **Purchase Management**: PO lifecycle (draft → pending → approved → received), partial payments, auto-stock on receive
- **Customer Management**: Loyalty points, VIP status, membership IDs, purchase history
- **Supplier Management**: Star ratings, purchase history, due payment tracking
- **Auto-Reorder System**: Automatically creates draft purchase orders when stock drops below reorder level
- **Notifications**: Real-time alerts for low stock, out of stock, and due payments
- **Reports & Analytics**: Sales, inventory, profit/loss, purchase, and customer reports with CSV export
- **Dashboard**: Live stats cards, Chart.js visualizations, top products, recent sales
- **Multi-Branch Support**: Branch CRUD, per-branch inventory, stock transfer suggestions
- **Activity Logging**: Full audit trail of all system actions
- **Settings**: Company info, billing config, theme toggle, database backup/restore
- **Role-Based Access**: Admin, manager, branch manager, cashier roles with permission gating

## Technology Stack

| Layer | Technology |
|-------|-----------|
| Backend | PHP 8 (Vanilla, no framework) |
| Database | MySQL 8 |
| Frontend | Bootstrap 5, Vanilla JavaScript |
| Charts | Chart.js |
| Icons | Font Awesome 6 |
| Auth | Session-based with bcrypt password hashing |

## Default Credentials

| Role | Email | Password |
|------|-------|----------|
| Admin | admin@ezsimbs.local | admin123 |

## Project Structure

```
ez_simbs/
├── api/              # REST-style JSON API endpoints
├── assets/           # CSS, JS, fonts, images
├── backup/           # Database backup files
├── classes/          # PHP model classes (one per domain)
├── docs/             # Project documentation
├── exports/pdf/      # PDF exports
├── includes/         # Config, DB, layout partials, helpers
├── pages/            # Page controllers
├── tests/            # Postman collections + Jest tests
└── uploads/          # Product/invoice images
```
