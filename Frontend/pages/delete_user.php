<?php
// Include database configuration
require_once "config.php";

// Set header to return JSON
header('Content-Type: application/json');

// Check if request method is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request method'
    ]);
    exit;
}

// Get the request body
$data = json_decode(file_get_contents('php://input'), true);

// Check if user ID is provided
if (!isset($data['id']) || empty($data['id'])) {
    echo json_encode([
        'success' => false,
        'message' => 'User ID is required'
    ]);
    exit;
}

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

$user_id = $data['id'];

// Check if user is admin (prevent deleting admin users)
$sql = "SELECT is_admin FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode([
        'success' => false,
        'message' => 'User not found'
    ]);
    $stmt->close();
    $conn->close();
    exit;
}

$user = $result->fetch_assoc();
if ($user['is_admin'] == 1) {
    echo json_encode([
        'success' => false,
        'message' => 'Cannot delete admin user'
    ]);
    $stmt->close();
    $conn->close();
    exit;
}

// Delete user
$sql = "DELETE FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);

if ($stmt->execute()) {
    echo json_encode([
        'success' => true,
        'message' => 'User deleted successfully'
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Failed to delete user',
        'error' => $conn->error
    ]);
}

// Close statement and connection
$stmt->close();
$conn->close();
?>
