<?php
// Include config file
require_once "config.php";

// Check if admin user exists
$sql = "SELECT * FROM users WHERE email = 'admin@aquasave.com'";
$result = mysqli_query($conn, $sql);

echo "<h2>Admin User Check</h2>";

if(mysqli_num_rows($result) > 0) {
    $user = mysqli_fetch_assoc($result);
    echo "<p>Admin user exists in the database.</p>";
    echo "<p>Admin details:</p>";
    echo "<ul>";
    echo "<li>ID: " . $user['id'] . "</li>";
    echo "<li>Name: " . $user['first_name'] . " " . $user['last_name'] . "</li>";
    echo "<li>Email: " . $user['email'] . "</li>";
    echo "<li>Is Admin: " . ($user['is_admin'] ? 'Yes' : 'No') . "</li>";
    echo "</ul>";
    
    // Check if password is correct
    $password = "admin123";
    if(password_verify($password, $user['password'])) {
        echo "<p style='color: green;'>Password verification successful!</p>";
    } else {
        echo "<p style='color: red;'>Password verification failed. The stored hash doesn't match.</p>";
        
        // Create a new hash for demonstration
        $new_hash = password_hash($password, PASSWORD_DEFAULT);
        echo "<p>New hash for 'admin123': " . $new_hash . "</p>";
        
        // Update the admin password
        $update_sql = "UPDATE users SET password = ? WHERE email = 'admin@aquasave.com'";
        if($stmt = mysqli_prepare($conn, $update_sql)) {
            mysqli_stmt_bind_param($stmt, "s", $new_hash);
            if(mysqli_stmt_execute($stmt)) {
                echo "<p style='color: green;'>Admin password has been updated. Please try logging in again.</p>";
            } else {
                echo "<p style='color: red;'>Error updating password: " . mysqli_error($conn) . "</p>";
            }
            mysqli_stmt_close($stmt);
        }
    }
} else {
    echo "<p>Admin user does not exist in the database.</p>";
    
    // Create admin user
    $admin_password = password_hash("admin123", PASSWORD_DEFAULT);
    $create_admin = "INSERT INTO users (first_name, last_name, email, password, location, user_type, is_admin) 
                     VALUES ('Admin', 'User', 'admin@aquasave.com', '$admin_password', 'urban', 'admin', 1)";
    
    if(mysqli_query($conn, $create_admin)) {
        echo "<p style='color: green;'>Admin user has been created. Please try logging in again.</p>";
    } else {
        echo "<p style='color: red;'>Error creating admin user: " . mysqli_error($conn) . "</p>";
    }
}

// Check if tables exist
echo "<h2>Database Tables Check</h2>";
$tables = ["users", "water_usage", "devices", "goals", "tips", "system_settings"];
foreach($tables as $table) {
    $check_table = "SHOW TABLES LIKE '$table'";
    $table_result = mysqli_query($conn, $check_table);
    
    if(mysqli_num_rows($table_result) > 0) {
        echo "<p>Table '$table' exists.</p>";
    } else {
        echo "<p style='color: red;'>Table '$table' does not exist!</p>";
    }
}

// Show login instructions
echo "<h2>Login Instructions</h2>";
echo "<p>1. Go to <a href='login.php'>login.php</a></p>";
echo "<p>2. Enter the following credentials:</p>";
echo "<ul>";
echo "<li>Email: admin@aquasave.com</li>";
echo "<li>Password: admin123</li>";
echo "</ul>";
echo "<p>3. Click 'Sign In'</p>";
echo "<p>4. You should be redirected to the admin dashboard.</p>";
?>
