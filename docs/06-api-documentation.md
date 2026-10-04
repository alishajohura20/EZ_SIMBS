# EZ_SIMBS - API Documentation

## Base URL

```
http://localhost:8000/api
```

## Authentication

All API endpoints require an active session. Login via `POST /api/auth/login.php`.

## Response Format

**Success:**
```json
{ "success": true, "message": "Success", "data": { ... } }
```

**Error:**
```json
{ "success": false, "message": "Error description" }
```

## Endpoints

### Auth

| Method | Endpoint | Description | Auth |
|--------|----------|-------------|------|
| POST | `/auth/login.php` | Login | No |
| GET | `/auth/logout.php` | Logout | Yes |
| POST | `/auth/register.php` | Register (public) | No |

### Users

| Method | Endpoint | Description | Role |
|--------|----------|-------------|------|
| GET | `/users/index.php` | List users (search, role filter, pagination) | admin |
| GET | `/users/index.php?id=N` | Get single user | admin |
| POST | `/users/index.php` | Create user | admin |
| PUT | `/users/index.php?id=N` | Update user | admin |
| DELETE | `/users/index.php?id=N` | Delete user | admin |

### Products

| Method | Endpoint | Description | Role |
|--------|----------|-------------|------|
| GET | `/products/index.php` | List products (search, category, brand, status, pagination) | logged in |
| GET | `/products/index.php?id=N` | Get single product | logged in |
| POST | `/products/index.php` | Create product | admin, manager |
| PUT | `/products/index.php?id=N` | Update product | admin, manager |
| DELETE | `/products/index.php?id=N` | Delete product | admin |
| GET | `/products/barcode.php?barcode=X` | Lookup by barcode/SKU | logged in |

### Categories

| Method | Endpoint | Description | Role |
|--------|----------|-------------|------|
| GET | `/categories/index.php` | List categories | logged in |
| POST | `/categories/index.php` | Create category | admin, manager |
| PUT | `/categories/index.php?id=N` | Update category | admin, manager |
| DELETE | `/categories/index.php?id=N` | Delete category | admin |

### Brands

| Method | Endpoint | Description | Role |
|--------|----------|-------------|------|
| GET | `/brands/index.php` | List brands | logged in |
| POST | `/brands/index.php` | Create brand | admin, manager |
| PUT | `/brands/index.php?id=N` | Update brand | admin, manager |
| DELETE | `/brands/index.php?id=N` | Delete brand | admin |

### Units

| Method | Endpoint | Description | Role |
|--------|----------|-------------|------|
| GET | `/units/index.php` | List units | logged in |
| POST | `/units/index.php` | Create unit | admin, manager |
| PUT | `/units/index.php?id=N` | Update unit | admin, manager |
| DELETE | `/units/index.php?id=N` | Delete unit | admin |

### Suppliers

| Method | Endpoint | Description | Role |
|--------|----------|-------------|------|
| GET | `/suppliers/index.php` | List suppliers (search, rating, pagination) | logged in |
| GET | `/suppliers/index.php?id=N` | Get supplier profile | logged in |
| POST | `/suppliers/index.php` | Create supplier | admin, manager |
| PUT | `/suppliers/index.php?id=N` | Update supplier | admin, manager |
| DELETE | `/suppliers/index.php?id=N` | Delete supplier | admin |

### Customers

| Method | Endpoint | Description | Role |
|--------|----------|-------------|------|
| GET | `/customers/index.php` | List customers (search, VIP, pagination) | logged in |
| GET | `/customers/index.php?id=N` | Get customer profile | logged in |
| POST | `/customers/index.php` | Create customer | logged in |
| PUT | `/customers/index.php?id=N` | Update customer | logged in |
| DELETE | `/customers/index.php?id=N` | Delete customer | admin |
| GET | `/customers/lookup.php?phone=X` | Lookup by phone | logged in |

### Purchases

| Method | Endpoint | Description | Role |
|--------|----------|-------------|------|
| GET | `/purchases/index.php` | List POs (search, status, pagination) | logged in |
| GET | `/purchases/index.php?id=N` | Get PO details | logged in |
| POST | `/purchases/index.php` | Create PO | admin, manager |
| PUT | `/purchases/index.php?id=N` | Update PO | admin, manager |
| POST | `/purchases/status.php` | Update PO status | admin, manager |
| POST | `/purchases/payment.php` | Add payment to PO | admin, manager |

### Sales

| Method | Endpoint | Description | Role |
|--------|----------|-------------|------|
| GET | `/sales/index.php` | List sales (search, status, date, pagination) | logged in |
| GET | `/sales/index.php?id=N` | Get sale details | logged in |
| POST | `/sales/index.php` | Create sale (POS) | logged in |
| POST | `/sales/hold.php` | Hold sale | logged in |
| POST | `/sales/return.php` | Process return (legacy) | logged in |

### Returns (Customer, Phase 13)

| Method | Endpoint | Description | Role |
|--------|----------|-------------|------|
| GET | `/returns/index.php` | List returns (status, sale/order, date, pagination) | logged in |
| GET | `/returns/returnable.php?sale_id=N` | Eligible returnable items for a sale/order (over-return guard) | logged in |
| POST | `/returns/index.php` | Create return (request); settles to approved when no approval gate | logged in |
| POST | `/returns/approve.php` | Approve a requested return | admin, manager |
| POST | `/returns/receive.php` | Receive goods back + decide restock (records stock_logs) | admin, manager |
| POST | `/returns/refund.php` | Refund a received return (original tender) | admin, manager |
| POST | `/returns/void.php` | Void a return (unwind tally + stock + loyalty) | admin, manager |
| POST | `/returns/photos.php` | Upload return photos; `kind=sale` (Phase 13) or `kind=purchase` (Phase 14) | logged in |

