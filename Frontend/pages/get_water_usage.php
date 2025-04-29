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

// Initialize data structure
$data = [
    'userTypeUsage' => [
        'residential' => 0,
        'commercial' => 0,
        'industrial' => 0
    ],
    'monthlyUsage' => []
];

// Get water usage by user type
$sql = "SELECT u.user_type, SUM(w.amount) as total_usage 
        FROM water_usage w 
        JOIN users u ON w.user_id = u.id 
        GROUP BY u.user_type";

$result = $conn->query($sql);

if ($result) {
    while($row = $result->fetch_assoc()) {
        if (isset($data['userTypeUsage'][$row['user_type']])) {
            $data['userTypeUsage'][$row['user_type']] = (float)$row['total_usage'];
        }
    }
}

// Get monthly water usage for the current year
$sql = "SELECT 
            MONTH(usage_date) as month, 
            SUM(amount) as total_usage 
        FROM water_usage 
        WHERE YEAR(usage_date) = YEAR(CURRENT_DATE()) 
        GROUP BY MONTH(usage_date) 
        ORDER BY MONTH(usage_date)";

$result = $conn->query($sql);

if ($result) {
    $months = [
        1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'May', 6 => 'Jun',
        7 => 'Jul', 8 => 'Aug', 9 => 'Sep', 10 => 'Oct', 11 => 'Nov', 12 => 'Dec'
    ];
    
    while($row = $result->fetch_assoc()) {
        $monthName = $months[(int)$row['month']];
        $data['monthlyUsage'][$monthName] = (float)$row['total_usage'];
    }
}

// Return data as JSON
echo json_encode($data);

// Close connection
$conn->close();
?>
