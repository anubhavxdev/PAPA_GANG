<?php
// Initialize the session
session_start();
 
// Check if the user is logged in and is an admin, if not then redirect to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || $_SESSION["is_admin"] !== 1){
    header("location: login.php");
    exit;
}

// Include config file
require_once "config.php";

// Check if user ID is provided
if(!isset($_GET["id"]) || empty($_GET["id"])){
    header("location: admin.php?error=Invalid user ID");
    exit;
}

$user_id = $_GET["id"];

// Fetch user details
$sql = "SELECT * FROM users WHERE id = ?";
if($stmt = mysqli_prepare($conn, $sql)){
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    
    if(mysqli_stmt_execute($stmt)){
        $result = mysqli_stmt_get_result($stmt);
        
        if(mysqli_num_rows($result) == 1){
            $user = mysqli_fetch_assoc($result);
        } else {
            header("location: admin.php?error=User not found");
            exit;
        }
    } else {
        header("location: admin.php?error=Something went wrong. Please try again later.");
        exit;
    }
    
    mysqli_stmt_close($stmt);
} else {
    header("location: admin.php?error=Something went wrong. Please try again later.");
    exit;
}

// Fetch user's water usage
$sql = "SELECT * FROM water_usage WHERE user_id = ? ORDER BY usage_date DESC";
$water_usage_result = mysqli_query($conn, "SELECT * FROM water_usage WHERE user_id = $user_id ORDER BY usage_date DESC");
$water_usage = [];
if($water_usage_result){
    $water_usage = mysqli_fetch_all($water_usage_result, MYSQLI_ASSOC);
}

// Fetch user's devices
$devices_result = mysqli_query($conn, "SELECT * FROM devices WHERE user_id = $user_id");
$devices = [];
if($devices_result){
    $devices = mysqli_fetch_all($devices_result, MYSQLI_ASSOC);
}

