-- EZ_SIMBS Database Schema
-- Smart Inventory Management & Billing System

CREATE DATABASE IF NOT EXISTS ez_simbs CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE ez_simbs;

-- Stores (each tenant is a store)
CREATE TABLE stores (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Roles & Permissions
CREATE TABLE roles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE permissions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    role_id INT NOT NULL,
    resource VARCHAR(100) NOT NULL,
    action VARCHAR(50) NOT NULL,
    FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE,
    UNIQUE KEY unique_permission (role_id, resource, action)
);

-- Branches (created without manager_id FK to avoid circular dependency with users)
CREATE TABLE branches (
    id INT AUTO_INCREMENT PRIMARY KEY,
    store_id INT DEFAULT NULL,
    name VARCHAR(100) NOT NULL,
    address TEXT,
    phone VARCHAR(20),
    manager_id INT DEFAULT NULL,
    status ENUM('active','inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    KEY idx_branch_store (store_id),
    CONSTRAINT fk_branch_store FOREIGN KEY (store_id) REFERENCES stores(id) ON DELETE SET NULL
);

-- Users
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role_id INT NOT NULL,
    store_id INT DEFAULT NULL,
    branch_id INT DEFAULT NULL,
    phone VARCHAR(20),
    avatar VARCHAR(255),
    status ENUM('active','inactive','locked') DEFAULT 'active',
    last_login DATETIME DEFAULT NULL,
    failed_attempts INT DEFAULT 0,
    lockout_until DATETIME DEFAULT NULL,
    remember_token VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (role_id) REFERENCES roles(id),
    FOREIGN KEY (store_id) REFERENCES stores(id) ON DELETE SET NULL,
    FOREIGN KEY (branch_id) REFERENCES branches(id) ON DELETE SET NULL,
    KEY idx_user_store (store_id)
);

-- Now that users exists, add the FK for branches.manager_id
ALTER TABLE branches
    ADD FOREIGN KEY (manager_id) REFERENCES users(id) ON DELETE SET NULL;

-- Active login sessions (supports multiple concurrent sessions per user)
CREATE TABLE user_sessions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    token VARCHAR(128) NOT NULL UNIQUE,
    remember_token VARCHAR(128) DEFAULT NULL UNIQUE,
    ip_address VARCHAR(45) DEFAULT '',
    user_agent VARCHAR(255) DEFAULT '',
    device VARCHAR(100) DEFAULT '',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    last_activity DATETIME DEFAULT NULL,
    expires_at DATETIME DEFAULT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Catalog Tables
CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    parent_id INT DEFAULT NULL,
    status ENUM('active','inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (parent_id) REFERENCES categories(id) ON DELETE SET NULL
);

