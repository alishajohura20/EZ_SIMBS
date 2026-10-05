# EZ_SIMBS - User Manual

## Getting Started

### Login

1. Open the application in your browser
2. Enter your email and password
3. Click **Login**

**Default Admin Credentials:**
- Email: `admin@ezsimbs.local`
- Password: `admin123`

---

## Dashboard

The dashboard displays real-time business metrics:

- **Today's Sales** - Total revenue and order count for today
- **Monthly Sales** - Revenue and profit for the current month
- **Products** - Total active products in the system
- **Customers** - Total active customers
- **Low Stock** - Products below reorder level (click to view suggestions)
- **Pending Payments** - Outstanding purchase payments
- **Sales Chart** - Toggle between daily/weekly/monthly views
- **Top Products** - Best-selling products in the last 30 days
- **Recent Sales** - Latest completed transactions
- **Low Stock Alert** - Products needing reorder

---

## Products

### Adding a Product

1. Go to **Products** in the sidebar
2. Click **Add Product**
3. Fill in: Name, Category, Brand, Unit, Price, Cost
4. SKU is auto-generated (or enter manually)
5. Upload an image (optional)
6. Click **Save**

### Searching Products

Use the search bar to find products by name or SKU. Filter by category, brand, or status.

---

## Inventory

### Stock Operations

1. Go to **Inventory** → click a product
2. Choose operation:
   - **Stock In** - Add stock (purchase received, returns)
   - **Stock Out** - Remove stock (sales, manual removal)
   - **Adjust** - Set exact quantity
   - **Damage** - Record damaged items
   - **Transfer** - Move stock between branches

### Stock Logs

View complete history of all stock movements with filters by type and date.

---

## Sales / POS

### Making a Sale

1. Go to **Sales / POS**
2. Browse products or scan barcode
3. Click a product to add to cart (or type quantity)
4. Select customer (optional - for loyalty points)
5. Choose payment method: Cash, Card, Mobile, or Mixed
6. Apply discount/tax if needed
7. Click **Complete Sale**

### Hold / Cancel / Return

- **Hold** - Save sale for later (stock restored temporarily)
- **Cancel** - Cancel completed sale (stock restored permanently)
- **Return** - Process a return (stock restored)

---

## Purchases

### Creating a Purchase Order

1. Go to **Purchases** → **Create PO**
2. Select supplier
3. Add products with quantities and costs
4. Click **Create**

### PO Lifecycle

```
Draft → Pending → Approved → Received
  └→ Cancelled
```

### Receiving Stock

1. Open the PO
2. Click **Receive** (stock is automatically added to inventory)

### Making Payments

1. Open the PO
2. Click **Add Payment**
3. Enter amount and method
4. Partial payments are supported

---

## Reports

Go to **Reports** to view analytics:

- **Sales Report** - Orders, revenue, discounts, payment breakdown, daily chart
- **Inventory Report** - Stock levels, values, out-of-stock items
- **Profit/Loss** - Revenue vs COGS, gross profit, margin percentage
- **Purchases** - PO totals by supplier
- **Customers** - Spending, orders, loyalty points, VIP status

All reports support date range filtering and CSV export.

---

## Notifications

- Click the bell icon in the navbar to view notifications
- **Low Stock** alerts warn when products need reorder
- **Out of Stock** alerts for zero-quantity items
- **Payment Due** alerts for outstanding PO balances
- Notifications auto-refresh every 30 seconds
- Use **Mark All Read** or **Delete All** for bulk actions

---

## Auto-Reorder (Wow Factor)

When stock drops below the reorder level:

1. System detects the low stock
2. Finds the preferred supplier (most-used from purchase history)
3. Automatically creates a **draft Purchase Order**
4. Sends notification to managers

View suggestions at **Notifications → Reorder Suggestions**.

---

## Branches

### Managing Branches

1. Go to **Branches** (admin/manager only)
2. Click **Add Branch** → enter name, address, phone, manager
3. View branch cards with stats

### Branch Details

Click a branch to see:
- Products, stock value, low stock count
- Completed sales, active staff
- **Transfer Suggestions** - stock available from other branches

### Stock Transfer

From branch details, click **Transfer** to move stock between branches.

---

## Settings

Admin-only panel with 5 tabs:

- **Company** - Name, email, phone, tax ID, address
- **Billing** - Tax rate, currency, invoice/PO prefixes
- **Inventory** - Low stock threshold
- **Appearance** - Light/Dark theme toggle
- **Backup** - Create/restore database backups

---

## Activity Log

View complete audit trail of all system actions:
- Filter by action, resource, date range
- Paginated results (50 per page)
- Shows user, timestamp, IP address

---

## Tips

- Use **barcode scanning** in POS for faster checkout
- Set **reorder levels** on products to enable auto-reorder
- Assign **customers** to sales for loyalty point tracking
- Use **CSV export** on reports for external analysis
- **Backup regularly** from Settings → Backup tab
