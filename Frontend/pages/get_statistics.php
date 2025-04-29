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

// Initialize statistics
$stats = [
    'total_users' => 0,
    'residential_users' => 0,
    'commercial_users' => 0,
    'industrial_users' => 0
];

// Get total users
$sql = "SELECT COUNT(*) as total FROM users";
$result = $conn->query($sql);
if ($result && $row = $result->fetch_assoc()) {
    $stats['total_users'] = (int)$row['total'];
}

// Get residential users
$sql = "SELECT COUNT(*) as total FROM users WHERE user_type = 'residential'";
$result = $conn->query($sql);
if ($result && $row = $result->fetch_assoc()) {
    $stats['residential_users'] = (int)$row['total'];
}

// Get commercial users
$sql = "SELECT COUNT(*) as total FROM users WHERE user_type = 'commercial'";
$result = $conn->query($sql);
if ($result && $row = $result->fetch_assoc()) {
    $stats['commercial_users'] = (int)$row['total'];
}

// Get industrial users
$sql = "SELECT COUNT(*) as total FROM users WHERE user_type = 'industrial'";
$result = $conn->query($sql);
if ($result && $row = $result->fetch_assoc()) {
    $stats['industrial_users'] = (int)$row['total'];
}

// Return statistics as JSON
echo json_encode($stats);

// Close connection
$conn->close();
?>