CREATE TABLE brands (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    logo VARCHAR(255),
    status ENUM('active','inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE units (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL,
    symbol VARCHAR(10) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sku VARCHAR(50) NOT NULL UNIQUE,
    name VARCHAR(200) NOT NULL,
    category_id INT,
    brand_id INT,
    unit_id INT,
    image VARCHAR(255),
    barcode VARCHAR(100),
    qr_code VARCHAR(255),
    description TEXT,
    price DECIMAL(10,2) NOT NULL DEFAULT 0,
    cost DECIMAL(10,2) NOT NULL DEFAULT 0,
    min_stock INT DEFAULT 0,
    reorder_level INT DEFAULT 0,
    total_stock INT NOT NULL DEFAULT 0,
    branch_count INT NOT NULL DEFAULT 0,
    status ENUM('active','inactive','discontinued') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL,
    FOREIGN KEY (brand_id) REFERENCES brands(id) ON DELETE SET NULL,
    FOREIGN KEY (unit_id) REFERENCES units(id) ON DELETE SET NULL
);

-- Partner Tables
CREATE TABLE suppliers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    company_name VARCHAR(200) NOT NULL,
    contact_person VARCHAR(100),
    email VARCHAR(150),
    phone VARCHAR(20),
    address TEXT,
    tax_id VARCHAR(50),
    rating DECIMAL(3,2) DEFAULT 0,
    balance DECIMAL(10,2) NOT NULL DEFAULT 0,
    status ENUM('active','inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE customers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    phone VARCHAR(20),
    email VARCHAR(150),
    address TEXT,
    membership_id VARCHAR(50) UNIQUE,
    loyalty_points INT DEFAULT 0,
    balance DECIMAL(10,2) DEFAULT 0,
    is_vip BOOLEAN DEFAULT FALSE,
    notes TEXT,
    status ENUM('active','inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Transaction Tables
CREATE TABLE purchases (
    id INT AUTO_INCREMENT PRIMARY KEY,
    supplier_id INT NOT NULL,
    po_number VARCHAR(50) NOT NULL UNIQUE,
    status ENUM('draft','pending','approved','received','cancelled') DEFAULT 'draft',
    subtotal DECIMAL(10,2) DEFAULT 0,
    tax DECIMAL(10,2) DEFAULT 0,
    total DECIMAL(10,2) DEFAULT 0,
    paid DECIMAL(10,2) DEFAULT 0,
    due DECIMAL(10,2) DEFAULT 0,
    payment_status ENUM('paid','pending','partial','due') DEFAULT 'pending',
    notes TEXT,
    invoice_file VARCHAR(255),
    created_by INT,
    approved_by INT,
    branch_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (supplier_id) REFERENCES suppliers(id),
    FOREIGN KEY (created_by) REFERENCES users(id),
    FOREIGN KEY (branch_id) REFERENCES branches(id)
);

CREATE TABLE purchase_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    purchase_id INT NOT NULL,
    product_id INT NOT NULL,
    qty INT NOT NULL DEFAULT 0,
    received_qty INT DEFAULT 0,
    returned_qty INT NOT NULL DEFAULT 0,
    unit_cost DECIMAL(10,2) NOT NULL DEFAULT 0,
    total DECIMAL(10,2) DEFAULT 0,
    FOREIGN KEY (purchase_id) REFERENCES purchases(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id)
);

CREATE TABLE sales (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_id INT DEFAULT NULL,
    invoice_no VARCHAR(50) NOT NULL UNIQUE,
    subtotal DECIMAL(10,2) DEFAULT 0,
    discount DECIMAL(10,2) DEFAULT 0,
    tax DECIMAL(10,2) DEFAULT 0,
    shipping DECIMAL(10,2) DEFAULT 0,
    grand_total DECIMAL(10,2) DEFAULT 0,
    payment_method ENUM('cash','card','mobile','mixed') DEFAULT 'cash',
    status ENUM('completed','held','cancelled','partially_returned','returned') DEFAULT 'completed',
    notes TEXT,
    created_by INT,
    branch_id INT,
    exchange_return_id INT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE SET NULL,
    FOREIGN KEY (created_by) REFERENCES users(id),
    FOREIGN KEY (branch_id) REFERENCES branches(id)
);

CREATE TABLE sale_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sale_id INT NOT NULL,
    product_id INT NOT NULL,
    qty INT NOT NULL DEFAULT 0,
    returned_qty INT NOT NULL DEFAULT 0,
    unit_price DECIMAL(10,2) NOT NULL DEFAULT 0,
    discount DECIMAL(10,2) DEFAULT 0,
    total DECIMAL(10,2) DEFAULT 0,
    FOREIGN KEY (sale_id) REFERENCES sales(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id)
);

-- Inventory Tables
CREATE TABLE inventory (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    branch_id INT NOT NULL,
    qty INT DEFAULT 0,
    min_stock INT DEFAULT 0,
    reorder_level INT DEFAULT 0,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY unique_product_branch (product_id, branch_id),
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    FOREIGN KEY (branch_id) REFERENCES branches(id) ON DELETE CASCADE
);

CREATE TABLE stock_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    branch_id INT NOT NULL,
    type ENUM('in','out','adjustment','damage','return','transfer','purchase_return') NOT NULL,
    qty INT NOT NULL,
    reference_id INT DEFAULT NULL,
    reference_type VARCHAR(50) DEFAULT NULL,
    notes TEXT,
    user_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id),
    FOREIGN KEY (branch_id) REFERENCES branches(id),
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Support Tables
CREATE TABLE invoices (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sale_id INT NOT NULL,
    pdf_path VARCHAR(255),
    qr_code VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (sale_id) REFERENCES sales(id) ON DELETE CASCADE
);

CREATE TABLE notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT DEFAULT NULL,
    title VARCHAR(200) NOT NULL,
    message TEXT,
    type ENUM('info','warning','danger','success') DEFAULT 'info',
    is_read BOOLEAN DEFAULT FALSE,
    link VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE activity_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    action VARCHAR(100) NOT NULL,
    resource VARCHAR(100),
    resource_id INT,
    old_value TEXT,
    new_value TEXT,
    ip_address VARCHAR(45),
    browser VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

