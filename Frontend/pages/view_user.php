<?php
// Initialize the session
session_start();
 
// Check if the user is logged in, if not then redirect to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}

// Check if user is admin
if(!isset($_SESSION["is_admin"]) || $_SESSION["is_admin"] !== 1){
    header("location: dashboard.php?error=You do not have permission to access this page");
    exit;
}

// Check if user ID is provided
if(!isset($_GET["id"]) || empty($_GET["id"])){
    header("location: admin.php?error=Invalid user ID");
    exit;
}

$user_id = $_GET["id"];

// Sample user data
$pseudo_users = [
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

// Get user by ID or default to first user
$user = isset($pseudo_users[$user_id]) ? $pseudo_users[$user_id] : $pseudo_users[1];

// Generate sample water usage data
$water_usage = [];
for($i = 0; $i < 5; $i++) {
    $water_usage[] = [
        'id' => $i + 1,
        'user_id' => $user_id,
        'usage_amount' => rand(50, 200) / 10,
        'usage_date' => date('Y-m-d', strtotime("-$i days")),
        'usage_type' => ['bathroom', 'kitchen', 'garden', 'laundry'][rand(0, 3)],
        'created_at' => date('Y-m-d H:i:s', strtotime("-$i days"))
    ];
}

// Generate sample devices data
$devices = [];
$device_types = ['smart meter', 'water filter', 'irrigation system', 'leak detector'];
for($i = 0; $i < 3; $i++) {
    $devices[] = [
        'id' => $i + 1,
        'user_id' => $user_id,
        'device_name' => $device_types[$i] . ' ' . ($i + 1),
        'device_type' => $device_types[$i],
        'installation_date' => date('Y-m-d', strtotime("-" . (30 + $i * 10) . " days")),
        'status' => ['active', 'inactive', 'maintenance'][rand(0, 2)],
        'created_at' => date('Y-m-d H:i:s', strtotime("-" . (30 + $i * 10) . " days"))
    ];
}

// Generate sample goals data
$goals = [];
$goal_descriptions = [
    'Reduce daily water usage by 10%',
    'Install water-efficient fixtures',
    'Fix all leaks in the house'
];

for($i = 0; $i < 3; $i++) {
    $goals[] = [
        'id' => $i + 1,
        'user_id' => $user_id,
        'goal_description' => $goal_descriptions[$i],
        'target_amount' => rand(100, 500) / 10,
        'start_date' => date('Y-m-d', strtotime("-" . (60 + $i * 10) . " days")),
        'end_date' => date('Y-m-d', strtotime("+" . (30 + $i * 10) . " days")),
        'status' => ['in progress', 'completed', 'pending'][rand(0, 2)],
        'created_at' => date('Y-m-d H:i:s', strtotime("-" . (60 + $i * 10) . " days"))
    ];
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

    <!-- Main Content -->
    <section class="pt-24 pb-12">
        <div class="container mx-auto px-4">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl md:text-3xl font-bold text-blue-800">User Details</h1>
                <a href="admin.php" class="bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded-md font-medium transition">
                    <i class="fas fa-arrow-left mr-1"></i> Back to Admin
                </a>
            </div>
            
            <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded relative mb-6" role="alert">
                <strong class="font-bold">Demo Mode:</strong>
                <span class="block sm:inline"> Showing pseudo data for presentation purposes.</span>
            </div>

            <!-- User Info -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-8">
                <div class="flex flex-col md:flex-row items-start md:items-center">
                    <div class="h-20 w-20 rounded-full bg-blue-500 flex items-center justify-center text-white text-3xl font-semibold mr-6 mb-4 md:mb-0">
                        <?php echo substr($user['first_name'], 0, 1) . substr($user['last_name'], 0, 1); ?>
                    </div>
                    <div>
                        <h2 class="text-2xl font-bold text-gray-800"><?php echo $user['first_name'] . ' ' . $user['last_name']; ?></h2>
                        <p class="text-gray-600 mb-1"><?php echo $user['email']; ?></p>
                        <div class="flex flex-wrap gap-2 mt-2">
                            <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-xs font-medium">
                                <?php echo ucfirst($user['location']); ?>
                            </span>
                            <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-xs font-medium">
                                <?php echo ucfirst($user['user_type']); ?>
                            </span>
                            <?php if($user['is_admin']): ?>
                                <span class="bg-purple-100 text-purple-800 px-3 py-1 rounded-full text-xs font-medium">
                                    Admin
                                </span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="ml-auto mt-4 md:mt-0">
                        <p class="text-sm text-gray-500">Joined: <?php echo date('M d, Y', strtotime($user['created_at'])); ?></p>
                    </div>
                </div>
            </div>

            <!-- Usage Statistics -->
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
                            echo number_format($total_usage, 1);
                        ?> L
                    </p>
                    <p class="text-sm text-gray-500 mt-2">Last 30 days</p>
                </div>
                
                <!-- Active Devices -->
                <div class="bg-white rounded-lg shadow-md p-5">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-gray-600 font-medium">Active Devices</h3>
                        <i class="fas fa-microchip text-green-500"></i>
                    </div>
                    <p class="text-3xl font-bold text-green-600">
                        <?php 
                            $active_devices = 0;
                            foreach($devices as $device) {
                                if($device['status'] == 'active') {
                                    $active_devices++;
                                }
                            }
                            echo $active_devices;
                        ?>
                    </p>
                    <p class="text-sm text-gray-500 mt-2">Out of <?php echo count($devices); ?> total devices</p>
                </div>
                
                <!-- Goals Progress -->
                <div class="bg-white rounded-lg shadow-md p-5">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-gray-600 font-medium">Goals Progress</h3>
                        <i class="fas fa-bullseye text-purple-500"></i>
                    </div>
                    <p class="text-3xl font-bold text-purple-600">
                        <?php 
                            $completed_goals = 0;
                            foreach($goals as $goal) {
                                if($goal['status'] == 'completed') {
                                    $completed_goals++;
                                }
                            }
                            echo $completed_goals . '/' . count($goals);
                        ?>
                    </p>
                    <p class="text-sm text-gray-500 mt-2">Goals completed</p>
                </div>
            </div>
            
            <!-- Water Usage History -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-8">
                <h2 class="text-xl font-semibold text-gray-700 mb-4">Water Usage History</h2>
                
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
                                    <td class="py-3 px-4"><?php echo number_format($usage["usage_amount"], 1); ?></td>
                                    <td class="py-3 px-4">
                                        <span class="px-2 py-1 rounded-full text-xs font-medium 
                                            <?php 
                                                switch($usage["usage_type"]) {
                                                    case "bathroom":
                                                        echo "bg-blue-100 text-blue-800";
                                                        break;
                                                    case "kitchen":
                                                        echo "bg-green-100 text-green-800";
                                                        break;
                                                    case "garden":
                                                        echo "bg-yellow-100 text-yellow-800";
                                                        break;
                                                    case "laundry":
                                                        echo "bg-purple-100 text-purple-800";
                                                        break;
                                                    default:
                                                        echo "bg-gray-100 text-gray-800";
                                                }
                                            ?>">
                                            <?php echo ucfirst($usage["usage_type"]); ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
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
                                            <span class="px-2 py-1 rounded-full text-xs font-medium 
                                                <?php 
                                                    switch($device["status"]) {
                                                        case "active":
                                                            echo "bg-green-100 text-green-800";
                                                            break;
                                                        case "inactive":
                                                            echo "bg-red-100 text-red-800";
                                                            break;
                                                        case "maintenance":
                                                            echo "bg-yellow-100 text-yellow-800";
                                                            break;
                                                        default:
                                                            echo "bg-gray-100 text-gray-800";
                                                    }
                                                ?>">
                                                <?php echo ucfirst($device["status"]); ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p class="text-gray-500">No devices found for this user.</p>
                <?php endif; ?>
            </div>
            
            <!-- Water Saving Goals -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-8">
                <h2 class="text-xl font-semibold text-gray-700 mb-4">Water Saving Goals</h2>
                
                <?php if(count($goals) > 0): ?>
                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white">
                            <thead>
                                <tr class="bg-blue-100 text-blue-800">
                                    <th class="py-3 px-4 text-left">Goal</th>
                                    <th class="py-3 px-4 text-left">Target</th>
                                    <th class="py-3 px-4 text-left">Timeline</th>
                                    <th class="py-3 px-4 text-left">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($goals as $goal): ?>
                                    <tr class="border-b hover:bg-blue-50">
                                        <td class="py-3 px-4"><?php echo htmlspecialchars($goal["goal_description"]); ?></td>
                                        <td class="py-3 px-4"><?php echo number_format($goal["target_amount"], 1); ?> L</td>
                                        <td class="py-3 px-4">
                                            <?php 
                                                echo date("M d, Y", strtotime($goal["start_date"])); 
                                                echo " - ";
                                                echo date("M d, Y", strtotime($goal["end_date"]));
                                            ?>
                                        </td>
                                        <td class="py-3 px-4">
                                            <span class="px-2 py-1 rounded-full text-xs font-medium 
                                                <?php 
                                                    switch($goal["status"]) {
                                                        case "completed":
                                                            echo "bg-green-100 text-green-800";
                                                            break;
                                                        case "in progress":
                                                            echo "bg-blue-100 text-blue-800";
                                                            break;
                                                        case "pending":
                                                            echo "bg-yellow-100 text-yellow-800";
                                                            break;
                                                        default:
                                                            echo "bg-gray-100 text-gray-800";
                                                    }
                                                ?>">
                                                <?php echo ucfirst($goal["status"]); ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p class="text-gray-500">No goals found for this user.</p>
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
                        <li><a href="#" class="text-blue-200 hover:text-white">FAQ</a></li>
                        <li><a href="#" class="text-blue-200 hover:text-white">Support</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-medium mb-4">Contact</h4>
                    <ul class="space-y-2">
                        <li class="flex items-start">
                            <i class="fas fa-envelope text-blue-300 mt-1 mr-2"></i>
                            <span class="text-blue-200">info@aquasave.com</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-phone text-blue-300 mt-1 mr-2"></i>
                            <span class="text-blue-200">+1 (555) 123-4567</span>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-blue-700 mt-8 pt-6 text-center text-blue-300">
                <p>&copy; 2025 AquaSave. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script>
        // Mobile menu toggle
        document.getElementById('menu-toggle')?.addEventListener('click', function() {
            document.getElementById('mobile-menu').classList.toggle('hidden');
        });
    </script>
</body>
</html>
