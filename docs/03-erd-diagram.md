# EZ_SIMBS - Entity Relationship Diagram

> Render this with any Mermaid-compatible tool (GitHub, VS Code, mermaid.live)

```mermaid
erDiagram
    USERS ||--o{ SALES : creates
    USERS ||--o{ PURCHASES : creates
    USERS ||--o{ ACTIVITY_LOGS : generates
    USERS }o--|| ROLES : has
    USERS }o--o{ BRANCHES : belongs_to

    PRODUCTS ||--o{ INVENTORY : tracked_in
    PRODUCTS ||--o{ SALE_ITEMS : sold_in
    PRODUCTS ||--o{ PURCHASE_ITEMS : bought_in
    PRODUCTS ||--o{ STOCK_LOGS : logged_in
    PRODUCTS }o--o{ CATEGORIES : belongs_to
    PRODUCTS }o--o{ BRANDS : belongs_to
    PRODUCTS }o--o{ UNITS : measured_in

    SALES ||--o{ SALE_ITEMS : contains
    SALES ||--o{ INVOICES : generates
    SALES }o--o{ CUSTOMERS : for

    PURCHASES ||--o{ PURCHASE_ITEMS : contains
    PURCHASES }o--o{ SUPPLIERS : from
    PURCHASES ||--o{ PURCHASE_PAYMENTS : has

    PURCHASES ||--o{ PURCHASE_RETURNS : returned_via
    PURCHASE_RETURNS ||--o{ PURCHASE_RETURN_ITEMS : contains
    PURCHASE_RETURNS ||--o{ PURCHASE_RETURN_PAYMENTS : credited_by
    PURCHASE_RETURNS }o--o{ SUPPLIERS : from

    SALES ||--o{ SALE_RETURNS : returned_via
    SALE_RETURNS ||--o{ SALE_RETURN_ITEMS : contains
    SALE_RETURNS ||--o{ SALE_RETURN_PAYMENTS : refunded_by
    SALE_RETURNS ||--o{ RETURN_PHOTOS : evidences
    SALES }|--|| SALE_RETURNS : exchange_sale (Phase 15: sales.exchange_return_id <-> sale_returns.exchange_sale_id)
    PRODUCTS ||--o{ SALE_RETURN_ITEMS : returned_in
    PRODUCTS ||--o{ PURCHASE_RETURN_ITEMS : sent_back_in
    PURCHASE_RETURNS ||--o{ STOCK_LOGS : ships_out (purchase_return)

    BRANCHES ||--o{ INVENTORY : stocks
    BRANCHES ||--o{ STOCK_LOGS : tracks
    BRANCHES }o--o{ USERS : managed_by

    NOTIFICATIONS }o--o{ USERS : for

    USERS {
        int id PK
        int role_id FK
        int branch_id FK
        string name
        string email
        string phone
        string password
        string status
        datetime created_at
    }

    ROLES {
        int id PK
        string name
        string description
    }

    PRODUCTS {
        int id PK
        int category_id FK
        int brand_id FK
        int unit_id FK
        string name
        string sku
        string barcode
        decimal price
        decimal cost
        string image
        string status
    }

    CATEGORIES {
        int id PK
        int parent_id FK
        string name
        string description
    }

    BRANDS {
        int id PK
        string name
        string logo
    }

    UNITS {
        int id PK
        string name
        string symbol
    }

    INVENTORY {
        int id PK
        int product_id FK
        int branch_id FK
        int qty
        int reorder_level
        int min_stock
    }

    SALES {
        int id PK
        int customer_id FK
        int branch_id FK
        int created_by FK
        string invoice_no
        decimal subtotal
        decimal discount
        decimal tax
        decimal shipping
        decimal grand_total
        string payment_method
        string status
        int exchange_return_id FK
    }

    SALE_ITEMS {
        int id PK
        int sale_id FK
        int product_id FK
        int qty
        decimal unit_price
        decimal total
    }

    INVOICES {
        int id PK
        int sale_id FK
        string invoice_no
        text qr_data
    }

    PURCHASES {
        int id PK
        int supplier_id FK
        int branch_id FK
        int created_by FK
        string po_number
        decimal total
        decimal paid
        decimal due
        string status
        text notes
    }

    PURCHASE_ITEMS {
        int id PK
        int purchase_id FK
        int product_id FK
        int qty
        int received_qty
        int returned_qty
        decimal unit_cost
        decimal total
    }

    PURCHASE_PAYMENTS {
        int id PK
        int purchase_id FK
        decimal amount
        string method
        text notes
    }

    PURCHASE_RETURNS {
        int id PK
        int purchase_id FK
        int supplier_id FK
        int branch_id FK
        string return_no
        string status
        string reason_code
        text reason
        string method
        decimal subtotal
        decimal tax
        decimal credit_total
        decimal applied_to_due
        decimal to_balance
        int requested_by FK
        int approved_by FK
        int received_by FK
        int credited_by FK
        text admin_note
    }

    PURCHASE_RETURN_ITEMS {
        int id PK
        int return_id FK
        int purchase_item_id FK
        int product_id FK
        int qty
        decimal unit_cost
        decimal total
        string condition_note
    }

    PURCHASE_RETURN_PAYMENTS {
        int id PK
        int return_id FK
        decimal amount
        string method
        string reference
        text notes
        int added_by FK
    }

    SALE_RETURNS {
        int id PK
        int sale_id FK
        int order_id FK
        int branch_id FK
        string return_no
        string status
        decimal subtotal
        decimal discount
        decimal tax
        decimal shipping
        decimal refund_total
        string reason
        text admin_note
        int exchange_sale_id FK
    }

    SALE_RETURN_ITEMS {
        int id PK
        int return_id FK
        int sale_item_id FK
        int order_item_id FK
        int product_id FK
        int qty
        decimal unit_price
        decimal discount
        decimal total
        string reason
        int restock
        text condition_note
    }

    SALE_RETURN_PAYMENTS {
        int id PK
        int return_id FK
        decimal amount
        string method
        string reference
        int processed_by FK
    }

    RETURN_PHOTOS {
        int id PK
        string return_kind
        int return_id FK
        int return_item_id FK
        string path
        int uploaded_by FK
    }

    SUPPLIERS {
        int id PK
        string company_name
        string contact_name
        string phone
        string email
        string address
        int rating
        string status
        decimal balance
    }

    CUSTOMERS {
        int id PK
        string name
        string phone
        string email
        string membership_id
        int loyalty_points
        int is_vip
        string status
    }

    BRANCHES {
        int id PK
        int manager_id FK
        string name
        string address
        string phone
        string status
    }

    STOCK_LOGS {
        int id PK
        int product_id FK
        int branch_id FK
        int user_id FK
        string type
        int qty
        int qty_before
        int qty_after
        text notes
    }
    # type enum: in, out, adjustment, damage, return, transfer, sale_return,
    #            purchase_return (Phase 14 outbound; void-restock uses 'in')
    # sale_return / purchase_return rows: reference_id = the RETURN id

    ACTIVITY_LOGS {
        int id PK
        int user_id FK
        string action
        string resource
        int resource_id
        text old_value
        text new_value
        string ip_address
        string browser
    }

    NOTIFICATIONS {
        int id PK
        int user_id FK
        string title
        text message
        string type
        string link
        int is_read
    }

    SETTINGS {
        int id PK
        string setting_key
        text setting_value
        string setting_group
    }
```
