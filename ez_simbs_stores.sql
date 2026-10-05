-- EZ_SIMBS Store Registration & Role Dashboards Migration
-- Adds: stores table, store_id columns on users & branches

USE ez_simbs;

-- Stores table
CREATE TABLE IF NOT EXISTS stores (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    owner_name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    phone VARCHAR(20) DEFAULT '',
    address TEXT,
    currency VARCHAR(10) DEFAULT 'USD',
    currency_symbol VARCHAR(5) DEFAULT '$',
    plan ENUM('free','pro') DEFAULT 'free',
    status ENUM('pending','active','suspended') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Add store_id to branches (nullable so legacy data works)
ALTER TABLE branches ADD COLUMN store_id INT DEFAULT NULL AFTER id;
ALTER TABLE branches ADD KEY idx_branch_store (store_id);
ALTER TABLE branches ADD CONSTRAINT fk_branch_store FOREIGN KEY (store_id) REFERENCES stores(id) ON DELETE SET NULL;

-- Add store_id to users (nullable)
ALTER TABLE users ADD COLUMN store_id INT DEFAULT NULL AFTER branch_id;
ALTER TABLE users ADD KEY idx_user_store (store_id);
ALTER TABLE users ADD CONSTRAINT fk_user_store FOREIGN KEY (store_id) REFERENCES stores(id) ON DELETE SET NULL;

-- Seed demo store for existing data
INSERT INTO stores (name, owner_name, email, phone, address, plan, status) VALUES
('EZ SIMBS Demo Store', 'Administrator', 'admin@ezsimbs.local', '+1234567890', '123 Business St, City', 'free', 'active');

-- Link existing demo data to the demo store
UPDATE branches SET store_id = 1 WHERE store_id IS NULL;
UPDATE users SET store_id = 1 WHERE store_id IS NULL;
