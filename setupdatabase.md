# Setup Database for MerchShipe Inventory Management System

This document contains the MySQL database schema required to run the MerchShipe inventory management application.

## Database Schema

```sql
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
-- Generate bcrypt hash using PHP's password_hash() function for 'admin123'
INSERT INTO users (username, email, password, role) VALUES
('admin', 'admin@merchshipe.com', 'YOUR_BCRYPT_HASH_FOR_admin123_HERE', 'admin');

-- Insert sample user (username: user, password: user123)
-- Generate bcrypt hash using PHP's password_hash() function for 'user123'
INSERT INTO users (username, email, password, role) VALUES
('user', 'user@merchshipe.com', 'YOUR_BCRYPT_HASH_FOR_user123_HERE', 'user');

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
```

## Password Hash Information

The default passwords in this schema are:
- admin: admin123
- user: user123

You need to generate bcrypt hash values using PHP's password_hash() function:
- For 'admin123': Run password_hash('admin123', PASSWORD_DEFAULT) in PHP
- For 'user123': Run password_hash('user123', PASSWORD_DEFAULT) in PHP

Replace 'YOUR_BCRYPT_HASH_FOR_admin123_HERE' and 'YOUR_BCRYPT_HASH_FOR_user123_HERE' 
with the actual hash values generated by the password_hash() function.

## How to Generate Password Hashes

### Method 1: PHP Command Line
```bash
php -r "echo password_hash('admin123', PASSWORD_DEFAULT);"
php -r "echo password_hash('user123', PASSWORD_DEFAULT);"
```

### Method 2: Create a Simple PHP File
Create a file called `generate_hash.php` with the following content:
```php
<?php
echo "Hash for admin123: " . password_hash('admin123', PASSWORD_DEFAULT) . "\n";
echo "Hash for user123: " . password_hash('user123', PASSWORD_DEFAULT) . "\n";
?>
```

Then access this file via your browser or run it from command line:
```bash
php generate_hash.php
```

### Method 3: Online PHP Sandbox
Use an online PHP sandbox to run: `password_hash('your_password', PASSWORD_DEFAULT)`

## Installation Instructions

1. **Create the database:**
   - Open phpMyAdmin
   - Create a new database named `merchshipe_db`

2. **Import the schema:**
   - Go to the SQL tab in phpMyAdmin
   - Copy the entire SQL schema above (from CREATE DATABASE to the last INSERT statement)
   - Execute the SQL

3. **Generate password hashes:**
   - Use one of the methods above to generate bcrypt hashes for 'admin123' and 'user123'
   - Update the password fields in the users table with the generated hashes

4. **Update configuration:**
   - Open `config/database.php` in your project
   - Ensure the database name is set to `merchshipe_db`

## Permissions Required

Make sure your database user has the following permissions:
- CREATE
- ALTER
- INSERT
- UPDATE
- DELETE
- SELECT

## Database Connection Configuration

After importing this schema, the default configuration in `config/database.php` will connect to:
- Database Name: web_store_app
- Username: root (or your MySQL username)
- Password: '' (or your MySQL password)
- Host: localhost (or your database server address)
- Port: 3306 (default MySQL port)

## Troubleshooting

**Issue: Invalid default value for 'expires_at'**
- Solution: This schema uses `expires_at TIMESTAMP NULL` to avoid the error

**Issue: Foreign key constraint errors**
- Solution: Make sure to create tables in the correct order (referenced tables first)

**Issue: Password not working after setup**
- Solution: Ensure you've properly replaced the placeholder hashes with actual bcrypt hashes