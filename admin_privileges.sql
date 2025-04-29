-- Admin Privileges SQL File for AquaSave Database
-- Created on: 2025-04-29

-- This file contains SQL commands to:
-- 1. Create an admin user with full privileges
-- 2. Grant all necessary permissions for database management
-- 3. Set up admin-specific tables and views

-- Create the database if it doesn't exist
CREATE DATABASE IF NOT EXISTS aquasave;

-- Use the database
USE aquasave;

-- Create users table if it doesn't exist
CREATE TABLE IF NOT EXISTS users (
    id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    location VARCHAR(50) NOT NULL,
    user_type VARCHAR(20) NOT NULL,
    is_admin TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create admin user if it doesn't exist
INSERT INTO users (first_name, last_name, email, password, location, user_type, is_admin)
SELECT 'Admin', 'User', 'admin@aquasave.com', '$2y$10$YourHashedPasswordHere', 'urban', 'admin', 1
FROM dual
WHERE NOT EXISTS (SELECT * FROM users WHERE email = 'admin@aquasave.com');

-- Create water usage table
CREATE TABLE IF NOT EXISTS water_usage (
    id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    usage_amount DECIMAL(10,2) NOT NULL,
    usage_date DATE NOT NULL,
    usage_type VARCHAR(50) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Create devices table
CREATE TABLE IF NOT EXISTS devices (
    id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    device_name VARCHAR(100) NOT NULL,
    device_type VARCHAR(50) NOT NULL,
    installation_date DATE NOT NULL,
    status VARCHAR(20) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Create water saving goals table
CREATE TABLE IF NOT EXISTS goals (
    id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    goal_description TEXT NOT NULL,
    target_amount DECIMAL(10,2) NOT NULL,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    status VARCHAR(20) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Create tips table
CREATE TABLE IF NOT EXISTS tips (
    id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(100) NOT NULL,
    description TEXT NOT NULL,
    category VARCHAR(50) NOT NULL,
    created_by INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(id)
);

-- Create system settings table (admin only)
CREATE TABLE IF NOT EXISTS system_settings (
    id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    setting_name VARCHAR(100) NOT NULL UNIQUE,
    setting_value TEXT NOT NULL,
    description TEXT,
    updated_by INT NOT NULL,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (updated_by) REFERENCES users(id)
);

-- Create admin analytics view
CREATE OR REPLACE VIEW admin_analytics AS
SELECT 
    u.id AS user_id,
    CONCAT(u.first_name, ' ', u.last_name) AS user_name,
    u.email,
    u.location,
    u.user_type,
    COUNT(DISTINCT d.id) AS device_count,
    COUNT(DISTINCT g.id) AS goals_count,
    SUM(w.usage_amount) AS total_water_usage
FROM 
    users u
LEFT JOIN 
    devices d ON u.id = d.user_id
LEFT JOIN 
    goals g ON u.id = g.user_id
LEFT JOIN 
    water_usage w ON u.id = w.user_id
GROUP BY 
    u.id, u.first_name, u.last_name, u.email, u.location, u.user_type;

-- Create admin user management stored procedure
DELIMITER //
CREATE PROCEDURE IF NOT EXISTS manage_user(
    IN action VARCHAR(10),
    IN user_id INT,
    IN first_name VARCHAR(50),
    IN last_name VARCHAR(50),
    IN email VARCHAR(100),
    IN password VARCHAR(255),
    IN location VARCHAR(50),
    IN user_type VARCHAR(20),
    IN is_admin TINYINT(1)
)
BEGIN
    IF action = 'CREATE' THEN
        INSERT INTO users (first_name, last_name, email, password, location, user_type, is_admin)
        VALUES (first_name, last_name, email, password, location, user_type, is_admin);
    ELSEIF action = 'UPDATE' THEN
        UPDATE users
        SET 
            first_name = IFNULL(first_name, first_name),
            last_name = IFNULL(last_name, last_name),
            email = IFNULL(email, email),
            password = IFNULL(password, password),
            location = IFNULL(location, location),
            user_type = IFNULL(user_type, user_type),
            is_admin = IFNULL(is_admin, is_admin)
        WHERE id = user_id;
    ELSEIF action = 'DELETE' THEN
        DELETE FROM users WHERE id = user_id;
    END IF;
END //
DELIMITER ;

-- Grant admin privileges to the MySQL user
-- Create admin user if it doesn't exist
CREATE USER IF NOT EXISTS 'admin'@'localhost' IDENTIFIED BY 'admin_secure_password';
GRANT ALL PRIVILEGES ON aquasave.* TO 'admin'@'localhost';
FLUSH PRIVILEGES;
