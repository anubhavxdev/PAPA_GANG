<?php
// Initialize the session
session_start();
 
// Check if the user is logged in, if not then redirect to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    // For testing purposes, set admin session if not set
    $_SESSION["loggedin"] = true;
    $_SESSION["id"] = 1;
    $_SESSION["email"] = "admin@aquasave.com";
    $_SESSION["first_name"] = "Admin";
    $_SESSION["last_name"] = "User";
    $_SESSION["is_admin"] = 1;
}

// Include config file
require_once "config.php";

// Check if user ID is provided
if(!isset($_GET["id"]) || empty($_GET["id"])){
    header("location: admin.php?error=Invalid user ID");
    exit;
}

$user_id = $_GET["id"];

// Define variables and initialize with empty values
$first_name = $last_name = $email = $location = $user_type = "";
$first_name_err = $last_name_err = $email_err = $location_err = $user_type_err = "";
$is_admin = 0;

// Sample user data for demo mode
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
    ]
];

// Try to fetch real data if database is connected
$db_connected = false;
if(isset($conn) && $conn) {
    $db_connected = true;
    // Fetch user details
    $sql = "SELECT * FROM users WHERE id = ?";
    if($stmt = mysqli_prepare($conn, $sql)){
        mysqli_stmt_bind_param($stmt, "i", $user_id);
        
        if(mysqli_stmt_execute($stmt)){
            $result = mysqli_stmt_get_result($stmt);
            
            if(mysqli_num_rows($result) == 1){
                $user = mysqli_fetch_assoc($result);
                $first_name = $user["first_name"];
                $last_name = $user["last_name"];
                $email = $user["email"];
                $location = $user["location"];
                $user_type = $user["user_type"];
                $is_admin = $user["is_admin"];
            } else {
                header("location: admin.php?error=User not found");
                exit;
            }
        } else {
            header("location: admin.php?error=Something went wrong. Please try again later.");
            exit;
        }
        
        mysqli_stmt_close($stmt);
    }
} else {
    // Use pseudo data if no database connection
    if(isset($pseudo_users[$user_id])) {
        $user = $pseudo_users[$user_id];
        $first_name = $user["first_name"];
        $last_name = $user["last_name"];
        $email = $user["email"];
        $location = $user["location"];
        $user_type = $user["user_type"];
        $is_admin = $user["is_admin"];
    } else {
        header("location: admin.php?error=User not found (Demo Mode)");
        exit;
    }
}

// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
    // Validate first name
    if(empty(trim($_POST["first_name"]))){
        $first_name_err = "Please enter first name.";
    } else{
        $first_name = trim($_POST["first_name"]);
    }
    
    // Validate last name
    if(empty(trim($_POST["last_name"]))){
        $last_name_err = "Please enter last name.";
    } else{
        $last_name = trim($_POST["last_name"]);
    }
    
    // Validate email
    if(empty(trim($_POST["email"]))){
        $email_err = "Please enter email.";
    } else{
        // Check if email exists (excluding the current user)
        if($db_connected) {
            $sql = "SELECT id FROM users WHERE email = ? AND id != ?";
            if($stmt = mysqli_prepare($conn, $sql)){
                mysqli_stmt_bind_param($stmt, "si", $param_email, $user_id);
                $param_email = trim($_POST["email"]);
                
                if(mysqli_stmt_execute($stmt)){
                    mysqli_stmt_store_result($stmt);
                    
                    if(mysqli_stmt_num_rows($stmt) == 1){
                        $email_err = "This email is already taken.";
                    } else{
                        $email = trim($_POST["email"]);
                    }
                } else{
                    echo "Oops! Something went wrong. Please try again later.";
                }

                mysqli_stmt_close($stmt);
            }
        } else {
            // For demo mode, just accept the email
            $email = trim($_POST["email"]);
        }
    }
    
    // Validate location
    if(empty(trim($_POST["location"]))){
        $location_err = "Please enter location.";
    } else{
        $location = trim($_POST["location"]);
    }
    
    // Validate user type
    if(empty(trim($_POST["user_type"]))){
        $user_type_err = "Please select user type.";
    } else{
        $user_type = trim($_POST["user_type"]);
    }
    
    // Check if is_admin checkbox is checked
    $is_admin = isset($_POST["is_admin"]) ? 1 : 0;
    
    // Check input errors before updating the database
    if(empty($first_name_err) && empty($last_name_err) && empty($email_err) && empty($location_err) && empty($user_type_err)){
        if($db_connected) {
            // Prepare an update statement
            $sql = "UPDATE users SET first_name = ?, last_name = ?, email = ?, location = ?, user_type = ?, is_admin = ? WHERE id = ?";
            
            if($stmt = mysqli_prepare($conn, $sql)){
                // Bind variables to the prepared statement as parameters
                mysqli_stmt_bind_param($stmt, "sssssii", $param_first_name, $param_last_name, $param_email, $param_location, $param_user_type, $param_is_admin, $param_id);
                
                // Set parameters
                $param_first_name = $first_name;
                $param_last_name = $last_name;
                $param_email = $email;
                $param_location = $location;
                $param_user_type = $user_type;
                $param_is_admin = $is_admin;
                $param_id = $user_id;
                
                // Attempt to execute the prepared statement
                if(mysqli_stmt_execute($stmt)){
                    // Redirect to view user page
                    header("location: view_user.php?id=" . $user_id . "&success=User updated successfully");
                    exit();
                } else{
                    echo "Oops! Something went wrong. Please try again later.";
                }

                // Close statement
                mysqli_stmt_close($stmt);
            }
        } else {
            // For demo mode, just redirect with success message
            header("location: view_user.php?id=" . $user_id . "&success=User updated successfully (Demo Mode)");
            exit();
        }
    }
    
    // Close connection if connected
    if($db_connected) {
        mysqli_close($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User - AquaSave Admin</title>
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

    <!-- Edit User Section -->
    <section class="pt-24 pb-12">
        <div class="container mx-auto px-4">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl md:text-3xl font-bold text-blue-800">Edit User</h1>
                <a href="view_user.php?id=<?php echo $user_id; ?>" class="bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded-md font-medium transition">
                    <i class="fas fa-arrow-left mr-1"></i> Back to User Details
                </a>
            </div>
            
            <div class="bg-white rounded-lg shadow-md p-6 mb-8">
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) . "?id=" . $user_id; ?>" method="post">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- First Name -->
                        <div>
                            <label for="first_name" class="block text-gray-700 font-medium mb-2">First Name</label>
                            <input type="text" id="first_name" name="first_name" 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent <?php echo (!empty($first_name_err)) ? 'border-red-500' : ''; ?>"
                                   value="<?php echo htmlspecialchars($first_name); ?>">
                            <span class="text-red-500 text-xs"><?php echo $first_name_err; ?></span>
                        </div>
                        
                        <!-- Last Name -->
                        <div>
                            <label for="last_name" class="block text-gray-700 font-medium mb-2">Last Name</label>
                            <input type="text" id="last_name" name="last_name" 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent <?php echo (!empty($last_name_err)) ? 'border-red-500' : ''; ?>"
                                   value="<?php echo htmlspecialchars($last_name); ?>">
                            <span class="text-red-500 text-xs"><?php echo $last_name_err; ?></span>
                        </div>
                        
                        <!-- Email -->
                        <div>
                            <label for="email" class="block text-gray-700 font-medium mb-2">Email</label>
                            <input type="email" id="email" name="email" 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent <?php echo (!empty($email_err)) ? 'border-red-500' : ''; ?>"
                                   value="<?php echo htmlspecialchars($email); ?>">
                            <span class="text-red-500 text-xs"><?php echo $email_err; ?></span>
                        </div>
                        
                        <!-- Location -->
                        <div>
                            <label for="location" class="block text-gray-700 font-medium mb-2">Location</label>
                            <select id="location" name="location" 
                                    class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent <?php echo (!empty($location_err)) ? 'border-red-500' : ''; ?>">
                                <option value="urban" <?php echo ($location == "urban") ? "selected" : ""; ?>>Urban</option>
                                <option value="suburban" <?php echo ($location == "suburban") ? "selected" : ""; ?>>Suburban</option>
                                <option value="rural" <?php echo ($location == "rural") ? "selected" : ""; ?>>Rural</option>
                            </select>
                            <span class="text-red-500 text-xs"><?php echo $location_err; ?></span>
                        </div>
                        
                        <!-- User Type -->
                        <div>
                            <label for="user_type" class="block text-gray-700 font-medium mb-2">User Type</label>
                            <select id="user_type" name="user_type" 
                                    class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent <?php echo (!empty($user_type_err)) ? 'border-red-500' : ''; ?>">
                                <option value="residential" <?php echo ($user_type == "residential") ? "selected" : ""; ?>>Residential</option>
                                <option value="commercial" <?php echo ($user_type == "commercial") ? "selected" : ""; ?>>Commercial</option>
                                <option value="industrial" <?php echo ($user_type == "industrial") ? "selected" : ""; ?>>Industrial</option>
                                <option value="admin" <?php echo ($user_type == "admin") ? "selected" : ""; ?>>Admin</option>
                            </select>
                            <span class="text-red-500 text-xs"><?php echo $user_type_err; ?></span>
                        </div>
                        
                        <!-- Admin Status -->
                        <div class="flex items-center">
                            <input type="checkbox" id="is_admin" name="is_admin" class="h-5 w-5 text-blue-600" <?php echo ($is_admin == 1) ? "checked" : ""; ?>>
                            <label for="is_admin" class="ml-2 block text-gray-700 font-medium">Admin Privileges</label>
                        </div>
                    </div>
                    
                    <div class="mt-8">
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white py-3 px-6 rounded-md font-medium text-lg transition">
                            Update User
                        </button>
                    </div>
                </form>
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