### POS Exchanges (Phase 15)

| Method | Endpoint | Description | Role |
|--------|----------|-------------|------|
| POST | `/returns/exchange.php` | Complete an exchange: restock returned goods + create replacement sale + settle difference in one atomic transaction | admin, manager, branch_manager, cashier |

### Supplier Returns (Purchase, Phase 14)

| Method | Endpoint | Description | Role |
|--------|----------|-------------|------|
| GET | `/purchase-returns/index.php` | List supplier returns (search, status, pagination; branch-manager scoped) | admin, manager, branch_manager |
| GET | `/purchase-returns/index.php?id=N` | Get return details with lines + payments + photos | admin, manager, branch_manager |
| GET | `/purchase-returns/returnable.php?purchase_id=N` | Eligible returnable items for a received PO | admin, manager, branch_manager |
| POST | `/purchase-returns/index.php` | Create supplier return `{purchase_id, items[], reason_code, notes}` (approved, or requested when branch-manager + approval gate) | admin, manager, branch_manager |
| POST | `/purchase-returns/approve.php` | Approve a requested return | admin, manager, branch_manager (same branch) |
| POST | `/purchase-returns/receive.php` | Mark goods shipped back (records `purchase_return` stock out; stock may stay per line) | admin, manager, branch_manager |
| POST | `/purchase-returns/credit.php` | Settle the credit: reduce `purchases.due` first, owner-remainder to `suppliers.balance` (recorded in `purchase_return_payments`, not deleted on void) | admin, manager, branch_manager |
| POST | `/purchase-returns/void.php` | Void a return (restore stock + unwind due/supplier balance + mark cancelled) | admin, manager |

### Suppliers (running balance, Phase 14)

| Method | Endpoint | Description | Role |
|--------|----------|-------------|------|
| GET | `/suppliers/balance.php?id=N` | Get supplier running balance (credited-return overflow net) | admin, manager |
| POST | `/suppliers/balance.php` | `action=refund` pays the supplier down (deducts balance, logs a refund) | admin, manager |

### Invoices

| Method | Endpoint | Description | Role |
|--------|----------|-------------|------|
| GET | `/invoices/index.php` | List invoices | logged in |

### Inventory

| Method | Endpoint | Description | Role |
|--------|----------|-------------|------|
| GET | `/inventory/index.php` | List stock (branch, status, search) | logged in |
| POST | `/inventory/index.php` | Stock in/out/adjust/damage/return/transfer | logged in |
| GET | `/inventory/logs.php` | Stock logs (type, date filters) | logged in |
| GET | `/inventory/summary.php` | Stock summary cards | logged in |
| GET | `/inventory/branches.php` | Branch list for transfers | logged in |

### Dashboard

| Method | Endpoint | Description | Role |
|--------|----------|-------------|------|
| GET | `/dashboard/stats.php` | Dashboard statistics | logged in |
| GET | `/dashboard/charts.php?period=X` | Sales chart data (daily/weekly/monthly) | logged in |

### Reports

| Method | Endpoint | Description | Role |
|--------|----------|-------------|------|
| GET | `/reports/sales.php` | Sales report (date_from, date_to) | admin, manager |
| GET | `/reports/inventory.php` | Inventory report | admin, manager |
| GET | `/reports/profit-loss.php` | Profit/loss report | admin, manager |
| GET | `/reports/purchases.php` | Purchase report | admin, manager |
| GET | `/reports/customers.php` | Customer report | admin, manager |
| GET | `/reports/returns.php` | Customer-returns report (summary + breakdowns) | admin, manager |
| GET | `/reports/purchase-returns.php` | Supplier-returns report (summary, by reason/supplier, CSV) | admin, manager |

All report endpoints support `?export=csv` for CSV download.

### Notifications

| Method | Endpoint | Description | Role |
|--------|----------|-------------|------|
| GET | `/notifications/index.php` | List user notifications | logged in |
| POST | `/notifications/index.php` | Mark read / delete (action param) | logged in |
| GET | `/notifications/check.php` | Run alert detection + auto-reorder | admin, manager |
| GET | `/notifications/reorder-suggestions.php` | Get reorder suggestions | admin, manager |

### Settings

| Method | Endpoint | Description | Role |
|--------|----------|-------------|------|
| GET | `/settings/index.php` | Get all settings (grouped) | logged in |
| POST | `/settings/index.php` | Save / set_theme / backup / restore / get_backups | admin |

### Branches

| Method | Endpoint | Description | Role |
|--------|----------|-------------|------|
| GET | `/branches/index.php` | List branches | admin, manager |
| GET | `/branches/index.php?id=N` | Get branch summary | admin, manager |
| POST | `/branches/index.php` | Create branch | admin, manager |
| PUT | `/branches/index.php?id=N` | Update branch | admin, manager |
| DELETE | `/branches/index.php?id=N` | Deactivate branch | admin, manager |
| GET | `/branches/inventory.php?branch_id=N` | Branch inventory | logged in |
| GET | `/branches/suggestions.php?branch_id=N` | Transfer suggestions | admin, manager |

### Activity Logs

| Method | Endpoint | Description | Role |
|--------|----------|-------------|------|
| GET | `/activity-logs/index.php` | List logs (action, resource, date filters, pagination) | admin, manager |
| GET | `/activity-logs/stats.php` | Log statistics | admin |
