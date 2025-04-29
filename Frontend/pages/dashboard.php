<?php
// Initialize the session
session_start();
 
// Check if the user is logged in, if not then redirect to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}

// Include config file
require_once "config.php";

// Get user information
$user_id = $_SESSION["id"];
$sql = "SELECT * FROM users WHERE id = ?";

if($stmt = mysqli_prepare($conn, $sql)){
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    
    if(mysqli_stmt_execute($stmt)){
        $result = mysqli_stmt_get_result($stmt);
        
        if(mysqli_num_rows($result) == 1){
            $user = mysqli_fetch_array($result, MYSQLI_ASSOC);
        } else {
            // User not found
            session_destroy();
            header("location: login.php");
            exit();
        }
    } else {
        echo "Oops! Something went wrong. Please try again later.";
    }
    
    mysqli_stmt_close($stmt);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - AquaSave</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/styles.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
                    <a href="dashboard.php" class="text-blue-800 hover:text-blue-600 font-medium border-b-2 border-blue-500">Dashboard</a>
                    <a href="devices.html" class="text-blue-800 hover:text-blue-600 font-medium">Devices</a>
                    <a href="goals.html" class="text-blue-800 hover:text-blue-600 font-medium">Goals</a>
                    <a href="tips.html" class="text-blue-800 hover:text-blue-600 font-medium">Water Tips</a>
                    <?php if(isset($_SESSION["is_admin"]) && $_SESSION["is_admin"] == 1): ?>
                    <a href="admin.php" class="text-blue-800 hover:text-blue-600 font-medium">Admin</a>
                    <?php endif; ?>
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
                <a href="dashboard.php" class="block py-2 text-blue-800 hover:text-blue-600 font-medium border-l-4 border-blue-500 pl-2">Dashboard</a>
                <a href="devices.html" class="block py-2 text-blue-800 hover:text-blue-600 font-medium">Devices</a>
                <a href="goals.html" class="block py-2 text-blue-800 hover:text-blue-600 font-medium">Goals</a>
                <a href="tips.html" class="block py-2 text-blue-800 hover:text-blue-600 font-medium">Water Tips</a>
                <?php if(isset($_SESSION["is_admin"]) && $_SESSION["is_admin"] == 1): ?>
                <a href="admin.php" class="block py-2 text-blue-800 hover:text-blue-600 font-medium">Admin</a>
                <?php endif; ?>
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

    <!-- Main Dashboard Section -->
    <section class="pt-24 pb-12">
        <div class="container mx-auto px-4">
            <h1 class="text-2xl md:text-3xl font-bold text-blue-800 mb-6">Water Usage Dashboard</h1>
            
            <!-- Stats Overview -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <!-- Current Usage -->
                <div class="bg-white rounded-lg shadow-md p-5 dashboard-card">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-gray-600 font-medium">Current Usage</h3>
                        <i class="fas fa-tint text-blue-500"></i>
                    </div>
                    <p class="text-3xl font-bold text-blue-800 mb-1">85 <span class="text-lg font-normal">gal/day</span></p>
                    <div class="flex items-center text-sm">
                        <span class="text-green-500 flex items-center">
                            <i class="fas fa-arrow-down mr-1"></i>12%
                        </span>
                        <span class="text-gray-500 ml-2">from last month</span>
                    </div>
                </div>
                
                <!-- Conservation Goal -->
                <div class="bg-white rounded-lg shadow-md p-5 dashboard-card">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-gray-600 font-medium">Conservation Goal</h3>
                        <i class="fas fa-bullseye text-green-500"></i>
                    </div>
                    <p class="text-3xl font-bold text-green-600 mb-1">80 <span class="text-lg font-normal">gal/day</span></p>
                    <div class="water-progress">
                        <div class="water-progress-bar" style="width: 94%;"></div>
                    </div>
                    <div class="flex justify-between mt-1 text-xs text-gray-500">
                        <span>94% Complete</span>
                        <span>5 gal remaining</span>
                    </div>
                </div>
                
                <!-- Cost Savings -->
                <div class="bg-white rounded-lg shadow-md p-5 dashboard-card">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-gray-600 font-medium">Cost Savings</h3>
                        <i class="fas fa-dollar-sign text-blue-500"></i>
                    </div>
                    <p class="text-3xl font-bold text-blue-800 mb-1">$32.50 <span class="text-lg font-normal">this month</span></p>
                    <div class="flex items-center text-sm">
                        <span class="text-green-500 flex items-center">
                            <i class="fas fa-arrow-up mr-1"></i>8%
                        </span>
                        <span class="text-gray-500 ml-2">from last month</span>
                    </div>
                </div>
                
                <!-- Water Footprint -->
                <div class="bg-white rounded-lg shadow-md p-5 dashboard-card">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-gray-600 font-medium">Water Footprint</h3>
                        <i class="fas fa-leaf text-green-500"></i>
                    </div>
                    <p class="text-3xl font-bold text-green-600 mb-1">Good</p>
                    <div class="flex items-center justify-between text-sm mt-1">
                        <div class="bg-green-100 text-green-800 px-2 py-1 rounded-full">
                            Better than 65% of users
                        </div>
                        <a href="goals.html" class="text-blue-500 hover:underline">Improve</a>
                    </div>
                </div>
            </div>
            
            <!-- Chart Section -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
                <!-- Main Usage Chart -->
                <div class="lg:col-span-2 bg-white rounded-lg shadow-md p-5 dashboard-card">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-semibold text-gray-700">Monthly Water Usage</h3>
                        <div class="flex space-x-2">
                            <button class="bg-blue-100 text-blue-600 px-3 py-1 rounded-md text-sm font-medium">6 Months</button>
                            <button class="text-gray-600 hover:bg-blue-50 px-3 py-1 rounded-md text-sm font-medium">1 Year</button>
                        </div>
                    </div>
                    <div class="h-72">
                        <canvas id="water-usage-chart"></canvas>
                    </div>
                </div>
                
                <!-- Water Source Breakdown -->
                <div class="bg-white rounded-lg shadow-md p-5 dashboard-card">
                    <h3 class="text-lg font-semibold text-gray-700 mb-6">Usage by Source</h3>
                    <div class="h-64">
                        <canvas id="water-sources-chart"></canvas>
                    </div>
                    <div class="mt-4">
                        <div class="flex items-center justify-between mb-2">
                            <div class="flex items-center">
                                <div class="w-3 h-3 rounded-full bg-blue-500 mr-2"></div>
                                <span class="text-sm text-gray-600">Kitchen (30%)</span>
                            </div>
                            <span class="text-sm font-medium">25.5 gal/day</span>
                        </div>
                        <div class="flex items-center justify-between mb-2">
                            <div class="flex items-center">
                                <div class="w-3 h-3 rounded-full bg-blue-400 mr-2"></div>
                                <span class="text-sm text-gray-600">Bathroom (40%)</span>
                            </div>
                            <span class="text-sm font-medium">34 gal/day</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="w-3 h-3 rounded-full bg-blue-300 mr-2"></div>
                                <span class="text-sm text-gray-600">Laundry (15%)</span>
                            </div>
                            <span class="text-sm font-medium">12.75 gal/day</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- User Profile Card -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-8">
                <div class="flex flex-col md:flex-row items-center">
                    <div class="mb-4 md:mb-0 md:mr-6">
                        <img src="https://images.unsplash.com/photo-1495774539583-885e02cca8c2" alt="Profile" class="h-24 w-24 rounded-full object-cover border-4 border-blue-100">
                    </div>
                    <div>
                        <h2 class="text-2xl font-bold text-blue-800"><?php echo htmlspecialchars($user["first_name"] . " " . $user["last_name"]); ?></h2>
                        <p class="text-gray-600 mb-2"><?php echo htmlspecialchars($user["email"]); ?></p>
                        <div class="flex flex-wrap gap-2">
                            <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm font-medium">
                                <i class="fas fa-map-marker-alt mr-1"></i> <?php echo ucfirst(htmlspecialchars($user["location"])); ?>
                            </span>
                            <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm font-medium">
                                <i class="fas fa-user mr-1"></i> <?php echo ucfirst(htmlspecialchars($user["user_type"])); ?>
                            </span>
                            <span class="bg-purple-100 text-purple-800 px-3 py-1 rounded-full text-sm font-medium">
                                <i class="fas fa-calendar-alt mr-1"></i> Member since <?php echo date("M Y", strtotime($user["created_at"])); ?>
                            </span>
                        </div>
                    </div>
                    <div class="mt-4 md:mt-0 md:ml-auto">
                        <a href="profile.php" class="bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded-md font-medium transition">
                            <i class="fas fa-edit mr-1"></i> Edit Profile
                        </a>
                    </div>
                </div>
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
                        <li><a href="devices.html" class="text-blue-200 hover:text-white">Devices</a></li>
                        <li><a href="goals.html" class="text-blue-200 hover:text-white">Goals</a></li>
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
        
        // Water Usage Chart
        const waterUsageCtx = document.getElementById('water-usage-chart').getContext('2d');
        const waterUsageChart = new Chart(waterUsageCtx, {
            type: 'line',
            data: {
                labels: ['Nov', 'Dec', 'Jan', 'Feb', 'Mar', 'Apr'],
                datasets: [{
                    label: 'Water Usage (gal/day)',
                    data: [110, 105, 100, 95, 90, 85],
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    borderColor: 'rgba(59, 130, 246, 1)',
                    borderWidth: 2,
                    tension: 0.4,
                    fill: true
                }, {
                    label: 'Goal',
                    data: [100, 95, 90, 85, 80, 80],
                    borderColor: 'rgba(16, 185, 129, 1)',
                    borderWidth: 2,
                    borderDash: [5, 5],
                    tension: 0.4,
                    fill: false,
                    pointRadius: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                    }
                },
                scales: {
                    y: {
                        beginAtZero: false,
                        min: 70,
                        ticks: {
                            stepSize: 10
                        }
                    }
                }
            }
        });
        
        // Water Sources Chart
        const waterSourcesCtx = document.getElementById('water-sources-chart').getContext('2d');
        const waterSourcesChart = new Chart(waterSourcesCtx, {
            type: 'doughnut',
            data: {
                labels: ['Kitchen', 'Bathroom', 'Laundry', 'Garden', 'Other'],
                datasets: [{
                    data: [30, 40, 15, 10, 5],
                    backgroundColor: [
                        'rgba(59, 130, 246, 0.8)',
                        'rgba(96, 165, 250, 0.8)',
                        'rgba(147, 197, 253, 0.8)',
                        'rgba(191, 219, 254, 0.8)',
                        'rgba(219, 234, 254, 0.8)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                cutout: '70%'
            }
        });
    </script>
</body>
</html>