// Fetch user's goals
$goals_result = mysqli_query($conn, "SELECT * FROM goals WHERE user_id = $user_id ORDER BY end_date ASC");
$goals = [];
if($goals_result){
    $goals = mysqli_fetch_all($goals_result, MYSQLI_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View User - AquaSave Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body class="bg-blue-50 font-poppins">
    <!-- Navigation -->
    <nav class="bg-white shadow-md fixed w-full z-10">
        <div class="container mx-auto px-4">
            <div class="flex justify-between items-center py-4">
                <div class="flex items-center">
                    <a href="../index.html" class="flex items-center">
                        <i class="fas fa-water text-blue-500 text-3xl mr-2"></i>
                        <span class="text-blue-800 font-bold text-xl">AquaSave</span>
                    </a>
                </div>
                <div class="hidden md:flex items-center space-x-8">
                    <a href="../index.html" class="text-blue-800 hover:text-blue-600 font-medium">Home</a>
                    <a href="dashboard.php" class="text-blue-800 hover:text-blue-600 font-medium">Dashboard</a>
                    <a href="admin.php" class="text-blue-800 hover:text-blue-600 font-medium border-b-2 border-blue-500">Admin</a>
                </div>
                <div class="flex items-center">
                    <div class="hidden md:block">
                        <div class="relative group">
                            <div class="flex items-center cursor-pointer">
                                <img src="https://images.unsplash.com/photo-1495774539583-885e02cca8c2" alt="Profile" class="h-8 w-8 rounded-full object-cover">
                                <span class="ml-2 text-gray-700 font-medium"><?php echo htmlspecialchars($_SESSION["first_name"] . " " . $_SESSION["last_name"]); ?></span>
                                <i class="fas fa-chevron-down text-xs ml-1 text-gray-500"></i>
                            </div>
                            <div class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-10 hidden group-hover:block">
                                <a href="profile.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50">
                                    <i class="fas fa-user mr-2 text-blue-500"></i> Profile
                                </a>
                                <a href="settings.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50">
                                    <i class="fas fa-cog mr-2 text-blue-500"></i> Settings
                                </a>
                                <hr class="my-1">
                                <a href="logout.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50">
                                    <i class="fas fa-sign-out-alt mr-2 text-blue-500"></i> Logout
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="md:hidden">
                        <button id="menu-toggle" class="text-blue-800 focus:outline-none">
                            <i class="fas fa-bars text-2xl"></i>
                        </button>
                    </div>
                </div>
            </div>
            <!-- Mobile Menu -->
            <div id="mobile-menu" class="hidden md:hidden pb-4">
                <a href="../index.html" class="block py-2 text-blue-800 hover:text-blue-600 font-medium">Home</a>
                <a href="dashboard.php" class="block py-2 text-blue-800 hover:text-blue-600 font-medium">Dashboard</a>
                <a href="admin.php" class="block py-2 text-blue-800 hover:text-blue-600 font-medium border-l-4 border-blue-500 pl-2">Admin</a>
                <div class="mt-4 flex items-center">
                    <img src="https://images.unsplash.com/photo-1495774539583-885e02cca8c2" alt="Profile" class="h-8 w-8 rounded-full object-cover">
                    <span class="ml-2 text-gray-700 font-medium"><?php echo htmlspecialchars($_SESSION["first_name"] . " " . $_SESSION["last_name"]); ?></span>
                </div>
                <div class="mt-4 space-y-2">
                    <a href="profile.php" class="block py-2 text-blue-800 hover:text-blue-600 font-medium">
                        <i class="fas fa-user mr-2"></i> Profile
                    </a>
                    <a href="settings.php" class="block py-2 text-blue-800 hover:text-blue-600 font-medium">
                        <i class="fas fa-cog mr-2"></i> Settings
                    </a>
                    <a href="logout.php" class="block py-2 text-blue-800 hover:text-blue-600 font-medium">
                        <i class="fas fa-sign-out-alt mr-2"></i> Logout
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- User Details Section -->
    <section class="pt-24 pb-12">
        <div class="container mx-auto px-4">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl md:text-3xl font-bold text-blue-800">User Details</h1>
                <a href="admin.php" class="bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded-md font-medium transition">
                    <i class="fas fa-arrow-left mr-1"></i> Back to Admin
                </a>
            </div>
            
            <!-- User Profile Card -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-8">
                <div class="flex flex-col md:flex-row">
                    <div class="md:w-1/3 mb-6 md:mb-0">
                        <div class="flex flex-col items-center">
                            <div class="w-32 h-32 bg-blue-100 rounded-full flex items-center justify-center mb-4">
                                <i class="fas fa-user text-blue-500 text-5xl"></i>
                            </div>
                            <h2 class="text-xl font-semibold text-gray-800"><?php echo htmlspecialchars($user["first_name"] . " " . $user["last_name"]); ?></h2>
                            <p class="text-gray-600 mb-2"><?php echo htmlspecialchars($user["email"]); ?></p>
                            <div class="mt-2">
                                <?php if($user["is_admin"] == 1): ?>
                                    <span class="bg-purple-100 text-purple-800 px-3 py-1 rounded-full text-sm font-medium">Admin</span>
                                <?php else: ?>
                                    <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm font-medium">User</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <div class="md:w-2/3 md:pl-8 border-t md:border-t-0 md:border-l border-gray-200 pt-6 md:pt-0 md:pl-8">
                        <h3 class="text-lg font-semibold text-gray-700 mb-4">User Information</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <p class="text-gray-500 text-sm">User ID</p>
                                <p class="font-medium"><?php echo htmlspecialchars($user["id"]); ?></p>
                            </div>
                            <div>
                                <p class="text-gray-500 text-sm">Location</p>
                                <p class="font-medium"><?php echo ucfirst(htmlspecialchars($user["location"])); ?></p>
                            </div>
                            <div>
                                <p class="text-gray-500 text-sm">User Type</p>
                                <p class="font-medium"><?php echo ucfirst(htmlspecialchars($user["user_type"])); ?></p>
                            </div>
                            <div>
                                <p class="text-gray-500 text-sm">Joined Date</p>
                                <p class="font-medium"><?php echo date("F d, Y", strtotime($user["created_at"])); ?></p>
                            </div>
                        </div>
                        
                        <div class="mt-6">
                            <h3 class="text-lg font-semibold text-gray-700 mb-4">Actions</h3>
                            <div class="flex space-x-3">
                                <a href="edit_user.php?id=<?php echo $user["id"]; ?>" class="bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded-md font-medium transition">
                                    <i class="fas fa-edit mr-1"></i> Edit User
                                </a>
                                <?php if($user["id"] != $_SESSION["id"]): ?>
                                    <a href="admin.php?delete_id=<?php echo $user["id"]; ?>" class="bg-red-600 hover:bg-red-700 text-white py-2 px-4 rounded-md font-medium transition" onclick="return confirm('Are you sure you want to delete this user?');">
                                        <i class="fas fa-trash mr-1"></i> Delete User
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- User Statistics -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <!-- Water Usage -->
                <div class="bg-white rounded-lg shadow-md p-5">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-gray-600 font-medium">Total Water Usage</h3>
                        <i class="fas fa-tint text-blue-500"></i>
                    </div>
                    <p class="text-3xl font-bold text-blue-800">
                        <?php 
                            $total_usage = 0;
                            foreach($water_usage as $usage) {
                                $total_usage += $usage['usage_amount'];
                            }
                            echo number_format($total_usage, 2) . " L";
                        ?>
                    </p>
                </div>
                
                <!-- Devices Count -->
                <div class="bg-white rounded-lg shadow-md p-5">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-gray-600 font-medium">Devices</h3>
                        <i class="fas fa-microchip text-green-500"></i>
                    </div>
                    <p class="text-3xl font-bold text-green-600"><?php echo count($devices); ?></p>
                </div>
                
                <!-- Goals Count -->
                <div class="bg-white rounded-lg shadow-md p-5">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-gray-600 font-medium">Goals</h3>
                        <i class="fas fa-bullseye text-purple-500"></i>
                    </div>
                    <p class="text-3xl font-bold text-purple-600"><?php echo count($goals); ?></p>
                </div>
            </div>
            
            <!-- Water Usage History -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-8">
                <h2 class="text-xl font-semibold text-gray-700 mb-4">Water Usage History</h2>
                
                <?php if(count($water_usage) > 0): ?>
                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white">
                            <thead>
                                <tr class="bg-blue-100 text-blue-800">
                                    <th class="py-3 px-4 text-left">Date</th>
                                    <th class="py-3 px-4 text-left">Amount (L)</th>
                                    <th class="py-3 px-4 text-left">Type</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($water_usage as $usage): ?>
                                    <tr class="border-b hover:bg-blue-50">
                                        <td class="py-3 px-4"><?php echo date("M d, Y", strtotime($usage["usage_date"])); ?></td>
                                        <td class="py-3 px-4"><?php echo number_format($usage["usage_amount"], 2); ?></td>
                                        <td class="py-3 px-4"><?php echo ucfirst(htmlspecialchars($usage["usage_type"])); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p class="text-gray-600">No water usage records found.</p>
                <?php endif; ?>
            </div>
            
            <!-- Devices -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-8">
                <h2 class="text-xl font-semibold text-gray-700 mb-4">Devices</h2>
                
                <?php if(count($devices) > 0): ?>
                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white">
                            <thead>
                                <tr class="bg-blue-100 text-blue-800">
                                    <th class="py-3 px-4 text-left">Device Name</th>
                                    <th class="py-3 px-4 text-left">Type</th>
                                    <th class="py-3 px-4 text-left">Installation Date</th>
                                    <th class="py-3 px-4 text-left">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($devices as $device): ?>
                                    <tr class="border-b hover:bg-blue-50">
                                        <td class="py-3 px-4"><?php echo htmlspecialchars($device["device_name"]); ?></td>
                                        <td class="py-3 px-4"><?php echo ucfirst(htmlspecialchars($device["device_type"])); ?></td>
                                        <td class="py-3 px-4"><?php echo date("M d, Y", strtotime($device["installation_date"])); ?></td>
                                        <td class="py-3 px-4">
                                            <?php if($device["status"] == "active"): ?>
                                                <span class="bg-green-100 text-green-800 px-2 py-1 rounded-full text-xs font-medium">Active</span>
                                            <?php else: ?>
                                                <span class="bg-gray-100 text-gray-800 px-2 py-1 rounded-full text-xs font-medium"><?php echo ucfirst($device["status"]); ?></span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p class="text-gray-600">No devices found.</p>
                <?php endif; ?>
            </div>
            
            <!-- Goals -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-8">
                <h2 class="text-xl font-semibold text-gray-700 mb-4">Water Saving Goals</h2>
                
                <?php if(count($goals) > 0): ?>
                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white">
                            <thead>
                                <tr class="bg-blue-100 text-blue-800">
                                    <th class="py-3 px-4 text-left">Goal</th>
                                    <th class="py-3 px-4 text-left">Target</th>
                                    <th class="py-3 px-4 text-left">Start Date</th>
                                    <th class="py-3 px-4 text-left">End Date</th>
                                    <th class="py-3 px-4 text-left">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($goals as $goal): ?>
                                    <tr class="border-b hover:bg-blue-50">
                                        <td class="py-3 px-4"><?php echo htmlspecialchars($goal["goal_description"]); ?></td>
                                        <td class="py-3 px-4"><?php echo number_format($goal["target_amount"], 2) . " L"; ?></td>
                                        <td class="py-3 px-4"><?php echo date("M d, Y", strtotime($goal["start_date"])); ?></td>
                                        <td class="py-3 px-4"><?php echo date("M d, Y", strtotime($goal["end_date"])); ?></td>
                                        <td class="py-3 px-4">
                                            <?php if($goal["status"] == "completed"): ?>
                                                <span class="bg-green-100 text-green-800 px-2 py-1 rounded-full text-xs font-medium">Completed</span>
                                            <?php elseif($goal["status"] == "in progress"): ?>
                                                <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded-full text-xs font-medium">In Progress</span>
                                            <?php else: ?>
                                                <span class="bg-gray-100 text-gray-800 px-2 py-1 rounded-full text-xs font-medium"><?php echo ucfirst($goal["status"]); ?></span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p class="text-gray-600">No goals found.</p>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-blue-800 text-white py-8">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div>
                    <h3 class="text-xl font-semibold mb-4">AquaSave</h3>
                    <p class="text-blue-200">Smart water conservation for a sustainable future.</p>
                </div>
                <div>
                    <h4 class="font-medium mb-4">Quick Links</h4>
                    <ul class="space-y-2">
                        <li><a href="../index.html" class="text-blue-200 hover:text-white">Home</a></li>
                        <li><a href="dashboard.php" class="text-blue-200 hover:text-white">Dashboard</a></li>
                        <li><a href="admin.php" class="text-blue-200 hover:text-white">Admin</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-medium mb-4">Resources</h4>
                    <ul class="space-y-2">
                        <li><a href="tips.html" class="text-blue-200 hover:text-white">Water Saving Tips</a></li>
                        <li><a href="#" class="text-blue-200 hover:text-white">FAQ</a></li>
                        <li><a href="#" class="text-blue-200 hover:text-white">Support</a></li>
                        <li><a href="#" class="text-blue-200 hover:text-white">Blog</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-medium mb-4">Connect With Us</h4>
                    <div class="flex space-x-4">
                        <a href="#" class="text-blue-200 hover:text-white"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="text-blue-200 hover:text-white"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="text-blue-200 hover:text-white"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="text-blue-200 hover:text-white"><i class="fab fa-linkedin-in"></i></a>
                    </div>
                </div>
            </div>
            <div class="border-t border-blue-700 mt-8 pt-6 text-center">
                <p class="text-blue-200">&copy; 2023 AquaSave. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script>
        // Toggle mobile menu
        document.getElementById('menu-toggle').addEventListener('click', function() {
            document.getElementById('mobile-menu').classList.toggle('hidden');
        });
    </script>
</body>
</html>
