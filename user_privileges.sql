-- Normal User Privileges SQL File for AquaSave Database
-- Created on: 2025-04-29

-- This file contains SQL commands to:
-- 1. Create a normal user with limited privileges
-- 2. Grant necessary permissions for user operations
-- 3. Set up user-specific views and stored procedures

-- Use the database
USE aquasave;

-- Create a normal user account in MySQL with limited privileges
CREATE USER IF NOT EXISTS 'normal_user'@'localhost' IDENTIFIED BY 'user_secure_password';

-- Grant limited privileges to the normal user
GRANT SELECT, INSERT, UPDATE ON aquasave.users TO 'normal_user'@'localhost';
GRANT SELECT, INSERT, UPDATE, DELETE ON aquasave.water_usage TO 'normal_user'@'localhost';
GRANT SELECT, INSERT, UPDATE, DELETE ON aquasave.devices TO 'normal_user'@'localhost';
GRANT SELECT, INSERT, UPDATE, DELETE ON aquasave.goals TO 'normal_user'@'localhost';
GRANT SELECT ON aquasave.tips TO 'normal_user'@'localhost';
FLUSH PRIVILEGES;

-- Create user-specific views

-- Personal water usage view
CREATE OR REPLACE VIEW user_water_usage AS
SELECT 
    w.id,
    w.usage_amount,
    w.usage_date,
    w.usage_type,
    w.created_at
FROM 
    water_usage w
JOIN 
    users u ON w.user_id = u.id
WHERE 
    u.id = CURRENT_USER();

-- Personal devices view
CREATE OR REPLACE VIEW user_devices AS
SELECT 
    d.id,
    d.device_name,
    d.device_type,
    d.installation_date,
    d.status,
    d.created_at
FROM 
    devices d
JOIN 
    users u ON d.user_id = u.id
WHERE 
    u.id = CURRENT_USER();

-- Personal goals view
CREATE OR REPLACE VIEW user_goals AS
SELECT 
    g.id,
    g.goal_description,
    g.target_amount,
    g.start_date,
    g.end_date,
    g.status,
    g.created_at
FROM 
    goals g
JOIN 
    users u ON g.user_id = u.id
WHERE 
    u.id = CURRENT_USER();

-- User analytics view (limited to their own data)
CREATE OR REPLACE VIEW user_analytics AS
SELECT 
    u.id AS user_id,
    CONCAT(u.first_name, ' ', u.last_name) AS user_name,
    u.location,
    COUNT(DISTINCT d.id) AS device_count,
    COUNT(DISTINCT g.id) AS goals_count,
    SUM(w.usage_amount) AS total_water_usage,
    AVG(w.usage_amount) AS average_daily_usage
FROM 
    users u
LEFT JOIN 
    devices d ON u.id = d.user_id
LEFT JOIN 
    goals g ON u.id = g.user_id
LEFT JOIN 
    water_usage w ON u.id = w.user_id
WHERE 
    u.id = CURRENT_USER()
GROUP BY 
    u.id, u.first_name, u.last_name, u.location;

-- Create stored procedures for normal users

-- Add water usage record
DELIMITER //
CREATE PROCEDURE IF NOT EXISTS add_water_usage(
    IN p_usage_amount DECIMAL(10,2),
    IN p_usage_date DATE,
    IN p_usage_type VARCHAR(50)
)
BEGIN
    DECLARE user_id INT;
    
    -- Get the current user's ID
    SELECT id INTO user_id FROM users WHERE email = CURRENT_USER();
    
    -- Insert the water usage record
    INSERT INTO water_usage (user_id, usage_amount, usage_date, usage_type)
    VALUES (user_id, p_usage_amount, p_usage_date, p_usage_type);
END //
DELIMITER ;

-- Add device
DELIMITER //
CREATE PROCEDURE IF NOT EXISTS add_device(
    IN p_device_name VARCHAR(100),
    IN p_device_type VARCHAR(50),
    IN p_installation_date DATE,
    IN p_status VARCHAR(20)
)
BEGIN
    DECLARE user_id INT;
    
    -- Get the current user's ID
    SELECT id INTO user_id FROM users WHERE email = CURRENT_USER();
    
    -- Insert the device
    INSERT INTO devices (user_id, device_name, device_type, installation_date, status)
    VALUES (user_id, p_device_name, p_device_type, p_installation_date, p_status);
END //
DELIMITER ;

-- Add goal
DELIMITER //
CREATE PROCEDURE IF NOT EXISTS add_goal(
    IN p_goal_description TEXT,
    IN p_target_amount DECIMAL(10,2),
    IN p_start_date DATE,
    IN p_end_date DATE,
    IN p_status VARCHAR(20)
)
BEGIN
    DECLARE user_id INT;
    
    -- Get the current user's ID
    SELECT id INTO user_id FROM users WHERE email = CURRENT_USER();
    
    -- Insert the goal
    INSERT INTO goals (user_id, goal_description, target_amount, start_date, end_date, status)
    VALUES (user_id, p_goal_description, p_target_amount, p_start_date, p_end_date, p_status);
END //
DELIMITER ;

-- Update user profile
DELIMITER //
CREATE PROCEDURE IF NOT EXISTS update_profile(
    IN p_first_name VARCHAR(50),
    IN p_last_name VARCHAR(50),
    IN p_email VARCHAR(100),
    IN p_location VARCHAR(50)
)
BEGIN
    DECLARE user_id INT;
    
    -- Get the current user's ID
    SELECT id INTO user_id FROM users WHERE email = CURRENT_USER();
    
    -- Update the user profile
    UPDATE users
    SET 
        first_name = IFNULL(p_first_name, first_name),
        last_name = IFNULL(p_last_name, last_name),
        email = IFNULL(p_email, email),
        location = IFNULL(p_location, location)
    WHERE id = user_id;
END //
DELIMITER ;
