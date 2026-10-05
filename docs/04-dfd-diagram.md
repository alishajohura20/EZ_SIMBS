# EZ_SIMBS - Data Flow Diagram

> Render this with any Mermaid-compatible tool

## Level 0 - Context Diagram

```mermaid
graph LR
    U[User/Cashier] -->|Login, Sell, Purchase| S[EZ_SIMBS]
    S -->|Reports, Alerts| U
    S <-->|CRUD| DB[(MySQL Database)]
    M[Manager/Admin] -->|Configure, Monitor| S
    S -->|Statistics, Charts| M
```

## Level 1 - Major Processes

```mermaid
graph TB
    subgraph Input
        U[User] --> P1[Auth & Session]
        U --> P2[Product Catalog]
        U --> P3[POS / Sales]
        U --> P4[Purchases]
        U --> P5[Inventory Ops]
        M[Manager] --> P6[Reports & Dashboard]
        M --> P7[Settings & Admin]
        Sys[System] --> P8[Notifications]
    end

    subgraph Processing
        P1 -->|Session| DB[(Database)]
        P2 -->|Products| DB
        P3 -->|Sales + Stock Deduction| DB
        P4 -->|PO + Stock Addition| DB
        P5 -->|Adjust/Transfer| DB
        P6 -->|Queries + Aggregation| DB
        P7 -->|Config| DB
        P8 -->|Alerts| DB
    end

    subgraph Output
        DB -->|User Data| U
        DB -->|Dashboards, Charts| M
        DB -->|Low Stock, Due Payments| P8
    end
```

## Level 2 - Sales Flow

```mermaid
graph TD
    A[POS Page Load] -->|AJAX| B[api/products - Get Products]
    B --> C[Product Grid + Barcode Scan]
    C --> D[Add to Cart]
    D --> E[Select Payment Method]
    E --> F[api/sales - POST Create Sale]
    F --> G[Deduct Stock from Inventory]
    F --> H[Generate Invoice Number]
    F --> I[Create Invoice Record]
    F --> J[Award Loyalty Points]
    F --> K[Log Activity]
    G --> L[Return Receipt + Invoice]
```

## Level 2 - Auto-Reorder Flow

```mermaid
graph TD
    A[Manager clicks Run Check] -->|AJAX| B[api/notifications/check.php]
    B --> C[Notification.checkLowStock]
    B --> D[Notification.checkOutOfStock]
    B --> E[Notification.checkDuePayments]
    B --> F[AutoReorder.checkAndCreateReorderRequests]
    F --> G{Stock below reorder level?}
    G -->|Yes| H{Pending PO exists?}
    H -->|No| I[Find preferred supplier from history]
    I --> J[Auto-create Draft PO]
    J --> K[Log Activity]
    G -->|No| L[Skip]
    H -->|Yes| L
    F --> M[Send notification to managers]
```
