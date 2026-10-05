-- ============================================================
-- EZ_SIMBS - Phase 14: Purchase Returns (Supplier-Side)
-- For UPGRADING an existing pre-Phase-14 database.
-- Fresh installs get everything from ez_simbs.sql (which now
-- contains these tables).
-- Tables: 37 -> 40 (adds purchase_returns, purchase_return_items,
--                  purchase_return_payments) + 2 columns + 1 enum
-- widening + 3 policy settings. Everything is additive.
-- ============================================================

-- How much of each PO line has already been returned and shipped back
-- (cached counter; the authoritative guard sums purchase_return_items)
ALTER TABLE purchase_items
    ADD COLUMN returned_qty INT NOT NULL DEFAULT 0 AFTER received_qty;

-- Money the supplier owes us (credit notes that exceeded a PO's due,
-- plus any returns against an already-settled PO).
ALTER TABLE suppliers
    ADD COLUMN balance DECIMAL(10,2) NOT NULL DEFAULT 0 AFTER rating;

-- New outbound movement. 'purchase_return' is deliberately NOT in
-- Product::updateStock()'s inbound list ('in','return'), so the
-- quantity SUBTRACTS with no code change there. A void reverses via
-- Inventory::stockIn(..., 'purchase_return_void') which is a regular
-- 'in' row carrying reference_type = 'purchase_return_void'.
ALTER TABLE stock_logs
    MODIFY type ENUM('in','out','adjustment','damage','return','transfer','purchase_return')
        NOT NULL;

-- ============================================================
-- purchase_returns
-- A supplier return is a document referencing the received PO it
-- reverses. Status lifecycle:
--   requested -> approved -> received -> credited
--   requested -> rejected
--   approved / received / credited -> cancelled (void)
-- 'received' means the goods were physically shipped back to the
-- supplier (stock left inventory). 'credited' means the credit was
-- settled against the PO's due and/or the supplier's balance.
-- ============================================================
CREATE TABLE IF NOT EXISTS purchase_returns (
    id INT AUTO_INCREMENT PRIMARY KEY,
    return_no VARCHAR(50) NOT NULL UNIQUE,

    purchase_id INT NOT NULL,
    supplier_id INT NOT NULL,
    branch_id INT NOT NULL,

    status ENUM('requested','approved','rejected','received','credited','cancelled')
        NOT NULL DEFAULT 'approved',

    reason_code VARCHAR(50) DEFAULT NULL,
    reason TEXT,

    -- How the credit is settled. 'credit_note' is the default: it first
    -- reduces purchases.due, and only overflows into suppliers.balance.
    method ENUM('credit_note','bank_transfer','cash') NOT NULL DEFAULT 'credit_note',

    -- Money breakdown. subtotal = returned line value; tax is the
    -- proportional share reversed from the purchase; credit_total is the
    -- credit the supplier receives for this return (subtotal + tax).
    subtotal     DECIMAL(10,2) DEFAULT 0,
    tax          DECIMAL(10,2) DEFAULT 0,
    credit_total DECIMAL(10,2) DEFAULT 0,

    -- How the credit was applied at settlement time (for audit + void)
    applied_to_due DECIMAL(10,2) DEFAULT 0,
    to_balance     DECIMAL(10,2) DEFAULT 0,

    -- Audit trail
    requested_by INT DEFAULT NULL,
    approved_by  INT DEFAULT NULL,
    approved_at  TIMESTAMP NULL DEFAULT NULL,
    received_by  INT DEFAULT NULL,
    received_at  TIMESTAMP NULL DEFAULT NULL,
    credited_by  INT DEFAULT NULL,
    credited_at  TIMESTAMP NULL DEFAULT NULL,
    admin_note TEXT,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (purchase_id) REFERENCES purchases(id),
    FOREIGN KEY (supplier_id) REFERENCES suppliers(id),
    FOREIGN KEY (branch_id) REFERENCES branches(id),

    KEY idx_pr_purchase (purchase_id),
    KEY idx_pr_supplier (supplier_id),
    KEY idx_pr_status (status),
    KEY idx_pr_branch (branch_id),
    KEY idx_pr_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- purchase_return_items
-- qty is ALWAYS positive; the existence of the row IS the return.
-- Prices always come from the source purchase_items row, never from
-- the browser. All lines are by definition going back to the supplier
-- (unlike sale returns there is no restock decision).
-- ============================================================
CREATE TABLE IF NOT EXISTS purchase_return_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    return_id INT NOT NULL,
    purchase_item_id INT NOT NULL,
    product_id INT NOT NULL,

    qty       INT NOT NULL,
    unit_cost DECIMAL(10,2) NOT NULL DEFAULT 0,
    total     DECIMAL(10,2) NOT NULL DEFAULT 0,
    condition_note VARCHAR(100) DEFAULT NULL,

    FOREIGN KEY (return_id) REFERENCES purchase_returns(id) ON DELETE CASCADE,
    FOREIGN KEY (purchase_item_id) REFERENCES purchase_items(id),
    FOREIGN KEY (product_id) REFERENCES products(id),

    KEY idx_pri_return (return_id),
    KEY idx_pri_purchase_item (purchase_item_id),
    KEY idx_pri_product (product_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- purchase_return_payments
-- The credit ledger. A 'credit_note' row records the amount that
-- settled the PO / overflowed to balance; a 'bank_transfer' / 'cash'
-- row records actual money the supplier paid back.
-- ============================================================
CREATE TABLE IF NOT EXISTS purchase_return_payments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    return_id INT NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    method ENUM('credit_note','bank_transfer','cash') NOT NULL DEFAULT 'credit_note',
    reference VARCHAR(100) DEFAULT NULL,
    notes TEXT,
    added_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (return_id) REFERENCES purchase_returns(id) ON DELETE CASCADE,
    FOREIGN KEY (added_by) REFERENCES users(id),

    KEY idx_prp_return (return_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- Purchase-return policy settings (Phase 14)
-- ============================================================
INSERT IGNORE INTO settings (setting_key, setting_value, setting_group) VALUES
('purchase_return_require_approval', '1', 'returns'),
('supplier_return_reasons', 'defective,damaged,wrong_stock,overstock,expired,other', 'returns'),
('purchase_return_credit_note', '1', 'returns');