<?php
/**
 * Database Connection Check Utility
 * 
 * This file provides functions to check if the database connection is working
 * and handles redirection to admin page with pseudo data when needed.
 */

/**
 * Check if the database connection is working
 * 
 * @param mysqli $conn The database connection object
 * @return bool True if connection is working, false otherwise
 */
function is_db_connected($conn) {
    if (!$conn) {
        return false;
    }
    
    // Try to select the database
    try {
        $db_select = mysqli_select_db($conn, DB_NAME);
        return $db_select !== false;
    } catch (Exception $e) {
        return false;
    }
}

/**
 * Handle admin login when database is not connected
 * 
 * @param string $email The email used for login attempt
 * @param string $password The password used for login attempt
 * @return bool True if admin credentials are valid, false otherwise
 */
function handle_admin_login_without_db($email, $password) {
    // Check if admin credentials are used
    if ($email === 'admin@aquasave.com' && $password === 'admin123') {
        // Set admin session variables
        $_SESSION["loggedin"] = true;
        $_SESSION["id"] = 1;
        $_SESSION["email"] = "admin@aquasave.com";
        $_SESSION["first_name"] = "Admin";
        $_SESSION["last_name"] = "User";
        $_SESSION["is_admin"] = 1;
        $_SESSION["db_connected"] = false; // Flag to indicate DB is not connected
        
        // Redirect to admin page
        header("location: admin.php");
        return true;
    }
    
    return false;
}

/**
 * Generate pseudo data for users
 * 
 * @return array Array of sample user data
 */
function get_pseudo_users() {
    return [
        1 => [
            'id' => 1,
            'first_name' => 'Admin',
            'last_name' => 'User',
            'email' => 'admin@aquasave.com',
            'location' => 'urban',
            'user_type' => 'admin',
            'is_admin' => 1,
            'created_at' => date('Y-m-d H:i:s', strtotime('-30 days'))
        ],
        2 => [
            'id' => 2,
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@example.com',
            'location' => 'suburban',
            'user_type' => 'residential',
            'is_admin' => 0,
            'created_at' => date('Y-m-d H:i:s', strtotime('-25 days'))
        ],
        3 => [
            'id' => 3,
            'first_name' => 'Jane',
            'last_name' => 'Smith',
            'email' => 'jane@example.com',
            'location' => 'urban',
            'user_type' => 'commercial',
            'is_admin' => 0,
            'created_at' => date('Y-m-d H:i:s', strtotime('-20 days'))
        ],
        4 => [
            'id' => 4,
            'first_name' => 'Robert',
            'last_name' => 'Johnson',
            'email' => 'robert@example.com',
            'location' => 'rural',
            'user_type' => 'residential',
            'is_admin' => 0,
            'created_at' => date('Y-m-d H:i:s', strtotime('-15 days'))
        ],
        5 => [
            'id' => 5,
            'first_name' => 'Emily',
            'last_name' => 'Williams',
            'email' => 'emily@example.com',
            'location' => 'suburban',
            'user_type' => 'residential',
            'is_admin' => 0,
            'created_at' => date('Y-m-d H:i:s', strtotime('-10 days'))
        ]
    ];
}
?>
