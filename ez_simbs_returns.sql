-- ============================================================
-- EZ_SIMBS - Phase 13: Returns Management (Customer-Side)
-- For UPGRADING an existing pre-Phase-13 database.
-- Fresh installs get everything from ez_simbs.sql +
-- ez_simbs_storefront.sql (which now contain these tables).
-- Tables: 33 -> 37 (adds sale_returns, sale_return_items,
--                  sale_return_payments, return_photos)
-- ============================================================

-- Track how much of each line has been returned (cached counter; the
-- authoritative guard sums sale_return_items, this is for fast reads)
ALTER TABLE sale_items
    ADD COLUMN returned_qty INT NOT NULL DEFAULT 0 AFTER qty;

-- Partial returns need a distinct status from full ones
ALTER TABLE sales
    MODIFY status ENUM('completed','held','cancelled','partially_returned','returned')
        DEFAULT 'completed';

-- Reverse link for Phase 15 exchanges (new sale created by an exchange)
ALTER TABLE sales
    ADD COLUMN exchange_return_id INT DEFAULT NULL AFTER branch_id;

-- Backfill returned_qty from legacy negative sale_items rows written by the
-- Phase 07 Sale::processReturn(). Harmless on a clean install (no negative
-- rows exist there), but must only ever run once. The double-nested derived
-- table is required: MySQL 8 refuses "target table in FROM clause" when the
-- SET subquery reads the same table directly (ERROR 1093).
UPDATE sale_items si
SET si.returned_qty = (
    SELECT COALESCE(SUM(t.qty), 0)
    FROM (
        SELECT ABS(si2.qty) AS qty
        FROM sale_items si2
        WHERE si2.product_id = si.product_id
          AND si2.qty < 0
          AND si2.id > (SELECT MAX(si3.id) FROM sale_items si3
                        WHERE si3.sale_id = si.sale_id AND si3.qty > 0)
    ) AS t
)
WHERE si.qty > 0;

