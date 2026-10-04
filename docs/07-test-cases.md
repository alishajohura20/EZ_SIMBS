# EZ_SIMBS - Test Cases

## 1. Authentication Tests

| # | Test Case | Input | Expected Result |
|---|-----------|-------|-----------------|
| T1.1 | Login with valid admin credentials | email: admin@ezsimbs.local, password: admin123 | 200, success: true, role: admin |
| T1.2 | Login with invalid email | email: wrong@email.com, password: admin123 | success: false |
| T1.3 | Login with invalid password | email: admin@ezsimbs.local, password: wrong | success: false |
| T1.4 | Login with empty fields | email: "", password: "" | success: false |
| T1.5 | Password is bcrypt hashed | Check DB users table | password starts with $2y$ |
| T1.6 | Session created on login | Check $_SESSION after login | user_id, user_role set |

## 2. Product Tests

| # | Test Case | Input | Expected Result |
|---|-----------|-------|-----------------|
| T2.1 | Create product | name: "Test", price: 29.99, cost: 15.00 | success: true, auto SKU generated |
| T2.2 | SKU auto-generation | Create without SKU | SKU matches pattern SKU-XXXXXX |
| T2.3 | Get product by ID | GET with valid id | product data returned |
| T2.4 | List products with search | search=Test | filtered results |
| T2.5 | Update product | PUT with new name/price | success: true |
| T2.6 | Delete product | DELETE with id | success: true |
| T2.7 | Barcode lookup | barcode=SKU-XXXXXX | product found |

## 3. Sales Tests

| # | Test Case | Input | Expected Result |
|---|-----------|-------|-----------------|
| T3.1 | Create sale with items | items: [{product_id, qty, price}] | invoice_no generated, grand_total > 0 |
| T3.2 | Stock deduction | Sale of qty 1 | inventory qty decreases by 1 |
| T3.3 | Hold sale | Hold action | status: held, stock restored |
| T3.4 | Cancel sale | Cancel action | status: cancelled, stock restored |
| T3.5 | Process return | Return action | stock restored |
| T3.6 | Invoice generation | Complete sale | invoice record created |
| T3.7 | Loyalty points | Sale with customer | points awarded |

## 4. Purchase Tests

| # | Test Case | Input | Expected Result |
|---|-----------|-------|-----------------|
| T4.1 | Create PO | supplier_id + items | po_number generated, status: draft |
| T4.2 | Approve PO | status → approved | status updated |
| T4.3 | Receive stock | status → received | inventory qty increased |
| T4.4 | Add payment | amount + method | paid/due updated |
| T4.5 | Partial payment | amount < total | due = total - paid |

## 5. Inventory Tests

| # | Test Case | Input | Expected Result |
|---|-----------|-------|-----------------|
| T5.1 | Stock in | action: in, qty: 50 | qty increases by 50 |
| T5.2 | Stock out | action: out, qty: 10 | qty decreases by 10 |
| T5.3 | Adjust stock | action: adjust, qty: new_value | qty set to new value |
| T5.4 | Record damage | action: damage, qty: 5 | qty decreases, logged as damage |
| T5.5 | Stock transfer | action: transfer, from, to, qty | from decreases, to increases |
| T5.6 | Low stock detection | qty <= reorder_level | flagged as low stock |
| T5.7 | Stock logs | Any stock operation | log entry created |

## 6. Dashboard Tests

| # | Test Case | Input | Expected Result |
|---|-----------|-------|-----------------|
| T6.1 | Get dashboard stats | GET /dashboard/stats.php | today_sales, monthly_sales, total_products, low_stock present |
| T6.2 | Get chart data | GET /dashboard/charts.php?period=monthly | sales_chart array returned |
| T6.3 | Stats are numeric | Check types | all values are numbers |

## 7. Notification Tests

| # | Test Case | Input | Expected Result |
|---|-----------|-------|-----------------|
| T7.1 | Get notifications | GET /notifications/index.php | notifications array + unread_count |
| T7.2 | Mark as read | POST action: read | is_read → 1 |
| T7.3 | Mark all read | POST action: read_all | all is_read → 1 |
| T7.4 | Delete notification | POST action: delete | removed from DB |
| T7.5 | Check alerts | GET /notifications/check.php | low_stock_alerts, out_of_stock_alerts counts |

## 8. Auto-Reorder Tests

| # | Test Case | Input | Expected Result |
|---|-----------|-------|-----------------|
| T8.1 | Low stock triggers suggestion | Product below reorder_level | suggestion returned |
| T8.2 | Auto-PO creation | Low stock + preferred supplier | draft PO created |
| T8.3 | Preferred supplier selection | Product with purchase history | most-used supplier selected |
| T8.4 | No duplicate POs | Pending PO exists for product | no new PO created |

## 9. Settings Tests

| # | Test Case | Input | Expected Result |
|---|-----------|-------|-----------------|
| T9.1 | Get grouped settings | GET /settings/index.php | settings grouped by group |
| T9.2 | Save settings | POST action: save | settings updated |
| T9.3 | Set theme | POST action: set_theme, theme: dark | session + DB updated |
| T9.4 | Create backup | POST action: backup | .sql file created in backup/ |
| T9.5 | List backups | POST action: get_backups | backup files listed |
