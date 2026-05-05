-- ============================================================
-- Csquare Fintech ERP System - Database Schema
-- ============================================================

CREATE DATABASE IF NOT EXISTS csquare_erp CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE csquare_erp;

-- ------------------------------------------------------------
-- Districts lookup table
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS districts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL
);

INSERT INTO districts (name) VALUES
('Ampara'), ('Anuradhapura'), ('Badulla'), ('Batticaloa'),
('Colombo'), ('Galle'), ('Gampaha'), ('Hambantota'),
('Jaffna'), ('Kalutara'), ('Kandy'), ('Kegalle'),
('Kilinochchi'), ('Kurunegala'), ('Mannar'), ('Matale'),
('Matara'), ('Monaragala'), ('Mullaitivu'), ('Nuwara Eliya'),
('Polonnaruwa'), ('Puttalam'), ('Ratnapura'), ('Trincomalee'),
('Vavuniya');

-- ------------------------------------------------------------
-- Item Categories
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS item_categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL
);

INSERT INTO item_categories (name) VALUES
('Electronics'), ('Clothing'), ('Food & Beverages'), ('Furniture'),
('Stationery'), ('Hardware'), ('Software'), ('Other');

-- ------------------------------------------------------------
-- Item Sub-Categories
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS item_subcategories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    FOREIGN KEY (category_id) REFERENCES item_categories(id)
);

INSERT INTO item_subcategories (category_id, name) VALUES
(1, 'Mobile Phones'), (1, 'Laptops'), (1, 'Accessories'), (1, 'Televisions'),
(2, 'Men'), (2, 'Women'), (2, 'Kids'), (2, 'Footwear'),
(3, 'Beverages'), (3, 'Snacks'), (3, 'Fresh Produce'), (3, 'Dairy'),
(4, 'Office Furniture'), (4, 'Home Furniture'), (4, 'Outdoor'),
(5, 'Pens & Pencils'), (5, 'Paper'), (5, 'Files & Folders'),
(6, 'Tools'), (6, 'Electrical'), (6, 'Plumbing'),
(7, 'Licenses'), (7, 'Subscriptions'),
(8, 'Miscellaneous');

-- ------------------------------------------------------------
-- Customers
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS customers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title ENUM('Mr','Mrs','Miss','Dr') NOT NULL,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    contact_number VARCHAR(15) NOT NULL,
    district_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (district_id) REFERENCES districts(id)
);

-- ------------------------------------------------------------
-- Items
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    item_code VARCHAR(50) NOT NULL UNIQUE,
    item_name VARCHAR(150) NOT NULL,
    category_id INT NOT NULL,
    subcategory_id INT NOT NULL,
    quantity INT NOT NULL DEFAULT 0,
    unit_price DECIMAL(12,2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES item_categories(id),
    FOREIGN KEY (subcategory_id) REFERENCES item_subcategories(id)
);

-- ------------------------------------------------------------
-- Invoices
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS invoices (
    id INT AUTO_INCREMENT PRIMARY KEY,
    invoice_number VARCHAR(20) NOT NULL UNIQUE,
    customer_id INT NOT NULL,
    invoice_date DATE NOT NULL,
    total_amount DECIMAL(12,2) NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (customer_id) REFERENCES customers(id)
);

-- ------------------------------------------------------------
-- Invoice Items
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS invoice_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    invoice_id INT NOT NULL,
    item_id INT NOT NULL,
    quantity INT NOT NULL,
    unit_price DECIMAL(12,2) NOT NULL,
    FOREIGN KEY (invoice_id) REFERENCES invoices(id),
    FOREIGN KEY (item_id) REFERENCES items(id)
);

-- ------------------------------------------------------------
-- Sample Data
-- ------------------------------------------------------------
INSERT INTO customers (title, first_name, last_name, contact_number, district_id) VALUES
('Mr', 'Kasun', 'Perera', '0771234567', 5),
('Mrs', 'Nimali', 'Silva', '0712345678', 7),
('Miss', 'Sanduni', 'Fernando', '0723456789', 11),
('Dr', 'Rajeev', 'Jayawardena', '0761234567', 5),
('Mr', 'Nuwan', 'Bandara', '0751234567', 13);

INSERT INTO items (item_code, item_name, category_id, subcategory_id, quantity, unit_price) VALUES
('ITM-001', 'Samsung Galaxy A54', 1, 1, 50, 85000.00),
('ITM-002', 'HP Pavilion Laptop', 1, 2, 20, 185000.00),
('ITM-003', 'USB-C Cable 2m', 1, 3, 200, 1500.00),
('ITM-004', 'Office Chair', 4, 13, 30, 12500.00),
('ITM-005', 'A4 Paper Ream', 5, 16, 500, 1200.00);

INSERT INTO invoices (invoice_number, customer_id, invoice_date, total_amount) VALUES
('INV-2026-001', 1, '2026-04-10', 87500.00),
('INV-2026-002', 2, '2026-04-15', 186200.00),
('INV-2026-003', 3, '2026-04-20', 25000.00),
('INV-2026-004', 4, '2026-05-01', 3600.00),
('INV-2026-005', 1, '2026-05-03', 370000.00);

INSERT INTO invoice_items (invoice_id, item_id, quantity, unit_price) VALUES
(1, 1, 1, 85000.00), (1, 5, 2, 1200.00),
(2, 2, 1, 185000.00), (2, 3, 1, 1500.00),
(3, 4, 2, 12500.00),
(4, 5, 3, 1200.00),
(5, 2, 2, 185000.00);