CREATE TABLE settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(100) NOT NULL UNIQUE,
    setting_value TEXT,
    setting_group VARCHAR(50) DEFAULT 'general',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE purchase_payments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    purchase_id INT NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    payment_method ENUM('cash','card','bank_transfer','cheque') DEFAULT 'cash',
    reference VARCHAR(100),
    notes TEXT,
    paid_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (purchase_id) REFERENCES purchases(id),
    FOREIGN KEY (paid_by) REFERENCES users(id)
);

CREATE TABLE sale_payments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sale_id INT NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    payment_method ENUM('cash','card','mobile','mixed') DEFAULT 'cash',
    reference VARCHAR(100),
    notes TEXT,
    received_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (sale_id) REFERENCES sales(id),
    FOREIGN KEY (received_by) REFERENCES users(id)
);

-- ============================================
-- Customer Storefront Tables (Landing / E-Commerce)
-- ============================================

-- Banners: hero sliders / promotion images on the landing page
CREATE TABLE banners (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200),
    subtitle VARCHAR(255),
    image VARCHAR(255),
    link VARCHAR(255),
    position ENUM('hero','mid','bottom') DEFAULT 'hero',
    sort_order INT DEFAULT 0,
    status ENUM('active','inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Product gallery: multiple images per product
CREATE TABLE product_gallery (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    image VARCHAR(255) NOT NULL,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

-- Wishlist: customer saved products
CREATE TABLE wishlist (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    product_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_user_product (user_id, product_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

-- Shopping cart per user
CREATE TABLE cart (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY unique_user_cart (user_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE cart_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cart_id INT NOT NULL,
    product_id INT NOT NULL,
    qty INT NOT NULL DEFAULT 1,
    added_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_cart_product (cart_id, product_id),
    FOREIGN KEY (cart_id) REFERENCES cart(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

-- Product reviews/ratings from customers
CREATE TABLE product_reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    user_id INT NOT NULL,
    rating TINYINT NOT NULL DEFAULT 5 CHECK (rating BETWEEN 1 AND 5),
    title VARCHAR(200),
    comment TEXT,
    status ENUM('pending','approved','rejected') DEFAULT 'approved',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_user_product_review (user_id, product_id),
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Coupons: discount codes usable on the storefront
CREATE TABLE coupons (
    id INT AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(50) NOT NULL UNIQUE,
    description TEXT,
    type ENUM('percent','fixed') DEFAULT 'percent',
    value DECIMAL(10,2) NOT NULL DEFAULT 0,
    min_order DECIMAL(10,2) DEFAULT 0,
    max_uses INT DEFAULT 0,
    used_count INT DEFAULT 0,
    starts_at DATETIME DEFAULT NULL,
    expires_at DATETIME DEFAULT NULL,
    status ENUM('active','inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Storefront orders placed from the customer dashboard
CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_no VARCHAR(50) NOT NULL UNIQUE,
    user_id INT NOT NULL,
    customer_id INT DEFAULT NULL,
    subtotal DECIMAL(10,2) DEFAULT 0,
    discount DECIMAL(10,2) DEFAULT 0,
    coupon_code VARCHAR(50),
    tax DECIMAL(10,2) DEFAULT 0,
    shipping DECIMAL(10,2) DEFAULT 0,
    grand_total DECIMAL(10,2) DEFAULT 0,
    payment_method ENUM('cash','card','mobile','cod') DEFAULT 'cod',
    status ENUM('pending','confirmed','processing','shipped','delivered','cancelled','returned') DEFAULT 'pending',
    shipping_address TEXT,
    contact_phone VARCHAR(20),
    notes TEXT,
    is_pos TINYINT(1) DEFAULT 0,
    sale_id INT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE SET NULL,
    FOREIGN KEY (sale_id) REFERENCES sales(id) ON DELETE SET NULL
);

CREATE TABLE order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    qty INT NOT NULL DEFAULT 1,
    unit_price DECIMAL(10,2) NOT NULL DEFAULT 0,
    total DECIMAL(10,2) NOT NULL DEFAULT 0,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id)
);

-- ============================================
-- PHASE 13: Returns Management (Customer-Side)
-- ============================================

CREATE TABLE sale_returns (
    id INT AUTO_INCREMENT PRIMARY KEY,
    return_no VARCHAR(50) NOT NULL UNIQUE,
    type ENUM('pos','online') NOT NULL DEFAULT 'pos',
    sale_id INT DEFAULT NULL,
    order_id INT DEFAULT NULL,
    customer_id INT DEFAULT NULL,
    branch_id INT NOT NULL,
    status ENUM('requested','approved','rejected','received','refunded','cancelled') NOT NULL DEFAULT 'approved',
    reason_code VARCHAR(50) DEFAULT NULL,
    reason TEXT,
    method ENUM('original','cash','card','mobile','customer_balance') DEFAULT 'original',
    subtotal     DECIMAL(10,2) DEFAULT 0,
    discount     DECIMAL(10,2) DEFAULT 0,
    tax          DECIMAL(10,2) DEFAULT 0,
    shipping     DECIMAL(10,2) DEFAULT 0,
    refund_total DECIMAL(10,2) DEFAULT 0,
    loyalty_reversed INT NOT NULL DEFAULT 0,
    exchange_sale_id INT DEFAULT NULL,
    requested_by INT DEFAULT NULL,
    requested_for INT DEFAULT NULL,
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

CREATE TABLE sale_return_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    return_id INT NOT NULL,
    sale_item_id  INT DEFAULT NULL,
    order_item_id INT DEFAULT NULL,
    product_id INT NOT NULL,
    qty INT NOT NULL,
    unit_price DECIMAL(10,2) NOT NULL DEFAULT 0,
    discount    DECIMAL(10,2) DEFAULT 0,
    total       DECIMAL(10,2) NOT NULL DEFAULT 0,
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

CREATE TABLE sale_return_payments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    return_id INT NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    method ENUM('original','cash','card','mobile','customer_balance','exchange') NOT NULL DEFAULT 'original',
    reference VARCHAR(100) DEFAULT NULL,
    notes TEXT,
    processed_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (return_id) REFERENCES sale_returns(id) ON DELETE CASCADE,
    FOREIGN KEY (processed_by) REFERENCES users(id),
    KEY idx_rp_return (return_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE return_photos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    return_kind ENUM('sale','purchase') NOT NULL,
    return_id INT NOT NULL,
    return_item_id INT DEFAULT NULL,
    path VARCHAR(255) NOT NULL,
    uploaded_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (uploaded_by) REFERENCES users(id) ON DELETE SET NULL,
    KEY idx_photo_return (return_kind, return_id),
    KEY idx_photo_item (return_item_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Phase 14: Purchase (Supplier) Returns & Credits
-- ============================================

CREATE TABLE purchase_returns (
    id INT AUTO_INCREMENT PRIMARY KEY,
    return_no VARCHAR(50) NOT NULL UNIQUE,

    purchase_id INT NOT NULL,
    supplier_id INT NOT NULL,
    branch_id INT NOT NULL,

    status ENUM('requested','approved','rejected','received','credited','cancelled')
        NOT NULL DEFAULT 'approved',

    reason_code VARCHAR(50) DEFAULT NULL,
    reason TEXT,
    method ENUM('credit_note','bank_transfer','cash') NOT NULL DEFAULT 'credit_note',

    subtotal     DECIMAL(10,2) NOT NULL DEFAULT 0,
    tax          DECIMAL(10,2) NOT NULL DEFAULT 0,
    credit_total DECIMAL(10,2) NOT NULL DEFAULT 0,

    applied_to_due DECIMAL(10,2) NOT NULL DEFAULT 0,
    to_balance     DECIMAL(10,2) NOT NULL DEFAULT 0,

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

CREATE TABLE purchase_return_items (
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

CREATE TABLE purchase_return_payments (
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

-- ============================================
-- SEED DATA
-- ============================================

-- Default roles
INSERT INTO roles (name, description) VALUES
('admin', 'Full system access'),
('manager', 'Reports, analytics, supplier & purchase management'),
('branch_manager', 'Branch-specific inventory, sales, staff'),
('cashier', 'POS, invoicing, customer lookup'),
('customer', 'View own invoices, loyalty points, profile');

-- Default permissions for admin
INSERT INTO permissions (role_id, resource, action) VALUES
(1, 'users', 'create'), (1, 'users', 'read'), (1, 'users', 'update'), (1, 'users', 'delete'),
(1, 'products', 'create'), (1, 'products', 'read'), (1, 'products', 'update'), (1, 'products', 'delete'),
(1, 'sales', 'create'), (1, 'sales', 'read'), (1, 'sales', 'update'), (1, 'sales', 'delete'),
(1, 'purchases', 'create'), (1, 'purchases', 'read'), (1, 'purchases', 'update'), (1, 'purchases', 'delete'),
(1, 'reports', 'read'), (1, 'settings', 'manage'), (1, 'branches', 'manage');

-- Default units
INSERT INTO units (name, symbol) VALUES
('Piece', 'pc'), ('Kilogram', 'kg'), ('Gram', 'g'), ('Liter', 'L'),
('Milliliter', 'mL'), ('Meter', 'm'), ('Box', 'box'), ('Pack', 'pk');

-- Default demo store (owner = Administrator)
INSERT INTO stores (name, owner_name, email, phone, address, plan, status) VALUES
('EZ SIMBS Demo Store', 'Administrator', 'admin@ezsimbs.local', '+1234567890', '123 Business St, City', 'free', 'active');

-- Default branch (must be before users since users references branches)
INSERT INTO branches (store_id, name, address, phone) VALUES
(1, 'Main Branch', '123 Business St, City', '+1234567890');

-- Default demo users (all passwords: admin123, all linked to demo store)
INSERT INTO users (name, email, password, role_id, store_id, branch_id, status) VALUES
('Administrator', 'admin@ezsimbs.local', '$2y$12$LWgIGumS2nPudEEVGOqpeejnZ4XnBSwPlDLRb7IhYQGLPxGZZ6H3K', 1, 1, 1, 'active'),
('Manager', 'manager@ezsimbs.local', '$2y$12$LWgIGumS2nPudEEVGOqpeejnZ4XnBSwPlDLRb7IhYQGLPxGZZ6H3K', 2, 1, 1, 'active'),
('Branch Manager', 'branch@ezsimbs.local', '$2y$12$LWgIGumS2nPudEEVGOqpeejnZ4XnBSwPlDLRb7IhYQGLPxGZZ6H3K', 3, 1, 1, 'active'),
('Cashier', 'cashier@ezsimbs.local', '$2y$12$LWgIGumS2nPudEEVGOqpeejnZ4XnBSwPlDLRb7IhYQGLPxGZZ6H3K', 4, 1, 1, 'active'),
('Test Customer', 'customer@ezsimbs.local', '$2y$12$LWgIGumS2nPudEEVGOqpeejnZ4XnBSwPlDLRb7IhYQGLPxGZZ6H3K', 5, 1, NULL, 'active');

-- Default settings
INSERT INTO settings (setting_key, setting_value, setting_group) VALUES
('company_name', 'EZ_SIMBS Store', 'company'),
('company_address', '123 Business St, City', 'company'),
('company_phone', '+1234567890', 'company'),
('company_email', 'info@ezsimbs.local', 'company'),
('tax_rate', '10', 'billing'),
('currency', 'USD', 'billing'),
('currency_symbol', '$', 'billing'),
('invoice_prefix', 'INV', 'billing'),
('po_prefix', 'PO', 'billing'),
('low_stock_threshold', '10', 'inventory'),
('timezone', 'UTC', 'general'),
('theme', 'light', 'appearance'),
('return_window_days', '14', 'returns'),
('return_reasons', 'damaged,wrong_item,not_as_described,defective,changed_mind,other', 'returns'),
('return_require_approval', '1', 'returns'),
('return_auto_restock', '1', 'returns'),
('return_allow_cashier_refund', '1', 'returns'),
('return_tender_lock', '1', 'returns'),
('purchase_return_require_approval', '1', 'returns'),
('supplier_return_reasons', 'defective,damaged,wrong_stock,overstock,expired,other', 'returns'),
('purchase_return_credit_note', '1', 'returns');

-- Sample storefront banners (image left empty: landing page renders a gradient block)
INSERT INTO banners (title, subtitle, link, position, sort_order, status) VALUES
('Run your store — online & offline', 'Inventory, billing, POS and a web storefront — all synced in one powerful dashboard.', NULL, 'hero', 1, 'active'),
('From counter to doorstep, covered', 'Real-time stock, instant invoicing and a web storefront your customers will love.', NULL, 'hero', 2, 'active');
