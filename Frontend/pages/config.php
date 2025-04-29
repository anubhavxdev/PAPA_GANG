<?php
// Database configuration
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_NAME', 'aquasave');

// Attempt to connect to MySQL database
$conn = null;
$db_connection_error = "";

try {
    $conn = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD);

    // Check connection
    if (!$conn) {
        $db_connection_error = "ERROR: Could not connect to MySQL. " . mysqli_connect_error();
    } else {
        // Create database if it doesn't exist
        $sql = "CREATE DATABASE IF NOT EXISTS " . DB_NAME;
        if (mysqli_query($conn, $sql)) {
            // Select the database
            if (!mysqli_select_db($conn, DB_NAME)) {
                $db_connection_error = "ERROR: Could not select database. " . mysqli_error($conn);
            } else {
                // Create users table
                $sql = "CREATE TABLE IF NOT EXISTS users (
                    id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
                    first_name VARCHAR(50) NOT NULL,
                    last_name VARCHAR(50) NOT NULL,
                    email VARCHAR(100) NOT NULL UNIQUE,
                    password VARCHAR(255) NOT NULL,
                    location VARCHAR(50) NOT NULL,
                    user_type VARCHAR(20) NOT NULL,
                    is_admin TINYINT(1) DEFAULT 0,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                )";

                if (!mysqli_query($conn, $sql)) {
                    $db_connection_error = "ERROR: Could not create users table. " . mysqli_error($conn);
                } else {
                    // Create admin user if it doesn't exist
                    $check_admin = "SELECT * FROM users WHERE email = 'admin@aquasave.com'";
                    $result = mysqli_query($conn, $check_admin);

                    if ($result && mysqli_num_rows($result) == 0) {
                        $admin_password = password_hash('admin123', PASSWORD_DEFAULT);
                        $create_admin = "INSERT INTO users (first_name, last_name, email, password, location, user_type, is_admin) 
                                        VALUES ('Admin', 'User', 'admin@aquasave.com', '$admin_password', 'urban', 'admin', 1)";
                        mysqli_query($conn, $create_admin);
                    } else if ($result) {
                        // Ensure admin has admin privileges
                        $update_admin = "UPDATE users SET is_admin = 1 WHERE email = 'admin@aquasave.com'";
                        mysqli_query($conn, $update_admin);
                    }
                }
            }
        } else {
            $db_connection_error = "ERROR: Could not create database. " . mysqli_error($conn);
        }
    }
} catch (Exception $e) {
    $db_connection_error = "ERROR: Database connection failed. " . $e->getMessage();
    $conn = null;
}

// Set a global flag for database connection status
$is_db_connected = ($conn !== null && empty($db_connection_error));
?>
