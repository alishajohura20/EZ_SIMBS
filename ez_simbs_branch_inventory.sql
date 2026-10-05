-- ============================================================
-- Multi-Branch Inventory: aggregate stock columns on products
-- ------------------------------------------------------------
-- Tracks total stock across ALL branches plus how many branches
-- carry each product. Kept in sync by Product::syncTotals() on
-- every stock mutation (stock in/out/adjust/damage/return/transfer).
-- ============================================================

ALTER TABLE products
    ADD COLUMN total_stock INT NOT NULL DEFAULT 0 AFTER reorder_level,
    ADD COLUMN branch_count INT NOT NULL DEFAULT 0 AFTER total_stock;

-- Backfill from existing per-branch inventory
UPDATE products p
SET total_stock = (SELECT IFNULL(SUM(i.qty), 0) FROM inventory i WHERE i.product_id = p.id),
    branch_count = (SELECT COUNT(*) FROM inventory i WHERE i.product_id = p.id);