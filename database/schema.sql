-- ============================================
-- Stock Management System - Database Schema
-- ============================================
-- Description: Complete database schema for inventory management
-- Version: 1.0
-- Date: 2026-01-07
-- ============================================



-- ============================================
-- Table: users
-- Description: User accounts with role-based access
-- ============================================
CREATE TABLE IF NOT EXISTS users (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    role ENUM('admin', 'staff', 'viewer') NOT NULL DEFAULT 'viewer',
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_username (username),
    INDEX idx_email (email),
    INDEX idx_role (role)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Table: products
-- Description: Product catalog with pricing and stock info
-- ============================================
CREATE TABLE IF NOT EXISTS products (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    sku VARCHAR(50) NOT NULL UNIQUE,
    name VARCHAR(200) NOT NULL,
    description TEXT,
    purchase_price DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
    selling_price DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
    quantity INT NOT NULL DEFAULT 0,
    minimum_quantity INT NOT NULL DEFAULT 10,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    supplier_id INT UNSIGNED,
    created_by INT UNSIGNED,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_sku (sku),
    INDEX idx_name (name),
    INDEX idx_quantity (quantity),
    INDEX idx_is_active (is_active),
    INDEX idx_supplier_id (supplier_id),
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (supplier_id) REFERENCES suppliers(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Table: suppliers
-- Description: Supplier information for products
-- ============================================
CREATE TABLE IF NOT EXISTS suppliers (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    contact_name VARCHAR(100),
    email VARCHAR(100),
    phone VARCHAR(20),
    address TEXT,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_name (name),
    INDEX idx_is_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Table: stock_movements
-- Description: Track all stock IN/OUT transactions
-- ============================================
CREATE TABLE IF NOT EXISTS stock_movements (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    product_id INT UNSIGNED NOT NULL,
    movement_type ENUM('IN', 'OUT') NOT NULL,
    quantity INT NOT NULL,
    note TEXT,
    created_by INT UNSIGNED,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_product_id (product_id),
    INDEX idx_movement_type (movement_type),
    INDEX idx_created_at (created_at),
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Insert Default Admin User
-- ============================================
-- Username: admin
-- Password: admin123 (CHANGE THIS AFTER DEPLOYMENT!)
-- Password hash generated using: password_hash('admin123', PASSWORD_DEFAULT)
INSERT INTO users (username, email, password, full_name, role, is_active) VALUES
('admin', 'admin@stocksystem.local', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'System Administrator', 'admin', 1);

-- ============================================
-- Insert Sample Users for Testing
-- ============================================
-- Staff User - Username: staff, Password: staff123
INSERT INTO users (username, email, password, full_name, role, is_active) VALUES
('staff', 'staff@stocksystem.local', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Staff Member', 'staff', 1);

-- Viewer User - Username: viewer, Password: viewer123
INSERT INTO users (username, email, password, full_name, role, is_active) VALUES
('viewer', 'viewer@stocksystem.local', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Viewer User', 'viewer', 1);

-- ============================================
-- Insert Sample Products for Testing
-- ============================================
INSERT INTO products (sku, name, description, purchase_price, selling_price, quantity, minimum_quantity, created_by) VALUES
('SKU-001', 'Laptop Dell XPS 15', 'High-performance laptop for business use', 1200.00, 1500.00, 15, 5, 1),
('SKU-002', 'Wireless Mouse Logitech', 'Ergonomic wireless mouse', 25.00, 35.00, 50, 20, 1),
('SKU-003', 'USB-C Cable 2m', 'High-speed USB-C charging cable', 8.00, 15.00, 100, 30, 1),
('SKU-004', 'Monitor Samsung 27"', '4K UHD monitor with HDR support', 350.00, 450.00, 8, 10, 1),
('SKU-005', 'Keyboard Mechanical RGB', 'Gaming mechanical keyboard with RGB lighting', 80.00, 120.00, 3, 10, 1);

-- ============================================
-- Insert Sample Suppliers
-- ============================================
INSERT INTO suppliers (name, contact_name, email, phone, address) VALUES
('TechWorld Distribution', 'John Smith', 'john@techworld.local', '555-0101', '123 Tech Lane, Silicon Valley'),
('Global Peripherals Inc.', 'Sarah Jones', 'sarah@global.local', '555-0102', '456 Component Way, Austin'),
('Office Max Solutions', 'Mike Brown', 'mike@officemax.local', '555-0103', '789 Supply St, Chicago');

-- Update sample products with supplier IDs
UPDATE products SET supplier_id = 1 WHERE id IN (1, 4);
UPDATE products SET supplier_id = 2 WHERE id IN (2, 5);
UPDATE products SET supplier_id = 3 WHERE id = 3;

-- ============================================
-- Insert Sample Stock Movements
-- ============================================
INSERT INTO stock_movements (product_id, movement_type, quantity, note, created_by) VALUES
(1, 'IN', 20, 'Initial stock purchase', 1),
(1, 'OUT', 5, 'Sold to corporate client', 1),
(2, 'IN', 100, 'Bulk purchase from supplier', 1),
(2, 'OUT', 50, 'Retail sales', 1),
(3, 'IN', 150, 'Restocking cables', 1),
(3, 'OUT', 50, 'Sold with laptops', 1),
(4, 'IN', 10, 'New monitor shipment', 1),
(4, 'OUT', 2, 'Office setup', 1),
(5, 'IN', 15, 'Gaming peripherals stock', 1),
(5, 'OUT', 12, 'Gaming event sales', 1);

-- ============================================
-- Verification Queries
-- ============================================
-- Uncomment to verify installation:
-- SELECT * FROM users;
-- SELECT * FROM products;
-- SELECT * FROM stock_movements;
-- SELECT p.*, (SELECT COUNT(*) FROM stock_movements WHERE product_id = p.id) as movement_count FROM products p;
