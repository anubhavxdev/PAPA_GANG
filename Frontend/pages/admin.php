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

// Process user deletion
if(isset($_GET["delete_id"]) && !empty($_GET["delete_id"])){
    $delete_id = $_GET["delete_id"];
    
    // Prevent admin from deleting themselves
    if($delete_id != $_SESSION["id"]){
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
        header("location: admin.php?error=You cannot delete your own account.");
    }
}

// Fetch all users
$sql = "SELECT id, first_name, last_name, email, location, user_type, is_admin, created_at FROM users ORDER BY created_at DESC";
$result = mysqli_query($conn, $sql);
$users = mysqli_fetch_all($result, MYSQLI_ASSOC);
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

    <!-- Admin Dashboard Section -->
    <section class="pt-24 pb-12">
        <div class="container mx-auto px-4">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl md:text-3xl font-bold text-blue-800">Admin Dashboard</h1>
                <a href="dashboard.php" class="bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded-md font-medium transition">
                    <i class="fas fa-tachometer-alt mr-1"></i> User Dashboard
                </a>
            </div>
            
            <?php if(isset($_GET["success"])): ?>
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <span class="block sm:inline"><?php echo htmlspecialchars($_GET["success"]); ?></span>
                </div>
            <?php endif; ?>
            
            <?php if(isset($_GET["error"])): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <span class="block sm:inline"><?php echo htmlspecialchars($_GET["error"]); ?></span>
                </div>
            <?php endif; ?>
            
            <!-- Admin Stats -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <!-- Total Users -->
                <div class="bg-white rounded-lg shadow-md p-5">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-gray-600 font-medium">Total Users</h3>
                        <i class="fas fa-users text-blue-500"></i>
                    </div>
                    <p class="text-3xl font-bold text-blue-800"><?php echo count($users); ?></p>
                </div>
                
                <!-- New Users This Month -->
                <div class="bg-white rounded-lg shadow-md p-5">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-gray-600 font-medium">New Users This Month</h3>
                        <i class="fas fa-user-plus text-green-500"></i>
                    </div>
                    <p class="text-3xl font-bold text-green-600">
                        <?php 
                            $current_month = date('m');
                            $current_year = date('Y');
                            $new_users = 0;
                            
                            foreach($users as $user) {
                                $user_month = date('m', strtotime($user['created_at']));
                                $user_year = date('Y', strtotime($user['created_at']));
                                
                                if($user_month == $current_month && $user_year == $current_year) {
                                    $new_users++;
                                }
                            }
                            
                            echo $new_users;
                        ?>
                    </p>
                </div>
                
                <!-- Admin Users -->
                <div class="bg-white rounded-lg shadow-md p-5">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-gray-600 font-medium">Admin Users</h3>
                        <i class="fas fa-user-shield text-purple-500"></i>
                    </div>
                    <p class="text-3xl font-bold text-purple-600">
                        <?php 
                            $admin_count = 0;
                            foreach($users as $user) {
                                if($user['is_admin'] == 1) {
                                    $admin_count++;
                                }
                            }
                            echo $admin_count;
                        ?>
                    </p>
                </div>
            </div>
            
            <!-- User Management -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-8">
                <h2 class="text-xl font-semibold text-gray-700 mb-4">User Management</h2>
                
                <div class="overflow-x-auto">
                    <table class="min-w-full bg-white">
                        <thead>
                            <tr class="bg-blue-100 text-blue-800">
                                <th class="py-3 px-4 text-left">ID</th>
                                <th class="py-3 px-4 text-left">Name</th>
                                <th class="py-3 px-4 text-left">Email</th>
                                <th class="py-3 px-4 text-left">Location</th>
                                <th class="py-3 px-4 text-left">User Type</th>
                                <th class="py-3 px-4 text-left">Role</th>
                                <th class="py-3 px-4 text-left">Joined</th>
                                <th class="py-3 px-4 text-left">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($users as $user): ?>
                                <tr class="border-b hover:bg-blue-50">
                                    <td class="py-3 px-4"><?php echo htmlspecialchars($user["id"]); ?></td>
                                    <td class="py-3 px-4"><?php echo htmlspecialchars($user["first_name"] . " " . $user["last_name"]); ?></td>
                                    <td class="py-3 px-4"><?php echo htmlspecialchars($user["email"]); ?></td>
                                    <td class="py-3 px-4"><?php echo ucfirst(htmlspecialchars($user["location"])); ?></td>
                                    <td class="py-3 px-4"><?php echo ucfirst(htmlspecialchars($user["user_type"])); ?></td>
                                    <td class="py-3 px-4">
                                        <?php if($user["is_admin"] == 1): ?>
                                            <span class="bg-purple-100 text-purple-800 px-2 py-1 rounded-full text-xs font-medium">Admin</span>
                                        <?php else: ?>
                                            <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded-full text-xs font-medium">User</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="py-3 px-4"><?php echo date("M d, Y", strtotime($user["created_at"])); ?></td>
                                    <td class="py-3 px-4">
                                        <div class="flex space-x-2">
                                            <a href="view_user.php?id=<?php echo $user["id"]; ?>" class="text-blue-500 hover:text-blue-700">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <?php if($user["id"] != $_SESSION["id"]): ?>
                                                <a href="admin.php?delete_id=<?php echo $user["id"]; ?>" class="text-red-500 hover:text-red-700" onclick="return confirm('Are you sure you want to delete this user?');">
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
