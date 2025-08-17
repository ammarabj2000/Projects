-- Database: bankapp
CREATE DATABASE IF NOT EXISTS bankapp CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE bankapp;

-- Users
CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  email VARCHAR(190) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  role ENUM('admin','teller','auditor') NOT NULL DEFAULT 'admin',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Customers
CREATE TABLE IF NOT EXISTS customers (
  id INT AUTO_INCREMENT PRIMARY KEY,
  first_name VARCHAR(100) NOT NULL,
  last_name VARCHAR(100) NOT NULL,
  email VARCHAR(190) UNIQUE,
  phone VARCHAR(30),
  address TEXT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Accounts
CREATE TABLE IF NOT EXISTS accounts (
  id INT AUTO_INCREMENT PRIMARY KEY,
  customer_id INT NOT NULL,
  account_number VARCHAR(20) NOT NULL UNIQUE,
  type ENUM('savings','checking') NOT NULL,
  status ENUM('active','frozen','closed') NOT NULL DEFAULT 'active',
  balance DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Transactions
CREATE TABLE IF NOT EXISTS transactions (
  id INT AUTO_INCREMENT PRIMARY KEY,
  account_id INT NOT NULL,
  amount DECIMAL(12,2) NOT NULL,
  type ENUM('deposit','withdraw') NOT NULL,
  description VARCHAR(255),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (account_id) REFERENCES accounts(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Seed admin user
-- Password: Admin@12345
INSERT INTO users (name, email, password_hash, role) VALUES
('Administrator', 'admin@bank.local', '$2y$12$9F9mUu6v8vSfiE0/OUr3Iuv9hRvjTeoWJb5fH29XUGZsMip7X.k8y', 'admin')
ON DUPLICATE KEY UPDATE email = email;