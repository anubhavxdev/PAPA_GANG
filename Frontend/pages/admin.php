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

// Include config file
require_once "config.php";
// Include database connection check utility
require_once "db_connection_check.php";

// Process user deletion
if(isset($_GET["delete_id"]) && !empty($_GET["delete_id"])){
    $delete_id = $_GET["delete_id"];
    
    // Prevent admin from deleting themselves
    if($delete_id != $_SESSION["id"]){
        // Try database deletion if connected
        if($is_db_connected) {
            $sql = "DELETE FROM users WHERE id = ? AND is_admin = 0";
            
            if($stmt = mysqli_prepare($conn, $sql)){
                mysqli_stmt_bind_param($stmt, "i", $delete_id);
                
                if(mysqli_stmt_execute($stmt)){
                    header("location: admin.php?success=User deleted successfully");
                } else{
                    header("location: admin.php?error=Something went wrong. Please try again later.");
                }
                
                mysqli_stmt_close($stmt);
            }
        } else {
            // Just redirect with success message for demo purposes
            header("location: admin.php?success=User deleted successfully (Demo Mode)");
        }
    } else {
        header("location: admin.php?error=You cannot delete your own account.");
    }
    exit;
}

// Initialize users array with pseudo data
$users = [];

// Try to fetch real users if database is connected
if($is_db_connected) {
    // Fetch all users
    $sql = "SELECT id, first_name, last_name, email, location, user_type, is_admin, created_at FROM users ORDER BY created_at DESC";
    $result = mysqli_query($conn, $sql);
    
    if($result) {
        $users = mysqli_fetch_all($result, MYSQLI_ASSOC);
    }
}

// If no users from database or connection failed, use pseudo data
if(empty($users)) {
    // Get pseudo users from the utility function
    $users = array_values(get_pseudo_users());
}

// Get statistics for dashboard
$total_users = count($users);
$total_admins = 0;
$residential_users = 0;
$commercial_users = 0;
$industrial_users = 0;

foreach($users as $user) {
    if($user['is_admin'] == 1) {
        $total_admins++;
    }
    
    if(isset($user['user_type'])) {
        switch($user['user_type']) {
            case 'residential':
                $residential_users++;
                break;
            case 'commercial':
                $commercial_users++;
                break;
            case 'industrial':
                $industrial_users++;
                break;
        }
    }
}

// Add notification for demo mode
$demo_notification = "";
if(!$is_db_connected) {
    $demo_notification = '<div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded relative mb-6" role="alert">
                            <strong class="font-bold">Demo Mode:</strong>
                            <span class="block sm:inline"> Database connection is unavailable. Showing pseudo data for presentation purposes.</span>
                          </div>';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - AquaSave</title>
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
                <h1 class="text-2xl md:text-3xl font-bold text-blue-800">Admin Dashboard</h1>
                <a href="dashboard.php" class="bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded-md font-medium transition">
                    <i class="fas fa-tachometer-alt mr-1"></i> User Dashboard
                </a>
            </div>
            
            <?php 
            // Display demo mode notification if needed
            if(!empty($demo_notification)){
                echo $demo_notification;
            }
            
            // Display success or error messages
            if(isset($_GET["success"])){
                echo '<div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-6" role="alert">
                        <span class="block sm:inline">' . htmlspecialchars($_GET["success"]) . '</span>
                      </div>';
            }
            
            if(isset($_GET["error"])){
                echo '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-6" role="alert">
                        <span class="block sm:inline">' . htmlspecialchars($_GET["error"]) . '</span>
                      </div>';
            }
            ?>
            
            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="bg-white rounded-lg shadow-md p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-blue-100 text-blue-500 mr-4">
                            <i class="fas fa-users text-2xl"></i>
                        </div>
                        <div>
                            <p class="text-gray-500 text-sm">Total Users</p>
                            <p class="text-2xl font-bold text-gray-700"><?php echo $total_users; ?></p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white rounded-lg shadow-md p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-green-100 text-green-500 mr-4">
                            <i class="fas fa-home text-2xl"></i>
                        </div>
                        <div>
                            <p class="text-gray-500 text-sm">Residential Users</p>
                            <p class="text-2xl font-bold text-gray-700"><?php echo $residential_users; ?></p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white rounded-lg shadow-md p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-yellow-100 text-yellow-500 mr-4">
                            <i class="fas fa-building text-2xl"></i>
                        </div>
                        <div>
                            <p class="text-gray-500 text-sm">Commercial Users</p>
                            <p class="text-2xl font-bold text-gray-700"><?php echo $commercial_users; ?></p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white rounded-lg shadow-md p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-purple-100 text-purple-500 mr-4">
                            <i class="fas fa-industry text-2xl"></i>
                        </div>
                        <div>
                            <p class="text-gray-500 text-sm">Industrial Users</p>
                            <p class="text-2xl font-bold text-gray-700"><?php echo $industrial_users; ?></p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- User Management -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-8">
                <h2 class="text-xl font-semibold text-gray-700 mb-4">User Management</h2>
                
                <div class="overflow-x-auto">
                    <table class="min-w-full bg-white">
                        <thead>
                            <tr>
                                <th class="py-3 px-4 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">ID</th>
                                <th class="py-3 px-4 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Name</th>
                                <th class="py-3 px-4 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Email</th>
                                <th class="py-3 px-4 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Location</th>
                                <th class="py-3 px-4 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">User Type</th>
                                <th class="py-3 px-4 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Joined</th>
                                <th class="py-3 px-4 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <?php foreach($users as $user): ?>
                            <tr>
                                <td class="py-3 px-4 text-sm text-gray-500"><?php echo $user['id']; ?></td>
                                <td class="py-3 px-4">
                                    <div class="flex items-center">
                                        <div class="h-8 w-8 rounded-full bg-blue-500 flex items-center justify-center text-white font-semibold mr-3">
                                            <?php echo substr($user['first_name'], 0, 1) . substr($user['last_name'], 0, 1); ?>
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-900"><?php echo $user['first_name'] . ' ' . $user['last_name']; ?></p>
                                            <p class="text-xs text-gray-500"><?php echo $user['is_admin'] ? 'Admin' : 'User'; ?></p>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-3 px-4 text-sm text-gray-500"><?php echo $user['email']; ?></td>
                                <td class="py-3 px-4 text-sm text-gray-500"><?php echo ucfirst($user['location']); ?></td>
                                <td class="py-3 px-4 text-sm text-gray-500"><?php echo ucfirst($user['user_type']); ?></td>
                                <td class="py-3 px-4 text-sm text-gray-500"><?php echo date('M d, Y', strtotime($user['created_at'])); ?></td>
                                <td class="py-3 px-4 text-sm">
                                    <div class="flex space-x-2">
                                        <a href="view_user.php?id=<?php echo $user['id']; ?>" class="text-blue-600 hover:text-blue-900" title="View User">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <?php if(!$user['is_admin']): ?>
                                        <a href="admin.php?delete_id=<?php echo $user['id']; ?>" class="text-red-600 hover:text-red-900" title="Delete User" onclick="return confirm('Are you sure you want to delete this user?');">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
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
                        <li><a href="admin.php" class="text-blue-200 hover:text-white">Admin</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-medium mb-4">Resources</h4>
                    <ul class="space-y-2">
                        <li><a href="tips.html" class="text-blue-200 hover:text-white">Water Saving Tips</a></li>
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
