<?php
// Database configuration
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_NAME', 'aquasave');

// Attempt to connect to MySQL database
try {
    $conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD);
    
    // Check connection
    if($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }
    
    // Create database if it doesn't exist
    $sql = "CREATE DATABASE IF NOT EXISTS " . DB_NAME;
    if($conn->query($sql) === FALSE) {
        throw new Exception("Error creating database: " . $conn->error);
    }
    
    // Select the database
    $conn->select_db(DB_NAME);
    
    // Create users table if it doesn't exist
    $sql = "CREATE TABLE IF NOT EXISTS users (
        id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
        first_name VARCHAR(50) NOT NULL,
        last_name VARCHAR(50) NOT NULL,
        email VARCHAR(100) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        location VARCHAR(50) NOT NULL,
        user_type VARCHAR(20) NOT NULL DEFAULT 'residential',
        is_admin TINYINT(1) NOT NULL DEFAULT 0,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )";
    
    if($conn->query($sql) === FALSE) {
        throw new Exception("Error creating table: " . $conn->error);
    }
    
    // Check if admin user exists, if not create one
    $sql = "SELECT id FROM users WHERE email = 'admin@aquasave.com' LIMIT 1";
    $result = $conn->query($sql);
    
    if($result->num_rows == 0) {
        // Create admin user
        $admin_password = password_hash("admin123", PASSWORD_DEFAULT);
        $sql = "INSERT INTO users (first_name, last_name, email, password, location, user_type, is_admin) 
                VALUES ('Admin', 'User', 'admin@aquasave.com', '$admin_password', 'urban', 'admin', 1)";
        
        if($conn->query($sql) === FALSE) {
            throw new Exception("Error creating admin user: " . $conn->error);
        }
    }
    
    // Set global connection variable
    $GLOBALS['db_connected'] = true;
    
} catch(Exception $e) {
    // Set global connection variable
    $GLOBALS['db_connected'] = false;
    $GLOBALS['db_error'] = $e->getMessage();
}
?>
