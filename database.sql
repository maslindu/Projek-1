-- Buat database
CREATE DATABASE IF NOT EXISTS praktikum1php;

-- Gunakan database
USE praktikum1php;

-- Buat tabel users
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(255) NOT NULL UNIQUE,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'user') NOT NULL DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert admin default
INSERT INTO users (username, email, password, role) 
VALUES ('admin', 'admin@example.com', '0192023a7bbd73250516f069df18b500', 'admin'); --password: admin123