-- ============================================================
-- sale_returns
-- ============================================================
CREATE TABLE IF NOT EXISTS sale_returns (
    id INT AUTO_INCREMENT PRIMARY KEY,
    return_no VARCHAR(50) NOT NULL UNIQUE,
    type ENUM('pos','online') NOT NULL DEFAULT 'pos',

    -- Exactly one of these is set. type decides which.
    sale_id INT DEFAULT NULL,
    order_id INT DEFAULT NULL,

    customer_id INT DEFAULT NULL,
    branch_id INT NOT NULL,

    status ENUM('requested','approved','rejected','received','refunded','cancelled')
        NOT NULL DEFAULT 'approved',

    reason_code VARCHAR(50) DEFAULT NULL,
    reason TEXT,

    -- 'original' resolves to the tender the sale was actually paid with;
    -- 'customer_balance' is a store-credit refund back to the loyalty wallet
    method ENUM('original','cash','card','mobile','customer_balance') DEFAULT 'original',

    -- Money breakdown. subtotal is the returned line value; the other
    -- three are the proportional shares reversed from the original sale.
    subtotal     DECIMAL(10,2) DEFAULT 0,
    discount     DECIMAL(10,2) DEFAULT 0,
    tax          DECIMAL(10,2) DEFAULT 0,
    shipping     DECIMAL(10,2) DEFAULT 0,
    refund_total DECIMAL(10,2) DEFAULT 0,

    -- Loyalty points clawed back (Sale::create awards floor(grand_total/10))
    loyalty_reversed INT NOT NULL DEFAULT 0,

    -- Populated by Phase 15 when this return is exchanged for a new sale
    exchange_sale_id INT DEFAULT NULL,

    -- Audit trail
    requested_by INT DEFAULT NULL,          -- staff who opened it (POS)
    requested_for INT DEFAULT NULL,          -- customer user (online)
    approved_by  INT DEFAULT NULL,
    approved_at  TIMESTAMP NULL DEFAULT NULL,
    received_by  INT DEFAULT NULL,
    received_at  TIMESTAMP NULL DEFAULT NULL,
    refunded_by  INT DEFAULT NULL,
    refunded_at  TIMESTAMP NULL DEFAULT NULL,
    admin_note TEXT,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (sale_id) REFERENCES sales(id),
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE SET NULL,
    FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE SET NULL,
    FOREIGN KEY (branch_id) REFERENCES branches(id),
    FOREIGN KEY (exchange_sale_id) REFERENCES sales(id) ON DELETE SET NULL,

    KEY idx_return_sale (sale_id),
    KEY idx_return_order (order_id),
    KEY idx_return_status (status),
    KEY idx_return_branch (branch_id),
    KEY idx_return_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- sale_return_items
-- qty is ALWAYS positive. The existence of the row IS the return;
-- no sign is needed, which removes the Phase 07 ambiguity between
-- "negative sale line" and "returned line".
-- ============================================================
CREATE TABLE IF NOT EXISTS sale_return_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    return_id INT NOT NULL,

    -- Which line is being returned. POS returns carry sale_item_id,
    -- online returns carry order_item_id. The other stays NULL.
    sale_item_id  INT DEFAULT NULL,
    order_item_id INT DEFAULT NULL,

    product_id INT NOT NULL,
    qty INT NOT NULL,
    unit_price DECIMAL(10,2) NOT NULL DEFAULT 0,
    discount    DECIMAL(10,2) DEFAULT 0,
    total       DECIMAL(10,2) NOT NULL DEFAULT 0,

    -- Per-line restock decision. 1 = resellable, goes back into
    -- inventory. 0 = damaged/quarantined, does NOT touch inventory.
    restock TINYINT(1) NOT NULL DEFAULT 1,
    condition_note VARCHAR(100) DEFAULT NULL,

    FOREIGN KEY (return_id) REFERENCES sale_returns(id) ON DELETE CASCADE,
    FOREIGN KEY (sale_item_id) REFERENCES sale_items(id),
    FOREIGN KEY (order_item_id) REFERENCES order_items(id),
    FOREIGN KEY (product_id) REFERENCES products(id),

    KEY idx_ri_return (return_id),
    KEY idx_ri_sale_item (sale_item_id),
    KEY idx_ri_product (product_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- sale_return_payments
-- The ledger Phase 07 never had. method records the tender that
-- actually left the till, not the tender originally used.
-- ============================================================
CREATE TABLE IF NOT EXISTS sale_return_payments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    return_id INT NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    method ENUM('original','cash','card','mobile','customer_balance') NOT NULL DEFAULT 'original',
    reference VARCHAR(100) DEFAULT NULL,
    notes TEXT,
    processed_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (return_id) REFERENCES sale_returns(id) ON DELETE CASCADE,
    FOREIGN KEY (processed_by) REFERENCES users(id),

    KEY idx_rp_return (return_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- return_photos
-- Polymorphic on purpose: Phase 14 attaches the same photo evidence
-- to supplier returns, avoiding a second identical table.
-- ============================================================
CREATE TABLE IF NOT EXISTS return_photos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    return_kind ENUM('sale','purchase') NOT NULL,
    return_id INT NOT NULL,
    return_item_id INT DEFAULT NULL,   -- NULL = request-level photo
    path VARCHAR(255) NOT NULL,
    uploaded_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (uploaded_by) REFERENCES users(id) ON DELETE SET NULL,

    KEY idx_photo_return (return_kind, return_id),
    KEY idx_photo_item (return_item_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- Returns policy settings (Phase 13)
-- ============================================================
INSERT IGNORE INTO settings (setting_key, setting_value, setting_group) VALUES
('return_window_days', '14', 'returns'),
('return_reasons', 'damaged,wrong_item,not_as_described,defective,changed_mind,other', 'returns'),
('return_require_approval', '1', 'returns'),
('return_auto_restock', '1', 'returns'),
('return_allow_cashier_refund', '1', 'returns'),
('return_tender_lock', '1', 'returns');