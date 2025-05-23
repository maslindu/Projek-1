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

-- Buat tabel settings
CREATE TABLE IF NOT EXISTS system_settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_name VARCHAR(255) NOT NULL UNIQUE,
    setting_value VARCHAR(255) NOT NULL,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Insert pengaturan default
INSERT INTO system_settings (setting_name, setting_value) VALUES 
('session_timeout', '1800'), -- 30 menit dalam detik
('password_strength', 'medium');

-- Buat tabel activity_log
CREATE TABLE IF NOT EXISTS activity_log (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    username VARCHAR(255) NOT NULL,
    activity_type VARCHAR(50) NOT NULL,
    description TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);