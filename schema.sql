-- Create database
CREATE DATABASE IF NOT EXISTS web_store_app;
USE web_store_app;

-- Create users table
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'user') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Create barang table (for inventory)
CREATE TABLE barang (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_barang VARCHAR(100) NOT NULL,
    deskripsi TEXT,
    harga DECIMAL(10, 2) NOT NULL,
    stok INT NOT NULL DEFAULT 0,
    kategori_id INT NULL,
    supplier_id INT NULL,
    toko_id INT NULL DEFAULT 1,
    gambar VARCHAR(255) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (kategori_id) REFERENCES kategori(id) ON DELETE SET NULL,
    FOREIGN KEY (supplier_id) REFERENCES supplier(id) ON DELETE SET NULL,
    FOREIGN KEY (toko_id) REFERENCES toko(id) ON DELETE SET NULL
);

-- Create kategori table
CREATE TABLE kategori (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_kategori VARCHAR(50) NOT NULL,
    deskripsi TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Create supplier table
CREATE TABLE supplier (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_supplier VARCHAR(100) NOT NULL,
    alamat TEXT,
    telepon VARCHAR(20),
    email VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Create toko table
CREATE TABLE toko (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_toko VARCHAR(100) NOT NULL,
    alamat TEXT,
    telepon VARCHAR(20),
    email VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Create sessions table (for session management)
CREATE TABLE sessions (
    id VARCHAR(128) PRIMARY KEY,
    user_id INT NOT NULL,
    ip_address VARCHAR(45),
    user_agent TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    expires_at TIMESTAMP NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Create activities table (for logging user activities)
CREATE TABLE activities (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    action VARCHAR(255) NOT NULL,
    description TEXT,
    ip_address VARCHAR(45),
    user_agent TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Insert default admin user (username: admin, password: admin123)
-- Using generated bcrypt hash: $2y$10$R9jgr6HJKJ2PbuWOaqRwie2k1aXCYh.4PeVvxS6Ka4zQru7nfXP8i
INSERT INTO users (username, email, password, role) VALUES
('admin', 'admin@merchshipe.com', '$2y$10$R9jgr6HJKJ2PbuWOaqRwie2k1aXCYh.4PeVvxS6Ka4zQru7nfXP8i', 'admin');

-- Insert sample user (username: user, password: user123)
-- Using generated bcrypt hash: $2y$10$j0IZOmBs./Az42LgzZgPYur2yF7r0RAOHFQVwWlzUwENsQr5OoSqy
INSERT INTO users (username, email, password, role) VALUES
('user', 'user@merchshipe.com', '$2y$10$j0IZOmBs./Az42LgzZgPYur2yF7r0RAOHFQVwWlzUwENsQr5OoSqy', 'user');

-- Insert sample categories
INSERT INTO kategori (nama_kategori, deskripsi) VALUES
('Electronics', 'Electronic devices and accessories'),
('Furniture', 'Furniture and home decor'),
('Clothing', 'Clothing and accessories'),
('Food & Beverage', 'Food and beverage products'),
('Books', 'Books and educational materials');

-- Insert sample suppliers
INSERT INTO supplier (nama_supplier, alamat, telepon, email) VALUES
('Tech Solutions Inc.', '123 Tech Street, Jakarta', '02112345678', 'contact@techsolutions.com'),
('Furniture World Co.', '456 Home Ave, Surabaya', '03187654321', 'info@furnitureworld.com');

-- Insert sample stores
INSERT INTO toko (nama_toko, alamat, telepon, email) VALUES
('MerchShipe Central', '789 Main Street, Jakarta', '02198765432', 'central@merchshipe.com');

-- Insert sample products
INSERT INTO barang (nama_barang, deskripsi, harga, stok, kategori_id, supplier_id, toko_id) VALUES
('Gaming Laptop', 'High-performance laptop for gaming and design', 15000000.00, 10, 1, 1, 1),
('Wireless Mouse', 'Wireless mouse for comfortable work', 250000.00, 25, 1, 1, 1),
('Office Chair', 'Ergonomic chair for comfortable work', 1200000.00, 5, 2, 2, 1),
('24" LED Monitor', '24 inch Full HD LED monitor', 1800000.00, 8, 1, 1, 1),
('Mechanical Keyboard', 'RGB mechanical keyboard with blue switches', 750000.00, 15, 1, 1, 1);