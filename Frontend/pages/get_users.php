<?php
// Include database configuration
require_once "config.php";

// Set header to return JSON
header('Content-Type: application/json');

// Check if database is connected
if(!isset($GLOBALS['db_connected']) || $GLOBALS['db_connected'] === false) {
    // Return error if database is not connected
    echo json_encode([
        'success' => false,
        'message' => 'Database connection failed',
        'error' => isset($GLOBALS['db_error']) ? $GLOBALS['db_error'] : 'Unknown error'
    ]);
    exit;
}

// Query to get all users
$sql = "SELECT id, first_name, last_name, email, location, user_type, is_admin, created_at FROM users ORDER BY created_at DESC";
$result = $conn->query($sql);

if ($result) {
    $users = [];
    
    // Fetch all users
    while($row = $result->fetch_assoc()) {
        $users[] = $row;
    }
    
    // Return users as JSON
    echo json_encode($users);
} else {
    // Return error if query failed
    echo json_encode([
        'success' => false,
        'message' => 'Failed to fetch users',
        'error' => $conn->error
    ]);
}

// Close connection
$conn->close();
?>
