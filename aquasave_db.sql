-- Create the AquaSave database
CREATE DATABASE IF NOT EXISTS aquasave;
USE aquasave;

-- Create users table
CREATE TABLE IF NOT EXISTS users (
    id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    location VARCHAR(50) NOT NULL,
    user_type VARCHAR(20) NOT NULL DEFAULT 'residential',
    is_admin TINYINT(1) NOT NULL DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Create water_usage table
CREATE TABLE IF NOT EXISTS water_usage (
    id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    usage_date DATE NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    unit VARCHAR(10) NOT NULL DEFAULT 'liters',
    source VARCHAR(50) NOT NULL DEFAULT 'tap',
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Create devices table
CREATE TABLE IF NOT EXISTS devices (
    id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    device_name VARCHAR(100) NOT NULL,
    device_type VARCHAR(50) NOT NULL,
    status VARCHAR(20) NOT NULL DEFAULT 'active',
    added_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Create goals table
CREATE TABLE IF NOT EXISTS goals (
    id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    goal_name VARCHAR(100) NOT NULL,
    target_amount DECIMAL(10,2) NOT NULL,
    current_amount DECIMAL(10,2) NOT NULL DEFAULT 0,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    status VARCHAR(20) NOT NULL DEFAULT 'in_progress',
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Insert admin user if it doesn't exist
INSERT INTO users (first_name, last_name, email, password, location, user_type, is_admin)
SELECT 'Admin', 'User', 'admin@aquasave.com', '$2y$10$YourHashedPasswordHere', 'urban', 'admin', 1
FROM dual
WHERE NOT EXISTS (SELECT 1 FROM users WHERE email = 'admin@aquasave.com');

-- Insert sample users for demonstration
INSERT INTO users (first_name, last_name, email, password, location, user_type, is_admin)
SELECT 'John', 'Doe', 'john@example.com', '$2y$10$SampleHashedPasswordHere', 'suburban', 'residential', 0
FROM dual
WHERE NOT EXISTS (SELECT 1 FROM users WHERE email = 'john@example.com');

INSERT INTO users (first_name, last_name, email, password, location, user_type, is_admin)
SELECT 'Jane', 'Smith', 'jane@example.com', '$2y$10$AnotherHashedPasswordHere', 'urban', 'commercial', 0
FROM dual
WHERE NOT EXISTS (SELECT 1 FROM users WHERE email = 'jane@example.com');

-- Insert sample water usage data
INSERT INTO water_usage (user_id, usage_date, amount, unit, source)
VALUES 
(2, DATE_SUB(CURDATE(), INTERVAL 7 DAY), 120.5, 'liters', 'tap'),
(2, DATE_SUB(CURDATE(), INTERVAL 6 DAY), 115.2, 'liters', 'tap'),
(2, DATE_SUB(CURDATE(), INTERVAL 5 DAY), 118.7, 'liters', 'tap'),
(2, DATE_SUB(CURDATE(), INTERVAL 4 DAY), 110.3, 'liters', 'tap'),
(2, DATE_SUB(CURDATE(), INTERVAL 3 DAY), 105.8, 'liters', 'tap'),
(2, DATE_SUB(CURDATE(), INTERVAL 2 DAY), 112.4, 'liters', 'tap'),
(2, DATE_SUB(CURDATE(), INTERVAL 1 DAY), 108.9, 'liters', 'tap'),
(3, DATE_SUB(CURDATE(), INTERVAL 7 DAY), 320.5, 'liters', 'tap'),
(3, DATE_SUB(CURDATE(), INTERVAL 6 DAY), 315.2, 'liters', 'tap'),
(3, DATE_SUB(CURDATE(), INTERVAL 5 DAY), 318.7, 'liters', 'tap'),
(3, DATE_SUB(CURDATE(), INTERVAL 4 DAY), 310.3, 'liters', 'tap'),
(3, DATE_SUB(CURDATE(), INTERVAL 3 DAY), 305.8, 'liters', 'tap'),
(3, DATE_SUB(CURDATE(), INTERVAL 2 DAY), 312.4, 'liters', 'tap'),
(3, DATE_SUB(CURDATE(), INTERVAL 1 DAY), 308.9, 'liters', 'tap');

-- Insert sample devices
INSERT INTO devices (user_id, device_name, device_type, status)
VALUES 
(2, 'Kitchen Faucet', 'faucet', 'active'),
(2, 'Bathroom Shower', 'shower', 'active'),
(2, 'Garden Sprinkler', 'sprinkler', 'inactive'),
(3, 'Main Water Meter', 'meter', 'active'),
(3, 'Restroom Faucets', 'faucet', 'active'),
(3, 'Water Cooler', 'cooler', 'active');

-- Insert sample goals
INSERT INTO goals (user_id, goal_name, target_amount, current_amount, start_date, end_date, status)
VALUES 
(2, 'Reduce Weekly Usage', 700, 650, DATE_SUB(CURDATE(), INTERVAL 14 DAY), DATE_ADD(CURDATE(), INTERVAL 14 DAY), 'in_progress'),
(2, 'Save Water in Kitchen', 300, 280, DATE_SUB(CURDATE(), INTERVAL 10 DAY), DATE_ADD(CURDATE(), INTERVAL 20 DAY), 'in_progress'),
(3, 'Monthly Conservation', 9000, 8500, DATE_SUB(CURDATE(), INTERVAL 15 DAY), DATE_ADD(CURDATE(), INTERVAL 15 DAY), 'in_progress'),
(3, 'Reduce Bathroom Usage', 3000, 2800, DATE_SUB(CURDATE(), INTERVAL 5 DAY), DATE_ADD(CURDATE(), INTERVAL 25 DAY), 'in_progress');
